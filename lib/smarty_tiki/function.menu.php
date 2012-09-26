<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.menu.php 42510 2012-08-01 19:49:08Z robertplummer $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/* params
 * - link_on_section
 * - css = use suckerfish menu
 * - type = vert|horiz
 * - id = menu ID (mandatory)
 * - translate = y|n , n means no option translation (default y)
 * - menu_cookie=y|n (default y) n, it will automatically open the submenu the url is in
 * - sectionLevel: displays from this level only
 * - toLevel : displays to this level only
 */
function smarty_function_menu($params, $smarty)
{
	global $headerlib, $prefs;

	$default = array('css' => 'y');
	if (isset($params['params'])) {
		$params = array_merge($params, $params['params']);
		unset($params['params']);
	}
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);

	if (empty($link_on_section) || $link_on_section == 'y') {
		$smarty->assign('link_on_section', 'y');
	} else {
		 $smarty->assign('link_on_section', 'n');
	}
	if (empty($translate)) {
		$translate = 'y';
	}
	$smarty->assignByRef('translate', $translate);
	if (empty($menu_cookie)) {
		$menu_cookie = 'y';
	}
	$smarty->assignByRef('menu_cookie', $menu_cookie);
	if ($css !== 'n' && $prefs['feature_cssmenus'] == 'y' && $drilldown != 'y') {
		static $idCssmenu = 0;
		if (empty($type)) {
			$type = 'vert';
		}
		$css = "cssmenu_$type.css";
		$headerlib->add_jsfile('lib/menubuilder/menu.js');
		$tpl = 'tiki-user_cssmenu.tpl';
		$smarty->assign('menu_type', $type);
		if (! isset($css_id)) {//adding $css_id parameter to customize menu id and prevent automatic id renaming when a menu is removed
			$smarty->assign('idCssmenu', $idCssmenu++);	
		} else {
			$smarty->assign('idCssmenu', $css_id);
		}
	} elseif ($drilldown == 'y') {
		$tpl = 'tiki-user_drilldownmenu.tpl';
	} else {
		$tpl = 'tiki-user_menu.tpl';
	}

	list($menu_info, $channels) = get_menu_with_selections($params);

	$smarty->assign('menu_channels', $channels['data']);
	$smarty->assign('menu_info', $menu_info);
	$data = $smarty->fetch($tpl);
	$data = preg_replace('/<ul>\s*<\/ul>/', '', $data);
	$data = preg_replace('/<ol>\s*<\/ol>/', '', $data);
	if ($prefs['mobile_feature'] !== 'y' || $prefs['mobile_mode'] !== 'y') {
		return '<nav class="role_navigation">' . $data . '</nav>';
	} else {
		$data = preg_replace('/<ul ([^>]*)>/Umi', '<ul $1 data-role="listview" data-theme="'.$prefs['mobile_theme_menus'].'">', $data, 1);
		// crude but effective hack for loading menu items via ajax - hopefully to be replaced by something more elegant soon
		$data = preg_replace('/<a ([^>]*)>/Umi', '<a $1 rel="external">', $data);
		return $data;
	}
}

function compare_menu_options($a, $b)
{
	return strcmp(tra($a['name']), tra($b['name']));
}

function get_menu_with_selections($params) {
	global $tikilib, $user, $prefs;
	global $menulib; include_once('lib/menubuilder/menulib.php');
	global $cachelib; include_once('lib/cache/cachelib.php');
	$cacheName = isset($prefs['mylevel']) ? $prefs['mylevel'] : 0;
	$cacheName .= '_'.$prefs['language'].'_'.md5(implode("\n", $tikilib->get_user_groups($user)));

	extract($params, EXTR_SKIP);

	if (isset($structureId)) {
		$cacheType = 'structure_'.$structureId;
	} else {
		$cacheType = 'menu_'. $id .'_';
	}

	if ( $cdata = $cachelib->getSerialized($cacheName, $cacheType) ) {
		list($menu_info, $channels) = $cdata;
	} elseif (!empty($structureId)) {
		global $structlib; include_once('lib/structures/structlib.php');

		if (!is_numeric($structureId)) {
			$structureId = $structlib->get_struct_ref_id($structureId);
		}

		$channels = $structlib->build_subtree_toc($structureId);
		$structure_info =  $structlib->s_get_page_info($structureId);
		$channels = $structlib->to_menu($channels, $structure_info['pageName']);
		$menu_info = array('type'=>'d', 'menuId'=> "s_$structureId", 'structure' => 'y');
		//echo '<pre>'; print_r($channels); echo '</pre>';
	} else if (!empty($id)) {
		$menu_info = $menulib->get_menu($id);
		$channels = $menulib->list_menu_options($id, 0, -1, 'position_asc', '', '', isset($prefs['mylevel'])?$prefs['mylevel']:0);
		$channels = $menulib->sort_menu_options($channels);
		$cachelib->cacheItem($cacheName, serialize(array($menu_info, $channels)), $cacheType);
	} else {
		return '<span class="error">menu function: Menu or Structure ID not set</span>';
	}
	$channels = $menulib->setSelected($channels, isset($sectionLevel)?$sectionLevel:'', isset($toLevel)?$toLevel: '', $params);

	return array($menu_info, $channels);
}