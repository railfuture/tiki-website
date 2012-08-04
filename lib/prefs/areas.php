<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: areas.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_areas_list()
{
	return array(
		'areas_root' => array(
			'name' => tra('Areas root category id'),
			'description' => tra('Id of category whose children are bound to a perspective by areas.'),
			'type' => 'text',
			'size' => '10',
			'filter' => 'digits',
			'default' => 0,
			'help' => 'Areas',
			'dependencies' => array(
				'feature_areas',
			),
		),
	);
}

