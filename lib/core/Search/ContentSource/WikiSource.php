<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: WikiSource.php 40234 2012-03-17 19:17:41Z changi67 $

class Search_ContentSource_WikiSource implements Search_ContentSource_Interface
{
	private $db;
	private $tikilib;
	private $flaggedrevisionlib;
	private $quantifylib;

	function __construct()
	{
		global $prefs;

		$this->db = TikiDb::get();
		$this->tikilib = TikiLib::lib('tiki');

		if ($prefs['flaggedrev_approval'] == 'y') {
			$this->flaggedrevisionlib = TikiLib::lib('flaggedrevision');
		}

		if ($prefs['quantify_changes'] == 'y') {
			$this->quantifylib = TikiLib::lib('quantify');
		}
	}

	function getDocuments()
	{
		return $this->db->table('tiki_pages')->fetchColumn('pageName', array());
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$wikilib = TikiLib::lib('wiki');

		$info = $this->tikilib->get_page_info($objectId, true, true);

		$contributors = $wikilib->get_contributors($objectId, $info['user']);
		if (! in_array($info['user'], $contributors)) {
			$contributors[] = $info['user'];
		}

		$data = array(
			'title' => $typeFactory->sortable($info['pageName']),
			'hash' => $typeFactory->identifier($info['version']),
			'language' => $typeFactory->identifier(empty($info['lang']) ? 'unknown' : $info['lang']),
			'modification_date' => $typeFactory->timestamp($info['lastModif']),
			'description' => $typeFactory->plaintext($info['description']),
			'contributors' => $typeFactory->multivalue($contributors),

			'wiki_content' => $typeFactory->wikitext($info['data']),

			'view_permission' => $typeFactory->identifier('tiki_p_view'),
			'url' => $typeFactory->identifier($wikilib->sefurl($info['pageName'])),
		);

		if ($this->quantifylib) {
			$data['wiki_uptodateness'] = $typeFactory->sortable($this->quantifylib->getCompleteness($info['page_id']));
		}

		$out = $data;

		if ($this->flaggedrevisionlib && $this->flaggedrevisionlib->page_requires_approval($info['pageName'])) {
			$out = array();

			// Will provide two documents: one approved and one latest
			$versionInfo = $this->flaggedrevisionlib->get_version_with($info['pageName'], 'moderation', 'OK');

			if (! $versionInfo || $versionInfo['version'] != $info['version']) {
				// No approved version or approved version differs, latest content marked as such
				$out[] = array_merge(
								$data, 
								array(
									'title' => $typeFactory->sortable(tr('%0 (latest)', $info['pageName'])),
									'view_permission' => $typeFactory->identifier('tiki_p_wiki_view_latest'),
									'url' => $typeFactory->identifier($wikilib->sefurl($info['pageName'], true) . 'latest'),
								)
				);
			}

			if ($versionInfo) {
				// Approved version not latest, include approved version in index
				// Also applies when versions are equal, data would be the same
				$out[] = array_merge(
								$data, 
								array(
									'wiki_content' => $typeFactory->wikitext($versionInfo['data']),
									'hash' => $typeFactory->identifier($versionInfo['version']),
								)
				);
			}
		}


		return $out;
	}

	function getProvidedFields()
	{
		$fields = array(
			'title',
			'hash',
			'url',
			'language',
			'modification_date',
			'description',
			'contributors',

			'wiki_content',

			'view_permission',
		);

		if ($this->quantifylib) {
			$fields[] = 'wiki_uptodateness';
		}

		return $fields;
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,
			'description' => true,

			'wiki_content' => false,
		);
	}
}

