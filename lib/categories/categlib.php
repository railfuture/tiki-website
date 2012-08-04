<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: categlib.php 41977 2012-06-17 14:03:23Z changi67 $

/**
 * \brief Categories support class
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $objectlib;require_once("lib/objectlib.php");

class CategLib extends ObjectLib
{

	// Returns a string representing the specified category's path.
	// The path includes all parent categories ordered from the root to the category's parent, and the category itself.
	// The string is a double colon (::) separated concatenation of category names.
	// Returns the empty string if the specified category does not exist.
	function get_category_path_string($categId)
	{
		$category = $this->get_category($categId);
		if ($category) {
			return $category['categpath'];
		} else {
			return '';
		}
	}

	/**
	 * Returns the path of the given category as a String in the format:
	 * "Root Category (TOP) > 1st Subcategory > 2nd Subcategory::..."	
	 */	
	function get_category_path_string_with_root($categId)
	{		
		$category = $this->get_category($categId);
		$tepath = array ('Top');
		foreach ($category['tepath'] as $pathelem) {
			$tepath[] = $pathelem;
		}
		return implode(" > ", $tepath);
	}
	
	// Returns false if the category is not found.
	// WARNING: permissions and the category filter are not considered.
	function get_category($categId)
	{
		if (!is_numeric($categId)) {
			throw new Exception('Invalid category identier');
		}
		$categories = $this->getCategories(array('identifier' => (int) $categId), false, false);
		return empty($categories) ? false : $categories[$categId];
	}
	
	function get_category_id($name)
	{
		$query = "select `categId` from `tiki_categories` where `name`=?";
		return $this->getOne($query, array((string)$name));
		
	
	}
	function get_category_name($categId,$real=false)
	{
	    if ( $categId==0 ) return 'Top';   
		$query = "select `name`,`parentId` from `tiki_categories` where `categId`=?";
		$result=$this->query($query, array((int) $categId));
		$res = $result->fetchRow();
		if ($real) return $res['name'];
		if (preg_match('/^Tracker ([0-9]+)$/', $res['name'])) {
		    $trackerId=preg_replace('/^Tracker ([0-9]+)$/', "$1", $res['name']);
		    return $this->getOne("select `name` from `tiki_trackers` where `trackerId`=?", array((int) $trackerId));
		}
		if (preg_match('/^Tracker Item ([0-9]+)$/', $res['name'])) {
		    global $trklib;require_once('lib/trackers/trackerlib.php');
		    $itemId=preg_replace('/^Tracker Item ([0-9]+)$/', "$1", $res['name']);
		    return $trklib->get_isMain_value(-1, $itemId);
		}
		return $res['name'];
	}
	
	// WARNING: This removes not only the specified category, but also all its descendants.
	function remove_category($categId)
	{
		global $cachelib; include_once('lib/cache/cachelib.php');

		$parentId=$this->get_category_parent($categId);
		$categoryName=$this->get_category_name($categId);
		$categoryPath=$this->get_category_path_string_with_root($categId);
		$description=$this->get_category_description($categId);

		$query = "delete from `tiki_categories` where `categId`=?";
		$result = $this->query($query, array((int) $categId));
		$query = "select `catObjectId` from `tiki_category_objects` where `categId`=?";
		$result = $this->query($query, array((int) $categId));

		while ($res = $result->fetchRow()) {
			$object = $res["catObjectId"];

			$query_cant = "select count(*) from `tiki_category_objects` where `catObjectId`=?";
			$cant = $this->getOne($query_cant, array($object));
			if ($cant <= 1) {
			$query2 = "delete from `tiki_categorized_objects` where `catObjectId`=?";
			$result2 = $this->query($query2, array($object));
			}
		}
		
		// remove any permissions assigned to this category
		$type = 'category';
		$object = $type . $categId;
		$query = "delete from `users_objectpermissions` where `objectId`=? and `objectType`=?";
		$result = $this->query($query, array(md5($object),$type));

		$query = "delete from `tiki_category_objects` where `categId`=?";
		$result = $this->query($query, array((int) $categId));
		$query = "select `categId` from `tiki_categories` where `parentId`=?";
		$result = $this->query($query, array((int) $categId));

		while ($res = $result->fetchRow()) {
			// Recursively remove the subcategory
			$this->remove_category($res["categId"]);
		}
		
		$cachelib->empty_type_cache('allcategs');
		$cachelib->empty_type_cache('fgals_perms');
	
		$values= array("categoryId"=>$categId, "categoryName"=>$categoryName, "categoryPath"=>$categoryPath,
			"description"=>$description, "parentId" => $parentId, "parentName" => $this->get_category_name($parentId),
			"action"=>"category removed");		
		$this->notify($values);

		$this->remove_category_from_watchlists($categId);
					
		return true;
	}

	// Throws an Exception if the category name conflicts
	function update_category($categId, $name, $description, $parentId)
	{
		global $cachelib; include_once('lib/cache/cachelib.php');

		$oldCategory=$this->get_category($categId);
		$oldCategoryName=$oldCategory['name'];
		$oldCategoryPath=$this->get_category_path_string_with_root($categId);
		$oldDescription=$oldCategory['description'];
		$oldParentId=$oldCategory['parentId'];
		$oldParentName=$this->get_category_name($oldParentId);

		if (($oldCategoryName != $name || $oldParentId != $parentId) && $this->exist_child_category($parentId, $name)) {
			throw new Exception(tr('A category named %0 already exists in %1.', $name, $this->get_category_name($parentId)));
		}
		
		// Make sure the description fits the column width
		if (strlen($description) > 250) {
			$description = substr($description, 0, 250);
		}

		$query = "update `tiki_categories` set `name`=?, `parentId`=?, `description`=? where `categId`=?";
		$result = $this->query($query, array($name,(int) $parentId, $description, (int) $categId));
		$cachelib->empty_type_cache('allcategs');
		$cachelib->empty_type_cache('fgals_perms');

		$values= array("categoryId"=>$categId, "categoryName"=>$name, "categoryPath"=>$this->get_category_path_string_with_root($categId),
			"description"=>$description, "parentId" => $parentId, "parentName" => $this->get_category_name($parentId),
			"action"=>"category updated","oldCategoryName"=>$oldCategoryName, "oldCategoryPath"=>$oldCategoryPath,
			"oldDescription"=>$oldDescription, "oldParentId" => $parentId, "oldParentName" => $oldParentName);			
		$this->notify($values);		
	}

	// Throws an Exception if the category name conflicts
	function add_category($parentId, $name, $description)
	{
		if ($this->exist_child_category($parentId, $name)) {
			throw new Exception(tr('A category named %0 already exists in %1.', $name, $this->get_category_name($parentId)));
		}
		global $cachelib; include_once('lib/cache/cachelib.php');
		
		// Make sure the description fits the column width
		// TODO: remove length constraint then remove this. See "Quiet truncation of data in database" thread on the development list
		if (strlen($description) > 250) {
			$description = substr($description, 0, 250);
		}

		$query = "insert into `tiki_categories`(`name`,`description`,`parentId`,`hits`) values(?,?,?,?)";
		$result = $this->query($query, array($name,$description,(int) $parentId, 0));
		$query = "select `categId` from `tiki_categories` where `name`=? and `parentId`=? order by `categId` desc";
		$id = $this->getOne($query, array($name,(int) $parentId));
		$cachelib->empty_type_cache('allcategs');
		$cachelib->empty_type_cache('fgals_perms');
		$values= array("categoryId"=>$id, "categoryName"=>$name, "categoryPath"=> $this->get_category_path_string_with_root($id),
			"description"=>$description, "parentId" => $parentId, "parentName" => $this->get_category_name($parentId),
			"action"=>"category created");		
		$this->notify($values);		 	
		return $id;
	}

	function is_categorized($type, $itemId)
	{
		if ( empty($itemId) ) return 0;

		if ( count($this->getCategories(NULL, false, false)) == 0 ) { // Optimization
			return 0;
		}

		$query = "select o.`objectId` from `tiki_categorized_objects` c, `tiki_objects` o, `tiki_category_objects` tco where c.`catObjectId`=o.`objectId` and o.`type`=? and o.`itemId`=? and tco.`catObjectId`=c.`catObjectId`";
		$bindvars = array($type,$itemId);
		settype($bindvars["1"], "string");
		$result = $this->query($query, $bindvars);

		if ( $result->numRows() ) {
			$res = $result->fetchRow();
			return $res["objectId"];
		} else {
			return 0;
		}
	}

	// $type The object's type, which has to be one of those handled by ObjectLib's add_object().
	// $checkHandled A boolean indicating whether only handled object types should be accepted when the object has no object record and no object information is given (legacy).
	// Returns the object's OID, or FALSE if the object type is not handled and $checkHandled is FALSE.
	function add_categorized_object($type, $itemId, $description = NULL, $name = NULL, $href = NULL, $checkHandled = FALSE)
	{
		$id = $this->add_object($type, $itemId, $checkHandled, $description, $name, $href);
		if ($id === FALSE) {
			return FALSE;
		}
		$query = "select `catObjectId` from `tiki_categorized_objects` where `catObjectId`=?";
		if (!$this->getOne($query, array($id))) {
			$query = "insert into `tiki_categorized_objects` (`catObjectId`) values (?)";
			$this->query($query, array($id));
			
			global $cachelib; include_once('lib/cache/cachelib.php');
			$cachelib->empty_type_cache('allcategs');
			$cachelib->empty_type_cache('fgals_perms');
		}
		return $id;
	}

	function categorize($catObjectId, $categId)
	{
		global $prefs;
		if (empty($categId)) {
			return;
		}
		$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId`=?";
		$result = $this->query($query, array((int) $catObjectId,(int) $categId), -1, -1, false);
	        
		$query = "insert into `tiki_category_objects`(`catObjectId`,`categId`) values(?,?)";
		$result = $this->query($query, array((int) $catObjectId,(int) $categId));

		global $cachelib;
		$cachelib->empty_type_cache("allcategs");
		$info = TikiLib::lib('object')->get_object_via_objectid($catObjectId);
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Categorized', $info['itemId'], $info['type'], "categId=$categId");
		}
		require_once 'lib/search/refresh-functions.php';
		refresh_index($info['type'], $info['itemId']);
	}

	function uncategorize($catObjectId, $categId)
	{
		global $prefs;
		$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId`=?";
		$result = $this->query($query, array((int) $catObjectId,(int) $categId), -1, -1, false);

		global $cachelib;
		$cachelib->empty_type_cache("allcategs");
		$info = TikiLib::lib('object')->get_object_via_objectid($catObjectId);
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Uncategorized', $info['itemId'], $info['type'], "categId=$categId");
		}
		require_once 'lib/search/refresh-functions.php';
		refresh_index($info['type'], $info['itemId']);
	}

	// WARNING: This may not do what you would think from the name.
	// Returns an array of the OIDs of a set of categories.
	// $categId is an integer.
	// If $categId is 0, that set is the set of all categories.
	// If $categId is the OID of a category, that set is the set of that category and its descendants.
	function get_category_descendants($categId)
	{
		if ($categId) {
			$category = $this->get_category($categId);
			if ($category == false) return false;
			return array_merge(array($categId), $category['descendants']);
		} else {
			return $this->getCategories(NULL, false, false);
		}
	}

	function list_category_objects($categId, $offset, $maxRecords, $sort_mode='pageName_asc', $type='', $find='', $deep=false, $and=false, $filter=null)
	{
		global $userlib, $prefs;
		if ($prefs['feature_sefurl'] == 'y') {
			include_once('tiki-sefurl.php');
		}
		if ($prefs['feature_trackers'] == 'y') {
			global $trklib;require_once('lib/trackers/trackerlib.php');
		}
	    
	    // Build the condition to restrict which categories objects must be in to be returned.
		$join = '';
		if (is_array($categId) && $and) {
			$categId = $this->get_jailed($categId);
			$i = count($categId)+1;
			$bindWhere = array();
			foreach ($categId as $c) {
				if (--$i) {
					$join .= " INNER JOIN tiki_category_objects tco$i on tco$i.`catObjectId`=o.`objectId` and tco$i.`categId`=? ";
					$bindWhere[] = $c;
				}
			}
		} elseif (is_array($categId)) {
			$bindWhere = $categId;
			if ($deep) {
				foreach ($categId as $c) {
					$bindWhere = array_merge($bindWhere, $this->get_category_descendants($c));
				}				
			}

			$bindWhere = $this->get_jailed($bindWhere);
			$bindWhere[] = -1;

			$where = " AND c.`categId` IN (".str_repeat("?,", count($bindWhere)-1)."?)";
		} else {
			if ($deep) {
				$bindWhere = $this->get_category_descendants($categId);
				$bindWhere[] = $categId;
				$bindWhere = $this->get_jailed($bindWhere);
				$bindWhere[] = -1;
				$where = " AND c.`categId` IN (".str_repeat("?,", count($bindWhere)-1)."?)";
			} else {
				$bindWhere = array($categId);
				$where = ' AND c.`categId`=? ';
			}
		}

	        // Restrict results by keyword
		if ($find) {
			$findesc = '%' . $find . '%';
			$bindWhere[]=$findesc;
			$bindWhere[]=$findesc;
			$where .= " AND (`name` LIKE ? OR `description` LIKE ?)";
		} 
		if (!empty($type)) {
			if (is_array($type)) {
				$where .= ' AND `type` in ('.implode(',', array_fill(0, count($type), '?')).')';
				$bindWhere = array_merge($bindWhere, $type);
			} else {
				$where .= ' AND `type` =? ';
				$bindWhere[] = $type;
			}
		}
		if (!empty($filter['language']) && !empty($type) && ($type == 'wiki' || $type == 'wiki page' || in_array('wiki', (array)$type) || in_array('wiki page', (array)$type))) {
			$join .= 'LEFT JOIN `tiki_pages` tp ON (o.`itemId` = tp.`pageName`)';
			if (!empty($filter['language_unspecified'])) {
				$where .= ' AND (tp.`lang` IS NULL OR tp.`lang` = ? OR tp.`lang`=?)';
				$bindWhere[] = '';
			} else {
				$where .= ' AND  tp.`lang`=?';
			}
			$bindWhere[] = $filter['language'];
		}

		$bindVars = $bindWhere;

		$orderBy = '';
		if ($sort_mode) {
			if ($sort_mode != 'shuffle') {
				$orderBy = " ORDER BY ".$this->convertSortMode($sort_mode);
			}
		}

		// Fetch all results as was done before, but only do it once
		$query_cant = "SELECT DISTINCT c.*, o.* FROM `tiki_category_objects` c, `tiki_categorized_objects` co, `tiki_objects` o $join WHERE c.`catObjectId`=o.`objectId` AND o.`objectId`=co.`catObjectId` $where";
		$query = $query_cant . $orderBy;
		$result = $this->fetchAll($query, $bindVars);
		$cant = count($result);

		if ($sort_mode == 'shuffle') {
			shuffle($ret);
		}

		return $this->filter_object_list($result, $cant, $offset, $maxRecords);
	}
		
	private function filter_object_list($result, $cant, $offset, $maxRecords)
	{
		global $user, $prefs;
		$permMap = TikiLib::lib('object')->map_object_type_to_permission();
		$groupList = $this->get_user_groups($user);

		// Filter based on permissions
		$contextMap = array( 'type' => 'type', 'object' => 'itemId' );
		$contextMapMap = array_fill_keys(array_keys($permMap), $contextMap);
		$result = Perms::mixedFilter(array(), 'type', 'object', $result, $contextMapMap, $permMap);
		
		if ( $maxRecords == -1 ) {
			$maxRecords = $cant;
		}

		// Capture only the required portion
		$result = array_slice($result, $offset, $maxRecords);

		$ret = array();
		$objs = array();

		foreach ( $result as $res ) {
			if (!in_array($res['catObjectId'].'-'.$res['categId'], $objs)) { // same object and same category
				if (preg_match('/trackeritem/', $res['type'])&&$res['description']=='') {
					global $trklib; include_once('lib/trackers/trackerlib.php');
					$trackerId=preg_replace('/^.*trackerId=([0-9]+).*$/', '$1', $res['href']);
					$res['name']=$trklib->get_isMain_value($trackerId, $res['itemId']);
					$filed=$trklib->get_field_id($trackerId, "description");
					$res['description']=$trklib->get_item_value($trackerId, $res['itemId'], $filed);
					if (empty($res['description'])) {
						$res['description']=$this->getOne("select `name` from `tiki_trackers` where `trackerId`=?", array((int) $trackerId));
					}
				}
				if ($prefs['feature_sefurl'] == 'y') {
					$type = $res['type'] == 'wiki page'? 'wiki': $res['type'];
					$res['sefurl'] = filter_out_sefurl($res['href'], $type);
				}
				if (empty($res['name'])) {
					$res['name'] = '#' . $res['itemId'];
				}
				$ret[] = $res;
				$objs[] = $res['catObjectId'].'-'.$res['categId'];
			}
		}

		return array(
			"data" => $ret,
			"cant" => $cant,
		);
	}

	function list_orphan_objects($offset, $maxRecords, $sort_mode)
	{
		$orderClause = $this->convertSortMode($sort_mode);

		$common = "
			FROM
				tiki_objects
				LEFT JOIN tiki_category_objects ON objectId = catObjectId
			WHERE
				catObjectId IS NULL
			ORDER BY $orderClause
			";

		$query = "SELECT objectId catObjectId, 0 categId, type, itemId, name, href $common";
		$queryCount = "SELECT COUNT(*) $common";
		
		$result = $this->fetchAll($query, array(), $maxRecords, $offset);
		$count = $this->getOne($queryCount);

		return $this->filter_object_list($result, $count, $offset, $maxRecords);
	}

	// get the parent categories of an object
	function get_object_categories($type, $itemId, $parentId=-1, $jailed = true)
	{
		$ret = array();
		if (!$itemId)
			return $ret;
		if ($parentId == -1) {
			$query = "select `categId` from `tiki_category_objects` tco, `tiki_categorized_objects` tto, `tiki_objects` o
				where tco.`catObjectId`=tto.`catObjectId` and o.`objectId`=tto.`catObjectId` and o.`type`=? and `itemId`=?";
			//settype($itemId,"string"); //itemId is defined as varchar
			$bindvars = array("$type",$itemId);
		} else {
			$query = "select tc.`categId` from `tiki_category_objects` tco, `tiki_categorized_objects` tto, `tiki_objects` o,`tiki_categories` tc
    		where tco.`catObjectId`=tto.`catObjectId` and o.`objectId`=tto.`catObjectId` and o.`type`=? and `itemId`=? and tc.`parentId` = ? and tc.`categId`=tco.`categId`";
			$bindvars = array("$type",$itemId,(int)$parentId);
		}
		$result = $this->query($query, $bindvars);
		while ($res = $result->fetchRow()) {
			$ret[] = $res["categId"];
		}

		if ( $jailed ) {
			return $this->get_jailed($ret);
		} else {
			return $ret;
		}
	}

	// WARNING: This method is very different from get_categoryobjects()
	// Get all the objects in a category
	// filter = array('table'=>, 'join'=>, 'filter'=>, 'bindvars'=>)
	function get_category_objects($categId, $type=null, $filter = null)
	{
		$bindVars[] = (int)$categId;
		if (!empty($type)) {
			$where = ' and o.`type`=?';
			$bindVars[] = $type;
		} else {
			$where = '';
		}
		if (!empty($filter)) {
			$from = ',`'.$filter['table'].'` ft';
			$where .= ' and o.`itemId`=ft.`'.$filter['join'].'` and ft.`'.$filter['filter'].'`=?';
			$bindVars[] .= $filter['bindvars'];
		} else {
			$from = '';
		}
		$query = "select * from `tiki_category_objects` c,`tiki_categorized_objects` co, `tiki_objects` o $from where c.`catObjectId`=co.`catObjectId` and co.`catObjectId`=o.`objectId` and c.`categId`=?".$where;
		return $this->fetchAll($query, $bindVars);
	}

	// Removes the object with the given identifer from the category with the given identifier
	function remove_object_from_category($catObjectId, $categId)
	{
		$this->remove_object_from_categories($catObjectId, array($categId));
	}

	// Removes the object with the given identifer from the categories specified in the $categIds array. The array contains category identifiers.
	function remove_object_from_categories($catObjectId, $categIds)
	{
		if (!empty($categIds)) {
			global $cachelib; include_once('lib/cache/cachelib.php');
			$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId` in (".implode(',', array_fill(0, count($categIds), '?')).")";
			$result = $this->query($query, array_merge(array($catObjectId), $categIds));
			$query = "select count(*) from `tiki_category_objects` where `catObjectId`=?";
			$cant = $this->getOne($query, array((int) $catObjectId));
			if (!$cant) {
				$query = "delete from `tiki_categorized_objects` where `catObjectId`=?";
				$result = $this->query($query, array((int) $catObjectId));
			}
			$cachelib->empty_type_cache('allcategs');
			$cachelib->empty_type_cache('fgals_perms');
		}
	}

	// Categorize the object of the given type and with the given unique identifier in the categories specified in the second parameter.
	// $categIds can be a category OID or an array of category OIDs.
	// $type The object's type, which has to be one of those handled by ObjectLib's add_object().
	// Returns the object OID, or FALSE if the given type is not handled.
	function categorize_any( $type, $identifier, $categIds )
	{
		$catObjectId = $this->add_categorized_object($type, $identifier, NULL, NULL, NULL, TRUE);
		if ($catObjectId === FALSE) {
			return FALSE;
		}
		if (!is_array($categIds)) {
			$categIds = array($categIds);
		}
		foreach ($categIds as $categId) {
			$this->categorize($catObjectId, $categId);
		}

		return $catObjectId;
	}
	
	// Return an array enumerating a subtree with the given root node in preorder
	private function getSortedSubTreeNodes($root, &$categories)
	{
		global $prefs;
		$subTreeNodes = array($root);
		$childrenSubTreeNodes = array();
		foreach ($categories[$root]['children'] as $child) {
			$childrenSubTreeNodes[$categories[$child]['name']] = $this->getSortedSubTreeNodes($child, $categories);
		}
		if ($prefs['category_sort_ascii'] == 'y') {
			uksort($childrenSubTreeNodes, array("CategLib", "cmpcatname"));
		} else {
			ksort($childrenSubTreeNodes, SORT_LOCALE_STRING);
		}
		foreach ($childrenSubTreeNodes as $childSubTreeNodes) {
			$subTreeNodes = array_merge($subTreeNodes, $childSubTreeNodes);
		}
		return $subTreeNodes;
	}
	
	/* Returns an array of categories.
	Each category is similar to a tiki_categories record, but with the following additional fields:
		"children" is an array of identifiers of the categories the category has as children.
		"descendants" is an array of identifiers of the categories the category has as descendants.
		"objects" is the number of objects directly in the category.
		"tepath" is an array representing the path to the category in the category tree, ordered from the ancestor to the category. Each element is the name of the represented category. Indices are category OIDs.
		"categpath" is a string representing the path to the category in the category tree, ordered from the ancestor to the category. Each category is separated by "::". For example, "Tiki" could have categpath "Software::Free software::Tiki".
		"relativePathString" defaults to categpath.
		When and only when filtering with a filter of type "children" or "descendants", it becomes the part of "categpath" which starts from after the filtered category rather than from a root category.
		For example, if filtering descendants of category "Software", the "relativePathString" of a grandchild may be "Free Software::Tiki".
		
	By default, we start from all categories. This happens if the filter is NULL or if its type is set to "all".
	If $filter is an array with an "identifier" element or a "type" element set to "roots", starting categories are restrained.
	If the "type" element is set to "roots", start from the root categories.
	If the "type" element is unset or set to "self", start from only the designated category.
	If the "type" element is set to "children", start from the designated category's children.
	If the "type" element is set to "descendants", start from the designated category's descendants.
	In the last 3 cases, an "identifier" element must be present.
		
	If considerCategoryFilter is true, only categories that match the category filter are returned.
	If considerPermissions is true, only categories that the user has the permission to view are returned.
	If localized is enabled, category names are translated to the user's language.
	*/
	function getCategories($filter = array('type'=>'all'), $considerCategoryFilter = true, $considerPermissions = true, $localized = true)
	{
		global $cachelib, $prefs;
		$cacheKey = 'all' . ($localized ? '_' . $prefs['language'] : '');
		if ( ! $ret = $cachelib->getSerialized($cacheKey, 'allcategs') ) {
			// This generates different caches for each language. The empty key is used when no localization was requested.
			// This could be optimized, but for now each cache is generated from scratch.

			$categories = array();
			$roots = array();
			$query = "select * from `tiki_categories`";
			$result = $this->query($query, array());
			while ($res = $result->fetchRow()) {
				$id = $res["categId"];
				$query = "select count(*) from `tiki_category_objects` where `categId`=?";
				$res["objects"] = $this->getOne($query, array($id));
				$res['children'] = array();
				$res['descendants'] = array();
				if ($localized) {
					$res['name'] = tr($res['name']);
				}
				
				$categories[$id] = $res;
			}

			foreach ($categories as &$category) {
				if ($category['parentId']) {
					// Link this category from its parent.
					$categories[$category['parentId']]['children'][] = $category['categId'];
				} else {
					// Mark as a root category.
					$roots[$category['name']] = $category['categId'];
				}
				
				$path = array($category['categId'] => $category['name']);
				for ($parent = $category['parentId']; $parent != 0; $parent = $categories[$parent]['parentId']) {
					$path[$parent] = $categories[$parent]['name'];
					
					$categories[$parent]['descendants'][] = $category['categId']; // Link this category from its ascendants for optimization.
				}
				$path = array_reverse($path, true);

				$category["tepath"] = $path;
				$category["categpath"] = implode("::", $path);
				$category["relativePathString"] = $category["categpath"];
			}
			
			// Sort in preorder. Siblings are sorted by name.
			if ($prefs['category_sort_ascii'] == 'y') {
				uksort($roots, array("CategLib", "cmpcatname"));
			} else {
				ksort($roots, SORT_LOCALE_STRING);
			}	
			$sortedCategoryIdentifiers = array();
			foreach ($roots as $root) {
				$sortedCategoryIdentifiers = array_merge($sortedCategoryIdentifiers, $this->getSortedSubTreeNodes($root, $categories));
			}
			$ret = array();
			foreach ($sortedCategoryIdentifiers as $categoryIdentifier) {
				$ret[$categories[$categoryIdentifier]['categId']] = $categories[$categoryIdentifier];
			}
			unset($categories);
			
			$cachelib->cacheItem($cacheKey, serialize($ret), 'allcategs');
			$cachelib->cacheItem('roots', serialize($roots), 'allcategs'); // Used in get_category_descendants()
		}

		$type = is_null($filter) ? 'all' : (isset($filter['type']) ? $filter['type'] : 'self');
		if ($type != 'all') {
			$kept = array();
			if ($type != 'roots') {
				if (!isset($filter['identifier'])) {
					throw new Exception("Missing base category");
				}
				$filterBaseCategory = $ret[$filter['identifier']];
			}
			switch ($type) {
				case 'children':
					$kept = $filterBaseCategory['children'];
    				break;
				case 'descendants':
					$kept = $filterBaseCategory['descendants'];
    				break;
				case 'roots':
					$kept = $cachelib->getSerialized('roots', 'allcategs');
	    			break;
				default:
					$ret = array($filter['identifier'] => $filterBaseCategory); // Avoid array functions for optimization 
			}
			if ($type != 'self') {
				$ret = array_intersect_key($ret, array_flip($kept));

				if ($type != 'roots') {
					// Set relativePathString by stripping the length of the common ancestor plus 2 characters for the pathname separator ("::").
					$strippedLength = strlen($filterBaseCategory['categpath']) + 2;
					foreach ($ret as &$category) {
						$category['relativePathString'] = substr($category['categpath'], $strippedLength);
					}
				}
			}
		}
		
		if ($considerCategoryFilter) {
			if ( $jail = $this->get_jail() ) {
				$prefilter = $ret;
				$ret = array();
	
				foreach ( $prefilter as $res ) {
					if ( in_array($res['categId'], $jail)) {
						$ret[$res['categId']] = $res;
					}
				}
			}
		}
		
		if ($considerPermissions) {
			$categoryIdentifiers = array_keys($ret);
			Perms::bulk(array( 'type' => 'category' ), 'object', $categoryIdentifiers);
			foreach ($categoryIdentifiers as $categoryIdentifier) {
				$permissions = Perms::get(array( 'type' => 'category', 'object' => $categoryIdentifier));
				if (!$permissions->view_category) {
					unset($ret[$categoryIdentifier]);
				}
			}
		}
		
		return $ret;
	}

	// get categories related to a link. For Whats related module.
	function get_link_categories($link)
	{
		$ret=array();
		$parsed=parse_url($link);
		$urlPath = preg_split("#\/#", $parsed["path"]);
		$parsed["path"]=end($urlPath);
		if (!isset($parsed["query"])) return($ret);
		/* not yet used. will be used to get the "base href" of a page
		$params=array();
		$a = explode('&', $parsed["query"]);
		for ($i=0; $i < count($a);$i++) {
			$b = preg_split('/=/', $a[$i]);
			$params[htmlspecialchars(urldecode($b[0]))]=htmlspecialchars(urldecode($b[1]));
		}
		*/
		$query="select distinct co.`categId` from `tiki_objects` o, `tiki_categorized_objects` cdo, `tiki_category_objects` co  where o.`href`=? and cdo.`catObjectId`=co.`catObjectId` and o.`objectId` = cdo.`catObjectId`";
		$result=$this->query($query, array($parsed["path"]."?".$parsed["query"]));
		while ($res = $result->fetchRow()) {
		  $ret[]=$res["categId"];
		}
		return($ret);
	}

	// input is a array of category id's and return is a array of 
	// maxRows related links with description
	function get_related($categories,$maxRows=10)
	{
		global $tiki_p_admin;
		if (count($categories)==0) return (array());
		$quarr=implode(",", array_fill(0, count($categories), '?'));
		$query="select distinct o.`type`, o.`description`, o.`itemId`,o.`href` from `tiki_objects` o, `tiki_categorized_objects` cdo, `tiki_category_objects` co  where co.`categId` in (".$quarr.") and co.`catObjectId`=cdo.`catObjectId` and o.`objectId`=cdo.`catObjectId`";
		$result=$this->query($query, $categories);
		$ret=array();
		if ($tiki_p_admin != 'y')
			$permMap = TikiLib::lib('object')->map_object_type_to_permission();
		while ($res = $result->fetchRow()) {
			if ($tiki_p_admin == 'y' || $this->user_has_perm_on_object($user, $res['itemId'], $res['type'], $permMap[$res['type']])) {
				if (empty($res["description"])) {
					$ret[$res["href"]]=$res["type"].": ".$res["itemId"];
				} else {
					$ret[$res["href"]]=$res["type"].": ".$res["description"];
				}
			}
		}
		if (count($ret)>$maxRows) {
			$ret2=array();
			$rand_keys = array_rand($ret, $maxRows);
			foreach ($rand_keys as $value) {
				$ret2[$value]=$ret[$value];
			}
			return($ret2);
		}
		return($ret);
	}
	
	// combines the two functions above
	function get_link_related($link,$maxRows=10)
	{
		return ($this->get_related($this->get_link_categories($link), $maxRows));
	}
	
	// Moved from tikilib.php
	function uncategorize_object($type, $id)
	{
		$query = "select `catObjectId` from `tiki_categorized_objects` c, `tiki_objects` o where o.`objectId`=c.`catObjectId` and o.`type`=? and o.`itemId`=?";
		$catObjectId = $this->getOne($query, array((string) $type,(string) $id));

		if ($catObjectId) {
		    $query = "delete from `tiki_category_objects` where `catObjectId`=?";
		    $result = $this->query($query, array((int) $catObjectId));
			// must keep tiki_categorized object because poll or ... can use it
	    
		    // Refresh categories
		    global $cachelib; include_once('lib/cache/cachelib.php');
		    $cachelib->empty_type_cache('allcategs');
        	$cachelib->empty_type_cache('fgals_perms');
		}
	}

   	// Get a string of HTML code representing an object's category paths.
   	// $cats: The OIDs of the categories of the object.
	function get_categorypath($cats)
   	{
		global $smarty, $prefs;

		$excluded = preg_split('/,/', $prefs['categorypath_excluded']);
		$cats = array_diff($cats, $excluded);
			
		$catpath = '';
		foreach ($cats as $categId) {
			$catp = array();
			$info = $this->get_category($categId);
			if (!in_array($info['categId'], $excluded)) {
				$catp[$info['categId']] = $info['name'];
			}
			while ($info["parentId"] != 0) {
				$info = $this->get_category($info["parentId"]);
				if (!in_array($info['categId'], $excluded)) {
					$catp[$info['categId']] = $info['name'];
				}
			}

			// Hard-code a flag to hide the catpath, if no view permission is granted
			//	If set to false, the hyperlinks will be removed and pure text is displayed, if no view permission is granted
			$flHideOnNoPerm = true;

			// Check if user has permission to view the page
			$perms = Perms::get(array( 'type' => 'category', 'object' => $categId ));
			$canView = $perms->view_category;

			if ($canView || !$flHideOnNoPerm) {
				$smarty->assign('catpathCanView', $canView);
				$smarty->assign('catp', array_reverse($catp, true));
				$catpath .= $smarty->fetch('categpath.tpl');
			}

		}
		return $catpath;
	}

	// WARNING: This method is very different from get_category_objects()
	// Format a list of objects in the given categories, returning HTML code.
	function get_categoryobjects($catids,$types="*",$sort='created_desc',$split=true,$sub=false,$and=false, $maxRecords = 500, $filter=null, $displayParameters = array())
	{
		global $smarty, $prefs;

		$typetokens = array(
			"article" => "article",
			"blog" => "blog",
			"directory" => "directory",
			"faq" => "faq",
			"fgal" => "file gallery",
			"forum" => "forum",
			"igal" => "image gallery",
			"newsletter" => "newsletter",
			"poll" => "poll",
			"quiz" => "quiz",
			"survey" => "survey",
			"tracker" => "tracker",
			"wiki" => "wiki page",
			"calendar" => "calendar",
			"img" => "image"
		);	//get_strings tra("article");tra("blog");tra("directory");tra("faq");tra("file gallery");tra("forum");tra("image gallery");tra("newsletter");
			//get_strings tra("poll");tra("quiz");tra("survey");tra("tracker");tra("wiki page");tra("image");tra("calendar");
			
		$typetitles = array(
			"article" => "Articles",
			"blog" => "Blogs",
			"directory" => "Directories",
			"faq" => "FAQs",
			"file gallery" => "File Galleries",
			"forum" => "Forums",
			"image gallery" => "Image Galleries",
			"newsletter" => "Newsletters",
			"poll" => "Polls",
			"quiz" => "Quizzes",
			"survey" => "Surveys",
			"tracker" => "Trackers",
			"wiki page" => "Wiki",
			"calendar" => "Calendar",
			"image" => "Image"
		);

		$out = "";
		$listcat = $allcats = array();
		$title = '';
		$find = "";
		$offset = 0;
		$firstpassed = false;
		$typesallowed = array();
		if (!isset($displayParameters['showTitle'])) {
			$displayParameters['showTitle'] = 'y';
		}
		if (!isset($displayParameters['categoryshowlink'])) {
			$displayParameters['categoryshowlink'] = 'y';
		}
		if (!isset($displayParameters['showtype'])) {
			$displayParameters['showtype'] = 'y';
		}
		if (!isset($displayParameters['one'])) {
			$displayParameters['one'] = 'n';
		}
		if (!isset($displayParameters['showlinks'])) {
			$displayParameters['showlinks'] = 'y';
		}
		if (!isset($displayParameters['showname'])) {
			$displayParameters['showname'] = 'y';
		}
		if (!isset($displayParameters['showdescription'])) {
			$displayParameters['showdescription'] = 'n';
		}
		$smarty->assign('params', $displayParameters);
		if ($and) {
			$split = false;
		}
		if ($types == '*') {
			$typesallowed = array_keys($typetitles);
		} elseif (strpos($types, '+')) {
			$alltypes = preg_split('/\+/', $types);
			foreach ($alltypes as $t) {
				if (isset($typetokens["$t"])) {
					$typesallowed[] = $typetokens["$t"];
				} elseif (isset($typetitles["$t"])) {
					$typesallowed[] = $t;
				}
			}
		} elseif (isset($typetokens["$types"])) {
			$typesallowed = array($typetokens["$types"]);
		} elseif (isset($typetitles["$types"])) {
			$typesallowed = array($types);
		}
		$out=$smarty->fetch("categobjects_title.tpl");
		foreach ($catids as $id) {
			$titles["$id"] = $this->get_category_name($id);
			$objectcat = array();
			$objectcat = $this->list_category_objects($id, $offset, $and? -1: $maxRecords, $sort, $types == '*'? '': $typesallowed, $find, $sub, false, $filter);

			$acats = $andcat = array();
			foreach ($objectcat["data"] as $obj) {
				$type = $obj["type"];
				if (substr($type, 0, 7) == 'tracker') $type = 'tracker';
				if (($types == '*') || in_array($type, $typesallowed)) {
					if ($split or !$firstpassed) {
						$listcat["$type"][] = $obj;
						$cats[] = $type.'.'.$obj['name'];
					} elseif ($and) {
						if (in_array($type.'.'.$obj['name'], $cats)) {
							$andcat["$type"][] = $obj;
							$acats[] = $type.'.'.$obj['name'];
						}
					} else {
						if (!in_array($type.'.'.$obj['name'], $cats)) {
							$listcat["$type"][] = $obj;
							$cats[] = $type.'.'.$obj['name'];
						}
					}
				}
			}
			if ($split) {
				$smarty->assign("id", $id);
				$smarty->assign("titles", $titles);
				$smarty->assign("listcat", $listcat);
				$smarty->assign("one", count($listcat));
				$out .= $smarty->fetch("categobjects.tpl");
				$listcat = array();
				$titles = array();
				$cats = array();
			} elseif ($and and $firstpassed) {
				$listcat = $andcat;
				$cats = $acats;
			}
			$firstpassed = true;
		}
		if (!$split) {
			$smarty->assign("id", $id);
			$smarty->assign("titles", $titles);
			$smarty->assign("listcat", $listcat);
			$smarty->assign("one", count($listcat));
			$out = $smarty->fetch("categobjects.tpl");
		}
		return $out;
	}
	
	// Returns an array representing the last $maxRecords objects in the category with the given $categId of the given type, ordered by decreasing creation date. By default, objects of all types are returned.
	// Each array member is a string-indexed array with fields catObjectId, categId, type, name and href.
	function last_category_objects($categId, $maxRecords, $type="")
	{
		$mid = "and `categId`=?";
		$bindvars = array((int)$categId);
		if ($type) {
		    $mid.= " and `type`=?";
		    $bindvars[] = $type;
		}
		$sort_mode = "created_desc";
		$query = "select co.`catObjectId`, `categId`, `type`, `name`, `href` from `tiki_category_objects` co, `tiki_categorized_objects` cdo, `tiki_objects` o where co.`catObjectId`=cdo.`catObjectId` and o.`objectId`=cdo.`catObjectId` $mid order by o.".$this->convertSortMode($sort_mode);
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, 0);

		return array('data'=> $ret);
	}

	// Gets a list of categories that will block objects to be seen by user, recursive
	function list_forbidden_categories($parentId=0, $parentAllowed='', $perm='tiki_p_view_categorized')
	{
		global $user, $userlib;
		if (empty($parentAllowed)) {
		    global $tiki_p_view_categorized;
		    $parentAllowed = $tiki_p_view_categorized;
		}
	
		$query = "select `categId` from `tiki_categories` where `parentId`=?";
		$result = $this->query($query, array($parentId));
	
		$forbidden = array();

		while ($row = $result->fetchRow()) {
		    $child = $row['categId'];
		    if ($userlib->object_has_one_permission($child, 'category')) {
			if ($userlib->object_has_permission($user, $child, 'category', $perm)) {
			    $forbidden = array_merge($forbidden, $this->list_forbidden_categories($child, 'y', $perm));
			} else {
			    $forbidden[] = $child;
			    $forbidden = array_merge($forbidden, $this->list_forbidden_categories($child, 'n', $perm));
			}
		    } else {
			if ($parentAllowed != 'y') {
			    $forbidden[] = $child;
			}
			$forbidden = array_merge($forbidden, $this->list_forbidden_categories($child, $parentAllowed, $perm));
		    }
		}
		return $forbidden;
	}

	/* build the portion of list join if filter by category
	 * categId can be a simple value, a list of values=>or between categ, array('AND'=>list values) for an AND
	 */
	function getSqlJoin($categId, $objType, $sqlObj, &$fromSql, &$whereSql, &$bindVars, $type = '?')
	{
		static $callno = 0;
		$callno++;
		$fromSql .= " inner join `tiki_objects` co$callno";
		$whereSql .= " AND co$callno.`type`=$type AND co$callno.`itemId`= $sqlObj ";
		if ( $type == '?' ) {
			$bind = array($objType);
		} else {
			$bind = array();
		}
		if (isset( $categId['AND'] ) && is_array($categId['AND'])) {
			$categId['AND'] = $this->get_jailed($categId['AND']);
			$i = 0;
			foreach ($categId['AND'] as $c) {
				$fromSql .= " inner join `tiki_category_objects` t{$callno}co$i ";
				$whereSql .= " AND t{$callno}co$i.`categId`= ?  AND co$callno.`objectId`=t{$callno}co$i.`catObjectId` ";
				++$i;
			}
			$bind = array_merge($bind, $categId['AND']);
		} elseif (is_array($categId)) {
			$categId = $this->get_jailed($categId);
			$fromSql .= " inner join `tiki_category_objects` tco$callno ";
			$whereSql .= " AND co$callno.`objectId`=tco$callno.`catObjectId` ";
			$whereSql .= "AND tco$callno.`categId` IN (".implode(',', array_fill(0, count($categId), '?')).')';
			$bind = array_merge($bind, $categId);
		} else {
			$fromSql .= " inner join `tiki_category_objects` tco$callno ";
			$whereSql .= " AND co$callno.`objectId`=tco$callno.`catObjectId` ";
			$whereSql .= " AND tco$callno.`categId`= ? ";
			$bind[] = $categId;
		}
		if (is_array($bindVars))
			$bindVars = array_merge($bindVars, $bind);
		else
			$bindVars = $bind;
	} 		
	function exist_child_category($parentId, $name)
	{
		$query = 'select `categId` from `tiki_categories` where `parentId`=? and `name`=?';
		return ($this->getOne($query, array((int)$parentId, $name)));
	}

	/**
	 * Sets watch entries for the given user and category. 
	 */
	function watch_category($user, $categId, $categName)
	{
		global $tikilib;		
		if ($categId != 0) {        
			$name = $this->get_category_path_string_with_root($categId);
			$tikilib->add_user_watch(
							$user,
							'category_changed',
							$categId,
							'Category',
							$name,
							"tiki-browse_categories.php?parentId=".$categId."&deep=off"
			);		
		}	                         
	}


	/**
	 * Sets watch entries for the given user and category. Also includes
	 * all descendant categories for which the user has view permissions.
	 */
	function watch_category_and_descendants($user, $categId, $categName)
	{
		global $tikilib;
		
		if ($categId != 0) {
			$tikilib->add_user_watch(
							$user,
							'category_changed',
							$categId,
							'Category',
							$categName,
							"tiki-browse_categories.php?parentId=".$categId."&deep=off"
			);
		}
                         
		$descendants = $this->get_category_descendants($categId);
		foreach ($descendants as $descendant) {
			if ($descendant != 0 && $this->has_view_permission($user, $descendant)) {
				$name = $this->get_category_path_string_with_root($descendant);
				$tikilib->add_user_watch(
								$user,
								'category_changed',
								$descendant,
								'Category',
								$name,
								"tiki-browse_categories.php?parentId=".$descendant."&deep=off"
				);
			}
		}		
	}
	
	function group_watch_category_and_descendants($group, $categId, $categName = NULL, $top = true)
	{
		global $tikilib; 
		
		if ($categId != 0 && $top == true) {
			$tikilib->add_group_watch(
							$group,
							'category_changed',
							$categId,
							'Category',
							$categName,
							"tiki-browse_categories.php?parentId=".$categId."&deep=off"
			);
		}
		$descendants = $this->get_category_descendants($categId);
		if ($top == false) {
			$length = count($descendants);
			$descendants = array_slice($descendants, 1, $length, true);
		}		
		foreach ($descendants as $descendant) {
			if ($descendant != 0) {
				$name = $this->get_category_path_string_with_root($descendant);
				$tikilib->add_group_watch(
								$group,
								'category_changed',
								$descendant,
								'Category',
								$name, 
								"tiki-browse_categories.php?parentId=".$descendant."&deep=off"
				);
			}
		}		
	}


	/**
	 * Removes the watch entry for the given user and category.
	 */
	function unwatch_category($user, $categId)
	{
		global $tikilib;		
		
		$tikilib->remove_user_watch($user, 'category_changed', $categId, 'Category');
	}


	/**
	 * Removes the watch entry for the given user and category. Also
	 * removes all entries for the descendants of the category.
	 */
	function unwatch_category_and_descendants($user, $categId)
	{
		global $tikilib;		
		
		$tikilib->remove_user_watch($user, 'category_changed', $categId, 'Category');
		$descendants = $this->get_category_descendants($categId);
		foreach ($descendants as $descendant) {
			$tikilib->remove_user_watch($user, 'category_changed', $descendant, 'Category');
		}
	}
	
	function group_unwatch_category_and_descendants($group, $categId, $top = true)
	{
		global $tikilib;	
			
		if ($categId != 0 && $top == true) {
			$tikilib->remove_group_watch($group, 'category_changed', $categId, 'Category');
		}
		$descendants = $this->get_category_descendants($categId);
		if ($top == false) {
			$length = count($descendants);
			$descendants = array_slice($descendants, 1, $length, true);
		}		
		foreach ($descendants as $descendant) {
			if ($descendant != 0) {
				$tikilib->remove_group_watch($group, 'category_changed', $descendant, 'Category');
			}
		}
	}

	/**
	 * Removes the category from all watchlists.
	 */
	function remove_category_from_watchlists($categId)
	{
	 	$query = 'delete from `tiki_user_watches` where `object`=? and `type`=?';
	 	$this->query($query, array((int) $categId, 'Category'));
	 	$query = 'delete from `tiki_group_watches` where `object`=? and `type`=?';
	 	$this->query($query, array((int) $categId, 'Category'));
	}
	


	/**
	 * Returns the description of the category.	
	 */	
	function get_category_description($categId)
	{
		$query = "select `description` from `tiki_categories` where `categId`=?";
		return $this->getOne($query, array((int) $categId));
	}

	/**
	 * Returns the parentId of the category.	
	 */	
	function get_category_parent($categId)
	{
		$query = "select `parentId` from `tiki_categories` where `categId`=?";
		return $this->getOne($query, array((int) $categId));
	}

	/**
	 * Returns true if the given user has view permission for the category.
	 */
	function has_view_permission($user, $categoryId)
	{
		return Perms::get(array( 'type' => 'category', 'object' => $categoryId ))->view_category;
	}

	/**
	 * Returns true if the given user has edit permission for the category.
	 */
	function has_edit_permission($user, $categoryId)
	{
		global $userlib;
		return ($userlib->user_has_permission($user, 'tiki_p_admin')
				|| ($userlib->user_has_permission($user, 'tiki_p_edit') && !$userlib->object_has_one_permission($categoryId, "category"))
				|| $userlib->object_has_permission($user, $categoryId, "category", "tiki_p_edit") 
				);
	}
	
	/**
	 * Notify the users, watching this category, about changes.
	 * The Array $values contains a selection of the following items:
	 * categoryId, categoryName, categoryPath, description, parentId, parentName, action
	 * oldCategoryName, oldCategoryPath, oldDescription, oldParendId, oldParentName,
	 * objectName, objectType, objectUrl 
	 */
	function notify ($values)
	{
		global $prefs;
        
        if ($prefs['feature_user_watches'] == 'y') {        	       
			include_once('lib/notifications/notificationemaillib.php');			
          	$foo = parse_url($_SERVER["REQUEST_URI"]);          	
          	$machine = $this->httpPrefix(true). dirname($foo["path"]);          	
          	$values['event']="category_changed";          	
          	sendCategoryEmailNotification($values);          	
        }
	}

	/**
	 * Returns a categorized object.
	 */
	function get_categorized_object($cat_type, $cat_objid)
	{
	    global $objectlib;
		return $objectlib->get_object($cat_type, $cat_objid);		
	}

	/**
	 * Returns a categorized object, identified via the $cat_objid.
	 */
	function get_categorized_object_via_category_object_id($cat_objid)
	{
	    global $objectlib;
		return $objectlib->get_object_via_objectid($cat_objid);		
	}
	
	/**
	 * Returns the categories that contain the object and are in the user's watchlist.
	 */
	function get_watching_categories($objId, $objType, $user)
	{
		global $tikilib;
		
		$categories=$this->get_object_categories($objType, $objId);
		$watchedCategories=$tikilib->get_user_watches($user, "category_changed");		
		$result=array();
		foreach ($categories as $cat) {						
			foreach ($watchedCategories as $wc ) {				
				if ( $wc['object'] == $cat) {									
					$result[]=$cat;	
				}
			}			
		}
		return $result;
	}

	// Change an object's categories
	// $objId: A unique identifier of an object of the given type, for example "Foo" for Wiki page Foo.
	function update_object_categories($categories, $objId, $objType, $desc=NULL, $name=NULL, $href=NULL, $managedCategories = null, $override_perms = false)
	{
		global $prefs, $user, $userlib;
		
		if (empty($categories)) {
			$forcedcat = $userlib->get_user_group_default_category($user);
			if ( !empty($forcedcat) ) {
				$categories[] = $forcedcat;
			}
		}

		require_once 'lib/core/Category/Manipulator.php';
		$manip = new Category_Manipulator($objType, $objId);
		if ($override_perms) {
			$manip->overrideChecks();
		}
		$manip->setNewCategories($categories ? $categories : array());

		if ( is_array($managedCategories) ) {
			$manip->setManagedCategories($managedCategories);
		}

		if ($prefs['category_defaults']) {
			foreach ($prefs['category_defaults'] as $constraint ) {
				$manip->addRequiredSet($this->extentCategories($constraint['categories']), $constraint['default'], $constraint['filter'], $constraint['type']);
			}
		}

		$this->applyManipulator($manip, $objType, $objId, $desc, $name, $href);

		if ( $prefs['category_i18n_sync'] != 'n' && $prefs['feature_multilingual'] == 'y' ) {
			global $multilinguallib; require_once 'lib/multilingual/multilinguallib.php';
			$targetCategories = $this->get_object_categories($objType, $objId, -1, false);

			if ( $objType == 'wiki page' ) {
				$translations = $multilinguallib->getTranslations($objType, $this->get_page_id_from_name($objId), $objId);
				$objectIdKey = 'objName';
			} else {
				$translations = $multilinguallib->getTranslations($objType, $objId);
				$objectIdKey = 'objId';
			}
			
			$subset = $prefs['category_i18n_synced'];
			if ( is_string($subset) ) {
				$subset = unserialize($subset);
			}

			foreach ( $translations as $tr ) {
				if (!empty($tr[$objectIdKey]) && $tr[$objectIdKey] != $objId) {
					$manip = new Category_Manipulator($objType, $tr[$objectIdKey]);
					$manip->setNewCategories($targetCategories);
					$manip->overrideChecks();
	
					if ( $prefs['category_i18n_sync'] == 'whitelist' ) {
						$manip->setManagedCategories($subset);
					} elseif ( $prefs['category_i18n_sync'] == 'blacklist' ) {
						$manip->setUnmanagedCategories($subset);
					}
	
					$this->applyManipulator($manip, $objType, $tr[$objectIdKey]);
				}
			}
		}
		$this->notify_add($manip->getAddedCategories(), $name, $objType, $href);
		$this->notify_remove($manip->getRemovedCategories(), $name, $objType, $href);
	}

	function notify_add($new_categories, $name, $objType, $href)
	{
		global $prefs;
		if ($prefs['feature_user_watches'] == 'y' && !empty($new_categories)) {
			foreach ($new_categories as $categId) {			
		   		$category = $this->get_category($categId);
				$values = array('categoryId'=>$categId, 'categoryName'=>$category['name'], 'categoryPath'=>$this->get_category_path_string_with_root($categId),
					'description'=>$category['description'], 'parentId'=>$category['parentId'], 'parentName'=>$this->get_category_name($category['parentId']),
					'action'=>'object entered category', 'objectName'=>$name, 'objectType'=>$objType, 'objectUrl'=>$href);		
				$this->notify($values);								
			}
		}
	}

	function notify_remove($removed_categories, $name, $objType, $href)
	{
		global $prefs;
		if ($prefs['feature_user_watches'] == 'y' && !empty($removed_categories)) {
			foreach ($removed_categories as $categId) {
				$category = $this->get_category($categId);	
				$values= array('categoryId'=>$categId, 'categoryName'=>$category['name'], 'categoryPath'=>$this->get_category_path_string_with_root($categId),
					'description'=>$category['description'], 'parentId'=>$category['parentId'], 'parentName'=>$this->get_category_name($category['parentId']),
				 	'action'=>'object leaved category', 'objectName'=>$name, 'objectType'=>$objType, 'objectUrl'=>$href);
				$this->notify($values);								
			}
		}
	}

	private function applyManipulator( $manip, $objType, $objId, $desc=NULL, $name=NULL, $href=NULL )
	{
		$old_categories = $this->get_object_categories($objType, $objId, -1, false);
		$manip->setCurrentCategories($old_categories);

		$new_categories = $manip->getAddedCategories();
		$removed_categories = $manip->getRemovedCategories();

		if (empty($new_categories) and empty($removed_categories)) { //nothing changed
			return;
		}

		if (! $catObjectId = $this->is_categorized($objType, $objId) ) {
			$catObjectId = $this->add_categorized_object($objType, $objId, $desc, $name, $href);
		}

		global $prefs;
		if ($prefs["category_autogeocode_within"]) {
			$geocats = $this->getCategories(array('identifier'=>$prefs["category_autogeocode_within"], 'type'=>'descendants'), true, false);
		} else {
			$geocats = false;
		}

		foreach ($new_categories as $category) {
			$this->categorize($catObjectId, $category);
			// Auto geocode if feature is on
			if ($geocats) {
				foreach ($geocats as $g) {
					if ($category == $g["categId"]) {
						$geonames = explode('::', $g["name"]);
						$geonames = array_reverse($geonames);
						$geoloc = implode(',', $geonames);
						global $geolib;
						if (!is_object($geolib)) {
							include_once('lib/geo/geolib.php');
						}
						$geocode = $geolib->geocode($geoloc);
						if ($geocode) {
							global $attributelib;
							if (!is_object($attributelib)) {
								include_once('lib/attributes/attributelib.php');	
							}
							if ($prefs["category_autogeocode_replace"] != 'y') {
								$attributes = $attributelib->get_attributes($objType, $objId);
								if ( !isset($attributes['tiki.geo.lon']) || !isset($attributes['tiki.geo.lat']) ) {
									$geonotexists = true;
								}
							}
							if ($prefs["category_autogeocode_replace"] == 'y' || isset($geonotexists) && $geonotexists) {
								if ($prefs["category_autogeocode_fudge"] == 'y') {
									$geocode = $geolib->geofudge($geocode);
								}
								$attributelib->set_attribute($objType, $objId, 'tiki.geo.lon', $geocode["lon"]);
								$attributelib->set_attribute($objType, $objId, 'tiki.geo.lat', $geocode["lat"]);
								if ($objType == 'trackeritem') {
									$geolib->setTrackerGeo($objId, $geocode);
								}
							}
						}
						break;
					}
				}
			}
		}

		$this->remove_object_from_categories($catObjectId, $removed_categories);
	}

	// Returns an array of OIDs of categories.
	// These categories are those from the specified categories whose parents are not in the set of specified categories.
	// $categories: An array of categories
	function findRoots( $categories )
	{
		$candidates = array();

		foreach ( $categories as $cat ) {
			$id = $cat['parentId'];
			$candidates[$id] = true;
		}

		foreach ( $categories as $cat ) {
			unset( $candidates[ $cat['categId'] ] );
		}

		return array_keys($candidates);
	}

	function get_jailed( $categories )
	{
		if ( $jail = $this->get_jail() ) {
			return array_values(array_intersect($categories, $jail));
		} else {
			return $categories;
		}
	}

	// Returns the categories a new object should be in by default, that is none in general, or the perspective categories if the user is in a perspective.
	function get_default_categories()
	{
		global $prefs;
		if ( $this->get_jail() ) {
			// Default categories are not the entire jail including the sub-categories but only the "root" categories
			return is_array($prefs['category_jail'])? $prefs['category_jail']: array($prefs['category_jail']);
		} else {
			return array();
		}
	}

	// Returns an array containing the ids of the passed $objects present in any of the passed $categories.
	function filter_objects_categories($objects, $categories)
	{
		$query="SELECT `catObjectId` from `tiki_category_objects` where `catObjectId` in (".implode(',', array_fill(0, count($objects), '?')).")";				
		if ($categories) {
			$query .= " and `categId` in (".implode(',', array_fill(0, count($categories), '?')).")";
		}	
		$result = $this->query($query, array_merge($objects, $categories));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[]=$res["catObjectId"];
		}
		return $ret;
	}
	// unassign all objects from a category
	function unassign_all_objects($categId)
	{
		$query = 'delete from  `tiki_category_objects` where `categId`=?';
		$this->query($query, array((int)$categId));
	}
	//move all objects from a categ to anotehr one
	function move_all_objects($from, $to)
	{
		$query = 'update ignore `tiki_category_objects` set `categId`=? where `categId`=?';
		$this->query($query, array((int)$to, (int)$from));
	}
	//assign all objects of a categ to another one
	function assign_all_objects($from, $to)
	{
		$query = 'insert ignore `tiki_category_objects` (`catObjectId`, `categId`) select `catObjectId`, ? from `tiki_category_objects` where `categId`=?';
		$this->query($query, array((int)$to, (int)$from));
	}
	// generate category tree for use in various places (like categorize_list.php)
	function generate_cat_tree($categories, $canchangeall = false, $forceincat = null)
	{
		global $smarty;
		include_once ('lib/tree/BrowseTreeMaker.php');
		$tree_nodes = array();
		$roots = $this->findRoots($categories);
		foreach ($categories as $c) {
			if (isset($c['name']) || $c['parentId'] != 0) {
				// if used for purposes such as find, should be able to "change" all cats
				if ($canchangeall) {
					$c['canchange'] = true;
				}
				
				// if used in find, should force incat to check those that have been selected
				if (is_array($forceincat)) {
					$c['incat'] = in_array($c['categId'], $forceincat) ? 'y' : 'n';
				}
				
				$smarty->assign('category_data', $c);
				$tree_nodes[] = array(
					'id' => $c['categId'],
					'parent' => $c['parentId'],
					'data' => $smarty->fetch('category_tree_entry.tpl'),
				);
			}
		}
		$tm = new BrowseTreeMaker("categorize");
		$res = '';
		foreach ( $roots as $root ) {
			$res .= $tm->make_tree($root, $tree_nodes);
		}
		return $res;
	}

	static function cmpcatname($a, $b)
	{
		$a = TikiLib::strtoupper(TikiLib::take_away_accent($a));
		$b = TikiLib::strtoupper(TikiLib::take_away_accent($b));
		return strcmp($a, $b);
	}

	/* replace each *i in the categories array with the categories of the sudtree i + i */
	function extentCategories($categories)
	{
		$ret = array();
		foreach ($categories as $cat) {
			if (is_numeric($cat)) {
				$ret[] = $cat;
			} else {
				$cats = $this->get_category_descendants(substr($cat, 1));
				$ret[] = substr($cat, 1);
				$ret = array_merge($ret, $cats);
			}
		}
		$ret = array_unique($ret);
		return $ret;
	}
}
$categlib = new CategLib;
