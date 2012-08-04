<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: errorreportlib.php 39469 2012-01-12 21:13:48Z changi67 $

class ErrorReportLib
{
	function report($message)
	{
		if (! isset($_SESSION['errorreport'])) {
			$_SESSION['errorreport'] = array();
		}

		$_SESSION['errorreport'][] = $message;
	}

	function get_errors()
	{
		if (! isset($_SESSION['errorreport'])) {
			return array();
		}

		$errors = $_SESSION['errorreport'];
		unset($_SESSION['errorreport']);

		return $errors;
	}

	function send_headers()
	{
		require_once 'lib/smarty_tiki/function.error_report.php';
		header('X-Tiki-Error: ' . smarty_function_error_report(array(), TikiLib::lib('smarty')));
	}
}

