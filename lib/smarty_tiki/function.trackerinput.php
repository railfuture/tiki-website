<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.trackerinput.php 39469 2012-01-12 21:13:48Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_trackerinput( $params, $smarty )
{
	$trklib = TikiLib::lib('trk');

	$field = $params['field'];
	$item = isset($params['item']) ? $params['item'] : array();

	$handler = $trklib->get_field_handler($field, $item);

	if ($handler) {
		$context = $params;
		unset($context['item']);
		unset($context['field']);

		$desc = '';
		if (isset($params['showDescription']) && $params['showDescription'] == 'y') {
			$desc = $params['field']['description'];
			if ($params['field']['descriptionIsParsed'] == 'y') {
				$desc = TikiLib::lib('parser')->parse_data($desc);
			}
			if (!empty($desc)) $desc = '<div class="description">'.$desc.'</div>';
		}

		return $handler->renderInput($context).$desc;
	}
}

