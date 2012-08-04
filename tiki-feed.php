<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-feed.php 40193 2012-03-15 18:00:59Z robertplummer $

$_SERVER['HTTP_USER_AGENT'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

require_once ('tiki-setup.php');

$_REQUEST['type'] = (!empty($_REQUEST['type']) ? $_REQUEST['type'] : 'html');

if ($_REQUEST['type'] == 'html') {
	$access->check_feature('feature_htmlfeed');
	$feed = new Feed_Html();
	print_r(json_encode($feed->feed()));
}