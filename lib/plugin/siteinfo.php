<?php
// Yamwat - Yet Another MediaWiki API Tool
// Plugin: siteinfo

class siteinfoPlugin {

	var $yamwat, $siteinfo, $save;

	function __construct( &$yamwat ) {
		$this->yamwat = $yamwat; 
		$this->save = FALSE;
		if( isset($_GET['save']) && $_GET['save'] ) { $this->save = TRUE; }
	}

	function name() { return 'siteinfo'; }

	function menu_name() { return 'Get siteinfo'; }

	function cli_usage() { return 'a=siteinfo wiki=example.com [save=1]'; }

	function extra_fields() { return ' <input type="checkbox" checked="checked" name="save">save to db'; }

	function required() { return array('wiki'); }

	function optional() { return array('save'); }

	function parameters() { 
		return '?action=query&format=json&meta=siteinfo&siprop=general|statistics';
	}

	function process($x='') {
		
		if( !isset($x->query->statistics) ) {
			$this->yamwat->debug('ERROR: PLUGIN: siteinfo: invalid response: missing statistics');
			return $this->yamwat->message;
		}

		$this->yamwat->debug('PLUGIN: siteinfo: process: wiki: ' . $this->yamwat->wiki);
		
		$res = $this->yamwat->get_siteinfo();

		$this->siteinfo = array();
		$this->siteinfo['wiki'] = $this->yamwat->wiki;
		$this->siteinfo['datetime'] = time();
		$this->siteinfo['api'] = @$this->yamwat->siteinfo['api']; if( !$this->siteinfo['api'] ) { $this->siteinfo[''] = 'api'; }
		$this->siteinfo['protocol'] = @$this->yamwat->siteinfo['protocol']; if( !$this->siteinfo['protocol'] ) { $this->siteinfo['protocol'] = ''; }
		$this->siteinfo['network'] = @$this->yamwat->siteinfo['network']; if( !$this->siteinfo['network'] ) { $this->siteinfo['network'] = ''; }
		$this->siteinfo['topic'] = @$this->yamwat->siteinfo['topic']; if( !$this->siteinfo['topic'] ) { $this->siteinfo['topic'] = ''; }
		$this->siteinfo['pages'] = @$x->query->statistics->pages; if( !$this->siteinfo['pages'] ) { $this->siteinfo['pages'] = '0'; }
		$this->siteinfo['articles'] = @$x->query->statistics->articles; if( !$this->siteinfo['articles'] ) { $this->siteinfo['articles'] = '0'; }
		$this->siteinfo['edits'] = @$x->query->statistics->edits; if( !$this->siteinfo['edits'] ) { $this->siteinfo['edits'] = '0'; }
		$this->siteinfo['images'] = @$x->query->statistics->images; if( !$this->siteinfo['images'] ) { $this->siteinfo['images'] = '0'; }
		$this->siteinfo['users'] = @$x->query->statistics->users; if( !$this->siteinfo['users'] ) { $this->siteinfo['users'] = '0'; }
		$this->siteinfo['activeusers'] = @$x->query->statistics->activeusers; if( !$this->siteinfo['activeusers'] ) { $this->siteinfo['activeusers'] = '0'; }
		$this->siteinfo['admins'] = @$x->query->statistics->admins; if( !$this->siteinfo['admins'] ) { $this->siteinfo['admins'] = '0'; }
		$this->siteinfo['jobs'] = @$x->query->statistics->jobs; if( !$this->siteinfo['jobs'] ) { $this->siteinfo['jobs'] = '0'; }
		$this->siteinfo['mainpage'] = @$x->query->general->mainpage; if( !$this->siteinfo['mainpage'] ) { $this->siteinfo['mainpage'] = ''; }
		$this->siteinfo['base'] = @$x->query->general->base; if( !$this->siteinfo['base'] ) { $this->siteinfo['base'] = ''; }
		$this->siteinfo['sitename'] = @$x->query->general->wikiname; if( !$this->siteinfo['sitename'] ) { $this->siteinfo['sitename'] = ''; }
		$this->siteinfo['generator'] = @$x->query->general->generator; if( !$this->siteinfo['generator'] ) { $this->siteinfo['generator'] = ''; }
		$this->siteinfo['case'] = @$x->query->general->case; if( !$this->siteinfo['case'] ) { $this->siteinfo['case'] = ''; }
		$this->siteinfo['rights'] = @$x->query->general->rights; if( !$this->siteinfo['rights'] ) { $this->siteinfo['rights'] = ''; }
		$this->siteinfo['language'] = @$x->query->general->lang; if( !$this->siteinfo['language'] ) { $this->siteinfo['language'] = ''; }
		$this->siteinfo['phpversion'] = @$x->query->general->phpversion; if( !$this->siteinfo['phpversion'] ) { $this->siteinfo['phpversion'] = ''; }
		$this->siteinfo['phpsapi'] = @$x->query->general->phpsapi; if( !$this->siteinfo['phpsapi'] ) { $this->siteinfo['phpsapi'] = ''; }
		$this->siteinfo['dbtype'] = @$x->query->general->dbtype; if( !$this->siteinfo['dbtype'] ) { $this->siteinfo['dbtype'] = ''; }
		$this->siteinfo['dbversion'] = @$x->query->general->dbversion; if( !$this->siteinfo['dbversion'] ) { $this->siteinfo['dbversion'] = ''; }
		$this->siteinfo['rev'] = @$x->query->general->rev; if( !$this->siteinfo['rev'] ) { $this->siteinfo['rev'] = ''; }
		$this->siteinfo['fallback8bitEncoding'] = @$x->query->general->fallback8bitEncoding; if( !$this->siteinfo['fallback8bitEncoding'] ) { $this->siteinfo['fallback8bitEncoding'] = ''; }
		$this->siteinfo['writeapi'] = @$x->query->general->writeapi; if( !$this->siteinfo['writeapi'] ) { $this->siteinfo['writeapi'] = ''; }
		$this->siteinfo['timezone'] = @$x->query->general->timezone; if( !$this->siteinfo['timezone'] ) { $this->siteinfo['timezone'] = ''; }
		$this->siteinfo['timeoffset'] = @$x->query->general->timeoffset; if( !$this->siteinfo['timeoffset'] ) { $this->siteinfo['timeoffset'] = ''; }
		$this->siteinfo['articlepath'] = @$x->query->general->articlepath; if( !$this->siteinfo['articlepath'] ) { $this->siteinfo['articlepath'] = ''; }
		$this->siteinfo['scriptpath'] = @$x->query->general->scriptpath; if( !$this->siteinfo['scriptpath'] ) { $this->siteinfo['scriptpath'] = ''; }
		$this->siteinfo['script'] = @$x->query->general->script; if( !$this->siteinfo['script'] ) { $this->siteinfo['script'] = ''; }
		$this->siteinfo['variantarticlepath'] = @$x->query->general->variantarticlepath; if( !$this->siteinfo['variantarticlepath'] ) { $this->siteinfo['variantarticlepath'] = ''; }
		$this->siteinfo['server'] = @$x->query->general->server; if( !$this->siteinfo['server'] ) { $this->siteinfo['server'] = ''; }
		$this->siteinfo['wikiid']= @$x->query->general->wikiid; if( !$this->siteinfo['wikiid'] ) { $this->siteinfo['wikiid'] = ''; }
		$this->siteinfo['time'] = @$x->query->general->time; if( !$this->siteinfo['time'] ) { $this->siteinfo['time'] = ''; }

		
		$hc = $this->yamwat->get_history_count();
		$this->yamwat->debug('PLUGIN: siteinfo: process: history_count: ' . $hc);
		
		switch( $hc ) {
			case 0:
				$this->yamwat->debug('PLUGIN: siteinfo: process: first entry');
				$this->siteinfo['aindex'] = ''; // first entry, do not get activity index
				break;
			case 1:
				$this->yamwat->debug('PLUGIN: siteinfo: process: second entry');
				$diffs = array();
				$diffs['datetime'] = $this->siteinfo['datetime'] - $this->yamwat->siteinfo['datetime'];
				$diffs['edits'] = $this->siteinfo['edits'] - $this->yamwat->siteinfo['edits'];
				$diffs['admins'] = $this->siteinfo['admins'] - $this->yamwat->siteinfo['admins'];
				$diffs['pages'] = $this->siteinfo['pages'] - $this->yamwat->siteinfo['pages'];
				$diffs['articles'] = $this->siteinfo['articles'] - $this->yamwat->siteinfo['articles'];
				$diffs['images'] = $this->siteinfo['images'] - $this->yamwat->siteinfo['images'];		
				$this->siteinfo['aindex'] = $this->yamwat->get_activity_index( $diffs );
				break;
			default:
				$res = $this->yamwat->get_first_history();
				$first = $this->yamwat->history[0];
				$this->yamwat->debug('PLUGIN: siteinfo: process: this datetime: ' . $this->siteinfo['datetime']);
				$this->yamwat->debug('PLUGIN: siteinfo: process: first datetime: ' . $first['datetime']);
				$diffs = array();
				$diffs['datetime'] = $this->siteinfo['datetime'] - $first['datetime'];
				$diffs['edits'] = $this->siteinfo['edits'] - $first['edits'];
				$diffs['admins'] = $this->siteinfo['admins'] - $first['admins'];
				$diffs['pages'] = $this->siteinfo['pages'] - $first['pages'];
				$diffs['articles'] = $this->siteinfo['articles'] - $first['articles'];
				$diffs['images'] = $this->siteinfo['images'] - $first['images'];		
				$this->siteinfo['aindex'] = $this->yamwat->get_activity_index( $diffs );				
				break;
		}
		if( $hc <= 0 ) {
			
		} elseif( $hc == 0 ) {
		

		} else {
			
		}
		

		$this->yamwat->debug('PLUGIN: siteinfo: process: ' . print_r($this->siteinfo,1));
		if( $this->save ) {
			if( !$this->save() ) {
				return 'ERROR: PLUGIN: siteinfo: can not save to database';
			}
		}
		return $this->siteinfo;
	}

	function save() {
		$this->yamwat->debug('PLUGIN: siteinfo: save');
		$ra = $this->save_into('wiki', $mode='replace');
		$rb = $this->save_into('wiki_history', $mode='insert');
		if( $ra && $rb ) { return TRUE; }
		return FALSE;
	}
	
	function save_into($table='', $mode='') {
		$this->yamwat->debug("PLUGIN: siteinfo: save_into: table=$table mode=$mode");
		
		if( !$table ) {
			$this->yamwat->debug('ERROR: PLUGIN: siteinfo: save_into: missing table name');
			return FALSE;
		}
		switch( $mode ) {
			case 'insert': $mode = 'INSERT INTO'; break;
			case 'replace': $mode = 'INSERT OR REPLACE INTO'; break;
			default: $this->yamwat->debug('ERROR: PLUGIN: siteinfo: save_into: missing mode (insert or replace)'); return FALSE; break;
		}
		if( !is_array($this->siteinfo) ) {
			$this->yamwat->debug('ERROR: PLUGIN: siteinfo: save_into: siteinfo not array');
			return FALSE; 
		}
		try {
			$this->yamwat->open_db();
			$statement = $this->yamwat->db->prepare("
				$mode '$table' (
					'wiki', 'datetime', 'api', 'protocol', 'network', 'topic', 'sitename', 'pages', 'articles',
					'edits', 'images', 'users', 'activeusers', 'admins', 'jobs',
					'mainpage', 'base', 'generator', 'case', 'rights', 'language',
					'phpversion', 'phpsapi', 'dbtype', 'dbversion', 'rev', 'fallback8bitEncoding',
					'writeapi', 'timezone', 'timeoffset', 'articlepath', 'scriptpath', 'script',
					'variantarticlepath', 'server', 'wikiid', 'time', 'aindex'
				) VALUES (
					:wiki, :datetime, :api, :protocol, :network, :topic, :sitename, :pages, :articles,
					:edits, :images, :users, :activeusers, :admins, :jobs,
					:mainpage, :base, :generator, :case, :rights, :language,
					:phpversion, :phpsapi, :dbtype, :dbversion, :rev, :fallback8bitEncoding,
					:writeapi, :timezone, :timeoffset, :articlepath, :scriptpath, :script,
					:variantarticlepath, :server, :wikiid, :time, :aindex
				);
			");
			$statement->bindParam(':wiki', $this->siteinfo['wiki'] );
			$statement->bindParam(':datetime', $this->siteinfo['datetime'] );
			$statement->bindParam(':api', $this->siteinfo['api'] );
			$statement->bindParam(':protocol', $this->siteinfo['protocol'] );
			$statement->bindParam(':network', $this->siteinfo['network'] );
			$statement->bindParam(':topic', $this->siteinfo['topic'] );
			$statement->bindParam(':sitename', $this->siteinfo['sitename'] );
			$statement->bindParam(':pages', $this->siteinfo['pages'] );
			$statement->bindParam(':articles', $this->siteinfo['articles'] );
			$statement->bindParam(':edits', $this->siteinfo['edits'] );
			$statement->bindParam(':images', $this->siteinfo['images'] );
			$statement->bindParam(':users', $this->siteinfo['users'] );
			$statement->bindParam(':activeusers', $this->siteinfo['activeusers'] );
			$statement->bindParam(':admins', $this->siteinfo['admins'] );
			$statement->bindParam(':jobs', $this->siteinfo['jobs'] );
			$statement->bindParam(':mainpage', $this->siteinfo['mainpage'] );
			$statement->bindParam(':base', $this->siteinfo['base'] );
			$statement->bindParam(':generator', $this->siteinfo['generator'] );
			$statement->bindParam(':case', $this->siteinfo['case'] );
			$statement->bindParam(':rights', $this->siteinfo['rights'] );
			$statement->bindParam(':language', $this->siteinfo['language'] );
			$statement->bindParam(':phpversion', $this->siteinfo['phpversion'] );
			$statement->bindParam(':phpsapi', $this->siteinfo['phpsapi'] );
			$statement->bindParam(':dbtype', $this->siteinfo['dbtype'] );
			$statement->bindParam(':dbversion', $this->siteinfo['dbversion'] );
			$statement->bindParam(':rev', $this->siteinfo['rev'] );
			$statement->bindParam(':fallback8bitEncoding', $this->siteinfo['fallback8bitEncoding'] );
			$statement->bindParam(':writeapi', $this->siteinfo['writeapi'] );
			$statement->bindParam(':timezone', $this->siteinfo['timezone'] );
			$statement->bindParam(':timeoffset', $this->siteinfo['timeoffset'] );
			$statement->bindParam(':articlepath', $this->siteinfo['articlepath'] );
			$statement->bindParam(':scriptpath', $this->siteinfo['scriptpath'] );
			$statement->bindParam(':script', $this->siteinfo['script'] );
			$statement->bindParam(':variantarticlepath', $this->siteinfo['variantarticlepath'] );
			$statement->bindParam(':server', $this->siteinfo['server'] );
			$statement->bindParam(':wikiid', $this->siteinfo['wikiid'] );
			$statement->bindParam(':time', $this->siteinfo['time'] );
			$statement->bindParam(':aindex', $this->siteinfo['aindex'] );
			$statement->execute();
			$this->yamwat->debug("PLUGIN: siteinfo: save_into: OK: mode:$mode table:$table");
			$this->siteinfo['saved_to_table_' . $table] = time();
			return TRUE;
		} catch(PDOException $e) {
			$this->yamwat->debug('ERROR: PLUGIN: siteinfo: save_into: PDOException: ' . $e->getCode() . ': ' . $e->getMessage() );
			return FALSE;
		} 
	}
}
