<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: userfiles.php 42578 2012-08-15 16:53:43Z jonnybradley $

function prefs_userfiles_list()
{
	return array(
		'userfiles_quota' => array(
			'name' => tra('Quota (MB)'),
			'type' => 'text',
			'size' => 5,
			'default' => 30,
		),
		'userfiles_private' => array(
			'name' => tra('Private'),
			'description' => tra("Users can see each other's files"),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
