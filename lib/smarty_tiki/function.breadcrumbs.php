<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.breadcrumbs.php 42271 2012-07-08 18:10:47Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_breadcrumbs($params, $smarty)
{
	global $prefs;
    extract($params);
	
    if (empty($crumbs)) {
        trigger_error("assign: missing 'crumbs' parameter");
        return;
    }
    if (empty($loc)) {
        trigger_error("assign: missing 'loc' parameter");
        return;
    }
    if ($type === 'pagetitle' && $prefs['site_title_breadcrumb'] === 'y') {
    	$type = 'desc';
    }
	$showLinks = empty($params['showLinks']) || $params['showLinks'] == 'y';
    $text_to_display = '';
    switch ($type) {
		case 'invertfull':
			$text_to_display = breadcrumb_buildHeadTitle(array_reverse($crumbs));
      break;
        case 'fulltrail':
			$text_to_display = breadcrumb_buildHeadTitle($crumbs);
            break;
        case 'pagetitle':
			$text_to_display = breadcrumb_getTitle($crumbs, $loc);
            break;
        case 'desc':
			$text_to_display = breadcrumb_getDescription($crumbs, $loc);
            break;
        case 'trail':
        default:
			$text_to_display = breadcrumb_buildTrail($crumbs, $loc, $showLinks);
            break;
    }
    if (!empty($machine_translate)) {
    	require_once('lib/core/Multilingual/MachineTranslation/GoogleTranslateWrapper.php');
		$translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper($source_lang, $target_lang);
		$text_to_display = $translator->translateText($text_to_display);	
    }
    print($text_to_display);

}
