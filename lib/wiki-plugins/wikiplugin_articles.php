<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_articles.php 41206 2012-04-30 09:32:59Z xavidp $

function wikiplugin_articles_info()
{
	return array(
		'name' => tra('Article List'),
		'documentation' => 'PluginArticles',
		'description' => tra('Display multiple articles'),
		'prefs' => array( 'feature_articles', 'wikiplugin_articles' ),
		'icon' => 'img/icons/table_multiple.png',
		'tags' => array( 'basic' ),		
		'params' => array(
			'usePagination' => array(
				'required' => false,
				'name' => tra('Use Pagination'),
				'description' => tra('Activate pagination when articles listing are long. Default is n'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum Displayed'),
				'description' => tra('The number of articles to display in the list (no max set by default)') . '. ' . tra('If Pagination is set to y (Yes), this will determine the amount of articles per page'),
				'filter' => 'int',
				'default' => -1
			),
			'topic' => array(
				'required' => false,
				'name' => tra('Topic Name Filter'),
				'description' => tra('Filter the list of articles by topic. Example: ') . '[!]topic+topic+topic',
				'filter' => 'striptags',
				'default' => ''
			),
			'topicId' => array(
				'required' => false,
				'name' => tra('Topic ID Filter'),
				'description' => tra('Filter the list of articles by topic ID. Example: ') . '[!]topicId+topicId+topicId',
				'filter' => 'striptags',
				'default' => ''
			),
			'type' => array(
				'required' => false,
				'name' => tra('Type Filter'),
				'description' => tra('Filter the list of articles by types. Example: ') . '[!]type+type+type',
				'filter' => 'striptags',
				'default' => ''
			),
			'categId' => array(
				'required' => false,
				'name' => tra('Category ID'),
				'description' => tra('The ID of the category that articles need to be in to be listed'),
				'filter' => 'digits',
				'default' => ''
			),
			'lang' => array(
				'required' => false,
				'name' => tra('Language'),
				'description' => tra('List only articles in this language'),
				'filter' => 'lang',
				'default' => ''
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort order'),
				'description' => tra('The column and order of the sort in columnName_asc or columnName_desc format. Defaults to "publishDate_desc" (other column examples are "title", "lang", "authorName" & "topicName")').'. '.tra('Use random to have random items.'),
				'filter' => 'word',
				'default' => 'publishDate_desc'
			),
			'order' => array(
				'required' => false,
				'name' => tra('Specific order'),
				'description' => tra('List of ArticleId that must appear in this order if present'),
				'filter' => 'digits',
				'separator' => '|',
				'default' => ''
			),
			'articleId' => array(
				'required' => false,
				'name' => tra('Only these articles'),
				'description' => tra('List of ArticleId to display separated by |'),
				'filter' => 'digits',
				'separator' => '|',
				'default' => ''
			),
			'notArticleId' => array(
				'required' => false,
				'name' => tra('Not these articles'),
				'description' => tra('List of ArticleId that can not be displayed separated by |'),
				'filter' => 'digits',
				'separator' => '|',
				'default' => ''
			),
			'quiet' => array(
				'required' => false,
				'name' => tra('Quiet'),
				'description' => tra('Whether to not report when there are no articles (no reporting by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'titleonly' => array(
				'required' => false,
				'name' => tra('Title Only'),
				'description' => tra('Whether to only show the title of the articles (not set to title only by default)'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'fullbody' => array(
				'required' => false,
				'name' => tra('Body Only'),
				'description' => tra('Whether to only show the body of the articles or just the heading and title. (not set to body only by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'start' => array(
				'required' => false,
				'name' => tra('Starting Article'),
				'description' => tra('The article number that the list should start with (starts with first article by default)') . '. ' . tra('This will not work if Pagination is used.'),
				'filter' => 'int',
				'default' => 0
			),
			'dateStart' => array(
				'required' => false,
				'name' => tra('Start Date'),
				'description' => tra('Earliest date to select articles from.') . tra(' (YYYY-MM-DD)'),
				'filter' => 'date',
				'default' => ''
			),
			'dateEnd' => array(
				'required' => false,
				'name' => tra('End date'),
				'description' => tra('Latest date to select articles from.') . tra(' (YYYY-MM-DD)'),
				'filter' => 'date',
				'default' => ''
			),
			'periodQuantity' => array(
				'required' => false,
				'name' => tra('Period quantity'),
				'description' => tr('Numeric value to display only last articles published within a user defined time-frame. Used in conjunction with the next parameter "Period unit", this parameter indicates how many of those units are to be considered to define the time frame. If this parameter is set, "Start Date" and "End date" are ignored.'),
				'filter' => 'int',
				'default' => '',
			),
			'periodUnit' => array(
				'required' => false,
				'name' => tra('Period unit'),
				'description' => tr('Time unit used with "Period quantity"'),
				'filter' => 'word',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tr('Hour'), 'value' => 'hour'),
					array('text' => tr('Day'), 'value' => 'day'),
					array('text' => tr('Week'), 'value' => 'week'),
					array('text' => tr('Month'), 'value' => 'month'),
				),
			),
			'overrideDates' => array(
				'required' => false,
				'name' => tra('Override Dates'),
				'description' => tra('Whether to obey article type\'s "show before publish" and "show after expiry" settings (not obeyed by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'containerClass' => array(
				'required' => false,
				'name' => tra('Container class'),
				'description' => tra('CSS Class to add to the container DIV.article. (Default="wikiplugin_articles")'),
				'filter' => 'striptags',
				'default' => 'wikiplugin_articles'
			),
			'largefirstimage' => array(
				'required' => false,
				'name' => tra('Large First Image'),
				'description' => tra('If set to y (Yes), the first image will be displayed with the dimension used to view of the article'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'urlparam' => array(
				'required' => false,
				'name' => tra('Additional URL Param to the link to read article'),
				'filter' => 'striptags',
				'default' => ''
			),
			'actions' => array(
				'required' => false,
				'name' => tra('Show actions (buttons and links)'),
				'description' => tra('Whether to show the buttons and links to do actions on each article (for the actions you have permission to do') . ' (y|n)',
				'filter' => 'alpha',
			),
			'translationOrphan' => array(
				'required' => false,
				'name' => tra('No translation'),
				'description' => tra('User or pipe separated list of two letter language codes for additional languages to display. List pages with no language or with a missing translation in one of the language'),
				'filter' => 'alpha',
				'separator' => '|',
				'default' => ''
			),
		),
	);
}

function wikiplugin_articles($data, $params)
{
	global $smarty, $tikilib, $prefs, $tiki_p_read_article, $tiki_p_articles_read_heading, $dbTiki, $pageLang;
	global $artlib; require_once 'lib/articles/artlib.php';
	$default = array('max' => -1, 'start' => 0, 'usePagination' => 'n', 'topicId' => '', 'topic' => '', 'sort' => 'publishDate_desc', 'type' => '', 'lang' => '', 'quiet' => 'n', 'categId' => '', 'largefirstimage' => 'n', 'urlparam' => '', 'translationOrphan' => '', 'showtable' => 'n');
	$auto_args = array('lang', 'topicId', 'topic', 'sort', 'type', 'lang', 'categId');
	$params = array_merge($default, $params);

	extract($params, EXTR_SKIP);
	$filter = '';
	if ($prefs['feature_articles'] !=  'y') {
		//	the feature is disabled or the user can't read articles, not even article headings
		return("");
	}

	$urlnext = '';
	if ($usePagination == 'y') {
		//Set offset when pagniation is used
		if (!isset($_REQUEST["offset"])) {
			$start = 0;
		} else {
			$start = $_REQUEST["offset"];
		}
		
		//Default to 10 when pagination is used
		if (($max == -1)) {
			$countPagination = 10;
		}
		foreach ($auto_args as $arg) {
			if (!empty($$arg))
				$paramsnext[$arg] = $$arg;
		}
		$paramsnext['_type'] = 'absolute_path';
		$smarty->loadPlugin('smarty_function_query');
		$urlnext = smarty_function_query($paramsnext, $smarty);
	}

	$smarty->assign_by_ref('quiet', $quiet);
	$smarty->assign_by_ref('urlparam', $urlparam);
	$smarty->assign_by_ref('urlnext', $urlnext);
	
	if (!isset($containerClass)) {
		$containerClass = 'wikiplugin_articles';
	}
	$smarty->assign('container_class', $containerClass);

	$dateStartTS = 0;
	$dateEndTS = 0;
	
	// if a period of time is set, date start and end are ignored
	if (isset($periodQuantity)) {
		switch ($periodUnit) {
			case 'hour':
				$periodUnit = 3600;
    			break;
			case 'day':
				$periodUnit = 86400;
    			break;
			case 'week':
				$periodUnit = 604800;
    			break;
			case 'month':
				$periodUnit = 2628000;
    			break;
			default:
    			break;
		}
		
		if (is_int($periodUnit)) {
			$dateStartTS = $tikilib->now - ($periodQuantity * $periodUnit);
			$dateEndTS = $tikilib->now;
		}
	} else {
		if (isset($dateStart)) {
			$dateStartTS = strtotime($dateStart);
		}
		
		if (isset($dateEnd)) {
			$dateEndTS = strtotime($dateEnd);
		}
	}
	
	if (isset($fullbody) && $fullbody == 'y') {
		$smarty->assign('fullbody', 'y');
	} else {
		$smarty->assign('fullbody', 'n');
		$fullbody = 'n';
	}
	$smarty->assign('largefirstimage', $largefirstimage);
	if (!isset($overrideDates))	$overrideDates = 'n';

	if (!empty($translationOrphan)) {
		$filter['translationOrphan'] = $translationOrphan;
	}
	if (!empty($articleId)) {
		$filter['articleId'] = $articleId;
	}
	if (!empty($notArticleId)) {
		$filter['notArticleId'] = $notArticleId;
	}
	
	include_once("lib/comments/commentslib.php");
	$commentslib = new Comments($dbTiki);
	
	$listpages = $artlib->list_articles($start, $max, $sort, '', $dateStartTS, $dateEndTS, 'admin', $type, $topicId, 'y', $topic, $categId, '', '', $lang, '', '', ($overrideDates == 'y'), 'y', $filter);
	if ($prefs['feature_multilingual'] == 'y' && empty($translationOrphan)) {
		global $multilinguallib;
		include_once("lib/multilingual/multilinguallib.php");
		$listpages['data'] = $multilinguallib->selectLangList('article', $listpages['data'], $pageLang);
	}

	for ($i = 0, $icount_listpages = count($listpages["data"]); $i < $icount_listpages; $i++) {
		$listpages["data"][$i]["parsed_heading"] = $tikilib->parse_data($listpages["data"][$i]["heading"], array('min_one_paragraph' => true));
		if ($fullbody == 'y') {
			$listpages["data"][$i]["parsed_body"] = $tikilib->parse_data($listpages["data"][$i]["body"], array('min_one_paragraph' => true,	
				'is_html' => isset($prefs['article_body_is_html']) && $prefs['article_body_is_html'] === 'y'));
		}
		$comments_prefix_var='article:';
		$comments_object_var=$listpages["data"][$i]["articleId"];
		$comments_objectId = $comments_prefix_var.$comments_object_var;
		$listpages["data"][$i]["comments_cant"] = $commentslib->count_comments($comments_objectId);
		//print_r($listpages["data"][$i]['title']);
	}
	global $artlib; require_once ('lib/articles/artlib.php');

	$topics = $artlib->list_topics();
	$smarty->assign_by_ref('topics', $topics);

	if (!empty($topic) && !strstr($topic, '!') && !strstr($topic, '+')) {
		$smarty->assign_by_ref('topic', $topic);
	} elseif (!empty($topicId) &&  is_numeric($topicId)) {
		if (!empty($listpages['data'][0]['topicName']))
			$smarty->assign_by_ref('topic', $listpages['data'][0]['topicName']);
		else {
			$topic_info = $artlib->get_topic($topicId);
			if (isset($topic_info['name']))
				$smarty->assign_by_ref('topic', $topic_info['name']);
		}
	}
	if (!empty($type) && !strstr($type, '!') && !strstr($type, '+')) {
		$smarty->assign_by_ref('type', $type);
	}
	
	if ($usePagination == 'y') {
		$smarty->assign('maxArticles', $max);
		$smarty->assign_by_ref('offset', $start);
		$smarty->assign_by_ref('cant', $listpages['cant']);
	}
	if (!empty($order)) {
		foreach ($listpages['data'] as $i=>$article) {
			$memo[$article['articleId']] = $i;
		}
		foreach ($order as $articleId) {
			if (isset($memo[$articleId])) {
				$list[] = $listpages['data'][$memo[$articleId]];
			}
		}
		foreach ($listpages['data'] as $i=>$article) {
			if (!in_array($article['articleId'], $order)) {
				$list[] = $article;
			}
		}
		$smarty->assign_by_ref('listpages', $list);
	} else {
		$smarty->assign_by_ref('listpages', $listpages["data"]);
	}
	$smarty->assign('usePagination', $usePagination);
	$smarty->assign_by_ref('actions', $actions);

	if (isset($titleonly) && $titleonly == 'y') {
		return "~np~ ".$smarty->fetch('tiki-view_articles-titleonly.tpl')." ~/np~";
	} else {
		return "~np~ ".$smarty->fetch('tiki-view_articles.tpl')." ~/np~";
	}
	//return str_replace("\n","",$smarty->fetch('tiki-view_articles.tpl')); // this considers the hour in the header like a link
}
