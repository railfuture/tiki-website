<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: CatchAllFilterTest.php 39469 2012-01-12 21:13:48Z changi67 $

/** 
 * @group unit
 * 
 */

class DeclFilter_CatchAllFilterTest extends TikiTestCase
{
	function testMatch()
	{
		$rule = new DeclFilter_CatchAllFilterRule('digits');

		$this->assertTrue($rule->match('hello'));
	}

	function testApply()
	{
		$rule = new DeclFilter_CatchAllFilterRule('digits');

		$data = array(
			'hello' => '123abc',
		);

		$rule->apply($data, 'hello');

		$this->assertEquals($data['hello'], '123');
	}

	function testApplyRecursive()
	{
		$rule = new DeclFilter_CatchAllFilterRule('digits');
		$rule->applyOnElements();

		$data = array(
			'hello' => array(
				'abc123',
				'abc456',
			),
		);

		$rule->apply($data, 'hello');

		$this->assertEquals($data['hello'][0], '123');
		$this->assertEquals($data['hello'][1], '456');
	}
}
