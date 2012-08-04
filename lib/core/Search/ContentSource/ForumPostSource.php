<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ForumPostSource.php 39469 2012-01-12 21:13:48Z changi67 $

class Search_ContentSource_ForumPostSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getDocuments()
	{
		global $prefs;
		if ($prefs['unified_forum_deepindexing'] == 'y') {
			$filters = array('objectType' => 'forum', 'parentId' => 0);
		} else {
			$filters = array('objectType' => 'forum');
		}
		return $this->db->table('tiki_comments')->fetchColumn('threadId', $filters);
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		global $prefs;

		$commentslib = TikiLib::lib('comments');
		$comment = $commentslib->get_comment($objectId);

		$lastModification = $comment['commentDate'];
		$content = $comment['data'];
		$author = array($comment['userName']);

		$thread = $commentslib->get_comments($comment['objectType'] . ':' . $comment['object'], $objectId, 0, 0);
		$forum_info = $commentslib->get_forum($comment['object']);
		$forum_language = $forum_info['forumLanguage'] ? $forum_info['forumLanguage'] : 'unknown';

		if ($prefs['unified_forum_deepindexing'] == 'y') {
			foreach ($thread['data'] as $reply) {
				$content .= "\n{$reply['data']}";
				$lastModification = max($lastModification, $reply['commentDate']);
				$author[] = $comment['userName'];
			}
		}

		$data = array(
			'title' => $typeFactory->sortable($comment['title']),
			'language' => $typeFactory->identifier($forum_language),
			'modification_date' => $typeFactory->timestamp($lastModification),
			'contributors' => $typeFactory->multivalue(array_unique($author)),

			'post_content' => $typeFactory->wikitext($content),
			'parent_thread_id' => $typeFactory->identifier($comment['parentId']),

			'parent_object_type' => $typeFactory->identifier($comment['objectType']),
			'parent_object_id' => $typeFactory->identifier($comment['object']),
			'parent_view_permission' => $typeFactory->identifier('tiki_p_forum_read'),
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

			'post_content',
			'parent_thread_id',

			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,

			'post_content' => false,
		);
	}
}

