<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-calendar_setup.php 39983 2012-02-28 22:18:13Z pkdille $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__))!=FALSE) {
	header('location: index.php');
	exit;
}

if ( ! ($prefs['feature_calendar'] == 'y' || $prefs['feature_action_calendar'] == 'y')) {
	if (isset($_SERVER['SCRIPT_NAME'])) {
		if ($_SERVER['SCRIPT_NAME'] == "tiki-calendar.php")
			$smarty->assign('msg', tra("This feature is disabled") . ": feature_calendar");
		elseif ($_SERVER['SCRIPT_NAME'] == "tiki-action_calendar.php")
			$smarty->assign('msg', tra("This feature is disabled") . ": feature_action_calendar");
		else
			$smarty->assign('msg', tra("This feature is disabled"));
	}
	$smarty->display("error.tpl");
	die;
}

global $calendarlib; include_once('lib/calendar/calendarlib.php');

$trunc = '40'; // put in a pref, number of chars displayed in cal cells

if (!empty($_REQUEST['focus'])) {
	$_REQUEST['todate'] = $_SESSION['CalendarFocusDate'] = $_REQUEST['focus'];
}

if (!empty($_REQUEST['day']) && !empty($_REQUEST['mon']) && !empty($_REQUEST['year'])) {//can come from the event module
	$_REQUEST['todate'] = $tikilib->make_time(23, 59, 59, intval($_REQUEST['mon']), intval($_REQUEST['day']), intval($_REQUEST['year']));
} elseif (isset($_REQUEST['todate']) && $_REQUEST['todate']) {
	$_SESSION['CalendarFocusDate'] = $_REQUEST["todate"];
} elseif (!isset($_REQUEST['todate']) && isset($_SESSION['CalendarFocusDate']) && $_SESSION['CalendarFocusDate']) {
	$_REQUEST["todate"] = $_SESSION['CalendarFocusDate'];
} else {
	$_REQUEST["todate"] = $tikilib->now;
}

$focusdate = $_REQUEST['todate'];
list($focus_day, $focus_month, $focus_year) = array(
		TikiLib::date_format("%d", $focusdate),
		TikiLib::date_format("%m", $focusdate),
		TikiLib::date_format("%Y", $focusdate)
);

$focus = array('day'=>$focus_day, 'month'=>$focus_month, 'year'=>$focus_year);
$focuscell = $tikilib->make_time(0, 0, 0, $focus_month, $focus_day, $focus_year);
$smarty->assign('focusdate', $focusdate);
$smarty->assign('focuscell', $focuscell);
$smarty->assign('today', $tikilib->make_time(0, 0, 0, $tikilib->date_format('%m'), $tikilib->date_format('%d'), $tikilib->date_format('%Y')));

// Get viewmode from URL, session or prefs if it has not already been defined by the calling script (for example by modules, to force a month view)
// ###trebly:B10111:[FIX-ADD-ENH]-> there are several meaning for the same var $calendarViewMode
if ( ! isset($calendarViewMode) ) {
	// ###trebly:B10111:[FIX-ADD-ENH]-> $calendarViewMode become an array, several bugs comes from confusion of global values and parameters by ref
	// for calendars : (main-)calendar, action_calendar, mod_calendar, mod_action_calendar the changes of values by url request is terrible
	// for the moment 01/11/2011:11:55 just one value is used with index 'default', but initialisation is done.
	// The init is actually into two places, tiki-calendar_setup.php and tiki-calendar_export.php will be grouped for clean
	// $prefs would be added when need, $_SESSION, $PARAMS too this now generates not any change in the behavior.
	$calendarViewMode = array(
			'casedefault'=>'month',
			'calgen'=>'month',
			'calaction'=>'month',
			'modcalgen'=>'month',
			'modcalaction'=>'month',
			'trackercal'=>'month'
			);

	if (!empty($_REQUEST['viewmode'])) {
		$calendarViewMode['casedefault'] = $_REQUEST['viewmode'];
	} elseif (!empty($_SESSION['CalendarViewMode'])) {
		$calendarViewMode['casedefault'] = $_SESSION['CalendarViewMode'];
	} else {
		$calendarViewMode['casedefault'] = $prefs['calendar_view_mode'];
	}
}

$_SESSION['CalendarViewMode'] = $calendarViewMode['casedefault'];
$smarty->assign_by_ref('viewmode', $calendarViewMode['casedefault']);

if (isset($_REQUEST["viewlist"])) {
	$viewlist = $_REQUEST['viewlist'];
	$_SESSION['CalendarViewList'] = $viewlist;
} elseif (isset($_REQUEST["viewlistmodule"])) {
	$viewlist = $_REQUEST['viewlistmodule'];
} elseif (!empty($_SESSION['CalendarViewList'])) {
	$viewlist = $_SESSION['CalendarViewList'];
} else {
	$viewlist = "";
}

$smarty->assign_by_ref('viewlist', $viewlist);

if (isset($_REQUEST["gbi"])) {
	$group_by_item = $_REQUEST["gbi"];
	$_SESSION['CalendarGroupByItem'] = $group_by_item;
} else {
	$group_by_item = "";
}

$smarty->assign_by_ref('group_by_item', $_SESSION['CalendarGroupByItem']);

$calendarViewGroups = (isset($_SESSION['CalendarViewGroups'])) ? $_SESSION['CalendarViewGroups'] : '';
$calendarViewList = array_key_exists('CalendarViewList', $_SESSION) ? $_SESSION['CalendarViewList'] : '';
$calendarGroupByItem = $_SESSION['CalendarGroupByItem'];

if ($prefs['calendar_firstDayofWeek'] == 'user') {
	$firstDayofWeek = (int)tra('First day of week: Sunday (its ID is 0) - translators you need to localize this string!');
	if ( $firstDayofWeek < 1 || $firstDayofWeek > 6 ) {
		$firstDayofWeek = 0;
	}
} else {
	$firstDayofWeek = $prefs['calendar_firstDayofWeek'];
}
$smarty->assign('firstDayofWeek', $firstDayofWeek);

$strRef = tra("%H:%M %Z");
if (strstr($strRef, "%h") || strstr($strRef, "%g")) {
	$timeFormat12_24 = "12";
} else {
	$timeFormat12_24 = "24";
}
$smarty->assign('timeFormat12_24', $timeFormat12_24);
$smarty->assign('short_format_day', tra('%m/%d'));

// To make "previous month" work if the current focus is on, for example, the last day of march.
$focus_day_limited = min($focus_day, 28);

if (!function_exists('cal_days_in_month')) {
	$smarty->assign('msg', tra('Your PHP installation does not have calendar enabled.'));
	$smarty->display('error.tpl');
	die;
}	
$focus_prev = $calendarlib->focusPrevious($focus, $calendarViewMode['casedefault']);
$focus_next = $calendarlib->focusNext($focus, $calendarViewMode['casedefault']);

$smarty->assign('focus_prev', $focus_prev['date']);
$smarty->assign('focus_next', $focus_next['date']);

$smarty->assign('focusday', $focus_day);
$smarty->assign('focusmonth', $focus_month);
$smarty->assign('focusdate', $focusdate);
$smarty->assign('focuscell', $focuscell);

$smarty->assign('now', $tikilib->now);
$smarty->assign('nowUser', $tikilib->now);

$weekdays = range(0, 6);
$hours = range(0, 23);

$d = 60 * 60 * 24;
$currentweek = TikiLib::date_format("%U", $focusdate);
$wd = TikiLib::date_format('%w', $focusdate);

//prepare for select first day of week (Hausi)
if ($firstDayofWeek == 1) {
	$wd--;
	if ($wd == -1) {
		$wd = 6;
	}
}

if (isset($request_day)) $focus_day = $request_day;
if (isset($request_month)) $focus_month = $request_month;
if (isset($request_year)) $focus_year = $request_year;

$smarty->assign('viewmonth', $focus_month);
$smarty->assign('viewday', $focus_day);
$smarty->assign('viewyear', $focus_year);

// calculate timespan for sql query
if ($viewlist == 'list' && $prefs['calendar_list_begins_focus'] == 'y') {
	$daystart = $focusdate;
} elseif ($calendarViewMode['casedefault'] == 'month' 
		|| $calendarViewMode['casedefault'] == 'quarter' 
		|| $calendarViewMode['casedefault'] == 'semester'
) {
	$daystart = $tikilib->make_time(0, 0, 0, $focus_month, 1, $focus_year);
} elseif ($calendarViewMode['casedefault'] == 'year') {
	$daystart = $tikilib->make_time(0, 0, 0, 1, 1, $focus_year);
} else {
	$daystart = $tikilib->make_time(0, 0, 0, $focus_month, $focus_day, $focus_year);
}

// viewstart is the beginning of the display, daystart is the beginning of the selected period
$viewstart = $daystart;

if ( $calendarViewMode['casedefault'] == 'month' ||
		$calendarViewMode['casedefault'] == 'quarter' ||
		$calendarViewMode['casedefault'] == 'semester' ||
		$calendarViewMode['casedefault'] == 'year' ) {

	$TmpWeekday = TikiLib::date_format("%w", $viewstart);

	// prepare for select first day of week (Hausi)
	if ( $firstDayofWeek == 1 ) {
		$TmpWeekday--;
		if ( $TmpWeekday == -1 ) {
			$TmpWeekday=6;
		}
	}

	// move viewstart back to first day of week ...
	if ( $viewlist != 'list' ) {
		//$viewstart -= $TmpWeekday * $d;

		if ( $TmpWeekday > 0 ) {
			$viewstart_m = TikiLib::date_format("%m", $viewstart);
			$viewstart_y = TikiLib::date_format("%Y", $viewstart);

			// $tikilib->make_time() used with timezones doesn't support month = 0
			if ( $viewstart_m == 1 ) {
				$viewstart_m = 12;
				$viewstart_y--;
			} else {
				$viewstart_m--;
			}

			// $tikilib->make_time() used with timezones doesn't support day = 0
			// This supposes that $viewstart's day == 1, as defined above
			$viewstart_d = Date_Calc::daysInMonth($viewstart_m, $viewstart_y) - ( $TmpWeekday - 1 );

			$viewstart = $tikilib->make_time(0, 0, 0, $viewstart_m, $viewstart_d, $viewstart_y);
		}
	}
	// this is the last day of $focus_month
	if ($viewlist == 'list' && $prefs['calendar_list_begins_focus'] == 'y') {
		$df = $focus_day;
	} else {
		$df = 1;
	}

	if ($calendarViewMode['casedefault'] == 'month') {
		$viewend = $tikilib->make_time(0, 0, 0, $focus_month + 1, $df, $focus_year);
	} elseif ($calendarViewMode['casedefault'] == 'quarter') {
		$viewend = $tikilib->make_time(0, 0, 0, $focus_month + 3, $df, $focus_year);
	} elseif ($calendarViewMode['casedefault'] == 'semester') {
		$viewend = TikiLib::make_time(0, 0, 0, $focus_month + 6, $df, $focus_year);
	} elseif ($calendarViewMode['casedefault'] == 'year') {
		$viewend = $tikilib->make_time(0, 0, 0, 1, $df, $focus_year+1);
	} else {
		$viewend = $tikilib->make_time(0, 0, 0, $focus_month + 1, 0, $focus_year);
	}
	$viewend -= 1;
	$dayend = $viewend;
	$TmpWeekday = TikiLib::date_format("%w", $viewend);
	if ( $viewlist != 'list' ) {
		//$viewend += (6 - $TmpWeekday) * $d;
		$viewend = $tikilib->make_time(
						23, 59, 59,
						TikiLib::date_format("%m", $viewend),
						TikiLib::date_format("%d", $viewend) + ( 6 - $TmpWeekday ),
						TikiLib::date_format("%Y", $viewend)
		);
	}

	// ISO weeks --- kinda mangled because ours begin on Sunday...
	$firstweek = TikiLib::date_format("%U", $viewstart + $d);
	$lastweek = TikiLib::date_format("%U", $viewend);

	if ($lastweek <= $firstweek) {
		$startyear = TikiLib::date_format("%Y", $daystart - 1);
		$weeksinyear = TikiLib::date_format("%U", $tikilib->make_time(0, 0, 0, 12, 31, $startyear));

		if ($weeksinyear == 1) {
			$weeksinyear = TikiLib::date_format("%U", $tikilib->make_time(0, 0, 0, 12, 28, $startyear));
		}

		$lastweek += $weeksinyear;
	}

	$numberofweeks = $lastweek - $firstweek;

} elseif ( $calendarViewMode['casedefault'] == 'week' ) {
	$firstweek = $currentweek;
	$lastweek = $currentweek;

	// then back up to the preceding Sunday;
	// $viewstart -= $wd * $d;
	if ( $wd > 0 and $viewlist != 'list' ) {

		$viewstart_d = TikiLib::date_format("%d", $viewstart);
		$viewstart_m = TikiLib::date_format("%m", $viewstart);
		$viewstart_y = TikiLib::date_format("%Y", $viewstart);

		// Start in previous month if $wd is greater than the current day (relative to th current month)
		if ( $viewstart_d <= $wd ) {

			// $tikilib->make_time() used with timezones doesn't support month = 0
			if ( $viewstart_m == 1 ) {
				$viewstart_m = 12;
				$viewstart_y--;
			} else {
				$viewstart_m--;
			}

			// $tikilib->make_time() used with timezones doesn't support day = 0
			// This supposes that $viewstart's day == 1, as defined above
			$viewstart_d = Date_Calc::daysInMonth($viewstart_m, $viewstart_y) - ( $wd - $viewstart_d );

		} else {
			$viewstart_d -= $wd;
		}

		$viewstart = $tikilib->make_time(0, 0, 0, $viewstart_m, $viewstart_d, $viewstart_y);
	}
	$daystart = $viewstart;
	// then go to the end of the week for $viewend
	// $viewend = $viewstart + (7 * $d) - 1;
	$viewend = $tikilib->make_time(
					0,
					0,
					0,
					TikiLib::date_format("%m", $daystart),
					TikiLib::date_format("%d", $daystart) + 7,
					TikiLib::date_format("%Y", $daystart)
	) - 1;

	$dayend = $viewend;
	$numberofweeks = 0;

} else {

	$firstweek = $currentweek;
	$lastweek = $currentweek;

	//	$viewend = $viewstart + ($d - 1);
	$viewend = $tikilib->make_time(
					0,
					0,
					0,
					TikiLib::date_format("%m", $viewstart),
					TikiLib::date_format("%d", $viewstart) + 1,
					TikiLib::date_format("%Y", $viewstart)
	) - 1;

	$dayend = $daystart;
	$weekdays = array(TikiLib::date_format('%w', $focusdate));
	$numberofweeks = 0;

}

$smarty->assign('viewstart', $viewstart);
$smarty->assign('viewend', $viewend);
$smarty->assign('numberofweeks', $numberofweeks);
$smarty->assign('daystart', $daystart);
$smarty->assign('dayend', $dayend);

$calendarlib->getDayNames($firstDayofWeek, $daysnames, $daysnames_abr);

$weeks = array();
$cell = array();

if (!function_exists('correct_start_day')) {
	function correct_start_day($d)
	{
		global $prefs;

		$tmp = $d - $prefs['calendar_firstDayofWeek'];
		if ($tmp < 0 ) {
			$tmp += 7;
		}
		return $tmp;
	}
}

if (empty($myurl)) {
	$myurl = 'tiki-calendar.php';
}
$jscal_url = "$myurl?todate=%s";
$smarty->assign('jscal_url', $jscal_url);
