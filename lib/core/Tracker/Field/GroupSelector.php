<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: GroupSelector.php 40123 2012-03-11 13:11:24Z sylvieg $

/**
 * Handler class for GroupSelector
 * 
 * Letter key: ~g~
 *
 */
class Tracker_Field_GroupSelector extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'g' => array(
				'name' => tr('Group Selector'),
				'description' => tr('Allows a selection from a specified list of user groups.'),
				'help' => 'Group selector',				
				'prefs' => array('trackerfield_groupselector'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'autoassign' => array(
						'name' => tr('Auto-Assign'),
						'description' => tr('Determines if any group should be automatically assigned to the field.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('None'),
							1 => tr('Creator'),
							2 => tr('Modifier'),
						),
					),
					'groupId' => array(
						'name' => tr('Group Filter'),
						'description' => tr('Limit listed groups to those including the specified group.'),
						'filter' => 'int',
					),
					'assign' => array(
						'name' => tr('Assign to the group'),
						'description' => tr('For no autoaassigned field, the user (user selector if exsits or user) will be assigned to the group and it will be his default group. The group must have the user choice setting on.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('None'),
							1 => tr('Assign'),
						),
						'default' => 0,
					)
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $tiki_p_admin_trackers, $group;
		
		$ins_id = $this->getInsertId();

		$data = array();
		
		$groupId = $this->getOption(1);
		if (empty($groupId)) {
			$data['list'] = TikiLib::lib('user')->list_all_groups();
		} else {
			$group_info = TikiLib::lib('user')->get_groupId_info($groupId);
			$data['list'] =	TikiLib::lib('user')->get_including_groups($group_info['groupName']);
		}

		if ( isset($requestData[$ins_id])) {
			if ($this->getOption(0) < 1 || $tiki_p_admin_trackers === 'y') {
				$data['value'] = in_array($requestData[$ins_id], $data['list'])? $requestData[$ins_id]: '';
			} else {
				if ($this->getOption(0) == 2) {
					$data['defvalue'] = $group;
					$data['value'] = $group;
				} elseif ($this->getOption(0) == 1) {
					$data['value'] = $group;
				} else {
					$data['value'] = '';
				}
			}
		} else {
			$data['defvalue'] = $group;
			$data['value'] = $this->getValue();		
		}
		
		return $data;
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/groupselector.tpl', $context);
	}

	function handleSave($value, $oldValue)
	{
		global $prefs, $user;

		if ($this->getOption(0) && is_null($oldValue)) {
			$definition = $this->getTrackerDefinition();
			if ($prefs['groupTracker'] == 'y' && $definition->isEnabled('autoCreateGroup')) {
				$value = TikiLib::lib('trk')->groupName($definition->getInformation(), $this->getItemId());
			}
		}
		if ($this->getOption(2)) {
			$creator = TikiLib::lib('trk')->get_item_creator($this->getConfiguration('trackerId'), $this->getItemId());
			if (empty($creator)) $creator = $user;
			$ginfo = TikiLib::lib('user')->get_group_info($value);
			if ($ginfo['userChoice'] == 'y') {
				TikiLib::lib('user')->assign_user_to_group($creator, $value);
				TikiLib::lib('user')->set_default_group($creator, $value);
			}
		}

		return array(
			'value' => $value,
		);
	}
}

