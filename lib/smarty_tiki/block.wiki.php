<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: block.wiki.php 39783 2012-02-08 09:14:14Z sept_7 $
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Smarty plugin to display wiki-parsed content
 *
 * Usage: {wiki}wiki text here{/wiki}
 * {wiki isHtml="true" }html text as stored by ckEditor here{/wiki}
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_wiki($params, $content, $smarty, &$repeat)
{
	if ( $repeat ) return;

	global $tikilib;
	if ( (isset($params['isHtml'])) and ($params['isHtml'] ) ) {
	  $isHtml = true;
	} else {
	  $isHtml = false;
	}
	$ret = $tikilib->parse_data($content, array('is_html' => $isHtml));
	if (isset($params['line']) && $params['line'] == 1) {
		$ret = preg_replace('/<br \/>$/', '', $ret);
	}
	return $ret;
}
