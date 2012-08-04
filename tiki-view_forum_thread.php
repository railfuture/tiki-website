<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-view_forum_thread.php 39467 2012-01-12 19:47:28Z changi67 $

$section = 'forums';
require_once ('tiki-setup.php');

$access->check_feature('feature_forums');

include_once ("lib/comments/commentslib.php");
$commentslib = new Comments($dbTiki);
if (!isset($_REQUEST['comments_parentId']) && isset($_REQUEST['threadId'])) {
	$_REQUEST['comments_parentId'] = $_REQUEST['threadId'];
}
if (!isset($_REQUEST['comments_parentId'])) {
	$smarty->assign('msg', tra("No thread indicated"));
	$smarty->display("error.tpl");
	die;
}

if (empty($_REQUEST['forumId'])) {
	$thread_info = $commentslib->get_comment($_REQUEST['comments_parentId']);
	if (empty($thread_info['object']) || $thread_info['objectType'] != 'forum') {
		$smarty->assign('msg', tra('Incorrect thread'));
		$smarty->display('error.tpl');
		die;
	}
	$_REQUEST['forumId'] = $thread_info['object'];
}
$forum_info = $commentslib->get_forum($_REQUEST["forumId"]);
if (empty($forum_info)) {
		$smarty->assign('msg', tra('Incorrect thread'));
		$smarty->display('error.tpl');
		die;
}

require_once 'lib/cache/pagecache.php';
$pageCache = Tiki_PageCache::create()
	->disableForRegistered()
	->onlyForGet()
	->requiresPreference('memcache_forum_output')
	->addArray($_GET)
	->addValue('role', 'forum-page-output')
	->addKeys($_REQUEST, array( 'locale', 'forumId', 'comments_parentId' ))
	->checkMeta(
					'forum-page-output-meta-time', array(
						'forumId'           => @$_REQUEST['forumId'],
						'comments_parentId' => @$_REQUEST['comments_parentId']
					)
	)
	->applyCache();

if ($prefs['feature_categories'] == 'y') {
	global $categlib; include_once ('lib/categories/categlib.php');
}
if (!isset($_REQUEST['topics_offset'])) {
	$_REQUEST['topics_offset'] = 0;
}
if (!isset($_REQUEST['topics_sort_mode']) || empty($_REQUEST['topics_sort_mode'])) {
	$_REQUEST['topics_sort_mode'] = $forum_info['topicOrdering'];
} else {
	$smarty->assign('topics_sort_mode_param', '&amp;topics_sort_mode=' . $_REQUEST['topics_sort_mode']);
}
if (!isset($_REQUEST['topics_find'])) {
	$_REQUEST['topics_find'] = '';
}
if (!isset($_REQUEST['topics_threshold']) || empty($_REQUEST['topics_threshold'])) {
	$_REQUEST['topics_threshold'] = 0;
}
if (isset($_REQUEST["quote"]) && $_REQUEST["quote"]) {
	$quote = $_REQUEST["quote"];
} else {
	$quote = 0;
}
$smarty->assign('quote', $quote);
$comments_parentId = $_REQUEST["comments_parentId"];
if (isset($_REQUEST["openpost"])) {
	$smarty->assign('openpost', 'y');
} else {
	$smarty->assign('openpost', 'n');
}
$smarty->assign('comments_parentId', $_REQUEST["comments_parentId"]);
if (isset($_REQUEST["comments_grandParentId"])) {
	$smarty->assign('comments_grandParentId', $_REQUEST["comments_grandParentId"]);
}
if (isset($_REQUEST["comments_reply_threadId"])) {
	$smarty->assign('comments_reply_threadId', $_REQUEST["comments_reply_threadId"]);
} else {
	$_REQUEST["comments_reply_threadId"] = $_REQUEST["comments_parentId"];
	$smarty->assign('comments_reply_threadId', $_REQUEST["comments_reply_threadId"]);
}
$smarty->assign('forumId', $_REQUEST["forumId"]);
if (isset($_REQUEST['lock'])) {
	check_ticket('view-forum');
	if ($_REQUEST['lock'] == 'y') {
		$commentslib->lock_comment($_REQUEST["comments_parentId"]);
	} elseif ($_REQUEST['lock'] == 'n') {
		$commentslib->unlock_comment($_REQUEST["comments_parentId"]);
	}
}
$commentslib->comment_add_hit($_REQUEST["comments_parentId"]);
$commentslib->mark_comment($user, $_REQUEST['forumId'], $_REQUEST["comments_parentId"]);

$tikilib->get_perm_object($_REQUEST["forumId"], 'forum');

if ($user) {
	if ($forum_info["moderator"] == $user) {
		$tiki_p_admin_forum = 'y';
		$smarty->assign('tiki_p_admin_forum', 'y');
	} elseif (in_array($forum_info['moderator_group'], $userlib->get_user_groups($user))) {
		$tiki_p_admin_forum = 'y';
		$smarty->assign('tiki_p_admin_forum', 'y');
	}
}
if ($tiki_p_admin_forum == 'y') {
	$tiki_p_forum_post = 'y';
	$smarty->assign('tiki_p_forum_post', 'y');
	$tiki_p_forum_read = 'y';
	$smarty->assign('tiki_p_forum_read', 'y');
	$tiki_p_forum_vote = 'y';
	$smarty->assign('tiki_p_forum_vote', 'y');
	$tiki_p_forum_post_topic = 'y';
	$smarty->assign('tiki_p_forum_post_topic', 'y');
}

$access->check_permission(array('tiki_p_forum_read'));

$smarty->assign('topics_next_offset', $_REQUEST['topics_offset'] + 1);
$smarty->assign('topics_prev_offset', $_REQUEST['topics_offset'] - 1);

$threads = $commentslib->get_forum_topics($_REQUEST['forumId'], max(0, $_REQUEST['topics_offset'] - 1), 3, $_REQUEST["topics_sort_mode"]);
if ($threads[0]['threadId'] == $_REQUEST['comments_parentId'] && count($threads) >= 1) {
	$next_thread = $threads[1];
	$smarty->assign('next_topic', $next_thread['threadId']);
} elseif (count($threads) >= 2 && $threads[1]['threadId'] == $_REQUEST['comments_parentId']) {
	$next_thread = $threads[2];
	$smarty->assign('next_topic', $next_thread['threadId']);
} else {
	$smarty->assign('next_topic', false);
}
if ($threads[0]['threadId'] != $_REQUEST['comments_parentId']) {
	$prev_thread = $threads[0];
	$smarty->assign('prev_topic', $prev_thread['threadId']);
} else {
	$smarty->assign('prev_topic', false);
}

if ($tiki_p_admin_forum == 'y') {
	if (isset($_REQUEST['delsel'])) {
		if (isset($_REQUEST['forumthread'])) {
			check_ticket('view-forum');
			foreach (array_values($_REQUEST['forumthread']) as $thread) {
				$commentslib->remove_comment($thread);
				$commentslib->register_remove_post($_REQUEST['forumId'], $_REQUEST['comments_parentId']);
			}
		}
	}
	if (isset($_REQUEST['remove_attachment'])) {
		$access->check_authenticity();
		$commentslib->remove_thread_attachment($_REQUEST['remove_attachment']);
	}
	if (isset($_REQUEST['movesel'])) {
		if (isset($_REQUEST['forumthread'])) {
			check_ticket('view-forum');
			foreach (array_values($_REQUEST['forumthread']) as $thread) {
				$commentslib->set_parent($thread, $_REQUEST['moveto']);
			}
		}
	}
	if ($prefs['feature_forum_topics_archiving'] == 'y' && isset($_REQUEST['archive']) && isset($_REQUEST['comments_parentId'])) {
		check_ticket('view-forum');
		if ($_REQUEST['archive'] == 'y') {
			$commentslib->archive_thread($_REQUEST['comments_parentId']);
		} elseif ($_REQUEST['archive'] == 'n') {
			$commentslib->unarchive_thread($_REQUEST['comments_parentId']);
		}
	}
}
if ($tiki_p_forums_report == 'y' && isset($_REQUEST['report'])) {
	check_ticket('view-forum');
	$commentslib->report_post($_REQUEST['forumId'], $_REQUEST['comments_parentId'], $_REQUEST['report'], $user, '');

	$pageCache->invalidate();

	$url = "tiki-view_forum_thread.php?forumId=" . $_REQUEST['forumId'] . "&comments_parentId=" . $_REQUEST['comments_parentId'] . "&post_reported=y";
	header('location: ' . $url);
	die;
}
//shows a "thanks for reporting" message
if (isset($_REQUEST["post_reported"]) && $_REQUEST["post_reported"] == 'y') {
	$smarty->assign('post_reported', 'y');
}
$smarty->assign_by_ref('forum_info', $forum_info);
$thread_info = $commentslib->get_comment($_REQUEST["comments_parentId"], null, $forum_info);
if (empty($thread_info)) {
	$smarty->assign('msg', tra("Incorrect thread"));
	$smarty->display("error.tpl");
	die;
}
if (!empty($thread_info['parentId'])) {
	$thread_info['topic'] = $commentslib->get_comment($thread_info['parentId'], null, $forum_info);
}
if ($tiki_p_admin_forum != 'y' && $thread_info['locked'] == 'y') {
	$tiki_p_forum_post = 'n';
	$smarty->assign('tiki_p_forum_post', 'n');
}

$smarty->assign_by_ref('thread_info', $thread_info);
$comments_per_page = $forum_info['commentsPerPage'];
$thread_sort_mode = $forum_info['threadOrdering'];
$thread_style = $forum_info['threadStyle'];
$comments_vars = array(
	'forumId'
);
$comments_prefix_var = 'forum:';
$comments_object_var = 'forumId';
if (isset($forum_info["inbound_pop_server"]) && !empty($forum_info["inbound_pop_server"])) $commentslib->process_inbound_mail($_REQUEST['forumId']);

if (isset($_REQUEST['display']) && $_REQUEST['display'] == 'print_all') {
	$_REQUEST['comments_per_page'] = 0; // unlimited
	
}
$forum_mode = 'y';
include_once ("comments.php");

$cat_type = 'forum';
$cat_objid = $_REQUEST["forumId"];
include_once ('tiki-section_options.php');

if ($user && $prefs['feature_notepad'] == 'y' && isset($_REQUEST['savenotepad']) && $tiki_p_notepad == 'y') {
	check_ticket('view-forum');
	$info = $commentslib->get_comment($_REQUEST['savenotepad'], null, $forum_info);
	$tikilib->replace_note($user, 0, $info['title'], $info['data']);
}
if ($prefs['feature_user_watches'] == 'y') {
	if ($user && isset($_REQUEST['watch_event'])) {
		check_ticket('view-forum');
		if ($_REQUEST['watch_action'] == 'add') {
			$tikilib->add_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object'], 'forum topic', $forum_info['name'] . ':' . $thread_info['title'], "tiki-view_forum_thread.php?forumId=" . $_REQUEST['forumId'] . "&amp;comments_parentId=" . $_REQUEST['comments_parentId']);
		} else {
			$tikilib->remove_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object'], 'forum topic');
		}
	}
	$smarty->assign('user_watching_topic', 'n');
	if ($user && $tikilib->user_watches($user, 'forum_post_thread', $_REQUEST['comments_parentId'], 'forum topic')) {
		$smarty->assign('user_watching_topic', 'y');
	}
	// Check, if the user is watching this forum's topic and thread by a category.
	if ($prefs['feature_categories'] == 'y') {
		$watching_categories_temp = $categlib->get_watching_categories($_REQUEST["forumId"], 'forum', $user);
		$smarty->assign('category_watched', 'n');
		if (count($watching_categories_temp) > 0) {
			$smarty->assign('category_watched', 'y');
			$watching_categories = array();
			foreach ($watching_categories_temp as $wct) {
				$watching_categories[] = array(
					"categId" => $wct,
					"name" => $categlib->get_category_name($wct)
				);
			}
			$smarty->assign('watching_categories', $watching_categories);
		}
	}
}
if ($tiki_p_admin_forum == 'y' || $prefs['feature_forum_quickjump'] == 'y') {
	$all_forums = $commentslib->list_forums(0, -1, 'name_asc', '');
	$temp_max = count($all_forums["data"]);
	for ($i = 0; $i < $temp_max; $i++) {
		if ($userlib->object_has_one_permission($all_forums["data"][$i]["forumId"], 'forum')) {
			if ($tiki_p_admin == 'y' || $userlib->object_has_permission($user, $all_forums["data"][$i]["forumId"], 'forum', 'tiki_p_admin_forum') || $userlib->object_has_permission($user, $all_forums["data"][$i]["forumId"], 'forum', 'tiki_p_forum_read')) {
				$all_forums["data"][$i]["can_read"] = 'y';
			} else {
				$all_forums["data"][$i]["can_read"] = 'n';
			}
		} else {
			$all_forums["data"][$i]["can_read"] = 'y';
		}
	}
	$smarty->assign('all_forums', $all_forums['data']);
}
// Generate the list of topics, used in comments.tpl for the moderator actions (e.g. move a comment to another topic)
if ($tiki_p_admin_forum == 'y') {
	$topics = $commentslib->get_forum_topics($_REQUEST['forumId'], 0, 200, 'commentDate_desc');
	$smarty->assign_by_ref('topics', $topics);
}
$smarty->assign('unread', 0);
if ($user && $prefs['feature_messages'] == 'y' && $tiki_p_messages == 'y') {
	$unread = $tikilib->user_unread_messages($user);
	$smarty->assign('unread', $unread);
}
if ($tiki_p_admin_forum == 'y') {
	$smarty->assign('queued', $commentslib->get_num_queued($comments_objectId));
	$smarty->assign('reported', $commentslib->get_num_reported($_REQUEST['forumId']));
}
if ($prefs['feature_freetags'] == 'y') {
	$cat_type = 'forum post';
	$cat_objid = $comments_parentId;
	$tags = $freetaglib->get_tags_on_object($cat_objid, $cat_type);
	$smarty->assign('freetags', $tags);
}
if (isset($_SESSION['feedbacks'])) {
	$smarty->assign('feedbacks', $_SESSION['feedbacks']);
	unset($_SESSION['feedbacks']);
}
$defaultRows = $prefs['default_rows_textarea_forumthread'];
$smarty->assign('forum_mode', 'y');

if ($prefs['feature_actionlog'] == 'y') {
	$logslib->add_action('Viewed', $_REQUEST['forumId'], 'forum', 'comments_parentId=' . $comments_parentId);
}
ask_ticket('view-forum');
if ($prefs['feature_forum_parse'] == 'y') {
	global $wikilib;
	include_once ('lib/wiki/wikilib.php');
	$plugins = $wikilib->list_plugins(true, 'editpost2');
	$smarty->assign_by_ref('plugins', $plugins);
}
if (!empty($_REQUEST['view_atts']) && $_REQUEST['view_atts'] == 'y') {
	$fa_offset = isset($_REQUEST['fa_offset'])? $_REQUEST['fa_offset']:0;
	$fa_maxRecords = isset($_REQUEST['fa_maxRecords'])? $_REQUEST['fa_maxRecords']:$prefs['maxRecords'];
	$atts = $commentslib->get_all_thread_attachments($_REQUEST['comments_parentId'], $fa_offset, $fa_maxRecords);
	$atts['offset'] = $fa_offset;
	$atts['maxRecords'] = $fa_maxRecords;
	$smarty->assign_by_ref('atts', $atts);
	$smarty->assign_by_ref('view_atts', $_REQUEST['view_atts']);
}

// Display the template
if (isset($_REQUEST['display'])) {
	// Remove icons and actions that should not be printed
	$prefs['forum_thread_user_settings'] = 'n';
	$smarty->assign('display', $_REQUEST['display']);
	$smarty->assign('thread_show_comment_footers', 'n');
	$smarty->assign('thread_show_pagination', 'n');
	$smarty->assign('tiki_p_forum_post', 'n');
	$smarty->assign('tiki_p_admin_forum', 'n');
	$smarty->assign('tiki_p_forum_edit_own_posts', 'n');
	$smarty->assign('tiki_p_notepad', 'n');
	// Display the forum messages
	$smarty->assign('mid', 'tiki-print_forum_thread.tpl');
	// Allow PDF export by installing a Mod that define an appropriate function
	if ($_REQUEST['display'] == 'pdf') {
		require_once 'lib/pdflib.php';
		$generator = new PdfGenerator();
		$pdf = $generator->getPdf('tiki-view_forum_thread.php', array('display' => 'print', 'comments_parentId' => $_REQUEST['comments_parentId'], 'forumId' => $_REQUEST['forumId']));

		header('Cache-Control: private, must-revalidate');
		header('Pragma: private');
		header("Content-Description: File Transfer");
		header('Content-disposition: attachment; filename="'. $thread_info['title'] . '.pdf"');
		header("Content-Type: application/pdf");
		header("Content-Transfer-Encoding: binary");
		header('Content-Length: '. strlen($pdf));
		echo $pdf;

	} else {
		$smarty->display('tiki-print.tpl');
	}
} else {
	$smarty->assign('pdf_export', ($prefs['print_pdf_from_url'] != 'none') ? 'y' : 'n');
	$smarty->assign('display', '');
	$smarty->assign('mid', 'tiki-view_forum_thread.tpl');
	$smarty->display('tiki.tpl');
}

