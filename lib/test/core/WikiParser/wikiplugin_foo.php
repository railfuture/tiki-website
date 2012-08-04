<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_foo.php 39469 2012-01-12 21:13:48Z changi67 $

/* Tiki-Wiki plugin example 
 */
function wikiplugin_foo_info()
{
	return array(
		'name' => tra('Foo'),
		'description' => tra('Sample plugin.'),
		'prefs' => array('wikiplugin_example'),
		'body' => tra('text'),
		'params' => array(
			'face' => array(
				'required' => true,
				'name' => tra('Face'),
				'description' => tra('Font family to use.'),
			),
			'size' => array(
				'required' => true,
				'name' => tra('Size'),
				'description' => tra('As defined by CSS.'),
			),
		),
	);
}

function wikiplugin_foo($data, $params)
{
	extract($params, EXTR_SKIP);

	$ret = "foo face=$face size=$size :: $data";
	return $ret;
}
