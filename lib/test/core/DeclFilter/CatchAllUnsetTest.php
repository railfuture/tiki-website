<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: CatchAllUnsetTest.php 39469 2012-01-12 21:13:48Z changi67 $

/**
 * @group unit
 * 
 */

class DeclFilter_CatchAllUnsetTest extends TikiTestCase
{
	function testMatch()
	{
		$rule = new DeclFilter_CatchAllUnsetRule();

		$this->assertTrue($rule->match('hello'));
	}

	function testApply()
	{
		$rule = new DeclFilter_CatchAllUnsetRule();

		$data = array(
			'hello' => '123abc',
		);

		$rule->apply($data, 'hello');

		$this->assertFalse(isset($data['hello'])); 
	}
}
