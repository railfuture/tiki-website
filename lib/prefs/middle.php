<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: middle.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_middle_list()
{
	return array(
		'middle_shadow_start' => array(
			'name' => tra('Middle shadow start'),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
		'middle_shadow_end' => array(
			'name' => tra('Middle shadow end'),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
	);	
}
