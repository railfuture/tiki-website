<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: cancel_membership_extension.php 39469 2012-01-12 21:13:48Z changi67 $

function payment_behavior_cancel_membership_extension( $users, $groupId )
{
	global $userlib;

	$users = (array) $users;

	foreach ( $users as $u ) {
		$attributelib = TikiLib::lib('attribute');
		$attributes = $attributelib->get_attributes('user', $u);

		foreach ($attributes as $a) {
			$attname = 'tiki.memberextend.' . $groupId; 
			if (isset($attributes[$attname])) {
				$attributelib->set_attribute('user', $u, $attname, '');
			}
		}
	}
}

