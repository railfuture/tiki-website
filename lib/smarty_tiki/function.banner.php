<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.banner.php 42060 2012-06-24 15:01:32Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_banner($params, $smarty)
{
    global $bannerlib;include_once('lib/banners/bannerlib.php');
	$default = array('zone'=>'', 'target'=>'', 'id'=>'');
	$params = array_merge($default, $params);

    extract($params);

    if (empty($zone) && empty($id)) {
        trigger_error("assign: missing 'zone' parameter");
        return;
    }
	$banner = $bannerlib->select_banner($zone, $target, $id);

    print($banner);
}
