<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_sort.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_sort_info()
{
	return array(
		'name' => tra('Sort'),
		'documentation' => 'PluginSort',
		'description' => tra('Sort lines of text'),
		'prefs' => array( 'wikiplugin_sort' ),
		'body' => tra('Data to sort, one entry per line.'),
		'filter' => 'text',
		'icon' => 'img/icons/table_sort.png',
		'tags' => array( 'basic' ),
		'params' => array(
			'sort' => array(
				'required' => false,
				'name' => tra('Order'),
				'description' => tra('Set the sort order of lines of content (default is ascending)'),
				'filter' => 'alpha',
				'default' => 'asc',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Ascending'), 'value' => 'asc'), 
					array('text' => tra('Descending'), 'value' => 'desc'),
					array('text' => tra('Reverse'), 'value' => 'reverse'),
					array('text' => tra('Shuffle'), 'value' => 'shuffle')
				)
			)
		)
	);
}

function wikiplugin_sort($data, $params)
{
	global $tikilib;

	extract($params, EXTR_SKIP);

	$sort = (isset($sort)) ? $sort : "asc";

	$lines = preg_split("/\n+/", $data, -1, PREG_SPLIT_NO_EMPTY); // separate lines into array
	// $lines = array_filter( $lines, "chop" ); // remove \n  
	srand((float)microtime() * 1000000); // needed for shuffle;

	if ($sort == "asc") {
		natcasesort($lines);
	} else if ($sort == "desc") {
		natcasesort($lines);
		$lines = array_reverse($lines);
	} else if ($sort == "reverse") {
		$lines = array_reverse($lines);
	} else if ($sort == "shuffle") {
		shuffle($lines);
	}

	reset($lines);

	if (is_array($lines)) {
		$data = implode("\n", $lines);
	}

	$data = trim($data);
	return $data;
}
