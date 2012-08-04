<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: toolbarslib.php 41817 2012-06-06 17:26:25Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once('lib/smarty_tiki/block.self_link.php');

$toolbarPickerIndex = -1;

abstract class Toolbar
{
	protected $wysiwyg;
	protected $icon;
	protected $label;
	protected $type;
	
	private $requiredPrefs = array();

	public static function getTag( $tagName ) // {{{
	{
		global $section;
		//we detect sheet first because it has unique buttons
		if ( $section == 'sheet' && $tag = ToolbarSheet::fromName($tagName) )
			return $tag;
		elseif ( $tag = Toolbar::getCustomTool($tagName) )
			return $tag;
		elseif ( $tag = ToolbarInline::fromName($tagName) )
			return $tag;
		elseif ( $tag = ToolbarBlock::fromName($tagName) )
			return $tag;
		elseif ( $tag = ToolbarLineBased::fromName($tagName) )
			return $tag;
		elseif ( $tag = ToolbarCkOnly::fromName($tagName) )
			return $tag;
		elseif ( $tag = ToolbarWikiplugin::fromName($tagName) )
			return $tag;
		elseif ( $tag = ToolbarPicker::fromName($tagName) )
			return $tag;
		elseif ( $tag = ToolbarDialog::fromName($tagName) )
			return $tag;
		elseif ( $tagName == 'fullscreen' )
			return new ToolbarFullscreen;
		elseif ( $tagName == 'tikiimage' )
			return new ToolbarFileGallery;
		elseif ( $tagName == 'tikifile' )
			return new ToolbarFileGalleryFile;
		elseif ( $tagName == 'help' )
			return new ToolbarHelptool;
		elseif ( $tagName == 'switcheditor' )
			return new ToolbarSwitchEditor;
		elseif ( $tagName == '-' )
			return new ToolbarSeparator;
		
	} // }}}

	public static function getList( $include_custom = true ) // {{{
	{
		global $tikilib;
		$parserlib = TikiLib::lib('parser');
		$plugins = $parserlib->plugin_get_list();
		
		foreach ( $plugins as & $name ) {
			$name = "wikiplugin_$name";
		}
		
		if ($include_custom) {
			$custom = Toolbar::getCustomList();
			$plugins = array_merge($plugins, $custom);
		}
		
		return array_unique(
						array_merge(
										array(
											'-',
											'bold',
											'italic',
											'underline',
											'strike',
											'sub',
											'sup',
											'tikilink',
											'link',
											'anchor',
											'color',
											'bgcolor',
											'center',
											'table',
											'rule',
											'pagebreak',
											'box',
											'email',			
											'h1',
											'h2',
											'h3',
											'titlebar',
											'toc',
											'list',
											'numlist',
											'specialchar',
											'smiley',
											'templates',
											'cut',
											'copy',
											'paste',
											'pastetext',
											'pasteword',
											'print',
											'spellcheck',
											'undo',
											'redo',
											'find',
											'replace',
											'selectall',
											'removeformat',
											'showblocks',
											'left',
											'right',
											'full',
											'indent',
											'outdent',
											'unlink',
											'style',
											'fontname',
											'fontsize',
											'format',
											'source',
											'fullscreen',
											'help',
											'tikiimage',
											'tikifile',
											'switcheditor',
											'autosave',
											'nonparsed',
											'bidiltr',
											'bidirtl',
										
											'sheetsave',	// spreadsheet ones
											'addrow',
											'addrowmulti',
											'addrowbefore',
											'deleterow',
											'addcolumn',
											'addcolumnbefore',
											'deletecolumn',
											'addcolumnmulti',
											'sheetgetrange',
											'sheetfind',
											'sheetrefresh',
											'sheetclose',
										),
										$plugins
						)
		);
	} // }}}
	
	public static function getCustomList()
	{

		global $prefs;
		if ( isset($prefs['toolbar_custom_list']) ) {
			$custom = @unserialize($prefs['toolbar_custom_list']);
			sort($custom);
		} else {
			$custom = array();
		}

		return $custom;
	}
	
	public static function getCustomTool($name)
	{
		global $prefs;
		if ( isset($prefs["toolbar_tool_$name"]) ) {
			$data = unserialize($prefs["toolbar_tool_$name"]);
			$tag = Toolbar::fromData($name, $data);
			return $tag;
		} else {
			return null;
		}
	
	}

	public static function isCustomTool($name)
	{
		global $prefs;
		return isset($prefs["toolbar_tool_$name"]);	
	}

	public static function saveTool($name, $label, $icon = 'img/icons/shading.png', $token = '', $syntax = '', $type = 'Inline', $plugin = '')
	{
		global $tikilib;
		
		$name = strtolower(TikiLib::remove_non_word_characters_and_accents($name));
		$standard_names = Toolbar::getList(false);
		$custom_list = Toolbar::getCustomList();
		if (in_array($name, $standard_names)) {		// don't allow custom tools with the same name as standard ones
			$c = 1;
			while (in_array($name . '_' . $c, $custom_list)) {
				$c++;
			}
			$name = $name . '_' . $c;
		}

		$prefName = "toolbar_tool_$name";
		$data = array('name'=>$name, 'label'=>$label, 'icon'=>$icon, 'token'=>$token, 'syntax'=>$syntax, 'type'=>$type, 'plugin'=>$plugin);
		
		$tikilib->set_preference($prefName, serialize($data));
		
		if ( !in_array($name, $custom_list) ) {
			$custom_list[] = $name;
			$tikilib->set_preference('toolbar_custom_list', serialize($custom_list));
		}
	}

	public static function deleteTool($name)
	{
		global $prefs, $tikilib;
		
		$name = strtolower($name);

		$prefName = "toolbar_tool_$name";
		if ( isset($prefs[$prefName]) ) {
			$tikilib->delete_preference($prefName);
			
			$list = array();
			if ( isset($prefs['toolbar_custom_list']) ) {
				$list = unserialize($prefs['toolbar_custom_list']);
			}
			if ( in_array($name, $list) ) {
				$list = array_diff($list, array($name));
				$tikilib->set_preference('toolbar_custom_list', serialize($list));
			}
	
		}
	}

	public static function deleteAllCustomTools()
	{
		global $tikilib;
		
		$tikilib->query('DELETE FROM `tiki_preferences` WHERE `name` LIKE \'toolbar_tool_%\'');
		$tikilib->delete_preference('toolbar_custom_list');
		
		//global $cachelib; require_once("lib/cache/cachelib.php");
		//$cachelib->invalidate('tiki_preferences_cache');
	}
	

	public static function fromData( $tagName, $data )
	{ // {{{
		
		$tag = null;
		
		switch ($data['type']) {
			case 'Inline':
				$tag = new ToolbarInline();
 				$tag->setSyntax($data['syntax']);
      	break;
			case 'Block':
				$tag = new ToolbarBlock();
				$tag->setSyntax($data['syntax']);
	      break;
			case 'LineBased':
				$tag = new ToolbarLineBased();
				$tag->setSyntax($data['syntax']);
   	   break;
			case 'Picker':
				$tag = new ToolbarPicker();
	      break;
			case 'Separator':
				$tag = new ToolbarSeparator();
	      break;
			case 'CkOnly':
				$tag = new ToolbarCkOnly($tagName);
	      break;
			case 'Fullscreen':
				$tag = new ToolbarFullscreen();
	      break;
			case 'TextareaResize':
				$tag = new ToolbarTextareaResize();
	      break;
			case 'Helptool':
				$tag = new ToolbarHelptool();
	      break;
			case 'FileGallery':
				$tag = new ToolbarFileGallery();
	      break;
			case 'Wikiplugin':
				if (!isset($data['plugin'])) {
					$data['plugin'] = '';
				}
				$tag = ToolbarWikiplugin::fromName('wikiplugin_' . $data['plugin']);
				if (empty($tag)) {
					$tag = new ToolbarWikiplugin();
				}
	      break;
			default:
				$tag = new ToolbarInline();
	      break;
		}

		$tag->setLabel($data['label'])
			->setWysiwygToken($data['token'])
				->setIcon(!empty($data['icon']) ? $data['icon'] : 'img/icons/shading.png')
						->setType($data['type']);
		
		return $tag;
	}	// {{{

	abstract function getWikiHtml( $areaId );

	function isAccessible() // {{{
	{
		global $prefs;

		foreach ( $this->requiredPrefs as $prefName )
			if ( ! isset($prefs[$prefName]) || $prefs[$prefName] != 'y' )
				return false;

		return true;
	} // }}}

	protected function addRequiredPreference( $prefName ) // {{{
	{
		$this->requiredPrefs[] = $prefName;
	} // }}}

	protected function setIcon( $icon ) // {{{
	{
		$this->icon = $icon;

		return $this;
	} // }}}

	protected function setLabel( $label ) // {{{
	{
		$this->label = $label;

		return $this;
	} // }}}

	protected function setWysiwygToken( $token ) // {{{
	{
		$this->wysiwyg = $token;

		return $this;
	} // }}}

	protected function setSyntax( $syntax ) // {{{
	{
		return $this;
	} // }}}

	protected function setType( $type ) // {{{
	{
		$this->type = $type;

		return $this;
	} // }}}

	function getIcon() // {{{
	{
		return $this->icon;
	} // }}}

	function getLabel() // {{{
	{
		return $this->label;
	} // }}}

	function getWysiwygToken( $areaId ) // {{{
	{
		return $this->wysiwyg;
	} // }}}
	
	
	function getWysiwygWikiToken( $areaId ) // {{{ // wysiwyg_htmltowiki
	{
		return null;
	} // }}}
	
	function getSyntax( $areaId ) // {{{
	{
		return '';
	} // }}}
	
	function getType() // {{{
	{
		return $this->type;
	} // }}}
	
	function getIconHtml() // {{{
	{
		global $headerlib;
		return '<img src="' . htmlentities($headerlib->convert_cdn($this->icon), ENT_QUOTES, 'UTF-8') . '" alt="' . htmlentities($this->getLabel(), ENT_QUOTES, 'UTF-8') . '" title="' . htmlentities($this->getLabel(), ENT_QUOTES, 'UTF-8') . '" class="icon"/>';
	} // }}}
	
	function getSelfLink( $click, $title, $class )
	{ // {{{
		global $smarty;
		
		$params = array();
		$params['_onclick'] = $click . (substr($click, strlen($click)-1) != ';' ? ';' : '') . 'return false;';
		$params['_class'] = 'toolbar ' . (!empty($class) ? ' '.$class : '');
		$params['_ajax'] = 'n';
		$content = $title;
		$params['_icon'] = $this->icon;
			
		if (strpos($class, 'qt-plugin') !== false && $this->icon == 'img/icons/plugin.png') {
			$params['_menu_text'] = 'y';
			$params['_menu_icon'] = 'y';
		}
		return smarty_block_self_link($params, $content, $smarty);
	} // }}}

	protected function setupCKEditorTool($js, $name, $label = '', $icon = '')
	{
		global $headerlib;
		if (empty($label)) {
			$label = $name;
		}
		$label = addcslashes($label, "'");
		$headerlib->add_js(
<<< JS
if (typeof window.CKEDITOR !== "undefined" && !window.CKEDITOR.plugins.get("{$name}")) {
	window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ',{$name}' : '{$name}' );
	window.CKEDITOR.plugins.add( '{$name}', {
		init : function( editor ) {
			var command = editor.addCommand( '{$name}', new window.CKEDITOR.command( editor , {
				modes: { wysiwyg:1 },
				exec: function(elem, editor, data) {
					{$js}
				},
				canUndo: false
			}));
			editor.ui.addButton( '{$name}', {
				label : '{$label}',
				command : '{$name}',
				icon: editor.config._TikiRoot + '{$icon}'
			});
		}
	});
}
JS
						, 10
		);
	}
}

class ToolbarSeparator extends Toolbar
{
	function __construct() // {{{
	{
		$this->setWysiwygToken('-')
			->setIcon('img/separator.gif')
				->setType('Separator');
	} // }}}

	function getWikiHtml( $areaId ) // {{{
	{
		return '|';
	} // }}}
}

class ToolbarCkOnly extends Toolbar
{
	function __construct( $token, $icon = '' ) // {{{
	{
		if (empty($icon)) {
			$img_path = 'lib/ckeditor_tiki/ckeditor-icons/' . strtolower($token) . '.gif';
			if (is_file($img_path)) {
				$icon = $img_path;
			} else {
				$icon = 'img/icons/shading.png';
			}
		}
		$this->setWysiwygToken($token)
			->setIcon($icon)
				->setType('CkOnly');
	} // }}}
	
	public static function fromName( $name ) // {{{
	{
		global $prefs;
		
		switch( $name ) {
		case 'templates':
			return new self( 'Templates' );
		case 'cut':
			return new self( 'Cut' );
		case 'copy':
			return new self( 'Copy' );
		case 'paste':
			return new self( 'Paste' );
		case 'pastetext':
			return new self( 'PasteText' );
		case 'pasteword':
			return new self( 'PasteFromWord' );
		case 'print':
			return new self( 'Print' );
		case 'spellcheck':
			return new self( 'SpellChecker' );
		case 'undo':
			return new self( 'Undo' );
		case 'redo':
			return new self( 'Redo' );
		case 'selectall':
			return new self( 'SelectAll' );
		case 'removeformat':
			return new self( 'RemoveFormat' );
		case 'showblocks':
			return new self( 'ShowBlocks' );
		case 'left':
			return new self( 'JustifyLeft' );
		case 'right':
			return new self( 'JustifyRight' );
		case 'full':
			return new self( 'JustifyBlock' );
		case 'indent':
			return new self( 'Indent' );
		case 'outdent':
			return new self( 'Outdent' );
		case 'unlink':
			return new self( 'Unlink' );
		case 'style':
			return new self( 'Styles' );
		case 'fontname':
			return new self( 'Font' );
		case 'fontsize':
			return new self( 'FontSize' );
		case 'format':
			return 	new self( 'Format' );
		case 'source':
			global $tikilib, $user, $page;
			$p = $prefs['wysiwyg_htmltowiki'] == 'y' ? 'tiki_p_wiki_view_source'  : 'tiki_p_use_HTML';
			if ($tikilib->user_has_perm_on_object($user, $page, 'wiki page', $p)) {
				return new self('Source');
			} else {
				return null;
			}
		case 'autosave':
			return new self( 'autosave', 'lib/ckeditor_tiki/plugins/autosave/images/ajaxAutoSaveDirty.gif');
		case 'sub':
			return new self( 'Subscript' );
		case 'sup':
			return new self( 'Superscript' );
		case 'showblocks':
			return new self( 'ShowBlocks' );
		case 'anchor':
			return new self( 'Anchor' );
		case 'bidiltr':
			return new self( 'BidiLtr' );
		case 'bidirtl':
			return new self( 'BidiRtl' );
		}
	} // }}}

	function getWikiHtml( $areaId ) // {{{
	{
		return null;
	} // }}}
	
	function getWysiwygWikiToken( $areaId ) // {{{ // wysiwyg_htmltowiki
	{
		switch ($this->wysiwyg) {
			case 'autosave':
			case 'Copy':
			case 'Cut':
			case 'Format':
			case 'JustifyLeft':
			case 'Paste':
			case 'Redo': 
			case 'RemoveFormat':
			case 'ShowBlocks': 
			case 'Source':
			case 'Undo':
				 return $this->wysiwyg;
			    break;
			default: return null;
		}
	} // }}}	
	
	function getLabel() // {{{
	{
		return $this->wysiwyg;
	} // }}}

	function getIconHtml() // {{{ for admin page
	{
		global $headerlib;
		
		if ((!empty($this->icon) && $this->icon !== 'img/icons/shading.png') || in_array($this->label, array('Autosave'))) {
			return parent::getIconHtml();
		}
		
		$headerlib->add_cssfile('lib/ckeditor/skins/kama/editor.css');
		$cls = strtolower($this->wysiwyg);
		$cls = str_replace(array('selectall', 'removeformat', 'spellchecker'), array('selectAll', 'removeFormat', 'checkspell'), $cls);	// work around some "features" in ckeditor icons.css
		$headerlib->add_css(
						'span.cke_skin_kama {border: none;background: none;padding:0;margin:0;}'.
						'.toolbars-admin .row li.toolbar > span.cke_skin_kama {display: inline-block;}'
		);
		return '<span class="cke_skin_kama"><span class="cke_button"><span class="cke_button_' . htmlentities($cls, ENT_QUOTES, 'UTF-8') . '"' .
						' title="' . htmlentities($this->getLabel(), ENT_QUOTES, 'UTF-8') . '">'.
						'<span class="cke_icon"> </span>'.
					'</span></span></span>';
	} // }}}
}

class ToolbarInline extends Toolbar
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'bold':
			$label = tra('Bold');
			$icon = tra('img/icons/text_bold.png');
			$wysiwyg = 'Bold';
			$syntax = '__text__';
      break;
		case 'italic':
			$label = tra('Italic');
			$icon = tra('img/icons/text_italic.png');
			$wysiwyg = 'Italic';
			$syntax = "''text''";
      break;
		case 'underline':
			$label = tra('Underline');
			$icon = tra('img/icons/text_underline.png');
			$wysiwyg = 'Underline';
			$syntax = "===text===";
      break;
		case 'strike':
			$label = tra('Strikethrough');
			$icon = tra('img/icons/text_strikethrough.png');
			$wysiwyg = 'Strike';
			$syntax = '--text--';
      break;
		case 'nonparsed':
			$label = tra('Non-parsed (Wiki syntax does not apply)');
			$icon = tra('img/icons/noparse.png');
			$wysiwyg = null;
			$syntax = '~np~text~/np~';
      break;
		default:
			return;
		}

		$tag = new self;
		$tag->setLabel($label)
			->setWysiwygToken($wysiwyg)
				->setIcon(!empty($icon) ? $icon : 'img/icons/shading.png')
					->setSyntax($syntax)
						->setType('Inline');
		
		return $tag;
	} // }}}

	function getSyntax( $areaId ) // {{{
	{
		return $this->syntax;
	} // }}}
	
	protected function setSyntax( $syntax ) // {{{
	{
		$this->syntax = $syntax;

		return $this;
	} // }}}
	
	function getWysiwygWikiToken( $areaId ) // {{{ // wysiwyg_htmltowiki
	{
		return $this->getWysiwygToken($areaId);
	} // }}}
	
	function getWikiHtml( $areaId ) // {{{
	{
		if ($this->syntax == '~np~text~/np~') {	// closing ~/np~ tag breaks toolbar when inside nested plugins
			return $this->getSelfLink(
							'insertAt(\'' . $areaId . '\', \'~np~text~\'+\'/np~\');',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'),
							'qt-inline'
			);
		} else {
			return $this->getSelfLink(
							'insertAt(\'' . $areaId . '\', \'' . addslashes(htmlentities($this->syntax, ENT_COMPAT, 'UTF-8')) . '\');',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'),
							'qt-inline'
			);
		}

	} // }}}
	
}

class ToolbarBlock extends ToolbarInline // Will change in the future
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		global $prefs;
		switch( $tagName ) {
		case 'center':
			$label = tra('Align Center');
			$icon = tra('img/icons/text_align_center.png');
			$wysiwyg = 'JustifyCenter';
			if ($prefs['feature_use_three_colon_centertag'] == 'y') {
				$syntax = ":::text:::";
			} else {
				$syntax = "::text::";
			}
      break;
		case 'rule':
			$label = tra('Horizontal Bar');
			$icon = tra('img/icons/page.png');
			$wysiwyg = 'HorizontalRule';
			$syntax = '---';
      break;
		case 'pagebreak':
			$label = tra('Page Break');
			$icon = tra('img/icons/page_break.png');
			$wysiwyg = 'PageBreak';
			$syntax = '...page...';
      break;
		case 'box':
			$label = tra('Box');
			$icon = tra('img/icons/box.png');
			$wysiwyg = 'Box';
			$syntax = '^text^';
      break;
		case 'email':
			$label = tra('Email');
			$icon = tra('img/icons/email.png');
			$wysiwyg = null;			
			$syntax = '[mailto:email@example.com|text]';
      break;				
		case 'h1':
		case 'h2':
		case 'h3':
			$label = tra('Heading') . ' ' . $tagName{1};
			$icon = 'img/icons/text_heading_' . $tagName{1} . '.png';
			$wysiwyg = null;
			$syntax = str_repeat('!', $tagName{1}) . 'text';
      break;
		case 'titlebar':
			$label = tra('Title bar');
			$icon = 'img/icons/text_padding_top.png';
			$wysiwyg = null;
			$syntax = '-=text=-';
      break;
		case 'toc':
			$label = tra('Table of contents');
			$icon = tra('img/icons/book.png');
			$wysiwyg = 'TOC';
			$syntax = '{maketoc}';
      break;
		default:
			return;
		}

		$tag = new self;
		$tag->setLabel($label)
			->setWysiwygToken($wysiwyg)
			->setIcon(!empty($icon) ? $icon : 'img/icons/shading.png')
			->setSyntax($syntax)
			->setType('Block');
		
		return $tag;
	} // }}}

	function getWikiHtml( $areaId ) // {{{
	{
		if ($this->syntax == '...page...') {	// for some reason breaks toolbar when inside nested plugins
			return $this->getSelfLink(
							'insertAt(\'' . $areaId . '\', \'...\'+\'page\'+\'...\');',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'),
							'qt-block'
			);
		} else {
			return $this->getSelfLink(
							'insertAt(\'' . $areaId . '\', \'' . addslashes(htmlentities($this->syntax, ENT_COMPAT, 'UTF-8')) . '\', true);',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'),
							'qt-block'
			);
		}
	} // }}}
}

class ToolbarLineBased extends ToolbarInline // Will change in the future
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'list':
			$label = tra('Unordered List');
			$icon = tra('img/icons/text_list_bullets.png');
			$wysiwyg =  'BulletedList';
			$syntax = '*text';
      break;
		case 'numlist':
			$label = tra('Ordered List');
			$icon = tra('img/icons/text_list_numbers.png');
			$wysiwyg =  'NumberedList';
			$syntax = '#text';
      break;
		default:
			return;
		}

		$tag = new self;
		$tag->setLabel($label)
			->setWysiwygToken($wysiwyg)
			->setIcon(!empty($icon) ? $icon : 'img/icons/shading.png')
			->setSyntax($syntax)
			->setType('LineBased');
		
		return $tag;
	} // }}}

	function getWikiHtml( $areaId ) // {{{
	{
		return $this->getSelfLink(
						'insertAt(\'' . $areaId . '\', \'' . addslashes(htmlentities($this->syntax, ENT_COMPAT, 'UTF-8')) . '\', true, true);',
						htmlentities($this->label, ENT_QUOTES, 'UTF-8'),
						'qt-line'
		);
	} // }}}
}


class ToolbarPicker extends Toolbar
{
	private $list;
	private $name;
	
	public static function fromName( $tagName ) // {{{
	{
		global $headerlib, $section;
		$prefs = array();
		$styleType = '';
		
		switch( $tagName ) {
		case 'specialchar':
			$wysiwyg = 'SpecialChar';
			$label = tra('Special Characters');
			$icon = tra('lib/ckeditor_tiki/ckeditor-icons/specialchar.gif');
			// Line taken from DokuWiki
            $list = explode(' ', 'À à Á á Â â Ã ã Ä ä Ǎ ǎ Ă ă Å å Ā ā Ą ą Æ æ Ć ć Ç ç Č č Ĉ ĉ Ċ ċ Ð đ ð Ď ď È è É é Ê ê Ë ë Ě ě Ē ē Ė ė Ę ę Ģ ģ Ĝ ĝ Ğ ğ Ġ ġ Ĥ ĥ Ì ì Í í Î î Ï ï Ǐ ǐ Ī ī İ ı Į į Ĵ ĵ Ķ ķ Ĺ ĺ Ļ ļ Ľ ľ Ł ł Ŀ ŀ Ń ń Ñ ñ Ņ ņ Ň ň Ò ò Ó ó Ô ô Õ õ Ö ö Ǒ ǒ Ō ō Ő ő Œ œ Ø ø Ŕ ŕ Ŗ ŗ Ř ř Ś ś Ş ş Š š Ŝ ŝ Ţ ţ Ť ť Ù ù Ú ú Û û Ü ü Ǔ ǔ Ŭ ŭ Ū ū Ů ů ǖ ǘ ǚ ǜ Ų ų Ű ű Ŵ ŵ Ý ý Ÿ ÿ Ŷ ŷ Ź ź Ž ž Ż ż Þ þ ß Ħ ħ ¿ ¡ ¢ £ ¤ ¥ € ¦ § ª ¬ ¯ ° ± ÷ ‰ ¼ ½ ¾ ¹ ² ³ µ ¶ † ‡ · • º ∀ ∂ ∃ Ə ə ∅ ∇ ∈ ∉ ∋ ∏ ∑ ‾ − ∗ √ ∝ ∞ ∠ ∧ ∨ ∩ ∪ ∫ ∴ ∼ ≅ ≈ ≠ ≡ ≤ ≥ ⊂ ⊃ ⊄ ⊆ ⊇ ⊕ ⊗ ⊥ ⋅ ◊ ℘ ℑ ℜ ℵ ♠ ♣ ♥ ♦ 𝛼 𝛽 𝛤 𝛾 𝛥 𝛿 𝜀 𝜁 𝛨 𝜂 𝛩 𝜃 𝜄 𝜅 𝛬 𝜆 𝜇 𝜈 𝛯 𝜉 𝛱 𝜋 𝛳 𝜍 𝛴 𝜎 𝜏 𝜐 𝛷 𝜑 𝜒 𝛹 𝜓 𝛺 𝜔 𝛻 𝜕 ★ ☆ ☎ ☚ ☛ ☜ ☝ ☞ ☟ ☹ ☺ ✔ ✘ × „ “ ” ‚ ‘ ’ « » ‹ › — – … ← ↑ → ↓ ↔ ⇐ ⇑ ⇒ ⇓ ⇔ © ™ ® ′ ″');
			$list = array_combine($list, $list);
      break;
		case 'smiley':
			$wysiwyg = 'Smiley';
			$label = tra('Smileys');
			$icon = tra('img/smiles/icon_smile.gif');
			$rawList = array( 'biggrin', 'confused', 'cool', 'cry', 'eek', 'evil', 'exclaim', 'frown', 'idea', 'lol', 'mad', 'mrgreen', 'neutral', 'question', 'razz', 'redface', 'rolleyes', 'sad', 'smile', 'surprised', 'twisted', 'wink', 'arrow', 'santa' );
			$prefs[] = 'feature_smileys';

			$list = array();
			global $headerlib;
			foreach ( $rawList as $smiley ) {
				$tra = htmlentities(tra($smiley), ENT_QUOTES, 'UTF-8');
				$list["(:$smiley:)"] = '<img src="' . $headerlib->convert_cdn('img/smiles/icon_' .$smiley . '.gif') . '" alt="' . $tra . '" title="' . $tra . '" width="15" height="15" />';
			}
      break;
		case 'color':
			$wysiwyg = 'TextColor';
			$label = tra('Foreground color');
			$icon = tra('img/icons/palette.png');
			$rawList = array();
			$styleType = 'color';
			
		   $hex = array('0', '3', '6', '8', '9', 'C', 'F');
			$count_hex = count($hex);
			
			for ($r = 0; $r < $count_hex; $r+=2) { // red
				for ($g = 0; $g < $count_hex; $g+=2) { // green
					for ($b = 0; $b < $count_hex; $b+=2) { // blue
						$color = $hex[$r].$hex[$g].$hex[$b];
						$rawList[] = $color;
					}
				}
			}
			
			$list = array();	
			foreach ( $rawList as $color) {
				$list["~~#$color:text~~"] = "<span style='background-color: #$color' title='#$color' />&nbsp;</span>";
			}
			
			if ($section == 'sheet')
				$list['reset'] = "<span title='".tra("Reset Colors")."' class='toolbars-picker-reset' reset='true'>".tra("Reset")."</span>";
			
			$headerlib->add_css('.toolbars-picker span {display: block; width: 14px; height: 12px}');
      break;

		case 'bgcolor':
			$label = tra('Background Color');
			$icon = tra('img/icons/palette_bg.png');
			$wysiwyg = 'BGColor';
			$styleType = 'background-color';
			$rawList = array();
			
			$hex = array('0', '3', '6', '8', '9', 'C', 'F');
			$count_hex = count($hex);
			
			for ($r = 0; $r < $count_hex; $r+=2) { // red
				for ($g = 0; $g < $count_hex; $g+=2) { // green
					for ($b = 0; $b < $count_hex; $b+=2) { // blue
						$color = $hex[$r].$hex[$g].$hex[$b];
						$rawList[] = $color;
					}
				}
			}
			
			$list = array();
			foreach ( $rawList as $color) {
				$list["~~black,#$color:text~~"] = "<span style='background-color: #$color' title='#$color' />&nbsp;</span>";
			}
			if ($section == 'sheet')
				$list['reset'] = "<span title='".tra("Reset Colors")."' class='toolbars-picker-reset' reset='true'>".tra("Reset")."</span>";
			
			$headerlib->add_css('.toolbars-picker span {display: block; width: 14px; height: 12px}');
      break;

		default:
			return;
		}

		$tag = new self;
		$tag->setWysiwygToken($wysiwyg)
			->setLabel($label)
			->setIcon(!empty($icon) ? $icon : 'img/icons/shading.png')
			->setList($list)
			->setType('Picker')
			->setName($tagName)
			->setStyleType($styleType);
		
		foreach ( $prefs as $pref ) {
			$tag->addRequiredPreference($pref);
		}

		global $toolbarPickerIndex;
		++$toolbarPickerIndex;
		$tag->index = $toolbarPickerIndex;
		ToolbarPicker::setupJs();

		return $tag;
	} // }}}

	function setName( $name ) // {{{
	{
		$this->name = $name;
		
		return $this;
	} // }}}

	
	function getWysiwygWikiToken( $areaId ) // {{{ // wysiwyg_htmltowiki
	{
		switch ($this->wysiwyg) {
			case 'BGColor':
			case 'TextColor':
			case 'SpecialChar':
				return $this->wysiwyg;
	    		break;
			default:
				return null;
		}
	} // }}}
	
	
	function setList( $list ) // {{{
	{
		$this->list = $list;
		
		return $this;
	} // }}}

	protected function setSyntax( $syntax ) // {{{
	{
		$this->syntax = $syntax;

		return $this;
	} // }}}
	
	public function getSyntax( $areaId = '$areaId' )
	{
		global $section;
		if ( $section == 'sheet' ) {
			return 'displayPicker( this, \'' . $this->name . '\', \'' . $areaId . '\', true, \'' . $this->styleType . '\' )';	// is enclosed in double quotes later
		} else {
			return 'displayPicker( this, \'' . $this->name . '\', \'' . $areaId . '\' )';	// is enclosed in double quotes later
		}
	}
	
	static private function setupJs()
	{
		
		static $pickerAdded = false;

		if ( ! $pickerAdded ) {
			$pickerAdded = true;
		}
	}

	function getWikiHtml( $areaId ) // {{{
	{
		global $headerlib, $prefs;
		$headerlib->add_js("window.pickerData['$this->name'] = " . str_replace('\/', '/', json_encode($this->list)) . ";");
		if ($prefs['feature_jquery_ui'] != 'y') {
			$headerlib->add_jsfile("lib/jquery/jquery-ui/ui/jquery-ui-$headerlib->jqueryui_version.js");
			$headerlib->add_cssfile('lib/jquery/jquery-ui/themes/' . $prefs['feature_jquery_ui_theme'] . '/jquery-ui.css');
		}
		
		return $this->getSelfLink(
						$this->getSyntax($areaId),
						htmlentities($this->label, ENT_QUOTES, 'UTF-8'),
						'qt-picker'
		);
	} // }}}
	
	protected function setStyleType( $type ) // {{{
	{
		$this->styleType = $type;

		return $this;
	} // }}}
}

class ToolbarDialog extends Toolbar
{
	private $list;
	private $index;
	private $name;
	
	public static function fromName( $tagName ) // {{{
	{
		global $prefs;
		$tool_prefs = array();

		switch( $tagName ) {
		case 'tikilink':
			$label = tra('Wiki Link');
			$icon = tra('img/icons/page_link.png');
			$wysiwyg = '';	// cke link dialog now adapted for wiki links
			$list = array('Wiki Link',
						'<label for="tbWLinkDesc">Show this text</label>',
						'<input type="text" id="tbWLinkDesc" class="ui-widget-content ui-corner-all" style="width: 98%" />',
						'<label for="tbWLinkPage">Link to this page</label>',
						'<input type="text" id="tbWLinkPage" class="ui-widget-content ui-corner-all" style="width: 98%" />',
						$prefs['wikiplugin_alink'] == 'y' ? '<label for="tbWLinkAnchor">Anchor:</label>' : '',
						$prefs['wikiplugin_alink'] == 'y' ? '<input type="text" id="tbWLinkAnchor" class="ui-widget-content ui-corner-all" style="width: 98%" />' : '',
						$prefs['feature_semantic'] == 'y' ? '<label for="tbWLinkRel">Semantic relation:</label>' : '',
						$prefs['feature_semantic'] == 'y' ? '<input type="text" id="tbWLinkRel" class="ui-widget-content ui-corner-all" style="width: 98%" />' : '',
						'{"open": function () { dialogInternalLinkOpen(area_id); },
						"buttons": { "Cancel": function() { dialogSharedClose(area_id,this); },'.
									'"Insert": function() { dialogInternalLinkInsert(area_id,this); }}}'
					);

      break;
		case 'link':
			$wysiwyg = 'Link';
			$label = tra('External Link');
			$icon = tra('img/icons/world_link.png');
			$list = array('External Link',
						'<label for="tbLinkDesc">Show this text</label>',
						'<input type="text" id="tbLinkDesc" class="ui-widget-content ui-corner-all" style="width: 98%" />',
						'<label for="tbLinkURL">link to this URL</label>',
						'<input type="text" id="tbLinkURL" class="ui-widget-content ui-corner-all" style="width: 98%" />',
						'<label for="tbLinkRel">Relation:</label>',
						'<input type="text" id="tbLinkRel" class="ui-widget-content ui-corner-all" style="width: 98%" />',
						$prefs['cachepages'] == 'y' ? '<br /><label for="tbLinkNoCache" style="display:inline;">No cache:</label>' : '',
						$prefs['cachepages'] == 'y' ? '<input type="checkbox" id="tbLinkNoCache" class="ui-widget-content ui-corner-all" />' : '',
						'{"width": 300, "open": function () { dialogExternalLinkOpen( area_id ) },
						"buttons": { "Cancel": function() { dialogSharedClose(area_id,this); },'.
									'"Insert": function() { dialogExternalLinkInsert(area_id,this) }}}'
					);
      break;

		case 'table':
			$icon = tra('img/icons/table.png');
			$wysiwyg = 'Table';
			$label = tra('Table Builder');
			$list = array('Table Builder',
						'{"open": function () { dialogTableOpen(area_id,this); },
						"width": 320, "buttons": { "Cancel": function() { dialogSharedClose(area_id,this); },'.
												  '"Insert": function() { dialogTableInsert(area_id,this); }}}'
					);
      break;

		case 'find':
			$icon = tra('img/icons/find.png');
			$wysiwyg = 'Find';
			$label = tra('Find Text');
			$list = array('Find Text',
						'<label>Search:</label>',
						'<input type="text" id="tbFindSearch" class="ui-widget-content ui-corner-all" />',
						'<label for="tbFindCase" style="display:inline;">Case Insensitivity:</label>',
						'<input type="checkbox" id="tbFindCase" checked="checked" class="ui-widget-content ui-corner-all" />',
						'<p class="description">Note: Uses regular expressions</p>',	// TODO add option to not
						'{"open": function() { dialogFindOpen(area_id); },'.
						 '"buttons": { "Close": function() { dialogSharedClose(area_id,this); },'.
									  '"Find": function() { dialogFindFind(area_id); }}}'
					);

      break;

		case 'replace':
			$icon = tra('img/icons/text_replace.png');
			$wysiwyg = 'Replace';
			$label = tra('Text Replace');
			$tool_prefs[] = 'feature_wiki_replace';
			
			$list = array('Text Replace',
						'<label for="tbReplaceSearch">Search:</label>',
						'<input type="text" id="tbReplaceSearch" class="ui-widget-content ui-corner-all" />',
						'<label for="tbReplaceReplace">Replace:</label>',
						'<input type="text" id="tbReplaceReplace" class="ui-widget-content ui-corner-all clearfix" />',
						'<label for="tbReplaceCase" style="display:inline;">Case Insensitivity:</label>',
						'<input type="checkbox" id="tbReplaceCase" checked="checked" class="ui-widget-content ui-corner-all" />',
						'<br /><label for="tbReplaceAll" style="display:inline;">Replace All:</label>',
						'<input type="checkbox" id="tbReplaceAll" checked="checked" class="ui-widget-content ui-corner-all" />',
						'<p class="description">Note: Uses regular expressions</p>',	// TODO add option to not
						'{"open": function() { dialogReplaceOpen(area_id); },'.
						 '"buttons": { "Close": function() { dialogSharedClose(area_id,this); },'.
									  '"Replace": function() { dialogReplaceReplace(area_id); }}}'
					);

      break;

		default:
			return;
		}

		$tag = new self;
		$tag->name = $tagName;
		$tag->setWysiwygToken($wysiwyg)
			->setLabel($label)
			->setIcon(!empty($icon) ? $icon : 'img/icons/shading.png')
			->setList($list)
			->setType('Dialog');
		
		foreach ( $tool_prefs as $pref ) {
			$tag->addRequiredPreference($pref);
		}

		global $toolbarDialogIndex;
		++$toolbarDialogIndex;
		$tag->index = $toolbarDialogIndex;
		
		ToolbarDialog::setupJs();

		return $tag;
	} // }}}

	function setList( $list ) // {{{
	{
		$this->list = $list;
		
		return $this;
	} // }}}

	protected function setSyntax( $syntax ) // {{{
	{
		$this->syntax = $syntax;

		return $this;
	} // }}}
	
	public function getSyntax( $areaId = '$areaId' )
	{
		return 'displayDialog( this, ' . $this->index . ', \'' . $areaId . '\')';
	}
	
	static private function setupJs()
	{
		
		static $dialogAdded = false;

		if ( ! $dialogAdded ) {
			$dialogAdded = true;
		}
	}

	function getWikiHtml( $areaId ) // {{{
	{
		global $headerlib;
		$headerlib->add_js("window.dialogData[$this->index] = " . json_encode($this->list) . ";", 1 + $this->index);
		
		return $this->getSelfLink(
						$this->getSyntax($areaId),
						htmlentities($this->label, ENT_QUOTES, 'UTF-8'),
						'qt-picker'
		);
	} // }}}

	function getWysiwygToken( $areaId ) // {{{
	{
		if (!empty($this->wysiwyg) && $this->name == 'tikilink') {	// TODO remove when ckeditor can handle tikilinks
			
			global $headerlib;
			$headerlib->add_js("window.dialogData[$this->index] = " . json_encode($this->list) . ";", 1 + $this->index);
			$label = addcslashes($this->label, "'");
			$headerlib->add_js(
<<< JS
if (typeof window.CKEDITOR !== "undefined" && !window.CKEDITOR.plugins.get("{$this->name}")) {
	window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ',{$this->name}' : '{$this->name}' );
	window.CKEDITOR.plugins.add( '{$this->name}', {
		init : function( editor ) {
			var command = editor.addCommand( '{$this->name}', new window.CKEDITOR.command( editor , {
				modes: { wysiwyg:1 },
				exec: function(elem, editor, data) {
					{$this->getSyntax( $areaId )};
				},
				canUndo: false
			}));
			editor.ui.addButton( '{$this->name}', {
				label : '{$label}',
				command : '{$this->name}',
				icon: editor.config._TikiRoot + '{$this->icon}'
			});
	
		}
	});
}
JS
							, 10
			);		
			
		}
		return $this->wysiwyg;
	} // }}}
	
	function getWysiwygWikiToken( $areaId ) // {{{ // wysiwyg_htmltowiki
	{
		switch ($this->name) {
			case 'tikilink':
				$this->wysiwyg = 'tikilink';
				break;
			default:
		}

		return $this->getWysiwygToken($areaId);
	} // }}}

}

class ToolbarFullscreen extends Toolbar
{
	function __construct() // {{{
	{
		$this->setLabel(tra('Full Screen Edit'))
			->setIcon('img/icons/application_get.png')
			->setWysiwygToken('Maximize')
			->setType('Fullscreen');
	} // }}}

	function getWikiHtml( $areaId ) // {{{
	{
		
		return $this->getSelfLink(
						'toggleFullScreen(\''.$areaId.'\');return false;',
						htmlentities($this->label, ENT_QUOTES, 'UTF-8'),
						'qt-fullscreen'
		);

	} // }}}

	function getWysiwygWikiToken( $areaId ) // {{{ // wysiwyg_htmltowiki
	{
		return $this->getWysiwygToken($areaId);
	} // }}}

}

class ToolbarHelptool extends Toolbar
{
	function __construct() // {{{
	{
		$this->setLabel(tra('Wiki Help'))
			->setIcon('img/icons/help.png')
			->setType('Helptool');
	} // }}}
	
	function getWikiHtml( $areaId ) // {{{
	{
		global $wikilib, $smarty, $plugins, $section;
		if (!isset($plugins)) {
			include_once ('lib/wiki/wikilib.php');
			$plugins = $wikilib->list_plugins(true, $areaId);
		}
		
		$sheethelp = '';
		
		if ($section == 'sheet') {
			$sheethelp .= $smarty->fetch('tiki-edit_help_sheet.tpl');
			$sheethelp .= $smarty->fetch('tiki-edit_help_sheet_interface.tpl');
		}
		
		$smarty->assign_by_ref('plugins', $plugins);
		return  $smarty->fetch('tiki-edit_help.tpl') .
				$smarty->fetch('tiki-edit_help_plugins.tpl') .
				$sheethelp;
		
	} // }}}

	function getWysiwygToken( $areaId ) // {{{
	{

		global $wikilib, $smarty, $plugins;
		
		include_once ('lib/wiki/wikilib.php');
		$plugins = $wikilib->list_plugins(true, $areaId);
		
		$smarty->assign_by_ref('plugins', $plugins);

		global $prefs;
		if ($prefs['wysiwyg_htmltowiki'] === 'y') {
			$exec_js = $smarty->fetch('tiki-edit_help_wiki_wysiwyg.tpl') .
					$smarty->fetch('tiki-edit_help_plugins.tpl');
			$this->setLabel(tra('Wiki Wysiwyg Help'));
		} else {
			$exec_js = $smarty->fetch('tiki-edit_help_wysiwyg.tpl') .
					$smarty->fetch('tiki-edit_help_plugins.tpl');
			$this->setLabel(tra('Wysiwyg Help'));
		}

		$name = 'tikihelp';

		global $headerlib;
		$label = addcslashes($this->label, "'");
		$headerlib->add_js(
<<< JS
if (typeof window.CKEDITOR !== "undefined" && !window.CKEDITOR.plugins.get("{$name}")) {
	window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ',{$name}' : '{$name}' );
	window.CKEDITOR.plugins.add( '{$name}', {
		init : function( editor ) {
			var command = editor.addCommand( '{$name}', new window.CKEDITOR.command( editor , {
				modes: { wysiwyg:1 },
				exec: function(elem, editor, data) {
					openEditHelp();
					return false;
				},
				canUndo: false
			}));
			editor.ui.addButton( '{$name}', {
				label : '{$label}',
				command : '{$name}',
				icon: editor.config._TikiRoot + '{$this->icon}'
			});
	
		}
	});
}
JS
						, 10
		);
		return $name;
	}

	function getWysiwygWikiToken( $areaId ) // {{{ // wysiwyg_htmltowiki
	{
		return $this->getWysiwygToken($areaId);
	} // }}}

}

class ToolbarFileGallery extends Toolbar
{
	private $name;
	
	function __construct() // {{{
	{
		$this->setLabel(tra('Choose or upload images'))
			->setIcon(tra('img/icons/pictures.png'))
			->setWysiwygToken('tikiimage')
			->setType('FileGallery')
			->addRequiredPreference('feature_filegals_manager');
	} // }}}
	
	function getSyntax( $areaId )
	{
		global $smarty;
		$smarty->loadPlugin('smarty_function_filegal_manager_url');
		return 'openFgalsWindow(\''.htmlentities(smarty_function_filegal_manager_url(array('area_id'=>$areaId), $smarty)).'\', true);';
	}

	function getWikiHtml( $areaId ) // {{{
	{
		return $this->getSelfLink($this->getSyntax($areaId), htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-filegal');
	} // }}}

	function getWysiwygToken( $areaId ) // {{{
	{
		if (!empty($this->wysiwyg)) {
			$this->name = $this->wysiwyg;	// temp
			$exec_js = str_replace('&amp;', '&', $this->getSyntax($areaId));	// odd?
			
			$this->setupCKEditorTool($exec_js, $this->name, $this->label, $this->icon);
		}
		return $this->wysiwyg;
	} // }}}

	function getWysiwygWikiToken( $areaId ) // {{{ // wysiwyg_htmltowiki
	{
		return $this->getWysiwygToken($areaId);
	} // }}}

	function isAccessible() // {{{
	{
		return parent::isAccessible();
	} // }}}
}

class ToolbarFileGalleryFile extends ToolbarFileGallery
{

	function __construct() // {{{
	{
		$this->setLabel(tra('Choose or upload files'))
			->setIcon(tra('img/icons/file-manager-add.png'))
			->setWysiwygToken('tikifile')
			->setType('FileGallery')
			->addRequiredPreference('feature_filegals_manager');
	} // }}}

	function getSyntax( $areaId )
	{
		global $smarty;
		$smarty->loadPlugin('smarty_function_filegal_manager_url');
		return 'openFgalsWindow(\''.htmlentities(smarty_function_filegal_manager_url(array('area_id'=>$areaId), $smarty)).'&insertion_syntax=file\', true);';
	}

}

class ToolbarSwitchEditor extends Toolbar
{
	private $name;
	function __construct() // {{{
	{
		$this->setLabel(tra('Switch Editor (wiki or WYSIWYG)'))
			->setIcon(tra('img/icons/pencil_go.png'))
			->setWysiwygToken('tikiswitch')
			->setType('SwitchEditor')
			->addRequiredPreference('feature_wysiwyg');
	} // }}}

	function getWikiHtml( $areaId ) // {{{
	{
		return $this->getSelfLink(
						'switchEditor(\'wysiwyg\', $(this).parents(\'form\')[0]);',
						htmlentities($this->label, ENT_QUOTES, 'UTF-8'),
						'qt-switcheditor'
		);
	} // }}}

	function getWysiwygToken( $areaId ) // {{{
	{
		global $prefs;
		if (!empty($this->wysiwyg)) {
			$this->name = $this->wysiwyg;	// temp
			
		if ($prefs['feature_wysiwyg'] == 'y' && $prefs['wysiwyg_optional'] == 'y') {
			global $headerlib;
			$label = addcslashes($this->label, "'");
			$headerlib->add_js(
<<< JS
if (typeof window.CKEDITOR !== "undefined" && !window.CKEDITOR.plugins.get("{$this->name}")) {
	window.CKEDITOR.config.extraPlugins += (window.CKEDITOR.config.extraPlugins ? ',{$this->name}' : '{$this->name}' );
	window.CKEDITOR.plugins.add( '{$this->name}', {
		init : function( editor ) {
			var command = editor.addCommand( '{$this->name}', new window.CKEDITOR.command( editor , {
				modes: { wysiwyg:1 },
				exec: function(elem, editor, data) {
					switchEditor('wiki', $('#$areaId').parents('form')[0]);
				},
				canUndo: false
			}));
			editor.ui.addButton( '{$this->name}', {
				label : '{$label}',
				command : '{$this->name}',
				icon: editor.config._TikiRoot + '{$this->icon}'
			});
		}
	});
}
JS
							, 10
			);			
		}
			
		}
		return $this->wysiwyg;
	} // }}}
	
	
	function getWysiwygWikiToken( $areaId ) // {{{ // wysiwyg_htmltowiki
	{
		return $this->getWysiwygToken($areaId); 
	} // }}}	
	
	
	function isAccessible() // {{{
	{
		global $tiki_p_edit_switch_mode;

		return parent::isAccessible() &&
				! isset($_REQUEST['hdr']) &&		// or in section edit
				$tiki_p_edit_switch_mode === 'y';	// or no perm (new in 7.1)
	} // }}}
	
/*	function getLabel() // {{{
	{
		return $this->label;
	} // }}}
*/
	
}

class ToolbarWikiplugin extends Toolbar
{
	private $pluginName;

	public static function fromName( $name ) // {{{
	{
		global $tikilib;
		$parserlib = TikiLib::lib('parser');
		
		if ( substr($name, 0, 11) == 'wikiplugin_' ) {
			$name = substr($name, 11);
			if ( $info = $parserlib->plugin_info($name) ) {
				if (isset($info['icon']) and $info['icon'] != '') {
					$icon = $info['icon'];
				} else {
					$icon = 'img/icons/plugin.png';
				}

				$tag = new self;
				$tag->setLabel(str_ireplace('wikiplugin_', '', $info['name']))
					->setIcon($icon)
					->setWysiwygToken($info['name'])
					->setPluginName($name)
					->setType('Wikiplugin');

				return $tag;
			}
		}
	} // }}}

	function setPluginName( $name ) // {{{
	{
		$this->pluginName = $name;

		return $this;
	} // }}}

	function getPluginName() // {{{
	{
		return $this->pluginName;
	} // }}}

	function isAccessible() // {{{
	{
		global $tikilib;
		$parserlib = TikiLib::lib('parser');
		$dummy_output = '';
		return parent::isAccessible() && $parserlib->plugin_enabled($this->pluginName, $dummy_output);
	} // }}}

	private static function getToken( $name ) // {{{
	{
		switch($name) {
		case 'flash': return 'Flash';
		}
	} // }}}

	function getWikiHtml( $areaId ) // {{{
	{
		return $this->getSelfLink(
						'popup_plugin_form(\'' . $areaId . '\',\'' . $this->pluginName . '\')',
						htmlentities($this->label, ENT_QUOTES, 'UTF-8'),
						'qt-plugin'
		);
	} // }}}
	function getWysiwygToken( $areaId, $add_js = true ) // {{{
	{
		if (!empty($this->wysiwyg) && $add_js) {
			if ($this->wysiwyg === 'Image') {	// cke's own image tool overrides this so set it up to use our filegal
				global $headerlib,  $smarty, $prefs;
				// can't do upload the cke way yet
				//$smarty->loadPlugin('smarty_function_filegal_manager_url');
				//$url =  smarty_function_filegal_manager_url(array('area_id'=> 'fgal_picker'), $smarty);
				//$headerlib->add_js('CKEDITOR.config.filebrowserUploadUrl = "'.$url.'"', 5);
				$url = 'tiki-list_file_gallery.php?galleryId='.$prefs['home_file_gallery'].'&filegals_manager=fgal_picker';
				$headerlib->add_js('if (typeof window.CKEDITOR !== "undefined") {window.CKEDITOR.config.filebrowserBrowseUrl = "'.$url.'"}', 5);
			} else {
				$js = "popup_plugin_form('{$areaId}','{$this->pluginName}');";
				$this->setupCKEditorTool($js, $this->wysiwyg, $this->label, $this->icon);
			}
		}
		return $this->wysiwyg;
	} // }}}
	
	function getWysiwygWikiToken( $areaId, $add_js = true ) // {{{ // wysiwyg_htmltowiki
	{
		switch ($this->pluginName) {
			case 'img':
				$this->wysiwyg = '';	// don't use ckeditor's html image dialog
				break;
			default:
		}

		return $this->getWysiwygToken($areaId, $add_js);
	} // }}}

}

class ToolbarSheet extends Toolbar
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
			case 'sheetsave':
				$label = tra('Save Sheet');
				$icon = tra('img/icons/disk.png');
				$syntax = '
					$("#saveState").hide();
					$.sheet.saveSheet($.sheet.tikiSheet, function() {
						$.sheet.manageState($.sheet.tikiSheet, true);
					});';
      	break;
			case 'addrow':
				$label = tra('Add Row After Selection Or To End If No Selection');
				$icon = tra('img/icons/sheet_row_add.png');
				$syntax = 'sheetInstance.controlFactory.addRow();';	// add row after end to workaround bug in jquery.sheet.js 1.0.2
	      break;														// TODO fix properly for 5.1
			case 'addrowmulti':
				$label = tra('Add Multiple Rows After Selection Or To End If No Selection');
				$icon = tra('img/icons/sheet_row_add_multi.png');
				$syntax = 'sheetInstance.controlFactory.addRowMulti();';
	      break;
			case 'addrowbefore':
				$label = tra('Add Row Before Selection Or To End If No Selection');
				$icon = tra('img/icons/sheet_row_add.png');
				$syntax = 'sheetInstance.controlFactory.addRow(null, true);';	// add row after end to workaround bug in jquery.sheet.js 1.0.2
	      break;	
			case 'deleterow':
				$label = tra('Delete Selected Row');
				$icon = tra('img/icons/sheet_row_delete.png');
				$syntax = 'sheetInstance.deleteRow();';
	      break;
			case 'addcolumn':
				$label = tra('Add Column After Selection Or To End If No Selection');
				$icon = tra('img/icons/sheet_col_add.png');
				$syntax = 'sheetInstance.controlFactory.addColumn();';	// add col before current or at end if none selected
	      break;
			case 'deletecolumn':
				$label = tra('Delete Selected Column');
				$icon = tra('img/icons/sheet_col_delete.png');
				$syntax = 'sheetInstance.deleteColumn();';
	      break;
			case 'addcolumnmulti':
				$label = tra('Add Multiple Columns After Selection Or To End If No Selection');
				$icon = tra('img/icons/sheet_col_add_multi.png');
				$syntax = 'sheetInstance.controlFactory.addColumnMulti();';
	      break;
			case 'addcolumnbefore':
				$label = tra('Add Column Before Selection Or To End If No Selection');
				$icon = tra('img/icons/sheet_col_add.png');
				$syntax = 'sheetInstance.controlFactory.addColumn(null, true);';	// add col before current or at end if none selected
	      break;
			case 'sheetgetrange':
				$label = tra('Get Cell Range');
				$icon = tra('img/icons/sheet_get_range.png');
				$syntax = 'sheetInstance.getTdRange(null, sheetInstance.obj.formula().val()); return false;';
	      break;
			case 'sheetfind':
				$label = tra('Find');
				$icon = tra('img/icons/find.png');
				$syntax = 'sheetInstance.cellFind();';
	      break;
			case 'sheetrefresh':
				$label = tra('Refresh Calculations');
				$icon = tra('img/icons/arrow_refresh.png');
				$syntax = 'sheetInstance.calc();';
	      break;
			case 'sheetclose':
				$label = tra('Finish Editing');
				$icon = tra('img/icons/close.png');
				$syntax = '$.sheet.manageState(sheetInstance.obj.parent(), true);';	// temporary workaround TODO properly
	      break;
			case 'bold':
				$label = tra('Bold');
				$icon = tra('img/icons/text_bold.png');
				$wysiwyg = 'Bold';
				$syntax = 'sheetInstance.cellStyleToggle("styleBold");';
	      break;
			case 'italic':
				$label = tra('Italic');
				$icon = tra('img/icons/text_italic.png');
				$wysiwyg = 'Italic';
				$syntax = 'sheetInstance.cellStyleToggle("styleItalics");';
	      break;
			case 'underline':
				$label = tra('Underline');
				$icon = tra('img/icons/text_underline.png');
				$wysiwyg = 'Underline';
				$syntax = 'sheetInstance.cellStyleToggle("styleUnderline");';
	      break;
			case 'strike':
				$label = tra('Strikethrough');
				$icon = tra('img/icons/text_strikethrough.png');
				$wysiwyg = 'Strike';
				$syntax = 'sheetInstance.cellStyleToggle("styleLineThrough");';
	      break;
			case 'center':
				$label = tra('Align Center');
				$icon = tra('img/icons/text_align_center.png');
				$syntax = 'sheetInstance.cellStyleToggle("styleCenter");';
	      break;
			default:
				return;
		}

		$tag = new self;
		$tag->setLabel($label)
			->setIcon(!empty($icon) ? $icon : 'img/icons/shading.png')
			->setSyntax($syntax)
			->setType('Sheet');
		
		return $tag;
	} // }}}

	function getSyntax( $areaId ) // {{{
	{
		return $this->syntax;
	} // }}}
	
	protected function setSyntax( $syntax ) // {{{
	{
		$this->syntax = $syntax;

		return $this;
	} // }}}

	function getWikiHtml( $areaId ) // {{{
	{
		return $this->getSelfLink(
						addslashes(htmlentities($this->syntax, ENT_COMPAT, 'UTF-8')),
						htmlentities($this->label, ENT_QUOTES, 'UTF-8'),
						'qt-sheet'
		);

	} // }}}
	
}



class ToolbarsList
{
	private $lines = array();

	private function __construct()
	{
	}
	
	public static function fromPreference( $section, $tags_to_hide = array() ) // {{{
	{
		global $tikilib;

		$global = $tikilib->get_preference('toolbar_global' . (strpos($section, '_comments') !== false ? '_comments' : ''));
		$local = $tikilib->get_preference('toolbar_'.$section, $global);

		foreach ($tags_to_hide as $name) {
			$local = str_replace($name, '', $local);
		}
		if ($section === 'wysiwyg_plugin') {	// quick fix to prevent nested wysiwyg plugins (messy)
			$local = str_replace('wikiplugin_wysiwyg', '', $local);
		}

		$local = str_replace(array(',,', '|,', ',|', ',/', '/,'), array(',', '|', '|', '/', '/'), $local);

		return self::fromPreferenceString($local);
	} // }}}

	public static function fromPreferenceString( $string ) // {{{
	{
		global $toolbarPickerIndex;
		$toolbarPickerIndex = -1;
		$list = new self;

		$string = preg_replace('/\s+/', '', $string);

		foreach (explode('/', $string) as $line) {
			$bits = explode('|', $line);
			if (count($bits) > 1) {
				$list->addLine(explode(',', $bits[0]), explode(',', $bits[1]));
			} else {
				$list->addLine(explode(',', $bits[0]));
			}
		}

		return $list;
	} // }}}	

	public	function addTag ( $name, $unique = false )
	{
		if ( $unique && $this->contains($name) ) {
			return false;
		}
		$this->lines[count($this->lines)-1][0][0][] = Toolbar::getTag($name);
		return true;
	}

	public	function insertTag ( $name, $unique = false )
	{
		if ( $unique && $this->contains($name) ) {
			return false;
		}
		array_unshift($this->lines[0][0][0], Toolbar::getTag($name));	
		return true;
	}

	private function addLine( array $tags, array $rtags = array() ) // {{{
	{
		$elements = array();
		$j = count($rtags) > 0 ? 2 : 1;
		
		for ($i = 0; $i <  $j; $i++) {
			$group = array();
			$elements[$i] = array();
			
			if ($i == 0) {
				$thetags = $tags;
			} else {
				$thetags = $rtags;
			}
			foreach ( $thetags as $tagName ) {
				if ( $tagName == '-' ) {
					if ( count($group) ) {
						$elements[$i][] = $group;
						$group = array();
					}
				} else {
					if ( ( $tag = Toolbar::getTag($tagName) )
						&& $tag->isAccessible() ) {
	
						$group[] = $tag;
					}
				}
			}
	
			if ( count($group) ) {
				$elements[$i][] = $group;
			}
		}
		if ( count($elements) )
			$this->lines[] = $elements;
	} // }}}

	function getWysiwygArray( $areaId, $isHtml = true) // {{{
	{
		$lines = array();
		foreach ( $this->lines as $line ) {
			$lineOut = array();

			foreach ( $line as $bit ) {
				foreach ( $bit as $group) {
					$group_count = 0;
					foreach ( $group as $tag ) {
						if ($isHtml) {
							if ( $token = $tag->getWysiwygToken($areaId) ) {
								$lineOut[] = $token; $group_count++;
							}
						} else {
							if ( $token = $tag->getWysiwygWikiToken($areaId) ) {
								$lineOut[] = $token; $group_count++;
							}
						}
					}
					if ($group_count) { // don't add separators for empty groups
						$lineOut[] = '-';	
					}
				}
			}

			$lineOut = array_slice($lineOut, 0, -1);

			if ( count($lineOut) )
				$lines[] = array($lineOut);
		}

		return $lines;
	} // }}}

	function getWikiHtml( $areaId, $comments='' ) // {{{
	{
		global $tiki_p_admin, $tiki_p_admin_toolbars, $smarty, $section, $prefs, $headerlib;
		$html = '';

		// $.selection() is in jquery.autocomplete.min.js
		
		if ($prefs['feature_jquery_autocomplete'] != 'y') {
			$headerlib->add_jsfile('lib/jquery/jquery-autocomplete/jquery.autocomplete.min.js');
		}
		
		$c = 0;
		foreach ( $this->lines as $line ) {
			$lineHtml = '';
			$right = '';
			if (count($line) == 1) {
				$line[1] = array();
			}
			
			// $line[0] is left part, $line[1] right floated section
			for ($bitx = 0, $bitxcount_line = count($line); $bitx < $bitxcount_line; $bitx++ ) {
				$lineBit = '';
				
				if ($c == 0 && $bitx == 1 && ($tiki_p_admin == 'y' or $tiki_p_admin_toolbars == 'y')) {
					$params = array('_script' => 'tiki-admin_toolbars.php', '_onclick' => 'needToConfirm = true;', '_class' => 'toolbar', '_icon' => 'wrench', '_ajax' => 'n');
					if (isset($comments) && $comments == 'y')
						$params['comments'] = 'on';
					if (isset($section))
						$params['section'] = $section;
					$content = tra('Admin Toolbars');
					$right .= smarty_block_self_link($params, $content, $smarty);
				}
			
				foreach ( $line[$bitx] as $group ) {
					$groupHtml = '';
					foreach ( $group as $tag ) {
						$groupHtml .= $tag->getWikiHtml($areaId);
					}
					
					if ( !empty($groupHtml) ) {
						$param = empty($lineBit) ? '' : ' class="toolbar-list"';
						$lineBit .= "<span$param>$groupHtml</span>";
					}
					if ($bitx == 1) {
						if (!empty($right)) {
							$right = '<span class="toolbar-list">' . $right . '</span>';
						}
						$lineHtml = "<div class='helptool-admin'>$lineBit $right</div>" . $lineHtml;
					} else {
						$lineHtml = $lineBit;
					}
				}
				
				// adding admin icon if no right part - messy - TODO better
				if ($c == 0 && empty($lineBit) && !empty($right)) {
					$lineHtml .= "<div class='helptool-admin'>$right</div>";
				} 
			}
			if ( !empty($lineHtml) ) {
				$html .= "<div>$lineHtml</div>";
			}
			$c++;
		}

		return $html;
	} // }}}
	
	function contains($name)
	{ // {{{
		foreach ( $this->lines as $line ) {
			foreach ( $line as $group ) {
				foreach ( $group as $tags ) {
					foreach ($tags as $tag) {
						if ($tag->getLabel() == $name) {
							return true;
						}
					}
				}
			}
		}
		return false;
	} // }}}
}


/**
 * Definition of the CKE Toolbar Combos
 */
class ToolbarCombos
{
	
	/**
	 * Get the content of the format combo
	 * 
	 * Valid toolbar types are:
	 * - 'html': WYSIWYG-HTML
	 * - 'wiki': Visual Wiki 
	 * 
	 * @param string $tb_type The CKE toolbar type 
	 */
	static function getFormatTags($tb_type)
	{
		switch ($tb_type) {
			case 'wiki': return 'p;h1;h2;h3;h4;h5;h6';
      	break;
			case 'html': 
			default: return 'p;h1;h2;h3;h4;h5;h6;pre;address;div'; // CKE default
		}
	}
}

