<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: CommentsTest.php 39469 2012-01-12 21:13:48Z changi67 $

require_once 'lib/comments/commentslib.php';

class CommentsTest extends TikiTestCase
{

	private $lib;

	function setUp()
	{
		$this->lib = new Comments();
	}

	function testGetHref()
	{
		$this->assertEquals('tiki-index.php?page=HomePage&amp;threadId=9&amp;comzone=show#threadId9', $this->lib->getHref('wiki page', 'HomePage', 9));
		$this->assertEquals('tiki-view_blog_post.php?postId=1&amp;threadId=10&amp;comzone=show#threadId10', $this->lib->getHref('blog post', 1, 10));
	}
}

