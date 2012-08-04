<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_jq.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_jq_info()
{
	return array(
		'name' => tra('jQuery'),
		'documentation' => 'PluginJQ',
		'description' => tra('Add JavaScript code'),
		'prefs' => array( 'wikiplugin_jq' ),
		'body' => tra('JavaScript code'),
		'validate' => 'all',
		'filter' => 'none',
		'icon' => 'img/icons/script_code_red.png',
		'params' => array(
			'notonready' => array(
				'required' => false,
				'name' => tra('Not On Ready'),
				'description' => tra('Do not execute on document ready (execute inline)'),
			),
			'nojquery' => array(
				'required' => false,
				'name' => tra('No JavaScript'),
				'description' => tra('Optional markup for when JavaScript is off'),
			)
		)
	);
}
	
function wikiplugin_jq($data, $params)
{
	global $headerlib, $prefs;
	extract($params, EXTR_SKIP);
	
	$nojquery = isset($nojquery) ? $nojquery : tr('<!-- jq plugin inactive: JavaScript off -->');
	if ($prefs['javascript_enabled'] != 'y') { 
		return $nojquery;
	}
	$notonready = isset($notonready) ? $notonready : false;

	// Need to manually decode greater than and less than (not sure if we want to decode all HTML entities
	$data = str_replace('&lt;', '<', $data);
	$data = str_replace('&gt;', '>', $data);

	if (!$notonready) {		
		$headerlib->add_jq_onready($data);
	} else { 
		$headerlib->add_js($data);
	}
	return '';
}
