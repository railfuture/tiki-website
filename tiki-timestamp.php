<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-timestamp.php 40367 2012-03-25 11:40:46Z changi67 $

require_once ('tiki-setup.php');
require_once("lib/phpseclib_tiki/tikisecure.php");

global $prefs;

if ($prefs['feature_forwardlinkprotocol'] == "y") {
	$requester = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	
	if (isset($requester)) {
		if (isset($_REQUEST['hash'], $_REQUEST["clienttime"])) {
			$timestamp = TikiSecure::timestamp($_REQUEST["hash"], $_REQUEST["clienttime"], $requester);
			
		} else if ($_REQUEST['timestamp']) {
			$timestamp = TikiSecure::openTimestamp(json_decode(urldecode($_REQUEST['timestamp'])), $requester);
			
		}
		
		echo json_encode($timestamp);
		exit();
	}
	
	//We list all public keys here
}