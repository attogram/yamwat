<?php
// Yamwat contrib - Example
// Version 0.2

$name = 'Example';

require('config.contrib.php');

if( PHP_SAPI !== 'cli') { print '<pre>'; }
print $name . ' START ' . gmdate('Y-m-d H:i:s e') .' UTC ('. time() .')' . "\n";
require($config['yamwat_home'] . 'lib/yamwat.core.php');
$yamwat = new yamwatCORE();


$yamwat->get_wikis();
print 'wikis: ' . sizeof($yamwat->wikis) . "\n";
while( list(,$x) = each($yamwat->wikis) ) {
	print 'wiki: ' . $x['wiki'] . "\n";
}


print $name . ' END ' . gmdate('Y-m-d H:i:s e') .' UTC ('. time() .')' . "\n";
if( PHP_SAPI !== 'cli') { print '</pre>'; }
