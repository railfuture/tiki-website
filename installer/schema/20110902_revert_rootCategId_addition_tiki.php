<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20110902_revert_rootCategId_addition_tiki.php 39469 2012-01-12 21:13:48Z changi67 $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20110902_revert_rootCategId_addition_tiki($installer)
{
	$result = $installer->fetchAll("SHOW COLUMNS FROM `tiki_categories` WHERE `Field`='rootCategId'");
	if ($result) {
		$result = $installer->query("ALTER TABLE `tiki_categories` DROP COLUMN `rootCategId`;");
	}
}
