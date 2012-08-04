<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-forums_most_commented_forums.php 40387 2012-03-26 04:04:06Z lindonb $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_forums_most_commented_forums_info()
{
	return array(
		'name' => tra('Most Commented Forums'),
		'description' => tra('Display the specified number of the forums with the most comments.'),
		'prefs' => array('feature_forums'),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_forums_most_commented_forums($mod_reference, $module_params)
{
	global $smarty;
	global $ranklib; include_once ('lib/rankings/ranklib.php');
	
	$ranking = $ranklib->forums_ranking_most_commented_forum($mod_reference["rows"]);
	$smarty->assign('modForumsMostCommentedForums', $ranking["data"]);
}
