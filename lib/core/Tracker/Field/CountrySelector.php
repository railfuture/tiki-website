<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: CountrySelector.php 42292 2012-07-09 14:10:09Z Jyhem $

/**
 * Handler class for CountrySelector
 * 
 * Letter key: ~y~
 *
 */
class Tracker_Field_CountrySelector extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'y' => array(
				'name' => tr('Country Selector'),
				'description' => tr('Allows a selection from a specified list of countries'),
				'help' => 'Country Selector',
				'prefs' => array('trackerfield_countryselector'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'name_flag' => array(
						'name' => tr('Display'),
						'description' => tr('Specify the rendering type for the field'),
						'filter' => 'int',
						'options' => array(
							0 => tr('Name and flag'),
							1 => tr('Name only'),
							2 => tr('Flag only'),
						),
					),
					'sortorder' => array(
						'name' => tr('Sort Order'),
						'description' => tr('Determines whether the ordering should be based on the translated name or the English name.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('Translated name'),
							1 => tr('English name'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		$data = array(
			'value' => isset($requestData[$ins_id])
				? $requestData[$ins_id]
				: $this->getValue(),
			'flags' => TikiLib::lib('trk')->get_flags(true, true, ($this->getOption(1) != 1)),
			'defaultvalue' => 'None',
		);
		
		return $data;
	}

	function renderInnerOutput($context = array())
	{
		$flags = $this->getConfiguration('flags');
		$current = $this->getConfiguration('value');
		
		if (empty($current)) {
			return '';
		}
		$label = $flags[$current];
		$out = '';
		
		if ($context['list_mode'] != 'csv') {
			if ($this->getOption(0) != 1) {
				$out .= '<img src="img/flags/'.$current.'.gif" title="'.$label.'" alt="'.$label.'" />';
			}
			if ($this->getOption(0) == 0) {
				$out .= ' ';
			}
		}
		if ($this->getOption(0) != 2) {
			$out .= $label;
		}
		
		return $out;
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/countryselector.tpl', $context);
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
		return $info;
	}
}

