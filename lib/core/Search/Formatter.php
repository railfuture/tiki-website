<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Formatter.php 40437 2012-03-27 20:13:30Z sampaioprimo $

class Search_Formatter
{
	private $plugin;
	private $subFormatters = array();
	private $dataSource;

	function __construct(Search_Formatter_Plugin_Interface $plugin)
	{
		$this->plugin = $plugin;
	}

	function setDataSource(Search_Formatter_DataSource_Interface $dataSource)
	{
		$this->dataSource = $dataSource;
	}

	function addSubFormatter($name, $formatter)
	{
		$this->subFormatters[$name] = $formatter;
	}

	function format($list)
	{
		$list = Search_ResultSet::create($list);
		$defaultValues = $this->plugin->getFields();

		$fields = array_keys($defaultValues);
		$subDefault = array();
		foreach ($this->subFormatters as $key => $plugin) {
			$subDefault[$key] = $plugin->getFields();
			$fields = array_merge($fields, array_keys($subDefault[$key]));
		}

		if ($this->dataSource) {
			$list = $this->dataSource->getInformation($list, $fields);
		}

		if (in_array('highlight', $fields)) {
			foreach ($list as & $entry) {
				$entry['highlight'] = $list->highlight($entry);
			}
		}

		$data = array();

		foreach ($list as $row) {
			// Clear blank values so the defaults prevail
			$row = array_filter($row, array($this, 'is_empty_string'));
			$row = array_merge($defaultValues, $row);

			$subEntries = array();
			foreach ($this->subFormatters as $key => $plugin) {
				$subInput = new Search_Formatter_ValueFormatter(array_merge($subDefault[$key], $row));
				$subEntries[$key] = $this->render($plugin, Search_ResultSet::create(array($plugin->prepareEntry($subInput))), $this->plugin->getFormat(), $list);
			}

			$row = array_merge($row, $subEntries);

			$data[] = $this->plugin->prepareEntry(new Search_Formatter_ValueFormatter($row));
		}

		$list = $list->replaceEntries($data);

		return $this->render($this->plugin, $list, Search_Formatter_Plugin_Interface::FORMAT_WIKI);
	}
	
	private function is_empty_string($v) {
		return $v !== '';
	}

	private function render($plugin, $resultSet, $target)
	{
		$pluginFormat = $plugin->getFormat();
		$rawOutput = $plugin->renderEntries($resultSet);

		if ($target == $pluginFormat) {
			$out = $rawOutput;
		} elseif ($target == Search_Formatter_Plugin_Interface::FORMAT_WIKI && $pluginFormat == Search_Formatter_Plugin_Interface::FORMAT_HTML) {
			$out = "~np~$rawOutput~/np~";
		} elseif ($target == Search_Formatter_Plugin_Interface::FORMAT_HTML && $pluginFormat == Search_Formatter_Plugin_Interface::FORMAT_WIKI) {
			$out = "~/np~$rawOutput~np~";
		}

		$out = str_replace(array('~np~~/np~', '~/np~~np~'), '', $out);
		return $out;
	}
}

