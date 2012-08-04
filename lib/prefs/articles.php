<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: articles.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_articles_list()
{
	return array(
		'articles_feature_copyrights' => array(
			'name' => tra('Articles'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_articles',
			),
			'default' => 'n',
		),
	);
}
