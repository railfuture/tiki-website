<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mobile.php 40447 2012-03-28 11:06:41Z jonnybradley $

function prefs_mobile_list()
{
	global $jquerymobile_version;

	$mobile_themes = array(
		'' => tra('Default'),
		'a' => 'A',
		'b' => 'B',
		'c' => 'C',
		'd' => 'D',
		'e' => 'E',
	);

	return array(

		'mobile_feature' => array(
			'name' => tra('Mobile Access'),
			'description' => tra('New mobile feature for Tiki 7'),
			'help' => 'Mobile',
			'warning' => tra('Experimental. This feature is under development.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_perspective',
			),
			'default' => 'n',
		),
		'mobile_perspectives' => array(
			'name' => tra('Mobile Perspectives'),
			'description' => tra('New mobile feature for Tiki 7'),
			'help' => 'Mobile',
			'type' => 'text',
			'separator' => ',',
			'filter' => 'int',
			'dependencies' => array(
				'mobile_feature',
			),
			'default' => array(''),
		),
		'mobile_theme_header' => array(
			'name' => tra('Header Theme'),
			'hint' => tra('jQuery Mobile Theme'),
			'help' => "http://jquerymobile.com/demos/$jquerymobile_version/#docs/api/themes.html",
			'type' => 'list',
			'options' => $mobile_themes,
			'dependencies' => array(
				'mobile_feature',
			),
			'default' => '',
		),
		'mobile_theme_content' => array(
			'name' => tra('Content Theme'),
			'hint' => tra('jQuery Mobile Theme'),
			'help' => "http://jquerymobile.com/demos/$jquerymobile_version/#docs/api/themes.html",
			'type' => 'list',
			'options' => $mobile_themes,
			'dependencies' => array(
				'mobile_feature',
			),
			'default' => '',
		),
		'mobile_theme_footer' => array(
			'name' => tra('Footer Theme'),
			'hint' => tra('jQuery Mobile Theme'),
			'help' => "http://jquerymobile.com/demos/$jquerymobile_version/#docs/api/themes.html",
			'type' => 'list',
			'options' => $mobile_themes,
			'dependencies' => array(
				'mobile_feature',
			),
			'default' => '',
		),
		'mobile_theme_modules' => array(
			'name' => tra('Modules Theme'),
			'hin' => tra('jQuery Mobile Theme'),
			'help' => "http://jquerymobile.com/demos/$jquerymobile_version/#docs/api/themes.html",
			'type' => 'list',
			'options' => $mobile_themes,
			'dependencies' => array(
				'mobile_feature',
			),
			'default' => '',
		),
		'mobile_theme_menus' => array(
			'name' => tra('Menus Theme'),
			'description' => tra('jQuery Mobile Theme'),
			'help' => "http://jquerymobile.com/demos/$jquerymobile_version/#docs/api/themes.html",
			'type' => 'list',
			'options' => $mobile_themes,
			'dependencies' => array(
				'mobile_feature',
			),
			'default' => '',
		),
		'mobile_use_latest_lib' => array(
			'name' => tra('Use Latest Library'),
			'description' => tra('Use latest version of the jquery.mobile'),
			'help' => 'Mobile',
			'warning' => tra('Uses latest build from jquery.com CDN.'),
			'type' => 'flag',
			'dependencies' => array(
				'mobile_feature',
			),
			'default' => 'n',
		),
	);
}
