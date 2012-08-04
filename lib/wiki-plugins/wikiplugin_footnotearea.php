<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_footnotearea.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_footnotearea_info()
{
	return array(
		'name' => tra('Footnote Area'),
		'documentation' => 'PluginFootnoteArea',
		'description' => tra('Create automatically numbered footnotes (together with PluginFootnote)'),
		'prefs' => array('wikiplugin_footnotearea'),
		'icon' => 'img/icons/text_horizontalrule.png',
		'format' => 'html',
		'params' => array(),
	);
}

function wikiplugin_footnotearea($data, $params)
{

	$html = '<div class="footnotearea">';
	$html .= '<hr />';

	foreach ($GLOBALS["footnotesData"] as $data => $number) {
		$html .= '<div class="onefootnote" id="footnote' . $number . '">';
		$html .= '<a href="#ref_footnote' . $number . '">'. $number . '.</a> ';
		$html .= '~/np~' . $data . '~np~';
		$html .= '</div>';
	}
	$html .= '</div>';
	
	return $html;
}
