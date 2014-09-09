<?php
// Yamwat - Yet Another MediaWiki API Tool
// Plugin: recentchanges

class recentchangesPlugin {

	var $yamwat;

	function __construct( &$yamwat ) { $this->yamwat = $yamwat; }

	function name() { return 'recentchanges'; }

	function menu_name() { return 'Get Recent changes'; }

	function cli_usage() { return 'a=recentchanges wiki=example.com [user=] [ns=] [limit=1]'; }

	function required() { return array('wiki'); }

	function optional() { return array('user','namespace','limit'); }

	function extra_fields() { return ' User: <input type="text" name="user" size="20" />'; }

	function parameters() {
		if( !$this->yamwat->limit ) { $this->yamwat->limit = 1; }
		$r = '?action=query&format=json'
		. '&list=recentchanges'
		. '&rcprop=timestamp|title|user|userid|comment|parsedcomment|ids|sizes|redirect|loginfo|tags|flags'
		. '&rcnamespace=' . $this->yamwat->namespace
		. '&rclimit=' . $this->yamwat->limit;
		if( $this->yamwat->user ) { $r .= '&rcuser=' . urlencode($this->yamwat->user); }
		return $r;
	}

	function process($x='') { 
		if( !@is_array($x->query->recentchanges) ) {
			$err = 'ERROR: recentchanges: not array';
			$this->yamwat->debug( $err );
			return $err;
		}
		if( sizeof($x->query->recentchanges) == 0 ) {
			return 'ERROR: recentchanges: No results';
		}
		$r = array();
		while( list(,$change)  = each($x->query->recentchanges) ) {
			$r[] = $change;
		}
		return $r;
	}

}
