<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: WikiTemplate.php 39469 2012-01-12 21:13:48Z changi67 $

class Search_Formatter_Plugin_WikiTemplate implements Search_Formatter_Plugin_Interface
{
	private $template;

	function __construct($template)
	{
		$this->template = $template;
	}

	function getFormat()
	{
		return self::FORMAT_WIKI;
	}

	function getFields()
	{
		$matches = WikiParser_PluginMatcher::match($this->template);
		$parser = new WikiParser_PluginArgumentParser;

		$fields = array();
		foreach ($matches as $match) {
			$name = $match->getName();

			if ($name === 'display') {
				$arguments = $parser->parse($match->getArguments());

				if (isset($arguments['name']) && ! isset($fields[$arguments['name']])) {
					$fields[$arguments['name']] = isset($arguments['default']) ? $arguments['default'] : null;
				}
			}
		}

		return $fields;
	}

	function prepareEntry($valueFormatter)
	{
		$matches = WikiParser_PluginMatcher::match($this->template);

		foreach ($matches as $match) {
			$name = $match->getName();

			if ($name === 'display') {
				$match->replaceWith((string) $this->processDisplay($valueFormatter, $match->getBody(), $match->getArguments()));
			}
		}

		return $matches->getText();
	}

	function renderEntries(Search_ResultSet $entries)
	{
		$out = '';
		foreach ($entries as $entry) {
			$out .= $entry;
		}
		return $out;
	}

	private function processDisplay($valueFormatter, $body, $arguments)
	{
		$parser = new WikiParser_PluginArgumentParser;
		$arguments = $parser->parse($arguments);

		$name = $arguments['name'];

		if (isset($arguments['format'])) {
			$format = $arguments['format'];
		} else {
			$format = 'plain';
		}

		unset($arguments['format']);
		unset($arguments['name']);
		return $valueFormatter->$format($name, $arguments);
	}
}

