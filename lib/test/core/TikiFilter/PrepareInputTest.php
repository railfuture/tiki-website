<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: PrepareInputTest.php 39469 2012-01-12 21:13:48Z changi67 $

class TikiFilter_PrepareInputTest extends PHPUnit_Framework_TestCase
{
	function setUp()
	{
		$this->obj = new TikiFilter_PrepareInput('.');
	}
	
	function testNormalInput()
	{
		$input = array(
			'foo' => 'bar',
			'hello' => 'world',
		);

		$this->assertEquals($input, $this->obj->prepare($input));
	}

	function testConvertArray()
	{
		$input = array(
			'foo.baz' => 'bar',
			'foo.bar' => 'baz',
			'hello' => 'world',
			'a.b.c' => '1',
			'a.b.d' => '2',
		);

		$expect = array(
			'foo' => array(
				'baz' => 'bar',
				'bar' => 'baz',
			),
			'hello' => 'world',
			'a' => array(
				'b' => array(
					'c' => '1',
					'd' => '2',
				),
			),
		);

		$this->assertEquals($expect, $this->obj->prepare($input));
	}
	
	function testNormalFlatten()
	{
		$input = array(
			'foo' => 'bar',
			'hello' => 'world',
		);
		
		$this->assertEquals($input, $this->obj->flatten($input));
	}
	
	function testConvertArrayFlatten()
	{
		$input = array(
			'foo' => array(
				'baz' => 'bar',
				'bar' => 'baz',
			),
			'hello' => 'world',
			'a' => array(
				'b' => array(
					'c' => '1',
					'd' => '2',
				),
			),
		);
		
		$expect = array(
			'foo.baz' => 'bar',
			'foo.bar' => 'baz',
			'hello' => 'world',
			'a.b.c' => '1',
			'a.b.d' => '2',
		);
		
		$this->assertEquals($expect, $this->obj->flatten($input));
	}
}

