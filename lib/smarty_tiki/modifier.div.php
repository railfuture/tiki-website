<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.div.php 39469 2012-01-12 21:13:48Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 *
 * -------------------------------------------------------------
 */
function smarty_modifier_div($string, $num, $max=10)
{
	if ($num == 0) 
		return 0;

	if (ceil(strlen($string) / $num) > $max)
		return $max;

	return ceil(strlen($string) / $num);
}
