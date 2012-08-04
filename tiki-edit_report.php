<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-edit_report.php 40326 2012-03-23 19:23:16Z robertplummer $

require_once('tiki-setup.php');
global $headerlib, $smarty, $reportFullscreen, $index, $values, $access;

$access->check_feature('feature_reports');

TikiLib::lib("sheet")->setup_jquery_sheet();

$headerlib
	->add_jsfile('lib/core/Report/Builder.js')
	->add_jq_onready('$.reportInit();');
	
$smarty->assign('definitions', Report_Builder::listDefinitions());

if (!empty($reportFullscreen)) {
	$smarty->assign('index', $index);
	$smarty->assign('values', $values);
	$smarty->assign('reportFullscreen', 'true');
	$smarty->display('tiki-edit_report.tpl');
} else {
	$smarty->assign('mid', 'tiki-edit_report.tpl');
	$smarty->display("tiki.tpl");
}
