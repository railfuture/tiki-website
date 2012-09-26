<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: http.php 42515 2012-08-02 13:00:58Z jonnybradley $

function prefs_http_list()
{
	return array(
		'http_port' => array(
			'name' => tra('HTTP port'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'default' => '',
			'shorthint' => tra('If left empty, port 80 will be used'),
		),
		'http_skip_frameset' => array(
			'name' => tra('HTTP Lookup Skip Framesets'),
			'description' => tra('When performing and HTTP request to an external source, verify if the result is a frameset and use heuristic to provide the real content.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'http_referer_registration_check' => array(
			'name' => tra('Registration referrer check'),
			'description' => tra('Use the HTTP referrer to check registration POST is sent from same host. (May not work on some setups.)'),
			'type' => 'flag',
			'default' => 'y',
		),
	);
}
