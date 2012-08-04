<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Word.php 40112 2012-03-10 17:32:24Z pkdille $

class TikiFilter_Word extends Zend_Filter_PregReplace
{
	function __construct()
	{
		parent::__construct('/\W+/', '');
	}
}
