<?php
// Update DB to V2

$name = 'Update DB to V2';

require('config.contrib.php');

if( PHP_SAPI !== 'cli') { print '<xpre>'; }
print $name . ' START ' . gmdate('Y-m-d H:i:s e') .' UTC ('. time() .')' . "\n";
require($config['yamwat_home'] . 'lib/yamwat.core.php');
$yamwat = new yamwatCORE();
run_update();
print $name . ' END ' . gmdate('Y-m-d H:i:s e') .' UTC ('. time() .')' . "\n";
if( PHP_SAPI !== 'cli') { print '</xpre>'; }


function run_update() {
	global $yamwat;

			if( !$yamwat->open_db() ) {
				print "\n ERROR: can not open database \n";
				return FALSE;
			}
			
	
	print "RUN UPDATE\n";

	$sql = 'DROP TABLE IF EXISTS xwiki'; 	run_sql($sql);
	$sql = 'DROP TABLE IF EXISTS xwiki_history'; 	run_sql($sql);		
	
	$sql = "CREATE TABLE  'xwiki' ( 
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
'time' TEXT NOT NULL DEFAULT '', PRIMARY KEY ( wiki ) );

"; run_sql($sql);		

$sql = " CREATE TABLE 'xwiki_history' ( 
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
'time' TEXT NOT NULL DEFAULT '', PRIMARY KEY ( wiki, datetime ) );

"; run_sql($sql);
	
	$sql = 'SELECT * FROM wiki'; 	$wiki = run_sql($sql);
	while( list(,$x) = each($wiki) ) {
		$x['aindex'] = '';
		$keys = array_keys($x);
		$trsh = sort($keys);
		$trsh = ksort($x);
		$k = array(); while( $y = each($keys) ) { $k[] = $yamwat->db->quote($y['value']); }
		$vals = array(); while( $z = each($x) ) { $vals[] = $yamwat->db->quote($z['value']); }
		$sql = 'INSERT INTO xwiki (' . implode(",", $k) . ") VALUES \n(" . implode(',', $vals) . ')';
		run_sql($sql);
	}

	$sql = 'SELECT * FROM wiki_history'; 	$wiki = run_sql($sql);
	reset($wiki);
	while( list(,$x) = each($wiki) ) {
		$x['aindex'] = '';
		$keys = array_keys($x);
		$trsh = sort($keys);
		$trsh = ksort($x);
		$k = array(); while( $y = each($keys) ) { $k[] = $yamwat->db->quote($y['value']); }
		$vals = array(); while( $z = each($x) ) { $vals[] = $yamwat->db->quote($z['value']); }
		$sql = 'INSERT INTO xwiki_history (' . implode(",", $k) . ") VALUES \n(" . implode(',', $vals) . ')';
		run_sql($sql);
	}

	$sql = "DROP TABLE wiki;"; run_sql($sql);		
	$sql = "DROP TABLE wiki_history;"; run_sql($sql);	
	$sql = "ALTER TABLE xwiki RENAME TO wiki;"; run_sql($sql);	
	$sql = "ALTER TABLE xwiki_history RENAME TO wiki_history;"; run_sql($sql);	

	
	
}


function run_sql($sql) {
	global $yamwat;
	set_time_limit(999);	
	//print "   SQL: $sql\n";
	try {	

			$statement = $yamwat->db->prepare($sql);
			$res = $statement->execute();
			$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
			print "OK "; flush();
			return $rows;

		} catch(PDOException $e) {
			print "\n PDOException: " . $e->getCode() .': '. $e->getMessage() . "\n";
			return FALSE;
		}	
}