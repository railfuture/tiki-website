<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: RawUnsafe.php 40672 2012-04-03 12:57:15Z jonnybradley $

class TikiFilter_RawUnsafe implements Zend_Filter_Interface
{
	function filter( $value )
	{
		return str_replace('<x>', '', $value);
	}
}
