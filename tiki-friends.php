<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-friends.php 39467 2012-01-12 19:47:28Z changi67 $

require_once('tiki-setup.php');
include_once('lib/messu/messulib.php');

$access->check_user($user);
$access->check_feature('feature_friends');

// TODO: all messages should be translated to receiver language, not sender.
if (isset($_REQUEST['request_friendship'])) {
    $friend = $_REQUEST['request_friendship'];
    
	if ($userlib->user_exists($friend)) {
		if (!$tikilib->verify_friendship($friend, $user)) {
			$userlib->request_friendship($user, $friend);
			$lg = $tikilib->get_user_preference($friend, "language", $prefs['site_language']);
			$smarty->assign('msg', sprintf(tra("Friendship request sent to %s"), $friend));
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$machine = $tikilib->httpPrefix(true). $foo["path"];
			$smarty->assign('server_name', $machine);
			$messulib->post_message(
							$friend,
							$user,
							$friend,
							'',
							$smarty->fetchLang($lg, 'mail/new_friend_invitation_subject.tpl'),
							$smarty->fetchLang($lg, 'mail/new_friend_invitation.tpl'),
							3
			);
	
		} else {
		    $smarty->assign('msg', sprintf(tra("You're already friend of %s"), $_REQUEST['request_friendship']));
		    $smarty->display("error.tpl");
		    die;
		}
	} else {
		$smarty->assign('msg', tra("Invalid username"));
		$smarty->display("error.tpl");
		die;
	}
} elseif (isset($_REQUEST['accept'])) {
	$friend = $_REQUEST['accept'];
	$userlib->accept_friendship($user, $friend);
	$lg = $tikilib->get_user_preference($friend, "language", $prefs['site_language']);
	$smarty->assign('msg', sprintf(tra('Accepted friendship request from %s'), $friend));

	$messulib->post_message(
					$friend,
					$user,
					$friend,
					'',
					tra("I have accepted your friendship request!", $lg),
					'', // Do we need a message?
					3
	);
} elseif (isset($_REQUEST['refuse'])) {
	$friend = $_REQUEST['refuse'];
	$userlib->refuse_friendship($user, $friend);
	$lg = $tikilib->get_user_preference($friend, "language", $prefs['site_language']);
	$smarty->assign('msg', sprintf(tra('Refused friendship request from %s'), $friend));
	
	// Should we send a message, or that would intimidate refusing friendships?
	// TODO: make it optional
	$messulib->post_message(
					$friend,
					$user,
					$friend,
					'',
					tra("I have refused your friendship request.", $lg),
					'',
					3
	);

} elseif (isset($_REQUEST['cancel_waiting_friendship'])) {
	$friend = $_REQUEST['cancel_waiting_friendship'];
	$userlib->refuse_friendship($friend, $user);
	$lg = $tikilib->get_user_preference($friend, "language", $prefs['site_language']);
	$smarty->assign('msg', sprintf(tra('Canceled friendship request with %s'), $friend));
	
	// Should we send a message, or that would intimidate refusing friendships?
	// TODO: make it optional
	$messulib->post_message(
					$friend,
					$user,
					$friend,
					'',
					tra("I have canceled my friendship request.", $lg),
					'',
					3
	);
			    
} elseif (isset($_REQUEST['break'])) { 
	$friend = $_REQUEST['break'];
	$userlib->break_friendship($user, $friend);
	$lg = $tikilib->get_user_preference($friend, "language", $prefs['site_language']);
	$smarty->assign('msg', sprintf(tra('Broke friendship with %s'), $friend));
	
	// Should we send a message, or that would intimidate user?
	// TODO: make it optional
	$messulib->post_message(
					$friend,
					$user,
					$friend,
					'',
					tra('I have broken our friendship!', $lg),
					'',
					3	
	);

}

if (!isset($_REQUEST["sort_mode"])) {
  $sort_mode = $prefs['user_list_order'];
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 

$smarty->assign_by_ref('sort_mode', $sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find', $find);

$smarty->assign('pending_requests', $userlib->list_pending_friendship_requests($user));
$smarty->assign('waiting_requests', $userlib->list_waiting_friendship_requests($user));

$listpages = $tikilib->list_user_friends($user, $offset, $maxRecords, $sort_mode, $find);

$smarty->assign_by_ref('cant_pages', $listpages["cant"]);

$smarty->assign_by_ref('listpages', $listpages["data"]);

$section='friends';
include_once('tiki-section_options.php');

$smarty->assign('mid', 'tiki-friends.tpl');
$smarty->display("tiki.tpl");
