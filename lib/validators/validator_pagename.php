<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: validator_pagename.php 39469 2012-01-12 21:13:48Z changi67 $

function validator_pagename($input, $parameter = '', $message = '')
{
	global $tikilib, $prefs;
	if ($parameter == 'not') {
		if ($tikilib->page_exists($input)) {
			return tra("Page already exists");
		}
	} else {
		if (!$tikilib->page_exists($input)) {
			return tra("Page does not exist");
		}
	}
	return true; 
}
