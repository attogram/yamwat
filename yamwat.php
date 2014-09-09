<?php
// Yamwat - Yet Another MediaWiki API Tool
// Command Line Interface

$config = array(); // Initiate the configuration variables
$debug_log = array(); // Initiate the debug log

$debug_log[] = 'Yamwat CLI Interface: ' . gmdate('Y-m-d H:i:s e') .' UTC ('. time() .')';

$yamwat_dir = @dirname(__FILE__);
if( !@chdir( $yamwat_dir ) ) {
	print 'ERROR: yamwat CLI Interface: can not change to directory: ' . $yamwat_dir; exit;
}

$file = 'config.cli.php';
if( !file_exists($file) || !is_readable($file) ) {
	print 'ERROR: yamwat CLI Interface: can not include file: ' . $yamwat_dir . '/' . $file . "\n\n";
	print 'Did you forget to copy config.cli.dist.php to config.cli.php ?' . "\n";
	exit;
}
$debug_log[] = 'Yamwat CLI Interface: include ' . $file;
include_once($file);
	
if( !isset($config['yamwat_home']) ) {
	print 'ERROR: yamwat CLI Interface: $config[\'yamwat_home\'] not set'; exit;
}

$file = $config['yamwat_home'] . 'lib/yamwat.cli.php';
if( !file_exists($file) || !is_readable($file) ) {
	print 'ERROR: yamwat CLI Interface: can not include file: ' . $file; exit;
}
$debug_log[] = 'Yamwat CLI Interface: include ' . $file;
include_once($file);

$yamwat = new yamwatCLI();
