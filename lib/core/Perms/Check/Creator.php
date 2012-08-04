<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Creator.php 40220 2012-03-16 19:50:45Z changi67 $

class Perms_Check_Creator implements Perms_Check
{
	private $user;
	private $key;
	private $suffix;
	
	function __construct( $user, $key = 'creator', $suffix = '_own' ) 
	{
		$this->user = $user;
		$this->key = $key;
		$this->suffix = $suffix;
	}

	function check( Perms_Resolver $resolver, array $context, $name, array $groups ) 
	{
		if ( isset( $context[$this->key] ) && $context[$this->key] == $this->user ) {
			return $resolver->check($name . $this->suffix, $groups);
		}

		return false;
	}

	function applicableGroups( Perms_Resolver $resolver ) 
	{
		return $resolver->applicableGroups();
	}
}
