<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: messu.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_messu_list()
{
	return array(
		'messu_mailbox_size' => array(
			'name' => tra('Maximum mailbox size (messages, 0=unlimited)'),
			'description' => tra('Maximum mailbox size (messages, 0=unlimited)'),
			'type' => 'text',
			'size' => '10',
			'filter' => 'digits',
			'dependencies' => array(
				'feature_messages',
			),
			'default' => '0',
		),
		'messu_archive_size' => array(
			'name' => tra('Maximum mail archive size (messages, 0=unlimited)'),
			'description' => tra('Maximum mail archive size (messages, 0=unlimited)'),
			'type' => 'text',
			'size' => '10',
			'filter' => 'digits',
			'dependencies' => array(
				'feature_messages',
			),
			'default' => '200',
		),
		'messu_sent_size' => array(
			'name' => tra('Maximum sent box size (messages, 0=unlimited)'),
			'description' => tra('Maximum sent box size (messages, 0=unlimited)'),
			'type' => 'text',
			'size' => '10',
			'filter' => 'digits',
			'dependencies' => array(
				'feature_messages',
			),
			'default' => '200',
		),
	);
}
