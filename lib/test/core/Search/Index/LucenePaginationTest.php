<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: LucenePaginationTest.php 39469 2012-01-12 21:13:48Z changi67 $

class Search_Index_LucenePaginationTest extends PHPUnit_Framework_TestCase
{
	private $index;
	private $dir;

	function setUp()
	{
		$this->dir = dirname(__FILE__) . '/test_index';
		$this->tearDown();

		$index = new Search_Index_Lucene($this->dir);

		$this->index = $index;
	}

	function tearDown()
	{
		$dir = escapeshellarg($this->dir);
		`rm -Rf $dir`;
	}

	function testNoPagingRequired()
	{
		$this->assertResultCorrect(15, 0, 25, 1, 15);
	}

	function testGetSecondPage()
	{
		$this->assertResultCorrect(30, 10, 10, 11, 20);
	}

	private function assertResultCorrect($count, $from, $perPage, $first, $last)
	{
		$this->addDocuments($count);

		$query = new Search_Query;
		$query->filterType('article');
		$query->setRange($from, $perPage);

		$result = $query->search($this->index);

		$this->assertEquals($count, count($result), 'total count');

		$real = array();
		foreach ($result as $hit) {
			$real[] = $hit;
		}

		$this->assertEquals($first, $real[0]['object_id'], 'first entry');
		$this->assertEquals($last, $real[count($real)-1]['object_id'], 'last entry');
	}

	private function addDocuments($count)
	{
		$index = $this->index;

		$typeFactory = $index->getTypeFactory();

		for ($i = 0; $count > $i; ++$i) {
			$index->addDocument(
							array(
								'object_type' => $typeFactory->identifier('article'),
								'object_id' => $typeFactory->identifier($i + 1),
							)
			);
		}
	}
}

