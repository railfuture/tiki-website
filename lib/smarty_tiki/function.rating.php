<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.rating.php 41350 2012-05-05 02:07:59Z chibaguy $

function smarty_function_rating( $params, $smarty )
{
	global $ratinglib;
	require_once 'lib/rating/ratinglib.php';

	if ( ! isset($params['type'], $params['id']) ) {
		return tra('No object information provided for rating.');
	}

	$type = $params['type'];
	$id = $params['id'];
	if ( isset($params['changemandated']) && $params['changemandated'] == 'y' ) {
		$changemandated = true; // needed to fix multiple submission problem in comments
	} else {
		$changemandated = false;
	}

	if ( isset( $_REQUEST['rating_value'][$type][$id], $_REQUEST['rating_prev'][$type][$id] ) ) {
		$value = $_REQUEST['rating_value'][$type][$id];
		$prev = $_REQUEST['rating_prev'][$type][$id];
		if ( ( !$changemandated || $value != $prev ) && $ratinglib->record_vote($type, $id, $value) ) {

			// Handle type-specific actions
			if ( $type == 'comment' ) {
				global $commentslib, $user; require_once 'lib/comments/commentslib.php';

				if ( $user ) {
					$commentslib->vote_comment($id, $user, $value);
				}
			}
		} elseif ( $value != $prev ) {
			return tra('An error occurred.');
		}
	}

	$vote = $ratinglib->get_vote($type, $id);

	$smarty->assign('rating_type', $type);
	$smarty->assign('rating_id', $id);
	$smarty->assign('rating_options', $ratinglib->get_options($type));
	$smarty->assign('current_rating', $vote);
	return $smarty->fetch('rating.tpl');
}

