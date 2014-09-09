<?php
// Yamwat - Yet Another MediaWiki API Tool
// CLI classes

if( !isset($config['yamwat_home']) ) { print 'ERROR: yamwat CLI: $config[\'yamwat_home\'] not set'; exit; }

include_once($config['yamwat_home'] . 'lib/yamwat.core.php');

class yamwatCLI extends yamwatCORE {

	var $silent;

	function __construct() {

		global $argv, $debug_log;

		parent::__construct();

		if( PHP_SAPI !== 'cli') { exit; }
		parse_str(implode('&', array_slice($argv, 1)), $_GET);

		if( isset($_GET['debug']) ) { $this->config['debug'] = $_GET['debug']; }
		$this->debug('CLI: time: ' . gmdate('Y-m-d H:i:s e') . ' (' . time() . ')  debug=' . $this->config['debug']);

		$this->silent = @$_GET['silent'];
		$this->action = @$_GET['a'];
		$this->wiki = @$_GET['wiki'];
		$this->namespace = @$_GET['ns'];
		$this->limit = @$_GET['limit'];
		if( !$this->limit ) { $this->limit = 1; }
		$this->user = @$_GET['user'];
		$this->search = @$_GET['search'];

		if( !isset($_GET['save']) ) { @$_GET['save'] = TRUE; }

		$this->cli_header();
		$this->cli_action();
		$this->close_db();
		exit;
	}

	function cli_action() {

		if( $this->cli_internal_action() ) { return; }

		$this->load_plugins();

		if( !array_key_exists( $this->action, $this->plugins) ) {
			print "ERROR: yamwatCLI: cli_action: unknown action\n";
			return;
		}

		$this->debug('yamwatCLI: cli_action: ' . $this->action);

		$params = $this->plugins[$this->action]->parameters();
		if( !$params ) {
			print $this->plugins[$this->action]->process('');
			return;
		}

		$this->get_wikis();
		if( !isset($this->wikis[$this->wiki]) ) {
			print "ERROR: yamwatCLI: cli_action: unknown wiki\n";
			return;
		}

		$required = $this->plugins[$this->action]->required();
		while( list(,$c) = each( $required ) ) {
			if( !isset( $this->{$c} ) || $this->{$c} == '' ) {
				print "ERROR: yamwatCLI: cli_action: Missing required input: $c=\n";
				return;
			}
		}

		$this->protocol = $this->wikis[$this->wiki]['protocol'];
		if( $this->protocol != 'https' ) { $this->protocol = 'http'; }
		$this->debug('yamwatCLI: cli_action: protocol: ' . $this->protocol);

		$this->url = $this->protocol . '://'
		. $this->wiki
		. $this->wikis[$this->wiki]['api']
		. $params;
		$this->debug('yamwatCLI: cli_action: this-url: ' . $this->url);

		$x = $this->get();
		if( !$x ) { return 'ERROR: yamwatCLI: cli_action: ' . $this->message; }

		$result = $this->plugins[$this->action]->process($x);

		if( !$this->silent) {
			print "\n" . $this->plugins[$this->action]->menu_name() . "\n";
			print_r( $result );
		}

	}

	function cli_internal_action() {
		switch( $this->action ) {
			case '': $this->cli_usage(); break;
			case 'create': $this->cli_create_table(); break;
			case 'add': $this->cli_add(); break;
			case 'delete': $this->cli_delete(); break;
			case 'edit': $this->cli_edit(); break;
			case 'wikis': $this->cli_wikis(); break;
			case 'wiki': $this->cli_wiki(); break;
			case 'history': $this->cli_history(); break;
			case 'topics': $this->cli_topics(); break;
			case 'networks': $this->cli_networks(); break;
			case 'languages': $this->cli_languages(); break;
			case 'versions': $this->cli_versions(); break;
			default: return FALSE; break;
		}
		return TRUE;
	}

	function cli_header() {
		if( $this->silent ) { return; }
		print "\n" . $this->core_name .' '. $this->core_version . ' (' . $this->core_description . ")\n";
	}

	function cli_usage() {
		print "
Usage:  php -f yamwat.php a=[action] [options]

Internal actions:
Create table  : a=create table=  ( wiki | wiki_history )
Add wiki      : a=add wiki=example.com api=/example/api.php [protocol=http] [network=] [topic=]
Delete wiki   : a=delete wiki=example.com
Edit wiki     : a=edit wiki=example.com [wiki_new=] [api=] [protocol=] [network=] [topic=]
List wikis    : a=wikis [network=] [topic=] [language=] [version=] [limit= (network|topic|language|version) ] [dir= (gte,gt,lt,lte)] [c=]
List a wiki   : a=wiki wiki=example.com
List topics   : a=topics
List networks : a=networks
List languages: a=languages
List versions : a=versions
Wiki history  : a=history wiki=example.com

Plugin actions:\n";
		$this->load_plugins();
		while( list($name,$p) = each($this->plugins) ) {
			if( !method_exists( $p, 'cli_usage') ) { continue; }
			print str_pad($p->menu_name(), 18, ' ') . ': ' . $p->cli_usage() . "\n";
		}

		print "
Global options:
Debug messages     :  debug=1
Only error messages:  silent=1

";
	}

	function cli_create_table() {
		if ( !$this->silent ) { print "\ncreate table:\n"; }
		$table = @$_GET['table'];
		if( !$table ) {
			print "ERROR: yamwatCLI: cli_create_table: missing table= \n";
			return;
		}
		if( $this->create_table($table) ) {
			if ( !$this->silent ) { print "Table created: $table\n"; }
		} else {
			print "ERROR: yamwatCLI: cli_create_table: Can not create table\n";
		}
	}

	function cli_wikis() {
		if ( !$this->silent ) { print "\nwikis:\n"; }
		$this->get_wikis();
		print_r($this->wikis);
	}

	function cli_wiki() {
		if ( !$this->silent ) { print "\nwiki:\n"; }
		if( !$this->wiki ) {
			print "ERROR: yamwatCLI: cli_wiki: missing wiki=\n";
			return;
		}
		$this->get_siteinfo();
		print_r($this->siteinfo);
	}

	function cli_history() {
		if ( !$this->silent ) { print "\nhistory:\n"; }
		if( !$this->wiki ) {
			print "ERROR: yamwatCLI: cli_history: missing wiki=\n";
			return;
		}
		$this->get_history();
		print_r($this->history);
	}

	function cli_add() {
		if( !$this->silent ) { print "\nadd:\n"; }
		$err = FALSE;
		if( !$this->wiki ) {
			print "ERROR: yamwatCLI: cli_add: missing wiki=\n";
			$err = TRUE;
		}
		if( !@$_GET['api'] ) {
			print "ERROR: yamwatCLI: cli_add: missing api=\n";
			$err = TRUE;
		}
		if( $err ) { return; }

		if( $this->add_wiki() ) {
			if( !$this->silent ) {
				print "Added wiki\n";
			}
		} else {
			print "ERROR: yamwatCLI: cli_add: can not add wiki\n";
		}
	}

	function cli_delete() {
		if ( !$this->silent ) { print "\ndelete:\n"; }
		print $this->delete_wiki();
	}

	function cli_edit() {
		if ( !$this->silent ) { print "\nedit:\n"; }
		$x = $this->edit_wiki();
		if( $x ) {
			if( !$this->silent ) { print "$x\n"; }
		} else {
			print 'ERROR: yamwatCLI: cli_edit: ' . $this->error . "\n";
		}
	}

	function cli_topics() { if ( !$this->silent ) { print "\ntopics:\n"; } $this->get_topics(); print_r($this->topics); }
	function cli_networks() { if ( !$this->silent ) { print "\nnetworks:\n"; } $this->get_networks(); print_r($this->networks); }
	function cli_languages() { if ( !$this->silent ) { print "\nlanguages:\n"; } $this->get_languages(); print_r($this->languages); }
	function cli_versions() { if ( !$this->silent ) { print "\nversions:\n"; } $this->get_versions(); print_r($this->versions); }

}
