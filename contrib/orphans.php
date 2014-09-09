<?php
// Find orphans in wiki_history table
// Version 0.2

$name = 'Find orphans in wiki_history table';

require('config.contrib.php');

if( PHP_SAPI !== 'cli') { print '<pre>'; }
print $name . ' START ' . gmdate('Y-m-d H:i:s e') .' ('. time() .')' . "\n\n";
require($config['yamwat_home'] . 'lib/yamwat.core.php');
$yamwat = new yamwatCORE();

try {
	if( !$yamwat->open_db() ) { print "\nERROR: can not open database"; exit; }
	
	$result = $yamwat->db->query('
	
	SELECT wiki_history.wiki, wiki_history.datetime
	FROM wiki_history
	LEFT OUTER JOIN wiki
		ON ( wiki.wiki = wiki_history.wiki )
		WHERE wiki.wiki IS NULL
	
	');
	
	if( !$result ) { print "\nERROR: Can not query database"; exit; }
	$orp = array();
	while( $row = $result->fetch(PDO::FETCH_ASSOC) ) { 
		print $row['wiki'] . "\t" . $row['datetime'] . "\n";
		
	}
} catch(PDOException $e) {
	print "\nPDOException: " . $e->getCode() . ': ' . $e->getMessage(); 
	exit;
}
print "\n" . $name . ' END ' . gmdate('Y-m-d H:i:s e') .' ('. time() .')' . "\n";
if( PHP_SAPI !== 'cli') { print '</pre>'; }
