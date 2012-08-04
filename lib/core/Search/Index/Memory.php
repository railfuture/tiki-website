<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Memory.php 39469 2012-01-12 21:13:48Z changi67 $

class Search_Index_Memory implements Search_Index_Interface
{
	private $data = array();
	private $lastQuery;
	private $lastOrder;
	private $lastStart;
	private $lastCount;

	function addDocument(array $data)
	{
		$this->data[] = $data;
	}

	function invalidateMultiple(Search_Expr_Interface $query)
	{
		return array();
	}

	function find(Search_Expr_Interface $query, Search_Query_Order $sortOrder, $resultStart, $resultCount)
	{
		$this->lastQuery = $query;
		$this->lastOrder = $sortOrder;
		$this->lastStart = $resultStart;
		$this->lastCount = $resultCount;
		return array();
	}

	function getTypeFactory()
	{
		return new Search_Type_Factory_Lucene;
	}

	function optimize()
	{
	}

	/**
	 * For test purposes.
	 */
	function size()
	{
		return count($this->data);
	}

	/**
	 * For test purposes.
	 */
	function getDocument($index)
	{
		return $this->data[$index];
	}

	/**
	 * For test purposes.
	 */
	function getLastQuery()
	{
		return $this->lastQuery;
	}

	/**
	 * For test purposes.
	 */
	function getLastOrder()
	{
		return $this->lastOrder;
	}

	/**
	 * For test purposes.
	 */
	function getLastStart()
	{
		return $this->lastStart;
	}

	/**
	 * For test purposes.
	 */
	function getLastCount()
	{
		return $this->lastCount;
	}
}

