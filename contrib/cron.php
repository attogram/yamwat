<?php
// Yamwat contribs - Generate Yamwat Cron
// Version 0.2

require('config.contrib.php');

$yamwat_home = '/var/www/yamwat/';
$start_hour = 19;
$start_min = 0;
$increment = 1;
$cron_user = 'root';

/* ******************************************************* */
require_once($config['yamwat_home'] . 'lib/yamwat.core.php');
$yamwat = new yamwatCORE();
$yamwat->debug = FALSE;
try {
	if( !$yamwat->open_db() ) { print "\nERROR: can not open database"; exit; }
	$result = $yamwat->db->query('SELECT wiki, api, network, protocol FROM wiki ORDER BY aindex DESC, articles DESC');
	if( !$result ) { print "\nERROR: Can not query database"; exit; }
	$sites = array();
	while( $row = $result->fetch(PDO::FETCH_ASSOC) ) { $sites[] = $row; }
} catch(PDOException $e) {
	print "\nPDOException: " . $e->getCode() . ': ' . $e->getMessage(); 
	exit;
}
reset($sites);
if( PHP_SAPI !== 'cli') { print '<pre>'; }
while( list(,$x) = each($sites) ) {
	print 
	"$start_min $start_hour * * * "
	. ($cron_user ? "$cron_user " : '' )
	. 'php -f ' . $yamwat_home . 'yamwat.php silent=1 a=siteinfo wiki=' . $x['wiki'] . "\n";
	$start_min += $increment;
	if( $start_min == 60 ) { $start_min = 0; $start_hour++; }
	if( $start_hour == 24 ) { $start_hour = 0; }
}
