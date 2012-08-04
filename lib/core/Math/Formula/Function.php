<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Function.php 40069 2012-03-07 21:05:21Z pkdille $

abstract class Math_Formula_Function
{
	private $callback;

	function evaluateTemplate( $element, $evaluateCallback )
	{
		$this->callback = $evaluateCallback;
		return $this->evaluate($element);
	}

	abstract function evaluate( $element );

	protected function evaluateChild( $child )
	{
		return call_user_func($this->callback, $child);
	}

	protected function error( $message )
	{
		throw new Math_Formula_Exception($message);
	}
}

