<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: OutputLink.php 40958 2012-04-17 14:26:05Z robertplummer $

class WikiParser_OutputLink
{
	private $description;
	private $identifier;
	private $language;
	private $qualifier;
	private $anchor;

	private $externals = array();
	private $handlePlurals = false;

	private $wikiLookup;
	private $wikiBuilder = 'trim';

	function setIdentifier( $identifier )
	{
		$this->identifier = $identifier;
	}

	function setDescription( $description )
	{
		$this->description = $description;
	}

	function setQualifier( $qualifier )
	{
		$this->qualifier = $qualifier;
	}

	function setLanguage( $lang )
	{
		$this->language = $lang;
	}

	function setWikiLookup( $lookup )
	{
		$this->wikiLookup = $lookup;
	}

	function setWikiLinkBuilder( $builder )
	{
		$this->wikiBuilder = $builder;
	}

	function setExternals( array $externals )
	{
		$this->externals = $externals;
	}

	function setHandlePlurals( $handle )
	{
		$this->handlePlurals = (bool) $handle;
	}

	function setAnchor( $anchor )
	{
		$this->anchor = $anchor;
	}

	function getHtml($ck_editor = false)
	{
		$page = $this->identifier;
		$description = $this->identifier;
		if ( $this->description ) {
			$description = $this->description;
		}

		if ( $link = $this->handleExternal($page, $description, $class) ) {
			return $this->outputLink(
							$description, 
							array(
									'href' => $link . $this->anchor,
									'class' => $class,
							)
			);
		} elseif ( ($info = $this->findWikiPage($page)) || $ck_editor ) {
			if (!empty($info['pageName'])) {
				$page = $info['pageName'];
			}
			$title = $page;
			if (!empty($info['description'])) {
				$title = $info['description'];
			}

			return $this->outputLink(
							$description, 
							array(
									'href' => call_user_func($this->wikiBuilder, $page) . $this->anchor,
									'title' => $title,
									'class' => 'wiki wiki_page',
							) 
			);
		} else {
			return $description . $this->outputLink(
							'?', 
							array(
									'href' => $this->getEditLink($page),
									'title' => tra('Create page:') . ' ' . $page,
									'class' => 'wiki wikinew',
							)
			);
		}
	}

	private function outputLink( $text, array $attributes )
	{
		if ( $this->qualifier ) {
			$attributes['class'] .= ' ' . $this->qualifier;
		}

		$string = '<a';
		foreach ($attributes as $attr => $val) {
			$val = TikiLib::lib("parser")->protectSpecialChars($val);
			$string .= " $attr=\"" . TikiLib::lib("parser")->unprotectSpecialChars($val) . '"'; //val CANNOT be html, so force it to non-html
		}

		$string .= '>' . $text . '</a>'; //text can return html, so let parser take care of that

		return $string;
	}

	private function getEditLink( $page )
	{
		$url = 'tiki-editpage.php?page=' . urlencode($page);

		if ( $this->language ) {
			$url .= '&lang=' . urlencode($this->language);
		}

		return $url;
	}

	private function handleExternal( & $page, & $description, & $class )
	{
		$parts = explode(':', $page);

		if ( count($parts) == 2 ) {
			list( $token, $remotePage ) = $parts;
			$token = strtolower($token);

			if ( isset( $this->externals[$token] ) ) {
				if ( $page == $description ) {
					$description = $remotePage;
				}

				$page = $remotePage;
				$pattern = $this->externals[$token];
				$class = 'wiki ext_page ' . $token;
				return str_replace('$page', rawurlencode($page), $pattern);
			}
		}
	}

	private function findWikiPage( $page )
	{
		if (! $this->wikiLookup) {
			return;
		}

		if ($info = call_user_func($this->wikiLookup, $page)) {
			return $info;
		} elseif ($alternate = $this->handlePlurals($page)) {
			return call_user_func($this->wikiLookup, $alternate);
		}
	}

	private function handlePlurals( $page )
	{
		if ( ! $this->handlePlurals ) {
			return;
		}

		$alternate = $page;
		// Plurals like policy / policies
		$alternate = preg_replace("/ies$/", "y", $alternate);
		// Plurals like address / addresses
		$alternate = preg_replace("/sses$/", "ss", $alternate);
		// Plurals like box / boxes
		$alternate = preg_replace("/([Xx])es$/", "$1", $alternate);
		// Others, excluding ending ss like address(es)
		$alternate = preg_replace("/([A-Za-rt-z])s$/", "$1", $alternate);

		if ( $alternate != $page ) {
			return $alternate;
		}
	}
}

