<?php
// Yamwat contribs - Redo activity index

$name = 'Redo activity index';

require('config.contrib.php');

if( PHP_SAPI !== 'cli') { print '<pre>'; }
print $name . ' START ' . gmdate('Y-m-d H:i:s e') .' UTC ('. time() .')' . "\n";
require($config['yamwat_home'] . 'lib/yamwat.core.php');
$yamwat = new yamwatCORE();

run();

print $name . ' END ' . gmdate('Y-m-d H:i:s e') .' UTC ('. time() .')' . "\n";
if( PHP_SAPI !== 'cli') { print '</pre>'; }


function run() {
	global $yamwat, $last, $aindex_last;
	$yamwat->get_wikis();
	print 'wikis: ' . sizeof($yamwat->wikis) . "\n";
	while( list(,$x) = each($yamwat->wikis) ) {
		print 'wiki: ' . $x['wiki'] . ' - ' . gmdate('Y-m-d H:i:s', $x['datetime']) . "\n";
		run_history($x['wiki']);
		print "  wiki:\n";
		$sql = "UPDATE wiki SET aindex = '$aindex_last' WHERE wiki ='" 
			. $x['wiki'] . "' AND datetime = '" . $last['datetime'] . "';";	
		run_sql($sql);
		//exit;	
	}

}

function run_history($wiki) {
	global $yamwat, $first, $last, $aindex_last;
	$yamwat->wiki = $wiki;
	$yamwat->get_history($reload=1);
	print "  history: " . sizeof($yamwat->history) . "\n";
	while( list(,$x) = each($yamwat->history) ) {
		$last = $x;
		print "  " . gmdate('Y-m-d H:i:s', $x['datetime']);
		if( !isset($first) ) { 
			$first = $x; 
			print ' - ' . gmdate('Y-m-d H:i:s', $x['datetime']) . " - 0\n";
			$sql = "UPDATE wiki_history SET aindex = '' WHERE wiki ='" 
				. $wiki . "' AND datetime = '" . $x['datetime'] . "';";
			run_sql($sql);	
		} else {
			print ' - ' . gmdate('Y-m-d H:i:s', $first['datetime']);
			$datetime_diff = $x['datetime'] - $first['datetime'];
			print ' - ' . $datetime_diff;
			
			$adiff = make_diff($first, $x);
			$aindex = $yamwat->get_activity_index($adiff);
			$aindex_last = $aindex;
			print " == $aindex\n";

			$sql = "UPDATE wiki_history SET aindex = '$aindex' WHERE wiki ='" 
				. $wiki . "' AND datetime = '" . $x['datetime'] . "';";	
			run_sql($sql);		
		}

		flush(); if( PHP_SAPI !== 'cli') { ob_flush(); }
	}
	$first = NULL; unset($first);
}

function run_sql($sql) {
	global $yamwat;
	print "   SQL: $sql\n";
	try {	
			if( !$yamwat->open_db() ) {
				print "\n ERROR: can not open database \n";
				return FALSE;
			}

			$res = $yamwat->db->exec($sql);

			if( $res === FALSE ) { 
				print "\n ERROR: db result is FALSE \n";
				return FALSE;
			}

			//print "   SQL OK\n";
			return TRUE;

		} catch(PDOException $e) {
			print "\n PDOException: " . $e->getCode() .': '. $e->getMessage() . "\n";
			return FALSE;
		}	
}

function make_diff($first, $last) {
	$diff = array();
	$diff['datetime'] = $last['datetime'] - $first['datetime'];
	$diff['pages'] = $last['pages'] - $first['pages'];
	$diff['articles'] = $last['articles'] - $first['articles'];
	$diff['edits'] = $last['edits'] - $first['edits'];
	$diff['images'] = $last['images'] - $first['images'];
	$diff['users'] = $last['users'] - $first['users'];
	$diff['activeusers'] = $last['activeusers'] - $first['activeusers'];
	$diff['admins'] = $last['admins'] - $first['admins'];
	//print_r($diff);
	return $diff;
}