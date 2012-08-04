<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-action_calendar.php 40108 2012-03-10 14:32:03Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function module_action_calendar_info()
{
	return array(
		'name' => tra('Action Calendar'),
		'description' => tra('Displays a calendar of system events, such as wiki page modifications, forum posts and article publications. Days with events show links to the action calendar page.'),
		'prefs' => array('feature_action_calendar'),
		'params' => array(
			'items' => array(
				'name' => tra('Item types filter'),
				'description' => tra('If set to a list of item types, restricts the items displayed to those of one of these types. Each set is a comma-separated list of item type codes.') . " " . tra('Possible item type values:') . ' wiki, gal, art, blog, forum, dir, fgal, faq, quiz, track, surv, nl.'
			)
		)
	);
}

function module_action_calendar($mod_reference, &$module_params)
{
	global $prefs, $tiki_p_view_tiki_calendar, $tikilib, $smarty;
	$smarty->assign('show_calendar_module', 'n');
	if ($tiki_p_view_tiki_calendar == 'y') {
		$smarty->assign('show_calendar_module', 'y');	
		global $tikicalendarlib; include_once('lib/calendar/tikicalendarlib.php');
		global $headerlib; $headerlib->add_cssfile('css/calendar.css', 20);
		global $calendarViewMode;
	
		$calendarViewMode['casedefault'] = 'month';
		$group_by = 'day';
	
		include('tiki-calendar_setup.php');
	
		$viewTikiCals = $tikicalendarlib->getTikiItems(false);
		if (isset($module_params['items'])) {
			$viewTikiCals = array_intersect(explode(',', strtolower(str_replace(' ', '', $module_params['items']))), $viewTikiCals);
		}

		// Don't show "Add event" link below the calendar in action calendar context
		$module_params['showaction'] = 'n';

		$tc_infos = $tikicalendarlib->getCalendar($viewTikiCals, $viewstart, $viewend, $group_by);
		foreach ($tc_infos as $tc_key => $tc_val) {
				$smarty->assign($tc_key, $tc_val);
		}
	
		$smarty->assign('name', 'action_calendar');
	
		$smarty->assign('daformat2', $tikilib->get_long_date_format());
		$smarty->assign('var', '');
		$smarty->assign('myurl', 'tiki-action_calendar.php');
	}
}
