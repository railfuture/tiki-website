<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-kaltura_upload.php 39467 2012-01-12 19:47:28Z changi67 $

require_once 'tiki-setup.php';
require_once 'lib/videogals/videogallib.php';

$access->check_permission(array('tiki_p_upload_videos'));
//get_strings tra('Upload Media')

$cwflashVars = array();
$cwflashVars["uid"]               = $kuser;
$cwflashVars["partnerId"]         = $prefs['partnerId'];
$cwflashVars["ks"]                = $ksession;
$cwflashVars["afterAddEntry"]     = "afterAddEntry";
$cwflashVars["close"]             = "onContributionWizardClose";
$cwflashVars["showCloseButton"]   = false;
$cwflashVars["Permissions"]       = 1;		// 1=public, 2=private, 3=group, 4=friends

$smarty->assign_by_ref('cwflashVars', json_encode($cwflashVars));

$count = 0;
if ($_REQUEST['kcw']) {
	$count = count($_REQUEST['entryId']);
	$smarty->assign_by_ref('count', $count);
}
// Display the template
$smarty->assign('mid', 'tiki-kaltura_upload.tpl');
$smarty->display("tiki.tpl");
