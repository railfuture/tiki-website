<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-poll_form.php 39467 2012-01-12 19:47:28Z changi67 $

$section = 'poll';
require_once ('tiki-setup.php');
require_once ('lib/tikilib.php'); // httpScheme()
include_once ('lib/polls/polllib.php');
if (!isset($polllib)) {
	$polllib = new PollLib;
}
$access->check_feature('feature_polls');
$access->check_permission('tiki_p_vote_poll');
if (!isset($_REQUEST["pollId"])) {
	$smarty->assign('msg', tra("No poll indicated"));
	$smarty->display("error.tpl");
	die;
}
$poll_info = $polllib->get_poll($_REQUEST["pollId"]);
$options = $polllib->list_poll_options($_REQUEST["pollId"]);
$smarty->assign_by_ref('menu_info', $poll_info);
$smarty->assign_by_ref('channels', $options);
$smarty->assign('ownurl', $tikilib->httpPrefix() . $_SERVER["REQUEST_URI"]);
ask_ticket('poll-form');
// Display the template
$smarty->assign('title', $poll_info['title']);
$smarty->assign('mid', 'tiki-poll_form.tpl');
$smarty->display("tiki.tpl");
