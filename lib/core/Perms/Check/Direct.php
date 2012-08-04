<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Direct.php 40234 2012-03-17 19:17:41Z changi67 $

class Perms_Check_Direct implements Perms_Check
{
	function check( Perms_Resolver $resolver, array $context, $name, array $groups ) 
	{
		return $resolver->check($name, $groups);
	}

	function applicableGroups( Perms_Resolver $resolver ) 
	{
		return $resolver->applicableGroups();
	}
}
