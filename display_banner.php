<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: display_banner.php 39467 2012-01-12 19:47:28Z changi67 $

// Only to be called from edit_banner or view_banner to display the banner without adding
// impressions to the banner
if (!isset($_REQUEST["id"])) {
	die;
}

require_once ('tiki-setup.php');
include_once ('lib/banners/bannerlib.php');

if (!isset($bannerlib)) {
	$bannerlib = new BannerLib;
}

// CHECK FEATURE BANNERS HERE
$access->check_feature('feature_banners');

$data = $bannerlib->get_banner($_REQUEST["id"]);
$id = $data["bannerId"];

switch ($data["which"]) {
	case 'useHTML':
		$raw = $data["HTMLData"];
    	break;

	case 'useImage':
		$raw = "<img border=\"0\" src=\"banner_image.php?id=" . $id . "\" />";
    	break;

	case 'useFixedURL':
		$fp = fopen($data["fixedURLData"], "r");
		if ($fp) {
			$raw = '';
			while (!feof($fp)) {
				$raw .= fread($fp, 8192);
			}
		}

		fclose($fp);
    	break;

	case 'useText':
		$raw = $data["textData"];
    	break;
}
print ($raw);
