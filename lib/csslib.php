<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: csslib.php 40097 2012-03-09 21:55:13Z pkdille $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

class cssLib extends TikiLib
{
	function list_css($path, $recursive = false)
	{
		$files = $this->list_files($path, '.css', $recursive);
		foreach ($files as $i=>$file) {
			$files[$i] = preg_replace("|^$path/(.*)\.css$|", '$1', $file);
		}
		return $files;
	}

	function list_files($path, $extension, $recursive)
	{
		$back = array();

		$handle = opendir($path);

		while ($file = readdir($handle)) {
			if ((substr($file, -4, 4) == $extension) and (preg_match('/^[-_a-zA-Z0-9\.]*$/', $file))) {
				$back[] = "$path/$file";
			} elseif ($recursive 
								&& $file != '.svn' 
								&& $file != '.' 
								&& $file != '..' 
								&& is_dir("$path/$file") 
								&& !file_exists("db/$file/local.php")
			) {
				$back = array_merge($back, $this->list_files("$path/$file", $extension, $recursive));
			}
		}
		closedir($handle);
		sort($back);
		return $back;
	}

	/* nickname = fivealive or Bidi/Bidi */
	/* write = in multidomain we always write in subdir domain but we read domain/file if exists or file */
	function get_nickname_path($nickname, $styledir, $write=false)
	{
		global $tikidomain;
		if (!strstr($nickname, '\/') && !empty($tikidomain) && (is_file("$styledir/$tikidomain/$nickname.css") || $write)) {
			$style = "$styledir/$tikidomain/$nickname.css";
		} else {
			$style = "$styledir/$nickname.css";
		}
		return $style;
	}

	function browse_css($path)
	{
		if (!is_file($path)) {
			return array('error' => "No such file : $path");
		}

		$meat = implode('', file($path));

		$find[0] = '/\}/';
		$repl[0] = "\n}\n";

		$find[1] = '/\{/';
		$repl[1] = "\n{\n";

		$find[2] = '/\/\*/';
		$repl[2] = "\n/*\n";

		$find[3] = '/\*\//';
		$repl[3] = "\n*/\n";

		$find[4] = '/;/';
		$repl[4] = ";\n";

		$find[5] = '/(W|w)hite/';
		$repl[5] = '#FFFFFF';

		$find[6] = '/(B|b)lack/';
		$repl[6] = '#000000';

		$res = preg_replace($find, $repl, $meat);
		return array(
			'error' => '',
			'content' => explode("\n", $res)
		);
	}

	function parse_css($data)
	{
		$back = array();

		$index = 0;
		$type = '';

		foreach ($data as $line) {
			$line = trim($line);

			if ($line) {
				if (($type != 'comment') and ($line == '/*')) {
					$type = 'comment';

					$index++;
					$back["$index"]['comment'] = '';
					$back["$index"]['items'] = array();
					$back["$index"]['attributes'] = array();
				} elseif (($type == 'comment') and ($line == '*/')) {
					$type = '';
				} elseif ($type == 'comment') {
					$back["$index"]['comment'] .= "$line\n";
				} elseif (($type == 'items') and ($line == '{')) {
					$type = 'attributes';
				} elseif ($type == 'items') {
					$li = explode(',', $line);

					foreach ($li as $l) {
						$l = trim($l);

						if ($l)
							$back["$index"]['items'][] = $l;
					}
				} elseif (($type == 'attributes') and ($line == '}')) {
					$type = '';

					$index++;
					$back["$index"]['comment'] = '';
					$back["$index"]['items'] = array();
					$back["$index"]['attributes'] = array();
				} elseif ($type == 'attributes') {
					$parts = explode(':', str_replace(';', '', $line));

					if (isset($parts[0]) && isset($parts[1])) {
						$obj = trim($parts[0]);

						$back["$index"]['attributes']["$obj"] = trim($parts[1]);
					}
				} else {
					$li = explode(',', $line);

					foreach ($li as $l) {
						$l = trim($l);

						if ($l)
							$back["$index"]['items'][] = $l;
					}

					$type = 'items';
				}

				$back['content'] = $line;
			}
		}

		return $back;
	}

	/**
	 * Find the version of Tiki that a CSS is compatible with
	 *
	 * @TODO: cache the results
	 * @TODO: only read the first 30 lines or so of the file
	 */
	function version_css($path)
	{
		if (!file_exists($path))
			return false;

		$data = implode('', file($path));
		$pos = strpos($data, '@version');

		if ( $pos === false ) {
			return false;
		}
		// get version
		preg_match("/(@[V|v]ersion):?\s?([\d]+)\.([\d]+)/i", $data, $matches);
		$version = $matches[2] . '.' . $matches[3];

		return $version;
	}
}
$csslib = new cssLib;
