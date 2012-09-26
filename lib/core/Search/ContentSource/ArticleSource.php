<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ArticleSource.php 42490 2012-07-27 14:59:42Z lphuberdeau $

class Search_ContentSource_ArticleSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getDocuments()
	{
		return $this->db->table('tiki_articles')->fetchColumn('articleId', array());
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$artlib = TikiLib::lib('art');
		
		$article = $artlib->get_article($objectId, false);

		$data = array(
			'title' => $typeFactory->sortable($article['title']),
			'language' => $typeFactory->identifier($article['lang'] ? $article['lang'] : 'unknown'),
			'modification_date' => $typeFactory->timestamp($article['publishDate']),
			'contributors' => $typeFactory->multivalue(array($article['author'])),
			'description' => $typeFactory->plaintext($article['heading']),

			'topic_id' => $typeFactory->identifier($article['topicId']),
			'article_type' => $typeFactory->identifier($article['type']),
			'article_content' => $typeFactory->wikitext($article['body']),
			'article_topline' => $typeFactory->wikitext($article['topline']),
			'article_subtitle' => $typeFactory->wikitext($article['subtitle']),
			'article_author' => $typeFactory->plaintext($article['authorName']),

			'view_permission' => $typeFactory->identifier('tiki_p_read_article'),
			'parent_object_type' => $typeFactory->identifier('topic'),
			'parent_object_id' => $typeFactory->identifier($article['topicId']),
			'parent_view_permission' => $typeFactory->identifier('tiki_p_read_topic'),
		);

		return $data;
	}

	function getProvidedFields()
	{
		return array(
			'title',
			'language',
			'modification_date',
			'contributors',
			'description',

			'topic_id',
			'article_content',
			'article_type',
			'article_topline',
			'article_subtitle',
			'article_author',

			'view_permission',
			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,
			'description' => true,

			'article_content' => false,
			'article_topline' => false,
			'article_subtitle' => false,
			'article_author' => false,
		);
	}
}

