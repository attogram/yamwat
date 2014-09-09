<?php
// Yamwat - Yet Another MediaWiki API Tool
// WEB

if( !isset($config['yamwat_home']) ) { print 'ERROR: yamwat WEB: $config[\'yamwat_home\'] not set'; exit; }
include_once($config['yamwat_home'] . 'lib/yamwat.core.php');
include_once($config['yamwat_home'] . 'lib/Smarty/Smarty.class.php');

class yamwatSMARTY extends yamwatCORE {

	var $smarty;

	function __construct() {

		parent::__construct();

		$this->smarty = new Smarty;
		if( !is_object($this->smarty) ) {
			$this->web_fail('ERROR: yamwatSMARTY: __construct: can not create Smarty object');
		}
		$this->smarty->debugging = @$this->config['SMARTY_debug'];
		if( $this->smarty->debugging ) { $this->debug('SMARTY: debug: TRUE'); }

		$template_dir = $this->config['yamwat_home'] . $this->config['TemplateDir'];
		if( !is_readable($template_dir) ) {
			$this->web_fail('ERROR: yamwatSMARTY: __construct: can not read from directory: ' . $template_dir);
		}
		$this->smarty->setTemplateDir( $template_dir  );

		$compile_dir = $this->config['yamwat_home'] . $this->config['CompileDir'];
		if( !is_writeable($compile_dir) ) {
			$this->web_fail('ERROR: yamwatSMARTY: __construct: can not write to directory: ' . $compile_dir);
		}
		$this->smarty->setCompileDir( $compile_dir );

		$this->smarty->caching = FALSE;
		$this->smarty->error_reporting = E_ALL & ~E_NOTICE; // disable notices
	}

	function display_template($tpl) {
		try {
			$this->smarty->display($tpl);
		} catch(SmartyException $e) {
			$this->debug('ERROR: yamwatSMARTY: display_template: ' . $tpl . ': SmartyException: ' . $e->getMessage() );
		}
	}
}

class yamwatWEB extends yamwatSMARTY {

	var $action, $namespace, $limit, $search, $user, $enable_web_admin, $is_admin;

	function __construct() {

		parent::__construct();

		$this->enable_web_admin = @$this->config['enable_web_admin'];
		$this->is_admin = FALSE;
		if( $this->enable_web_admin ) {
			$this->debug('yamwatWEB: __construct: web admin enabled');
			if( !session_start() ) { $this->web_fail('ERROR: yamwatWEB: __construct: session failed'); }
			$this->debug('yamwatWEB: __construct: session started');
			if( @$_SESSION['is_admin'] ) {
				$this->is_admin = TRUE;
				$this->debug('yamwatWEB: __construct: session: admin login OK');
			}
		}
		$this->smarty->assign('enable_web_admin', $this->enable_web_admin );
		$this->smarty->assign('enable_contact_form', $this->config['enable_contact_form'] );
		$this->smarty->assign('is_admin', $this->is_admin );

		$this->action = ( isset($_GET['a']) && $_GET['a'] != '' ) ? trim($_GET['a']) : NULL;
		if($this->action) { $this->debug('yamwatWEB: __construct: action: ' . $this->action); }

		$this->smarty->assign('system_name', $this->config['system_name']);
		$this->smarty->assign('core_url', $this->core_url);
		$this->smarty->assign('core_version', $this->core_version );
		$this->smarty->assign('time_utc', gmdate('Y-m-d H:i:s') );

		if( !$this->do_internal_action() ) {
			if( !$this->enable_web_admin ) { $this->web_fail('Web Admin Disabled'); }
			if( !$this->plugin_exists() ) { $this->web_fail('404 Not Found', $fof=TRUE); }
			$this->admin_login();
			$this->smarty->assign('result', $this->do_action() );
			$this->smarty->assign('wiki', $this->wiki );
			$this->smarty->assign('action', htmlentities($this->action) );
			$this->smarty->assign('html_title', $this->action . ' - ' . $this->config['system_name'] );
			$this->display_template('header.tpl');
			$this->display_template('admin/result.tpl');
		}

		$this->footer();

	}

	function plugin_menu() {
		$this->load_plugins();
		$r = '';
		while( list($name,$x) = each($this->plugins) ) {
			$r .= '<input type="radio" name="a" value="' . $name . '"'
			. ( $name==$this->action ? ' checked' : '') . '>' . $x->menu_name()
			. $x->extra_fields() . '<br />';
		}
		return $r;
	}

	// Do internal action - Returns: TRUE or FALSE
	function do_internal_action() {

		try {
			switch( $this->action ) {

				case '':
					$this->smarty->assign('wikis_count', $this->get_wikis_count());
					$this->smarty->assign('topics_count', $this->get_topics_count());
					$this->smarty->assign('networks_count', $this->get_networks_count());
					$this->smarty->assign('languages_count', $this->get_languages_count());
					$this->smarty->assign('versions_count', $this->get_versions_count());
					$this->smarty->assign('history_count', $this->get_history_count());
					$this->smarty->assign('system_last_update', $this->get_system_last_update());
					$this->display_template('header.tpl');
					$this->display_template('home.tpl');
					break;

				case 'wikis':
					if( @isset($_GET['network']) ) {
						$this->smarty->assign('network', htmlentities(@$_GET['network']) );
						if( !isset($_GET['where']) ) { $_GET['c'] = '0'; }
					}
					if( @isset($_GET['topic']) ) {
						$this->smarty->assign('topic', htmlentities(@$_GET['topic']) );
						if( !isset($_GET['where']) ) { $_GET['c'] = '0'; }
					}
					if( @isset($_GET['language']) ) {
						$this->smarty->assign('language', htmlentities(@$_GET['language']) );
						if( !isset($_GET['where']) ) { $_GET['c'] = '0'; }
					}
					if( @isset($_GET['version']) ) {
						$this->smarty->assign('version', htmlentities(@$_GET['version']) );
						if( !isset($_GET['where']) ) { $_GET['c'] = '0'; }
					}

					if( !isset($_GET['where']) ) {
						$_GET['where'] = 'pages';
						if( @isset($this->config['default_where']) ) { $_GET['where'] = $this->config['default_where']; }
					}
					if( !isset($_GET['dir']) ) {
						$_GET['dir'] = 'gte';
						if( @isset($this->config['default_where_dir']) ) { $_GET['dir'] = $this->config['default_where_dir']; }
					}
					if( !isset($_GET['c']) ) {
						$_GET['c'] = '0';
						if( @isset($this->config['default_where_count']) ) { $_GET['c'] = $this->config['default_where_count']; }
					}

					if( !$this->get_wikis() ) { $this->smarty->assign('error', $this->message); }
					$this->smarty->assign('wikis', $this->wikis );
					$this->smarty->assign('wikis_count', $this->get_wikis_count());
					$this->smarty->assign('where', htmlentities($this->where) );
					$this->smarty->assign('dir', htmlentities($this->dir) );
					$this->smarty->assign('c', htmlentities($this->c) );
					$this->smarty->assign('html_title', 'Wikis List - ' . $this->config['system_name'] );
					$this->display_template('header.tpl');
					$this->display_template('wikis.tpl');
					break;

				case 'wiki':
					if( !$this->get_siteinfo() ) {
						$this->debug('ERROR: yamwatWEB: do_internal_action: wiki: no wiki found');
						$this->web_fail('404 wiki not found', $fof=TRUE);
					}
					$this->smarty->assign('siteinfo', $this->siteinfo );
					$this->smarty->assign('history_count', $this->get_history_count());
					$this->smarty->assign('html_title', $this->wiki . ' - info - ' . $this->config['system_name'] );
					$this->display_template('header.tpl');
					$this->display_template('wiki.tpl');
					break;

				case 'wiki.history':
					if( !$this->get_history() ) {
						$this->debug('ERROR: yamwatWEB: do_internal_action: wiki.history: no wiki found');
						$this->web_fail('404 wiki not found', $fof=TRUE);
					}
					$this->smarty->assign('history', $this->history);
					$this->smarty->assign('siteinfo', $this->siteinfo);
					$this->smarty->assign('wiki',  $this->wiki);
					$this->smarty->assign('html_title', $this->wiki . ' - history - ' . $this->config['system_name']);
					$this->display_template('header.tpl');
					$this->display_template('wiki.history.tpl');
					break;

				case 'topics':
					$this->get_topics();
					$this->smarty->assign('list_name', 'topics');
					$this->smarty->assign('url_name', 'topic');
					$this->smarty->assign('list', $this->topics);
					$this->smarty->assign('html_title', 'Wiki topics - ' . $this->config['system_name']);
					$this->display_template('header.tpl');
					$this->display_template('list.tpl');
					break;
				case 'networks':
					$this->get_networks();
					$this->smarty->assign('list_name', 'networks');
					$this->smarty->assign('url_name', 'network');
					$this->smarty->assign('list', $this->networks);
					$this->smarty->assign('html_title', 'Wiki networks - ' . $this->config['system_name']);
					$this->display_template('header.tpl');
					$this->display_template('list.tpl');
					break;
				case 'languages':
					$this->get_languages();
					$this->smarty->assign('list_name', 'languages');
					$this->smarty->assign('url_name', 'language');
					$this->smarty->assign('list', $this->languages);
					$this->smarty->assign('html_title', 'Wiki languages - ' . $this->config['system_name']);
					$this->display_template('header.tpl');
					$this->display_template('list.tpl');
					break;
				case 'versions':
					$this->get_versions();
					$this->smarty->assign('list_name', 'versions');
					$this->smarty->assign('url_name', 'version');
					$this->smarty->assign('html_title', 'Wiki versions - ' . $this->config['system_name']);
					$this->display_template('header.tpl');
					$this->smarty->assign('list', $this->versions);
					$this->display_template('list.tpl');
					break;

				case 'contact':
					if( !$this->config['enable_contact_form'] ) { $this->web_fail(); }
					$this->smarty->assign('result', $this->web_contact());
					$this->smarty->assign('email', @htmlentities(@$_POST['email']));
					$this->smarty->assign('msg', @htmlentities(@$_POST['msg']));
					$this->display_template('header.tpl');
					$this->display_template('contact.tpl');
					break;

				// Admin actions
				case 'logoff':
					$this->is_admin = FALSE;
					$_SESSION = array();
					if (ini_get("session.use_cookies")) {
						$params = session_get_cookie_params();
						setcookie(session_name(), '', time() - 42000,
							$params["path"], $params["domain"],
							$params["secure"], $params["httponly"]
						);
					}
					session_destroy();
					$this->smarty->assign('result', 'Logged Off' );
					$this->smarty->assign('wiki', NULL );
					$this->smarty->assign('action', 'logoff' );
					$this->smarty->assign('html_title', $this->action . ' - ' . $this->config['system_name']);
					$this->display_template('header.tpl');
					$this->display_template('admin/result.tpl');
					break;


				case 'admin':
					if( !$this->enable_web_admin ) { $this->web_fail('Web Admin Disabled'); }
					$this->admin_login();
					$this->smarty->assign('html_title', $this->action . ' - ' . $this->config['system_name']);
					$this->display_template('header.tpl');
					$this->display_template('admin/home.tpl');
					break;

				case 'tools':
					if( !$this->enable_web_admin ) { $this->web_fail('Web Admin Disabled'); }
					$this->admin_login();
					$this->load_plugins();
					$this->get_wikis($reload=1,$orderby='wiki',$orderdir='ASC');
					$this->smarty->assign('wikis', $this->wikis );
					$this->smarty->assign('wiki', $this->wiki );
					$this->smarty->assign('menu', $this->plugin_menu() );
					$this->smarty->assign('html_title', $this->action . ' - ' . $this->config['system_name']);
					$this->display_template('header.tpl');
					$this->display_template('admin/tools.tpl');
					break;

				case 'edit':
					if( !$this->enable_web_admin ) { $this->web_fail('Web Admin Disabled'); }
					$this->admin_login();
					$this->smarty->assign('wiki', $this->wiki );
					if( $this->wiki && @$_GET['go'] ) {
						$res = $this->web_edit_wiki();
						$this->smarty->assign('result', $this->message);
					}
					unset($_GET['network']);
					unset($_GET['topic']);
					unset($_GET['language']);
					unset($_GET['version']);
					$this->get_wikis($reload=1,$orderby='wiki',$orderdir='ASC');
					$this->smarty->assign('wikis', $this->wikis );
					$this->smarty->assign('html_title', $this->action . ' - ' . $this->config['system_name']);
					$this->display_template('header.tpl');
					$this->display_template('admin/edit.tpl');
					break;

				case 'add':
					if( !$this->enable_web_admin ) { $this->web_fail('Web Admin Disabled'); }
					$this->admin_login();
					if( $this->wiki ) {
						$res = $this->add_wiki();
						$this->smarty->assign('result', $this->message);
					} else {
						$this->smarty->assign('result', NULL);
					}
					$this->smarty->assign('html_title', $this->action . ' - ' . $this->config['system_name']);
					$this->display_template('header.tpl');
					$this->display_template('admin/add.tpl');
					break;
				default:
					$this->debug('yamwatWEB: do_internal_action: action not found');
					return FALSE;
					break;
			}
		} catch(SmartyException $e) {
			$this->debug('ERROR: yamwatWEB: do_internal_action: SmartyException: ' . $e->getMessage());
			return FALSE;
		}
		return TRUE;
	}

	// Do (plugin) action - Returns ARRAY or FALSE
	function do_action() {

		$this->namespace = ( isset($_GET['ns']) && $_GET['ns'] != '' ) ? trim($_GET['ns']) : NULL;
		if($this->namespace) { $this->debug('yamwatWEB: do_action: namespace: ' . $this->namespace); }

		$this->limit = ( isset($_GET['limit']) && $_GET['limit'] != '' ) ? trim($_GET['limit']) : NULL;
		if($this->limit) { $this->debug('yamwatWEB: do_action: limit:' . $this->limit); }

		$this->search = ( isset($_GET['search']) && $_GET['search'] != '' ) ? trim($_GET['search']) : NULL;
		if($this->search) { $this->debug('yamwatWEB: do_action: search: ' . $this->search); }

		$this->user = ( isset($_GET['user']) && $_GET['user'] != '' ) ? trim($_GET['user']) : NULL;
		if($this->user) { $this->debug('yamwatWEB: do_action: user: ' . $this->user); }

		$params = $this->plugins[$this->action]->parameters();
		if( !$params ) { return $this->plugins[$this->action]->process(''); }

		$this->get_wikis();
		if( !isset($this->wikis[$this->wiki]) ) {
			$this->debug('ERROR: yamwatWEB: do_action: unknown wiki');
			return FALSE;
		}

		$required = $this->plugins[$this->action]->required();
		while( list(,$c) = each( $required ) ) {
			if( !isset( $this->{$c} ) || $this->{$c} == '' ) {
				$this->debug('ERROR: yamwatWEB: do_action: Missing required input: ' . $c);
				return FALSE;
			}
		}

		$this->protocol = $this->wikis[$this->wiki]['protocol'];
		if( $this->protocol != 'https' ) { $this->protocol = 'http'; }

		$this->debug('yamwatWEB: do_action: protocol: ' . $this->protocol);
		$this->debug('yamwatWEB: do_action: wiki: ' . $this->wiki);
		$this->debug('yamwatWEB: do_action: api: ' . $this->wikis[$this->wiki]['api']);

		$this->url = '' . $this->protocol . '://' . $this->wiki . $this->wikis[$this->wiki]['api'] . $params;

		$x = $this->get();
		if( !$x ) { return 'ERROR: yamwatWEB: do_action: ' . $this->message; }

		return $this->plugins[$this->action]->process($x);
	}

	// Edit/Delete a wiki - Returns TRUE or FALSE
	function web_edit_wiki() {
		if( isset($_GET['delete']) ) {
			if( $this->delete_wiki() ) { return TRUE; }
			return FALSE;
		}
		if( $this->edit_wiki() ) { return TRUE; }
		return FALSE;
	}

	function footer() {

		$gtime = microtime(1) - $this->start_time;
		$gtime = round($gtime,4);
		if( $gtime <= 0.0001 ) { $gtime = '> 0.0001'; }
		$this->smarty->assign('generation_time', $gtime );
		$this->smarty->assign('debug', $this->get_debug() );
		$this->display_template('footer.tpl');
	}

	function web_fail($m='', $fof=FALSE) {
		if( $fof ) {
			header('HTTP/1.0 404 Not Found');
			$this->debug('yamwatWEB: web_fail: HTTP/1.0 404 Not Found');
		}
		$this->smarty->assign('html_title', 'ERROR - ' . $this->config['system_name'] );
		$this->display_template('header.tpl');
		$this->display_template('menu.tpl');
		print '<div class="content"><p>ERROR</p>';
		if($m) { print "$m"; }
		print '</div>';
		$this->footer();
		exit;
	}

	function admin_login() {

		if( @$_SESSION['is_admin'] ) {
			$this->is_admin = TRUE;
			$this->smarty->assign('is_admin', $this->is_admin );
			$this->debug('yamwatWEB: admin_login: session logged in OK');
			return;
		}

		if( !@isset($_POST['p']) || !$_POST['p'] ) {
			$this->debug('yamwatWEB: admin_login: not logged in');
			$this->smarty->assign('HTTP_HOST', $_SERVER['HTTP_HOST']);
			$this->smarty->assign('REQUEST_URI', $_SERVER['REQUEST_URI']);
			$this->smarty->assign('html_title', 'login - ' . $this->config['system_name'] );
			$this->display_template('header.tpl');
			$this->display_template('admin/login.tpl');
			$this->footer();
			exit;
		}

		if( $_POST['p'] === $this->config['web_admin_password'] ) {
			$this->debug('yamwatWEB: admin_login: password OK');
			$_SESSION['is_admin'] = TRUE;
			$this->is_admin = TRUE;
			$this->smarty->assign('is_admin', $this->is_admin );
			return;
		}

		$this->debug('ERROR: yamwatWEB: admin_login: bad password');
		$this->smarty->assign('HTTP_HOST', $_SERVER['HTTP_HOST']);
		$this->smarty->assign('REQUEST_URI', $_SERVER['REQUEST_URI']);
		$this->smarty->assign('html_title', 'login - ' . $this->config['system_name'] );
		$this->display_template('header.tpl');
		$this->display_template('admin/login.tpl');
		$this->footer();
		exit;

	}

	// Web contact form
	function web_contact() {

		$email = @$_POST['email'];
		$msg = @$_POST['msg'];

		if( !$email && !$msg ) {
			$this->debug('yamwatWEB: web_contact: empty submit');
			return FALSE;
		}

		if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
			$this->debug('ERROR: invalid email');
			return $this->message;
		}

		if( !$msg ) {
			$this->debug('ERROR: missing message');
			return $this->message;
		}

		$res = @mail(
			$this->config['contact_to'],
			$this->config['system_name'] . ' CONTACT FORM',
			$msg,
			'From: ' . $this->config['contact_from'] . "\r\n"
		);

		if( !$res ) {
			$this->debug('ERROR: email send failed');
			return $this->message;
		}

		return 'Message sent OK.  Thank you!';
	}
}
