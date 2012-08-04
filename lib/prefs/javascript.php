<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: javascript.php 40815 2012-04-07 17:42:42Z jonnybradley $

function prefs_javascript_list()
{
	return array(
		'javascript_cdn' => array(
			'name' => tra('Use CDN for JavaScript'),
			'description' => tra('Obtain jQuery and jQuery UI libraries through a content delivery network.'),
			'type' => 'list',
			'options' => array(
				'none' => tra('None'),
				'google' => tra('Google (supports SSL via HTTPS)'),
				'jquery' => tra('jQuery'),
			),
			'default' => 'none',
			'tags' => array('basic'),
		),
	);
}
