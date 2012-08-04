<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: comments.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_comments_list()
{
	return array(
		'comments_notitle' => array(
			'name' => tra('Disable title for comments'),
			'description' => tra('Hide the title field on comments and their replies.'),
			'type' => 'flag',
			'default' => 'y',
		),
		'comments_field_email' => array(
			'name' => tra('Email field'),
			'description' => tra('Email field for comments (only for anonymous users).'),
			'type' => 'flag',
			'default' => 'n',
		),
		'comments_field_website' => array(
			'name' => tra('Website field'),
			'description' => tra('Website field for comments (only for anonymous users).'),
			'type' => 'flag',
			'default' => 'n',
		),
		'comments_vote' => array(
			'name' => tra('Use vote system for comments'),
			'description' => tra('Allows users with permission tiki_p_vote_comments to vote comments.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'comments_archive' => array(
			'name' => tra('Archive comments'),
			'description' => tra('If a comment is archived, only admins can see it'),
			'type' => 'flag',
			'default' => 'n',
		),
		'comments_akismet_filter' => array(
			'name' => tra('Use Akismet to filter comments'),
			'description' => tra('Prevent comment spam by using the Akismet service to determine if the comment is spam. If comment moderation is enabled, the Akismet will indicate if the comment is to be moderated or not. If there is no comment moderation, the comment will be rejected if considered as spam.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('advanced'),
			'keywords' => 'askimet', // Let an admin find the preference even if his query has this common typo
		),
		'comments_akismet_apikey' => array(
			'name' => tra('Akismet API Key'),
			'description' => tra('Key required for the Akismet comment spam prevention to work.'),
			'hint' => tr('Obtain this key by registering your site on [%0]', 'http://akismet.com'),
			'type' => 'text',
			'filter' => 'word',
			'tags' => array('advanced'),
			'default' => '',
			'keywords' => 'askimet',	
		),
		'comments_akismet_check_users' => array(
			'name' => tr('Filter spam for registered users'),
			'description' => tr('Enable spam filtering for registered users as well. Useful if your site allows for anyone to register without much validation.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('advanced'),
			'keywords' => array('askimet'),			
		),
	);
}
