<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: url.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_url_list()
{
	return array(
		'url_after_validation' => array(
			'name' => tra('URL a user is redirected to after account validation'),
			'hint' => tra('Default').': tiki-information.php?msg='.tra('Account validated successfully.'),
			'type' => 'text',
			'dependencies' => array(
				'allowRegister',
			),
			'default' => '',
		),
		'url_anonymous_page_not_found' => array(
			'name' => tra('URL an anonymous is redirected when page not found'),
			'type' => 'text',
			'default' => '',
		),
	);
}
