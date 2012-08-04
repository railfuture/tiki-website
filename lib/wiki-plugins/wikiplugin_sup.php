<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_sup.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_sup_info()
{
	return array(
		'name' => tra('Superscript'),
		'documentation' => 'PluginSup',
		'description' => tra('Apply superscript font to text'),
		'prefs' => array( 'wikiplugin_sup' ),
		'body' => tra('text'),
		'icon' => 'img/icons/text_superscript.png',
		'filter' => 'wikicontent',
		'tags' => array( 'basic' ),
		'params' => array(
		),
	);
}

function wikiplugin_sup($data, $params)
{
	global $tikilib;

	extract($params, EXTR_SKIP);
	return "<sup>$data</sup>";
}
