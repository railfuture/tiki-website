<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.max_user_inscriptions.php 39469 2012-01-12 21:13:48Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     max_user_inscriptions
 * Purpose:  to use with the tracker field type "User inscription"
 * -------------------------------------------------------------
 */
function smarty_modifier_max_user_inscriptions( $text )
{
  return substr($text, 0, strpos($text, '#'));
}
