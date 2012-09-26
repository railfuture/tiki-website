<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: user.php 42341 2012-07-11 11:31:30Z marclaporte $

function prefs_user_list($partial = false)
{
	
	global $prefs;
	
	$catree = array('-1' => tra('None'));

	if (! $partial && $prefs['feature_categories'] == 'y') {
		global $categlib;

		include_once ('lib/categories/categlib.php');
		$all_categs = $categlib->getCategories(NULL, true, false);

		$catree['0'] = tra('All');

		foreach ($all_categs as $categ) {
			$catree[$categ['categId']] = $categ['categpath'];
		}
	}
	
	return array(
		'user_show_realnames' => array(
			'name' => tra('Show user\'s real name instead of login (when possible)'),
			'description' => tra('Show user\'s real name instead of login (when possible)'),
			'help' => 'User+Preferences',
			'type' => 'flag',
			'default' => 'n',
		),
		'user_tracker_infos' => array(
			'name' => tra('Display UserTracker information on the user information page'),
			'description' => tra('Display UserTracker information on the user information page'),
			'help' => 'User+Tracker',
			'hint' => tra('Input the user tracker ID then field IDs to be shown, all separated by commas. Example: 1,1,2,3,4 (user tracker ID 1 followed by field IDs 1-4)'),
			'type' => 'text',
			'size' => '50',
			'dependencies' => array(
				'userTracker',
			),
			'default' => '',
		),
		'user_assigned_modules' => array(
			'name' => tra('Users can configure modules'),
			'help' => 'Users+Configure+Modules',
			'type' => 'flag',
			'default' => 'n',
		),	
		'user_flip_modules' => array(
			'name' => tra('Users can shade modules'),
			'help' => 'Users+Shade+Modules',
			'type' => 'list',
			'description' => tra('Allows users to hide/show modules.'),
			'options' => array(
				'y' => tra('Always'),
				'module' => tra('Module decides'),
				'n' => tra('Never'),
			),
			'default' => 'module',
		),
		'user_store_file_gallery_picture' => array(
			'name' => tra('Store full-size copy of avatar in file gallery'),
			'help' => 'User+Preferences',
			'type' => 'flag',
			'default' => 'n',
		),
		'user_picture_gallery_id' => array(
			'name' => tra('File gallery to store full-size copy of avatar in'),
			'description' => tra('Enter the gallery id here. Please create a dedicated gallery that is admin-only for security, or make sure gallery permissions are set so that only admins can edit.'),
			'help' => 'User+Preferences',
			'type' => 'text',
			'filter' => 'digits',
			'size' => '3',
			'default' => 0,
		),
		'user_default_picture_id' => array(
			'name' => tra('File ID of default avatar image'),
			'deacription' => tra('File ID of image to use in file gallery as the avatar if user has no avatar image in file galleries'), 
			'help' => 'User+Preferences',
			'type' => 'text',
			'filter' => 'digits',
			'size' => '5',
			'default' => 0,
			'dependencies' => array('user_store_file_gallery_picture'),
		),
		'user_who_viewed_my_stuff' => array(
			'name' => tra('Display who viewed my stuff on the user information page'),
			'description' => tra('You will need to activate tracking of views for various items in the action log for this to work'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_actionlog',
			),
			'default' => 'n',
		),
		'user_who_viewed_my_stuff_days' => array(
			'name' => tra('Number of days to consider who viewed my stuff'),
			'description' => tra('Number of days before current time to consider when showing who viewed my stuff'),
			'type' => 'text',
			'filter' => 'digit',
			'size' => '4',
			'default' => 90,
		),
		'user_who_viewed_my_stuff_show_others' => array(
			'name' => tra('Show to others who viewed my stuff on the user information page'),
			'description' => tra('Show to others who viewed my stuff on the user information page. Admins can always see this information.'),
			'type' => 'flag',
			'dependencies' => array(
				'user_who_viewed_my_stuff',
			),
			'default' => 'n',
		),
		'user_list_order' => array(
			'name' => tra('Sort Order'),
			'type' => 'list',
			'options' => $partial ? array() : UserListOrder(),
			'default' => 'score_desc',
		),
		'user_selector_threshold' => array(
			'name' => tra('Maximum number of users to show in drop down lists'),
			'description' => tra('Prevents out of memory and performance issues when user list is very large by using a jQuery autocomplete text input box.'),
			'type' => 'text',
			'size' => '5',
			'dependencies' => array('feature_jquery_autocomplete'),
		),
		'user_register_prettytracker' => array(
			'name' => tra('Use pretty trackers for registration form'),
			'help' => 'User+Tracker',
			'description' => tra('Use pretty trackers for registration form'),
			'type' => 'flag',
			'dependencies' => array(
				'userTracker',
			),
			'default' => 'n',
		),
		'user_register_prettytracker_tpl' => array(
			'name' => tra('Registration pretty tracker template'),
			'description' => tra('Use wiki page name or template file with .tpl extension'),
			'type' => 'text',
			'size' => '20',
			'dependencies' => array(
				'user_register_pretty_tracker',
			),
			'default' => '',
		),
		'user_register_prettytracker_output' => array(
			'name' => tra('Output the registration results'),
			'help' => 'User+Tracker',
			'description' => tra('Use a wiki page as template to output the registration results to'),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array(
				'userTracker',
			),
		),
		'user_register_prettytracker_outputwiki' => array(
			'name' => tra('Output registration pretty tracker template'),
			'description' => tra('Wiki page only'),
			'type' => 'text',
			'size' => '20',
			'default' => '',
			'dependencies' => array(
				'user_register_prettytracker_output',
			),
		),
		'user_register_prettytracker_outputtowiki' => array(
			'name' => tra('Page name fieldId'),
			'description' => tra('User trackers field id whose value is used as output page name'),
			'type' => 'text',
			'size' => '20',
			'default' => '',
			'dependencies' => array(
				'user_register_prettytracker_output',
			),
		),
		'user_trackersync_trackers' => array(
			'name' => tra('User tracker IDs to sync prefs from'),
			'description' => tra('Enter the IDs separated by commas of trackers to sync user prefs from'),
			'type' => 'text',
			'size' => '10',
			'dependencies' => array(
				'userTracker',
			),
			'default' => '',
		),
		'user_trackersync_realname' => array(
			'name' => tra('Tracker field IDs to sync Real Name pref from'),
			'description' => tra('Enter the IDs separated by commas in priority of being chosen, each item can concatenate multiple fields using +, e.g. 2+3,4'),
			'type' => 'text',
			'size' => '10',
			'dependencies' => array(
				'userTracker',
				'user_trackersync_trackers',
			),
			'default' => '',
		),
		'user_trackersync_geo' => array(
			'name' => tra('Synchronize long/lat/zoom to location field'),
			'description' => tra('Synchronize user geolocation prefs to main location field'),
			'type' => 'flag',
			'dependencies' => array(
				'userTracker',
				'user_trackersync_trackers',
			),
			'default' => 'n',
		),
		'user_trackersync_groups' => array(
			'name' => tra('Synchronize categories of user tracker item to user groups'),
			'description' => tra('Will add the user tracker item to the category of the same name as the user groups and vice versa'),
			'type' => 'flag',
			'dependencies' => array(
				'userTracker',
				'user_trackersync_trackers',
				'feature_categories',
			),
			'default' => 'n',
		),
		'user_trackersync_parentgroup' => array(
			'name' => tra('Put user in group only if categorized within'),
			'type' => 'list',
			'options' => $catree,
			'dependencies' => array(
				'userTracker',
				'user_trackersync_trackers',
				'user_trackersync_groups',
				'feature_categories',
			),
			'default' => -1,
		),
		'user_trackersync_lang' => array(
			'name' => tra('Change user system language when changing user tracker item language'),
			'type' => 'flag',
			'dependencies' => array(
				'userTracker',
				'user_trackersync_trackers',
			),
			'default' => 'n',
		),
		'user_selector_threshold' => array(
			'name' => tra('Maximum number of users to show in drop down lists'),
			'description' => tra('Prevents out of memory and performance issues when user list is very large by using a jQuery autocomplete text input box.'),
			'type' => 'text',
			'size' => '5',
			'dependencies' => array('feature_jquery_autocomplete'),
			'default' => 50,
		),
		'user_selector_realnames_tracker' => array(
			'name' => tra('Show user\'s real name instead of login in autocomplete selector in trackers feature'),
			'description' => tra('Use user\'s real name instead of login in autocomplete selector in trackers feature'),
			'type' => 'flag',
			'dependencies' => array('feature_jquery_autocomplete', 'user_show_realnames', 'feature_trackers'),
			'default' => 'n',
		),
		'user_selector_realnames_messu' => array(
			'name' => tra('Show user\'s real name instead of login in autocomplete selector in messaging feature'),
			'description' => tra('Use user\'s real name instead of login in autocomplete selector in messaging feature'),
			'type' => 'flag',
			'dependencies' => array('feature_jquery_autocomplete', 'user_show_realnames', 'feature_messages'),
			'default' => 'n',
		),
		'user_favorites' => array(
			'name' => tra('User Favorites'),
			'description' => tra('Allows for users to flag content as their favorite.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'user_must_choose_group' => array(
			'name' => tra('Users must choose a group at registration'),
			'description' => tra('Users cannot register without choosing one of the groups defined above.'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}

/**
 * UserListOrder computes the value list for user_list_order preference
 * 
 * @access public
 * @return array : list of values
 */
function UserListOrder()
{
	global $prefs;
	$options = array();

	if ($prefs['feature_community_list_score'] == 'y') {
		$options['score_asc'] = tra('Score ascending');
		$options['score_desc'] = tra('Score descending');
	}
	
	if ($prefs['feature_community_list_name'] == 'y') {
		$options['pref:realname_asc'] = tra('Name ascending');
		$options['pref:realname_desc'] = tra('Name descending');
	}

	$options['login_asc'] = tra('Login ascending');
	$options['login_desc'] = tra('Login descending');

	return $options;
}
