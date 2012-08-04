<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_group.php 41461 2012-05-14 22:30:18Z sylvieg $

function wikiplugin_group_info()
{
	return array(
		'name' => tra('Group'),
		'documentation' => 'PluginGroup',
		'description' => tra('Display content based on the user\'s groups or friends'),
		'body' => tra('Wiki text to display if conditions are met. The body may contain {ELSE}. Text after the marker will be displayed to users not matching the conditions.'),
		'prefs' => array('wikiplugin_group'),
		'icon' => 'img/icons/group.png',
		'filter' => 'wikicontent',
		'tags' => array( 'basic' ),		
		'params' => array(
			'friends' => array(
				'required' => false,
				'name' => tra('Allowed User Friends'),
				'description' => tra('Pipe separated list of users whose friends are allowed to view the block. ex: admin|johndoe|foo'),
				'filter' => 'username',
				'default' => ''
			),
			'groups' => array(
				'required' => false,
				'name' => tra('Allowed Groups'),
				'description' => tra('Pipe separated list of groups allowed to view the block. ex: Admins|Developers'),
				'filter' => 'groupname',
				'default' => ''
			),
			'notgroups' => array(
				'required' => false,
				'name' => tra('Denied Groups'),
				'description' => tra('Pipe separated list of groups denied from viewing the block. ex: Anonymous|Managers'),
				'filter' => 'groupname',
				'default' => ''
			),
		),
	);
}

function wikiplugin_group($data, $params)
{
	global $user, $prefs, $tikilib, $smarty;
	$dataelse = '';
	if (strrpos($data, '{ELSE}')) {
		$dataelse = substr($data, strrpos($data, '{ELSE}')+6);
		$data = substr($data, 0, strrpos($data, '{ELSE}'));
	}

	if (!empty($params['friends']) && $prefs['feature_friends'] == 'y') {
		$friends = explode('|', $params['friends']);
	}
	if (!empty($params['groups'])) {
		$groups = explode('|', $params['groups']);
	}
	if (!empty($params['notgroups'])) {
		$notgroups = explode('|', $params['notgroups']);
	}
	if (empty($friends) && empty($groups) && empty($notgroups)) {
		return '';
	}

	$userGroups = $tikilib->get_user_groups($user);

	if (count($userGroups) > 1) { //take away the anonymous as everybody who is registered is anonymous
		foreach ($userGroups as $key=>$grp) {
			if ($grp == 'Anonymous') {
				$userGroups[$key] = '';
				break;
			}
		}
	}

	if (!empty($friends)) {
		$ok = false;

		foreach ($friends as $key=>$friend) {
		    if ($tikilib->verify_friendship($user, $friend)) {
			    $ok = true;
			    break;
		    }
		}
		if (!$ok)
			return $dataelse;
	}
	if (!empty($groups)) {
		$ok = false;

		foreach ($userGroups as $grp) {
			if (in_array($grp, $groups)) {
				$ok = true;
				$smarty->assign('groupValid', 'y');
				break;
			}
			$smarty->assign('groupValid', 'n');
		}
		if (!$ok)
			return $dataelse;
	}
	if (!empty($notgroups)) {
		$ok = true;
		foreach ($userGroups as $grp) {
			if (in_array($grp, $notgroups)) {
				$ok = false;
				$smarty->assign('notgroupValid', 'y');
				break;
			}
			$smarty->assign('notgroupValid', 'n');
		}
		if (!$ok)
			return $dataelse;
	}
		
	
	return $data;
}
