<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-switch_perspective.php 40234 2012-03-17 19:17:41Z changi67 $

require_once 'tiki-setup.php';
require_once 'lib/perspectivelib.php';

$access->check_feature('feature_perspective');

$_SESSION['current_perspective'] = 0;


if ( isset($_REQUEST['perspective']) ) {
	$perspectivelib->set_perspective($_REQUEST['perspective']);
}

if ( isset($_REQUEST['back']) && isset($_SERVER['HTTP_REFERER']) ) {
	header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
	header('Location: index.php');
}

// EOF
