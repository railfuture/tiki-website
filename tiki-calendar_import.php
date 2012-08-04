<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-calendar_import.php 39467 2012-01-12 19:47:28Z changi67 $

$section = 'calendar';
require_once ('tiki-setup.php');
require_once ('lib/calendar/calendarlib.php');

$access->check_feature('feature_calendar');
$access->check_permission('tiki_p_admin_calendar');

if (isset($_REQUEST["import"]) && isset($_REQUEST["calendarId"]) && isset($_FILES["fileCSV"])) {
	if ($calendarlib->importCSV($_FILES["fileCSV"]["tmp_name"], $_REQUEST["calendarId"]))
		$smarty->assign('updated', "y");
}
$calendars = $calendarlib->list_calendars(); // no check perm as p_admin only
$smarty->assign_by_ref('calendars', $calendars['data']);

include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-calendar_import.tpl');
$smarty->display("tiki.tpl");
