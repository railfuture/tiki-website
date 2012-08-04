<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_groupstat.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_groupstat_info()
{
	return array(
		'name' => tra('Group Stats'),
		'documentation' => 'PluginGroupStat',
		'description' => tra('Show the distribution of users among groups'),
		'body' => tra('Title'),
		'icon' => 'img/icons/calculator.png',
		'params' => array(
			'groups' => array(
				'required' => false,
				'name' => tra('Groups'),
				'description' => tra('Groups separated by :. If empty, all groups will be listed.'),
			),
			'percent_of' => array(
				'required' => false,
				'name' => tra('Percentage of'),
				'description' => tra('Show percentage out of all users in site, or just specified groups if any.'),
				'default' => 'groups',
				'options' => array(
					array('text' => tra('Users in specified groups, if any'), 'value' => 'groups'),
					array('text' => tra('Site users'), 'value' => 'site')
				)
			),
			'show_percent' => array(
				'required' => false,
				'name' => tra('Show Percentage'),
				'description' => tra('Show the percentage of total users that are members of each group (percentages are shown by default)'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'show_bar' => array(
				'required' => false,
				'name' => tra('Show Bar'),
				'description' => tra('Represent the percentage of total users that are members of each group in a bar graph (default is not to show the bar graph)'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
}

function wikiplugin_groupstat($data, $params)
{
	global $smarty, $prefs, $userlib, $tikilib;

	if (isset($params['groups'])) {
		$groups = explode(':', $params['groups']);
		if (isset($params['percent_of']) && $params['percent_of'] == 'site') {
			$total = $userlib->nb_users_in_group();
		} else {
			$query = 'SELECT COUNT(DISTINCT `userId`) FROM `users_usergroups` WHERE `groupName` IN('.implode(',', array_fill(0, count($groups), '?')).')';
			$total = $tikilib->getOne($query, $groups);
		}
	} else {
		$groups = $userlib->list_all_groups();
		$total = $userlib->nb_users_in_group();
	}
	$stats = array();
	foreach ($groups as $group) {
		$nb = $userlib->nb_users_in_group($group);
		$stats[] = array('group' => $group, 'nb' => $nb);
	}
	foreach ($stats as $i=>$stat) {
		$stats[$i]['percent'] = ($stat['nb'] * 100) / $total;
	}
	$smarty->assign_by_ref('params', $params);
	$smarty->assign_by_ref('stats', $stats);
	return "~np~".$smarty->fetch('wiki-plugins/wikiplugin_groupstat.tpl')."~/np~";
}
