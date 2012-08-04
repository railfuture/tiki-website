<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: PhpTest.php 39469 2012-01-12 21:13:48Z changi67 $

require_once('lib/language/FileType.php');
require_once('lib/language/FileType/Php.php');

class Language_FileType_PhpTest extends TikiTestCase
{

	protected $obj;
	
	protected function setUp()
	{
		$this->obj = new Language_FileType_Php();
	}

	public function testSingleQuoted_shouldRemoveEscapesFromSingleQuotes()
	{
		$strings = array(
			'Features',
			'Enable/disable Tiki features here, but configure them elsewhere',
			"Show user\'s real name instead of login in autocomplete selector in trackers feature",
			'General preferences and settings',
		);
		
		$expectedResult = array(
			'Features',
			'Enable/disable Tiki features here, but configure them elsewhere',
			"Show user's real name instead of login in autocomplete selector in trackers feature",
			'General preferences and settings',
		);

		$this->assertEquals($expectedResult, $this->obj->singleQuoted($strings));
	}
	
	public function testDoubleQuoted_shouldRemoveEscapes()
	{
		$strings = array(
			'Congratulations!\n\nYour server can send emails.\n\n',
			'Handling actions of plugin \"%s\" failed',
			'Could not create \$tdo.mid in data directory',
		);
		
		$expectedResult = array(
			"Congratulations!\n\nYour server can send emails.\n\n",
			'Handling actions of plugin "%s" failed',
			'Could not create $tdo.mid in data directory',
		);

		$this->assertEquals($expectedResult, $this->obj->doubleQuoted($strings));
	}
}