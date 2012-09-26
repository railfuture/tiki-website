<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 999999991_decode_pages_sources_tiki.php 42327 2012-07-10 13:01:10Z jonnybradley $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/* ABOUT THE NUMBERING:
 *
 * Because this script calls tiki-setup_base.php , which does very
 * complicated things like checking if users are logged in and so
 * on, this script depends on every other script, because
 * tiki-setup_base.php does.
 *
 * -----
 *
 * 				see http://info.tiki.org/HTMLentities for examples of HTML entities
 */



function upgrade_999999991_decode_pages_sources_tiki($installer)
{
	global $user_overrider_prefs;
	set_time_limit(60 * 60); // Set maximum execution time to 1 hour since this runs on all pages
	include_once('tiki-setup_base.php');
	include_once ('lib/categories/categlib.php');	// needed for cat_jail fn in list_pages()
	include_once('lib/wiki/wikilib.php');

	$converter = new convertToTiki9();
	$converter->convertPages();
	$converter->convertModules();
}

