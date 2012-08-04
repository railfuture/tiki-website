<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: include_areas.php 40161 2012-03-13 20:49:26Z pkdille $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

require_once('lib/perspective/binderlib.php');
global $areaslib;

// updating table tiki_areas
if (isset($_REQUEST['update_areas'])) {
	check_ticket('admin-inc-areas');
	$pass = $areaslib->update_areas();
	if ($pass !== true)
		$smarty->assign_by_ref('error', $pass);
}

// building overview
$areas_table = $areaslib->table('tiki_areas');
$conditions = array();

// if count zero, table probably not up-to-date
if ($areas_table->fetchCount($conditions) == 0)
	$areaslib->update_areas();

$result = $areas_table->fetchAll(array('categId', 'perspectives'), $conditions);
$areas = array();
$perspectives = array();

foreach ($result as $item) {
	$area = array();
	$area['categId'] = $item['categId'];
	$area['perspectives'] = array();
	foreach (unserialize($item['perspectives']) as $pers) {
		if (!array_key_exists($pers, $perspectives))
			$perspectives[$pers] = $perspectivelib->get_perspective($pers);

		$area['perspectives'][] = $perspectives[$pers];
	}
	$area['categName'] = $areaslib->get_category_name($item['categId']);
	$area['description'] = $areaslib->get_category_description($item['categId']);
	$areas[] = $area;
}

$no_area = (count($areas)) ? 0 : 1;
$smarty->assign('no_area', $no_area);
$smarty->assign_by_ref('areas', $areas);

ask_ticket('admin-inc-areas');
