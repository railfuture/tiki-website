<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-menu.php 40317 2012-03-23 11:50:01Z jyhem $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_menu_info()
{
	return array(
		'name' => tra('Menu'),
		'description' => tra('Displays a menu or a structure as a menu.'),
		'params' => array(
			'id' => array(
				'name' => tra('Menu'),
				'description' => tra('Identifier of a menu (from tiki-admin_menus.php)'),
				'filter' => 'int',
			),
			'structureId' => array(
				'name' => tra('Structure'),
				'description' => tra('Identifier of a structure of wiki pages (name or number from tiki-admin_structures.php)'),
				'filter' => 'text',
			),
			'type' => array(
				'name' => tra('Type'),
				'description' => tra('Direction for menu: horiz or vert (default vert)'),
				'filter' => 'text',
			),
			'css' => array(
				'name' => tra('CSS/Superfish'),
				'description' => tra('Use CSS Superfish menu. y|n (default y)'),
				'filter' => 'alpha',
			),
			'menu_id' => array(
				'name' => tra('DOM #id'),
				'description' => tra('Id of the menu in the DOM'),
			),
			'menu_class' => array(
				'name' => tra('CSS class'),
				'description' => tra('Class of the menu container'),
				'filter' => 'text',
			),
			'sectionLevel' => array(
				'name' => tra('Limit low visibles levels'),
				'description' => tra('All the submenus beginning at this level will be displayed if the url matches one of the option of this level or above or below.'),
				'filter' => 'int',
			),
			'toLevel' => array(
				'name' => tra('Limit top visible levels'),
				'description' => tra('Do not display options higher than this level.'),
				'filter' => 'int',
			),
			'link_on_section' => array(
				'name' => tra('Link on Section'),
				'description' => tra('Create links on menu sections') . ' ' . tra('(y/n default y)'),
				'filter' => 'alpha',
			),
			'translate' => array(
				'name' => tra('Translate'),
				'description' => tra('Translate labels') . ' ' . tra('(y/n default y)'),
				'filter' => 'alpha',
			),
			'menu_cookie' => array(
				'name' => tra('Menu Cookie'),
				'description' => tra('Open the menu to show current option if possible') . ' ' . tra('(y/n default y)'),
				'filter' => 'alpha',
			),
		)
	);
}

function module_menu($mod_reference, $module_params)
{
	global $smarty;
	$smarty->assign('module_error', '');
	if (empty($module_params['id']) && empty($module_params['structureId'])) {
		$smarty->assign('module_error', tr('One of these parameters has to be set:') . ' ' . tr('Menu') . ', ' . tr('Structure') . '.');
	}
	if (!empty($module_params['structureId'])) {
		global $structlib; include_once('lib/structures/structlib.php');

		if (empty($module_params['title'])) {
			$smarty->assign('tpl_module_title', $module_params['structureId']);
		}
	}
	$smarty->assign('module_type', empty($module_params['css']) || $module_params['css'] === 'y' ? 'cssmenu' : 'menu');
}
