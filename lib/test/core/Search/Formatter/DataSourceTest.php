<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: DataSourceTest.php 39469 2012-01-12 21:13:48Z changi67 $

class Search_Formatter_DataSourceTest extends PHPUnit_Framework_TestCase
{
	private $wikiSource;
	private $categorySource;
	private $permissionSource;

	function setUp()
	{
		$this->wikiSource = new Search_ContentSource_Static(
						array('Test' => array('description' => 'ABC'),), 
						array('description' => 'sortable')
		);

		$this->categorySource = new Search_GlobalSource_Static(
						array('wiki page:Test' => array('categories' => array(1, 2, 3)),), 
						array('categories' => 'multivalue')
		);

		$this->permissionSource = new Search_GlobalSource_Static(
						array(
							'wiki page:Test' => array('allowed_groups' => array('Editors', 'Admins')),
						), 
						array('allowed_groups' => 'multivalue')
		);
	}

	function testObtainInformationFromContentSource()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addContentSource('wiki page', $this->wikiSource);

		$this->assertSetsEquals(
						$source, 
						array(
							array('object_type' => 'wiki page', 'object_id' => 'Test', 'description' => 'ABC'),
						), 
						array(
							array('object_type' => 'wiki page', 'object_id' => 'Test'),
						), 
						array('object_id', 'description')
		);
	}

	function testRequestedValueNotProvided()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addContentSource('wiki page', $this->wikiSource);

		$this->assertSetsEquals(
						$source, 
						array(array('object_type' => 'wiki page', 'object_id' => 'Test'),), 
						array(
							array('object_type' => 'wiki page', 'object_id' => 'Test'),
						), 
						array('object_id', 'title')
		);
	}

	function testValueFromGlobal()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addGlobalSource($this->categorySource);
		$source->addGlobalSource($this->permissionSource);

		$this->assertSetsEquals(
						$source, 
						array(
							array(
								'object_type' => 'wiki page', 
								'object_id' => 'Test', 
								'categories' => array(1, 2, 3), 
								'allowed_groups' => array('Editors', 'Admins')
							),
						), 
						array(
							array('object_type' => 'wiki page', 'object_id' => 'Test'),
						), 
						array('object_id', 'description', 'categories', 'allowed_groups')
		);
	}

	function testContentSourceNotAvailable()
	{
		$source = new Search_Formatter_DataSource_Declarative;

		$this->assertSetsEquals(
						$source, 
						array(
							array('object_type' => 'wiki page', 'object_id' => 'Test'),
						), 
						array(array('object_type' => 'wiki page', 'object_id' => 'Test'),), 
						array('object_id', 'description', 'categories', 'allowed_groups')
		);
	}

	function testCompleteTest()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addContentSource('wiki page', $this->wikiSource);
		$source->addGlobalSource($this->categorySource);

		$this->assertSetsEquals(
						$source, 
						array(
							array(
								'object_type' => 'wiki page', 
								'object_id' => 'Test', 
								'description' => 'ABC', 
								'categories' => array(1, 2, 3)
							),
						), 
						array(array('object_type' => 'wiki page', 'object_id' => 'Test'),), 
						array('object_id', 'description', 'categories', 'allowed_groups')
		);
	}

	function testProvideResultSet()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addContentSource('wiki page', $this->wikiSource);
		$source->addGlobalSource($this->categorySource);

		$in = new Search_ResultSet(
						array(
							array('object_type' => 'wiki page', 'object_id' => 'Test'),
						), 
						11, 
						10, 
						10
		);

		$out = new Search_ResultSet(
						array(
							array(
								'object_type' => 'wiki page', 
								'object_id' => 'Test', 
								'description' => 'ABC', 
								'categories' => array(1, 2, 3)
							),
						), 
						11, 
						10, 
						10
		);

		$this->assertEquals($out, $source->getInformation($in, array('object_id', 'description', 'categories', 'allowed_groups')));
	}

	function testSourceFindsEntryWithMultipleSourceResults()
	{
		$wikiSource = new Search_ContentSource_Static(
						array(
							'Test' => array(
									array('title' => 'Test', 'hash' => '3'),
									array('title' => 'Test (latest)', 'hash' => '4'),
							),
						), 
						array('title' => 'sortable', 'hash' => 'identifier')
		);

		$source = new Search_Formatter_DataSource_Declarative;
		$source->addContentSource('wiki page', $wikiSource);

		$in = new Search_ResultSet(
						array(
							array(
								'object_type' => 'wiki page', 
								'object_id' => 'Test', 
								'hash' => '4'
							),
						), 
						11, 
						10,
						10
		);

		$out = new Search_ResultSet(
						array(
							array(
								'object_type' => 'wiki page', 
								'object_id' => 'Test', 
								'title' => 'Test (latest)', 
								'hash' => '4'
							),
						), 
						11, 
						10, 
						10
		);

		$this->assertEquals($out, $source->getInformation($in, array('object_id', 'hash', 'title')));
	}

	private function assertSetsEquals($source, $expect, $in, $arg)
	{
		$out = $source->getInformation(Search_ResultSet::create($in), $arg);
		$this->assertEquals(Search_ResultSet::create($expect), $out);
	}
}

