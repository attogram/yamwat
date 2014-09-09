<?php
// Yamwat - Yet Another MediaWiki API Tool
// Plugin: help

class helpPlugin {
	var $yamwat;
	function __construct( &$yamwat ) { $this->yamwat = $yamwat; }
	function name() { return 'help'; }
	function menu_name() { return 'Get API Help'; }
	function cli_usage() { return 'a=help wiki=example.com'; }
	function required() { return array('wiki'); }
	function optional() { return array(); }
	function extra_fields() { return; }	
	function parameters() { return '?action=help&version=1&format=json'; }
	function process($x='') { return print_r(@$x->error->{"*"},1); }
}