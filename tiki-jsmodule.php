<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-jsmodule.php 40593 2012-04-01 17:08:50Z jonnybradley $

/*
 * This is used to make module-specific parameters available to jquery 
 * so that the plugin edit popup form for PluginModule can include the parameters for the selected module in the form
 * The java script generated defines tiki_module_params["modulename"] with meta data for the parameters of the module.
 * 
 * Cached by language to allow translations 
 */
header('content-type: application/x-javascript');
header('Cache-Control: no-cache, pre-check=0, post-check=0');
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+3600*24*365*10) . ' GMT');

require_once 'tiki-filter-base.php';

$filter = TikiFilter::get('alpha');
$_REQUEST['language'] = isset($_GET['language']) ? $_GET['language'] = $filter->filter($_GET['language']) : '';

$cache = "temp/cache/module_ALL_".$_REQUEST['language'];

if ( file_exists($cache) ) {
	readfile($cache);
	exit;
}
include 'tiki-setup.php';
$modlib = TikiLib::lib('mod');
$mods = $modlib->get_all_modules();

ob_start();
?>if ( typeof tiki_module_params == 'undefined' ) { var tiki_module_params = {}; }
<?php 
foreach ( $mods as $mod ):
	include_once('modules/mod-func-' . $mod . '.php');
	$info_func = "module_{$mod}_info";
	if (function_exists($info_func)) {
		$info = $info_func();
	} else {
		$info = false;
	}
?>
tiki_module_params.<?php echo TikiLib::remove_non_word_characters_and_accents($mod) ?> = <?php echo json_encode($info) ?>;
<?php endforeach;
$content = ob_get_contents();
file_put_contents($cache, $content);
ob_end_flush();
