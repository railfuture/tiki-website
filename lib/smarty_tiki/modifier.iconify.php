<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.iconify.php 40030 2012-03-04 12:55:30Z gezzzan $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     iconify
 * Purpose:  Returns a filetype icon if the filetype is known and there's an icon in img/icons/mime. Returns a default file type icon in any other case
 * -------------------------------------------------------------
 */

function smarty_modifier_iconify($string, $filetype = null)
{
	global $smarty;

	$smarty->loadPlugin('smarty_function_icon');
	$ext = strtolower(substr($string, strrpos($string, '.') + 1));
	$icon = file_exists("img/icons/mime/$ext.png") ? $ext : 'default';

	return smarty_function_icon(
					array(
						'_id' => 'img/icons/mime/'.$icon.'.png',
						'alt' => ( $filetype === null ? $icon : $filetype ),
						'class' => ''
					), 
					$smarty
	);
}
