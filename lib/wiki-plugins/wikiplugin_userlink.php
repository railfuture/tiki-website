<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_userlink.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_userlink_info()
{
	return array(
		'name' => tra('User Link'),
		'documentation' => 'PluginUserlink',
		'description' => tra('Display a link to a user\'s information page'),
		'prefs' => array('wikiplugin_userlink'),
		'icon' => 'img/icons/user_go.png',
		'params' => array( 
			'user' => array(
				'required' => false,
				'name' => tra('User Name'),
				'description' => tra('User account name (which can be an email address)'),
				'filter' => 'xss',
				'default' => ''
			),
		),
	);
}

function wikiplugin_userlink($data, $params)
{
	global $smarty;
	$path = 'lib/smarty_tiki/modifier.userlink.php';
	include_once($path);
	$func = 'smarty_modifier_userlink';
	$content = $func($params['user'], '', '', $data);
	return '~np~'.$content.'~/np~';
}
