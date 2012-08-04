<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_tabs.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_tabs_info()
{
	return array(
		'name' => tra('Tabs'),
		'documentation' => 'PluginTabs',
		'description' => tra('Arrange content in tabs'),
		'prefs' => array( 'wikiplugin_tabs' ),
		'body' => tra('Tabs content separated by /////'),
		'icon' => 'img/icons/tab_edit.png',
		'filter' => 'wikicontent',
		'tags' => array( 'basic' ),
		'params' => array(
			'name' => array(
				'required' => false,
				'name' => tra('Tabset Name'),
				'description' => tra('Unique tabset name (if you want it to remember its last state). Ex: user_profile_tabs'),
				'default' => '',
			),
			'tabs' => array(
				'required' => true,
				'name' => tra('Tab Titles'),
				'description' => tra('Pipe separated list of tab titles. Ex: tab 1|tab 2|tab 3'),
				'default' => '',
			),
			'toggle' => array(
				'required' => false,
				'name' => tra('Toggle Tabs'),
				'description' => tra('Allows to toggle from tabs to no tabs view'),
				'default' => 'y',
				'options' => array (
					array('value' => 'y' , 'text' => tra('Yes')),
					array('value' => 'n', 'text' => tra('No')),
				),
			),
			'inside_pretty' => array(
				'required' => false,
				'name' => tra('Inside Pretty Tracker'),
				'description' => tra('Parse pretty tracker variables within tabs'),
				'default' => 'n',
				'options' => array (
					array('value' => 'n', 'text' => tra('No')),
					array('value' => 'y' , 'text' => tra('Yes')),
				),
			),		
		),
	);
}

function wikiplugin_tabs($data, $params)
{
	global $tikilib, $smarty;
	if (!empty($params['name'])) {
		$tabsetname = $params['name'];
	} else {
		$tabsetname = '';
	}
	
	if (!empty($params['toggle'])) {
		$toggle = $params['toggle'];
	} else {
		$toggle = 'y';
	}

	if (!empty($params['inside_pretty']) && $params['inside_pretty'] == 'y') {
		$inside_pretty = true;
	} else {
		$inside_pretty = false;
	}
	
	$tabs = array();
	if (!empty($params['tabs'])) {
		$tabs = explode('|', $params['tabs']);
	} else {
		return "''".tra("No tab title specified. At least one has to be set to make the tabs appear.")."''";
	}
	if (!empty($data)) {
		$data = $tikilib->parse_data($data, array('suppress_icons' => true, 'inside_pretty' => $inside_pretty));
		$tabData = explode('/////', $data);
	}
	
	$smarty->assign('tabsetname', $tabsetname);
	$smarty->assign_by_ref('tabs', $tabs);
	$smarty->assign('toggle', $toggle);
	$smarty->assign_by_ref('tabcontent', $tabData);

	$content = $smarty->fetch('wiki-plugins/wikiplugin_tabs.tpl');

	return $content;
}
