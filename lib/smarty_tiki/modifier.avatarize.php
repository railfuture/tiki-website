<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.avatarize.php 39469 2012-01-12 21:13:48Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     capitalize
 * Purpose:  capitalize words in the string
 * -------------------------------------------------------------
 */
function smarty_modifier_avatarize($user)
{
	global $tikilib;
	global $userlib;
	$avatar = $tikilib->get_user_avatar($user);
	if ( $avatar != '' && $tikilib->get_user_preference($user, 'user_information', 'public') == 'public' ) {
		$id = $userlib->get_user_id($user);
		include_once('tiki-sefurl.php');
		$url = "tiki-user_information.php?userId=$id";
		$url = filter_out_sefurl($url);	
		$avatar = "<a title=\"" . htmlspecialchars($user, ENT_QUOTES) . "\" href=\"$url\">".$avatar.'</a>';
	}
	return $avatar;	
}
