<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-forums_rss.php 39467 2012-01-12 19:47:28Z changi67 $

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/rss/rsslib.php');

$access->check_feature('feature_forums');

if ($prefs['feed_forums'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

$res=$access->authorize_rss(array('tiki_p_admin_forum','tiki_p_forum_read'));
if ($res) {
   if ($res['header'] == 'y') {
      header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
      header('HTTP/1.0 401 Unauthorized');
   }
   $errmsg=$res['msg'];
   require_once ('tiki-rss_error.php');
}

$feed = "forums";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$title = $prefs['feed_forums_title'];
	$desc = $prefs['feed_forums_desc'];
	$id = 'object';
	$param = "threadId";
	$descId = "data";
	$dateId = "commentDate";
	$authorId = "userName";
	$titleId = "title";
	$readrepl = 'tiki-view_forum_thread.php?forumId=%s&comments_parentId=%s';

	$changes = $tikilib -> list_all_forum_topics(0, $prefs['feed_forums_max'], $dateId.'_desc', '');
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, $param, $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
