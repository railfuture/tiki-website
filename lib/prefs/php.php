<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: php.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_php_list()
{
	return array(
		'php_libxml' => array(
			'name' => tra('PHP libxml extension'),
			'description' => tra(
							'This extension requires the libxml PHP extension.
							This means that passing in --enable-libxml is also required, although this is
							implicitly accomplished because libxml is enabled by default.'
			),
			'type' => 'flag',
			'default' => class_exists('DOMDocument') ? 'y' :'n',
		),
		'php_datetime' => array(
			'name' => tra('PHP DateTime'),
			'description' => tra(
							'DateTime class (and related functions) are enabled
							by default since PHP 5.2.0, it is possible to add experimental support into PHP
							5.1.x by using the following flag before configure/compile:
							CFLAGS=-DEXPERIMENTAL_DATE_SUPPORT=1'
			),
			'type' => 'flag',
			'default' => class_exists('DateTime') ? 'y' :'n',
		),
	);
}
