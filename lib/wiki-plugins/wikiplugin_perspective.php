<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_perspective.php 39467 2012-01-12 19:47:28Z changi67 $

function wikiplugin_perspective_info()
{
	return array(
		'name' => tra('Perspective'),
		'documentation' => 'PluginPerspective',
		'description' => tra('Display content based on the reader\'s current perspective'),
		'prefs' => array( 'feature_perspective', 'wikiplugin_perspective' ),
		'body' => tra('Wiki text to display if conditions are met. The body may contain {ELSE}. Text after the marker will be displayed to users not matching the condition.'),
		'filter' => 'wikicontent',
		'params' => array(
			'perspectives' => array(
				'required' => false,
				'name' => tra('Allowed Perspectives'),
				'description' => tra('Pipe-separated list of identifiers of perspectives in which the block is shown.') . tra('Example value:') . '2|3|5',
				'filter' => 'text',
				'default' => ''
			),
			'notperspectives' => array(
				'required' => false,
				'name' => tra('Denied Perspectives'),
				'description' => tra('Pipe-separated list of identifiers of perspectives in which the block is not shown.') . tra('Example value:') . '3|5|8',
				'filter' => 'text',
				'default' => ''
			),
		),
	);
}

function wikiplugin_perspective($data, $params)
{
	global $prefs, $perspectivelib;

	$dataelse = '';
	if (strpos($data, '{ELSE}')) {
		$dataelse = substr($data, strpos($data, '{ELSE}')+6);
		$data = substr($data, 0, strpos($data, '{ELSE}'));
	}

	if (!empty($params['perspectives'])) {
		$perspectives = explode('|', $params['perspectives']);
	}
	if (!empty($params['notperspectives'])) {
		$notperspectives = explode('|', $params['notperspectives']);
	}
	if (empty($perspectives) && empty($notperspectives)) {
		return '';
	}

	require_once 'lib/perspectivelib.php';
	$currentPerspective = $perspectivelib->get_current_perspective($prefs);

	// if the current perspective is not an allowed perspective, return the content after the "{ELSE}"
	if (!empty($perspectives) && !in_array($currentPerspective, $perspectives)) {
		return $dataelse;
	}

	// if the current perspective is a denied perspective, return the content after the "{ELSE}"
	if (!empty($notperspectives) && in_array($currentPerspective, $notperspectives)) {
		return $dataelse;
	}

	return $data;
}
