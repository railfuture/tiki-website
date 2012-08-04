<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_trackeritemfield.php 41429 2012-05-11 11:44:57Z lphuberdeau $

function wikiplugin_trackeritemfield_info()
{
	return array(
		'name' => tra('Tracker Item Field'),
		'documentation' => 'PluginTrackerItemField',
		'description' => tra('Display or test the value of a tracker item field'),
		'prefs' => array( 'wikiplugin_trackeritemfield', 'feature_trackers' ),
		'body' => tra('Wiki text containing an {ELSE} marker.'),
		'icon' => 'img/icons/database_go.png',
		'filter' => 'wikicontent',
		'params' => array(
			'trackerId' => array(
				'required' => false,
				'name' => tra('Tracker ID'),
				'description' => tra('Numeric value representing the tracker ID.'),
				'filter' => 'digits',
				'default' => ''
			),
			'itemId' => array(
				'required' => false,
				'name' => tra('Item ID'),
				'description' => tra('Numeric value representing the item ID. Default is the user tracker item for the current user.'),
				'filter' => 'digits',
				'default' => ''
			),
			'fieldId' => array(
				'required' => false,
				'name' => tra('Field ID'),
				'description' => tra('Numeric value representing the field ID displayed or tested'),
				'filter' => 'digits',
				'default' => '',
			),
			'fields' => array(
				'required' => false,
				'name' => tra('Fields'),
				'description' => tra('Colon separated list of field IDs. Default is all fields'),
				'default' => '',
				'filter' => 'text',
			),
			'status' => array(
				'required' => false,
				'name' => tra('Status'),
				'description' => tra('Status of the tracker item'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Open'), 'value' => 'o'), 
					array('text' => tra('Pending'), 'value' => 'p'), 
					array('text' => tra('Closed'), 'value' => 'c'), 
					array('text' => tra('Open & Pending'), 'value' => 'op'), 
					array('text' => tra('Open & Closed'), 'value' => 'oc'), 
					array('text' => tra('Pending & Closed'), 'value' => 'pc'), 
					array('text' => tra('Open, Pending & Closed'), 'value' => 'opc')
				)
			),
			'test' => array(
				'required' => false,
				'name' => tra('Test'),
				'description' => tra('Set to 1 (Yes) to test whether a field is empty (if value parameter is empty) or has a value the same as the value parameter.'),
				'default' => '',
				'filter' => 'digits',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'value' => array(
				'required' => true,
				'name' => tra('Value'),
				'description' => tra('Value to compare against.'),
				'default' => '',
				'filter' => 'text',
			),
		),
	);
}

function wikiplugin_trackeritemfield($data, $params)
{
	global $userTracker, $group, $user, $userlib, $tiki_p_admin_trackers, $prefs, $smarty, $tikilib;
	global $trklib; include_once('lib/trackers/trackerlib.php');
	static $memoItemId = 0;
	static $memoTrackerId = 0;
	static $memoStatus = 0;
	static $memoUserTracker = false;
	static $memoItemObject = null;

	extract($params, EXTR_SKIP);

	if (empty($itemId) && !empty($_REQUEST['itemId'])) {
		if (!empty($trackerId)) {
			$info = $trklib->get_item_info($_REQUEST['itemId']);
			if (!empty($info) && $info['trackerId'] == $trackerId) {
				$itemId = $_REQUEST['itemId'];
			}
		} else {
			$itemId = $_REQUEST['itemId'];
		}
	}

	if (empty($itemId) && !empty($trackerId) && ($tracker_info = $trklib->get_tracker($trackerId))) {
		if ($t = $trklib->get_tracker_options($trackerId)) {
			$tracker_info = array_merge($tracker_info, $t);
		}
		$itemId = $trklib->get_user_item($trackerId, $tracker_info);
	}

	if ((!empty($itemId) && $memoItemId == $itemId) || (empty($itemId) && !empty($memoItemId))) {
		$itemId = $memoItemId;
		if (empty($memoTrackerId)) {
			return tra('Incorrect param');
		}
		$trackerId = $memoTrackerId;
		$itemObject = $memoItemObject;
	} else {
		if (!empty($trackerId) && !empty($_REQUEST['view_user'])) {
			$itemId = $trklib->get_user_item($trackerId, $tracker_info, $_REQUEST['view_user']);
		}
		if (empty($trackerId) && empty($itemId) && ((isset($userTracker) && $userTracker == 'y') || (isset($prefs) && $prefs['userTracker'] == 'y')) && !empty($group) && ($utid = $userlib->get_tracker_usergroup($user)) && $utid['usersTrackerId']) {
			$trackerId = $utid['usersTrackerId'];
			$itemId = $trklib->get_item_id($trackerId, $utid['usersFieldId'], $user);
		} else if (empty($trackerId) && !empty($itemId)) {
			$item = $trklib->get_tracker_item($itemId);
			$trackerId = $item['trackerId'];
		}

		if (empty($itemId) && empty($test) && empty($status)) {// need an item
			return tra('Incorrect param').': itemId';
		}

		if (!empty($status) && !$trklib->valid_status($status)) {
			return tra('Incorrect param').': status';
		}

		$info = $trklib->get_tracker_item($itemId);
		if (!empty($info) && empty($trackerId)) {
			$trackerId = $info['trackerId'];
		}

		$itemObject = Tracker_Item::fromInfo($info);
		if (! $itemObject->canView()) {
			return WikiParser_PluginOutput::error(tr('Permission denied'), tr('You are not allowed to view this item.'));
		}

		$memoStatus = $info['status'];
		$memoItemId = $itemId;
		$memoTrackerId = $info['trackerId'];
		$memoItemObject = $itemObject;
		if (isset($_REQUEST['itemId']) && $_REQUEST['itemId'] != $itemId) {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Viewed', $itemId, 'trackeritem', $_SERVER['REQUEST_URI'].'&trackeritemfield');
		}
	}
	if (!isset($data)) {
		$data = $dataelse = '';
	} elseif (!empty($data) && strpos($data, '{ELSE}')) {
		$dataelse = substr($data, strpos($data, '{ELSE}')+6);
		$data = substr($data, 0, strpos($data, '{ELSE}'));
	} else {
			$dataelse = '';
	}
	if (!empty($status)) {
		if (!strstr($status, $memoStatus)) {
			return $dataelse;
		}
	}
	if (empty($itemId) && !empty($test)) {
		return $dataelse;
	} elseif (empty($itemId)) {
		return tra('Incorrect param').': itemId';
	} elseif (isset($fields)) {
		$all_fields = $trklib->list_tracker_fields($trackerId, 0, -1);
		$all_fields = $all_fields['data'];
		if (!empty($fields)) {
			$fields = explode(':', $fields);
			foreach ($all_fields as $i=>$fopt) {
				if (!in_array($fopt['fieldId'], $fields)) {
					unset($all_fields[$i]);
				}
			}
			if (empty($all_fields)) {
				return tra('Incorrect param');
			}
		}
		$field_values = $trklib->get_item_fields($trackerId, $itemId, $all_fields, $itemUser);
		foreach ($field_values as $field_value) {
			if (($field_value['type'] == 'p' && $field_value['options_array'][0] == 'password') || ($field_value['isHidden'] != 'n' && $field_value['isHidden'] != 'c'))
				continue;

			if (! $itemObject->canViewField($field_value['fieldId'])) {
				continue;
			}

			if (empty($field_value['value'])) {
				return $dataelse;
			}
		}
	} elseif (!empty($fieldId)) {

		if (!($field = $trklib->get_tracker_field($fieldId))) {
			return tra('Incorrect param').': fieldId';
		}

		if (! $itemObject->canViewField($fieldId)) {
			return WikiParser_PluginOutput::error(tr('Permission denied'), tr('You are not allowed to view this field.'));
		}

		if (empty($test))
			$test = false;

		if (($val = $trklib->get_item_value($trackerId, $itemId, $fieldId)) !== false) {
			if ($test) { 
				if (!empty($value) && $val != $value) {
					return $dataelse;
				}
				return $data;
			} else {
				$field['value'] = $val;
				$handler = $trklib->get_field_handler($field, $info);	// gets the handler to blend back the value into the definitions array
				$out = $handler->renderOutput(array('showlinks'=>'n'));

				return $out;
			}
		} elseif ($test) { // testing the value of a field that does not exist yet
			return $dataelse;
		}
	}
	return $data;
}
