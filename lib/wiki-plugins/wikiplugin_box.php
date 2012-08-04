<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_box.php 41488 2012-05-17 14:51:32Z eromneg $

function wikiplugin_box_info()
{
	return array(
		'name' => tra('Box'),
		'documentation' => 'PluginBox',
		'description' => tra('Creates a formatted box with a title bar'),
		'prefs' => array('wikiplugin_box'),
		'body' => tra('text'),
		'icon' => 'img/icons/layout_header.png',
		'tags' => array( 'basic' ),		
		'params' => array(
			'title' => array(
				'required' => false,
				'name' => tra('Box title'),
				'description' => tra('Displayed above the content'),
				'default' => '',
			),
			'bg' => array(
				'required' => false,
				'name' => tra('Background color'),
				'description' => tra('As defined by CSS, name or Hex code.'),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Box width'),
				'description' => tra('In pixels or percentage. Default value is 100%.'),
			),
			'align' => array(
				'required' => false,
				'name' => tra('Text Alignment'),
				'description' => tra('Aligns the text within the box (left aligned by default)'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Right'), 'value' => 'right'), 
					array('text' => tra('Center'), 'value' => 'center'), 
				),
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float Position'),
				'description' => tra(
								'Set the alignment for the entire box. For elements with a width of less than 100%, other elements will wrap around it 
								unless the clear parameter is appropriately set.)'
				),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Left'), 'value' => 'left'), 
					array('text' => tra('Right'), 'value' => 'right'), 
				),
			),
			'clear' => array(
				'required' => false,
				'name' => tra('Clear'),
				'description' => tra('Items are not allowed to wrap around the box if this parameter is set to.1 (Yes)'),
				'filter' => 'text',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('Apply custom CSS class to the box.'),
			),
			'id' => array(
				'required' => false,
				'name' => tra('ID'),
				'description' => tra('ID'),
			),
		),
	);
}

function wikiplugin_box($data, $params)
{
	global $tikilib;
	
	// Remove first <ENTER> if exists...
	// if (substr($data, 0, 2) == "\r\n") $data = substr($data, 2);
    
	extract($params, EXTR_SKIP);
	$bg   = (isset($bg))    ? " background:$bg" : "";
	$id = (isset($id)) ? " id=\"$id\" ":'';
	$class = (isset($class))? ' '.$class: ' ';
	$w = (isset($width)) ? " width:$width"  : "";
	$f = ($float == "left" || $float == "right")? " float:$float" : "";
	$c = (isset($clear))    ? " clear:both" : "";
	if ($float == '' or !isset($float)) {
	$begin = "<div class='cbox$class' $id style='$bg;$f;margin:0;$w;$c'>";
    } else {
	$begin = "<div class='cbox$class' $id style='$bg;$f;margin:1em;margin-$float:0;$w;$c'>";
	}
    
	if (isset($title)) {
		$begin .= "<div class='cbox-title'>$title</div>";
	}
	$begin.= "<div class='cbox-data'".(strlen($bg) > 0 ? " style=\"$bg\"" : "").">";
	$end = "</div></div>";
	// Prepend any newline char with br
	//$data = preg_replace("/\\n/", "<br />", $data);
	// Insert "\n" at data begin if absent (so start-of-line-sensitive syntaxes will be parsed OK)
	//if (substr($data, 0, 1) != "\n") $data = "\n".$data;
	//$data = $tikilib->parse_data($data);
	return $begin . $data . $end;
}
