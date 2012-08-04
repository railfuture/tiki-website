<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Definition.php 39481 2012-01-13 19:42:28Z jonnybradley $

class Tracker_Definition
{
	static $definitions = array();

	private $trackerInfo;
	private $factory;
	private $fields;

	public static function get($trackerId)
	{
		$trackerId = (int) $trackerId;

		if (isset(self::$definitions[$trackerId])) {
			return self::$definitions[$trackerId];
		}

		$trklib = TikiLib::lib('trk');
		$tracker_info = $trklib->get_tracker($trackerId);

		$definition = false;

		if ($tracker_info) {
			if ($t = $trklib->get_tracker_options($trackerId)) {
				$tracker_info = array_merge($t, $tracker_info);
			}

			$definition = new self($tracker_info);
		}

		return self::$definitions[$trackerId] = $definition;
	}

	public static function createFake(array $trackerInfo, array $fields)
	{
		$def = new self($trackerInfo);
		$def->fields = $fields;

		return $def;
	}

	public static function getDefault()
	{
		$def = new self(array());
		$def->fields = array();

		return $def;
	}

	private function __construct($trackerInfo)
	{
		$this->trackerInfo = $trackerInfo;
	}

	function getInformation()
	{
		return $this->trackerInfo;
	}

	function getFieldFactory()
	{
		if ($this->factory) {
			return $this->factory;
		}

		return $this->factory = new Tracker_Field_Factory($this);
	}

	function getConfiguration($key, $default = false)
	{
		return isset($this->trackerInfo[$key]) ? $this->trackerInfo[$key] : $default;
	}

	function isEnabled($key)
	{
		return $this->getConfiguration($key) === 'y';
	}
	
	function getFieldsIdKeys()
	{
		$fields = array();
		foreach ($this->getFields() as $key => $field) {
			$fields[$field['fieldId']] = $field;
		}
		return $fields;
	}
	
	function getFields()
	{
		if ($this->fields) {
			return $this->fields;
		}

		$trklib = TikiLib::lib('trk');
		$trackerId = $this->trackerInfo['trackerId'];

		if ($trackerId) {
			$fields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc', '', true);
		
			return $this->fields = $fields['data'];
		} else {
			$this->fields = array();
		}
	}

	function getField($id)
	{
		if (is_numeric($id)) {
			foreach ($this->getFields() as $f) {
				if ($f['fieldId'] == $id) {
					return $f;
				}
			}
		} else {
			return $this->getFieldFromPermName($id);
		}
	}

	function getFieldFromName($name)
	{
		foreach ($this->getFields() as $f) {
			if ($f['name'] == $name) {
				return $f;
			}
		}
	}

	function getFieldFromPermName($name)
	{
		if (empty($name)) {
			return null;
		}

		foreach ($this->getFields() as $f) {
			if ($f['permName'] == $name) {
				return $f;
			}
		}
	}

	function getPopupFields()
	{
		if (!empty($this->trackerInfo['showPopup'])) {
			return explode(',', $this->trackerInfo['showPopup']);
		} else {
			return array();
		}
	}

	function getAuthorField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'u'
				&& isset($field['options'][0]) && $field['options'][0] == 1
				&& isset($this->trackerInfo["writerCanModify"]) && $this->trackerInfo["writerCanModify"] == 'y') {

				return $field['fieldId'];
			}
		}
	}

	function getWriterField()
	{
		foreach ($this->getFields() as $field) {
			if (in_array($field['type'], array('u', 'I'))
				&& isset($field['options'][0]) && $field['options'][0] == 1) {
				return $field['fieldId'];
			}
		}
	}

	function getUserField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'u'
				&& isset($field['options'][0]) && $field['options'][0] == 1) {

				return $field['fieldId'];
			}
		}
	}

	function getGeolocationField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'G' && isset($field['options_array'][0]) && ($field['options_array'][0] == 1 || $field['options_array'][0] == 'y')) {
				return $field['fieldId'];
			}
		}
	}

	function getIconField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'icon') {
				return $field['fieldId'];
			}
		}
	}

	function getWriterGroupField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'g'
				&& isset($field['options'][0]) && $field['options'][0] == 1) {
				return $field['fieldId'];
			}
		}
	}

	function getRateField()
	{
		// This is here to support some legacy code for the deprecated 's' type rating field. It is not meant to be generically apply to the newer stars rating field
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 's' && $field['name'] == 'Rating') {
				return $field['fieldId'];
			}
		}
	}

	function getFreetagField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'F') {
				return $field['fieldId'];
			}
		}
	}

	function getLanguageField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'LANG'
				&& isset($field['options'][0]) && $field['options'][0] == 1) {
				return $field['fieldId'];
			}
		}
	}

	function getCategorizedFields()
	{
		$out = array();

		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'e') {
				$out[] = $field['fieldId'];
			}
		}

		return $out;
	}
	
	/**
	 * Get the name of the item user if any.
	 * A item user is defined if a 'user selector' field
	 * exist for this tracker and it has one user selected.
	 * 
	 * @param int $itemId
	 * @return string item user name
	 */
	function getItemUser($itemId)
	{
		global $trklib;
		return $trklib->get_item_creator($this->trackerInfo['trackerId'], $itemId);
	}

	function getSyncInformation()
	{
		global $prefs;

		if ($prefs['tracker_remote_sync'] != 'y') {
			return false;
		}

		$attributelib = TikiLib::lib('attribute');
		$attributes = $attributelib->get_attributes('tracker', $this->getConfiguration('trackerId'));

		if (! isset($attributes['tiki.sync.provider'])) {
			return false;
		}

		return array(
			'provider' => $attributes['tiki.sync.provider'],
			'source' => $attributes['tiki.sync.source'],
			'last' => $attributes['tiki.sync.last'],
			'modified' => $this->getConfiguration('lastModif') > $attributes['tiki.sync.last'],
		);
	}
}

