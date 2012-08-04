<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: initlib.php 40873 2012-04-12 19:56:27Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

class TikiInit
{

	function TikiInit()
	{
	}


/** Return 'windows' if windows, otherwise 'unix'
  * \static
  */
	function os()
	{
		static $os;
		if (!isset($os)) {
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
				$os = 'windows';
			} else {
				$os = 'unix';
			}
		}
		return $os;
	}


/** Return true if windows, otherwise false
  * \static
  */
	static function isWindows()
	{
		static $windows;
		if (!isset($windows)) {
			$windows = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';
		}
		return $windows;
	}
	
	/*
	 * @param string $path	directory to test
	 * @param bool $is_file	default false for a dir
	 * @return bool
	 *
	 * Copes with Windows premissions
	 */
	static function is_writeable($path)
	{
		if (self::isWindows()) {
			return self::is__writable($path);
		} else {
			return is_writeable($path);
		}
	}

	/*
	 * @param string $path	directory to test	NOTE: use a trailing slash for folders!!!
	 * @return bool
	 * 
	 * From the php is_writable manual (thanks legolas558 d0t users dot sf dot net)
	 * Note the two underscores and no "e"
	 */
	static function is__writable($path)
	{
		//will work in despite of Windows ACLs bug
		//NOTE: use a trailing slash for folders!!!
		//see http://bugs.php.net/bug.php?id=27609
		//see http://bugs.php.net/bug.php?id=30931

		if ($path{strlen($path)-1}=='/') { // recursively return a temporary file path
			return self::is__writable($path.uniqid(mt_rand()).'.tmp');
		} else if (is_dir($path)) {
			return self::is__writable($path.'/'.uniqid(mt_rand()).'.tmp');
		}
		// check tmp file for read/write capabilities
		$rm = file_exists($path);
		$f = @fopen($path, 'a');
		if ($f===false)
			return false;
		fclose($f);
		if (!$rm)
			unlink($path);
		return true;
	}


/** Prepend $path to the include path
  * \static
  */
	static function prependIncludePath($path)
	{
		$include_path = ini_get('include_path');
		$paths = explode(PATH_SEPARATOR, $include_path);

		if ($include_path && !in_array($path, $paths)) {
			$include_path = $path . PATH_SEPARATOR . $include_path;
		} else if (!$include_path) {
			$include_path = $path;
		} 

		return set_include_path($include_path);
	}


/** Append $path to the include path
  * \static
  */
	static function appendIncludePath($path)
	{
		$include_path = ini_get('include_path');
		$paths = explode(PATH_SEPARATOR, $include_path);

		if ($include_path && !in_array($path, $paths)) {
			$include_path .= PATH_SEPARATOR . $path;
		} else if (!$include_path) {
			$include_path = $path;
		} 

		return set_include_path($include_path);
	}


/** Return system defined temporary directory.
  * In Unix, this is usually /tmp
  * In Windows, this is usually c:\windows\temp or c:\winnt\temp
  * \static
  */
	static function tempdir()
	{
		static $tempdir;
		if (!$tempdir) {
			$tempfile = @tempnam(false, '');
			$tempdir = dirname($tempfile);
			@unlink($tempfile);
		}
		return $tempdir;
	}

	/**
	* Convert a string to UTF-8. Fixes a bug in PHP decode
	* From http://w3.org/International/questions/qa-forms-utf-8.html
	* @param string String to be converted
	* @return UTF-8 representation of the string
	*/
	static function to_utf8( $string )
	{
			// 
		if ( preg_match('%^(?:
      [\x09\x0A\x0D\x20-\x7E]            # ASCII
    | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
    | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
    | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
    | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
    | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
    | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
    | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
)*$%xs', $string) ) {
			return $string;
		} else {
			return iconv('CP1252', 'UTF-8', $string);
		}
	} 
	
	/**
	*	Determine if the web server is an IIS server
	*	@return true if IIS server, else false
  	* \static
	*/
	static function isIIS()
	{
		static $IIS;

		// Sample value Microsoft-IIS/7.5
		if (!isset($IIS) && isset($_SERVER['SERVER_SOFTWARE'])) {
			$IIS = substr($_SERVER['SERVER_SOFTWARE'], 0, 13) == 'Microsoft-IIS';
		}

		return $IIS;
	}	

	/**
	*	Determine if the web server is an IIS server
	*	@return true if IIS server, else false
  	* \static
	*/
	static function hasIIS_UrlRewriteModule()
	{
			return isset($_SERVER['IIS_UrlRewriteModule']) == true;
	}	
}

function tiki_error_handling($errno, $errstr, $errfile, $errline)
{
	global $prefs, $phpErrors;

	if ( 0 === error_reporting() ) {
		// This error was triggered when evaluating an expression prepended by the at sign (@) error control operator, but since we are in a custom error handler, we have to ignore it manually.
		// See http://ca3.php.net/manual/en/language.operators.errorcontrol.php#98895 and http://php.net/set_error_handler
		return;
	}

	$err[E_ERROR]           = 'E_ERROR';
	$err[E_CORE_ERROR]      = 'E_CORE_ERROR';
	$err[E_USER_ERROR]      = 'E_USER_ERROR';
	$err[E_COMPILE_ERROR]   = 'E_COMPILE_ERROR';
	$err[E_WARNING]         = 'E_WARNING';
	$err[E_CORE_WARNING]    = 'E_CORE_WARNING';
	$err[E_USER_WARNING]    = 'E_USER_WARNING';
	$err[E_COMPILE_WARNING] = 'E_COMPILE_WARNING';
	$err[E_PARSE]           = 'E_PARSE';
	$err[E_NOTICE]          = 'E_NOTICE';
	$err[E_USER_NOTICE]     = 'E_USER_NOTICE';
	$err[E_STRICT]          = 'E_STRICT';

	if ( !defined('E_RECOVERABLE_ERROR') ) define('E_RECOVERABLE_ERROR', 4096);
	$err[E_RECOVERABLE_ERROR] = 'E_RECOVERABLE_ERROR';

	if ( !defined('E_DEPRECATED') ) define('E_DEPRECATED', 8192);
	$err[E_DEPRECATED] = 'E_DEPRECATED';

	if ( !defined('E_USER_DEPRECATED') ) define('E_USER_DEPRECATED', 16384);
	$err[E_USER_DEPRECATED] = 'E_USER_DEPRECATED';

	global $tikipath;
	$errfile = str_replace($tikipath, '', $errfile);
	switch ($errno) {
	case E_ERROR:
	case E_CORE_ERROR:
	case E_USER_ERROR:
	case E_COMPILE_ERROR:
	case E_WARNING:
	case E_CORE_WARNING:
	case E_USER_WARNING:
	case E_COMPILE_WARNING:
	case E_PARSE:
	case E_RECOVERABLE_ERROR:
		$back = "<div class='rbox-data' style='font-size:10px;border:1px solid'>";
		$back.= "<b>PHP (".PHP_VERSION.") ERROR (".$err[$errno]."):</b><br />";
		$back.= "<b style='font-family: monospace'>File:</b> $errfile<br />";
		$back.= "<b style='font-family: monospace'>Line:</b> $errline<br />";
		$back.= "<b style='font-family: monospace'>Type:</b> $errstr";
		$back.= "</div>";
		$phpErrors[] = $back;
    	break;
	case E_STRICT:
	case E_NOTICE:
	case E_USER_NOTICE:
	case E_DEPRECATED:
	case E_USER_DEPRECATED:
		if (!  defined('THIRD_PARTY_LIBS_PATTERN') ||  ! preg_match(THIRD_PARTY_LIBS_PATTERN, $errfile) ) {
			if ( ! empty($prefs['smarty_notice_reporting']) && $prefs['smarty_notice_reporting'] != 'y' && strstr($errfile, '.tpl.php'))
				break;
			$back = "<div class='rbox-data' style='font-size:10px;border:1px solid'>";
			$back.= "<b>PHP (".PHP_VERSION.") NOTICE ($err[$errno]):</b><br />";
			$back.= "<b style='font-family: monospace'>File:</b> $errfile<br />";
			$back.= "<b style='font-family: monospace'>Line:</b> $errline<br />";
			$back.= "<b style='font-family: monospace'>Type:</b> $errstr";
			$back.= "</div>";
			$phpErrors[] = $back;
		}
	default:
    	break;
	}
}
