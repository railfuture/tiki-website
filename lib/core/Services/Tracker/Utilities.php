<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Utilities.php 42005 2012-06-19 18:10:00Z robertplummer $

class Services_Tracker_Utilities
{
	function insertItem($definition, $item)
	{
		$newItem = $this->replaceItem($definition, 0, $item['status'], $item['fields']);

		return $newItem;
	}

	function updateItem($definition, $item)
	{
		$this->replaceItem($definition, $item['itemId'], $item['status'], $item['fields']);
	}

	private function replaceItem($definition, $itemId, $status, $fieldMap)
	{
		$trackerId = $definition->getConfiguration('trackerId');
		$fields = array();

		$factory = $definition->getFieldFactory();
		foreach ($fieldMap as $key => $value) {
			if (preg_match('/ins_/',$key)) { //make compatible with the 'ins_' keys
				$id = (int)str_replace('ins_', '', $key);
				$field = $definition->getField($id);
				$field['value'] = $value;
				$fields[$field['fieldId']] = $field;
			} else if ($field = $definition->getFieldFromPermName($key)) {
				$field['value'] = $value;
				$fields[$field['fieldId']] = $field;
			}
		}

		$trklib = TikiLib::lib('trk');
		$categorizedFields = $definition->getCategorizedFields();
		$errors = $trklib->check_field_values(array('data' => $fields), $categorizedFields, $trackerId, $itemId ? $itemId : '');

		if (count($errors['err_mandatory']) == 0 && count($errors['err_value']) == 0) {
			$newItem = $trklib->replace_item($trackerId, $itemId, array('data' => $fields), $status, 0, true);
			return $newItem;
		}

		$errorreportlib = TikiLib::lib('errorreport');
		if ($errors['err_mandatory'] > 0) {
			$names = array();
			foreach ($errors['err_mandatory'] as $f) {
				$names[] = $f['name'];
			}

			$errorreportlib->report(tr('Following mandatory fields are missing: %0', implode(', ', $names)));
		}

		foreach ($errors['err_value'] as $f) {
			if (! empty($f['errorMsg'])) {
				$errorreportlib->report(tr('Invalid value in %0: %1', $f['name'], $f['errorMsg']));
			} else {
				$errorreportlib->report(tr('Invalid value in %0', $f['name']));
			}
		}

		return false;
	}

	function createField(array $data)
	{
		$definition = Tracker_Definition::get($data['trackerId']);

		$isFirst = 0 === count($definition->getFields());

		$trklib = TikiLib::lib('trk');
		return $trklib->replace_tracker_field(
						$data['trackerId'],
						0,
						$data['name'],
						$data['type'],
						($isFirst ? 'y' : 'n'),
						'n',
						($isFirst ? 'y' : 'n'),
						'y',
						isset($data['isHidden']) ? $data['isHidden'] : 'n',
						isset($data['isMandatory']) ? ($data['isMandatory'] ? 'y' : 'n') : ($isFirst ? 'y' : 'n'),
						$trklib->get_last_position($data['trackerId']) + 10,
						isset($data['options']) ? $data['options'] : '',
						$data['description'],
						'',
						null,
						'',
						null,
						null,
						$data['descriptionIsParsed'] ? 'y' : 'n',
						'',
						'',
						'',
						$data['permName']
		);
	}

	function updateField($trackerId, $fieldId, array $properties)
	{
		$definition = Tracker_Definition::get($trackerId);

		$field = $definition->getField($fieldId);
		$trklib = TikiLib::lib('trk');
		$trklib->replace_tracker_field(
						$trackerId,
						$fieldId,
						isset($properties['name']) ? $properties['name'] : $field['name'],
						isset($properties['type']) ? $properties['type'] : $field['type'],
						isset($properties['isMain']) ? $properties['isMain'] : $field['isMain'],
						isset($properties['isSearchable']) ? $properties['isSearchable'] : $field['isSearchable'],
						isset($properties['isTblVisible']) ? $properties['isTblVisible'] : $field['isTblVisible'],
						isset($properties['isPublic']) ? $properties['isPublic'] : $field['isPublic'],
						isset($properties['isHidden']) ? $properties['isHidden'] : $field['isHidden'],
						isset($properties['isMandatory']) ? $properties['isMandatory'] : $field['isMandatory'],
						isset($properties['position']) ? $properties['position'] : $field['position'],
						isset($properties['options']) ? $properties['options'] : $field['options'],
						isset($properties['description']) ? $properties['description'] : $field['description'],
						isset($properties['isMultilingual']) ? $properties['isMultilingual'] : $field['isMultilingual'],
						'', // itemChoices
						isset($properties['errorMsg']) ? $properties['errorMsg'] : $field['errorMsg'],
						isset($properties['visibleBy']) ? $properties['visibleBy'] : $field['visibleBy'],
						isset($properties['editableBy']) ? $properties['editableBy'] : $field['editableBy'],
						isset($properties['descriptionIsParsed']) ? $properties['descriptionIsParsed'] : $field['descriptionIsParsed'],
						isset($properties['validation']) ? $properties['validation'] : $field['validation'],
						isset($properties['validationParam']) ? $properties['validationParam'] : $field['validationParam'],
						isset($properties['validationMessage']) ? $properties['validationMessage'] : $field['validationMessage'],
						isset($properties['permName']) ? $properties['permName'] : $field['permName']
		);
	}

	/**
	 * @param array $conditions     e.g. array('trackerId' => 42)
	 * @param int $maxRecords       default -1 (all)
	 * @param int $offset           default -1
	 * @param array $fields         array of fields to fetch (by permNames)
	 *
	 * @return mixed
	 */
	function getItems(array $conditions, $maxRecords = -1, $offset = -1, $fields = array())
	{
		$keyMap = array();
		$definition = Tracker_Definition::get($conditions['trackerId']);
		foreach ($definition->getFields() as $field) {
			if (! empty($field['permName']) && (empty($fields) || in_array($field['permName'], $fields))) {
				$keyMap[$field['fieldId']] = $field['permName'];
			}
		}

		$table = TikiDb::get()->table('tiki_tracker_items');

		if (! empty($conditions['status'])) {
			$conditions['status'] = $table->in(str_split($conditions['status'], 1));
		} else {
			unset($conditions['status']);
		}

		if (! empty($conditions['modifiedSince'])) {
			$conditions['lastModif'] = $table->greaterThan($conditions['modifiedSince']);
		}

		if (! empty($conditions['itemId'])) {
			$conditions['itemId'] = $table->in((array) $conditions['itemId']);
		}

		unset($conditions['modifiedSince']);

		$items = $table->fetchAll(array('itemId', 'status'), $conditions, $maxRecords, $offset);

		foreach ($items as & $item) {
			$item['fields'] = $this->getItemFields($item['itemId'], $keyMap);
		}

		return $items;
	}

	function getItem($trackerId, $itemId)
	{
		$items = $this->getItems(array(
			'trackerId' => $trackerId,
			'itemId' => $itemId,
		), 1, 0);
		$item = reset($items);

		return $item;
	}

	function processValues($definition, $item)
	{
		$trklib = TikiLib::lib('trk');

		foreach ($item['fields'] as $permName => $rawValue) {
			$field = $definition->getFieldFromPermName($permName);
			$field['value'] = $rawValue;
			$item['fields'][$permName] = $trklib->field_render_value(
							array(
								'field' => $field,
								'process' => 'y',
							)
			);
		}

		return $item;
	}

	private function getItemFields($itemId, $keyMap)
	{
		$table = TikiDb::get()->table('tiki_tracker_item_fields');
		$dataMap = $table->fetchMap(
						'fieldId',
						'value',
						array(
							'fieldId' => $table->in(array_keys($keyMap)),
							'itemId' => $itemId,
						)
		);

		$out = array();
		foreach ($keyMap as $fieldId => $name) {
			if (isset($dataMap[$fieldId])) {
				$out[$name] = $dataMap[$fieldId];
			} else {
				$out[$name] = '';
			}
		}

		return $out;
	}

	function createTracker($data)
	{
		$trklib = TikiLib::lib('trk');
		return $trklib->replace_tracker(
						0,
						$data['name'],
						$data['description'],
						array(),
						$data['descriptionIsParsed']
		);
	}

	function updateTracker($trackerId, $data)
	{
		$trklib = TikiLib::lib('trk');
		$name = $data['name'];
		$description = $data['description'];
		$descriptionIsParsed = $data['descriptionIsParsed'];

		unset($data['name']);
		unset($data['description']);
		unset($data['descriptionIsParsed']);

		$logslib = TikiLib::lib('logs');
		$logslib->add_log('admintrackers', 'changed or created tracker ' . $name);
		return $trklib->replace_tracker($trackerId, $name, $description, $data, $descriptionIsParsed);
	}

	function clearTracker($trackerId)
	{
		$table = TikiDb::get()->table('tiki_tracker_items');

		$items = $table->fetchColumn(
						'itemId',
						array('trackerId' => $trackerId,)
		);

		foreach ($items as $itemId) {
			$this->removeItem($itemId);
		}
	}

	function importField($trackerId, $field, $preserve)
	{
		$fieldId = $field->fieldId->int();

		if (! $preserve) {
			$fieldId = 0;
		}

		$description = $field->descriptionStaticText->text();
		if (! $description) {
			$description = $field->description->text();
		}

		$data = array(
				'name' => $field->name->text(),
				'permName' => $field->permName->word(),
				'type' => $field->type->word(),
				'position' => $field->position->int(),
				'options' => $field->options->none(),

				'isMain' => $field->isMain->alpha(),
				'isSearchable' => $field->isSearchable->alpha(),
				'isTblVisible' => $field->isTblVisible->alpha(),
				'isPublic' => $field->isPublic->alpha(),
				'isHidden' => $field->isHidden->alpha(),
				'isMandatory' => $field->isMandatory->alpha(),
				'isMultilingual' => $field->isMultilingual->alpha(),

				'description' => $description,
				'descriptionIsParsed' => $field->descriptionIsParsed->alpha(),

				'validation' => $field->validation->word(),
				'validationParam' => $field->validationParam->none(),
				'validationMessage' => $field->validationMessage->text(),

				'itemChoices' => '',

				'editableBy' => $field->editableBy->groupname(),
				'visibleBy' => $field->visibleBy->groupname(),
				'errorMsg' => $field->errorMsg->text(),
				);

		// enable prefs for imported fields if required
		$factory = new Tracker_Field_Factory(false);
		$completeList = $factory->getFieldTypes();

		if (! $this->isEnabled($completeList[$data['type']])) {
			foreach ($completeList[$data['type']]['prefs'] as $pref) {
				TikiLib::lib('tiki')->set_preference($pref, 'y');
			}
		}

		$this->updateField($trackerId, $fieldId, $data);
	}

	function exportField($field)
	{
		return <<<EXPORT
[FIELD{$field['fieldId']}]
fieldId = {$field['fieldId']}
name = {$field['name']}
permName = {$field['permName']}
position = {$field['position']}
type = {$field['type']}
options = {$field['options']}
isMain = {$field['isMain']}
isTblVisible = {$field['isTblVisible']}
isSearchable = {$field['isSearchable']}
isPublic = {$field['isPublic']}
isHidden = {$field['isHidden']}
sMandatory = {$field['isMandatory']}
description = {$field['description']}
descriptionIsParsed = {$field['descriptionIsParsed']}

EXPORT;
	}

	function buildOptions($input, $typeInfo)
	{
		if (is_string($typeInfo)) {
			$types = $this->getFieldTypes();
			$typeInfo = $types[$typeInfo];
		}

		if (is_array($input)) {
			$input = new JitFilter($input);
		}

		$parts = array();

		foreach ($typeInfo['params'] as $key => $info) {
			$filter = $info['filter'];

			if (isset($info['count']) && $info['count'] === '*') {
				$values = explode(',', $input->$key->none());
				$filter = TikiFilter::get($filter);
				$values = array_map(array($filter, 'filter'), $values);
			} elseif (isset($info['separator'])) {
				$input->setFilter($key, $filter);
				$values = $input->asArray($key, $info['separator']);
				$values = array(implode($info['separator'], $values));
			} else {
				$values = array($input->$key->$filter());
			}

			foreach ($values as $value) {
				if (isset($info['options']) && ! isset($info['options'][$value])) {
					$value = null;
				}

				$parts[] = $value;
			}
		}

		$rawOptions = implode(',', $parts);
		return rtrim($rawOptions, ',');
	}

	function parseOptions($raw, $typeInfo)
	{
		$out = array();

		foreach ($typeInfo['params'] as $key => $info) {
			if (isset($info['count']) && $info['count'] === '*') {
				// There is a possibility that * does not mean all of the remaining, to apply reasonable heuristic
				$filter = TikiFilter::get($info['filter']);
				$outarray = array();
				foreach ($raw as $r) {
					$filtered = $filter->filter($r);
					if (strcmp($filtered, $r) == 0) {
						$outarray[] = array_shift($raw);
					} else {
						break;
					}
				}
				$out[$key] = implode(',', $outarray);
			} else {
				$out[$key] = array_shift($raw);
			}
		}

		return $out;
	}

	function getFieldTypes()
	{
		$factory = new Tracker_Field_Factory(false);
		$completeList = $factory->getFieldTypes();

		$list = array();

		foreach ($completeList as $code => $info) {
			if ($this->isEnabled($info)) {
				$list[$code] = $info;
			}
		}

		return $list;
	}

	private function isEnabled($info)
	{
		global $prefs;

		foreach ($info['prefs'] as $p) {
			if ($prefs[$p] != 'y') {
				return false;
			}
		}

		return true;
	}

	function getFieldsFromIds($definition, $fieldIds)
	{
		$fields = array();
		foreach ($fieldIds as $fieldId) {
			$field = $field = $definition->getField($fieldId);

			if (! $field) {
				throw new Services_Exception(tr('Field does not exist in tracker'), 404);
			}

			$fields[] = $field;
		}

		return $fields;
	}

	function removeItem($itemId)
	{
		$trklib = TikiLib::lib('trk');
		$trklib->remove_tracker_item($itemId, true);
	}

	function removeTracker($trackerId)
	{
		$trklib = TikiLib::lib('trk');
		$trklib->remove_tracker($trackerId);

		$logslib = TikiLib::lib('logs');
		$logslib->add_log('admintrackers', 'removed tracker ' . $trackerId);
	}

	function duplicateTracker($trackerId, $name, $duplicateCategories, $duplicatePermissions)
	{
		$trklib = TikiLib::lib('trk');
		$newTrackerId = $trklib->duplicate_tracker($trackerId, $name, '', 'n');

		if ($duplicateCategories) {
			$categlib = TikiLib::lib('categ');
			$cats = $categlib->get_object_categories('tracker', $trackerId);
			$catObjectId = $categlib->add_categorized_object('tracker', $newTrackerId, '', $name, "tiki-view_tracker.php?trackerId=$newTrackerId");
			foreach ($cats as $cat) {
				$categlib->categorize($catObjectId, $cat);
			}
		}

		if ($duplicatePermissions) {
			$userlib = TikiLib::lib('user');
			$userlib->copy_object_permissions($trackerId, $newTrackerId, 'tracker');
		}

		return $newTrackerId;
	}
}

