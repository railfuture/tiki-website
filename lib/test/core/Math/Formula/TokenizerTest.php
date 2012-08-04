<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: TokenizerTest.php 39469 2012-01-12 21:13:48Z changi67 $

class Math_Formula_TokenizerTest extends TikiTestCase
{
	function testSimpleToken()
	{
		$tokenizer = new Math_Formula_Tokenizer;

		$this->assertEquals(array('test'), $tokenizer->getTokens('test'));
	}

	function testWithParenthesis()
	{
		$tokenizer = new Math_Formula_Tokenizer;

		$this->assertEquals(array('test', ')'), $tokenizer->getTokens('test)'));
	}

	function testWithMultipleParenthesis()
	{
		$tokenizer = new Math_Formula_Tokenizer;

		$this->assertEquals(array('(', 'test', ')'), $tokenizer->getTokens('(test)'));
	}

	function testIgnoreSpaces()
	{
		$tokenizer = new Math_Formula_Tokenizer;

		$this->assertEquals(array('(', 'test', ')'), $tokenizer->getTokens(" (test\n\t\r) "));
	}

	function testWithMultipleWords()
	{
		$tokenizer = new Math_Formula_Tokenizer;
		$this->assertEquals(array('hello', 'world', 'foo-bar'), $tokenizer->getTokens('hello world foo-bar'));
	}

	function testWordsAfterParenthesis()
	{
		$tokenizer = new Math_Formula_Tokenizer;
		$this->assertEquals(array('hello', '(', 'world', ')', 'foo-bar'), $tokenizer->getTokens('hello (world) foo-bar'));
	}
}

