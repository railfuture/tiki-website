<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: JsCalendar.php 39755 2012-02-06 16:26:45Z jonnybradley $

class Tracker_Field_JsCalendar extends Tracker_Field_DateTime
{
	public static function getTypes()
	{
		return array(
			'j' => array(
				'name' => tr('Date and Time (Date Picker)'),
				'description' => tr('Provides jQuery-UI date picker select a date and optionally time.'),
				'prefs' => array('trackerfield_jscalendar'),
				'tags' => array('advanced'),
				'default' => 'y',
				'params' => array(
					'datetime' => array(
						'name' => tr('Type'),
						'description' => tr('Components to be included'),
						'filter' => 'text',
						'options' => array(
							'dt' => tr('Date and Time'),
							'd' => tr('Date only'),
						),
					),
					'useNow' => array(
						'name' => tr('Default value'),
						'description' => tr('Default date and time for new items'),
						'filter' => 'int',
						'options' => array(
							0 => tr('None (undefined)'),
							1 => tr('Item creation date and time'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $prefs;
		if ($prefs['feature_jquery_ui'] !== 'y') {	// fall back to simple date field
			return parent::getFieldData($requestData);
		}

		$ins_id = $this->getInsertId();

		return array(
			'value' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: $this->getValue(),
		);
	}

	function renderInput($context = array())
	{
		global $prefs;
		if ($prefs['feature_jquery_ui'] !== 'y') {	// fall back to simple date field
			return parent::renderInput($context);
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_jscalendar');

		$params = array( 'fieldname' => $this->getInsertId());
		$params['showtime'] = $this->getOption(0) === 'd' ? 'n' : 'y';
		if ( empty($context['inForm'])) {
			$params['date'] = $this->getValue();
			if (empty($params['date'])) {
				$params['date'] = $this->getConfiguration('value');
			}
			if (empty($params['date']) && $this->getOption(1)) {
				$params['date'] = TikiLib::lib('tiki')->now;
			}
		} else {
			$params['date'] = '';
		}

		return smarty_function_jscalendar($params, $smarty);
	}
}

