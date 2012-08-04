<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: include_community.php 39469 2012-01-12 21:13:48Z changi67 $

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

if (isset($_REQUEST['userfeatures'])) {
	check_ticket('admin-inc-community');
}

// Users Defaults
if (isset($_REQUEST['users_defaults'])) {
	check_ticket('admin-inc-login');

	foreach ($_prefs as $pref) {
		simple_set_value($pref);
	}
}

ask_ticket('admin-inc-community');
