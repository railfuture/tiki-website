<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_dialog.php 39469 2012-01-12 21:13:48Z changi67 $

function wikiplugin_dialog_info()
{
	return array(
		'name' => tra('Dialog'),
		'documentation' => 'PluginDialog',
		'validate' => 'all',
		'description' => tra('Create a jQuery-UI dialog'),
		'prefs' => array( 'wikiplugin_dialog', 'feature_jquery_ui' ),
		'body' => tra('text'),
		'params' => array(
			'title' => array(
				'required' => false,
				'name' => tra('Title'),
				'description' => tra(''),
				'filter' => 'text',
				'default' => '',
			),
			'buttons' => array(
				'required' => false,
				'name' => tra('Buttons'),
				'description' => tra('Button labels separated by commas.'),
				'filter' => 'text',
				'separator' => ',',
				'default' => tra('Ok') . ',' . tra('Cancel'),
			),
			'actions' => array(
				'required' => false,
				'name' => tra('Button actions'),
				'description' => tra('JS to perform on 1st button click.'),
				'filter' => 'rawhtml_unsafe',
				'separator' => ',',
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Dialog width'),
				'description' => tra('In pixels.'),
				'filter' => 'int',
				'default' => '300',
			),
			'id' => array(
				'required' => false,
				'name' => tra('HTML ID'),
				'description' => tra('Automatically generated if left empty in the form "wpdialog_XX" (must be unique per page)'),
				'filter' => 'striptags',
				'default' => '',
			),
			'showAnim' => array(
				'required' => false,
				'name' => tra('Show animation'),
				'description' => tra(''),
				'filter' => 'text',
				'default' => '',
			),
			'hideAnim' => array(
				'required' => false,
				'name' => tra('Hide animation'),
				'description' => tra(''),
				'filter' => 'text',
				'default' => '',
			),
			'autoOpen' => array(
				'required' => false,
				'name' => tra('Auto open'),
				'description' => tra('y/n'),
				'filter' => 'alpha',
				'default' => 'y',
			),
			'modal' => array(
				'required' => false,
				'name' => tra('Modal'),
				'description' => tra('y/n'),
				'filter' => 'alpha',
				'default' => 'n',
			),
			'wiki' => array(
				'required' => false,
				'name' => tra('Wiki page'),
				'description' => tra('Wiki page to use as dialog body.'),
				'filter' => 'pagename',
				'default' => '',
			),
			'openAction' => array(
				'required' => false,
				'name' => tra('Open Action'),
				'description' => tra('JS to execute when dialog opens.'),
				'filter' => 'rawhtml_unsafe',
				'default' => '',
			),
		),
	);
}

function wikiplugin_dialog($data, $params)
{

	static $id = 0;
	$unique = 'wpdialog_' . ++$id;

	$headerlib = TikiLib::lib('header');

	$defaults = array();
	$plugininfo = wikiplugin_dialog_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults["$key"] = $param['default'];
	}
	$params = array_merge($defaults, $params);

	if (empty($params['id'])) {
		$params['id'] = $unique;
	}

	if (!empty($params['wiki'])) {
		$ignore = '';
		$data = TikiLib::lib('wiki')->get_parse($params['wiki'], $ignore, true);
	}

	$options = array('width' => $params['width']);
	$options['autoOpen'] = ($params['autoOpen'] === 'y');
	$options['modal'] = ($params['modal'] === 'y');
	if (!empty($params['showAnim'])) {
		$options['show'] = $params['showAnim'];
	}
	if (!empty($params['hideAnim'])) {
		$options['hide'] = $params['hideAnim'];
	}

	$buttons = '';	// buttons need functions attached and json_encode cannot deal with them ;(

	$nbuts = count($params['buttons']);
	if ($nbuts > 0) {
		$buttons = '{';
		for ($i = 0; $i < $nbuts; $i++) {
			if (!isset($params['actions'][$i])) {
				$params['actions'][$i] = '$(this).dialog("close");';
			}
			if (strpos($params['actions'][$i], '$(this).dialog("close");') === false) {
				$params['actions'][$i] .= ';$(this).dialog("close");';
			}
			$buttons .= strlen($buttons) > 1 ? ',' : '';
			$buttons .= json_encode($params['buttons'][$i]);
			$buttons .= ': function(){' . $params['actions'][$i] . '}';
		}
		$buttons .= '}';
		$options['buttons'] = 'buttonsdummy';
	}
	$opens = '';
	if (!empty($params['openAction'])) {
		$opens = 'function(){' . $params['openAction'] . '}';
		$options['open'] = 'opendummy';
	}
	$optString = json_encode($options);
	if (!empty($buttons)) {
		$optString = str_replace('"buttonsdummy"', $buttons, $optString);
	}
	if (!empty($opens)) {
		$optString = str_replace('"opendummy"', $opens, $optString);
	}

	$js = '$("#'.$params['id'].'").dialog(' . $optString . ');';
	$headerlib->add_js('$(function() {' . $js . '});');

	if (empty($params['title'])) {
		$titlestr = '';
	} else {
		$titlestr = ' title="' . $params['title'] . '"';
	}
	$html = '<div id="'.$params['id'].'"'.$titlestr.' style="display:none">'.$data.'</div>';

	return $html;
}
