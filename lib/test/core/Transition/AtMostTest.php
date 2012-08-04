<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: AtMostTest.php 39469 2012-01-12 21:13:48Z changi67 $

/** 
 * @group unit
 * 
 */

class Transition_AtMostTest extends PHPUnit_Framework_TestCase
{
	function testOver()
	{
		$transition = new Transition('A', 'B');
		$transition->setStates(array('A', 'C', 'D', 'F'));
		$transition->addGuard('atMost', 2, array('C', 'D', 'E', 'F', 'G'));

		$this->assertEquals(
						array(array('class' => 'extra', 'count' => 1, 'set' => array('C', 'D', 'F')),), 
						$transition->explain()
		);
	}

	function testRightOn()
	{
		$transition = new Transition('A', 'B');
		$transition->setStates(array('A', 'C', 'D', 'F'));
		$transition->addGuard('atMost', 3, array('C', 'D', 'E', 'F', 'G'));

		$this->assertEquals(array(), $transition->explain());
	}

	function testUnder()
	{
		$transition = new Transition('A', 'B');
		$transition->setStates(array('A', 'C', 'D', 'F'));
		$transition->addGuard('atMost', 4, array('C', 'D', 'E', 'F', 'G'));

		$this->assertEquals(array(), $transition->explain());
	}
}
