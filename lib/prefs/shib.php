<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: shib.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_shib_list()
{
	return array(
		'shib_group' => array(
			'name' => tra('Default group'),
			'type' => 'text',
			'size' => 40,
			'perspective' => false,
			'default' => 'Shibboleth',
		),
		'shib_usegroup' => array(
			'name' => tra('Create with default group'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'shib_affiliation' => array(
			'name' => tra('Valid affiliations'),
			'type' => 'text',
			'size' => 40,
			'hint' => tra('Separate multiple affiliations with commas'),
			'perspective' => false,
			'default' => '',
		),
		'shib_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'shib_create_user_tiki' => array(
			'name' => tra('Create user if not in Tiki'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
	);	
}
