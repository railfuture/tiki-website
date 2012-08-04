<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_bigbluebutton.php 41789 2012-06-05 15:44:46Z xavidp $

function wikiplugin_bigbluebutton_info()
{
	return array(
		'name' => tra('BigBlueButton'),
		'documentation' => 'PluginBigBlueButton',
		'description' => tra('Starts a video/audio/chat/presentation session using BigBlueButton'),
		'format' => 'html',
		'prefs' => array( 'wikiplugin_bigbluebutton', 'bigbluebutton_feature' ),
		'icon' => 'img/icons/webcam.png',
		'tags' => array( 'basic' ),		
		'params' => array(
			'name' => array(
				'required' => true,
				'name' => tra('Meeting'),
				'description' => tra('MeetingID for BigBlueButton. This is a 5 digit number, starting with a 7. Ex.: 77777 or 71111.'),
				'filter' => 'text',
				'default' => ''
			),
			'prefix' => array(
				'required' => false,
				'name' => tra('Anonymous prefix'),
				'description' => tra('Unregistered users will get this token prepended to their name.'),
				'filter' => 'text',
				'default' => ''
			),
			'welcome' => array(
				'required' => false,
				'name' => tra('Welcome Message'),
				'description' => tra('A message to be provided when someone enters the room.'),
				'filter' => 'text',
				'default' => ''
			),
			'number' => array(
				'required' => false,
				'name' => tra('Dial Number'),
				'description' => tra('The phone-in support number to join from traditional phones.'),
				'filter' => 'text',
				'default' => ''
			),
			'voicebridge' => array(
				'required' => false,
				'name' => tra('Voice Bridge'),
				'description' => tra('Code to enter for phone attendees to join the room. Typically, the same 5 digits of the MeetingID.'),
				'filter' => 'digits',
				'default' => ''
			),
			'logout' => array(
				'required' => false,
				'name' => tra('Log-out URL'),
				'description' => tra('URL to which the user will be redirected when logging out from BigBlueButton.'),
				'filter' => 'url',
				'default' => ''
			),
			'recording' => array(
				'required' => false,
				'name' => tra('Record meetings'),
				'description' => tra('Requires BBB >= 0.8.'),
				'filter' => 'int',
				'default' => 0,
				'options' => array(
					array('value' => 0, 'text' => tr('Off')),
					array('value' => 1, 'text' => tr('On')),
				),
			),
		),
	);
}

function wikiplugin_bigbluebutton( $data, $params )
{
	try {
		global $smarty, $prefs, $user;
		$bigbluebuttonlib = TikiLib::lib('bigbluebutton');
		$meeting = $params['name']; // Meeting is more descriptive than name, but parameter name was already decided.

		$smarty->assign('bbb_meeting', $meeting);
		$smarty->assign('bbb_image', parse_url($prefs['bigbluebutton_server_location'], PHP_URL_SCHEME) . '://' . parse_url($prefs['bigbluebutton_server_location'], PHP_URL_HOST) . '/images/bbb_logo.png');

		$perms = Perms::get('bigbluebutton', $meeting);

		if ( ! $bigbluebuttonlib->roomExists($meeting) ) {
			if ( ! isset($_POST['bbb']) || $_POST['bbb'] != $meeting || ! $perms->bigbluebutton_create ) {
				$smarty->assign( 'bbb_recordings', $bigbluebuttonlib->getRecordings( $meeting ) );
				return $smarty->fetch('wiki-plugins/wikiplugin_bigbluebutton_create.tpl');
			}
		}

		$params = array_merge(array('prefix' => ''), $params);

		if ( $perms->bigbluebutton_join ) {
			if ( isset($_POST['bbb']) && $_POST['bbb'] == $meeting ) {
				if ( ! $user && isset($_POST['bbb_name']) && ! empty($_POST['bbb_name']) ) {
					$_SESSION['bbb_name'] = $params['prefix'] . $_POST['bbb_name'];
				}

				// Attempt to create room made before joining as the BBB server has no persistency.
				// Prior check ensures that the user has appropriate rights to create the room in the
				// first place or that the room was already officially created and this is only a
				// re-create if the BBB server restarted.
				//
				// This avoids the issue occuring when tiki cache thinks the room exist and it's gone
				// on the other hand. It does not solve the issue if the room is lost on the BBB server
				// and tiki cache gets flushed. To cover that one, create can be granted to everyone for
				// the specific object.
				$bigbluebuttonlib->createRoom($meeting, $params);
				$bigbluebuttonlib->joinMeeting($meeting);
			}

			$smarty->assign('bbb_attendees', $bigbluebuttonlib->getAttendees($meeting));
			$smarty->assign('bbb_recordings', $bigbluebuttonlib->getRecordings($meeting));

			return $smarty->fetch('wiki-plugins/wikiplugin_bigbluebutton.tpl');

		} elseif ( $perms->bigbluebutton_view_rec ) { # Case for anonymous users with the perm to view recordings but not to join meetings
			$smarty->assign('bbb_recordings', $bigbluebuttonlib->getRecordings($meeting));

			return $smarty->fetch('wiki-plugins/wikiplugin_bigbluebutton_view_recordings.tpl');
		}
	} catch (Exception $e) {
		return WikiParser_PluginOutput::internalError(tr('BigBlueButton misconfigured or unaccessible.'));
	}
}
