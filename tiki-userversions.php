<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-userversions.php 39467 2012-01-12 19:47:28Z changi67 $

require_once ('tiki-setup.php');
include_once ('lib/wiki/histlib.php');

$access->check_feature('feature_wiki');
$access->check_permission('tiki_p_admin');

// We have to get the variable ruser as the user to check
if (!isset($_REQUEST["ruser"])) {
	$smarty->assign('msg', tra("No user indicated"));
	$smarty->display("error.tpl");
	die;
}
if (!user_exists($_REQUEST["ruser"])) {
	$smarty->assign('msg', tra("Non-existent user"));
	$smarty->display("error.tpl");
	die;
}
$smarty->assign_by_ref('ruser', $_REQUEST["ruser"]);
$smarty->assign('preview', false);
if (isset($_REQUEST["preview"])) {
	$version = $histlib->get_version($_REQUEST["page"], $_REQUEST["version"]);
	$version["data"] = $tikilib->parse_data($version["data"]);
	if ($version) {
		$smarty->assign_by_ref('preview', $version);
		$smarty->assign_by_ref('version', $_REQUEST["version"]);
	}
}
$history = $histlib->get_user_versions($_REQUEST["ruser"]);
$smarty->assign_by_ref('history', $history);
ask_ticket('userversion');
$smarty->assign('mid', 'tiki-userversions.tpl');
$smarty->display("tiki.tpl");
