<?php
// Yamwat - Yet Another MediaWiki API Tool
// Plugin: random

class randomPlugin {
	var $yamwat;
	function __construct( &$yamwat ) { $this->yamwat = $yamwat; }
	function name() { return 'random'; }
	function menu_name() { return 'Get Random Page'; }
	function cli_usage() { return 'a=random wiki=example.com [ns=] [limit=1]'; }
	function required() { return array('wiki'); }
	function optional() { return array('namespace','limit'); }
	function extra_fields() { return; }
	function parameters() { 
		if( !$this->yamwat->limit ) { $this->yamwat->limit = 1; }
		return '?action=query&format=json&list=random'
			. '&rnnamespace=' . $this->yamwat->namespace
			. '&rnlimit=' . $this->yamwat->limit;
	}
	function process($x='') { 
		if( !isset($x->query->random) || !is_array($x->query->random) ) {
			$err = 'ERROR: invalid response';
			$this->yamwat->debug($err);
			return $err;
		}
		while( list(,$y) = each($x->query->random) ) {
			$r[] = $y;
		}
		return $r;
	}
}
