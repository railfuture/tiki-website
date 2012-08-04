<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: highlight.php 39469 2012-01-12 21:13:48Z changi67 $


function prefs_highlight_list($partial = false)
{
	return array(
		'highlight_group' => array(
			'name' => tra('Highlight group'),
			'help' => 'Groups',
			'type' => 'list',
			'options' => highlight_group_values($partial),
			'default' => '0',
		),
	);
}

/**
 * highlight_group_values
 * 
 * @access public
 * @return array: group list with '0' => None as added value
 */
function highlight_group_values($partial)
{
	global $userlib;

	if ($partial) {
		return false;
	}

	$listgroups = $userlib->get_groups(0, -1, 'groupName_desc', '', '', 'n');

	$dropdown_listgroups = array();
	$dropdown_listgroups['0'] = tra('None');

	foreach ($listgroups['data'] as $onegroup) {
		$dropdown_listgroups[$onegroup['groupName']] =substr($onegroup['groupName'], 0, 50);
	}

	return $dropdown_listgroups;
}
