<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.content.php 42060 2012-06-24 15:01:32Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Param: 'id' or 'label'
function smarty_function_content($params, $smarty)
{
  global $dcslib; require_once 'lib/dcs/dcslib.php';

  if ( isset($params['id']) ) {
    $data = $dcslib->get_actual_content($params['id']);
  } elseif ( isset($params['label']) ) {
    $data = $dcslib->get_actual_content_by_label($params['label']);
  } else {
    trigger_error("assign: missing 'id' or 'label' parameter");
    return false;
  }

  return $data;
}
