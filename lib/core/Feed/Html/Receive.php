<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Receive.php 40196 2012-03-15 18:15:10Z robertplummer $

/**
 * For HtmlFeed_Remote Protocol
 */
class Feed_Html_Receive extends Feed_Abstract
{
	var $type = "html_feed";
	var $href = "";
	var $verion = "0.1";
	
	function __construct($href)
	{
		$this->href = $href;
	}
	
	public function getContents()
	{
		if (!empty($this->contents)) {
			return $this->contents;
		} else {
			$feed = json_decode(file_get_contents($this->href));
			$this->contents = $feed->feed;
		}
		return $this->contents;
	}
}