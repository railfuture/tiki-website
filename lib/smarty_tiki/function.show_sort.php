<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.show_sort.php 39469 2012-01-12 21:13:48Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_show_sort($params, $smarty)
{
	global $url_path;

	if ( isset($_REQUEST[$params['sort']]) ) {
		$p = $_REQUEST[$params['sort']];
	} elseif ( $s = $smarty->getTemplateVars($params['sort']) ) {
		$p = $s;
	}

	if ( isset($params['sort']) and isset($params['var']) and isset($p) ) {
		$prop = substr($p, 0, strrpos($p, '_'));
		$order = substr($p, strrpos($p, '_') + 1);

		if ( strtolower($prop) == strtolower(trim($params['var'])) ) {
			$smarty->loadPlugin('smarty_function_icon');
			$icon_params = array('alt' => tra('Invert Sort'), 'style' => 'vertical-align:middle');

			switch( $order ) {
				case 'asc':
				case 'nasc':
					$icon_params['_id'] = 'resultset_up';
					return smarty_function_icon($icon_params, $smarty);
								break;
				case 'desc':
				case 'ndesc':
					$icon_params['_id'] = 'resultset_down';
					return smarty_function_icon($icon_params, $smarty);
								break;
			}
		}
	}
}
