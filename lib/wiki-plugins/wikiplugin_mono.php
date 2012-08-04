<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_mono.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_mono_info()
{
	return array(
		'name' => tra('Monospace'),
		'documentation' => 'PluginMono',
		'description' => tra('Display text in a monospace font'),
		'prefs' => array( 'wikiplugin_mono' ),
		'body' => tra('text'),
		'icon' => 'img/icons/font.png',
		'params' => array(
			'font' => array(
				'required' => false,
				'name' => tra('Font'),
				'description' => tra('Font name as known in browsers.'),
				'default' => 'monospace',
			),
		),
	);
}

function wikiplugin_mono($data, $params)
{
	global $tikilib;

	extract($params, EXTR_SKIP);

	$code = /* htmlentities( htmlspecialchars(*/ trim($data) /* ) )*/;
	$code = preg_replace("/\n/", "<br />", $code);

	if (!isset($font)) {
		$font = "monospace";
	} else {
		$font .= ", monospace";
	}

	$style = "style=\"font-family: " . $font . ";\"";
	$data = "<span " . $style . ">" . $code . "</span>";

	return $data;
}
