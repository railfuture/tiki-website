<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: UserPreference.php 39469 2012-01-12 21:13:48Z changi67 $

/**
 * Handler class for User preference
 * 
 * Letter key: ~p~
 *
 */
class Tracker_Field_UserPreference extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'p' => array(
				'name' => tr('User Preference'),
				'description' => tr('Allows user preference changes from a tracker.'),
				'help' => 'User Preference Field',
				'prefs' => array('trackerfield_userpreference'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'type' => array(
						'name' => tr('Preference Name'),
						'description' => tr('Name of the preference to manipulate. password and email are not preferences, but are also valid values that will modify the user\'s profile.'),
						'filter' => 'word',
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();
		
		if (isset($requestData[$ins_id])) {
			$value = $requestData[$ins_id];
		} else {
			global $trklib, $userlib;
	
			$value = '';
			$itemId = $this->getItemId();
			
			if ($itemId) {
				$itemUser = $this->getTrackerDefinition()->getItemUser($itemId);
		
				if (!empty($itemUser)) {
					if ($this->getOption(0) == 'email') {
						$value = $userlib->get_user_email($itemUser);
					} else {
						$value = $userlib->get_user_preference($itemUser, $this->getOption(0));
					}
				}
			}
		}
					
		return array('value' => $value);
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/userpreference.tpl', $context);
	}
}

