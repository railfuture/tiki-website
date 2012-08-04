<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: FactoryTest.php 40032 2012-03-04 18:17:02Z changi67 $

class Reports_FactoryTest extends TikiTestCase
{
	public function testBuild_shouldReturnInstances()
	{
		$classes = array('Reports_Users', 'Reports_Cache', 'Reports_Manager', 'Reports_Send');
		
		foreach ($classes as $className) {
			$this->assertInstanceOf($className, Reports_Factory::build($className));
		}
	}
	
	public function testBuild_shouldThrowExceptionForUnknownClass()
	{
		$this->setExpectedException('Exception', 'Unknown class Unknown_Class');
		Reports_Factory::build('Unknown_Class');
	}
}