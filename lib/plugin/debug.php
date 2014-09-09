<?php
// Yamwat - Yet Another MediaWiki API Tool
// Plugin: debug

class debugPlugin {
	var $yamwat;
	function __construct( &$yamwat ) { $this->yamwat = $yamwat; }
	function name() { return 'debug'; }
	function menu_name() { return 'Debug'; }
	function cli_usage() { return 'a=debug'; }
	function required() { return array(); }
	function optional() { return array(); }
	function extra_fields() { return; }
	function parameters() { return FALSE; }
	function process($x='') {
		$r = ''
		. 'yamwat_home: ' . @$this->yamwat->config['yamwat_home'] . "\n"
		. 'core_name: ' . @$this->yamwat->core_name . "\n"
		. 'core_version: ' . @$this->yamwat->core_version . "\n"
		. 'core_description: ' . @$this->yamwat->core_description . "\n"
		. 'core_url: ' . @$this->yamwat->core_url . "\n"
		. 'core_email: ' . @$this->yamwat->core_email . "\n"
		. 'system_name: ' . @$this->yamwat->config['system_name'] . "\n"
		. 'system_version: ' . @$this->yamwat->config['system_version'] . "\n"
		. 'system_url: ' . @$this->yamwat->config['system_url'] . "\n"
		. 'system_email: ' . @$this->yamwat->config['system_email'] . "\n"
		. 'user_agent: ' . $this->yamwat->get_user_agent() . "\n"
		. 'config: ' . @sizeof($this->yamwat->config) . "\n"
		. 'debug: ' . @$this->yamwat->config['debug'] . "\n"
		. 'db_file: ' . @$this->yamwat->config['db_file'] . "\n"
		. 'enable_web_admin: ' . @$this->yamwat->config['enable_web_admin'] . "\n"
		. 'message: ' . @$this->yamwat->message . "\n"
		. 'action: ' . @$this->yamwat->action . "\n";

		reset( $this->yamwat->plugins );
		$r .= 'plugins: ' . sizeof(@$this->yamwat->plugins) . "\n";
		$count = 0;
		while( list($name,$p) = each($this->yamwat->plugins) ) {
			$r .= 'plugin ' . $count++ 
				. ': ' . $p->name() . ' - ' . $p->menu_name()
				. ' - req: ' . implode(', ', $p->required())
				. ' - opt: ' . implode(', ', $p->optional())
				. "\n";
		}

		$r .= 'wikis: ' . $this->yamwat->get_wikis_count() . "\n";
		return $r;
	}
}
