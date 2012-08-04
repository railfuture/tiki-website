<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: block.wikiplugin.php 39469 2012-01-12 21:13:48Z changi67 $

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_wikiplugin( $params, $content, $smarty, $repeat = false )
{
 	global $tikilib;

	if ( $repeat ) return;

	if ( ! isset( $params['_name'] ) ) {
		return '<div class="error">' . tra('Plugin name not specified.') . '</div>';
	}

	$name = $params['_name'];
	unset( $params['_name'] );
	
	$parserlib = TikiLib::lib('parser');
	return $parserlib->plugin_execute(
					$name, 
					$content, 
					$params, 
					0, 
					false, 
					array(
						'context_format' => 'html',
						'ck_editor' => false,
						'is_html' => 'y'
					) 
	);
}

