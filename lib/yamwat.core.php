<?php
// Yamwat - Yet Another MediaWiki API Tool
// CORE classes

class yamwatINIT {

	var $core_name = 'Yamwat';
	var $core_version = '2.7';
	var $core_description = 'Yet Another MediaWiki API Tool';
	var $core_url = 'https://github.com/attogram/yamwat';
	var $core_email = 'YOUR-EMAIL-HERE';

	var $debug, $debug_log, $config, $message, $start_time;

	function __construct() {

		global $config, $debug_log;

		$this->start_time = microtime(1);

		$this->debug_log = array();
		if( is_array($debug_log) ) { $this->debug_log = $debug_log; }

		$this->debug_log[] = 'yamwatINIT: __construct: ' . gmdate('Y-m-d H:i:s e') .' ('. time() .')';

		if( !isset($config['yamwat_home']) ) {
			print 'ERROR: yamwatINIT: __construct: $config[\'yamwat_home\'] not set';  exit;
		}
		$this->debug_log[] = 'yamwatINIT: __construct: yamwat_home: ' . $config['yamwat_home']
		. ' config: ' . sizeof($config);

		$system_config = $config['yamwat_home'] . 'config.php';
		if( !file_exists($system_config) || !is_readable($system_config) ) {
			if( PHP_SAPI !== 'cli') { print '<pre>'; }
			print 'ERROR: yamwatINIT: __construct: can not load system config: ' . $system_config . "\n\n";

			if( PHP_SAPI !== 'cli') {
				print 'Did you forget to copy <strong>config.sample.php</strong> to <strong>config.php</strong> ?' . "\n";
				print '</pre>';
			} else {
				print 'Did you forget to copy config.sample.php to config.php ?' . "\n";
			}
			exit;
		}
		include_once($system_config); // Load system config - updates $config
		$this->debug_log[] = 'yamwatINIT: __construct: include ' . $system_config
			. ' config: ' . sizeof($config);

		if( !@$config['system_name'] ) {
			$this->debug_log[] = 'WARNING: yamwatINIT: __construct: no system_name';
			$config['system_name'] = 'NAME';
		}
		$this->debug_log[] = 'yamwatINIT: __construct: system_name: ' . $config['system_name'];

		$this->config = $config; // global configuration settings
	}

	function debug($m='') {
		$this->message = $m;
		if( !$this->config['debug'] ) { return; }
		if( PHP_SAPI === 'cli') { print 'DEBUG: ' . print_r($m,1) . "\n"; return; }
		$this->debug_log[] = htmlentities($m);
	}

	function get_debug() {
		if( !$this->config['debug'] ) { return; }
		return $this->debug_log;
	}

}

class yamwatJSON extends yamwatINIT {

	function json_decode($x='') {
		if( !$x ) {
			$this->debug('ERROR: yamwatJSON: json_decode: no input');
			return array();
		}
		$decode = json_decode($x);
		if( !$decode ) {
			$this->debug('ERROR: yamwatJSON: json_decode: ' . $this->json_error(json_last_error()));
			return array();
		}
		return $decode;
	}

	function json_error($erno='') {
		switch($erno) {
			case JSON_ERROR_NONE: $r = 'No errors'; break;
			case JSON_ERROR_DEPTH: $r = 'Maximum stack depth exceeded'; break;
			case JSON_ERROR_STATE_MISMATCH: $r = 'Underflow or the modes mismatch'; break;
			case JSON_ERROR_CTRL_CHAR: $r = 'Unexpected control character found'; break;
			case JSON_ERROR_SYNTAX: $r = 'Syntax error, malformed JSON'; break;
			case JSON_ERROR_UTF8: $r = 'Malformed UTF-8 characters, possibly incorrectly encoded'; break;
			default: $r = 'Unknown JSON error'; break;
		}
		return $r;
	}

}

class yamwatCURL extends yamwatJSON {

	var $curl, $url, $user_agent, $system_email, $system_url;

	function __construct() { parent::__construct(); }

	// Initialize Curl system -  Returns: TRUE or FALSE
	function init_curl() {

		if( !extension_loaded('curl') ) {
			$this->debug('ERROR: yamwatCURL: init_curl: curl PHP extension not loaded');
			return FALSE;
		}

		$lib_curl = $this->config['yamwat_home'] . 'lib/curl/curl.php';
		$lib_curl_response = $this->config['yamwat_home'] . 'lib/curl/curl_response.php';
		if( !is_readable($lib_curl) || !is_readable($lib_curl_response) ) {
			$this->debug('ERROR: yamwatCURL: init_curl: curl libs not readable');
			return FALSE;
		}
		include_once($lib_curl);
		include_once($lib_curl_response);

		if( !$this->curl ) {
			$this->curl = new Curl;
		}
		if( !is_object($this->curl) ) {
			$this->debug('ERROR: yamwatCURL: init_curl: curl not object');
			return FALSE;
		}

		$this->curl->options['CURLOPT_SSLVERSION'] = 4;
		$this->curl->options['CURLOPT_SSL_VERIFYPEER'] = FALSE;
		$this->curl->options['CURLOPT_SSL_VERIFYHOST'] = FALSE;
		$this->curl->options['CURLOPT_CONNECTTIMEOUT'] = 15;  // connection timeout
		$this->curl->options['CURLOPT_TIMEOUT'] = 45; // response timeout

		$this->system_email = @$this->config['system_email'];
		$this->debug('yamwatCURL: init_curl: system_email: ' . $this->system_email);

		$this->system_url = @$this->config['system_url'];
		$this->debug('yamwatCURL: init_curl: system_url: ' . $this->system_url);

		$this->curl->user_agent = $this->get_user_agent();
		$this->debug('yamwatCURL: init_curl: user_agent: ' . $this->curl->user_agent);

		$this->curl->referer = @$this->config['referer'];
		$this->debug('yamwatCURL: init_curl: referer: ' . $this->curl->referer);

		$this->curl->cookie_file = NULL;

		return TRUE;

	}

	// Get a url with Curl system - Returns: STRING or FALSE
	function get() {

		if( !$this->init_curl() ) {
			$this->debug('ERROR: yamwatCURL: get: CURL init failed');
			return FALSE;
		}

		if( !$this->url ) {
			$this->debug('ERROR: yamwatCURL: get: no API endpoint found');
			return FALSE;
		}

		$this->debug('yamwatCURL: get: url: ' . $this->url );

		flush();

		$r = $this->curl->get( $this->url );

		if( !$r ) {
			$this->debug('ERROR: yamwatCURL: get: ' . $this->curl->error());
			return FALSE;
		}

		if( $r->headers['Status-Code'] != '200' ) {
			$this->debug('ERROR: yamwatCURL: get: ' . @$r->headers['Status-Code'] .': '. @$r->headers['Status']);
			return FALSE;
		}

		if( isset($r->headers['Set-Cookie']) ) {
			$this->debug('yamwatCURL: get: Set-Cookie: ' . $r->headers['Set-Cookie'] );
		}

		$x = $this->json_decode($r->body);
		if( !$x ) {
			$this->debug('ERROR: yamwatCURL: get: no body');
			return FALSE;
		}

		if( isset($x->error) ) {
			$this->debug('ERROR: yamwatCURL: get: '. @$x->error->code .': '. @$x->error->info);
		}

		if( isset($x->warnings) ) {
			$this->debug('WARNING: yamwatCURL: get: ' . print_r($x->warnings,1));
		}

		return $x;

	}

	// Get the user agent string
	function get_user_agent() {

		if( !$this->config['system_name'] ) {
			$this->debug('WARNING: yamwatCURL: get_user_agent: no system_name');
			$this->config['system_name'] = 'NAME';
		}
		if( !$this->config['system_version'] ) {
			$this->debug('WARNING: yamwatCURL: get_user_agent: no system_version');
			$this->config['system_version'] = '0.0';
		}
		if( !$this->config['system_url'] ) {
			$this->debug('WARNING: yamwatCURL: get_user_agent: no system_url');
			$this->config['system_url'] = 'URL';
		}
		if( !$this->config['system_email'] ) {
			$this->debug('WARNING: yamwatCURL: get_user_agent: no system_email');
			$this->config['system_email'] = 'EMAIL';
		}

		return $this->config['system_name'] . '/' . $this->config['system_version'] . ' ('
			. $this->config['system_url'] . '; ' . $this->config['system_email'] . ') '
			. $this->core_name . '/' . $this->core_version . ' (' . $this->core_url . ')';
	}

}

class yamwatDB extends yamwatCURL {

	var $db, $db_file;

	function __construct() { parent::__construct(); }

	// Open the database - Returns: TRUE or FALSE - Sets: $this->db
	function open_db() {

		if( is_object($this->db) ) { return TRUE; }

		if( !$this->db_file ) {
			if( !@$this->config['db_file'] ) {
				$this->debug('ERROR: yamwatDB: open_db: missing $config[\'db_file\']');
				return FALSE;
			}
			$this->db_file = $this->config['yamwat_home'] . $this->config['db_file'];
		}

		$create_tables = FALSE;
		if( !file_exists($this->db_file) ) { $create_tables = TRUE; }

		try {
			$this->db = new PDO('sqlite:' . $this->db_file);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			if( $create_tables && !$this->create_table('wiki') ) {
				$this->debug('ERROR: yamwatDB: open_db: can create table: wiki');
				return FALSE;
			}
			if( $create_tables && !$this->create_table('wiki_history') ) {
				$this->debug('ERROR: yamwatDB: open_db: can create table: wiki_history');
				return FALSE;
			}
			$this->debug('yamwatDB: open_db: ' . $this->config['db_file']);
			return TRUE;
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatDB: open_db: PDOException: ' . $e->getMessage());
			return FALSE;
		}
	}

	function close_db() { $this->db = NULL; }

	// create a table - return: TRUE or FALSE
	function create_table($t) {

		$standard_wiki_fields = "
'wiki' TEXT NOT NULL,
'datetime' INTEGER NOT NULL DEFAULT '',
'aindex' INTEGER NOT NULL DEFAULT '',
'api' TEXT NOT NULL DEFAULT '',
'protocol' TEXT NOT NULL DEFAULT '',
'topic' TEXT NOT NULL DEFAULT '',
'network' TEXT NOT NULL DEFAULT '',
'pages' INTEGER NOT NULL DEFAULT '',
'articles' INTEGER NOT NULL DEFAULT '',
'edits' INTEGER NOT NULL DEFAULT '',
'images' INTEGER NOT NULL DEFAULT '',
'users' INTEGER NOT NULL DEFAULT '',
'activeusers' INTEGER NOT NULL DEFAULT '',
'admins' INTEGER NOT NULL DEFAULT '',
'jobs' INTEGER NOT NULL DEFAULT '',
'mainpage' TEXT NOT NULL DEFAULT '',
'base' TEXT NOT NULL DEFAULT '',
'sitename' TEXT NOT NULL DEFAULT '',
'generator' TEXT NOT NULL DEFAULT '',
'case' TEXT NOT NULL DEFAULT '',
'rights' TEXT NOT NULL DEFAULT '',
'language' TEXT NOT NULL DEFAULT '',
'phpversion' TEXT NOT NULL DEFAULT '',
'phpsapi' TEXT NOT NULL DEFAULT '',
'dbtype' TEXT NOT NULL DEFAULT '',
'dbversion' TEXT NOT NULL DEFAULT '',
'rev' TEXT NOT NULL DEFAULT '',
'fallback8bitEncoding' TEXT NOT NULL DEFAULT '',
'writeapi' TEXT NOT NULL DEFAULT '',
'timezone' TEXT NOT NULL DEFAULT '',
'timeoffset' TEXT NOT NULL DEFAULT '',
'articlepath' TEXT NOT NULL DEFAULT '',
'scriptpath' TEXT NOT NULL DEFAULT '',
'script' TEXT NOT NULL DEFAULT '',
'variantarticlepath' TEXT NOT NULL DEFAULT '',
'server' TEXT NOT NULL DEFAULT '',
'wikiid' TEXT NOT NULL DEFAULT '',
'time' TEXT NOT NULL DEFAULT ''";
		switch( $t ) {
			case 'wiki':
				$sql = "CREATE TABLE IF NOT EXISTS 'wiki' ( $standard_wiki_fields, PRIMARY KEY ( wiki ) )";
				break;
			case 'wiki_history':
				$sql = "CREATE TABLE IF NOT EXISTS 'wiki_history' ( $standard_wiki_fields, PRIMARY KEY ( wiki, datetime ) )";
				break;
			default:
				$this->debug('ERROR: yamwatDB: create_table: Unknown table');
				return FALSE;
				break;
		}
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatDB: create_table: can not open database');
				return FALSE;
			}
			$this->debug("yamwatDB: create_table: sql:\n$sql");

			$res = $this->db->exec($sql);

			if( $res === FALSE ) {
				$this->debug('ERROR: yamwatDB: create_table: ' . print_r($this->db->errorInfo(),1));
				return FALSE;
			}

			return TRUE;

		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatDB: create_table: PDOException: ' . $e->getCode() .': '. $e->getMessage());
			return FALSE;
		}
	}

}

class yamwatPLUGIN extends yamwatDB {

	var $plugins;

	function __construct() { parent::__construct(); }

	// Load the plugins - Returns: TRUE or FALSE - Sets: $this->plugins
	function load_plugins() {
		if( is_array( $this->plugins ) ) { return TRUE; }
		$this->plugins = array();
		$plugins_dir = $this->config['yamwat_home'] . 'lib/plugin/';
		$this->debug('yamwatPLUGIN: load_plugins: directory: ' . $plugins_dir);
		if( !is_dir($plugins_dir) ) {
			$this->debug('ERROR: yamwatPLUGIN: load_plugins: can not read plugin directory: ' . $plugins_dir);
			return FALSE;
		}
		$p = scandir($plugins_dir);
		if( !$p ) {
			$this->debug('ERROR: yamwatPLUGIN: load_plugins: can not scan plugin directory: ' . $plugins_dir);
			return FALSE;
		}
		while( list(,$file) = each( $p ) ) {
			if( $file === '.' || $file === '..' ) { continue; }
			if( !is_readable($plugins_dir . $file) ) {
				$this->debug("ERROR: yamwatPLUGIN: load_plugins: Can not read plugin file:$file");
				continue;
			}
			if( !(include_once($plugins_dir . $file)) ) {
				$this->debug("ERROR: yamwatPLUGIN: load_plugins: Can not include plugin file: $file");
				continue;
			}
			$name = str_replace('.php','',$file);
			$class = $name . 'Plugin';
			if( !class_exists($class) ) {
				$this->debug('ERROR: yamwatPLUGIN: load_plugins: Can not find plugin class: ' . $class);
				continue;
			}
			$x = new $class($this);
			if( !is_object($x) ) {
				$this->debug('ERROR: yamwatPLUGIN: load_plugins: Can not load plugin class: ' . $class);
				continue;
			}
			$this->plugins[$name] = $x;
		}
		reset($this->plugins);
		return TRUE;
	}

	// Check if a plugin exists - Returns: TRUE or FALSE
	function plugin_exists() {
		$this->load_plugins();
		if( !array_key_exists( $this->action, $this->plugins) ) {
			$this->debug('WARNING: yamwatWEB: plugin_exists: not found');
			return FALSE;
		}
		return TRUE;
	}
}

class yamwatWIKI extends yamwatPLUGIN {

	var $wiki, $siteinfo, $history;

	// yamwatWIKI init - Sets: $this->wiki
	function __construct() {
		parent::__construct();
		$this->wiki = ( isset($_GET['wiki']) && $_GET['wiki'] != '' ) ? $_GET['wiki'] : NULL;
		if( $this->wiki ) { $this->debug( "yamwatWIKI: __construct: wiki: $this->wiki"); }
	}

	// Add a wiki - Returns: TRUE OR FALSE
	function add_wiki() {
		if( !$this->wiki ) {
			$this->debug('ERROR: yamwatWIKI: add_wiki: no wiki found');
			return FALSE;
		}
		$api = @$_GET['api'];
		if( !$api ) {
			$this->debug('ERROR: yamwatWIKI: add_wiki: no api found');
			return FALSE;
		}

		$network = @$_GET['network']; if( !$network ) { $network = ''; }
		$topic = @$_GET['topic']; if( !$topic ) { $topic = ''; }

		$protocol = @$_GET['protocol'];
		switch( $protocol ) {
			case 'http': case 'https': break;
			default: $protocol = 'http'; break;
		}

		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatWIKI: add_wiki: can not open database');
				return FALSE;
			}
			$statement = $this->db->prepare('
				INSERT INTO wiki (
					wiki, api, protocol, network, topic, datetime
				) VALUES (
					:wiki, :api, :protocol, :network, :topic, :datetime
				)');
			$statement->bindParam(':wiki', $this->wiki);
			$statement->bindParam(':api', $api);
			$statement->bindParam(':protocol', $protocol);
			$statement->bindParam(':network', $network);
			$statement->bindParam(':topic', $topic);
			$now = time(); $statement->bindParam(':datetime', $now);
			$statement->execute();
			$count = $statement->rowCount();
			$this->debug("yamwatWIKI: add_wiki: OK: wiki:$this->wiki api:$api protocol:$protocol network:$network");
			return TRUE;
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatWIKI: add_wiki: PDOException: ' . $e->getCode() . ': ' . $e->getMessage());
			return FALSE;
		}
	}

	// Delete a wiki - Returns: TRUE or FALSE
	function delete_wiki() {
		if( !$this->wiki ) {
			$this->debug('ERROR: yamwatWIKI: delete_wiki: no wiki found');
			return FALSE;
		}
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatWIKI: delete_wiki: can not open database');
				return FALSE;
			}
			$statement = $this->db->prepare('DELETE FROM wiki WHERE wiki = :wiki');
			$statement->bindParam(':wiki', $this->wiki);
			$statement->execute();
			$count = $statement->rowCount();
			if( !$count ) {
				$this->debug('ERROR: yamwatWIKI: delete_wiki: wiki not found in database');
				return FALSE;
			}
			$this->debug("yamwatWIKI: delete_wiki: $count wiki deleted: $this->wiki");
			return TRUE;
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatWIKI: delete_wiki: PDOException: ' . $e->getCode() . ': ' . $e->getMessage());
			return TRUE;
		}
	}

	// Edit a wiki - Returns: TRUE or FALSE
	function edit_wiki() {
		if( !$this->wiki ) {
			$this->debug('ERROR: yamwatWIKI: edit_wiki: no wiki found');
			return FALSE;
		}
		$wiki_new = @$_GET['wiki_new'];
		$api = @$_GET['api'];
		$protocol = @$_GET['protocol'];
		$topic = @$_GET['topic'];
		$network = @$_GET['network'];
		$set = array();
		$set[] = 'wiki=:wiki_new';
		$set[] = 'api=:api';
		$set[] = 'protocol=:protocol';
		$set[] = 'topic=:topic';
		$set[] = 'network=:network';
		$sql = 'UPDATE wiki SET ' . implode(', ', $set) . ' WHERE wiki=:wiki';
		$this->debug('yamwatWIKI: edit_wiki: sql: ' . $sql);
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatWIKI: edit_wiki: can not open database');
				return FALSE;
			}
			$statement = $this->db->prepare($sql);
			$statement->bindParam(':wiki', $this->wiki);
			$statement->bindParam(':wiki_new', $wiki_new);
			$statement->bindParam(':api', $api);
			$statement->bindParam(':protocol', $protocol);
			$statement->bindParam(':topic', $topic);
			$statement->bindParam(':network', $network);
			$statement->execute();
			$count = $statement->rowCount();
			if( !$count ) {
				$this->debug('ERROR: yamwatWIKI: edit_wiki: wiki not found in database');
				return FALSE;
			}
			$this->debug("yamwatWIKI: edit_wiki: OK: wiki=$this->wiki wiki_new=$wiki_new api=$api protocol=$protocol network=$network topic=$topic");
			return TRUE;
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatWIKI: edit_wiki: PDOException: ' . $e->getCode() . ': ' . $e->getMessage());
			return FALSE;
		}
	}

	// Get wiki siteinfo - Returns: TRUE or FALSE - Sets: $this->siteinfo
	function get_siteinfo($reload=FALSE) {
		if( $this->siteinfo && !$reload ) {
			$this->debug('WARNING: yamwatWIKI: get_siteinfo: siteinfo already set. Reload FALSE. size: ' . sizeof($this->siteinfo));
			return TRUE;
		}
		$this->siteinfo = array();
		if( !$this->wiki ) {
			$this->debug('ERROR: yamwatWIKI: get_siteinfo: wiki not found');
			return FALSE;
		}
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatWIKI: get_siteinfo: can not open database');
				return FALSE;
			}
			$statement = $this->db->prepare('SELECT * FROM wiki WHERE wiki = :wiki LIMIT 1');
			$statement->bindParam(':wiki', $this->wiki);
			$statement->execute();
			$this->siteinfo = $statement->fetch(PDO::FETCH_ASSOC);
			if( !$this->siteinfo ) {
				$this->debug('ERROR: yamwatWIKI: get_siteinfo: siteinfo not found');
				return FALSE;
			}
			$this->siteinfo['datetime_utc'] = gmdate('Y-m-d H:i:s', $this->siteinfo['datetime']);
			$this->debug('yamwatWIKI: get_siteinfo: OK');
			return TRUE;
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatWIKI: get_siteinfo: PDOException: ' . $e->getCode() .': '. $e->getMessage());
			return FALSE;
		}
	}

	// Get wiki history - Returns: TRUE or FALSE - Sets: $this->history
	function get_history($reload=FALSE) {
		if( $this->history && !$reload ) {
			$this->debug('WARNING: yamwatWIKI: get_history: history already set. Reload FALSE. size: ' . sizeof($this->history));
			return TRUE;
		}
		$this->history = array();
		if( !$this->wiki ) {
			$this->debug('ERROR: yamwatWIKI: get_history: no wiki');
			return FALSE;
		}
		if( !$this->get_siteinfo() ) { return FALSE; } // wiki must exist in the wiki table
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatWIKI: get_history: can not open database');
				return FALSE;
			}
			$statement = $this->db->prepare('SELECT * FROM wiki_history WHERE wiki = :wiki ORDER BY datetime ASC LIMIT 365');
			$statement->bindParam(':wiki', $this->wiki);
			$res = $statement->execute();
			$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
			if( !is_array($rows) || sizeof($rows) <= 0 ) {
				$this->debug('ERROR: yamwatWIKI: get_history: no history found');
				return TRUE;
			}

			while( list(,$row) = each($rows) ) {

				if( !@is_array($first) ) { $first = $row; }
				$last = $row;

				$row['datetime_utc'] = gmdate('Y-m-d H:i:s', $row['datetime']);

				if( !@isset($aindex_prev) ) { $aindex_diff = ''; } else { $aindex_diff = $row['aindex'] - $aindex_prev; }
				$aindex_prev = $row['aindex'];

				if( !@isset($datetime_prev) ) { $datetime_diff = ''; } else { $datetime_diff = $row['datetime'] - $datetime_prev; }
				$datetime_prev = $row['datetime'];

				if( !@isset($pages_prev) ) { $pages_diff = ''; } else { $pages_diff = $row['pages'] - $pages_prev; }
				$pages_prev = $row['pages'];

				if( !@isset($edits_prev) ) { $edits_diff = ''; } else { $edits_diff = $row['edits'] - $edits_prev; }
				$edits_prev = $row['edits'];

				if( !@isset($articles_prev) ) { $articles_diff = ''; } else { $articles_diff = $row['articles'] - $articles_prev; }
				$articles_prev = $row['articles'];

				if( !@isset($images_prev) ) { $images_diff = ''; } else { $images_diff = $row['images'] - $images_prev; }
				$images_prev = $row['images'];

				if( !@isset($users_prev) ) { $users_diff = ''; } else { $users_diff = $row['users'] - $users_prev; }
				$users_prev = $row['users'];

				if( !@isset($activeusers_prev) ) { $activeusers_diff = ''; } else { $activeusers_diff = $row['activeusers'] - $activeusers_prev; }
				$activeusers_prev = $row['activeusers'];

				if( !@isset($admins_prev) ) { $admins_diff = ''; } else { $admins_diff = $row['admins'] - $admins_prev; }
				$admins_prev = $row['admins'];

				$a_diff = array();
				$row['aindex_diff'] = $a_diff['aindex_diff'] = $aindex_diff; if( !$row['aindex_diff'] ) { $row['aindex_diff'] = '0'; }
				$row['datetime_diff'] = $a_diff['datetime'] = $datetime_diff; if( !$row['datetime_diff'] ) { $row['datetime_diff'] = '0'; }
				$row['pages_diff'] = $a_diff['pages'] = $pages_diff; if( !$row['pages_diff'] ) { $row['pages_diff'] = '0'; }
				$row['articles_diff'] = $a_diff['articles'] = $articles_diff; if( !$row['articles_diff'] ) { $row['articles_diff'] = '0'; }
				$row['edits_diff'] = $a_diff['edits'] = $edits_diff; if( !$row['edits_diff'] ) { $row['edits_diff'] = '0'; }
				$row['images_diff'] = $a_diff['images'] = $images_diff; if( !$row['images_diff'] ) { $row['images_diff'] = '0'; }
				$row['users_diff'] = $a_diff['users'] = $users_diff; if( !$row['users_diff'] ) { $row['users_diff'] = '0'; }
				$row['activeusers_diff'] = $a_diff['activeusers'] = $activeusers_diff; if( !$row['activeusers_diff'] ) { $row['activeusers_diff'] = '0'; }
				$row['admins_diff'] = $a_diff['admins'] = $admins_diff; if( !$row['admins_diff'] ) { $row['admins_diff'] = '0'; }

				$this->debug('yamwatWIKI: get_history: a_diff: ' . print_r($a_diff,1));
				$row['activity_index'] = $this->get_activity_index( $a_diff );

				$this->history[] = $row;
			}

			$this->history[0]['seconds'] = $last['datetime'] - $first['datetime'];;
			$this->history[0]['days'] = round( $this->history[0]['seconds'] / 86400, 3);

			return TRUE;
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatWIKI: get_history: PDOException: ' . $e->getCode() .': '. $e->getMessage());
			return FALSE;
		}
	}

	// Get the first wiki history entry - Returns: TRUE or FALSE - Sets: $this->history
	function get_first_history($reload=FALSE) {
		if( $this->history && !$reload ) {
			$this->debug('WARNING: yamwatWIKI: get_first_history: history already set, reload false');
			return TRUE;
		}
		$this->history = array();
		if( !$this->wiki ) {
			$this->debug('ERROR: yamwatWIKI: get_first_history: no wiki');
			return FALSE;
		}
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatWIKI: get_first_history: can not open database');
				return FALSE;
			}
			$statement = $this->db->prepare(
				'SELECT * FROM wiki_history WHERE wiki = :wiki ORDER BY datetime ASC LIMIT 1');
			$statement->bindParam(':wiki', $this->wiki);
			$res = $statement->execute();
			$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
			if( !is_array($rows) || sizeof($rows) <= 0 ) {
				$this->debug('ERROR: yamwatWIKI: get_first_history: no history found');
				return TRUE;
			}
			while( list(,$row) = each($rows) ) {
				$row['datetime_utc'] = gmdate('Y-m-d H:i:s', $row['datetime']);
				$this->history[] = $row;
			}
			$this->debug('yamwatWIKI: get_first_history: OK');
			return TRUE;
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatWIKI: get_history: PDOException: ' . $e->getCode() .': '. $e->getMessage());
			return FALSE;
		}
	}

	// Get count of history entries of a wiki
	function get_history_count() {
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatWIKI: get_history_count: can not open database');
				return '';
			}
			$sql = 'SELECT count(wiki) AS count FROM wiki_history';
			if( $this->wiki ) { $sql .= ' WHERE wiki = :wiki'; }

			$statement = $this->db->prepare($sql);
			if( $this->wiki ) { $statement->bindParam(':wiki', $this->wiki); }
			$statement->execute();
			$res = $statement->fetch(PDO::FETCH_ASSOC);
			if( !$res['count'] ) { $res['count'] = '0'; }
			return $res['count'];
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatWIKI: get_history_count: PDOException: ' . $e->getCode() .': '. $e->getMessage());
			return '';
		}
	}

	// Get activity index of a wiki
	function get_activity_index( $diffs='' ) {
		/*

		Yamwat Activity Index

		Version 0.3

		Change              Points
		+   1 edits         +1.0
		+/- 1 admins        +0.25
		+/- 1 pages         +0.25
		+/- 1 articles      +0.25
		+/- 1 images        +0.25
		+   1 users         0
		+/- 1 activeusers   0

		1 point within time range of 86400 seconds (1 day) = activity index 1

		*/

		if( !is_array($diffs) ) { return ''; }
		$this->debug('yamwatWIKI: get_activity_index: diffs: ' . print_r($diffs,1));

		$datetime_diff = @$diffs['datetime'];
		$edits_diff = @$diffs['edits'];
		$admins_diff = @$diffs['admins'];
		$pages_diff = @$diffs['pages'];
		$articles_diff = @$diffs['articles'];
		$images_diff = @$diffs['images'];

		if( !$datetime_diff || $datetime_diff <= 0 ) {
			$this->debug('ERROR: yamwatWIKI: get_activity_index: no datetime_diff');
			return '';
		}

		$points = 0;
		$points += $edits_diff;
		$points += ( abs($admins_diff) ) * 0.25;
		$points += ( abs($pages_diff) ) * 0.25;
		$points += ( abs($articles_diff) ) * 0.25;
		$points += ( abs($images_diff) ) * 0.25;

		$this->debug('yamwatWIKI: get_activity_index: '
			. $points . ' points / ' . $datetime_diff . ' seconds');

		if( $points <= 0 ) { return 0; }

		$points = ( $points / $datetime_diff ) * 86400;

		$round = 0;
		if ( $points < 1 ) { $round = 3; }
		if ( $points < 10 ) { $round = 1; }
		$points = number_format($points, $round, '.', '');

		$this->debug('yamwatWIKI: get_activity_index: ' . $points);
		return $points;
	}

}

class yamwatLIST extends yamwatWIKI {

	var $topics, $networks, $languages, $versions;

	function __construct() { parent::__construct(); }

	// Get (array) list of (topics,networks,languages,versions)
	function get_list($list='') {
		if( !$list ) { return array(); }
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatLIST: get_list: can not open database');
				return FALSE;
			}
			$sql = "SELECT distinct($list) AS name FROM wiki ORDER BY $list";
			$statement = $this->db->prepare($sql);
			$statement->execute();
			$res = $statement->fetchAll(PDO::FETCH_ASSOC);
			if( !is_array($res) ) {
				$this->debug('ERROR: yamwatLIST: get_list: ' . $list);
				return array();
			}
			reset($res);
			$r = array();
			while( list(,$x) = each($res) ) {
				$r[] = array(
					'name' => $x['name'],
					'count' => $this->get_list_count($list, $x['name'])
				);
			}
			return $r;
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatLIST: get_list: PDOException: ' . $e->getCode() .': '. $e->getMessage());
			return array();
		}
	}

	// Get count of lists of (topics,networks,languages,versions)
	function get_list_count($list='', $name='') {
		if( !$list ) { return ''; }
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatLIST: get_list_count: can not open database');
				return "?";
			}
			$sql = "SELECT count($list) AS c FROM wiki WHERE $list = :name";
			if( !$name ) {
				$sql = "SELECT $list FROM wiki where $list IS NULL OR $list = ''";
			}
			$statement = $this->db->prepare($sql);
			if( $name ) { $statement->bindParam(':name', $name); }
			$statement->execute();

			if( $name ) {
				$res = $statement->fetch(PDO::FETCH_ASSOC);
				if( !is_array($res) ) {
					$this->debug('ERROR: yamwatLIST: get_list_count: '. $list);
					return '';
				}
				if( @!isset($res['c']) ) { $res['c'] = ''; }
				return $res['c'];
			} else {
				$res = $statement->fetchAll(PDO::FETCH_ASSOC);
				return sizeof($res);
			}

		} catch(PDOException $e) {
			$this->debug('yamwatLIST: get_list_count: PDOException: ' . $e->getCode() .': '. $e->getMessage());
			return '';
		}
	}

	function get_topics() { $this->topics = $this->get_list('topic'); }
	function get_networks() { $this->networks = $this->get_list('network'); }
	function get_languages() { $this->languages = $this->get_list('language'); }
	function get_versions() { $this->versions = $this->get_list('generator'); }

	function get_topics_count() { $this->topics = $this->get_list('topic'); return sizeof($this->topics); }
	function get_networks_count() { $this->networks = $this->get_list('network'); return sizeof($this->networks); }
	function get_languages_count() { $this->languages = $this->get_list('language'); return sizeof($this->languages); }
	function get_versions_count() { $this->versions = $this->get_list('generator'); return sizeof($this->versions); }

}

class yamwatCORE extends yamwatLIST {

	var $wikis, $where, $dir, $c;

	function __construct() { parent::__construct(); }

	// Get list of wikis - Returns: TRUE or FALSE - Sets: $this->wikis
	function get_wikis($reload=FALSE, $orderby='articles', $orderdir='DESC', $columns=array()) {

		$this->debug('yamwatCORE: get_wikis: wikis=' . @sizeof($this->wikis)
		. " reload=$reload orderby=$orderby orderdir=$orderdir columns=" . @sizeof($columns)
		. ' where=' . @$_GET['where'] . ' dir=' . @$_GET['dir'] . ' c=' . @$_GET['c']
		);

		if( !$reload && is_array($this->wikis) && sizeof($this->wikis) > 0  ) {
			$this->debug('WARNING: yamwatCORE: get_wikis: wikis already set. Reload FALSE. size: ' . sizeof($this->wikis));
			return TRUE;
		}
		if( @isset($_GET['network']) ) { $network = $_GET['network']; }
		if( @isset($_GET['topic']) ) { $topic = $_GET['topic']; }
		if( @isset($_GET['language']) ) { $language = $_GET['language']; }
		if( @isset($_GET['version']) ) { $generator = $_GET['version']; }

		$size_where = '';
		if( @isset($_GET['where']) ) {
			switch( $_GET['where'] ) {
				case 'aindex': case 'pages': case 'articles': case 'edits': case 'images': case 'users': case 'admins':
					$this->where = $_GET['where']; break;
				case 'ausers':
					$this->where = 'activeusers'; break;
				default:
					$this->debug('ERROR: yamwatCORE: get_wikis: unknown where type');
					return FALSE; break;
			}
			if( @isset($_GET['dir']) ) {
				switch( $_GET['dir'] ) {
					case 'gte': case 'gt': case 'lt': case 'lte':
						$this->dir = $_GET['dir']; break;
					default:
						$this->debug('ERROR: yamwatCORE: get_wikis: unknown direction');
						return FALSE; break;
				}
			}
			if( @isset($_GET['c']) ) {
				if( !ctype_digit($_GET['c']) || $_GET['c'] < 0 || $_GET['c'] > 999999999999 ) {
						$this->debug('ERROR: yamwatCORE: get_wikis: malformed count');
						return FALSE;
				}
				$this->c = $_GET['c'];
			}

			$size_where = 'AND ' . $this->where;
			switch( $this->dir ) {
				case 'gte': $dirr = '>='; break;
				case 'gt': $dirr = '>'; break;
				case 'lt': $dirr = '<'; break;
				case 'lte':	$dirr = '<='; break;
				default:
					$this->debug('ERROR: yamwatCORE: get_wikis: unknown direction');
					return FALSE;
					break;
			}
			$size_where .= " $dirr " . $this->c;
			$this->debug('yamwatCORE: get_wikis: size_where: ' . $size_where);
		}

		$where = array();
		if( @isset($network) ) { $where[] = 'network=:network'; }
		if( @isset($topic) ) { $where[] = 'topic=:topic'; }
		if( @isset($language) ) { $where[] = 'language=:language'; }
		if( @isset($generator) ) { $where[] = 'generator=:generator'; }
		$where_clause = '';
		if( sizeof($where) > 0 ) { $where_clause = ' AND ' . implode(' AND ', $where); }
		$sql = "SELECT wiki, api, aindex, protocol, network, topic,
		datetime, time, sitename, generator, language,
		pages, articles, edits, images, users, activeusers, admins
		FROM wiki WHERE 1=1
		$where_clause $size_where
		ORDER BY $orderby $orderdir";
		$this->debug('yamwatCORE: get_wikis: sql: ' . $sql);
		$this->wikis = array();
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatCORE: get_wikis: can not open database');
				return FALSE;
			}
			$statement = $this->db->prepare($sql);
			if( @isset($network) ) { $statement->bindParam(':network', $network); }
			if( @isset($topic) ) { $statement->bindParam(':topic', $topic); }
			if( @isset($language) ) { $statement->bindParam(':language', $language); }
			if( @isset($generator) ) { $statement->bindParam(':generator', $generator); }
			$statement->execute();
			while( $row = $statement->fetch(PDO::FETCH_ASSOC) ) {
				$row['datetime_utc'] = gmdate('Y-m-d H:i:s', $row['datetime']);
				$this->wikis[$row['wiki']] = $row;
			}
			$this->debug('yamwatCORE: get_wikis: wikis=' . @sizeof($this->wikis));
			return TRUE;
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatCORE: get_wikis: PDOException: ' . $e->getCode() .': '. $e->getMessage());
			return FALSE;
		}
	}

	// Get count of wikis
	function get_wikis_count() {
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatCORE: get_wikis_count: can not open database');
				return '';
			}
			$statement = $this->db->prepare('SELECT count(wiki) AS count FROM wiki');
			$statement->execute();
			$res = $statement->fetch(PDO::FETCH_ASSOC);
			if( !$res['count'] ) { $res['count'] = '0'; }
			return $res['count'];
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatCORE: get_wikis_count: PDOException: ' . $e->getCode() .': '. $e->getMessage());
			return '';
		}
	}

	// Get time of last wiki update
	function get_system_last_update($mode='') {
		try {
			if( !$this->open_db() ) {
				$this->debug('ERROR: yamwatCORE: get_system_last_update: can not open database');
				return '';
			}
			$sql = 'SELECT datetime FROM wiki ORDER BY datetime DESC LIMIT 1';
			$statement = $this->db->prepare($sql);
			$statement->execute();
			$res = $statement->fetch(PDO::FETCH_ASSOC);
			if( $mode == 'unix' ) { return $res['datetime']; }
			if( !$res['datetime'] ) { return ''; }
			return gmdate('Y-m-d H:i:s e', $res['datetime']);
		} catch(PDOException $e) {
			$this->debug('ERROR: yamwatCORE: get_system_last_update: PDOException: ' . $e->getCode() .': '. $e->getMessage());
			return '';
		}
	}

}
