<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: block.tab.php 39469 2012-01-12 21:13:48Z changi67 $

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * \brief smarty_block_tabs : add tabs to a template
 *
 * params: TODO
 *
 * usage:
 * \code
 *	{tab name=}
 *  tab content
 *	{/tab}
 * \endcode
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_tab($params, $content, $smarty, &$repeat)
{
	global $prefs, $smarty_tabset, $cookietab, $smarty_tabset_i_tab, $tabset_index;
	
	if ( $repeat ) {
		return;
	} else {
		$print_page = $smarty->getTemplateVars('print_page');

		if ($print_page != 'y') {
			$smarty_tabset_i_tab = count($smarty_tabset[$tabset_index]['tabs']) + 1;
			if ( !empty($params['name'])) {
				$smarty_tabset[$tabset_index]['tabs'][] = $params['name'];
			} else {
				$smarty_tabset[$tabset_index]['tabs'][] = $params['name'] = "tab"+$smarty_tabset_i_tab;
			}
		}
		
		$ret = "<a name='tab_a_$smarty_tabset_i_tab'></a>";
		$ret .= "<fieldset ";
		if ($prefs['feature_tabs'] == 'y' && $cookietab != 'n' && $print_page != 'y') {
   			$ret .= "class='tabcontent content$smarty_tabset_i_tab' style='clear:both;display:".($smarty_tabset_i_tab == $cookietab ? 'block' : 'none').";'>";
		} else {
			$ret .= "id='content$smarty_tabset_i_tab'>";
		}
		if ($prefs['feature_tabs'] != 'y' || $cookietab == 'n' || $print_page == 'y') {
     		$ret .= '<legend class="heading"><a href="#"' . ($prefs['javascript_enabled'] === 'y' ? ' onclick="$(\'>:not(legend)\', $(this).parents(\'fieldset\')).toggle();return false;"' : '') . '><span>'.$params['name'].'</span></a></legend>';
		}
	
		$ret .= "$content</fieldset>";
		
		return $ret;
	}
}
