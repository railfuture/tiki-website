<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: cookie.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_cookie_list()
{
	return array(
		'cookie_name' => array(
			'name' => tra('Cookie name'),
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
			'default' => 'tikiwiki',
		),
		'cookie_domain' => array(
			'name' => tra('Domain'),
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
			'default' => '',
		),
		'cookie_path' => array(
			'name' => tra('Path'),
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
			'default' => $GLOBALS['tikiroot'],
		),
	);
}
	
