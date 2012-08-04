<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_footnote.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_footnote_info()
{
	return array(
		'name' => tra('Footnote'),
		'documentation' => 'PluginFootnote',
		'description' => tra('Create automatically numbered footnotes (together with PluginFootnoteArea)'),
		'prefs' => array('wikiplugin_footnote'),
		'body' => tra('The footnote'),
		'icon' => 'img/icons/text_horizontalrule.png',
		'filter' => 'wikicontent',
		'params' => array(
			'sameas' => array(
				'required' => false,
				'name' => tra('Sameas'),
				'description' => tra('Tag to existing footnote'),
				'default' => '',
				'filter' => 'int',
			),
		)
	);
}

function wikiplugin_footnote($data, $params)
{
	if (! isset($GLOBALS['footnoteCount'])) {
		$GLOBALS['footnoteCount'] = 0;
		$GLOBALS['footnotesData'] = array();
	}

	if (! empty($data)) {
		$data = trim($data);
		if (! isset($GLOBALS['footnotesData'][$data])) {
			$GLOBALS['footnotesData'][$data] = ++$GLOBALS['footnoteCount'];
		}

		$number = $GLOBALS['footnotesData'][$data];
	} elseif (isset($params['sameas'])) {
		$number = $params['sameas'];
	}

	$html = '{SUP()}~np~' . "<a id=\"ref_footnote$number\" href=\"#footnote$number\">$number</a>" . '~/np~{SUP}';

	return $html;
}
