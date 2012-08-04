<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Currency.php 39469 2012-01-12 21:13:48Z changi67 $

/**
 * Handler class for currency field
 * 
 * Letter key: ~b~
 *
 */
class Tracker_Field_Currency extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'b' => array(
				'name' => tr('Currency Field'),
				'description' => tr('Provides a one-line field for numeric input only. Prepend or append values may be alphanumeric.'),
				'help' => 'Currency Amount Tracker Field',
				'prefs' => array('trackerfield_currency'),
				'tags' => array('basic'),
				'default' => 'n',
				'params' => array(
					'samerow' => array(
						'name' => tr('Same Row'),
						'description' => tr('Displays the next field on the same line.'),
						'deprecated' => true,
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
					'size' => array(
						'name' => tr('Display Size'),
						'description' => tr('Visible size of the field in characters. Does not affect the numeric length.'),
						'filter' => 'int',
						'default' => 7,
					),
					'prepend' => array(
						'name' => tr('Prepend'),
						'description' => tr('Text to be displayed prior to the currency amount.'),
						'filter' => 'text',
						'default' => '',
					),
					'append' => array(
						'name' => tr('Append'),
						'description' => tr('Text to be displayed after the numeric value.'),
						'filter' => 'text',
						'default' => '',
					),
					'locale' => array(
						'name' => tr('Locale'),
						'description' => tr('Set locale for currency formatting, for example en_US or en_US.UTF-8 or en_US.ISO-8559-1. Default is en_US.'),
						'filter' => 'text',
						'default' => 'en_US',
					),
					'currency' => array(
						'name' => tr('Currency'),
						'description' => tr('A custom alphanumeric currency code. Not needed if locale is set and a standard code is desired. Default is USD.'),
						'filter' => 'alpha',
						'default' => 'USD',
					),
					'symbol' => array(
						'name' => tr('Symbol'),
						'description' => tr('Set whether the currency code (for example USD) or symbol (for example $) will display. Defaults to symbol.'),
						'filter' => 'alpha',
						'default' => 'n',
						'options' => array(
							'i' => tr('Currency code'),
							'n' => tr('Currency symbol'),
						),
					),
					'all_symbol' => array(
						'name' => tr('First or all'),
						'description' => tr('Set whether the currency code or symbol will be displayed against all amounts or only the first amount.'),
						'filter' => 'int',
						'default' => 0,
						'options' => array(
							0 => tr('First item only'),
							1 => tr('All'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		return array(
			'value' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: $this->getValue(),
		);
	}

	function renderInnerOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/currency.tpl', $context);
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/numeric.tpl', $context);
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

