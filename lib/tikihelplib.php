<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tikihelplib.php 40096 2012-03-09 21:54:05Z pkdille $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

class TikiHelpLib
{
	/*
	function TikiHelpLib()
	{

	}
	*/
/* end of class */
}


/**
 *  Returns a single html-formatted crumb
 *  @param crumb a breadcrumb instance, or
 *  @param url, desc:  a doc page and associated (already translated) help description
 */
/* static */
function help_doclink($params)
{
	global $prefs;

	extract($params);
	// Param = zone
		$ret = '';
	if (empty($url) && empty($desc) && empty($crumb)) {
		return;
	}

	if (!empty($crumb)) {
		$url = $crumb->helpUrl;
		$desc = $crumb->helpDescription;
	}

	if ($prefs['feature_help'] == 'y' and $url) {
		if (!isset($desc))
			$desc = tra('Help link');

			$ret = '<a title="' . htmlentities($desc, ENT_COMPAT, 'UTF-8') . '" href="'
						. $prefs['helpurl'] . $url . '" target="tikihelp" class="tikihelp">'
						. '<img src="img/icons/help.png"'
						. ' height="16" width="16" alt="' . tra('Help', '', true) . '" /></a>';
	}

	return $ret;
}
