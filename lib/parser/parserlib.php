<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: parserlib.php 42165 2012-06-29 09:51:00Z changi67 $

/**
 * Parser Library
 *
 * \wiki syntax parser for tiki
 *
 * NB: Needs to be kept in utf-8
 *
 * @package		Tiki
 * @subpackage		Parser
 * @author		Robert Plummer
 * @copyright		Copyright (c) 2002-2012, All Rights Reserved.
 * 			See copyright.txt for details and a complete list of authors.
 * @license		LGPL - See license.txt for details.
 * @version		SVN $Rev$
 * @filesource
 * @link		http://dev.tiki.org/Parser
 * @since		8
 */

class ParserLib extends TikiDb_Bridge
{
	private $pre_handlers = array();
	private $pos_handlers = array();
	private $postedit_handlers = array();

	var $isHtmlPurifying = false;
	var $isEditMode = false;

	//This var is used in both protectSpecialChars and unprotectSpecialChars to simplify the html ouput process
	var $specialChars = array(
		'≤REAL_LT≥' => array(
			'html'=>		'<',
			'nonHtml'=>		'&lt;'
		),
		'≤REAL_GT≥' => array(
			'html'=>		'>',
			'nonHtml'=>		'&gt;'
		),
		'≤REAL_NBSP≥' => array(
			'html'=>		'&nbsp;',
			'nonHtml'=>		'&nbsp;'
		),
		/*on post back the page is parsed, which turns & into &amp;
		this is done to prevent that from happening, we are just
		protecting some chars from letting the parser nab them*/
		'≤REAL_AMP≥' => array(
			'html'=>		'& ',
			'nonHtml'=>		'& '
		),
	);

	//*
	function parse_data_raw($data)
	{
		$data = $this->parse_data($data);
		$data = str_replace("tiki-index", "tiki-index_raw", $data);
		return $data;
	}

	//*
	function add_pre_handler($name)
	{
		if (!in_array($name, $this->pre_handlers)) {
			$this->pre_handlers[] = $name;
		}
	}

	//*
	function add_pos_handler($name)
	{
		if (!in_array($name, $this->pos_handlers)) {
			$this->pos_handlers[] = $name;
		}
	}

	// add a post edit filter which is called when a wiki page is edited and before
	// it is committed to the database (see tiki-handlers.php on its usage)
	//*
	function add_postedit_handler($name)
	{
		if (!in_array($name, $this->postedit_handlers)) {
			$this->postedit_handlers[]=$name;
		}
	}

	// apply all the post edit handlers to the wiki page data
	//*
	function apply_postedit_handlers($data)
	{
		// Process editpage_handlers here
		foreach ($this->postedit_handlers as $handler) {
			$data = $handler($data);
		}
		return $data;
	}

	// This function handles wiki codes for those special HTML characters
	// that textarea won't leave alone.
	//*
	function parse_htmlchar(&$data, $options = array())
	{
		// cleaning some user input
		// ckeditor parses several times and messes things up, we should only let it parse once
		if ($this->isEditMode != true && !$options['ck_editor']) {
			$data = str_replace('&', '&amp;', $data);
		}

		// oft-used characters (case insensitive)
		$data = preg_replace("/~bs~/i", "&#92;", $data);
		$data = preg_replace("/~hs~/i", "&nbsp;", $data);
		$data = preg_replace("/~amp~/i", "&amp;", $data);
		$data = preg_replace("/~ldq~/i", "&ldquo;", $data);
		$data = preg_replace("/~rdq~/i", "&rdquo;", $data);
		$data = preg_replace("/~lsq~/i", "&lsquo;", $data);
		$data = preg_replace("/~rsq~/i", "&rsquo;", $data);
		$data = preg_replace("/~c~/i", "&copy;", $data);
		$data = preg_replace("/~--~/", "&mdash;", $data);
		$data = preg_replace("/ -- /", " &mdash; ", $data);
		$data = preg_replace("/~lt~/i", "&lt;", $data);
		$data = preg_replace("/~gt~/i", "&gt;", $data);

		// HTML numeric character entities
		$data = preg_replace("/~([0-9]+)~/", "&#$1;", $data);
	}

	// This function handles the protection of html entities so that they are not mangled when
	// parse_htmlchar runs, and as well so they can be properly seen, be it html or non-html
	function protectSpecialChars($data, $is_html = false, $options = array())
	{
		if (($this->isHtmlPurifying == true || (isset($options['is_html']) && $options['is_html'] != true)) || !empty($options['ck_editor'])) {
			foreach($this->specialChars as $key => $specialChar) {
				$data = str_replace($specialChar['html'], $key, $data);
			}
		}
		return $data;
	}

	// This function removed the protection of html entities so that they are rendered as expected by the viewer
	function unprotectSpecialChars($data, $is_html = false, $options = array())
	{
		if (( $is_html != false || ( isset($options['is_html']) && $options['is_html'])) 
			|| (isset($options['ck_editor']) && $options['ck_editor'])) {
			foreach($this->specialChars as $key => $specialChar) {
				$data = str_replace($key, $specialChar['html'], $data);
			}
		} else {
			foreach($this->specialChars as $key => $specialChar) {
				$data = str_replace($key, $specialChar['nonHtml'], $data);
			}
		}

		return $data;
	}

	// Reverses parse_first.
	//*
	function replace_preparse(&$data, &$preparsed, &$noparsed, $is_html = false, $options = array())
	{
		$data1 = $data;
		$data2 = "";

		// Cook until done.  Handles nested cases.
		while ( $data1 != $data2 ) {
			$data1 = $data;
			if (isset($noparsed["key"]) and count($noparsed["key"]) and count($noparsed["key"]) == count($noparsed["data"])) {
				$data = str_replace($noparsed["key"], $noparsed["data"], $data);
			}

			if (isset($preparsed["key"]) and count($preparsed["key"]) and count($preparsed["key"]) == count($preparsed["data"])) {
				$data = str_replace($preparsed["key"], $preparsed["data"], $data);
			}
			$data2 = $data;
		}

		$data = $this->unprotectSpecialChars($data, $is_html, $options);
	}

	/**
	 * Replace plugins with guid keys and store them in an array
	 *
	 * @param $data	string		data to be cleaned of plugins
	 * @param $noparsed array	output array
	 */

	function plugins_remove(&$data, &$noparsed) {

		$matches = WikiParser_PluginMatcher::match($data);		// find the plugins

		foreach ($matches as $match) {							// each plugin
			$plugin = (string) $match;
			$key = '§'.md5(TikiLib::genPass()).'§';				// by replace whole plugin with a guid

			$noparsed['key'][] = $key;
			$noparsed['data'][] = $plugin;
		}
		$data = str_replace($noparsed['data'], $noparsed['key'], $data);
	}

	/**
	 * Restore plugins from array
	 *
	 * @param $data	string		data previously processed with plugins_remove()
	 * @param $noparsed array	input array
	 */

	function plugins_replace(&$data, $noparsed, $is_html = false) {
		$preparsed = array();	// unused
		$noparsed['data'] = isset($noparsed['data']) ? str_replace('<x>', '', $noparsed['data']) : '';
		$this->replace_preparse($data, $preparsed, $noparsed, $is_html);
	}

	//*
	function plugin_match(&$data, &$plugins)
	{
		global $pluginskiplist;
		if ( !is_array($pluginskiplist) )
			$pluginskiplist = array();

		$matcher_fake = array("~pp~","~np~","&lt;pre&gt;");
		$matcher = "/\{([A-Z0-9_]+) *\(|\{([a-z]+)(\s|\})|~pp~|~np~|&lt;[pP][rR][eE]&gt;/";

		$plugins = array();
		preg_match_all($matcher, $data, $tmp, PREG_SET_ORDER);
		foreach ( $tmp as $p ) {
			if ( in_array(TikiLib::strtolower($p[0]), $matcher_fake)
				|| ( isset($p[1]) && ( in_array($p[1], $matcher_fake) || $this->plugin_exists($p[1]) ) )
				|| ( isset($p[2]) && ( in_array($p[2], $matcher_fake) || $this->plugin_exists($p[2]) ) )
			) {
				$plugins = $p;
				break;
			}
		}

		// Check to make sure there was a match.
		if ( count($plugins) > 0 && count($plugins[0])  > 0 ) {
			$pos = 0;
			while ( in_array($plugins[0], $pluginskiplist) ) {
				$pos = strpos($data, $plugins[0], $pos) + 1;
				if ( ! preg_match($matcher, substr($data, $pos), $plugins) )
					return;
			}

			// If it is a true plugin
			if ( $plugins[0]{0} == "{" ) {
				$pos = strpos($data, $plugins[0]); // where plugin starts
				$pos_end = $pos+strlen($plugins[0]); // where character after ( is

				// Here we're going to look for the end of the arguments for the plugin.

				$i = $pos_end;
				$last_data = strlen($data);

				// We start with one open curly brace, and one open paren.
				$curlies = 1;

				// If model with (
				if ( strlen($plugins[1]) ) {
					$parens = 1;
					$plugins['type'] = 'long';
				} else {
					$parens = 0;
					$plugins[1] = $plugins[2];
					unset($plugins[3]);
					$plugins['type'] = 'short';
				}

				// While we're not at the end of the string, and we still haven't found both closers
				while ( $i < $last_data ) {
					$char = substr($data, $i, 1);
					//print "<pre>Data char: $i, $char, $curlies, $parens\n.</pre>\n";
					if ( $char == "{" ) {
						$curlies++;
					} elseif ( $char == "(" && $plugins['type'] == 'long' ) {
						$parens++;
					} elseif ( $char == "}" ) {
						$curlies--;
						if ( $plugins['type'] == 'short' )
							$lastParens = $i;
					} elseif ( $char == ")"  && $plugins['type'] == 'long' ) {
						$parens--;
						$lastParens = $i;
					}

					// If we found the end of the match...
					if ( $curlies == 0 && $parens == 0 ) {
						break;
					}

					$i++;
				}

				if ( $curlies == 0 && $parens == 0 ) {
					$plugins[2] = (string) substr($data, $pos_end, $lastParens - $pos_end);
					$plugins[0] = $plugins[0] . (string) substr($data, $pos_end, $i - $pos_end + 1);
					/*
						 print "<pre>Match found: ";
						 print( $plugins[2] );
						 print "</pre>";
					 */
				}

				$plugins['arguments'] = isset($plugins[2]) ? $this->plugin_split_args($plugins[2]) : array();
			} else {
				$plugins[1] = $plugins[0];
				$plugins[2] = "";
			}
		}

		/*
			 print "<pre>Plugin match end:";
			 print_r( $plugins );
			 print "</pre>";
		 */
	}

	//*
	function plugin_split_args( $params_string )
	{
		$parser = new WikiParser_PluginArgumentParser;

		return $parser->parse($params_string);
	}

	// get all the plugins of a text- can be limitted only to some
	//*
	function getPlugins($data, $only=null)
	{
		$plugins = array();
		for (; ;) {
			$this->plugin_match($data, $plugin);
			if (empty($plugin)) {
				break;
			}
			if (empty($only) || in_array($plugin[1], $only) || in_array(TikiLib::strtoupper($plugin[1]), $only) || in_array(TikiLib::strtolower($plugin[1]), $only)) {
				$plugins[] = $plugin;
			}
			$pos = strpos($data, $plugin[0]);
			$data = substr_replace($data, '', $pos, strlen($plugin[0]));
		}
		return $plugins;
	}

	// This recursive function handles pre- and no-parse sections and plugins
	//*
	function parse_first(&$data, &$preparsed, &$noparsed, $options=null, $real_start_diff='0')
	{
		global $tikilib, $tiki_p_edit, $prefs, $pluginskiplist;
		$smarty = TikiLib::lib('smarty');

		if ( ! is_array($pluginskiplist) )
			$pluginskiplist = array();

		$is_html = (isset($options['is_html']) ? $options['is_html'] : false);
		$data = $this->protectSpecialChars($data, $is_html, $options);

		$matches = WikiParser_PluginMatcher::match($data);
		$argumentParser = new WikiParser_PluginArgumentParser;

		if (!isset($options['parseimgonly'])) {
			$options['parseimgonly'] = false;
		}

		foreach ($matches as $match) {
			if ($options['parseimgonly'] && $this->getName() != 'img') {
				continue;
			}

			//note parent plugin in case of plugins nested in an include - to suppress plugin edit icons below
			$plugin_parent = isset($plugin_name) ? $plugin_name : false;
			$plugin_name = $match->getName();
			//suppress plugin edit icons for plugins within includes since edit doesn't work for these yet
			$options['suppress_icons'] = $plugin_name != 'include' && $plugin_parent && $plugin_parent == 'include' ?
				true : $options['suppress_icons'];

			$plugin_data = $match->getBody();
			$arguments = $argumentParser->parse($match->getArguments());
			$start = $match->getStart();

			$pluginOutput = null;
			if ( $this->plugin_enabled($plugin_name, $pluginOutput) ) {

				static $plugin_indexes = array();

				if ( ! array_key_exists($plugin_name, $plugin_indexes) )
					$plugin_indexes[$plugin_name] = 0;

				$current_index = ++$plugin_indexes[$plugin_name];

				// get info to test for preview with auto_save
				if (!$options['skipvalidation']) {
					$status = $this->plugin_can_execute($plugin_name, $plugin_data, $arguments, $options['preview_mode'] || $options['ck_editor']);
				} else {
					$status = true;
				}
				global $tiki_p_plugin_viewdetail, $tiki_p_plugin_preview, $tiki_p_plugin_approve;
				$details = $tiki_p_plugin_viewdetail == 'y' && $status != 'rejected';
				$preview = $tiki_p_plugin_preview == 'y' && $details && ! $options['preview_mode'];
				$approve = $tiki_p_plugin_approve == 'y' && $details && ! $options['preview_mode'];

				if ( $status === true || ($tiki_p_plugin_preview == 'y' && $details && $options['preview_mode'] && $prefs['ajax_autosave'] === 'y') || (isset($options['noparseplugins']) && $options['noparseplugins']) ) {
					if (isset($options['stripplugins']) && $options['stripplugins']) {
						$ret = $plugin_data;
					} else if (isset($options['noparseplugins']) && $options['noparseplugins']) {
						$ret = '~np~' . (string) $match . '~/np~';
					} else {
						$ret = $this->plugin_execute($plugin_name, $plugin_data, $arguments, $start, false, $options);
					}
				} else {

					if ( $status != 'rejected' ) {
						$smarty->assign('plugin_fingerprint', $status);
						$status = 'pending';
					}

					if ($options['ck_editor']) {
						$ret = $this->convert_plugin_for_ckeditor($plugin_name, $arguments, tra('Plugin execution pending approval'), $plugin_data, array('icon' => 'img/icons/error.png'));
					} else {
						$smarty->assign('plugin_name', $plugin_name);
						$smarty->assign('plugin_index', $current_index);

						$smarty->assign('plugin_status', $status);

						if (!$options['inside_pretty']) {
							$smarty->assign('plugin_details', $details);
						} else {
							$smarty->assign('plugin_details', '');
						}
						$smarty->assign('plugin_preview', $preview);
						$smarty->assign('plugin_approve', $approve);

						$smarty->assign('plugin_body', $plugin_data);
						$smarty->assign('plugin_args', $arguments);

						$ret = '~np~' . $smarty->fetch('tiki-plugin_blocked.tpl') . '~/np~';
					}
				}
			} else {
				$ret = $pluginOutput->toWiki();
			}

			if ($ret === false) {
				continue;
			}

			if ( $this->plugin_is_editable($plugin_name) && (empty($options['preview_mode']) || !$options['preview_mode']) && empty($options['indexing']) && (empty($options['print']) || !$options['print']) && !$options['suppress_icons'] ) {
				$headerlib = TikiLib::lib('header');
				$headerlib->add_jsfile('tiki-jsplugin.php?language='.$prefs['language'], 'dynamic');
				if ($prefs['wikiplugin_module'] === 'y' && $prefs['wikiplugininline_module'] === 'n') {
					$headerlib->add_jsfile('tiki-jsmodule.php?language='.$prefs['language'], 'dynamic');
				}
				include_once('lib/smarty_tiki/function.icon.php');
				global $page;
				$id = 'plugin-edit-' . $plugin_name . $current_index;

				$headerlib->add_js(
								"\$(document).ready( function() {
if ( \$('#$id') ) {
\$('#$id').click( function(event) {
	popup_plugin_form("
								. json_encode('editwiki')
								. ', '
								. json_encode($plugin_name)
								. ', '
								. json_encode($current_index)
								. ', '
								. json_encode($page)
								. ', '
								. json_encode($arguments)
								. ', '
								. json_encode($this->unprotectSpecialChars($plugin_data, true)) //we restore it back to html here so that it can be edited, we want no modification, ie, it is brought back to html
								. ", event.target);
} );
}
} );
"
);

				$iconDisplayStyle = '';
				if ($prefs['wiki_edit_icons_toggle'] == 'y' && ($prefs['wiki_edit_plugin'] == 'y' || $prefs['wiki_edit_section'] == 'y')) {
					if (!isset($_COOKIE['wiki_plugin_edit_view'])) {
						$iconDisplayStyle = ' style="display:none;"';
					}
				}

				$ret .= '~np~<a id="' .$id. '" href="javascript:void(1)" class="editplugin"'.$iconDisplayStyle.'>'.smarty_function_icon(array('_id'=>'wiki_plugin_edit', 'alt'=>tra('Edit Plugin').':'.$plugin_name), $smarty)."</a>~/np~";
			}

			// End plugin handling

			$ret = str_replace('~/np~~np~', '', $ret);
			$match->replaceWith($ret);
		}

		$data = $matches->getText();

		$this->strip_unparsed_block($data, $noparsed);

		// ~pp~
		$start = -1;
		while (false !== $start = strpos($data, '~pp~', $start + 1)) {
			if (false !== $end = strpos($data, '~/pp~', $start)) {
				$content = substr($data, $start + 4, $end - $start - 4);

				// ~pp~ type "plugins"
				$key = "§".md5($tikilib->genPass())."§";
				$noparsed["key"][] = preg_quote($key);
				$noparsed["data"][] = '<pre>'.$content.'</pre>';
				$data = substr($data, 0, $start) . $key . substr($data, $end + 5);
			}
		}
	}

	private function strip_unparsed_block(& $data, & $noparsed)
	{
		$tikilib = TikiLib::lib('tiki');

		$start = -1;
		while (false !== $start = strpos($data, '~np~', $start + 1)) {
			if (false !== $end = strpos($data, '~/np~', $start)) {
				$content = substr($data, $start + 4, $end - $start - 4);

				// ~pp~ type "plugins"
				$key = "§".md5($tikilib->genPass())."§";
				$noparsed["key"][] = preg_quote($key);
				$noparsed["data"][] = $content;

				$data = substr($data, 0, $start) . $key . substr($data, $end + 5);
			}
		}
	}

	//*
	function plugin_get_list( $includeReal = true, $includeAlias = true )
	{
		$real = array();
		$alias = array();

		foreach ( glob('lib/wiki-plugins/wikiplugin_*.php') as $file ) {
			$base = basename($file);
			$plugin = substr($base, 11, -4);

			$real[] = $plugin;
		}

		global $prefs;
		if ( isset($prefs['pluginaliaslist']) ) {
			$alias = @unserialize($prefs['pluginaliaslist']);
			$alias = array_filter($alias);
		}

		if ( $includeReal && $includeAlias )
			$plugins = array_merge($real, $alias);
		elseif ( $includeReal )
			$plugins = $real;
		elseif ( $includeAlias )
			$plugins = $alias;
		else
			$plugins = array();

		$plugins = array_filter($plugins);
		sort($plugins);

		return $plugins;
	}

	//*
	function plugin_exists( $name, $include = false )
	{
		$php_name = 'lib/wiki-plugins/wikiplugin_';
		$php_name .= TikiLib::strtolower($name) . '.php';

		$exists = file_exists($php_name);

		if ( $include && $exists )
			include_once $php_name;

		if ( $exists )
			return true;
		elseif ( $info = $this->plugin_alias_info($name) ) {
			// Make sure the underlying implementation exists

			return $this->plugin_exists($info['implementation'], $include);
		}
		return false;
	}

	//*
	function plugin_info( $name )
	{
		static $known = array();

		if ( isset( $known[$name] ) ) {
			return $known[$name];
		}

		if ( ! $this->plugin_exists($name, true) )
			return $known[$name] = false;

		$func_name_info = "wikiplugin_{$name}_info";

		if ( ! function_exists($func_name_info) ) {
			if ( $info = $this->plugin_alias_info($name) )
				return $known[$name] = $info['description'];
			else
				return $known[$name] = false;
		}

		return $known[$name] = $func_name_info();
	}

	//*
	function plugin_alias_info( $name )
	{
		global $prefs;
		if (empty($name))
			return false;
		$name = TikiLib::strtolower($name);
		$prefName = "pluginalias_$name";

		if ( ! isset( $prefs[$prefName] ) )
			return false;

		return @unserialize($prefs[$prefName]);
	}

	//*
	function plugin_alias_store( $name, $data )
	{
		/*
			Input data structure:

			implementation: other plugin_name
			description:
				** Equivalent of plugin info function here **
			body:
				input: use|ignore
				default: body content to use
				params:
					token_name:
						input: token_name, default uses same name above
						default: value to use if missing
						encoding: none|html|url - default to none
			params:
				; Use input parameter directly
				token_name: default value

				; Custom input parameter replacement
				token_name:
					pattern: body content to use
					params:
						token_name:
							input: token_name, default uses same name above
							default: value to use if missing
							encoding: none|html|url - default to none
		*/
		if (empty($name)) {
			return;
		}

		$name = TikiLib::strtolower($name);
		$data['plugin_name'] = $name;

		$prefName = "pluginalias_$name";
		$tikilib = TikiLib::lib('tiki');
		$tikilib->set_preference($prefName, serialize($data));

		global $prefs;
		$list = array();
		if ( isset($prefs['pluginaliaslist']) )
			$list = unserialize($prefs['pluginaliaslist']);

		if ( ! in_array($name, $list) ) {
			$list[] = $name;
			$tikilib->set_preference('pluginaliaslist', serialize($list));
		}

		foreach ( glob('temp/cache/wikiplugin_*') as $file )
			unlink($file);

		$cachelib = TikiLib::lib('cache');
		$cachelib->invalidate('plugindesc');
	}

	//*
	function plugin_alias_delete( $name )
	{
		$tikilib = TikiLib::lib('tiki');
		$name = TikiLib::strtolower($name);
		$prefName = "pluginalias_$name";

		// Remove from list
		$list = $tikilib->get_preference('pluginaliaslist', array(), true);
		$list = array_diff($list, array( $name ));
		$this->set_preference('pluginaliaslist', serialize($list));

		// Remove the definition
		$tikilib->delete_preference($prefName);

		// Clear cache
		$cachelib = TikiLib::lib('cache');
		$cachelib->invalidate('plugindesc');
		foreach ( glob('temp/cache/wikiplugin_*') as $file )
			unlink($file);
	}

	//*
	function plugin_enabled( $name, & $output )
	{
		if ( ! $meta = $this->plugin_info($name) )
			return true; // Legacy plugins always execute

		global $prefs;

		$missing = array();

		if ( isset( $meta['prefs'] ) )
			foreach ( $meta['prefs'] as $pref )
				if ( $prefs[$pref] != 'y' )
					$missing[] = $pref;

		if ( count($missing) > 0 ) {
			$output = WikiParser_PluginOutput::disabled($name, $missing);
			return false;
		}

		return true;
	}

	//*
	function plugin_is_inline( $name )
	{
		if ( ! $meta = $this->plugin_info($name) )
			return true; // Legacy plugins always inline

		global $prefs;

		$inline = false;
		if ( isset( $meta['inline'] ) && $meta['inline'] )
			return true;

		$inline_pref = 'wikiplugininline_' .  $name;
		if ( isset( $prefs[ $inline_pref ] ) && $prefs[ $inline_pref ] == 'y' )
			return true;

		return false;
	}

	/**
	 * Check if possible to execute a plugin
	 *
	 * @param string $name
	 * @param string $data
	 * @param array $args
	 * @param bool $dont_modify
	 * @return bool|string Boolean true if can execute, string 'rejected' if can't execute and plugin fingerprint if pending
	 */
	//*
	function plugin_can_execute( $name, $data = '', $args = array(), $dont_modify = false )
	{
		global $prefs;

		// If validation is disabled, anything can execute
		if ( $prefs['wiki_validate_plugin'] != 'y' )
			return true;

		$meta = $this->plugin_info($name);
		if ( ! isset( $meta['validate'] ) )
			return true;

		$fingerprint = $this->plugin_fingerprint($name, $meta, $data, $args);

		$val = $this->plugin_fingerprint_check($fingerprint, $dont_modify);
		if ( strpos($val, 'accept') === 0 )
			return true;
		elseif ( strpos($val, 'reject') === 0 )
			return 'rejected';
		else {
			global $tiki_p_plugin_approve, $tiki_p_plugin_preview, $user;
			if (
				isset($_SERVER['REQUEST_METHOD'])
				&& $_SERVER['REQUEST_METHOD'] == 'POST'
				&& isset( $_POST['plugin_fingerprint'] )
				&& $_POST['plugin_fingerprint'] == $fingerprint
			) {
				if ( $tiki_p_plugin_approve == 'y' ) {
					if ( isset( $_POST['plugin_accept'] ) ) {
						global $page;
						$tikilib = TikiLib::lib('tiki');
						$this->plugin_fingerprint_store($fingerprint, 'accept');
						$tikilib->invalidate_cache($page);
						return true;
					} elseif ( isset( $_POST['plugin_reject'] ) ) {
						global $page;
						$tikilib = TikiLib::lib('tiki');
						$this->plugin_fingerprint_store($fingerprint, 'reject');
						$tikilib->invalidate_cache($page);
						return 'rejected';
					}
				}

				if ( $tiki_p_plugin_preview == 'y'
					&& isset( $_POST['plugin_preview'] ) ) {
					return true;
				}
			}

			return $fingerprint;
		}
	}

	//*
	function plugin_fingerprint_check( $fp, $dont_modify = false )
	{
		global $tikilib, $user;
		$limit = date('Y-m-d H:i:s', time() - 15*24*3600);
		$result = $this->query("SELECT `status`, if (`status`='pending' AND `last_update` < ?, 'old', '') flag FROM `tiki_plugin_security` WHERE `fingerprint` = ?", array( $limit, $fp ));

		$needUpdate = false;

		if ( $row = $result->fetchRow() ) {
			$status = $row['status'];
			$flag = $row['flag'];

			if ( $status == 'accept' || $status == 'reject' )
				return $status;

			if ( $flag == 'old' )
				$needUpdate = true;
		} else {
			$needUpdate = true;
		}

		if ( $needUpdate && !$dont_modify ) {
			global $page;
			if ( $page ) {
				$objectType = 'wiki page';
				$objectId = $page;
			} else {
				$objectType = '';
				$objectId = '';
			}

			if (!$user) {
				$user = tra('Anonymous');
			}

			$pluginSecurity = $tikilib->table('tiki_plugin_security');
			$pluginSecurity->delete(array('fingerprint' => $fp));
			$pluginSecurity->insert(
							array('fingerprint' => $fp, 'status' => 'pending',	'added_by' => $user,	'last_objectType' => $objectType, 'last_objectId' => $objectId)
			);
		}

		return '';
	}

	//*
	function plugin_fingerprint_store( $fp, $type )
	{
		global $tikilib, $prefs, $user, $page;
		if ( $page ) {
			$objectType = 'wiki page';
			$objectId = $page;
		} else {
			$objectType = '';
			$objectId = '';
		}

		$pluginSecurity = $tikilib->table('tiki_plugin_security');
		$pluginSecurity->delete(array('fingerprint' => $fp));
		$pluginSecurity->insert(
						array('fingerprint' => $fp,'status' => $type,'added_by' => $user,'last_objectType' => $objectType,'last_objectId' => $objectId)
		);
	}

	//*
	function plugin_clear_fingerprint( $fp )
	{
		global $tikilib;
		$pluginSecurity = $tikilib->table('tiki_plugin_security');
		$pluginSecurity->delete(array('fingerprint' => $fp));
	}

	//*
	function list_plugins_pending_approval()
	{
		global $tikilib;
		return $tikilib->fetchAll("SELECT `fingerprint`, `added_by`, `last_update`, `last_objectType`, `last_objectId` FROM `tiki_plugin_security` WHERE `status` = 'pending' ORDER BY `last_update` DESC");
	}

	//*
	function approve_all_pending_plugins()
	{
		global $tikilib, $user;

		$pluginSecurity = $tikilib->table('tiki_plugin_security');
		$pluginSecurity->updateMultiple(array('status' => 'accept', 'approval_by' => $user), array('status' => 'pending',));
	}

	//*
	function approve_selected_pending_plugings($fp)
	{
		global $tikilib, $user;

		$pluginSecurity = $tikilib->table('tiki_plugin_security');
		$pluginSecurity->update(array('status' => 'accept', 'approval_by' => $user), array('fingerprint' => $fp));
	}

	//*
	function plugin_fingerprint( $name, $meta, $data, $args )
	{
		$validate = (isset($meta['validate']) ? $meta['validate'] : '');

		$data = $this->unprotectSpecialChars($data, true);

		if ( $validate == 'all' || $validate == 'body' )
			$validateBody = str_replace('<x>', '', $data);	// de-sanitize plugin body to make fingerprint consistant with 5.x
		else
			$validateBody = '';

		if ( $validate == 'all' || $validate == 'arguments' ) {
			$validateArgs = $args;

			// Remove arguments marked as safe from the fingerprint
			foreach ( $meta['params'] as $key => $info ) {
				if ( isset( $validateArgs[$key] )
					&& isset( $info['safe'] )
					&& $info['safe']
				) {
					unset($validateArgs[$key]);
				}
			}
			// Parameter order needs to be stable
			ksort($validateArgs);

			if (empty($validateArgs)) {
				$validateArgs = array( '' => '' );	// maintain compatibility with pre-Tiki 7 fingerprints
			}
		} else
			$validateArgs = array();

		$bodyLen = str_pad(strlen($validateBody), 6, '0', STR_PAD_RIGHT);
		$serialized = serialize($validateArgs);
		$argsLen = str_pad(strlen($serialized), 6, '0', STR_PAD_RIGHT);

		$bodyHash = md5($validateBody);
		$argsHash = md5($serialized);

		return "$name-$bodyHash-$argsHash-$bodyLen-$argsLen";
	}

	//*
	function plugin_execute( $name, $data = '', $args = array(), $offset = 0, $validationPerformed = false, $parseOptions = array() )
	{
		global $prefs, $killtoc;

		$data = $this->unprotectSpecialChars($data, true);					// We want to give plugins original
		$args = preg_replace(array('/^&quot;/','/&quot;$/'),'',$args);		// Similarly remove the encoded " chars from the args

		$outputFormat = 'wiki';
		if ( isset($parseOptions['context_format']) ) {
			$outputFormat = $parseOptions['context_format'];
		}

		if ( ! $this->plugin_exists($name, true) ) {
			return false;
		}

		if ( ! $validationPerformed && ! $this->plugin_enabled($name, $output) ) {
			return $this->convert_plugin_output($output, '', $outputFormat, $parseOptions);
		}

		if (isset($parseOptions['inside_pretty']) && $parseOptions['inside_pretty'] === true) {
			$trklib = TikiLib::lib('trk');
			$trklib->replace_pretty_tracker_refs($args);
		}

		$func_name = 'wikiplugin_' . $name;

		if ( ! $validationPerformed ) {
			$this->plugin_apply_filters($name, $data, $args, $parseOptions);
		}

		if ( function_exists($func_name) ) {
			$pluginFormat = 'wiki';
			$info = $this->plugin_info($name);
			if ( isset( $info['format'] ) ) {
				$pluginFormat = $info['format'];
			}

			$killtoc = false;

			if ($pluginFormat === 'wiki' && $parseOptions['preview_mode'] && $_SESSION['wysiwyg'] === 'y') {	// fix lost new lines in wysiwyg plugins data
				$data = nl2br($data);
			}

			$output = $func_name($data, $args, $offset, $parseOptions);

			//This was added to remove the table of contents sometimes returned by other plugins, to use, simply have global $killtoc, and $killtoc = true;
			if ($killtoc == true) {
				while ( ($maketoc_start = strpos($output, "{maketoc")) !== false ) {
					$maketoc_end = strpos($output, "}");
					$output = substr_replace($output, "", $maketoc_start, $maketoc_end - $maketoc_start + 1);
				}
			}
			
			$killtoc = false;

			$plugin_result =  $this->convert_plugin_output($output, $pluginFormat, $outputFormat, $parseOptions);
			if (isset($parseOptions['ck_editor']) && $parseOptions['ck_editor']) {
				return $this->convert_plugin_for_ckeditor($name, $args, $plugin_result, $data, $info);
			} else {
				return $plugin_result;
			}
		} elseif ( $this->plugin_find_implementation($name, $data, $args) ) {
			return $this->plugin_execute($name, $data, $args, $offset, $validationPerformed, $parseOptions);
		}
	}

	//*
	private function convert_plugin_for_ckeditor( $name, $args, $plugin_result, $data, $info = array() )
	{
		$ck_editor_plugin = '{' . (empty($data) ? $name : TikiLib::strtoupper($name) . '(') . ' ';
		$arg_str = '';		// not using http_build_query() as it converts spaces into +
		if (!empty($args)) {
			foreach ( $args as $argKey => $argValue ) {
				if (is_array($argValue)) {
					if (isset($info['params'][$argKey]['separator'])) {
						$sep = $info['params'][$argKey]['separator'];
					} else {
						 $sep = ',';
					}
					$ck_editor_plugin .= $argKey.'="'.implode($sep, $argValue).'" ';	// process array
					$arg_str .= $argKey.'='.implode($sep, $argValue).'&';
				} else {
					$ck_editor_plugin .= $argKey.'="'.$argValue.'" ';
					$arg_str .= $argKey.'='.$argValue.'&';
				}
			}
		}
		if (substr($ck_editor_plugin, -1) === ' ') {
			$ck_editor_plugin = substr($ck_editor_plugin, 0, -1);
		}
		if (!empty($data)) {
			$ck_editor_plugin .= ')}' . $data . '{' . TikiLib::strtoupper($name) . '}';
		} else {
			$ck_editor_plugin .= '}';
		}
		// work out if I'm a nested plugin and return empty if so
		$stack = debug_backtrace();
		$plugin_nest_level = 0;
		foreach ($stack as $st) {
			if ($st['function'] === 'parse_first') {
				$plugin_nest_level ++;
				if ($plugin_nest_level > 1) {
					return '';
				}
			}
		}
		$arg_str = rtrim($arg_str, '&');
		$icon = isset($info['icon']) ? $info['icon'] : 'img/icons/wiki_plugin_edit.png';

		// some plugins are just too flakey to do wysiwyg, so show the "source" for them ;(
		if (in_array($name, array('trackerlist', 'kaltura', 'toc', 'freetagged', 'draw', 'googlemap'))) {
			$plugin_result = '&nbsp;&nbsp;&nbsp;&nbsp;' . $ck_editor_plugin;
		} else {
			// Tiki 7+ adds ~np~ to plugin output so remove them
			$plugin_result = preg_replace('/~[\/]?np~/ms', '', $plugin_result);

			$plugin_result = $this->parse_data($plugin_result, array('is_html' => false, 'suppress_icons' => true, 'ck_editor' => true, 'noparseplugins' => true));

			// remove hrefs and onclicks
			$plugin_result = preg_replace('/\shref\=/i', ' tiki_href=', $plugin_result);
			$plugin_result = preg_replace('/\sonclick\=/i', ' tiki_onclick=', $plugin_result);
			$plugin_result = preg_replace('/<script.*?<\/script>/mi', '', $plugin_result);
		}
		if (!in_array($name, array('html'))) {		// remove <p> and <br>s from non-html
			$data = str_replace(array('<p>', '</p>', "\t"), '', $data);
			$data = str_replace('<br />', "\n", $data);
		}

		if ($this->contains_html_block($plugin_result)) {
			$elem = 'div';
		} else {
			$elem = 'span';
		}
		$elem_style = 'position:relative;';
		if (in_array($name, array('img', 'div')) && preg_match('/<'.$name.'[^>]*style="(.*?)"/i', $plugin_result, $m)) {
			if (count($m)) {
				$elem_style .= $m[1];
			}
		}
		$ret = '~np~<'.$elem.' class="tiki_plugin" plugin="' . $name . '" style="' . $elem_style . '"' .
				' syntax="' . htmlentities($ck_editor_plugin, ENT_QUOTES, 'UTF-8') . '"' .
				' args="' . htmlentities($arg_str, ENT_QUOTES, 'UTF-8') . '"' .
				' body="' . htmlentities($data, ENT_QUOTES, 'UTF-8') . '">'.	// not <!--{cke_protected}
				'<img src="'.$icon.'" width="16" height="16" style="float:left;position:relative;z-index:10001" />' .
				$plugin_result.'<!-- end tiki_plugin --></'.$elem.'>~/np~';

		return 	$ret;
	}

	//*
	private function plugin_apply_filters( $name, & $data, & $args, $parseOptions )
	{
		global $tikilib;

		$info = $this->plugin_info($name);
		$default = TikiFilter::get(isset( $info['defaultfilter'] ) ? $info['defaultfilter'] : 'xss');

		// Apply filters on the body
		$filter = isset($info['filter']) ? TikiFilter::get($info['filter']) : $default;
		//$data = TikiLib::htmldecode($data);		// jb 9.0 commented out in fix for html entitles
		$data = $filter->filter($data);

		if (isset($parseOptions) && (!empty($parseOptions['is_html']) && (!$parseOptions['is_html']))) {
			$noparsed = array('data' => array(), 'key' => array());
			$this->strip_unparsed_block($data, $noparsed);
			$data = str_replace(array('<', '>'), array('&lt;', '&gt;'), $data);
			foreach ($noparsed['data'] as &$instance) {
				$instance = '~np~' . $instance . '~/np~';
			}
			unset($instance);
			$data = str_replace($noparsed['key'], $noparsed['data'], $data);
		}

		// Make sure all arguments are declared
		$params = $info['params'];

		if ( ! isset( $info['extraparams'] ) && is_array($params) ) {
			$args = array_intersect_key($args, $params);
		}

		// Apply filters on values individually
		if (!empty($args)) {
			foreach ( $args as $argKey => &$argValue ) {
				if (!isset($params[$argKey])) {
					continue;// extra params
				}
				$paramInfo = $params[$argKey];
				$filter = isset($paramInfo['filter']) ? TikiFilter::get($paramInfo['filter']) : $default;
				$argValue = TikiLib::htmldecode($argValue);

				if ( isset($paramInfo['separator']) ) {
					$vals = array();

					$vals = $tikilib->array_apply_filter($tikilib->multi_explode($paramInfo['separator'], $argValue), $filter);

					$argValue = array_values($vals);
				} else {
					$argValue = $filter->filter($argValue);
				}
			}
		}
	}

	//*
	private function convert_plugin_output( $output, $from, $to, $parseOptions )
	{
		if ( ! $output instanceof WikiParser_PluginOutput ) {
			if ( $from === 'wiki' ) {
				$output = WikiParser_PluginOutput::wiki($output);
			} elseif ( $from === 'html' ) {
				$output = WikiParser_PluginOutput::html($output);
			}
		}

		if ( $to === 'html' ) {
			return $output->toHtml($parseOptions);
		} elseif ( $to === 'wiki' ) {
			return $output->toWiki();
		}
	}

	//*
	function plugin_replace_args( $content, $rules, $args )
	{
		$patterns = array();
		$replacements = array();

		foreach ( $rules as $token => $info ) {
			$patterns[] = "%$token%";
			if ( isset( $info['input'] ) && ! empty( $info['input'] ) )
				$token = $info['input'];

			if ( isset( $args[$token] ) ) {
				$value = $args[$token];
			} else {
				$value = isset($info['default']) ? $info['default'] : '';
			}

			switch( isset($info['encoding']) ? $info['encoding'] : 'none' ) {
				case 'html': $replacements[] = htmlentities($value, ENT_QUOTES, 'UTF-8');
							 break;
				case 'url': $replacements[] = rawurlencode($value);
							 break;
				default: $replacements[] = $value;
			}
		}

		return str_replace($patterns, $replacements, $content);
	}

	//*
	function plugin_is_editable( $name )
	{
		global $tiki_p_edit, $prefs, $section;
		$info = $this->plugin_info($name);
		// note that for 3.0 the plugin editor only works in wiki pages, but could be extended later
		return $section == 'wiki page' && $info && $tiki_p_edit == 'y' && $prefs['wiki_edit_plugin'] == 'y'
			&& !$this->plugin_is_inline($name);
	}

	//*
	function quotesplit( $splitter = ',', $repl_string = '' )
	{
		global $tikilib;
		$matches = preg_match_all('/"[^"]*"/', $repl_string, $quotes);

		$quote_keys = array();
		if ( $matches ) {
			foreach ( array_unique($quotes) as $quote ) {
				$key = '§'.md5($tikilib->genPass()).'§';
				$aux["key"] = $key;
				$aux["data"] = $quote;
				$quote_keys[] = $aux;
				$repl_string = str_replace($quote[0], $key, $repl_string);
			}
		}

		$result = explode($splitter, $repl_string);

		if ( $matches ) {
			// Loop through the result sections
			while (list($rarg, $rval) = each($result)) {
				// Replace all stored strings
				foreach ( $quote_keys as $qval ) {
					$replacement = $qval["data"][0];
					$result[$rarg] = str_replace($qval["key"], $replacement, $rval);
				}
			}
		}
		return $result;
	}


	// Replace hotwords in given line
	//*
	function replace_hotwords($line, $words)
	{
		global $prefs;
		$hotw_nw = ($prefs['feature_hotwords_nw'] == 'y') ? "target='_blank'" : '';

		// Replace Hotwords
		if ($prefs['feature_hotwords'] == 'y') {
			$sep =  $prefs['feature_hotwords_sep'];
			$sep = empty($sep)? " \n\t\r\,\;\(\)\.\:\[\]\{\}\!\?\"":"$sep";
			foreach ($words as $word => $url) {
				// \b is a word boundary, \s is a space char
				$pregword = preg_replace("/\//", "\/", $word);
				$line = preg_replace("/(=(\"|')[^\"']*[$sep'])$pregword([$sep][^\"']*(\"|'))/i", "$1:::::$word,:::::$3", $line);
				$line = preg_replace("/([$sep']|^)$pregword($|[$sep])/i", "$1<a class=\"wiki\" href=\"$url\" $hotw_nw>$word</a>$2", $line);
				$line = preg_replace("/:::::$pregword,:::::/i", "$word", $line);
			}
		}
		return $line;
	}

	// Make plain text URIs in text into clickable hyperlinks
	//*
	function autolinks($text)
	{
		global $tikilib, $prefs, $smarty;
		//	check to see if autolinks is enabled before calling this function
		//		if ($prefs['feature_autolinks'] == "y") {
		$attrib = '';
		if ($prefs['popupLinks'] == 'y')
			$attrib .= 'target="_blank" ';
		if ($prefs['feature_wiki_ext_icon'] == 'y') {
			$attrib .= 'class="wiki external" ';
			include_once('lib/smarty_tiki/function.icon.php');
			$ext_icon = smarty_function_icon(array('_id'=>'external_link', 'alt'=>tra('(external link)'), '_class' => 'externallink', '_extension' => 'gif', '_defaultdir' => 'img/icons', 'width' => 15, 'height' => 14), $smarty);

		} else {
			$attrib .= 'class="wiki" ';
			$ext_icon = "";
		}

		// add a space so we can match links starting at the beginning of the first line
		$text = " " . $text;
		// match prefix://suffix, www.prefix.suffix/optionalpath, prefix@suffix
		$patterns = array();
		$replacements = array();
		$patterns[] = "#([\n ])([a-z0-9]+?)://([^<, \n\r]+)#i";
		$replacements[] = "\\1<a $attrib href=\"\\2://\\3\">\\2://\\3$ext_icon</a>";
		$patterns[] = "#([\n ])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[^,< \n\r]*)?)#i";
		$replacements[] = "\\1<a $attrib href=\"http://www.\\2.\\3\\4\">www.\\2.\\3\\4$ext_icon</a>";
		$patterns[] = "#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i";
		if ($prefs['feature_wiki_protect_email'] == 'y')
			$replacements[] = "\\1" . $tikilib->protect_email("\\2", "\\3");
		else
			$replacements[] = "\\1<a class='wiki' href=\"mailto:\\2@\\3\">\\2@\\3</a>";
		$patterns[] = "#([\n ])magnet\:\?([^,< \n\r]+)#i";
		$replacements[] = "\\1<a class='wiki' href=\"magnet:?\\2\">magnet:?\\2</a>";
		$text = preg_replace($patterns, $replacements, $text);
		// strip the space we added
		$text = substr($text, 1);
		return $text;

		//		} else {
		//			return $text;
		//		}
	}

	// split string into a list of
	//*
	function split_tag( $string, $cleanup = TRUE )
	{
		$_splts = explode('&quot;', $string);
		$inside = FALSE;
		$parts = array();
		$index=0;

		foreach ($_splts as $i) {
			if ($cleanup) {
				$i = str_replace('}', '', $i);
				$i = str_replace('{', '', $i);
				$i = str_replace('\'', '', $i);
				$i = str_replace('"', '', $i);
				// IE silently removes null-byte html char \0, so let's remove it anyways
				$i = str_replace('\\0', '', $i);
			}

			if ($inside) {  // inside "foo bar" - append
				if ($index>0) {
					$parts[$index-1] .= $i;
				} else {    // else: first element (should never happen)
					$parts[] = $i;
				}
			} else {        //
				$_spl = explode(" ", $i);
				foreach ($_spl as $j) {
					$parts[$index++] = $j;
				}
			}
			$inside = ! $inside;
		}
		return $parts;
	}

	//*
	function split_assoc_array($parts, $assoc)
	{
		//$assoc = array();
		foreach ($parts as $part) {
			$res=array();
			$assoc[$part] = '';
			preg_match("/(\w+)\s*=\s*(.*)/", $part, $res);
			if ($res) {
				$assoc[$res[1]] = $res[2];
			}
		}
		return $assoc;
	}

	/**
	 * close_blocks - Close out open paragraph, lists, and div's
	 *
	 * During parse_data, information is kept on blocks of text (paragraphs, lists, divs)
	 * that need to be closed out. This function does that, rather than duplicating the
	 * code inline.
	 *
	 * @param	$data			- Output data
	 * @param	$in_paragraph		- TRUE if there is an open paragraph
	 * @param	$listbeg		- array of open list terminators
	 * @param	$divdepth		- array indicating how many div's are open
	 * @param	$close_paragraph	- TRUE if open paragraph should be closed.
	 * @param	$close_lists		- TRUE if open lists should be closed.
	 * @param	$close_divs		- TRUE if open div's should be closed.
	 */
	/* private */
	//*
	function close_blocks(&$data, &$in_paragraph, &$listbeg, &$divdepth, $close_paragraph, $close_lists, $close_divs)
	{

		$closed = 0;	// Set to non-zero if something has been closed out
		// Close the paragraph if inside one.
		if ($close_paragraph && $in_paragraph) {
			$data .= "</p>\n";
			$in_paragraph = 0;
			$closed++;
		}
		// Close open lists
		if ($close_lists) {
			while (count($listbeg)) {
				$data .= array_shift($listbeg);
				$closed++;
			}
		}

		// Close open divs
		if ($close_divs) {
			$temp_max = count($divdepth);
			for ($i = 1; $i <= $temp_max; $i++) {
				$data .= '</div>';
				$closed++;
			}
		}

		return $closed;
	}

	//PARSEDATA
	// options defaults : is_html => false, absolute_links => false, language => ''
	//*
	function parse_data($data, $options = null)
	{
		global $tikilib;
		// Don't bother if there's nothing...
		if (function_exists('mb_strlen')) {
			if ( mb_strlen($data) < 1 ) {
				return;
			}
		}

		global $page_regex, $slidemode, $prefs, $ownurl_father, $tiki_p_upload_picture, $page, $page_ref_id, $user, $tikidomain, $tikiroot;
		$wikilib = TikiLib::lib('wiki');

		// Handle parsing options
		if ( $options == null ) $options = array();
		$options['is_html'] = $is_html = isset($options['is_html']) ? $options['is_html'] : false;
		$options['absolute_links'] = $absolute_links = isset($options['absolute_links']) ? $options['absolute_links'] : false;
		$options['language'] = $language = isset($options['language']) ? $options['language'] : '';
		$options['noparseplugins'] = $noparseplugins = isset($options['noparseplugins']) ? $options['noparseplugins'] : false;
		$options['stripplugins'] = $stripplugins = isset($options['stripplugins']) ? $options['stripplugins'] : false;
		$options['noheaderinc'] = $noheaderinc = isset($options['noheaderinc']) ? $options['noheaderinc'] : false;
		$options['page'] = isset($options['page']) ? $options['page'] : $page;
		$options['print'] = isset($options['print']) ? $options['print'] : false;
		$options['parseimgonly'] = isset($options['parseimgonly']) ? $options['parseimgonly'] : false;
		$options['preview_mode'] = isset($options['preview_mode']) ? (bool)$options['preview_mode'] : false;
		$options['suppress_icons'] = isset($options['suppress_icons']) ? (bool)$options['suppress_icons'] : false;
		$options['parsetoc'] = isset($options['parsetoc']) ? (bool)$options['parsetoc'] : true;
		$options['inside_pretty'] = isset($options['inside_pretty']) ? $options['inside_pretty'] : false;
		$options['process_wiki_paragraphs'] = isset($options['process_wiki_paragraphs']) ? $options['process_wiki_paragraphs'] : true;
		$options['min_one_paragraph'] = isset($options['min_one_paragraph']) ? $options['min_one_paragraph'] : false;
		$options['skipvalidation'] = isset($options['skipvalidation']) ? $options['skipvalidation'] : false;

		if (empty($options['ck_editor'])) $options['ck_editor'] = false;

		$old_wysiwyg_parsing = null;
		if ($options['ck_editor']) {
			$headerlib = TikiLib::lib('header');
			$old_wysiwyg_parsing = $headerlib->wysiwyg_parsing;
			$headerlib->wysiwyg_parsing = true;
		}

		//The following will stop and return based off new parser
		if ($prefs['feature_jison_wiki_parser'] == 'y') {
			//Testing new parser ;)
			$parser = new JisonParser_Wiki_Handler();
			return $parser->parse($data);
		}

		// if simple_wiki is true, disable some wiki syntax
		// basically, allow wiki plugins, wiki links and almost
		// everything between {}
		$simple_wiki = false;
		if ($prefs['feature_wysiwyg'] == 'y' and $is_html) {
			if ($prefs['wysiwyg_wiki_semi_parsed'] == 'y') {
				$simple_wiki = true;
			} elseif ($prefs['wysiwyg_wiki_parsed'] == 'n') {
				return $data;
			}
		}

		$this->parse_wiki_argvariable($data, $options);

		/* <x> XSS Sanitization handling */

		// Converts &lt;x&gt; (<x> tag using HTML entities) into the tag <x>. This tag comes from the input sanitizer (XSS filter).
		// This is not HTML valid and avoids using <x> in a wiki text,
		//   but hide '<x>' text inside some words like 'style' that are considered as dangerous by the sanitizer.
		$data = str_replace(array( '&lt;x&gt;', '~np~', '~/np~' ), array( '<x>', '~np~', '~/np~' ), $data);

		// Fix false positive in wiki syntax
		//   It can't be done in the sanitizer, that can't know if the input will be wiki parsed or not
		$data = preg_replace('/(\{img [^\}]+li)<x>(nk[^\}]+\})/i', '\\1\\2', $data);

		// Process pre_handlers here
		if (is_array($this->pre_handlers)) {
			foreach ($this->pre_handlers as $handler) {
				$data = $handler($data);
			}
		}

		// Handle pre- and no-parse sections and plugins
		$preparsed = array('data'=>array(),'key'=>array());
		$noparsed = array('data'=>array(),'key'=>array());
		if (!$noparseplugins || $stripplugins) {
			$this->parse_first($data, $preparsed, $noparsed, $options);
			$this->parse_wiki_argvariable($data, $options);
		}

		// Handle ~pre~...~/pre~ sections
		$data = preg_replace(';~pre~(.*?)~/pre~;s', '<pre>$1</pre>', $data);

		// Strike-deleted text --text-- (but not in the context <!--[if IE]><--!> or <!--//--<!CDATA[//><!--
		if (!$simple_wiki) {
			// FIXME produces false positive for strings contining html comments. e.g: --some text<!-- comment -->
			$data = preg_replace("#(?<!<!|//)--([^\s>].+?)--#", "<strike>$1</strike>", $data);
		}

		// Handle comment sections
		$data = preg_replace(';~tc~(.*?)~/tc~;s', '', $data);
		$data = preg_replace(';~hc~(.*?)~/hc~;s', '<!-- $1 -->', $data);

		// Replace special characters
		// done after url catching because otherwise urls of dyn. sites will be modified
		// not done in wysiwyg mode, i.e. $prefs['feature_wysiwyg'] set to something other than 'no' or not set at all
		//			if (!$simple_wiki and $prefs['feature_wysiwyg'] == 'n')
		//above line changed by mrisch - special functions were not parsing when wysiwyg is set but wysiswyg is not enabled
		// further changed by nkoth - why not parse in wysiwyg mode as well, otherwise it won't parse for display/preview?
		// must be done before color as we can have ~hs~~hs
		// jb 9.0 html entity fix - excluded not $options['is_html'] pages
		if (!$simple_wiki && !$options['is_html']) {
			$this->parse_htmlchar($data, $options);
		}

		//needs to be before text color syntax because of use of htmlentities in lib/core/WikiParser/OutputLink.php
		$data = $this->parse_data_wikilinks($data, $simple_wiki, $options['ck_editor']);

		if (!$simple_wiki) {
			// Replace colors ~~foreground[,background]:text~~
			// must be done before []as the description may contain color change
			$parse_color = 1;
			while ($parse_color) { // handle nested colors, parse innermost first
				$data = preg_replace("/~~([^~:,]+)(,([^~:]+))?:([^~]*)(?!~~[^~:,]+(?:,[^~:]+)?:[^~]*~~)~~/Ums", "<span style=\"color:$1; background-color:$3\">$4</span>", $data, -1, $parse_color);
			}
		}

		// Extract [link] sections (to be re-inserted later)
		$noparsedlinks = array();

		// This section matches [...].
		// Added handling for [[foo] sections.  -rlpowell
		if (!$simple_wiki) {
			preg_match_all("/(?<!\[)(\[[^\[][^\]]+\])/", $data, $noparseurl);

			foreach (array_unique($noparseurl[1])as $np) {
				$key = '§'.md5($tikilib->genPass()).'§';

				$aux["key"] = $key;
				$aux["data"] = $np;
				$noparsedlinks[] = $aux;
				$data = preg_replace('/(^|[^a-zA-Z0-9])'.preg_quote($np, '/').'([^a-zA-Z0-9]|$)/', '\1'.$key.'\2', $data);
			}
		}

		// BiDi markers
		$bidiCount = 0;
		$bidiCount = preg_match_all("/(\{l2r\})/", $data, $pages);
		$bidiCount += preg_match_all("/(\{r2l\})/", $data, $pages);

		$data = preg_replace("/\{l2r\}/", "<div dir='ltr'>", $data);
		$data = preg_replace("/\{r2l\}/", "<div dir='rtl'>", $data);
		$data = preg_replace("/\{lm\}/", "&lrm;", $data);
		$data = preg_replace("/\{rm\}/", "&rlm;", $data);
		// smileys
		$data = $this->parse_smileys($data);

		$data = $this->parse_data_dynamic_variables($data, $options['language']);

		if (!$simple_wiki) {
			// Replace boxes
			$delim = (isset($prefs['feature_simplebox_delim']) && $prefs['feature_simplebox_delim'] !="" )?preg_quote($prefs['feature_simplebox_delim']):preg_quote("^");
			$data = preg_replace("/${delim}(.+?)${delim}/s", "<div class=\"simplebox\">$1</div>", $data);

			// Underlined text
			$data = preg_replace("/===(.+?)===/", "<u>$1</u>", $data);
			// Center text
			if ($prefs['feature_use_three_colon_centertag'] == 'y') {
				$data = preg_replace("/:::(.+?):::/", "<div style=\"text-align: center;\">$1</div>", $data);
			} else {
				$data = preg_replace("/::(.+?)::/", "<div style=\"text-align: center;\">$1</div>", $data);
			}
		}

		// reinsert hash-replaced links into page
		foreach ($noparsedlinks as $np) {
			$data = str_replace($np["key"], $np["data"], $data);
		}

		$data = $this->parse_data_externallinks($data, $options);

		$data = $this->parse_data_tables($data, $simple_wiki);

		if (!$simple_wiki && $options['parsetoc']) {
			$this->parse_data_process_maketoc($data, $options, $noparsed);

		} else {
			$data = $this->parse_data_simple($data);
		}

		// linebreaks using %%%
		$data = preg_replace("/\n?%%%/", "<br />", $data);

		// Close BiDi DIVs if any
		for ($i = 0; $i < $bidiCount; $i++) {
			$data .= "</div>";
		}

		// Put removed strings back.
		$this->replace_preparse($data, $preparsed, $noparsed, $is_html, $options);

		// Process pos_handlers here
		foreach ($this->pos_handlers as $handler) {
			$data = $handler($data);
		}
		if ($old_wysiwyg_parsing !== null) {
			$headerlib->wysiwyg_parsing = $old_wysiwyg_parsing;
		}

		return $data;
	}

	//*
	function parse_data_simple( $data )
	{
		global $prefs;
		$words = array();

		if ( $prefs['feature_hotwords'] == 'y' ) {
			// Get list of HotWords
			$words = $this->get_hotwords();
		}

		$data = $this->parse_data_wikilinks($data, true);
		$data = $this->parse_data_externallinks($data, array( 'suppress_icons' => true ));
		$data = $this->parse_data_inline_syntax($data, $words);

		return $data;
	}

	//*
	private function parse_data_wikilinks( $data, $simple_wiki, $ck_editor = false )
	{
		global $page_regex, $prefs;

		// definitively put out the protected words ))protectedWord((
		if ($prefs['feature_wikiwords'] == 'y' ) {
			preg_match_all("/\)\)(\S+?)\(\(/", $data, $matches);
			$noParseWikiLinksK = array();
			$noParseWikiLinksT = array();
			foreach ($matches[0] as $mi=>$match) {
				do {
					$randNum = chr(0xff).rand(0, 1048576).chr(0xff);
				} while (strstr($data, $randNum));
				$data = str_replace($match, $randNum, $data);
				$noParseWikiLinksK[] = $randNum;
				$noParseWikiLinksT[] = $matches[1][$mi];
			}
		}

		// Links with description
		preg_match_all("/\(([a-z0-9-]+)?\(($page_regex)\|([^\)]*?)\)\)/", $data, $pages);

		$temp_max = count($pages[1]);
		for ($i = 0; $i < $temp_max; $i++) {
			$exactMatch = $pages[0][$i];
			$description = $pages[6][$i];
			$anchor = null;

			if ($description{0} == '#') {
				$temp = $description;
				$anchor = strtok($temp, '|');
				$description = strtok('|');
			}

			$replacement = $this->get_wiki_link_replacement($pages[2][$i], array('description' => $description,'reltype' => $pages[1][$i],'anchor' => $anchor), $ck_editor);

			$data = str_replace($exactMatch, $replacement, $data);
		}

		// Wiki page syntax without description
		preg_match_all("/\(([a-z0-9-]+)?\( *($page_regex) *\)\)/", $data, $pages);

		foreach ($pages[2] as $idx => $page_parse) {
			$exactMatch = $pages[0][$idx];
			$replacement = $this->get_wiki_link_replacement($page_parse, array( 'reltype' => $pages[1][$idx] ), $ck_editor);

			$data = str_replace($exactMatch, $replacement, $data);
		}

		// Links to internal pages
		// If they are parenthesized then don't treat as links
		// Prevent ))PageName(( from being expanded \"\'
		//[A-Z][a-z0-9_\-]+[A-Z][a-z0-9_\-]+[A-Za-z0-9\-_]*
		if ( ! $simple_wiki && $prefs['feature_wiki'] == 'y' && $prefs['feature_wikiwords'] == 'y' ) {
			// The first part is now mandatory to prevent [Foo|MyPage] from being converted!
			if ($prefs['feature_wikiwords_usedash'] == 'y') {
				preg_match_all("/(?<=[ \n\t\r\,\;]|^)([A-Z][a-z0-9_\-\x80-\xFF]+[A-Z][a-z0-9_\-\x80-\xFF]+[A-Za-z0-9\-_\x80-\xFF]*)(?=$|[ \n\t\r\,\;\.])/", $data, $pages);
			} else {
				preg_match_all("/(?<=[ \n\t\r\,\;]|^)([A-Z][a-z0-9\x80-\xFF]+[A-Z][a-z0-9\x80-\xFF]+[A-Za-z0-9\x80-\xFF]*)(?=$|[ \n\t\r\,\;\.])/", $data, $pages);
			}
			//TODO to have a real utf8 Wikiword where the capitals can be a utf8 capital
			$words = ( $prefs['feature_hotwords'] == 'y' ) ? $this->get_hotwords() : array();
			foreach ( array_unique($pages[1]) as $page_parse ) {
				if ( ! array_key_exists($page_parse, $words) ) {
					$repl = $this->get_wiki_link_replacement($page_parse, array('plural' => $prefs['feature_wiki_plurals'] == 'y' ), $ck_editor);

					$data = preg_replace("/(?<=[ \n\t\r\,\;]|^)$page_parse(?=$|[ \n\t\r\,\;\.])/", "$1" . $repl . "$2", $data);
				}
			}
		}

		// Reinsert ))Words((
		if ($prefs['feature_wikiwords'] == 'y' ) {
			$data = str_replace($noParseWikiLinksK, $noParseWikiLinksT, $data);
		}

		return $data;
	}

	private function parse_data_externallinks( $data, $options )
	{
		global $tikilib, $prefs;

		// *****
		// This section handles external links of the form [url] and such.
		// *****

		$links = $tikilib->get_links($data);
		$notcachedlinks = $tikilib->get_links_nocache($data);
		$cachedlinks = array_diff($links, $notcachedlinks);
		$tikilib->cache_links($cachedlinks);

		// Note that there're links that are replaced
		foreach ($links as $link) {
			$target = '';
			$class = 'class="wiki"';
			$ext_icon = '';
			$rel='';

			if ($prefs['popupLinks'] == 'y') {
				$target = 'target="_blank"';
			}
			if (!strstr($link, '://')) {
				$target = '';
			} else {
				$class = 'class="wiki external"';
				if ($prefs['feature_wiki_ext_icon'] == 'y' && !$options['suppress_icons']) {
					$smarty = TikiLib::lib('smarty');
					include_once('lib/smarty_tiki/function.icon.php');
					$ext_icon = smarty_function_icon(array('_id'=>'external_link', 'alt'=>tra('(external link)'), '_class' => 'externallink', '_extension' => 'gif', '_defaultdir' => 'img/icons', 'width' => 15, 'height' => 14), $smarty);
				}
				$rel='external';
				if ($prefs['feature_wiki_ext_rel_nofollow'] == 'y') {
					$rel .= ' nofollow';
				}
			}

			// The (?<!\[) stuff below is to give users an easy way to
			// enter square brackets in their output; things like [[foo]
			// get rendered as [foo]. -rlpowell

			if ($prefs['cachepages'] == 'y' && $tikilib->is_cached($link)) {
				//use of urlencode for using cached versions of dynamic sites
				$cosa = "<a class=\"wikicache\" target=\"_blank\" href=\"tiki-view_cache.php?url=".urlencode($link)."\">(cache)</a>";

				$link2 = str_replace("/", "\/", preg_quote($link));
				$pattern = "/(?<!\[)\[$link2\|([^\]\|]+)\|([^\]\|]+)\|([^\]]+)\]/"; //< last param expected here is always nocache
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$2 $rel\">$1</a>$ext_icon", $data);
				$pattern = "/(?<!\[)\[$link2\|([^\]\|]+)\|([^\]]+)\]/";//< last param here ($2) is used for relation (rel) attribute (e.g. shadowbox) or nocache
				preg_match($pattern, $data, $matches);
				if (isset($matches[2]) && $matches[2]=='nocache') {
					$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$rel\">$1</a>$ext_icon", $data);
				} else {
					$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$2 $rel\">$1</a>$ext_icon $cosa", $data);
				}
				$pattern = "/(?<!\[)\[$link2\|([^\]\|]+)\]/";
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$rel\">$1</a>$ext_icon $cosa", $data);
				$pattern = "/(?<!\[)\[$link2\]/";
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$rel\">$link</a>$ext_icon $cosa", $data);
			} else {
				$link2 = str_replace("/", "\/", preg_quote($link));
				$data = str_replace("|nocache", "", $data);

				$pattern = "/(?<!\[)\[$link2\|([^\]\|]+)\|([^\]]+)\]/";
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$2 $rel\">$1</a>$ext_icon", $data);
				$pattern = "/(?<!\[)\[$link2\|([^\]\|]+)([^\]])*\]/";
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$rel\">$1</a>$ext_icon", $data);
				$pattern = "/(?<!\[)\[$link2\]/";
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$rel\">$link</a>$ext_icon", $data);
			}
		}

		// Handle double square brackets. to display [foo] use [[foo] -rlpowell. Improved by sylvieg to avoid replacing them in [[code]] cases.
		if (empty($options['process_double_brackets']) || $options['process_double_brackets'] != 'n') {
			$data = preg_replace("/\[\[([^\]]*)\](?!\])/", "[$1]", $data);
			$data = preg_replace("/\[\[([^\]]*)$/", "[$1", $data);
		}

		return $data;
	}

	//*
	private function parse_data_inline_syntax( $line, $words = array(), $ck_editor = false )
	{
		global $prefs;

		if (!$ck_editor) {
			if ($prefs['feature_hotwords'] == 'y') {
				// Replace Hotwords before begin
				$line = $this->replace_hotwords($line, $words);
			}

			// Make plain URLs clickable hyperlinks
			if ($prefs['feature_autolinks'] == 'y') {
				$line = $this->autolinks($line);
			}
		}

		// Replace monospaced text
		$line = preg_replace("/(^|\s)-\+(.*?)\+-/", "$1<code>$2</code>", $line);
		// Replace bold text
		$line = preg_replace("/__(.*?)__/", "<strong>$1</strong>", $line);
		// Replace italic text
		$line = preg_replace("/\'\'(.*?)\'\'/", "<em>$1</em>", $line);
		
		if (!$ck_editor) {
			// Replace definition lists
			$line = preg_replace("/^;([^:]*):([^\/\/].*)/", "<dl><dt>$1</dt><dd>$2</dd></dl>", $line);
			$line = preg_replace("/^;(<a [^<]*<\/a>):([^\/\/].*)/", "<dl><dt>$1</dt><dd>$2</dd></dl>", $line);
		}

		return $line;
	}

	//*
	private function parse_data_tables( $data, $simple_wiki )
	{
		global $prefs;

		/*
		 * Wiki Tables syntax
		 */
		// tables in old style
		if ($prefs['feature_wiki_tables'] != 'new') {
			if (preg_match_all("/\|\|(.*)\|\|/", $data, $tables)) {
				$maxcols = 1;
				$cols = array();
				$temp_max = count($tables[0]);
				for ($i = 0; $i < $temp_max; $i++) {
					$rows = explode('||', $tables[0][$i]);
					$temp_max2 = count($rows);
					for ($j = 0; $j < $temp_max2; $j++) {
						$cols[$i][$j] = explode('|', $rows[$j]);
						if (count($cols[$i][$j]) > $maxcols)
							$maxcols = count($cols[$i][$j]);
					}
				} // for ($i ...

				$temp_max3 = count($tables[0]);
				for ($i = 0; $i < $temp_max3; $i++) {
					$repl = '<table class="wikitable">';

					$temp_max4 = count($cols[$i]);
					for ($j = 0; $j < $temp_max4; $j++) {
						$ncols = count($cols[$i][$j]);

						if ($ncols == 1 && !$cols[$i][$j][0])
							continue;

						$repl .= '<tr>';

						for ($k = 0; $k < $ncols; $k++) {
							$repl .= '<td class="wikicell" ';

							if ($k == $ncols - 1 && $ncols < $maxcols)
								$repl .= ' colspan="' . ($maxcols - $k).'"';

							$repl .= '>' . $cols[$i][$j][$k] . '</td>';
						} // // for ($k ...

						$repl .= '</tr>';
					} // for ($j ...

					$repl .= '</table>';
					$data = str_replace($tables[0][$i], $repl, $data);
				} // for ($i ...
			} // if (preg_match_all("/\|\|(.*)\|\|/", $data, $tables))
		} else {
			// New syntax for tables
			// REWRITE THIS CODE
			if (!$simple_wiki) {
				if (preg_match_all("/\|\|(.*?)\|\|/s", $data, $tables)) {
					$maxcols = 1;
					$cols = array();
					$temp_max5 = count($tables[0]);
					for ($i = 0; $i < $temp_max5; $i++) {
						$rows = preg_split("/(\n|\<br\/\>)/", $tables[0][$i]);
						$col[$i] = array();
						$temp_max6 = count($rows);
						for ($j = 0; $j < $temp_max6; $j++) {
							$rows[$j] = str_replace('||', '', $rows[$j]);
							$cols[$i][$j] = explode('|', $rows[$j]);
							if (count($cols[$i][$j]) > $maxcols)
								$maxcols = count($cols[$i][$j]);
						}
					}

					$temp_max7 = count($tables[0]);
					for ($i = 0; $i < $temp_max7; $i++) {
						$repl = '<table class="wikitable">';
						$temp_max8 = count($cols[$i]);
						for ($j = 0; $j < $temp_max8; $j++) {
							$ncols = count($cols[$i][$j]);

							if ($ncols == 1 && !$cols[$i][$j][0])
								continue;

							$repl .= '<tr>';

							for ($k = 0; $k < $ncols; $k++) {
								$repl .= '<td class="wikicell" ';
								if ($k == $ncols - 1 && $ncols < $maxcols)
									$repl .= ' colspan="' . ($maxcols - $k).'"';

								$repl .= '>' . $cols[$i][$j][$k] . '</td>';
							}
							$repl .= '</tr>';
						}
						$repl .= '</table>';
						$data = str_replace($tables[0][$i], $repl, $data);
					}
				}
			}
		}

		return $data;
	}

	//*
	function parse_wiki_argvariable(&$data, $options=null)
	{
		global $prefs, $user;
		if ( $prefs['feature_wiki_argvariable'] == 'y' && !$options['ck_editor'] ) {
			if (preg_match_all("/\\{\\{((\w+)(\\|([^\\}]*))?)\\}\\}/", $data, $args, PREG_SET_ORDER)) {
				$needles = array();
				$replacements = array();

				foreach ( $args as $arg ) {
					$value = isset($arg[4])?$arg[4]:'';
					$name = $arg[2];
					switch( $name ) {
					case 'user':
						$value = $user;
									break;
					case 'page':
						$value = $options['page'];
									break;
					default:
						if ( isset($_GET[$name]) )
							$value = $_GET[$name];
									break;
					}

					$needles[] = $arg[0];
					$replacements[] = $value;
				
				}
				$data = str_replace($needles, $replacements, $data);
			}
		}
	}
	
	//*
	private function parse_data_dynamic_variables( $data, $lang = null )
	{
		global $tiki_p_edit_dynvar, $prefs;

		$enclose = '%';
		if ( $prefs['wiki_dynvar_style'] == 'disable' ) {
			return $data;
		} elseif ( $prefs['wiki_dynvar_style'] == 'double' ) {
			$enclose = '%%';
		}

		// Replace dynamic variables
		// Dynamic variables are similar to dynamic content but they are editable
		// from the page directly, intended for short data, not long text but text
		// will work too
		//     Now won't match HTML-style '%nn' letter codes and some special utf8 situations...
		if (preg_match_all("/[^%]$enclose([^% 0-9A-Z][^% 0-9A-Z][^% ]*){$enclose}[^%]/", $data, $dvars)) {
			// remove repeated elements
			$dvars = array_unique($dvars[1]);
			// Now replace each dynamic variable by a pair composed of the
			// variable value and a text field to edit the variable. Each
			foreach ($dvars as $dvar) {
				$value = $this->get_dynamic_variable($dvar, $lang);
				// Now build 2 divs
				$id = 'dyn_'.$dvar;

				if (isset($tiki_p_edit_dynvar)&& $tiki_p_edit_dynvar=='y') {
					$span1 = "<span  style='display:inline;' id='dyn_".$dvar."_display'><a class='dynavar' onclick='javascript:toggle_dynamic_var(\"$dvar\");' title='".tra('Click to edit dynamic variable', '', true).": $dvar'>$value</a></span>";
					$span2 = "<span style='display:none;' id='dyn_".$dvar."_edit'><input type='text' name='dyn_".$dvar."' value='".$value."' />".'<input type="submit" name="_dyn_update" value="'.tra('Update variables', '', true).'"/></span>';
				} else {
					$span1 = "<span class='dynavar' style='display:inline;' id='dyn_".$dvar."_display'>$value</span>";
					$span2 = '';
				}
				$html = $span1.$span2;
				//It's important to replace only once
				$dvar_preg = preg_quote($dvar);
				$data = preg_replace("+$enclose$dvar_preg$enclose+", $html, $data, 1);
				//Further replacements only with the value
				$data = str_replace("$enclose$dvar$enclose", $value, $data);
			}
			//At the end put an update button
			//<br /><div align="center"><input type="submit" name="dyn_update" value="'.tra('Update variables','',true).'"/></div>
			$data='<form method="post" name="dyn_vars">'."\n".$data.'</form>';
		}

		return $data;
	}

	//*
	private function get_dynamic_variable( $name, $lang = null )
	{
		global $tikilib;
		$result = $tikilib->table('tiki_dynamic_variables')->fetchAll(array('data', 'lang'), array('name' => $name));

		$value = "NaV";

		foreach ( $result as $row ) {
			if ( $row['lang'] == $lang ) {
				// Exact match
				return $row['data'];
			} elseif ( empty( $row['lang'] ) ) {
				// Universal match, keep in case no exact match
				$value = $row['data'];
			}
		}

		return $value;
	}

	//*
	private function parse_data_process_maketoc( &$data, $options, $noparsed)
	{

		global $tikilib, $prefs;

		$this->makeTocCount++;

		if ( $options['ck_editor'] ) {
			$need_maketoc = false ;
		} else {
			$need_maketoc = strpos($data, "{maketoc");
		}

		// Wysiwyg or allowhtml mode {maketoc} handling when not in editor mode (i.e. viewing)
		if ($need_maketoc && $options['is_html']) {
			// Header needs to start at beginning of line (wysiwyg does not necessary obey)
			$data = $this->unprotectSpecialChars($data, true);
			$data = preg_replace('/<\/([a-z]+)><h([1-6])>/im', "</\\1>\n<h\\2>", $data);
			$data = preg_replace('/^\s+<h([1-6])>/im', "<h\\1>", $data); // headings with leading spaces
			$data = preg_replace('/\/><h([1-6])>/im', "/>\n<h\\1>", $data); // headings after /> tag
			$htmlheadersearch = '/<h([1-6])>\s*([^<]+)\s*<\/h[1-6]>/im';
			preg_match_all($htmlheadersearch, $data, $htmlheaders);
			$nbhh=count($htmlheaders[1]);
			for ($i = 0; $i < $nbhh; $i++) {
				$htmlheaderreplace = '';
				for ($j = 0; $j < $htmlheaders[1][$i]; $j++) {
					$htmlheaderreplace .= '!';
				}
				$htmlheaderreplace .= $htmlheaders[2][$i];
				$data = str_replace($htmlheaders[0][$i], $htmlheaderreplace, $data);
			}
			$data = $this->protectSpecialChars($data, true);
		}

		$need_autonumbering = ( preg_match('/^\!+[\-\+]?#/m', $data) > 0 );

		$anch = array();
		global $anch;
		$pageNum = 1;

		// 08-Jul-2003, by zaufi
		// HotWords will be replace only in ordinal text
		// It looks __really__ goofy in Headers or Titles

		$words = array();
		if ( $prefs['feature_hotwords'] == 'y' ) {
			// Get list of HotWords
			$words = $this->get_hotwords();
		}

		// Now tokenize the expression and process the tokens
		// Use tab and newline as tokenizing characters as well  ////
		$lines = explode("\n", $data);
		if (empty($lines[count($lines)-1]) && empty($lines[count($lines)-2])) {
			array_pop($lines);
		}
		$data = '';
		$listbeg = array();
		$divdepth = array();
		$hdr_structure = array();
		$show_title_level = array();
		$last_hdr = array();
		$nb_last_hdr = 0;
		$nb_hdrs = 0;
		$inTable = 0;
		$inPre = 0;
		$inComment = 0;
		$inTOC = 0;
		$inScript = 0;
		$title_text = '';

		// loop: process all lines
		$in_paragraph = 0;
		$in_empty_paragraph = 0;

		foreach ($lines as $line) {
			$current_title_num = '';
			$numbering_remove = 0;

			$line = rtrim($line); // Trim off trailing white space
			// Check for titlebars...
			// NOTE: that title bar should start at the beginning of the line and
			//	   be alone on that line to be autoaligned... otherwise, it is an old
			//	   styled title bar...
			if (substr(ltrim($line), 0, 2) == '-=' && substr($line, -2, 2) == '=-') {
				// Close open paragraph and lists, but not div's
				$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 1, 0);
				//
				$align_len = strlen($line) - strlen(ltrim($line));

				// My textarea size is about 120 space chars.
				//define('TEXTAREA_SZ', 120);

				// NOTE: That strict math formula (split into 3 areas) gives
				//	   bad visual effects...
				// $align = ($align_len < (TEXTAREA_SZ / 3)) ? "left"
				//		: (($align_len > (2 * TEXTAREA_SZ / 3)) ? "right" : "center");
				//
				// Going to introduce some heuristic here :)
				// Visualy (remember that space char is thin) center starts at 25 pos
				// and 'right' from 60 (HALF of full width!) -- thats all :)
				//
				// NOTE: Guess align only if more than 10 spaces before -=title=-
				if ($align_len > 10) {
					$align = ($align_len < 25) ? "left" : (($align_len > 60) ? "right" : "center");

					$align = ' style="text-align: ' . $align . ';"';
				} else {
					$align = '';
				}

				//
				$line = trim($line);
				$line = '<div class="titlebar"' . $align . '>' . substr($line, 2, strlen($line) - 4). '</div>';
				$data .= $line . "\n";
				// TODO: Case is handled ...  no need to check other conditions
				//	   (it is apriori known that they are all false, moreover sometimes
				//	   check procedure need > O(0) of compexity)
				//	   -- continue to next line...
				//	   MUST replace all remaining parse blocks to the same logic...
				continue;
			}

			// Replace old styled titlebars
			if (strlen($line) != strlen($line = preg_replace("/-=(.+?)=-/", "<div class='titlebar'>$1</div>", $line))) {
				// Close open paragraph, but not lists (why not?) or div's
				$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 0, 0);
				$data .= $line . "\n";

				continue;
			}

			// check if we are inside a ~hc~ block and, if so, ignore
			// monospaced and do not insert <br />
			$lineInLowerCase = TikiLib::strtolower($this->unprotectSpecialChars($line, true));

			$inComment += substr_count($lineInLowerCase, "<!--");
			$inComment -= substr_count($lineInLowerCase, "-->");

			// check if we are inside a ~pre~ block and, if so, ignore
			// monospaced and do not insert <br />
			$inPre += substr_count($lineInLowerCase, "<pre");
			$inPre -= substr_count($lineInLowerCase, "</pre");

			// check if we are inside a table, if so, ignore monospaced and do
			// not insert <br />

			$inTable += substr_count($lineInLowerCase, "<table");
			$inTable -= substr_count($lineInLowerCase, "</table");

			// check if we are inside an ul TOC list, if so, ignore monospaced and do
			// not insert <br />
			$inTOC += substr_count($lineInLowerCase, "<ul class=\"toc");
			$inTOC -= substr_count($lineInLowerCase, "</ul><!--toc-->");

			// check if we are inside a script not insert <br />
			$inScript += substr_count($lineInLowerCase, "<script ");
			$inScript -= substr_count($lineInLowerCase, "</script>");

			// If the first character is ' ' and we are not in pre then we are in pre
			if (substr($line, 0, 1) == ' ' && $prefs['feature_wiki_monosp'] == 'y' && $inTable == 0 && $inPre == 0 && $inComment == 0 && !$options['is_html']) {
				// Close open paragraph and lists, but not div's
				$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 1, 0);

				// If the first character is space then make font monospaced.
				// For fixed formatting, use ~pp~...~/pp~
				$line = '<tt>' . $line . '</tt>';
			}

			$line = $this->parse_data_inline_syntax($line, $words, $options['ck_editor']);

			// This line is parseable then we have to see what we have
			if (substr($line, 0, 3) == '---') {
				// This is not a list item --- close open paragraph and lists, but not div's
				$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 1, 0);
				$line = '<hr />';
			} else {
				$litype = substr($line, 0, 1);
				if (($litype == '*' || $litype == '#') && !(strlen($line)-count($listbeg)>4 && preg_match('/^\*+$/', $line))) {
					// Close open paragraph, but not lists or div's
					$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 0, 0);
					$listlevel = $tikilib->how_many_at_start($line, $litype);
					$liclose = '</li>';
					$addremove = 0;
					if ($listlevel < count($listbeg)) {
						while ($listlevel != count($listbeg)) $data .= array_shift($listbeg);
						if (substr(current($listbeg), 0, 5) != '</li>') $liclose = '';
					} elseif ($listlevel > count($listbeg)) {
						$listyle = '';
						while ($listlevel != count($listbeg)) {
							array_unshift($listbeg, ($litype == '*' ? '</ul>' : '</ol>'));
							if ($listlevel == count($listbeg)) {
								$listate = substr($line, $listlevel, 1);
								if (($listate == '+' || $listate == '-') && !($litype == '*' && !strstr(current($listbeg), '</ul>') || $litype == '#' && !strstr(current($listbeg), '</ol>'))) {
									$thisid = 'id' . microtime() * 1000000;
									if ( !$options['ck_editor'] ) {
										$data .= '<br /><a id="flipper' . $thisid . '" class="link" href="javascript:flipWithSign(\'' . $thisid . '\')">[' . ($listate == '-' ? '+' : '-') . ']</a>';
									}
									$listyle = ' id="' . $thisid . '" style="display:' . ($listate == '+' || $options['ck_editor'] ? 'block' : 'none') . ';"';
									$addremove = 1;
								}
							}
							$data.=($litype=='*'?"<ul$listyle>":"<ol$listyle>");
						}
						$liclose='';
					}
					if ($litype == '*' && !strstr(current($listbeg), '</ul>') || $litype == '#' && !strstr(current($listbeg), '</ol>')) {
						$data .= array_shift($listbeg);
						$listyle = '';
						$listate = substr($line, $listlevel, 1);
						if (($listate == '+' || $listate == '-')) {
							$thisid = 'id' . microtime() * 1000000;
							if ( !$options['ck_editor'] ) {
								$data .= '<br /><a id="flipper' . $thisid . '" class="link" href="javascript:flipWithSign(\'' . $thisid . '\')">[' . ($listate == '-' ? '+' : '-') . ']</a>';
							}
							$listyle = ' id="' . $thisid . '" style="display:' . ($listate == '+' || $options['ck_editor'] ? 'block' : 'none') . ';"';
							$addremove = 1;
						}
						$data .= ($litype == '*' ? "<ul$listyle>" : "<ol$listyle>");
						$liclose = '';
						array_unshift($listbeg, ($litype == '*' ? '</li></ul>' : '</li></ol>'));
					}
					$line = $liclose . '<li>' . substr($line, $listlevel + $addremove);
					if (substr(current($listbeg), 0, 5) != '</li>') array_unshift($listbeg, '</li>' . array_shift($listbeg));
				} elseif ($litype == '+') {
					// Close open paragraph, but not list or div's
					$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 0, 0);
					$listlevel = $tikilib->how_many_at_start($line, $litype);
					// Close lists down to requested level
					while ($listlevel < count($listbeg)) $data .= array_shift($listbeg);

					// Must append paragraph for list item of given depth...
					$listlevel = $tikilib->how_many_at_start($line, $litype);
					if (count($listbeg)) {
						if (substr(current($listbeg), 0, 5) != '</li>') {
							array_unshift($listbeg, '</li>' . array_shift($listbeg));
							$liclose = '<li>';
						} else $liclose = '<br />';
					} else $liclose = '';
					$line = $liclose . substr($line, count($listbeg));
				} else {
					// This is not a list item - close open lists,
					// but not paragraph or div's. If we are
					// closing a list, there really shouldn't be a
					// paragraph open anyway.
					$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 0, 1, 0);
					// Get count of (possible) header signs at start
					$hdrlevel = $tikilib->how_many_at_start($line, '!');
					// If 1st char on line is '!' and its count less than 6 (max in HTML)
					if ($litype == '!' && $hdrlevel > 0 && $hdrlevel <= 6) {

						/*
						 * Handle headings autonumbering syntax (i.e. !#Text, !!#Text, ...)
						 * Note :
						 *    this needs to be done even if the current header has no '#'
						 *    in order to generate the right numbers when they are not specified for every headers.
						 *    This is the case, for example, when you want to add numbers to headers of level 2 but not to level 1
						 */

						$line_lenght = strlen($line);

						// Generate an array containing the squeleton of maketoc (based on headers levels)
						//   i.e. hdr_structure will contain something lile this :
						//     array( 1, 2, 2.1, 2.1.1, 2.1.2, 2.2, ... , X.Y.Z... )
						//

						$hdr_structure[$nb_hdrs] = '';

						// Generate the number (e.g. 1.2.1.1) of the current title, based on the previous title number :
						//   - if the current title deepest level is lesser than (or equal to)
						//     the deepest level of the previous title : then we increment the last level number,
						//   - else : we simply add new levels with value '1' (only if the previous level number was shown),
						//
						if ( $nb_last_hdr > 0 && $hdrlevel <= $nb_last_hdr ) {
							$hdr_structure[$nb_hdrs] = array_slice($last_hdr, 0, $hdrlevel);
							if ( !empty($show_title_level[$hdrlevel]) || ! $need_autonumbering ) {
								//
								// Increment the level number only if :
								//     - the last title of the same level number has a displayed number
								//  or - no title has a displayed number (no autonumbering)
								//
								$hdr_structure[$nb_hdrs][$hdrlevel - 1]++;
							}
						} else {
							if ( $nb_last_hdr > 0 ) {
								$hdr_structure[$nb_hdrs] = $last_hdr;
							}
							for ( $h = 0 ; $h < $hdrlevel - $nb_last_hdr ; $h++ ) {
								$hdr_structure[$nb_hdrs][$h + $nb_last_hdr] = '1';
							}
						}
						$show_title_level[$hdrlevel] = preg_match('/^!+[\+\-]?#/', $line);

						// Update last_hdr info for the next header
						$last_hdr = $hdr_structure[$nb_hdrs];
						$nb_last_hdr = count($last_hdr);

						$current_title_real_num = implode('.', $hdr_structure[$nb_hdrs]).'. ';

						// Update the current title number to hide all parents levels numbers if the parent has no autonumbering
						$hideall = false;
						for ( $j = $hdrlevel ; $j > 0 ; $j-- ) {
							if ( $hideall || ! $show_title_level[$j] ) {
								unset($hdr_structure[$nb_hdrs][$j - 1]);
								$hideall = true;
							}
						}

						// Store the title number to use only if it has to be shown (if the '#' char is used)
						$current_title_num = $show_title_level[$hdrlevel] ? implode('.', $hdr_structure[$nb_hdrs]).'. ' : '';

						$nb_hdrs++;


						// Close open paragraph (lists already closed above)
						$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 0, 0);
						// Close lower level divs if opened
						for (;current($divdepth) >= $hdrlevel; array_shift($divdepth)) $data .= '</div>';

						// Remove possible hotwords replaced :)
						//   Umm, *why*?  Taking this out lets page
						//   links in headers work, which can be nice.
						//   -rlpowell
						// $line = strip_tags($line);

						// OK. Parse headers here...
						$anchor = '';
						$aclose = '';
						$aclose2 = '';
						$addremove = $show_title_level[$hdrlevel] ? 1 : 0; // If needed, also remove '#' sign from title beginning

						// May be special signs present after '!'s?
						$divstate = substr($line, $hdrlevel, 1);
						if (($divstate == '+' || $divstate == '-') && !$options['ck_editor']) {
							// OK. Must insert flipper after HEADER, and then open new div...
							$thisid = 'id' . preg_replace('/[^a-zA-z0-9]/', '', urlencode($options['page'])) .$nb_hdrs;
							$aclose = '<a id="flipper' . $thisid . '" class="link" href="javascript:flipWithSign(\'' . $thisid . '\')">[' . ($divstate == '-' ? '+' : '-') . ']</a>';
							$aclose2 = '<div id="' . $thisid . '" class="showhide_heading" style="display:' . ($divstate == '+' ? 'block' : 'none') . ';">';
							$headerlib = TikiLib::lib('header');
							$headerlib->add_jq_onready("setheadingstate('$thisid');");
							array_unshift($divdepth, $hdrlevel);
							$addremove += 1;
						}

						// Generate the final title text
						$title_text_base = substr($line, $hdrlevel + $addremove);
						$title_text = $current_title_num.$title_text_base;

						// create stable anchors for all headers
						// use header but replace non-word character sequences
						// with one underscore (for XHTML 1.0 compliance)
						// Workaround pb with plugin replacement and header id
						//  first we remove hash from title_text for headings beginning
						//  with images and HTML tags
						$thisid = preg_replace('/§[a-z0-9]{32}§/', '', $title_text);
						$thisid = preg_replace('#</?[^>]+>#', '', $thisid);
						$thisid = preg_replace('/[^a-zA-Z0-9\:\.\-\_]+/', '_', $thisid);
						$thisid = preg_replace('/^[^a-zA-Z]*/', '', $thisid);
						if (empty($thisid)) $thisid = 'a'.md5($title_text);

						// Add a number to the anchor if it already exists, to avoid duplicated anchors
						if ( isset($all_anchors[$thisid]) ) {
							$all_anchors[$thisid]++;
							$thisid .= '_'.$all_anchors[$thisid];
						} else {
							$all_anchors[$thisid] = 1;
						}

						// Collect TOC entry if any {maketoc} is present on the page
						//if ( $need_maketoc !== false ) {
						$anch[] =  array(
										'id' => $thisid,
										'hdrlevel' => $hdrlevel,
										'pagenum' => $pageNum,
										'title' => $title_text_base,
										'title_displayed_num' => $current_title_num,
										'title_real_num' => $current_title_real_num
										);
						//}
						global $tiki_p_edit, $section;
						if ($prefs['wiki_edit_section'] === 'y' && $section === 'wiki page' && $tiki_p_edit === 'y' &&
								( $prefs['wiki_edit_section_level'] == 0 || $hdrlevel <= $prefs['wiki_edit_section_level']) &&
								(empty($options['print']) || !$options['print']) && !$options['suppress_icons'] ) {

							$smarty = TikiLib::lib('smarty');
							include_once('lib/smarty_tiki/function.icon.php');

							if ($prefs['wiki_edit_icons_toggle'] == 'y' && !isset($_COOKIE['wiki_plugin_edit_view'])) {
								$iconDisplayStyle = ' style="display:none;"';
							} else {
								$iconDisplayStyle = '';
							}
							$button = '<div class="icon_edit_section"' . $iconDisplayStyle . '><a href="tiki-editpage.php?';
							if (!empty($options['page'])) {
								$button .= 'page='.urlencode($options['page']).'&amp;';
							}
							$button .= 'hdr='.$nb_hdrs.'">'.smarty_function_icon(array('_id'=>'page_edit_section', 'alt'=>tra('Edit Section')), $smarty).'</a></div>';
						} else {
							$button = '';
						}

						// replace <div> with <h> style attribute
						$do_center = 0;
						$title_text = preg_replace('/<div style="text-align: center;">(.*)<\/div>/', '\1', $title_text, 1, $do_center);
						$style = $do_center ? ' style="text-align: center;"' : '';

						if ( $prefs['feature_wiki_show_hide_before'] == 'y' ) {
							$line = $button.'<h'.($hdrlevel).$style.' class="showhide_heading" id="'.$thisid.'">'.$aclose.' '.$title_text.'</h'.($hdrlevel).'>'.$aclose2;
						} else {
							$line = $button.'<h'.($hdrlevel).$style.' class="showhide_heading" id="'.$thisid.'">'.$title_text.'</h'.($hdrlevel).'>'.$aclose.$aclose2;
						}
					} elseif (!strcmp($line, $prefs['wiki_page_separator'])) {
						// Close open paragraph, lists, and div's
						$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 1, 1);
						// Leave line unchanged... tiki-index.php will split wiki here
						$line = $prefs['wiki_page_separator'];
						$pageNum += 1;
					} else {
						/** Usual paragraph.
						 *
						 * If the
						 * $prefs['feature_wiki_paragraph_formatting']
						 * is on, then consecutive lines of
						 * text will be gathered into a block
						 * that is surrounded by HTML
						 * paragraph tags. One or more blank
						 * lines, or another special Wiki line
						 * (e.g., heading, titlebar, etc.)
						 * signifies the end of the
						 * paragraph. If the paragraph
						 * formatting feature is off, the
						 * original Tikiwiki behavior is used,
						 * in which each line in the source is
						 * terminated by an explicit line
						 * break (br tag).
						 *
						 * @since Version 1.9
						 */
						if ($inTable == 0 && $inPre == 0 && $inComment == 0 && $inTOC == 0 &&  $inScript == 0
								// Don't put newlines at comments' end!
								&& strpos($line, "-->") !== (strlen($line) - 3)
								&& $options['process_wiki_paragraphs']) {
							 	
							$tline = trim(str_replace('&nbsp;', '', $this->unprotectSpecialChars($line, true)));

							if ($prefs['feature_wiki_paragraph_formatting'] == 'y') {
								if (count($lines) > 1 || $options['min_one_paragraph']) {	// don't apply wiki para if only single line so you can have inline includes
									$contains_block = $this->contains_html_block($tline);
									$contains_br = $this->contains_html_br($tline);

									if (!$contains_block) {	// check inside plugins etc for block elements
										preg_match_all('/\xc2\xa7[^\xc2\xa7]+\xc2\xa7/', $tline, $m);	// noparse guid for plugins 
										if (count($m) > 0) {
											$m_count = count($m[0]);
											$nop_ix = false;
											for ($i = 0; $i < $m_count; $i++) {
												//$nop_ix = array_search( $m[0][$i], $noparsed['key'] ); 	// array_search doesn't seem to work here - why? no "keys"?
												foreach ($noparsed['key'] as $k => $s) {
													if ($m[0][$i] == $s) {
														$nop_ix = $k;
														break;
													}
												}
												if ($nop_ix !== false) {
													$nop_str = $noparsed['data'][$nop_ix];
													$contains_block = $this->contains_html_block($nop_str);
													if ($contains_block) {
														break;
													}
												}
											}
										}
									}
									
									if ($in_paragraph && ((empty($tline) && !$in_empty_paragraph) || $contains_block)) {
										// If still in paragraph, on meeting first blank line or end of div or start of div created by plugins; close a paragraph
										$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 0, 0);
									} elseif (!$in_paragraph && !$contains_block && !$contains_br && (!empty($tline) || $prefs['feature_wiki_paragraph_formatting_add_br'] === 'y')) {
										// If not in paragraph, first non-blank line; start a paragraph; if not start of div created by plugins
										$data .= "<p>";
										$in_paragraph = 1;
										$in_empty_paragraph = empty($tline) && $prefs['feature_wiki_paragraph_formatting_add_br'] === 'y';
									} elseif ($in_paragraph && $prefs['feature_wiki_paragraph_formatting_add_br'] == 'y' && !$contains_block) {
										// A normal in-paragraph line if not close of div created by plugins
										if (!empty($tline)) {
											$in_empty_paragraph = false;
										}
										$line = "<br />" . $line;
									} // else {
									  // A normal in-paragraph line or a consecutive blank line.
									  // Leave it as is.
									  // }
								}
							} else {
								$line .= "<br />";
							}
						}
					}
				}
			}
			$data .= $line . "\n";
		}

		if ($options['is_html']) {
			$count = 1;
			while ($count == 1) {
				$data = preg_replace("#<p>([^(</p>)]*)<p>([^(</p>)]*)</p>#uims", "<p>$1$2", $data, 1, $count);
			}
		}

		// Close open paragraph, lists, and div's
		$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 1, 1);

		/*
		 * Replace special "maketoc" plugins
		 *  Valid arguments :
		 *    - type (look of the maketoc),
		 *    - maxdepth (max level displayed),
		 *    - title (replace the default title),
		 *    - showhide (if set to y, add the Show/Hide link)
		 *    - nolinks (if set to y, don't add links on toc entries)
		 *    - nums : 
		 *       * 'n' means 'no title autonumbering' in TOC,
		 *       * 'force' means :
		 *	    ~ same as 'y' if autonumbering is used in the page,
		 *	    ~ 'number each toc entry as if they were all autonumbered'
		 *       * any other value means 'same as page's headings autonumbering',
		 *
		 *  (Note that title will be translated if a translation is available)
		 *
		 *  Examples: {maketoc}, {maketoc type=box maxdepth=1 showhide=y}, {maketoc title="Page Content" maxdepth=3}, ...
		 *  Obsolete syntax: {maketoc:box}
		 */
		$new_data = '';
		$search_start = 0;
		if ( !$options['ck_editor']) {
			while ( ($maketoc_start = strpos($data, "{maketoc", $search_start)) !== false ) {
				$maketoc_length = strpos($data, "}", $maketoc_start) + 1 - $maketoc_start;
				$maketoc_string = substr($data, $maketoc_start, $maketoc_length);

				// Handle old type definition for type "box" (and preserve environment for the title also)
				if ( $maketoc_length > 12 && TikiLib::strtolower(substr($maketoc_string, 8, 4)) == ':box' ) {
					$maketoc_string = "{maketoc type=box showhide=y title='".tra('index', $options['language'], true).'"'.substr($maketoc_string, 12);
				}

				$maketoc_string = str_replace('&quot;', '"', $maketoc_string);
				$maketoc_regs = array();

				if ( $maketoc_length == 9 || preg_match_all("/([^\s=\(]+)=([^\"\s=\)\}]+|\"[^\"]*\")/", $maketoc_string, $maketoc_regs) ) {

					if ( $maketoc_start > 0 ) {
						$new_data .= substr($data, 0, $maketoc_start);
					}

					// Set maketoc default values
					$maketoc_args = array(
							'type' => '',
							'maxdepth' => 0, // No limit
							'title' => tra('Table of contents', $options['language'], true),
							'showhide' => '',
							'nolinks' => '',
							'nums' => '',
							'levels' => ''
							);

					// Build maketoc arguments list (and remove " chars if they are around the value)
					if ( isset($maketoc_regs[1]) ) {
						$nb_args = count($maketoc_regs[1]);
						for ( $a = 0; $a < $nb_args ; $a++ ) {
							$maketoc_args[TikiLib::strtolower($maketoc_regs[1][$a])] = trim($maketoc_regs[2][$a], '"');
						}
					}

					if ( $maketoc_args['title'] != '' ) {
						// Translate maketoc title
						$maketoc_summary = ' summary="'.tra($maketoc_args['title'], $options['language'], true).'"';
						$maketoc_title = "<div id='toctitle'><h3>".tra($maketoc_args['title'], $options['language']).'</h3>';

						if ( isset($maketoc_args['showhide']) && $maketoc_args['showhide'] == 'y' ) {
						  $maketoc_title .= '<a class="link"  href="javascript:toggleToc()">' . '[' . tra('Show/Hide') . ']' . '</a>';
						}
						$maketoc_title .= '</div>';
					} else {
						$maketoc_summary = '';
						$maketoc_title = '';
					}
					if (!empty($maketoc_args['levels'])) {
						$maketoc_args['levels'] = preg_split('/\s*,\s*/', $maketoc_args['levels']);
					}

					// Build maketoc
					switch ( $maketoc_args['type'] ) {
						case 'box': 
							$maketoc_header = '';
							$maketoc = "<table id='toc' class='toc'$maketoc_summary>\n<tr><td>$maketoc_title<ul>";
							$maketoc_footer = "</ul></td></tr></table>\n";
							$link_class = 'toclink';
										break;
						default: 
							$maketoc = '';
							$maketoc_header = "<div id='toc'>".$maketoc_title;
							$maketoc_footer = '</div>';
							$link_class = 'link';
					}
					if ( count($anch) and $need_maketoc !== false) {
						foreach ( $anch as $tocentry ) {
							if ( $maketoc_args['maxdepth'] > 0 && $tocentry['hdrlevel'] > $maketoc_args['maxdepth'] ) {
								continue;
							}
							if (!empty($maketoc_args['levels']) && !in_array($tocentry['hdrlevel'], $maketoc_args['levels'])) {
								continue;
							}
							// Generate the toc entry title (with nums)
							if ( $maketoc_args['nums'] == 'n' ) {
								$tocentry_title = '';
							} elseif ( $maketoc_args['nums'] == 'force' && ! $need_autonumbering ) {
								$tocentry_title = $tocentry['title_real_num'];
							} else {
								$tocentry_title = $tocentry['title_displayed_num'];
							}
							$tocentry_title .= $tocentry['title'];

							// Generate the toc entry link
							$tocentry_link = '#'.$tocentry['id'];
							if ( $tocentry['pagenum'] > 1 ) {
								$tocentry_link = $_SERVER['PHP_SELF'].'?page='.$options['page'].'&pagenum='.$tocentry['pagenum'].$tocentry_link;
							}
							if ( $maketoc_args['nolinks'] != 'y' ) {
								$tocentry_title = "<a href='$tocentry_link' class='link'>".$tocentry_title.'</a>';
							}

							if ( $maketoc != '' ) $maketoc.= "\n";
							$shift = $tocentry['hdrlevel'];
							if (!empty($maketoc_args['levels'])) {
								for ($i = 1; $i <= $tocentry['hdrlevel']; ++$i) {
									if (!in_array($i, $maketoc_args['levels']))
										--$shift;
								}
							}
							switch ( $maketoc_args['type'] ) {
								case 'box':
									$maketoc .= "<li class='toclevel-".$shift."'>".$tocentry_title."</li>";
												break;
								default:
									$maketoc .= str_repeat('*', $shift).$tocentry_title;
							}
						}

						$maketoc = $this->parse_data($maketoc, array('noparseplugins' => true));

						if (preg_match("/^<ul>/", $maketoc)) {
							$maketoc = preg_replace("/^<ul>/", '<ul class="toc">', $maketoc);
							$maketoc .= '<!--toc-->';
						}

						if ( $link_class != 'link' ) {
							$maketoc = preg_replace("/'link'/", "'$link_class'", $maketoc);
						}
					}
					if (!empty($maketoc)) {
						$maketoc = $maketoc_header.$maketoc.$maketoc_footer;
					}
					$new_data .= $maketoc;
					$data = substr($data, $maketoc_start + $maketoc_length);
					$search_start = 0; // Reinitialize search start cursor, since data now begins after the last replaced maketoc
				} else {
					$search_start = $maketoc_start + $maketoc_length;
				}
			}
		}
		$data = $new_data.$data;
		// Add icon to edit the text before the first section (if there is some)
		if ($prefs['wiki_edit_section'] === 'y' && isset($section) && $section === 'wiki page' && $tiki_p_edit === 'y' && (empty($options['print']) ||
				!$options['print'])  && strpos($data, '<div class="icon_edit_section">') != 0 && !$options['suppress_icons']) {
					
			$smarty = TikiLib::lib('smarty');
			include_once('lib/smarty_tiki/function.icon.php');
			$button = '<div class="icon_edit_section"><a href="tiki-editpage.php?';
			if (!empty($options['page'])) {
				$button .= 'page='.urlencode($options['page']).'&amp;';
			}
			$button .= 'hdr=0">'.smarty_function_icon(array('_id'=>'page_edit_section', 'alt'=>tra('Edit Section')), $smarty).'</a></div>';
			$data = $button.$data;
		}
	}
	
	//*
	function contains_html_block($inHtml)
	{
		// detect all block elements as defined on http://www.w3.org/2007/07/xhtml-basic-ref.html
		$block_detect_regexp = '/<[\/]?(?:address|blockquote|div|dl|fieldset|h\d|hr|li|noscript|ol|p|pre|table|ul)/i';
		return  (preg_match($block_detect_regexp, $this->unprotectSpecialChars($inHtml, true)) > 0);
	}

	//*
	function contains_html_br($inHtml)
	{
		$block_detect_regexp = '/<(?:br)/i';
		return  (preg_match($block_detect_regexp, $this->unprotectSpecialChars($inHtml, true)) > 0);
	}

	//*
	function get_wiki_link_replacement( $pageLink, $extra = array(), $ck_editor = false )
	{
		global $tikilib, $prefs;
		$wikilib = TikiLib::lib('wiki');

		// Fetch all externals once
		static $externals = false;
		if ( false === $externals ) {
			$externals = $tikilib->fetchMap('SELECT LOWER(`name`), `extwiki` FROM `tiki_extwiki`');
		}
		
		$displayLink = $pageLink;

		// HTML entities encoding breaks page lookup
		$pageLink = html_entity_decode($pageLink, ENT_COMPAT, 'UTF-8');

		$description = null;
		$reltype = null;
		$processPlural = false;
		$anchor = null;
		
		if ( array_key_exists('description', $extra) )
			$description = $extra['description'];
		if ( array_key_exists('reltype', $extra) )
			$reltype = $extra['reltype'];
		if ( array_key_exists('plural', $extra) )
			$processPlural = (boolean) $extra['plural'];
		if ( array_key_exists('anchor', $extra) )
			$anchor = $extra['anchor'];

		$link = new WikiParser_OutputLink;
		$link->setIdentifier($pageLink);
		$link->setQualifier($reltype);
		$link->setDescription($description);
		$link->setWikiLookup(array( $this, 'parser_helper_wiki_info_getter' ));
		$link->setWikiLinkBuilder(array( $this, 'parser_helper_wiki_link_builder' ));
		$link->setExternals($externals);
		$link->setHandlePlurals($processPlural);
		$link->setAnchor($anchor);

		if ( $prefs['feature_multilingual'] == 'y' && isset( $GLOBALS['pageLang'] ) ) {
			$link->setLanguage($GLOBALS['pageLang']);
		}

		return $link->getHtml($ck_editor);
	}

	//*
	function parser_helper_wiki_link_builder( $pageLink )
	{
		$wikilib = TikiLib::lib('wiki');
		return $wikilib->sefurl($pageLink);
	}

	//*
	function parser_helper_wiki_info_getter( $pageName )
	{
		global $prefs;
		$tikilib = TikiLib::lib('tiki');
		$page_info = $tikilib->get_page_info($pageName, false);
		
		if ( $page_info !== false ) {
			return $page_info;
		}

		// If page does not exist directly, attempt to find an alias
		if ( $prefs['feature_wiki_pagealias'] == 'y' ) {
			$semanticlib = TikiLib::lib('semantic');

			$toPage = $pageName;
			$tokens = explode(',', $prefs['wiki_pagealias_tokens']); 
			
			$prefixes = explode(',', $prefs["wiki_prefixalias_tokens"]);
			foreach ($prefixes as $p) {
				$p = trim($p);
				if (strlen($p) > 0 && TikiLib::strtolower(substr($pageName, 0, strlen($p))) == TikiLib::strtolower($p)) {
					$toPage = $p;
					$tokens = 'prefixalias';
				}
			}
			 
			$links = $semanticlib->getLinksUsing(
							$tokens,
							array( 'toPage' => $toPage )
			);

			if ( count($links) > 1 ) {
				// There are multiple aliases for this page. Need to disambiguate.
				//
				// When feature_likePages is set, trying to display the alias itself will
				// display an error page with the list of aliased pages in the "like pages" section.
				// This allows the user to pick the appropriate alias.
				// So, leave the $pageName to the alias.
				// 
				// If feature_likePages is not set, then the user will only see that the page does not
				// exist. So it's better to just pick the first one.
				//													
				if ($prefs['feature_likePages'] == 'y' || $tokens == 'prefixalias') {
					// Even if there is more then one match, if prefix is being redirected then better
					// to fail than to show possibly wrong page
					return true;
				} else {
					// If feature_likePages is NOT set, then trying to display the first one is fine
					// $pageName is by ref so it does get replaced 
					$pageName = $links[0]['fromPage'];
					return $tikilib->get_page_info($pageName);
				}
			} elseif (count($links)) {
				// there is exactly one match
				if ($prefs['feature_wiki_1like_redirection'] == 'y') {
					return true;
				} else {
					$pageName = $links[0]['fromPage'];
					return $tikilib->get_page_info($pageName);
				} 
			}
		}
	}

	//*
	function parse_smileys($data)
	{
		global $prefs;
		static $patterns;

		if ($prefs['feature_smileys'] == 'y') {
			if (! $patterns) {
				$patterns = array(
					// Example of all Tiki Smileys (the old syntax)
					// (:biggrin:) (:confused:) (:cool:) (:cry:) (:eek:) (:evil:) (:exclaim:) (:frown:)
					// (:idea:) (:lol:) (:mad:) (:mrgreen:) (:neutral:) (:question:) (:razz:) (:redface:)
					// (:rolleyes:) (:sad:) (:smile:) (:surprised:) (:twisted:) (:wink:) (:arrow:) (:santa:)
					
					"/\(:([^:]+):\)/" => "<img alt=\"$1\" src=\"img/smiles/icon_$1.gif\" />",

					// :) :-)
					'/(\s|^):-?\)/' => "$1<img alt=\":-)\" title=\"".tra('smiling')."\" src=\"img/smiles/icon_smile.gif\" />",
					// :( :-(
					'/(\s|^):-?\(/' => "$1<img alt=\":-(\" title=\"".tra('sad')."\" src=\"img/smiles/icon_sad.gif\" />",
					// :D :-D
					'/(\s|^):-?D/' => "$1<img alt=\":-D\" title=\"".tra('grinning')."\" src=\"img/smiles/icon_biggrin.gif\" />",
					// :S :-S :s :-s
					'/(\s|^):-?S/i' => "$1<img alt=\":-S\" title=\"".tra('confused')."\" src=\"img/smiles/icon_confused.gif\" />",
					// B) B-) 8-)
					'/(\s|^)(B-?|8-)\)/' => "$1<img alt=\"B-)\" title=\"".tra('cool')."\" src=\"img/smiles/icon_cool.gif\" />",
					// :'( :_(
					'/(\s|^):[\'|_]\(/' => "$1<img alt=\":_(\" title=\"".tra('crying')."\" src=\"img/smiles/icon_cry.gif\" />",
					// 8-o 8-O =-o =-O
					'/(\s|^)[8=]-O/i' => "$1<img alt=\"8-O\" title=\"".tra('frightened')."\" src=\"img/smiles/icon_eek.gif\" />",
					// }:( }:-(
					'/(\s|^)\}:-?\(/' => "$1<img alt=\"}:(\" title=\"".tra('evil stuff')."\" src=\"img/smiles/icon_evil.gif\" />",
					// !-) !)
					'/(\s|^)\!-?\)/' => "$1<img alt=\"(!)\" title=\"".tra('exclamation mark !')."\" src=\"img/smiles/icon_exclaim.gif\" />",
					// >:( >:-(
					'/(\s|^)\>:-?\(/' => "$1<img alt=\"}:(\" title=\"".tra('frowning')."\" src=\"img/smiles/icon_frown.gif\" />",
					// i-)
					'/(\s|^)i-\)/' => "$1<img alt=\"(".tra('light bulb').")\" title=\"".tra('idea !')."\" src=\"img/smiles/icon_idea.gif\" />",
					// LOL
					'/(\s|^)LOL(\s|$)/' => "$1<img alt=\"(".tra('LOL').")\" title=\"".tra('laughing out loud !')."\" src=\"img/smiles/icon_lol.gif\" />$2",
					// >X( >X[ >:[ >X-( >X-[ >:-[
					'/(\s|^)\>[:X]-?\(/' => "$1<img alt=\">:[\" title=\"".tra('mad')."\" src=\"img/smiles/icon_mad.gif\" />",
					// =D =-D
					'/(\s|^)[=]-?D/' => "$1<img alt=\"=D\" title=\"".tra('Mr. Green laughing')."\" src=\"img/smiles/icon_mrgreen.gif\" />",
				);
			}

			foreach ($patterns as $p => $r) {
				$data = preg_replace($p, $r, $data);
			}
		}
		return $data;
	}

	//*
	function get_pages($data,$withReltype = false)
	{
		global $page_regex, $prefs, $tikilib;

		$matches = WikiParser_PluginMatcher::match($data);
		foreach ( $matches as $match ) {
			if ( $match->getName() == 'code' ) {
				$match->replaceWith('');
			}
		}

		$data = $matches->getText();

		preg_match_all("/\(([a-z0-9-]+)?\( *($page_regex) *\)\)/", $data, $normal);
		preg_match_all("/\(([a-z0-9-]+)?\( *($page_regex) *\|(.+?)\)\)/", $data, $withDesc);
		preg_match_all('/<a class="wiki" href="tiki-index\.php\?page=([^\?&"]+)[^"]*"/', $data, $htmlLinks);
		preg_match_all('/<a class="wiki wikinew" href="tiki-editpage\.php\?page=([^\?&"]+)"/', $data, $htmlWantedLinks);
		// TODO: revise the need to call modified urldecode() (shouldn't be needed after r37568). 20110922
		foreach ($htmlLinks[1] as &$h) {
			$h = $tikilib->urldecode($h);
		}
		foreach ($htmlWantedLinks[1] as &$h) {
			$h = $tikilib->urldecode($h);
		}

		if ($prefs['feature_wikiwords'] == 'y') {
			preg_match_all("/([ \n\t\r\,\;]|^)?([A-Z][a-z0-9_\-]+[A-Z][a-z0-9_\-]+[A-Za-z0-9\-_]*)($|[ \n\t\r\,\;\.])/", $data, $wikiLinks);

			$pageList = array_merge($normal[2], $withDesc[2], $wikiLinks[2], $htmlLinks[1], $htmlWantedLinks[1]);
			if ( $withReltype ) {
				$relList = array_merge(
								$normal[1], 
								$withDesc[1], 
								count($wikiLinks[2]) ? array_fill(0, count($wikiLinks[2]), null) : array(),
								count($htmlLinks[1]) ? array_fill(0, count($htmlLinks[1]), null) : array(),
								count($htmlWantedLinks[1]) ? array_fill(0, count($htmlWantedLinks[1]), null) : array()
				);
			}
		} else {
			$pageList = array_merge($normal[2], $withDesc[2], $htmlLinks[1], $htmlWantedLinks[1]);
			if ( $withReltype ) {
				$relList = array_merge(
								$normal[1], 
								$withDesc[1],
								count($htmlLinks[1]) ? array_fill(0, count($htmlLinks[1]), null) : array(),
								count($htmlWantedLinks[1]) ? array_fill(0, count($htmlWantedLinks[1]), null) : array()
				);
			}
		}
	
		if ( $withReltype ) {
			$complete = array();
			foreach ( $pageList as $idx => $name ) {
				if ( ! array_key_exists($name, $complete) )
					$complete[$name] = array();
				if ( ! empty( $relList[$idx] ) && ! in_array($relList[$idx], $complete[$name]) )
					$complete[$name][] = $relList[$idx];
			}

			return $complete;
		} else {
			return array_unique($pageList);
		}
	}

	function plugin_find_implementation( & $implementation, & $data, & $args )
	{
		if ( $info = $this->plugin_alias_info($implementation) ) {
			$implementation = $info['implementation'];

			// Do the body conversion
			if ( isset($info['body']) ) {
				if ( ( isset($info['body']['input']) && $info['body']['input'] == 'ignore' )
					|| empty( $data ) )
					$data = isset($info['body']['default']) ? $info['body']['default'] : '';

				if ( isset($info['body']['params']) )
					$data = $this->plugin_replace_args($data, $info['body']['params'], $args);
			} else {
				$data = '';
			}

			// Do parameter conversion
			$params = array();
			if ( isset($info['params']) ) {
				foreach ( $info['params'] as $key => $value ) {
					if ( is_array($value) && isset($value['pattern']) && isset($value['params']) ) {
						$params[$key] = $this->plugin_replace_args($value['pattern'], $value['params'], $args);
					} else {
						// Handle simple values
						if ( isset($args[$key]) )
							$params[$key] = $args[$key];
						else
							$params[$key] = $value;
					}
				}
			}

			$args = $params;

			// Attempt to find recursively
			$this->plugin_find_implementation($implementation, $data, $args);

			return true;
		}

		return false;
	}

	private function get_hotwords()
	{
		global $tikilib;
		static $cache_hotwords;
		if ( isset($cache_hotwords) ) {
			return $cache_hotwords;
		}
		$query = "select * from `tiki_hotwords`";
		$result = $tikilib->fetchAll($query, array(), -1, -1, false);
		$ret = array();
		foreach ($result as $res ) {
			$ret[$res["word"]] = $res["url"];
		}
		$cache_hotwords = $ret;
		return $ret;
	}
}

