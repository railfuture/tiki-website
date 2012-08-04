<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.tr_if.php 39469 2012-01-12 21:13:48Z changi67 $

// Translate only if feature_multilingual is on

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_tr_if($source)
{
	global $prefs;
	if ($prefs['language'] != 'en') {
		include_once('lib/init/tra.php');
		return tra($source);
	} else {
		return $source;
	}
}
