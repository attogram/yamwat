<?php
// Yamwat - Yet Another MediaWiki API Tool
// Web Interface

$config = array(); // Initiate the configuration variables
$debug_log = array(); // Initiate the debug log

$debug_log[] = 'Yamwat Web Interface: ' . gmdate('Y-m-d H:i:s e') .' UTC ('. time() .')';

$file = 'config.web.php';
if( !file_exists($file) || !is_readable($file) ) {
	print '<pre>';
	print 'ERROR: yamwat Web Interface: can not include file: ' . @dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $file . "\n\n";
	print 'Did you forget to copy <b>config.web.dist.php</b> to <b>config.web.php</b> ?' . "\n";
	print '</pre>'; 
	exit;
}
$debug_log[] = 'Yamwat Web Interface: include ' . $file;
include_once($file);

if( !isset($config['yamwat_home']) ) {
	print 'ERROR: yamwat Web Interface: $config[\'yamwat_home\'] not set'; exit;
}

$file = $config['yamwat_home'] . 'lib/yamwat.web.php';
if( !file_exists($file) || !is_readable($file) ) {
	print 'ERROR: yamwat Web Interface: can not include file: ' . $file; exit;
}
$debug_log[] = 'Yamwat Web Interface: include ' . $file;
include_once($file);

$yamwat = new yamwatWEB();
