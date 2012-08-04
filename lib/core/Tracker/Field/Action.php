<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Action.php 39469 2012-01-12 21:13:48Z changi67 $

class Tracker_Field_Action implements Tracker_Field_Interface
{
	public static function getTypes()
	{
		return array(
			'x' => array(
				'name' => tr('Action'),
				'description' => tr('Create a form which will be posted somewhere, not necessarily trackers or even Tiki.'),
				'help' => 'Action Tracker Field',
				'prefs' => array('trackerfield_action'),
				'tags' => array('experimental'),
				'default' => 'n',
				'params' => array(
					'label' => array(
						'name' => tr('Name'),
						'description' => tr('Needs explanation'),
						'filter' => 'text',
					),
					'post' => array(
						'name' => tr('Post'),
						'description' => tr('Needs explanation'),
						'filter' => 'text',
					),
					'script' => array(
						'name' => tr('Script'),
						'description' => tr('Needs explanation'),
						'filter' => 'text',
						'example' => 'tiki-index.php',
					),
					'parameters' => array(
						'name' => tr('Parameters'),
						'description' => tr('Needs explanation'),
						'filter' => 'text',
						'count' => '*',
						'example' => 'page:fieldname',
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		return array();
	}

	function renderInput($context = array())
	{
		return null;
	}

	function renderOutput($context = array())
	{
		return null;
	}

	function watchCompare($new, $old)
	{
		return null;
	}
}
