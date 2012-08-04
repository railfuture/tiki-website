<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-users_own_rank.php 40393 2012-03-26 04:12:18Z lindonb $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

function module_users_own_rank_info()
{
	return array(
		'name' => tra('My Score'),
		'description' => tra('Display the logged user\'s rank and score.'),
		'prefs' => array( 'feature_score' ),
		'params' => array()
	);
}

function module_users_own_rank( $mod_reference, $module_params )
{
	global $scorelib, $smarty, $user;
	include_once('lib/score/scorelib.php');

	$position = $scorelib->user_position($user);
	$smarty->assign('position', $position);
	$score = $scorelib->get_user_score($user);
	$smarty->assign('score', $score);
	$count = $scorelib->count_users(0);
	$smarty->assign('count', $count);
	$smarty->assign('user', $user);
}
