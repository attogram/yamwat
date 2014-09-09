<?php
// Yamwat - Yet Another MediaWiki API Tool
// Plugin: search

class searchPlugin {
	var $yamwat;
	function __construct( &$yamwat ) { $this->yamwat = $yamwat; }
	function name() { return 'search'; }
	function menu_name() { return 'Get Search results'; }
	function cli_usage() { return 'a=search wiki=example.com search=STRING [ns=] [limit=1]'; }
	function required() { return array('wiki','search'); }
	function optional() { return array('namespace','limit'); }
	function extra_fields() { return ' <input type="text" name="search" size="20" />'; }
	function parameters() {
		if( !$this->yamwat->limit ) { $this->yamwat->limit = 1; }
		return '?action=opensearch&format=json'
			. '&search=' . urlencode($this->yamwat->search)
			. '&namespace=' . $this->yamwat->namespace
			. '&limit=' . $this->yamwat->limit;
	}
	function process($x='') {
		if( is_object($x) ) {
			return 'ERROR: search: process is not object';
		}
		if( !is_array($x[1]) || sizeof($x[1]) == 0 ) {
			return 'ERROR: search: No results';
		}
		//$this->yamwat->get_wikis();
		$r = array();
		while( list(,$title) = each($x[1]) ) {
			$r[] = $title;
		}
		return $r;
	}
}
