<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-gmap_usermap.php 39467 2012-01-12 19:47:28Z changi67 $

include "tiki-setup.php";
$access->check_feature('feature_gmap');
$smarty->assign('mid', 'tiki-gmap_usermap.tpl');
$smarty->display('tiki.tpl');
