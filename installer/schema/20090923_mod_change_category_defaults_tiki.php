<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20090923_mod_change_category_defaults_tiki.php 39469 2012-01-12 21:13:48Z changi67 $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20090923_mod_change_category_defaults_tiki($installer)
{
	$result = $installer->query("select moduleId, params from tiki_modules where name='change_category'; ");
	while ($row = $result->fetchRow()) {
		$params = $row['params'];
		if (strpos($params, "multiple=") === false) {
			if ($params) $params .= "&";
			$params .= "multiple=n";
			$installer->query("update tiki_modules set params='" . $params . "' where moduleId=" . $row['moduleId'] . "; ");
		}
	}
}
