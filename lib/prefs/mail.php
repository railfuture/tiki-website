<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mail.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_mail_list()
{
	return array(
		'mail_crlf' => array(
			'name' => tra('Mail end of line'),
			'description' => tra('Mail end of line'),
			'type' => 'list',
			'options' => array(
				'CRLF' => tra('CRLF (standard)'),
				'LF' => tra('LF (some Unix MTA)'),
			),
			'default' => 'LF',
		),
	);
}
