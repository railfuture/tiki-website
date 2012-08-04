<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: short.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_short_list()
{
	return array(
		'short_date_format' => array(
			'name' => tra('Short date format'),
			'type' => 'text',
			'size' => '30',
			'default' => '%Y-%m-%d',
			//get_strings tra("%Y-%m-%d");
			'tags' => array('basic'),
		),
		'short_time_format' => array(
			'name' => tra('Short time format'),
			'type' => 'text',
			'size' => '30',
			'default' => '%H:%M',
			//get_strings tra("%H:%M");
			'tags' => array('basic'),
		),
		//get_strings tra("%Y-%m-%d %H:%M");
	);
}
