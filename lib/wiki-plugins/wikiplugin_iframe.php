<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_iframe.php 40420 2012-03-26 19:51:43Z marclaporte $

function wikiplugin_iframe_info()
{
	return array(
		'name' => tra('Iframe'),
		'documentation' => 'PluginIframe',
		'description' => tra('Include another web page within a frame'),
		'prefs' => array( 'wikiplugin_iframe' ),
		'body' => tra('URL'),
		'format' => 'html',
		'validate' => 'all',
		'tags' => array( 'basic' ),
		'icon' => 'img/icons/page_copy.png',
		'params' => array(
			'name' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Name'),
				'description' => tra('Name'),
				'default' => '',
			),
			'title' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Title'),
				'description' => tra('Frame title'),
				'default' => '',
			),
			'width' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width in pixels or %'),
				'default' => '',
			),
			'height' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Pixels or %'),
				'default' => '',
			),
			'align' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Alignment'),
				'description' => tra('Align the iframe on the page'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Top'), 'value' => 'top'), 
					array('text' => tra('Middle'), 'value' => 'middle'), 
					array('text' => tra('Bottom'), 'value' => 'bottom'), 
					array('text' => tra('Left'), 'value' => 'left'), 
					array('text' => tra('Right'), 'value' => 'right') 
				)
			),
			'frameborder' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Frame Border'),
				'description' => tra('Choose whether to show a border around the iframe'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'marginheight' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Height'),
				'description' => tra('Margin height in pixels'),
				'default' => '',
			),
			'marginwidth' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Width'),
				'description' => tra('Margin width in pixels'),
				'default' => '',
			),
			'scrolling' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Scrolling'),
				'description' => tra('Choose whether to add a scroll bar'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'yes'), 
					array('text' => tra('No'), 'value' => 'no'),
					array('text' => tra('Auto'), 'value' => 'auto'),
				)
			),
			'src' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('URL'),
				'default' => '',
			),
		), 
	);
}

function wikiplugin_iframe($data, $params)
{

	extract($params, EXTR_SKIP);
	$ret = '<iframe ';

	if (isset($name)) {
		$ret .= " name=\"$name\"";
	}
	if (isset($title)) {
		$ret .= " title=\"$title\"";
	}
	if (isset($width)) {
		$ret .= " width=\"$width\"";
	}
	if (isset($height)) {
		$ret .= " height=\"$height\"";
	}
	if (isset($align)) {
		$ret .= " align=\"$align\"";
	}
	if (isset($frameborder)) {
		$ret .= " frameborder=\"$frameborder\"";
	}
	if (isset($marginheight)) {
		$ret .= " marginheight=\"$marginheight\"";
	}
	if (isset($marginwidth)) {
		$ret .= " marginwidth=\"$marginwidth\"";
	}
	if (isset($scrolling)) {
		$ret .= " scrolling=\"$scrolling\"";
	}
	if (isset($src)) {
		$ret .= " src=\"$src\"";
	} elseif (!empty($data)) {
		$ret .= " src=\"$data\"";
	}
	$ret .= ">$data</iframe>";
	return $ret;
}
