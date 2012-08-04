<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Rating.php 40304 2012-03-22 18:17:06Z jonnybradley $

class Tracker_Field_Rating extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'STARS' => array(
				'name' => tr('Rating'),
				'description' => tr('A rating of the tracker item'),
				'readonly' => true,
				'help' => 'Rating Tracker Field',
				'prefs' => array('trackerfield_rating'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'option' => array(
						'name' => tr('Option'),
						'description' => tr('The possible options (comma separated integers) for the rating.'),
						'filter' => 'int',
						'count' => '*', 
					),
					'mode' => array(
						'name' => tr('Mode'),
						'description' => tr('Display rating options as:'),
						'filter' => 'text',
						'options' => array(
							'stars' => tr('Stars'),
							'radio' => tr('Radio Buttons'),
							'like' => tr('Single Option: e.g. Like'),
						), 
					),
					'labels' => array(
						'name' => tr('Labels'),
						'description' => tr('The text labels (comma separated) for the possible options.'),
						'filter' => 'text',
						'count' => '*',
					),	
				),
			), 
			'*' => array(
				'name' => tr('Stars (deprecated)'),
				'description' => tr('Displays a star rating'),
				'readonly' => true,
				'deprecated' => true,
				'prefs' => array('trackerfield_stars'),
				'tags' => array('experimental'),
				'default' => 'n',
				'params' => array(
					'option' => array(
						'name' => tr('Option'),
						'description' => tr('A possible option for the rating.'),
						'filter' => 'int',
						'count' => '*',
					),
				),
			),
			's' => array(
				'name' => tr('Stars (system)'),
				'description' => tr('Displays a star rating'),
				'readonly' => true,
				'deprecated' => true,
				'prefs' => array('trackerfield_starsystem'),
				'tags' => array('experimental'),
				'default' => 'n',
				'params' => array(
					'option' => array(
						'name' => tr('Option'),
						'description' => tr('A possible option for the rating.'),
						'filter' => 'int',
						'count' => '*',
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		if (isset($requestData['vote']) && isset($requestData['itemId'])) {
			$trklib = TikiLib::lib('trk');
			$data = $this->getBaseFieldData();
			global $user;
			$result = $trklib->replace_star($requestData[$ins_id], $this->getConfiguration('trackerId'), $requestData['itemId'], $data, $user, true);
		} else {
			$data = $this->gatherVoteData();
		}

		return array(
			'my_rate' => $data['my_rate'],
			'numvotes' => empty($data['numvotes']) ? 0 : $data['numvotes'],
			'voteavg' => empty($data['voteavg']) ? 0 : $data['voteavg'],
			'request_rate' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: null,
			'value' => $data['value'],
			'mode' => $data['mode'],
			'labels' => $data['labels_array'],      
			'rating_options' => $data['rating_options'],
			'result' => $result,
		);
	}

	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/rating.tpl', $context);
	}

	function renderInput($context = array())
	{
		if ($this->getConfiguration('type') == 's') {
			return $this->renderTemplate('trackerinput/rating.tpl', $context);
		} else {
			$data = $this->gatherVoteData();
			$str = tra("Number of votes:") . ' ' .$data['numvotes'] . ', ' . tra('Average:') . ' ' . $data['voteavg'];
			if (!empty($data['my_rate'])) {
				$str .= ' (' . tra("Your rating:")  . ' ' . $data['my_rate'] . ')';
			}
			return $str;
		}
	}

	function getDocumentPart($baseKey, Search_Type_Factory_Interface $typeFactory)
	{
		$data = $this->gatherVoteData();

		return array(
			$baseKey => $typeFactory->sortable($data['voteavg']),
			"{$baseKey}_count" => $typeFactory->sortable($data['numvotes']),
			"{$baseKey}_sum" => $typeFactory->sortable($data['total']),
		);
	}

	function getProvidedFields($baseKey)
	{
		return array(
			$baseKey,
			"{$baseKey}_count",
			"{$baseKey}_sum",
		);
	}

	function getGlobalFields($baseKey)
	{
		return array();
	}

	private function getBaseFieldData()
	{
		global $user;

		$mode = 'stars'; // default is stars for legacy reasons

		$options_array = $this->getConfiguration('options_array');
		foreach ($options_array as $k => $v) {
			if (!is_numeric($v)) {
				$mode = $v;
				$labelstartkey = $k + 1;
				$rating_option_num = $k;
				break;
			}
		}
		if ($mode == 'stars') {
			$labels_array = array();
		} else { 
			for ($i = $labelstartkey, $count_options_array = count($options_array); $i < $count_options_array; $i++) {
				$labels_array[] = $options_array[$i]; 
			} 
		}
		if ($mode == 'like') {
			$rating_options = array(0,1);
		} elseif (isset($rating_option_num)) {
			$rating_options = array_slice($options_array, 0, $rating_option_num);
		} else {
			$rating_options = $options_array;
		}

		return array(
			'fieldId' => $this->getConfiguration('fieldId'),
			'type' => $this->getConfiguration('type'),
			'name' => $this->getConfiguration('name'),
			'value' => $this->getValue(), 
			'options_array' => $options_array,
			'rating_options' => $rating_options,
			'labels_array' => $labels_array,
			'mode' => $mode,
		);
	}

	private function gatherVoteData()
	{
		global $user;
		$field = $this->getBaseFieldData();
		$trackerId = $this->getConfiguration('trackerId');
		$itemId = $this->getItemId();

		$votings = TikiDb::get()->table('tiki_user_votings');

		if ($field['type'] == 's' && $field['name'] == tra('Rating')) { // global rating to an item - value is the sum of the votes
			$key = 'tracker.'.$trackerId.'.'.$itemId; 
		} elseif ($field['type'] == '*' || $field['type'] == 'STARS') { // field rating - value is the average of the votes
			$key = "tracker.$trackerId.$itemId.".$field['fieldId']; 
		}

		$data = $votings->fetchRow(
						array(
							'count' => $votings->count(),
							'total' => $votings->sum('optionId'),
						), 
						array('id' => $key)
		);

		$field['numvotes'] = $data['count'];
		$field['total'] = $data['total']; 
		if ($field['numvotes']) {
			$field['voteavg'] = round($field['total'] / $field['numvotes'], 2);
		} else {
			$field['voteavg'] = 0;
		}
		// be careful optionId is the value - not the optionId
		$field['my_rate'] = $votings->fetchOne('optionId', array('id' => $key, 'user' => $user));

		return $field;
	}
}

