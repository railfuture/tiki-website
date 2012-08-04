<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Query.php 39469 2012-01-12 21:13:48Z changi67 $

class Search_Query
{
	private $objectList;
	private $expr;
	private $sortOrder;
	private $start = 0;
	private $count = 50;
	private $weightCalculator = null;

	function __construct($query = null)
	{
		$this->expr = new Search_Expr_And(array());

		if ($query) {
			$this->filterContent($query);
		}
	}

	function addObject($type, $objectId)
	{
		if (is_null($this->objectList)) {
			$this->objectList = new Search_Expr_Or(array());
			$this->expr->addPart($this->objectList);
		}

		$type = new Search_Expr_Token($type, 'identifier', 'object_type');
		$objectId = new Search_Expr_Token($objectId, 'identifier', 'object_id');

		$this->objectList->addPart(new Search_Expr_And(array($type, $objectId)));
	}

	function filterContent($query, $field = 'contents')
	{
		$this->addPart($query, 'plaintext', $field);
	}

	function filterType($types)
	{
		if (is_array($types)) {
			foreach ($types as $type) {
				$tokens[] = new Search_Expr_Token($type);
			}
			$or =  new Search_Expr_Or($tokens);
			$this->addPart($or, 'identifier', 'object_type');
		} else {
			$token = new Search_Expr_Token($types);
			$this->addPart($token, 'identifier', 'object_type');
		}
	}

	function filterContributors($query)
	{
		$this->addPart($query, 'multivalue', 'contributors');
	}

	function filterCategory($query, $deep = false)
	{
		$this->addPart($query, 'multivalue', $deep ? 'deep_categories' : 'categories');
	}

	function filterTags($query)
	{
		$this->addPart($query, 'multivalue', 'freetags');
	}

	function filterLanguage($query)
	{
		$this->addPart($query, 'identifier', 'language');
	}

	function filterPermissions(array $groups)
	{
		$tokens = array();
		foreach ($groups as $group) {
			$tokens[] = new Search_Expr_Token($group);
		}

		$or = new Search_Expr_Or($tokens);

		$this->addPart($or, 'multivalue', 'allowed_groups');
	}

	/**
	 * Sets up Zend search term for a date range
	 *
	 * @param string	$from date - a unix timestamp or most date strings such as 'now', '2011-11-21', 'last week' etc
	 * @param string	$to date as with $from (other examples: '-42 days', 'last tuesday')
	 * @param string	$field to search in such as 'tracker_field_42'. default: modification_date
	 * @link			http://www.php.net/manual/en/datetime.formats.php
	 * @return void
	 */

	function filterRange($from, $to, $field = 'modification_date')
	{
		if (!is_numeric($from)) {
			$from2 = strtotime($from);
			if ($from2) {
				$from = $from2;
			} else {
				TikiLib::lib('errorreport')->report(tra('filterRange: "from" value not parsed'));
			}
		}
		if (!is_numeric($to)) {
			$to2 = strtotime($to);
			if ($to2) {
				$to = $to2;
			} else {
				TikiLib::lib('errorreport')->report(tra('filterRange: "to" value not parsed'));
			}
		}

		$this->expr->addPart(new Search_Expr_Range($from, $to, 'timestamp', $field));
	}

	function filterTextRange($from, $to, $field = 'title')
	{
		$this->expr->addPart(new Search_Expr_Range($from, $to, 'plaintext', $field));
	}

	function filterInitial($initial, $field = 'title')
	{
		$this->expr->addPart(new Search_Expr_Range($initial, substr($initial, 0, -1) . chr(ord(substr($initial, -1)) + 1), 'plaintext', $field));
	}

	function filterRelation($query, array $invertable = array())
	{
		$query = $this->parse($query);
		$replacer = new Search_Query_RelationReplacer($invertable);
		$query = $query->walk(array($replacer, 'visit'));
		$this->addPart($query, 'multivalue', 'relations');
	}

	private function addPart($query, $type, $field)
	{
		$parts = array();
		foreach ((array) $field as $f) {
			$part = $this->parse($query);
			$part->setType($type);
			$part->setField($f);
			$parts[] = $part;
		}
		
		if (count($parts) === 1) {
			$this->expr->addPart($parts[0]);
		} else {
			$this->expr->addPart(new Search_Expr_Or($parts));
		}
	}

	function setOrder($order)
	{
		if (is_string($order)) {
			$this->sortOrder = Search_Query_Order::parse($order);
		} else {
			$this->sortOrder = $order;
		}
	}

	function setRange($start, $count = null)
	{
		$this->start = (int) $start;

		if ($count) {
			$this->count = (int) $count;
		}
	}

	function setWeightCalculator(Search_Query_WeightCalculator_Interface $calculator)
	{
		$this->weightCalculator = $calculator;
	}

	function search(Search_Index_Interface $index)
	{
		if ($this->sortOrder) {
			$sortOrder = $this->sortOrder;
		} else {
			$sortOrder = Search_Query_Order::getDefault();
		}

		if ($this->weightCalculator) {
			$this->expr->walk(array($this->weightCalculator, 'calculate'));
		}

		return $index->find($this->expr, $sortOrder, $this->start, $this->count);
	}

	function invalidate(Search_Index_Interface $index)
	{
		return $index->invalidateMultiple($this->expr);
	}
	
	private function parse($query)
	{
		if (is_string($query)) {
			$parser = new Search_Expr_Parser;
			$query = $parser->parse($query);
		}

		return $query;
	}
}
