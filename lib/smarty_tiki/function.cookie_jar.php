<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.cookie_jar.php 39469 2012-01-12 21:13:48Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_function_cookie_jar: Get a cookie value from the Tiki Cookie Jar
 *
 * params:
 *	- name: Name of the cookie
 */
function smarty_function_cookie_jar($params, $smarty)
{
	if ( empty($params['name']) ) return;
	return getCookie($params['name']);
}
