<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-user_cached_bookmark.php 39467 2012-01-12 19:47:28Z changi67 $

$section = 'mytiki';
require_once ('tiki-setup.php');
include_once ('lib/bookmarks/bookmarklib.php');

$access->check_feature('feature_user_bookmarks', '', 'community');
$access->check_user($user);

if (!isset($_REQUEST['urlid'])) {
	$smarty->assign('msg', tra('No url indicated'));
	$smarty->display('error.tpl');
	die;
}
// Get a list of last changes to the Wiki database
$info = $bookmarklib->get_url($_REQUEST['urlid']);
$info['refresh'] = $info['lastUpdated'];
$smarty->assign_by_ref('info', $info);
$smarty->assign('ggcacheurl', 'http://google.com/search?q=cache:' . urlencode(strstr($info['url'], 'http://')));
$smarty->display('tiki-view_cache.tpl');
