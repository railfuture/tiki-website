<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: section.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_section_list()
{
	return array(
		'section_comments_parse' => array(
			'name' => tra('Parse wiki syntax in comments in all sections apart from Forums'),
			'type' => 'flag',
			'help' => 'Wiki+Syntax',
			'hint' => tra('Use "Accept wiki syntax" for forums in admin forums page'),
			'default' => 'y',		// parse wiki markup on comments in all sections
		),
	);
}
