<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_groupexpiry.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_groupexpiry_info()
{
	return array(
		'name' => tra('Group Expiry'),
		'documentation' => 'PluginGroupExpiry',
		'description' => tra('Show the expiry date of a group membership of the current user'),
		'prefs' => array( 'wikiplugin_groupexpiry' ),
		'icon' => 'img/icons/group_delete.png',
		'params' => array(
			'group' => array(
				'required' => true,
				'name' => tra('Group Name'),
			),
		),
	);
}

function wikiplugin_groupexpiry($data, $params)
{
	global $tikilib, $userlib, $user;
	extract($params, EXTR_SKIP);	
	$groups = $userlib->get_user_groups($user);
	if (!in_array($group, $groups)) {
		return tra("not in group");
	}
	$userId = $tikilib->get_user_id($user);
	$date = $tikilib->getOne('SELECT `expire` FROM `users_usergroups` where `userId` = ? AND `groupName` = ?', array($userId, $group));
	if (!$date) {
		return tra("never expires");
	} else {
		return '~np~' . $tikilib->get_long_datetime($date) . '~/np~';
	}	
}
