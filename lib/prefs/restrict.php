<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: restrict.php 42404 2012-07-17 23:56:18Z marclaporte $

function prefs_restrict_list()
{
	return array(
		'restrict_language' => array(
			'name' => tra('Restrict supported languages'),
			'description' => tra('Limit the languages made available on the site.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('basic'),
		),
	);
}

