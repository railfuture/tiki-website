<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.ed.php 42060 2012-06-24 15:01:32Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_ed($params, $smarty)
{
    global $tikilib;
    extract($params);
    // Param = zone

    if (empty($id)) {
        trigger_error("ed: missing 'id' parameter");
        return;
    }

    print($banner);
}
