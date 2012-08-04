<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-tracker_http_request.php 39467 2012-01-12 19:47:28Z changi67 $

// TODO - refactor to ajax-services then KILME

require_once ('tiki-setup.php');
include_once ('lib/trackers/trackerlib.php');
if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once ('lib/categories/categlib.php');
	}
}
if ($prefs['feature_trackers'] != 'y') {
	die;
}
$arrayTrackerId = explode(',', $_GET["trackerIdList"]);
$arrayMandatory = explode(',', $_GET["mandatory"]);
if (isset($_GET['selected'])) $arraySelected = explode(',', utf8_encode(rawurldecode($_GET["selected"])));
$arrayFieldlist = explode(',', $_GET["fieldlist"]);
$arrayFilterfield = explode(',', $_GET["filterfield"]);
$arrayStatus = explode(',', $_GET["status"]);
$arrayItem = explode(',', $_GET['item']);
$sort_mode = 'f_' . $arrayFieldlist[0] . '_asc';
header('Cache-Control: no-cache');
header('content-type: application/x-javascript');
Perms::bulk(array( 'type' => 'tracker' ), 'object', $arrayTrackerId);


for ($index = 0, $count_arrayTrackerId = count($arrayTrackerId); $index < $count_arrayTrackerId; $index++) {
	$tikilib->get_perm_object($arrayTrackerId[$index], 'tracker');

	if (!isset($_GET['selected'])) {
		$selected = '';
		$filtervalue = utf8_encode(rawurldecode($_GET["filtervalue"]));
	} elseif (isset($_GET["filtervalue"])) {
		$selected = $arraySelected[$index];
		$filtervalue = $_GET["filtervalue"];
	}
	if (!empty($_GET['item'])) { // we want the value of field filterfield for item 
		$filtervalue = $trklib-> get_item_value($arrayTrackerId[$index], $arrayItem[$index], $arrayFilterfield[$index]);
	}
	if ($filtervalue) {
		$xfields = $trklib->list_tracker_fields($arrayTrackerId[$index], 0, -1, 'name_asc', '');
		foreach ($xfields["data"] as $idfi => $val) {
			if ($xfields["data"][$idfi]["fieldId"] == $arrayFieldlist[$index]) {
				$fid = $xfields["data"][$idfi]["fieldId"];
				$dfid = $idfi;
				break;
			}
		}
		$listfields = array();
		$listfields[$fid]['fieldId'] = $fid;
		$listfields[$fid]['type'] = $xfields["data"][$dfid]["type"];
		$listfields[$fid]['name'] = $xfields["data"][$dfid]["name"];
		$listfields[$fid]['options'] = $xfields["data"][$dfid]["options"];
		$listfields[$fid]['options_array'] = explode(',', $xfields["data"][$dfid]["options"]);
		$listfields[$fid]['isMain'] = $xfields["data"][$dfid]["isMain"];
		$listfields[$fid]['isTblVisible'] = $xfields["data"][$dfid]["isTblVisible"];
		$listfields[$fid]['isHidden'] = $xfields["data"][$dfid]["isHidden"];
		$listfields[$fid]['isSearchable'] = $xfields["data"][$dfid]["isSearchable"];
		$items = $trklib->list_items($arrayTrackerId[$index], 0, -1, $sort_mode, $listfields, $arrayFilterfield[$index], $filtervalue, $arrayStatus[$index]);
		
		$json_return = array();
		if ($arrayMandatory[$index] != 'y') {
			$json_return[] = "";		
		}
	// behaviour differ between smarty encoding and javascript encoding
		foreach ($items['data'] as $field) {
			$json_return[] = $field['field_values'][0]['value'];
		}
		global $access; include_once 'lib/tikiaccesslib.php';
		$access->output_serialized($json_return);
		
// unused from here down in tiki 7?
//		$isSelected = false;
//		for ($i = 0; $i < $items["cant"]; $i++) {
//			if ($selected == $items["data"][$i]['field_values'][0]['value']) {
//				$selbool = "true,true";
//				$isSelected = true;
//			} else {
//				$selbool = "false,false";
//			}
//			echo "tracker_dynamic_options[$index][$i+1]= new Option('" . str_replace("'", "\\'", $items["data"][$i]['field_values'][0]['value']) . "','" . str_replace("'", "\\'", $items["data"][$i]['field_values'][0]['value']) . "'," . $selbool . ");\n";
//		}
//		if ($isSelected == false && $selected != '') {
//			echo "tracker_dynamic_options[$index][$i+1]= new Option('" . $selected . "','" . $selected . "',true,true);\n";
//		}
	}
}
