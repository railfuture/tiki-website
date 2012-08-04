<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.norecords.php 39469 2012-01-12 21:13:48Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
	function norecords
	
	Param list :
		_colspan : How much column need to be covered
		_text : text to display, bu default => No records found.
*/

function smarty_function_norecords($params, $smarty)
{
	$html = '<tr class="even">';
	if (is_int($params["_colspan"])) {
		$html .= '<td colspan="'.$params["_colspan"].'" class="norecords">';
	} else {
		$html .= '<td class="norecords">';
	}
	if (isset($params["_text"])) {
		$html .= tra($params["_text"]);
	} else {
		$html .= tra("No records found.");
	}
	$html .= "</td></tr>";
	return $html;
}
