<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-calendar_params_ical.php 40028 2012-03-04 08:38:46Z pkdille $

$section = 'calendar';
require_once ('tiki-setup.php');

$access->check_feature('feature_calendar');
$access->check_permission('tiki_p_view_events');

include_once ('lib/calendar/calendarlib.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/newsletters/nllib.php');

$smarty->assign('edit', false);

if (isset($_SESSION['CalendarFocusDate']) && $_SESSION['CalendarFocusDate']) {
	$now = explode('/', date('m/d/Y', $_SESSION['CalendarFocusDate']));
} else {
	// by default, export will start from yesterday's events.
	$now = explode('/', date('m/d/Y'));
}

if (isset($_SESSION['CalendarViewMode'])) {
	switch ($_SESSION['CalendarViewMode']) {
		case 'month':
			$startTime = mktime(0, 0, 0, $now[0], 1, $now[2]);
			$stopTime = mktime(0, 0, 0, $now[0]+1, 0, $now[2]);
						break;
		default:
			$startTime = mktime(0, 0, 0, $now[0], $now[1]-1, $now[2]);
			$stopTime = mktime(0, 0, 0, $now[0], $now[1]+1, $now[2]);
	}
} else {
	$startTime = mktime(0, 0, 0, $now[0], $now[1]-1, $now[2]);
	$stopTime = mktime(0, 0, 0, $now[0], $now[1]+1, $now[2]);
}

$smarty->assign('startTime', $startTime);
$smarty->assign('stopTime', $stopTime);

$caladd = array();
$rawcals = $calendarlib->list_calendars();

foreach ($rawcals['data'] as $cal_id=>$cal_data)
	$caladd["$cal_id"] = $cal_data;

$smarty->assign('listcals', $caladd);
$smarty->assign('mid', 'tiki-calendar_params_ical.tpl');
$smarty->display('tiki.tpl');
