<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: smarty.php 41017 2012-04-21 15:22:34Z robertplummer $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== FALSE) {
  header('location: index.php');
  exit;
}

require_once 'lib/setup/third_party.php';
require_once (defined('SMARTY_DIR') ? SMARTY_DIR : 'lib/smarty/libs/') . 'Smarty.class.php';

class Tiki_Security_Policy extends Smarty_Security
{
		public $php_modifiers = array( 'nl2br','escape', 'count', 'addslashes', 'ucfirst', 'ucwords', 'urlencode', 'md5', 'implode', 'explode', 'is_array', 'htmlentities', 'var_dump', 'strip_tags', 'json_encode', 'stristr' );
		public $php_functions = array('isset', 'empty', 'count', 'sizeof', 'in_array', 'is_array', 'time', 'nl2br', 'tra', 'strlen', 'strstr', 'strtolower', 'basename', 'ereg', 'array_key_exists', 'preg_match', 'json_encode', 'stristr', 'is_numeric', 'array', 'is_numeric' );
		public $secure_dir = array(
			'',
			'img/',
			'img/icons',
			'styles/strasa/img/icons/',
			'styles/strasa/pics/icons/',
			'styles/coelesce/pics/icons/',
			'styles/darkroom/pics/icons/',
			'styles/thenews/pics/icons/',
			'styles/tikinewt/pics/icons/',
			'styles/twist/pics/icons/',
			'img/flags',
			'img/mytiki',
			'img/smiles',
			'img/trackers',
			'images/',
			'img/icons/mime',
			'img/icons/large',
			'lib/ckeditor_tiki/ckeditor-icons',
		);
}

class Smarty_Tiki extends Smarty
{
	var $url_overriding_prefix_stack = null;
	var $url_overriding_prefix = null;
	var $main_template_dir = null;

	function Smarty_Tiki($tikidomain = '')
	{
		parent::__construct();
		global $prefs, $style_base;

		if (empty($style_base) && class_exists('TikiLib')) {	// TikiLib doesn't exist in the installer
			$tikilib = TikiLib::lib('tiki');
			if (method_exists($tikilib, "get_style_base")) {
				$style_base = TikiLib::lib('tiki')->get_style_base($prefs['style']);
			}
		}
		if ($tikidomain) {
			$tikidomain.= '/';
		}
		$this->main_template_dir = realpath('templates/');
		$this->setTemplateDir(null);
		if ( !empty($tikidomain) && $tikidomain !== '/' ) {
			$this->addTemplateDir($this->main_template_dir.'/'.$tikidomain.'/styles/'.$style_base.'/');
			$this->addTemplatedir($this->main_template_dir.'/'.$tikidomain.'/'); 
		}
		$this->addTemplateDir($this->main_template_dir.'/styles/'.$style_base.'/'); 
		$this->addTemplateDir($this->main_template_dir); 
		
		$this->setCompileDir(realpath("templates_c/$tikidomain"));
		$this->setConfigDir(null);
		$this->compile_check = ( $prefs['smarty_compilation'] != 'never' );
		$this->force_compile = ( $prefs['smarty_compilation'] == 'always' );
		$this->assign('app_name', 'Tiki');
		$this->setPluginsDir(
						array(	// the directory order must be like this to overload a plugin
							TIKI_SMARTY_DIR,
							SMARTY_DIR.'plugins'
						)
		);

		if ( $prefs['smarty_security'] == 'y' ) {
			$this->enableSecurity('Tiki_Security_Policy');
		} else {
			$this->disableSecurity();
		}
		$this->use_sub_dirs = false;
		$this->url_overriding_prefix_stack = array();
		if (!empty($prefs['smarty_notice_reporting']) and $prefs['smarty_notice_reporting'] === 'y' ) {
			$this->error_reporting = E_ALL;
		} else {
			$this->error_reporting = E_ALL ^ E_NOTICE;
		}
	}

	// Fetch templates from plugins (smarty plugins, wiki plugins, modules, ...) that may need to :
	//   - temporarily override some smarty vars,
	//   - prefix their self_link / button / query URL arguments
	//
	function plugin_fetch($_smarty_tpl_file, &$override_vars = null)
	{
		$smarty_orig_values = array();
		if ( is_array($override_vars) ) {
			foreach ( $override_vars as $k => $v ) {
				$smarty_orig_values[ $k ] = $this->getTemplateVars($k);
				$this->assignByRef($k, $override_vars[ $k ]);
			}
		}

		$return = $this->fetch($_smarty_tpl_file);

		// Restore original values of smarty variables
		if ( count($smarty_orig_values) > 0 ) {
			foreach ( $smarty_orig_values as $k => $v ) {
				$this->assignByRef($k, $smarty_orig_values[ $k ]);
			}
		}

		unset($smarty_orig_values);
		return $return;
	}

	public function fetch($_smarty_tpl_file = null, $_smarty_cache_id = null, $_smarty_compile_id = null, $parent = null, $_smarty_display = false, $merge_tpl_vars = true, $no_output_filter = false)
	{
		global $prefs, $style_base, $tikidomain;

    if ( empty($_smarty_cache_id) )
		    $_smarty_cache_id = $prefs['language'] . $_smarty_cache_id . md5($_smarty_tpl_file);
    if ( empty($_smarty_compile_id) )
    		$_smarty_compile_id = $prefs['language'] . $_smarty_compile_id . md5($_smarty_tpl_file);

		if ( ($tpl = $this->getTemplateVars('mid')) && ( $_smarty_tpl_file == 'tiki.tpl' || $_smarty_tpl_file == 'tiki-print.tpl' || $_smarty_tpl_file == 'tiki_full.tpl' ) ) {

			// Set the last mid template to be used by AJAX to simulate a 'BACK' action
			if ( isset($_SESSION['last_mid_template']) ) {
				$this->assign('last_mid_template', $_SESSION['last_mid_template']);
				$this->assign('last_mid_php', $_SESSION['last_mid_php']);
			}
			$_SESSION['last_mid_template'] = $tpl;
			$_SESSION['last_mid_php'] = $_SERVER['REQUEST_URI'];

			// set the first part of the browser title for admin pages
			if ( null === $this->getTemplateVars('headtitle') ) {
				$script_name = basename($_SERVER['SCRIPT_NAME']);
				if ($script_name != 'tiki-admin.php' && strpos($script_name, 'tiki-admin') === 0) {
					$str = substr($script_name, 10, strpos($script_name, '.php') - 10);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', 'Admin ' . $str);
				} else if (strpos($script_name, 'tiki-list') === 0) {
					$str = substr($script_name, 9, strpos($script_name, '.php') - 9);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', 'List ' . $str);
				} else if (strpos($script_name, 'tiki-view') === 0) {
					$str = substr($script_name, 9, strpos($script_name, '.php') - 9);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', 'View ' . $str);
				} else { // still not set? guess...
					$str = str_replace(array('tiki-', '.php', '_'), array('', '', ' '), $script_name);
					$str = ucwords($str);
					$this->assign('headtitle', tra($str));	// for files where no title has been set or can be reliably calculated - translators: please add comments here as you find them
				}
			}

			if ( $_smarty_tpl_file == 'tiki-print.tpl' ) {
				$this->assign('print_page', 'y');
			}
			$data = $this->fetch($tpl, $_smarty_cache_id, $_smarty_compile_id, $parent);//must get the mid because the modules can overwrite smarty variables

			$this->assign('mid_data', $data);

			include_once('tiki-modules.php');

		} elseif ($_smarty_tpl_file == 'confirm.tpl' || $_smarty_tpl_file == 'error.tpl' || $_smarty_tpl_file == 'error_ticket.tpl' || $_smarty_tpl_file == 'error_simple.tpl') {
			ob_end_clean(); // Empty existing Output Buffer that may have been created in smarty before the call of this confirm / error* template
			if ( $prefs['feature_obzip'] == 'y' ) {
				ob_start('ob_gzhandler');
			}

			include_once('tiki-modules.php');

		}
		if (isset($style_base)) {
			if ($tikidomain and file_exists("templates/$tikidomain/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/styles/$style_base/$_smarty_tpl_file";
			} elseif ($tikidomain and file_exists("templates/$tikidomain/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/$_smarty_tpl_file";
			} elseif (file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";
			}
		}

		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $parent, $_smarty_display);
	}

	function clear_assign($var)
	{
		return parent::clearAssign($var);
	}

	function assign_by_ref($var,&$value)
	{
		return parent::assignByRef($var, $value);
	}

	/* fetch in a specific language  without theme consideration */
	function fetchLang($lg, $_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false)
	{
		global $prefs, $lang, $style_base, $tikidomain;

		if (isset($prefs['style']) && isset($style_base)) {
			if ($tikidomain and file_exists("templates/$tikidomain/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/styles/$style_base/$_smarty_tpl_file";
			} elseif ($tikidomain and file_exists("templates/$tikidomain/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/$_smarty_tpl_file";
			} elseif (file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";
			}
		}

		$_smarty_cache_id = $lg . $_smarty_cache_id;
		$_smarty_compile_id = $lg . $_smarty_compile_id;

		$lgSave = $prefs['language'];
		$prefs['language'] = $lg;
		$res = parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, null, $_smarty_display);
		$prefs['language'] = $lgSave; // Restore the language of the user triggering the notification

		return preg_replace("/^[ \t]*/", '', $res);
	}

	function display($resource_name = null, $cache_id=null, $compile_id = null, $parent = null, $content_type = 'text/html; charset=utf-8')
	{

		global $prefs;

		if ( !empty($prefs['feature_htmlpurifier_output']) and $prefs['feature_htmlpurifier_output'] == 'y' ) {
			static $loaded = false;
			static $purifier = null;
			if (!$loaded) {
				require_once('lib/htmlpurifier_tiki/HTMLPurifier.tiki.php');
				$config = getHTMLPurifierTikiConfig();
				$purifier = new HTMLPurifier($config);
				$loaded = true;
			}
		}

		//
		// By default, display is used with text/html content in UTF-8 encoding
		// If you want to output other data from smarty,
		//   - either use fetch() / fetchLang()
		//   - or set $content_type to '' (empty string) or another content type.
		//
		if ( $content_type != '' && ! headers_sent() ) {
			header('Content-Type: '.$content_type);
		}
		if ( !empty($prefs['feature_htmlpurifier_output']) and $prefs['feature_htmlpurifier_output'] == 'y' ) {
			return $purifier->purify(parent::display($resource_name, $cache_id, $compile_id));
		} else {
			return parent::display($resource_name, $cache_id, $compile_id);

		}
	}
	// Returns the file name associated to the template name
	function get_filename($template)
	{
		global $tikidomain, $style_base;
		if (!empty($tikidomain) && is_file($this->main_template_dir.'/'.$tikidomain.'/styles/'.$style_base.'/'.$template)) {
    			$file = "/$tikidomain/styles/$style_base/";
		} elseif (!empty($tikidomain) && is_file($this->main_template_dir.'/'.$tikidomain.'/'.$template)) {
    			$file = "/$tikidomain/";
		} elseif (is_file($this->main_template_dir.'/styles/'.$style_base.'/'.$template)) {
			$file = "/styles/$style_base/";
		} else {
    			$file = '/';
		}
		return $this->main_template_dir.$file.$template;
	}

	function set_request_overriders( $url_arguments_prefix, $arguments_list )
	{
		$this->url_overriding_prefix_stack[] = array( $url_arguments_prefix . '-', $arguments_list );
		$this->url_overriding_prefix =& $this->url_overriding_prefix_stack[ count($this->url_overriding_prefix_stack) - 1 ];
	}

	function remove_request_overriders( $url_arguments_prefix, $arguments_list )
	{
		$last_override_prefix = empty( $this->url_overriding_prefix_stack ) ? false : array_pop($this->url_overriding_prefix_stack);
		if ( ! is_array($last_override_prefix) || $url_arguments_prefix . '-' != $last_override_prefix[0] ) {
			trigger_error('URL Overriding prefix stack is in a bad state', E_USER_ERROR);
		}
		$this->url_overriding_prefix =& $this->url_overriding_prefix_stack[ count($this->url_overriding_prefix_stack) - 1 ];;
	}
}

if (!isset($tikidomain)) {
	$tikidomain = '';
}
$smarty = new Smarty_Tiki($tikidomain);
$smarty->loadFilter('pre', 'tr');
$smarty->loadFilter('pre', 'jq');

include_once('lib/smarty_tiki/resource.tplwiki.php');
$smarty->registerResource('tplwiki', array('smarty_resource_tplwiki_source', 'smarty_resource_tplwiki_timestamp', 'smarty_resource_tplwiki_secure', 'smarty_resource_tplwiki_trusted'));

include_once('lib/smarty_tiki/resource.wiki.php');
$smarty->registerResource('wiki', array('smarty_resource_wiki_source', 'smarty_resource_wiki_timestamp', 'smarty_resource_wiki_secure', 'smarty_resource_wiki_trusted'));

global $prefs;
// Assign the prefs array in smarty, by reference
$smarty->assignByRef('prefs', $prefs);

if ( !empty($prefs['log_tpl']) && $prefs['log_tpl'] === 'y' ) {
	$smarty->loadFilter('pre', 'log_tpl');
}
if ( !empty($prefs['feature_sefurl_filter']) && $prefs['feature_sefurl_filter'] === 'y' ) {
  require_once ('tiki-sefurl.php');
  $smarty->registerFilter('output', 'filter_out_sefurl');
}
