<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Handler.php 41324 2012-05-04 13:10:06Z robertplummer $

class JisonParser_Phraser_Handler extends JisonParser_Phraser
{
	var $chars = array();
	var $words = array();
	var $currentWord = -1;
	var $wordsChars = array();
	var $indexes = array();
	var $parsed = '';
	var $cache = array();

	function tagHandler($tag)
	{
		return $tag;
	}

	function wordHandler($word)
	{
		$this->currentWord++;
		$this->words[] = $word;

		foreach ($this->indexes as $i => $index) {
			if ($this->currentWord >= $index['start'] 
					&& $this->currentWord <= $index['end']
			) {
				$word = '<span class="phrase phrase' . $i . '" style="border: none;">' . $word . '</span>';
			}

			if ($this->currentWord == $index['start']) {
				$word = '<span class="phraseStart phraseStart' . $i . '" style="border: none; font-weight: cold;"></span>' . $word;
			}

			if ($this->currentWord == $index['end']) {
				if (empty($this->wordsChars[$this->currentWord])) {
					$word = $word . '<span class="phraseEnd phraseEnd' . $i . '" style="border: none;"></span>';
				} else {
					$word = '<span class="phrase phrase' . $i . '" style="border: none;">' . $word . '</span>';
				}
			}
		}

		return $word;
	}

	function charHandler($char)
	{
		if (empty($this->wordsChars[$this->currentWord])) $this->wordsChars[$this->currentWord] = "";

		//this line attempts to solve some character translation problems
		$char = iconv('UTF-8', 'ISO-8859-1', $char);

		$this->wordsChars[$this->currentWord] .= $char;
		$this->chars[] = $char;

		foreach ($this->indexes as $i => $index) {
			if ($this->currentWord >= $index['start'] && $this->currentWord <= $index['end']) {
				$char = '<span class="phrases phrase' . $i . '" style="border: none;">' . $char . '</span>';

				if ($this->currentWord == $index['end']) {
					if (!empty($this->wordsChars[$this->currentWord])) {
						$char = $char . '<span class="phraseEnd phraseEnd' . $i . '" style="border: none;"></span>';
					}
				}
			}
		}


		return $char;
	}

	function isUnique($parent, $phrase)
	{
		$parentWords = $this->sanitizeToWords($parent);
		$phraseWords = $this->sanitizeToWords($phrase);

		$this->clearIndexes();

		$this->addIndexes($parentWords, $phraseWords, true);

		if (count($this->indexes) > 1) {
			return false;
		} else {
			return true;
		}
	}

	function findPhrases($parent, $phrases)
	{
		$parentWords = $this->sanitizeToWords($parent);
		$phrasesWords = array();

		$this->clearIndexes();

		foreach ($phrases as $phrase) {
			$phraseWords = $this->sanitizeToWords($phrase);

			$this->addIndexes($parentWords, $phraseWords);
			$phrasesWords[] = $phraseWords;
		}

		if (!empty($this->indexes)) {
			$parent = $this->parse($parent);
		}

		return $parent;
	}

	function clearIndexes()
	{
		$this->indexes = array();
	}

	function addIndexes($parentWords, $phraseWords, $allMatches = false)
	{
		$phraseLength = count($phraseWords) - 1;
		$phraseConcat = implode($phraseWords, '|');
		$parentConcat = implode($parentWords, '|');

		$boundaries = explode($phraseConcat, $parentConcat);

		//We may not have a match
		if (count($boundaries) == 1 && strlen($boundaries[0]) == strlen($parentConcat)) {
			return array();
		}

		for ($i = 0, $j = count($boundaries); $i < $j; $i++) {
			$boundaryLength = substr_count($boundaries[$i], '|');

			$this->indexes[] = array(
					'start' => min(count($parentWords) - count($phraseWords), $boundaryLength),
					'end' => min(count($parentWords), $boundaryLength + $phraseLength)
			);

			$i++;
		}
	}

	static function hasPhrase($parent, $phrase)
	{
		$parent = self::sanitizeToWords($parent);
		$phrase = self::sanitizeToWords($phrase);

		$parent = implode('|', $parent);
		$phrase = implode('|', $phrase);

		return (strpos($parent, $phrase) !== false ? true : false);
	}

	static function sanitizeToWords($html)
	{
		$sanitized = preg_replace('/<(.|\n)*?>/', ' ', $html);
		$sanitized = preg_replace('/\W/', ' ', $sanitized);
		$sanitized = explode(" ", $sanitized);
		$sanitized = array_values(array_filter($sanitized, 'strlen'));

		return $sanitized;
	}

	static function superSanitize($html)
	{
		return utf8_encode(implode('', self::sanitizeToWords($html)));
	}
}
