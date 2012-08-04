<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Interface.php 40354 2012-03-24 16:30:39Z jonnybradley $

interface Search_Formatter_ValueFormatter_Interface
{
	function render($name, $value, array $entry);

	function canCache();
}

