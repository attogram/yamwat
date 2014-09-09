<?php
// fix frwiki

$name = '';

require('config.contrib.php');

if( PHP_SAPI !== 'cli') { print '<pre>'; }
print $name . ' START ' . gmdate('Y-m-d H:i:s e') .' ('. time() .')' . "\n\n";
require($config['yamwat_home'] . 'lib/yamwat.core.php');
$yamwat = new yamwatCORE();

try {
	if( !$yamwat->open_db() ) { print "\nERROR: can not open database"; exit; }
	
	$result = $yamwat->db->query("

VACUUM;
-- DELETE FROM wiki_history WHERE wiki IN ('wikicafe.metacafe.com/nl/');



-- DELETE FROM wiki_history WHERE wiki IN ('wiki.musialek.org/w', 'ten.wikipedia.org', 'th.wikinews.org');	
	-- DELETE FROM wiki_history WHERE wiki = 'fr.wikipedia.org'
	
	");
	
	if( !$result ) { print "\nERROR: Can not query database"; exit; }
	$orp = array();
	while( $row = $result->fetch(PDO::FETCH_ASSOC) ) { 
		print $row['wiki']; print "\t";
		print $row['datetime']; print "\t";
		print $row['aindex']; print "\t";
		print $row['edits'];
		print "\n";
	}
} catch(PDOException $e) {
	print "\nPDOException: " . $e->getCode() . ': ' . $e->getMessage(); 
	exit;
}
print "\n" . $name . ' END ' . gmdate('Y-m-d H:i:s e') .' ('. time() .')' . "\n";
if( PHP_SAPI !== 'cli') { print '</pre>'; }
