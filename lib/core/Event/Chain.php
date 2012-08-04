<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Chain.php 39469 2012-01-12 21:13:48Z changi67 $

class Event_Chain
{
	private $event;
	private $manager;

	function __construct(Event_Manager $manager, $eventName)
	{
		$this->event = $eventName;
		$this->manager = $manager;
	}

	function trigger($arguments)
	{
		$this->manager->trigger($this->event, $arguments);
	}

	function getEventName()
	{
		return $this->event;
	}
}

