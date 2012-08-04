<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: user_prefs.php 40059 2012-03-07 06:25:54Z pkdille $

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

// Handle the current user prefs in session
if ( ! isset($_SESSION['u_info']) || $_SESSION['u_info']['login'] != $user ) {
	$_SESSION['u_info'] = array();
	$_SESSION['u_info']['login'] = $user;
	$_SESSION['u_info']['group'] = ( $user ) ? $userlib->get_user_default_group($user) : '';
	if (empty($user)) {
		$_SESSION['preferences'] = array(); // For anonymous, store some preferences like the theme in the session.
	}
}

// Define the globals $u_info array for use in php / smarty
$u_info =& $_SESSION['u_info'];
$smarty->assign_by_ref('u_info', $u_info);

$smarty->assign_by_ref('user', $user);
$user_preferences = array(); // Used for cache

if ( $user ) {
	$default_group = $group = $_SESSION['u_info']['group'];
	$smarty->assign('group', $group); // do not use by_ref as $group can be changed in the .php
	$smarty->assign('default_group', $group);

	// Initialize user preferences

	// Get all user prefs in one query
	$tikilib->get_user_preferences($user);

	// Prefs overriding
	$prefs = array_merge($prefs, $user_preferences[$user]);

	// Copy some user prefs that doesn't have the same name as the related site pref
	//   in order to symplify the overriding and the use
	if ( $prefs['change_theme'] == 'y') {
		if ( !empty($prefs['theme']) ) {
			$prefs['style'] = $prefs['theme'];
			if ( isset($prefs['theme-option']) ) {
				$prefs['style_option'] = $prefs['theme-option'];
			}
		}
	}

	// Set the userPage name for this user since other scripts use this value.
	$userPage = $prefs['feature_wiki_userpage_prefix'].$user;
	$exist = $tikilib->page_exists($userPage);
	$smarty->assign('userPage', $userPage);
	$smarty->assign('userPage_exists', $exist);

} else {
	$prefs = array_merge($prefs, $_SESSION['preferences']);
	$allowMsgs = 'n';
}

$smarty->assign('IP', $tikilib->get_ip_address());

if ($prefs['users_prefs_display_timezone'] == 'Site'
			|| (isset($user_preferences[$user]['display_timezone'])
			&& $user_preferences[$user]['display_timezone'] == 'Site')
) {
	// Stay in the time zone of the server
	$prefs['display_timezone'] = $prefs['server_timezone'];
} elseif ( ! isset($user_preferences[$user]['display_timezone'])
					|| $user_preferences[$user]['display_timezone'] == ''
					|| $user_preferences[$user]['display_timezone'] == 'Local'
) {
	// If the display timezone is not known ...
	if ( isset($_COOKIE['local_tz'])) {
		//   ... we try to use the timezone detected by javascript and stored in cookies
		if (TikiDate::TimezoneIsValidId($_COOKIE['local_tz'])) {
			$prefs['display_timezone'] = $_COOKIE['local_tz'];
		} elseif ( $_COOKIE['local_tz'] == 'HAEC' ) {
			// HAEC, returned by Safari on Mac, is not recognized as a DST timezone (with daylightsavings)
			//  ... So use one equivalent timezone name
			$prefs['display_timezone'] = 'Europe/Paris';
		} else {
			$prefs['display_timezone'] = $prefs['server_timezone'];
		}
	} else {
		// ... and we fallback to the server timezone if the cookie value is not available
		$prefs['display_timezone'] = $prefs['server_timezone'];
	}
}
