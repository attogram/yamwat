<?php
// get count of history entries for each wiki
// Version 0.1

$name = 'history count';

require('config.contrib.php');

if( PHP_SAPI !== 'cli') { print '<pre>'; }
print $name . ' START ' . gmdate('Y-m-d H:i:s e') .' ('. time() .')' . "\n\n";
require($config['yamwat_home'] . 'lib/yamwat.core.php');
$yamwat = new yamwatCORE();

try {
	if( !$yamwat->open_db() ) { print "\nERROR: can not open database"; exit; }
	$result = $yamwat->db->query('SELECT distinct(wiki) FROM wiki_history ORDER BY wiki');
	if( !$result ) { print "\nERROR: Can not query database"; exit; }
	$wikis = array();
	while( $row = $result->fetch(PDO::FETCH_ASSOC) ) { $wikis[] = $row['wiki']; }
} catch(PDOException $e) {
	print "\nPDOException: " . $e->getCode() . ': ' . $e->getMessage(); 
	exit;
}

print 'wikis: ' . sizeof($wikis) . "\n\n";

print "wiki:                             #    entries\n";
print "------------------------------ ----    -------\n";

while( $x = each($wikis) ) {

	print str_pad($x['value'] . ' ',30,'_');


	try {
		$result = $yamwat->db->query(
			"SELECT distinct(datetime) FROM wiki_history WHERE wiki = " 
				. $yamwat->db->quote($x['value'])
		);
		if( !$result ) { print "\nERROR: Can not query database"; exit; }
		$history = array();
		while( $row = $result->fetch(PDO::FETCH_ASSOC) ) { $history[] = $row['datetime']; }
	} catch(PDOException $e) {
		print "\nPDOException: " . $e->getCode() . ': ' . $e->getMessage(); 
		exit;
	}
	print " " . str_pad(sizeof($history) . '    ', 8,' ',STR_PAD_LEFT) 
	. implode($history, ', ') . "\n";
	//print_r($history);
}

print "\n" . $name . ' END ' . gmdate('Y-m-d H:i:s e') .' ('. time() .')' . "\n";
if( PHP_SAPI !== 'cli') { print '</pre>'; }
