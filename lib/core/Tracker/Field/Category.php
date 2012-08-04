<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Category.php 40234 2012-03-17 19:17:41Z changi67 $

/**
 * Handler class for Category
 * 
 * Letter key: ~e~
 *
 */
class Tracker_Field_Category extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'e' => array(
				'name' => tr('Category'),
				'description' => tr('Allows for one or multiple categories under the specified main category to be affected to the tracker item.'),
				'help' => 'Category Tracker Field',					
				'prefs' => array('trackerfield_category', 'feature_categories'),
				'tags' => array('advanced'),
				'default' => 'y',
				'params' => array(
					'parentId' => array(
						'name' => tr('Parent Category'),
						'description' => tr('Child categories will be provided as options for the field.'),
						'filter' => 'int',
					),
					'inputtype' => array(
						'name' => tr('Input Type'),
						'description' => tr('User interface control to be used.'),
						'default' => 'd',
						'filter' => 'alpha',
						'options' => array(
							'd' => tr('Drop Down'),
							'radio' => tr('Radio buttons'),
							'm' => tr('List box'),
							'checkbox' => tr('Multiple-selection check-boxes'),
						),
					),
					'selectall' => array(
						'name' => tr('Select All'),
						'description' => tr('Includes a control to select all available options for multi-selection controls.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No controls'),
							1 => tr('Include controls'),
						),
					),
					'descendants' => array(
						'name' => tr('All descendants'),
						'description' => tr('Display all descendants instead of only first-level ones'),
						'filter' => 'int',
						'options' => array(
							0 => tr('First level only'),
							1 => tr('All descendants'),
						),
					),
					'help' => array(
						'name' => tr('Help'),
						'description' => tr('Displays the field description in a help tooltip.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No help'),
							1 => tr('Tooltip'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$key = 'ins_' . $this->getConfiguration('fieldId');
		$parentId = $this->getOption(0);

		if (isset($requestData[$key]) && is_array($requestData[$key])) {
			$selected = $requestData[$key];
		} elseif ($this->getItemId() && empty($requestData)) {
			// only show existing category of not receiving request, otherwise might be uncategorization in progress
			$selected = $this->getCategories();
		} else {
			$selected = TikiLib::lib('categ')->get_default_categories();
		}

		$categories = $this->getApplicableCategories();
		$selected = array_intersect($selected, $this->getIds($categories));

		$data = array(
			'value' => implode(',', $selected),
			'selected_categories' => $selected,
			'list' => $categories,
		);

		return $data;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/category.tpl', $context);
	}

	function renderInnerOutput($context = array())
	{
		$selected_categories = $this->getConfiguration('selected_categories');
		$categories = $this->getConfiguration('list');
		$ret = array();
		foreach ($selected_categories as $categId) {
			foreach ($categories as $category) {
				if ($category['categId'] == $categId) {
					$ret[] = $category['name'];
					break;
				}
			}
		}
		return implode('<br/>', $ret);
	}

	function handleSave($value, $oldValue)
	{
		return array(
			'value' => $value,
		);
	}

	function watchCompare($old, $new)
	{
		$old = array_filter(explode(',', $old));
		$new = array_filter(explode(',', $new));

		$output = $this->getConfiguration('name') . ":\n";

		$new_categs = array_diff($new, $old);
		$del_categs = array_diff($old, $new);
		$remain_categs = array_diff($new, $new_categs);

		if (count($new_categs) > 0) {
			$output .= "  -[Added]-:\n";
			$output .= $this->describeCategoryList($new_categs);
		}
		if (count($del_categs) > 0) {
			$output .= "  -[Removed]-:\n";
			$output .= $this->describeCategoryList($del_categs);
		}
		if (count($remain_categs) > 0) {
			$output .= "  -[Remaining]-:\n";
			$output .= $this->describeCategoryList($remain_categs);
		}

		return $output;
	}
	
	private function describeCategoryList($categs) 
	{
	    $categlib = TikiLib::lib('categ');
	    $res = '';
	    foreach ($categs as $cid) {
			$info = $categlib->get_category($cid);
			$res .= '    ' . $info['name'] . "\n";
	    }
	    return $res;
	}

	private function getIds($categories)
	{
		$validIds = array();
		foreach ($categories as $c) {
			$validIds[] = $c['categId'];
		}

		return $validIds;
	}

	private function getApplicableCategories()
	{
		$parentId = (int) $this->getOption(0);
		$descends = $this->getOption(3) == 1;
		if ($parentId > 0) {
			return TikiLib::lib('categ')->getCategories(array('identifier'=>$parentId, 'type'=>$descends ? 'descendants' : 'children'));
		} else {
			return array();
		}
	}

	private function getCategories()
	{
		return TikiLib::lib('categ')->get_object_categories('trackeritem', $this->getItemId());
	}

	function importRemote($value)
	{
		return $value;
	}

	function exportRemote($value)
	{
		return $value;
	}

	function importRemoteField(array $info, array $syncInfo)
	{
		$sourceOptions = explode(',', $info['options']);
		$parentId = isset($sourceOptions[0]) ? (int) $sourceOptions[0] : 0;
		$fieldType = isset($sourceOptions[1]) ? $sourceOptions[1] : 'd';
		$desc = isset($sourceOptions[3]) ? (int) $sourceOptions[3] : 0;

		$info['options'] = $this->getRemoteCategoriesAsOptions($syncInfo, $parentId, $desc);

		if ($fieldType == 'm' || $fieldType == 'checkbox') {
			$info['type'] = 'M';
		} else {
			$info['type'] = 'd';
		}

		return $info;
	}

	private function getRemoteCategoriesAsOptions($syncInfo, $parentId, $descending)
	{
		$controller = new Services_RemoteController($syncInfo['provider'], 'category');
		$categories = $controller->list_categories(
						array(
							'parentId' => $parentId,
							'descends' => $descending,
						)
		);

		$parts = array();
		foreach ($categories as $categ) {
			$parts[] = $categ['categId'] . '=' . $categ['name'];
		}

		return implode(',', $parts);
	}
}

