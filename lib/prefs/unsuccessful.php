<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: unsuccessful.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_unsuccessful_list()
{
	return array(
		'unsuccessful_logins' => array(
			'name' => tra('Re-validate user by email after'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'int',
			'shorthint' => tra('unsuccessful login attempts'),
			'hint' => tra('Use "-1" for never'),
			'description' => tra('After a certain number of consecutive unsuccessfull login attempts, the user will receive a mail with instruction to validate his account. However the user can still log-in with his old password.'),
			'default' => 20,
		),
		'unsuccessful_logins_invalid' => array(
			'name' => tra('Suspend account after'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'int',
			'description' => tra('After a certain number of consecutive unsuccessfull login attempts, the account is suspended . An admin must revalidate the account before the user can use it again.'),
			'shorthint' => tra('unsuccessful login attempts'),
			'hint' => tra('Use "-1" for never'),
			'default' => -1,
		),
	);	
}
