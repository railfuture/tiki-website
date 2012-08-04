<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-plugins.php 39467 2012-01-12 19:47:28Z changi67 $

require_once 'tiki-setup.php';
$access->check_feature('wiki_validate_plugin');
$access->check_permission('tiki_p_plugin_approve');
$parserlib = TikiLib::lib('parser');

if (isset($_POST['submit_mult']) && ($_POST['submit_mult'] == 'clear') && is_array($_POST['clear'])) {
	foreach ($_POST['clear'] as $fp) {
		$parserlib->plugin_clear_fingerprint($fp);
	}
}

if (isset($_POST['submit_mult']) && ($_POST['submit_mult'] == 'approve') && is_array($_POST['clear'])) {
	foreach ($_POST['clear'] as $fp) {
		$parserlib->approve_selected_pending_plugings($fp);
	}
}

if (isset($_REQUEST['approveone'])) {
	$parserlib->approve_selected_pending_plugings($_REQUEST['approveone']);
}

if (isset($_REQUEST['clearone'])) {
	$parserlib->plugin_clear_fingerprint($_REQUEST['clearone']);
}

if (isset($_REQUEST['refresh'])) {
	$pages = $tikilib->list_pages();
	foreach ($pages['data'] as $page) {
		$parserlib->parse_first($page['data'], $pre, $no);
	}
}

if (isset($_POST['approveall'])) {
	$parserlib->approve_all_pending_plugins();
}

$smarty->assign('plugin_list', $parserlib->list_plugins_pending_approval());
$smarty->assign('mid', 'tiki-plugins.tpl');
$smarty->display("tiki.tpl");
