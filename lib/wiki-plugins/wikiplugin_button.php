<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_button.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_button_info()
{
	return array(
		'name' => tra('Button'),
		'documentation' => 'PluginButton',
		'description' => tra('Add a link formatted as a button'),
		'prefs' => array('wikiplugin_button'),
		'validate' => 'none',
		'extraparams' => false,
		'icon' => 'img/icons/control_play_blue.png',
		'tags' => array( 'basic' ),		
		'params' => array(
			'href' => array(
				'required' => true,
				'name' => tra('Url'),
				'description' => tra('URL to be produced by the button. You can use wiki argument variables like {{itemId}} in it'),
				'filter' => 'url',
				'default' => '',
			),
			'_class' => array(
				'required' => false,
                                'name' => tra('CSS Class'),
                                'description' => tra('CSS class for the button'),
                                'filter' => 'text',
                                'default' => '',
                        ),
			'_text' => array(
				'required' => false,
				'name' => tra('Label'),
				'description' => tra('Label for the button'),
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

function wikiplugin_button($data, $params)
{
	global $tikilib,$smarty;
	$parserlib = TikiLib::lib('parser');
	
	if (empty($params['href'])) {
		return tra('Incorrect param');
	}
	$path = 'lib/smarty_tiki/function.button.php';
	if (!file_exists($path)) {
		return tra('lib/smarty_tiki/function.button.php is missing or unreadable');
	}

	// for some unknown reason if a wikiplugin param is named _text all whitespaces from
	// its value are removed, but we need to rename the param to _text for smarty_functin  
	if (isset($params['text'])) {
		$params['_text'] = $params['text'];
		unset($params['text']);
	}
	
	// Parse wiki argument variables in the url, if any (i.e.: {{itemId}} for it's numeric value).
	$parserlib->parse_wiki_argvariable($params['href']);

	include_once($path);
	$func = 'smarty_function_button';
	$content = $func($params, $smarty);
	return '~np~'.$content.'~/np~';
}
