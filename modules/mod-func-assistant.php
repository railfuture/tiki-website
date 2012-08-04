<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-assistant.php 40401 2012-03-26 04:19:20Z lindonb $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


function module_assistant_info()
{
	return array(
		'name' => tra('Tiki Assistant'),
		'description' => tra('Display an assistant to guide new Tiki admins.'),
		'prefs' => array(),
		'params' => array()
	);
}
