<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: rsslib.php 40803 2012-04-07 14:25:49Z Jyhem $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class RSSLib extends TikiDb_Bridge
{
	private $items;
	private $feeds;
	private $modules;

	function __construct()
	{
		$this->items = $this->table('tiki_rss_items');
		$this->feeds = $this->table('tiki_rss_feeds');
		$this->modules = $this->table('tiki_rss_modules');
	}

	// ------------------------------------
	// functions for rss feeds we syndicate
	// ------------------------------------

	/**
	 * Return the feed format name
	 *
	 * @return string feed format name
	 */
	function get_current_feed_format_name()
	{
		$ver = $this->get_current_feed_format();

		if ($ver == '2') {
			$name = 'rss';
		} else if ($ver == '5') {
			$name = 'atom';
		}

		return $name;
	}

	/**
	 * Return the feed format code (2 for rss and 5 for atom)
	 * we currently use (user param or default value)
	 *
	 * @return int $ver
	 */
	function get_current_feed_format()
	{
		global $prefs;

		if (isset($_REQUEST['ver'])) {
			$ver = $_REQUEST['ver'];
		} else {
			$ver = $prefs['feed_default_version'];
		}

		return $ver;
	}

	/* check for cached rss feed data */
	function get_from_cache($uniqueid, $rss_version="9")
	{
		global $tikilib, $user, $prefs;

		$rss_version=$this->get_current_feed_format();

		$output = array();
		$output["content-type"] = "application/xml";
		$output["encoding"] = "UTF-8";

		$output["data"] = "EMPTY";

		// caching rss data for anonymous users only
		if (isset($user) && $user<>"") return $output;

		$res = $this->feeds->fetchFullRow(array('name' => $uniqueid, 'rssVer' => $rss_version));
		if (!$res) {
			// nothing found, then insert empty row for this feed+rss_ver
			$this->feeds->insert(
							array(
								'name' => $uniqueid,
								'rssVer' => $rss_version,
								'refresh' => (int) $prefs['feed_cache_time'],
								'lastUpdated' => 1,
								'cache' => '-',
							)
			);
		} else {
			// entry found in db:
			$output["data"] = $res["cache"];
			// $refresh = $res["refresh"]; // global cache time currently
			$refresh = $prefs['feed_cache_time']; // global cache time currently
			$lastUpdated = $res["lastUpdated"];
			// up to date? if not, then set trigger to reload data:
			if ($tikilib->now - $lastUpdated >= $refresh ) { 
				$output["data"]="EMPTY";
			}
		}
		$output['content-type'] = 'application/xml';
		return $output;
	}

	/* put to cache */
	function put_to_cache($uniqueid, $rss_version="9", $output)
	{
		global $user, $tikilib;
		// caching rss data for anonymous users only		
		if (isset($user) && $user<>"") return;
		if ($output=="" || $output=="EMPTY") return;

		$rss_version=$this->get_current_feed_format();

		// update cache with new generated data if data not empty

		$this->feeds->update(
						array(
							'cache' => $output,
							'lastUpdated' => $tikilib->now,
							), 
						array(
						'name' => $uniqueid,
						'rssVer' => $rss_version,
							)
		);
	}

	/**
	 * Generate a feed (ATOM 1.0 or RSS 2.0 using Zend_Feed_Writer_Feed
	 *
	 * @param string $section Tiki feature the feed is related to
	 * @param string $uniqueid
	 * @param string $feed_version DEPRECATED
	 * @param array $changes the content that will be used to generate the feed
	 * @param string $itemurl base url for items (e.g. "tiki-view_blog_post.php?postId=%s")
	 * @param string $urlparam
	 * @param string $id name of the id field used to identify each feed item (e.g. "postId")
	 * @param string $title title for the feed
	 * @param string $titleId name of the key in the $changes array with the title of each item
	 * @param string $desc description for the feed
	 * @param string $descId name of the key in the $changes array with the description of each item
	 * @param string $dateId name of the key in the $changes array with the date of each item
	 * @param string $authorId name of the key in the $changes array with the author of each item
	 * @param bool $fromcache if true recover the feed from cache
	 * 
	 * @return array the generated feed
	 */
	function generate_feed($section, $uniqueid, $feed_version, $changes, $itemurl
			, $urlparam, $id, $title, $titleId, $desc, $descId, $dateId, $authorId
			, $fromcache=false
			) 
	{
		global $tikilib, $tiki_p_admin, $prefs, $userlib, $prefs, $smarty;

		// both title and description fields cannot be null
		if (empty($title) || empty($desc)) {
			$msg = tra('The fields title and description are mandatory to generate a feed.');
			if ($tiki_p_admin) {
				$msg .= ' ' . tra('To fix this error go to Admin -> Feeds.');
			} else {
				$msg .= ' ' . tra('Please contact the site administrator and ask him to fix this error');
			}
			$smarty->assign('msg', $msg);
			$smarty->display('error.tpl');
			die;
		}

		$feed_format = $this->get_current_feed_format();
		$feed_format_name = $this->get_current_feed_format_name();

		if ($prefs['feed_cache_time'] < 1) $fromcache=false;

		// only get cache data if rss cache is enabled
		if ($fromcache) {
			$output = $this->get_from_cache($uniqueid, $feed_format);
			if ($output['data'] != 'EMPTY') return $output;
		}

		$urlarray = parse_url($_SERVER["REQUEST_URI"]);
		$rawPath = str_replace('\\', '/', dirname($urlarray["path"]));
		$URLPrefix = $tikilib->httpPrefix() . $rawPath;
		if ($rawPath != "/") {
			$URLPrefix .= "/"; // Append a slash unless Tiki is in the document root. dirname() removes a slash except in that case.
		}

		if ($prefs['feed_'.$section.'_index'] != '') {
			$feedLink = $prefs['feed_'.$section.'_index'];
		} else {
			$feedLink = htmlspecialchars($tikilib->httpPrefix().$_SERVER["REQUEST_URI"]);
		}

		$img = htmlspecialchars($URLPrefix.$prefs['feed_img']);

		$title = htmlspecialchars($title);
		$desc = htmlspecialchars($desc);
		$read = $URLPrefix.$itemurl;

		$feed = new Zend_Feed_Writer_Feed();
		$feed->setTitle($title);
		$feed->setDescription($desc);

		if (!empty($prefs['feed_language'])) {
			$feed->setLanguage($prefs['feed_language']);
		}

		$feed->setLink($tikilib->tikiUrl(''));
		$feed->setFeedLink($feedLink, $feed_format_name);
		$feed->setDateModified($tikilib->now);

		if ($feed_format_name == 'atom') {
			$author = array();

			if (!empty($prefs['feed_atom_author_name'])) {
				$author['name'] = $prefs['feed_atom_author_name'];
			}
			if (!empty($prefs['feed_atom_author_email'])) {
				$author['email'] = $prefs['feed_atom_author_email'];
			}
			if (!empty($prefs['feed_atom_author_url'])) {
				$author['url'] = $prefs['feed_atom_author_url'];
			}

			if (!empty($author)) {
				if (empty($author['name'])) {
					$msg = tra('If you set feed author email or URL, you must set feed author name.');
					$smarty->assign('msg', $msg);
					$smarty->display('error.tpl');
					die;
				}
				$feed->addAuthor($author);
			}
		} else {
			$authors = array();

			if (!empty($prefs['feed_rss_editor_email'])) {
				$authors['name'] = $prefs['feed_rss_editor_email'];
			}
			if (!empty($prefs['feed_rss_webmaster_email'])) {
				$authors['name'] = $prefs['feed_rss_webmaster_email'];
			}

			if (!empty($authors)) {
				$feed->addAuthors($authors);
			}
		}

		if (!empty($prefs['feed_img'])) {
			$image = array();
			$image['uri'] = $tikilib->tikiUrl($prefs['feed_img']);
			$image['title'] = tra('Feed logo');
			$image['link'] = $tikilib->tikiUrl('');
			$feed->setImage($image);
		}

		foreach ($changes["data"] as $data) {
			$item = $feed->createEntry(); 
			$item->setTitle($data[$titleId]); 

			if (isset($data['sefurl'])) {
				$item->setLink($URLPrefix.$data['sefurl']);
			} elseif ($urlparam != '') {			// 2 parameters to replace
				$item->setlink(sprintf($read, urlencode($data["$id"]), urlencode($data["$urlparam"])));
			} else {
				$item->setLink(sprintf($read, urlencode($data["$id"])));
			}

			if (isset($data[$descId]) && $data[$descId] != '') {
				$item->setDescription($data[$descId]); 
			}

			$item->setDateCreated($data[$dateId]); 
			$item->setDateModified($data[$dateId]); 

			if ($authorId != '' && $prefs['feed_'.$section.'_showAuthor'] == 'y') {
				$author = $this->process_item_author($data[$authorId]);
				$item->addAuthor($author);
			}

			$feed->addEntry($item); 
		}

		$data = $feed->export($feed_format_name);
		$this->put_to_cache($uniqueid, $feed_format, $data);

		$output = array();
		$output["data"] = $data;
		$output["content-type"] = 'application/xml';

		return $output;
	}

	/**
	 * Return information about the user acording to its preferences
	 *
	 * @param string $login
	 * @return array author data (can be the login name or the realName if set and email if public)
	 */
	function process_item_author($login)
	{
		global $userlib, $tikilib;

		$author = array();

		if ($userlib->user_exists($login) && $tikilib->get_user_preference($login, 'user_information', 'private') == 'public') {
			// if realName is not set use $login
			$author['name'] = $tikilib->get_user_preference($login, 'realName', $login);

			if ($tikilib->get_user_preference($login, 'email is public', 'n') != 'n') {
				$res = $userlib->get_user_info($login, false);
				require_once('lib/userprefs/scrambleEmail.php');
				$author['email'] = scrambleEmail($res['email']);
			}
		} else {
			$author['name'] = $login;
		}

		return $author;
	}

	// --------------------------------------------
	// functions for rss feeds syndicated by others
	// --------------------------------------------

	/* get (a part of) the list of existing rss feeds from db */
	function list_rss_modules($offset, $maxRecords, $sort_mode, $find)
	{
		$conditions = array();
		if ($find) {
			$conditions['search'] = $this->modules->expr('(`name` LIKE ? OR `description` LIKE ?)', array("%$find%", "%$find%"));
		}

		$ret = $this->modules->fetchAll($this->modules->all(), $conditions, -1, -1, $this->modules->sortMode($sort_mode));

		foreach ($ret as & $res) {
			$res["minutes"] = $res["refresh"] / 60;
		}

		return array(
				'data' => $ret,
				'cant' => count($ret),
				);
	}

	/* replace rss feed in db */
	function replace_rss_module($rssId, $name, $description, $url, $refresh, $showTitle, $showPubDate, $noUpdate = false)
	{
		//if ($this->rss_module_name_exists($name)) return false; // TODO: Check the name
		$refresh = 60 * $refresh;

		$data = array(
				'name' => $name,
				'description' => $description,
				'refresh' => $refresh,
				'url' => $url,
				'showTitle' => $showTitle,
				'showPubDate' => $showPubDate,
				);

		if ($rssId) {
			$this->modules->update($data, array('rssId' => (int) $rssId,));
		} else {
			$data['lastUpdated'] = 1;
			$rssId = $this->modules->insert($data);
		}

		if (!$noUpdate) {
			// Updating is normally required, except for cases where we know it will be updated later (e.g. after article generation is set, so that articles are created immediately) 
			$this->update_feeds(array($rssId), true);
		}
		return $rssId;
	}

	/* remove rss feed from db */
	function remove_rss_module($rssId)
	{
		$this->modules->delete(array('rssId' => $rssId,));

		return true;
	}

	/* read rss feed data from db */
	function get_rss_module($rssId)
	{
		return $this->modules->fetchFullRow(array('rssId' => $rssId));
	}

	function refresh_rss_module($rssId)
	{
		$this->update_feeds(array( $rssId ), true);
	}

	function clear_rss_cache($rssId)
	{
		$this->items->deleteMultiple(array('rssId' => (int) $rssId));
	}

	/* check if an rss feed name already exists */
	function rss_module_name_exists($name)
	{
		return $this->modules->fetchCount(array('name' => $name));
	}

	/* get rss feed id by name */
	function get_rss_module_id($name)
	{
		return $this->modules->fetchOne('rssId', array('name' => $name));
	}

	/* check if 'showTitle' for an rss feed is enabled */
	function get_rss_showTitle($rssId)
	{
		return $this->modules->fetchOne('showTitle', array('rssId' => $rssId));
	}

	/* check if 'showPubdate' for an rss feed is enabled */
	function get_rss_showPubDate($rssId)
	{
		return $this->modules->fetchOne('showPubDate', array('rssId' => $rssId));
	}

	function get_feed_items( $feeds, $count = 10 )
	{
		$feeds = (array) $feeds;

		$this->update_feeds($feeds);

		return $this->items->fetchAll(
						$this->items->all(), 
						array('rssId' => $this->items->in($feeds),), 
						$count, 
						0, 
						array('publication_date' => 'DESC')
		);
	}

	private function update_feeds( $feeds, $force = false )
	{
		global $tikilib;

		$conditions = array('rssId' => $this->modules->in($feeds),);

		if ( ! $force ) {
			$conditions['date'] = $this->modules->expr('`lastUpdated` < ? - `refresh`', array($tikilib->now));
		}

		$result = $this->modules->fetchAll(array('rssId', 'url', 'actions'), $conditions);

		foreach ( $result as $row ) {
			$this->update_feed($row['rssId'], $row['url'], $row['actions']);
		}
	}

	private function update_feed( $rssId, $url, $actions )
	{
		global $tikilib;

		$filter = new DeclFilter;
		$filter->addStaticKeyFilters(
						array(
							'url' => 'url',
							'title' => 'striptags',
							'author' => 'striptags',
							'description' => 'striptags',
							'content' => 'purifier',
						)
		);

		$guidFilter = TikiFilter::get('url');

		try {
			$content = $tikilib->httprequest($url);
			$feed = Zend_Feed_Reader::importString($content);
		} catch( Zend_Exception $e ) {
			$this->modules->update(
							array(
								'lastUpdated' => $tikilib->now,
								'sitetitle' => 'N/A',
								'siteurl' => '#',
								), 
							array('rssId' => $rssId,)
			);
			return;
		}
		$siteTitle = TikiFilter::get('striptags')->filter($feed->getTitle());
		$siteUrl = TikiFilter::get('url')->filter($feed->getLink());

		$this->modules->update(
						array(
							'lastUpdated' => $tikilib->now,
							'sitetitle' => $siteTitle,
							'siteurl' => $siteUrl,
							), 
						array('rssId' => $rssId,)
		);

		foreach ( $feed as $entry ) { // TODO: optimize. Atom entries have an 'updated' element which can be used to only update updated entries
			$guid = $guidFilter->filter($entry->getId());

			$authors = $entry->getAuthors();

			$data = $filter->filter(
							array(
								'title' => $entry->getTitle(),
								'url' => $entry->getLink(),
								'description' => $entry->getDescription(),
								'content' => $entry->getContent(),
								'author' => $authors ? implode(', ', $authors->getValues()) : '', 
							) 
			);

			$data['guid'] = $guid;
			if ( method_exists($entry, 'getDateCreated') && $createdDate = $entry->getDateCreated() ) {
				$data['publication_date'] = $createdDate->get(Zend_Date::TIMESTAMP);
			} else {
				global $tikilib;
				$data['publication_date'] = $tikilib->now;
			}

			$count = $this->items->fetchCount(array('rssId' => $rssId, 'guid' => $guid));
			if ( 0 == $count ) {
				$this->insert_item($rssId, $data, $actions);
			} else {
				$this->update_item($rssId, $data['guid'], $data);
			}
		}
	}

	private function insert_item( $rssId, $data, $actions )
	{
		$this->items->insert(
						array(
							'rssId' => $rssId,
							'guid' => $data['guid'],
							'url' => $data['url'],
							'publication_date' => $data['publication_date'],
							'title' => $data['title'],
							'author' => $data['author'],
							'description' => $data['description'],
							'content' => $data['content'],
						)
		);

		$actions = json_decode($actions, true);

		if (!empty($actions)) {
			foreach ( $actions as $action ) {
				$method = 'process_action_' . $action['type'];
				unset( $action['type'] );

				if ( $action['active'] ) {
					$this->$method( $action, $data );
				}
			}
		}
	}

	private function update_item( $rssId, $guid, $data )
	{
		// A feed may contain several entries with the same GUID... see http://framework.zend.com/issues/browse/ZF-10954. Assuming a single record would actually cause issues, see r37318.
		$this->items->updateMultiple(
						array('rssId' => $rssId, 'guid' => $guid,), 
						array(
							'url' => $data['url'],
							'publication_date' => $data['publication_date'],
							'title' => $data['title'],
							'author' => $data['author'],
							'description' => $data['description'],
							'content' => $data['content'],
						)
		);
	}

	private function process_action_article( $configuration, $data )
	{
		$tikilib = TikiLib::lib('tiki');
		$artlib = TikiLib::lib('art');
		$publication = $data['publication_date'];

		if ( $configuration['future_publish'] > 0 ) {
			$publication = $tikilib->now + $configuration['future_publish']*60;
		}

		$expire = $publication + 3600*24*$configuration['expiry'];

		if (strpos($data['content'], trim($data['description'])) === 0 && strlen($data['description']) < 1024) {
			$data['content'] = substr($data['content'], strlen(trim($data['description'])));
		}
		$data['content'] = trim($data['content']) == '' ? $data['content'] : '~np~' . $data['content'] . '~/np~';

		if ($configuration['submission'] == true) {
			$subid = $artlib->replace_submission(
							$data['title'], 
							$data['author'], 
							$configuration['topic'], 
							'n', 
							'', 
							0, 
							'', 
							'', 
							$data['description'], 
							$data['content'], 
							$publication, 
							$expire, 
							'admin', 
							0, 
							0, 
							0, 
							$configuration['atype'], 
							'', 
							'', 
							$data['url'], 
							'', 
							'', 
							$configuration['rating']
			);

			if (count($configuration['categories'])) {
				$categlib = TikiLib::lib('categ');
				$objectId = $categlib->add_categorized_object('submission', $subid, $data['title'], $data['title'], 'tiki-edit_submission.php?subId=' . $subid);

				foreach ( $configuration['categories'] as $categId ) {
					$categlib->categorize($objectId, $categId);
				}
			}
		} else {
			$id = $artlib->replace_article(
							$data['title'], 
							$data['author'], 
							$configuration['topic'], 
							'n', 
							'', 
							0, 
							'', 
							'', 
							$data['description'], 
							$data['content'], 
							$publication, 
							$expire, 
							'admin', 
							0, 
							0, 
							0, 
							$configuration['atype'], 
							'', 
							'', 
							$data['url'], 
							'', 
							'', 
							$configuration['rating']
			);

			if (count($configuration['categories'])) {
				$categlib = TikiLib::lib('categ');
				$objectId = $categlib->add_categorized_object('article', $id, $data['title'], $data['title'], 'tiki-read_article.php?articleId=' . $id);

				foreach ( $configuration['categories'] as $categId ) {
					$categlib->categorize($objectId, $categId);
				}
			}
		}
	}

	function set_article_generator( $rssId, $configuration )
	{
		$configuration['type'] = 'article';

		$module = $this->get_rss_module($rssId);

		if ( $module['actions'] ) {
			$actions = json_decode($module['actions'], true);
		} else {
			$actions = array();
		}

		$out = array();
		foreach ( $actions as $action ) {
			if ( $action['type'] != 'article' ) {
				$out[] = $action;
			}
		}

		$out[] = $configuration;

		$this->modules->update(
						array('actions' => json_encode($out),), 
						array('rssId' => $rssId,)
		);
	}

	function get_article_generator( $rssId )
	{
		$module = $this->get_rss_module($rssId);

		if ( $module['actions'] ) {
			$actions = json_decode($module['actions'], true);
		} else {
			$actions = array();
		}

		$default = array(
				'active' => false,
				'expiry' => 365,
				'atype' => 'Article',
				'topic' => 0,
				'future_publish' => -1,
				'categories' => array(),
				'rating' => 5,
				);

		foreach ( $actions as $action ) {
			if ( $action['type'] == 'article' ) {
				unset( $action['type'] );
				return array_merge($default, $action);
			}
		}

		return $default;
	}

	function generate_feed_from_data($data, $feed_descriptor)
	{
		require_once 'lib/smarty_tiki/modifier.sefurl.php';

		$tikilib = TikiLib::lib('tiki');
		$writer = new Zend_Feed_Writer_Feed;
		$writer->setTitle($feed_descriptor['feedTitle']);
		$writer->setDescription($feed_descriptor['feedDescription']);
		$writer->setLink($tikilib->tikiUrl(''));
		$writer->setDateModified(time());

		foreach ($data as $row) {
			$titleKey = $feed_descriptor['entryTitleKey'];
			$url = $row[$feed_descriptor['entryUrlKey']];
			$title = $row[$titleKey];

			if (isset($feed_descriptor['entryObjectDescriptors'])) {
				list($typeKey, $objectKey) = $feed_descriptor['entryObjectDescriptors'];
				$object = $row[$objectKey];
				$type = $row[$typeKey];

				if (empty($url)) {
					$url = smarty_modifier_sefurl($object, $type);
				}

				if (empty($title)) {
					$title = TikiLib::lib('object')->get_title($type, $object);
				}
			}

			$entry = $writer->createEntry();
			$entry->setTitle($title ? $title : tra('Unspecified'));
			$entry->setLink($tikilib->tikiUrl($url));
			$entry->setDateModified($row[$feed_descriptor['entryModificationKey']]);

			$writer->addEntry($entry);
		}

		return $writer;
	}
}
global $rsslib;
$rsslib = new RSSLib;
