<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: print.php 39476 2012-01-13 00:40:36Z marclaporte $

function prefs_print_list()
{
	return array(
		'print_pdf_from_url' => array(
			'name' => tra('PDF from URL'),
			'description' => tra('Using extenal tools, generate PDF document from web URLs.'),
			'type' => 'list',
			'options' => array(
				'none' => tra('Disabled'),
				'webkit' => tra('WebKit (wkhtmltopdf)'),
				'webservice' => tra('Webservice'),
			),
			'default' => 'none',
		),
		'print_pdf_webservice_url' => array(
			'name' => tra('Webservice URL'),
			'description' => tra('URL to a service taking a URL as the query string and returns a PDF document.'),
			'type' => 'text',
			'size' => 50,
			'default' => '',
		),
		'print_pdf_webkit_path' => array(
			'name' => tra('WebKit path'),
			'description' => tra('Full path to the wkhtmltopdf executable to generate the PDF document with.'),
			'type' => 'text',
			'size' => 50,
			'help' => 'wkhtmltopdf',
			'default' => '',
		),
	);
}

