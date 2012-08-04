<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: challenge.php 40059 2012-03-07 06:25:54Z pkdille $

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

// If we are processing a login then do not generate the challenge
// if we are in any other case then yes.
if ( ! strstr($_SERVER['REQUEST_URI'], 'tiki-login') ) {
	$chall = $userlib->generate_challenge();

	$_SESSION['challenge'] = $chall;
	$smarty->assign('challenge', $chall);
}

