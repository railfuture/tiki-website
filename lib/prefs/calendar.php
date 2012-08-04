<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: calendar.php 39788 2012-02-08 17:33:27Z marclaporte $

function prefs_calendar_list()
{
	return array(
		'calendar_view_days' => array(
			'name' => tra('Days to display in the Calendar'),
			'type' => 'multicheckbox',
			'options' => array( 
				0 => tra('Sunday'),
				1 => tra('Monday'),
				2 => tra('Tuesday'),
				3 => tra('Wednesday'),
				4 => tra('Thursday'),
				5 => tra('Friday'),
				6 => tra('Saturday'),
			),
			'default' => array(0,1,2,3,4,5,6),
		),
		'calendar_view_mode' => array(
			'name' => tra('Default view mode'),
			'type' => 'list',
			'options' => array(
				'day' => tra('Day'),
				'week' => tra('Week'),
				'month' => tra('Month'),
				'quarter' => tra('Quarter'),
				'semester' => tra('Semester'),
				'year' => tra('Year'),
			),
			'default' => 'month',
			'tags' => array('basic'),
		),
		'calendar_list_begins_focus' => array(
			'name' => tra('View list begins'),
			'type' => 'list',
			'options' => array(
				'y' => tra('Focus Date'),
				'n' => tra('Period beginning'),
			),
			'default' => 'n',
		),
		'calendar_firstDayofWeek' => array(
			'name' => tra('First day of the week'),
			'type' => 'list',
			'options' => array(
				'0' => tra('Sunday'),
				'1' => tra('Monday'),
				'user' => tra('Depends user language'),
			),
			'default' => 'user',
		),
		'calendar_timespan' => array(
			'name' => tra('Split hours in periods of'),
			'type' => 'list',
			'options' => array(
				'1' => tra('1 minute'),
				'5' => tra('5 minutes'),
				'10' => tra('10 minutes'),
				'15' => tra('15 minutes'),
				'30' => tra('30 minutes'),
			),
			'default' => '30',
		),
		'calendar_start_year' => array(
			'name' => tra('First year in the dropdown'),
			'type' => 'text',
			'size' => '5',
			'hint' => tra('Enter a year or use +/- N to specify a year relative to the current year'),
			'default' => '-3',
		),
		'calendar_end_year' => array(
			'name' => tra('Last year in the dropdown'),
			'type' => 'text',
			'size' => '5',
			'hint' => tra('Enter a year or use +/- N to specify a year relative to the current year'),
			'default' => '+5',
		),
		'calendar_sticky_popup' => array(
			'name' => tra('Sticky popup'),
			'type' => 'flag',
			'default' => 'n',
		),
		'calendar_view_tab' => array(
			'name' => tra('Item view tab'),
			'type' => 'flag',
			'default' => 'n',
		),
		'calendar_addtogooglecal' => array(
			'name' => tra('Show Add to Google Calendar icon'),
			'type' => 'flag',
			'dependencies' => array(
				'wikiplugin_addtogooglecal'
			),
			'default' => 'n',
		),
		'calendar_export' => array(
			'name' => tra('Show Export Calendars button'),
			'type' => 'flag',
			'default' => 'n',
		),
		'calendar_fullcalendar' => array(
			'name' => tra('Use FullCalendar to display Calendars'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_jquery'
			),
			'default' => 'n',
		),
		'calendar_description_is_html' => array(
			'name' => tra('Treat calendar item descriptions as HTML'),
			'description' => tra('Use this if you use the WYSIWYG editor for calendars. This is to handle legacy data from Tiki pre 7.0.'),
			'type' => 'flag',
			'default' => 'y',
		),
	);
}
