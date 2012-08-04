<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ArticleSubmitted.php 41668 2012-05-31 16:28:26Z xavidp $

/**
 * Class for article_submitted events
 */
class Reports_Send_EmailBuilder_ArticleSubmitted extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('New articles submited:');
	}
	
	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		$output = '<u>' . $change['data']['user'] . '</u> ' . tra('created the article') .
							" <a href=\"{$base_url}tiki-read_article.php?articleId=" . $change['data']['articleId'] . "\">" . $change['data']['articleTitle'] . "</a>.";
		
		return $output;
	}
}
