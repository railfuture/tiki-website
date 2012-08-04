<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: footer.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_footer_list()
{
	return array(
		'footer_shadow_start' => array(
			'name' => tra('Footer shadow start'),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
		'footer_shadow_end' => array(
			'name' => tra('Footer shadow end'),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
	);	
}
