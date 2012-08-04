<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: include_search.php 40060 2012-03-07 06:52:03Z pkdille $

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
if (isset($_REQUEST['searchprefs'])) {
	check_ticket('admin-inc-search');
}
function activated_features()
{
	global $prefs;
	$activated_features = array();

	$features = array(
		'articles',
		'blogs',
		'directory',
		'comments',
		'faqs',
		'file_galleries',
		'forums',
		'wiki',
		'trackers',
		'galleries'
	);

	foreach ($features as $feature) {
		switch ($feature) {
			case 'blogs':
				if (isset($prefs['feature_blogs']) and $prefs['feature_blogs'] == 'y') {
					$activated_features[] = 'blogs';
					$activated_features[] = 'blogs_posts';
				}
							break;

			case 'comments':
				$activated_features[] = 'comments';
							break;

			case 'wiki':
				if (isset($prefs['feature_wiki']) and $prefs['feature_wiki'] == 'y') {
					$activated_features[] = 'pages';
				}
							break;

			case 'articles':
				if (isset($prefs['feature_articles']) and $prefs['feature_articles'] == 'y') {
					$activated_features[] = 'articles';
				}
							break;

			case 'faqs':
				if (isset($prefs['feature_faqs']) and $prefs['feature_faqs'] == 'y') {
					$activated_features[] = 'faqs';
					$activated_features[] = 'faqs_questions';
				}
							break;

			case 'file_galleries':
				if (isset($prefs['feature_file_galleries']) and $prefs['feature_file_galleries'] == 'y') {
					$activated_features[] = 'file_galleries';
					$activated_features[] = 'files';
				}
							break;

			case 'forums':
				if (isset($prefs['feature_forums']) and $prefs['feature_forums'] == 'y') {
					$activated_features[] = 'forums';
				}
							break;

			case 'galleries':
				if (isset($prefs['feature_galleries']) and $prefs['feature_galleries'] == 'y') {
					$activated_features[] = 'galleries';
					$activated_features[] = 'images';
				}
							break;

			case 'trackers':
				if (isset($prefs['feature_trackers']) and $prefs['feature_trackers'] == 'y') {
					$activated_features[] = 'trackers';
					$activated_features[] = 'tracker_items';
				}
							break;

			case 'directory':
				if (isset($prefs['feature_directory']) and $prefs['feature_directory'] == 'y') {
					$activated_features[] = 'directory_categories';
					$activated_features[] = 'directory_sites';
				}
							break;
		}
	}
	return $activated_features;
}
global $tiki_p_admin, $unifiedsearchlib;
require_once 'lib/search/searchlib-unified.php';

$queueCount = $unifiedsearchlib->getQueueCount();

if ($tiki_p_admin == 'y' && isset($_REQUEST['rebuild']) && $_REQUEST['rebuild'] == 'now') {
	$stat = $unifiedsearchlib->rebuild(isset($_REQUEST['loggit']));
	$smarty->assign_by_ref('stat', $stat);

	TikiLib::lib('cache')->empty_type_cache('search_valueformatter');
}

if ($tiki_p_admin == 'y' && !empty($_REQUEST['process'])) {
	if (is_numeric($_REQUEST['process'])) {
		$toProcess = (int) $_REQUEST['process'];
	} else if ($_REQUEST['process'] === 'all') {
		$toProcess = 10;	// do it in batches
	} else {
		$toProcess = 0;
	}
	@ini_set('max_execution_time', 0);
	@ini_set('memory_limit', -1);
	$stat = $unifiedsearchlib->processUpdateQueue($toProcess);
	$smarty->assign_by_ref('stat', $stat);

	$queueCount = $unifiedsearchlib->getQueueCount();
}

$smarty->assign('queue_count', $queueCount);

if ($tiki_p_admin == 'y' && isset($_REQUEST['optimize']) && $_REQUEST['optimize'] == 'now') {
	global $unifiedsearchlib; require_once 'lib/search/searchlib-unified.php';

	@ini_set('max_execution_time', 0);
	@ini_set('memory_limit', -1);
	$stat = $unifiedsearchlib->getIndex()->optimize();
}

if ($tiki_p_admin == 'y' && !empty($_REQUEST['refresh_index_all_now']) && $_REQUEST['refresh_index_all_now'] == 'y') {
	require_once ('lib/search/refresh-functions.php');
	foreach (activated_features() as $feature) refresh_index($feature);
	$smarty->assign('refresh_index_all_now', $_REQUEST['refresh_index_all_now']);
}

if ($tiki_p_admin == 'y' && !empty($_REQUEST['refresh_files_index_now']) && $_REQUEST['refresh_files_index_now'] == 'y') {
	require_once ('lib/search/refresh-functions.php');
	refresh_index('files');
	$smarty->assign('refresh_files_index_now', $_REQUEST['refresh_files_index_now']);
}

if ($tiki_p_admin == 'y' && !empty($_REQUEST['refresh_index_now']) && $_REQUEST['refresh_index_now'] == 'y') {
	require_once ('lib/search/refresh-functions.php');
	refresh_index('pages');
	$smarty->assign('refresh_index_now', $_REQUEST['refresh_index_now']);
}

if ($tiki_p_admin == 'y' && !empty($_REQUEST['refresh_tracker_index_now']) && $_REQUEST['refresh_tracker_index_now'] == 'y') {
	require_once ('lib/search/refresh-functions.php');
	refresh_index('tracker_items');
	$smarty->assign('refresh_tracker_index_now', $_REQUEST['refresh_tracker_index_now']);
}

if (isMySQLFulltextSearchSupported()) {
	$smarty->assign('no_fulltext_support', false);
} else {
	$smarty->assign('no_fulltext_support', true);
}

$headerlib->add_cssfile('css/admin.css');
ask_ticket('admin-inc-search');
