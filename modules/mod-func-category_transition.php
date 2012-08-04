<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-category_transition.php 40201 2012-03-15 20:18:52Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_category_transition_info()
{
	return array(
		'name' => tra('Category Transitions'),
		'description' => tra('Displays controls to trigger category transitions and change the page\'s state according to predefined rules.'),
		'prefs' => array('feature_category_transition'),
		'params' => array(
		),
	);
}

function module_category_transition($mod_reference, $module_params)
{
	global $smarty, $cat_type, $cat_objid, $modlib;

	if ($cat_type && $cat_objid) {
		$smarty->assign('objType', $cat_type);
		$smarty->assign('objId', $cat_objid);

		require_once 'lib/transitionlib.php';
		$transitionlib = new TransitionLib('category');

		if (isset($_POST['transition'])) {
			$transitionlib->triggerTransition($_POST['transition'], $cat_objid, $cat_type);
			header('Location: ' . $_SERVER['REQUEST_URI']);
			exit;
		}

		$transitions = $transitionlib->getAvailableTransitions($cat_objid, $cat_type);
		$smarty->assign('mod_transitions', $transitions);

	} else if ($modlib->is_admin_mode(true)) {	// add a dummy transition to display on the module admin page

		$smarty->assign(
						'mod_transitions',
						array(array(
								'enabled' => true,
								'transitionId' => 0,
								'name' => tra('Example Transition')
						))
		);
	}
}

