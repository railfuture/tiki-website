<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-top_active_blogs.php 39469 2012-01-12 21:13:48Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_top_active_blogs_info()
{
	return array(
		'name' => tra('Most Active blogs'),
		'description' => tra('Displays the specified number of blogs with links to them, from the most active one to the least.') . tra('Blog activity measurement can be more or less accurate.'),
		'prefs' => array('feature_blogs'),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_active_blogs($mod_reference, $module_params)
{
	global $smarty;
	global $bloglib; require_once('lib/blogs/bloglib.php');
	$ranking = $bloglib->list_blogs(0, $mod_reference["rows"], 'activity_desc', '');
	
	$smarty->assign('modTopActiveBlogs', $ranking["data"]);
}
