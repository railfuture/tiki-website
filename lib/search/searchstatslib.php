<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: searchstatslib.php 39469 2012-01-12 21:13:48Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class SearchStatsLib extends TikiLib
{
	function clear_search_stats()
	{
		$query = "delete from tiki_search_stats";
		$result = $this->query($query, array());
	}

	function list_search_stats($offset, $maxRecords, $sort_mode, $find)
	{
		if ($find) {
			$mid = " where (`term` like ?)";
			$bindvars = array("%$find%");
		} else {
			$mid = "";
			$bindvars = array();
		}

		$query = "select * from `tiki_search_stats` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_search_stats` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
}
$searchstatslib = new SearchStatsLib;
