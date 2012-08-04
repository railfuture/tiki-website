<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-auto_save.php 41386 2012-05-07 19:21:58Z jonnybradley $

// Called by CKEDITOR.config.ajaxAutoSaveTargetUrl defined in block.textarea.php

// Used by ckeditor tikiwiki plugin and to reparse plugins in html mode
// possibly should be renamed?

$inputConfiguration = array( array(
	'staticKeyFilters' => array(
		'editor_id' => 'alpha',
//		'data' => 'alpha',
//		'script' => 'alpha',
	),
) );

require_once('tiki-setup.php');

if ($prefs['feature_ajax'] != 'y' || ($prefs['ajax_autosave'] != 'y')) {
	return;
}

require_once('lib/ajax/autosave.php');

function send_ajax_response($command, $data ) 
{
	header('Content-Type:text/xml; charset=UTF-8');
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<adapter command="' . $command . '">';
	echo '<data><![CDATA[' .  rawurlencode($data) . ']]></data>';
	echo '</adapter>';
	exit;
}

if (isset($_REQUEST['editor_id'])) {
	if (isset($_REQUEST['command']) && isset($_REQUEST['data']) && $_REQUEST['data'] != 'ajax error') {
		if (!isset($_REQUEST['referer']))
			$_REQUEST['referer'] =  '';
		$referer = explode(':', $_REQUEST['referer']);	// user, section, object id
		if ($referer && count($referer) === 3 && $referer[1] === 'wiki_page') {
			$page = rawurldecode($referer[2]);	// plugins use global $page for approval
		}
		
		if ($_REQUEST['command'] == 'toWikiFormat') {
			global $editlib; include_once 'lib/wiki/editlib.php';
			$res = $editlib->parseToWiki(urldecode($_REQUEST['data']));
		} else if ($_REQUEST['command'] == 'toHtmlFormat') {
			global $editlib; include_once 'lib/wiki/editlib.php';
			$res = $editlib->parseToWysiwyg(urldecode($_REQUEST['data']), false, !empty($_REQUEST['allowhtml']) ? $_REQUEST['allowhtml'] : false);
		} else if ($_REQUEST['command'] == 'auto_save') {
			include_once 'lib/ajax/autosave.php';
			$data = $_REQUEST['data'];
			$res = auto_save($_REQUEST['editor_id'], $data, $_REQUEST['referer']);
		} else if ($_REQUEST['command'] == 'auto_remove') {
			include_once 'lib/ajax/autosave.php';
			remove_save($_REQUEST['editor_id'], $_REQUEST['referer']);
		} else if ($_REQUEST['command'] == 'auto_get') {
			include_once 'lib/ajax/autosave.php';
			$res = get_autosave($_REQUEST['editor_id'], $_REQUEST['referer']);
		}
		send_ajax_response($_REQUEST['command'], $res);
	} else if (isset($_REQUEST['autoSaveId'])) {	// wiki page previews
		
		$autoSaveIdParts = explode(':', $_REQUEST['autoSaveId']);	// user, section, object id
		foreach ($autoSaveIdParts as & $part) {
			$part = urldecode($part);
		}
		
		$page = $autoSaveIdParts[2];	// plugins use global $page for approval
		$info = $tikilib->get_page_info($page, false);
		if (isset($_REQUEST['allowHtml']) || empty($info)) {
			$info['is_html'] = !empty($_REQUEST['allowHtml'])? 1 : 0;
		}
		if (!isset($info['wysiwyg'])) {
			$info['wysiwyg'] = $_SESSION['wysiwyg'];
		}
		$options = array('is_html' => ($info['is_html'] == 1), 'preview_mode' => true, 'process_wiki_paragraphs' => ($prefs['wysiwyg_htmltowiki'] === 'y' || $info['wysiwyg'] == 'n'), 'page' => $autoSaveIdParts[2]);

		if (count($autoSaveIdParts) === 3 && !empty($user) && $user === $autoSaveIdParts[0] && $autoSaveIdParts[1] === 'wiki_page') {
			
			$editlib; include_once 'lib/wiki/editlib.php';
			if (isset($_REQUEST['inPage'])) {
				if (!isset($_REQUEST['diff_style'])) {	// use previously set diff_style
					$_REQUEST['diff_style'] = isset($_COOKIE['preview_diff_style']) ? $_COOKIE['preview_diff_style'] : '';
				}
				$data = $editlib->partialParseWysiwygToWiki(get_autosave($_REQUEST['editor_id'], $_REQUEST['autoSaveId']));
				$smarty->assign('diff_style', $_REQUEST['diff_style']);
				global $tikilib;
				if (!empty($_REQUEST['diff_style'])) {
					$info = $tikilib->get_page_info($autoSaveIdParts[2]);
					if (!empty($info)) {
						if (!empty($_REQUEST['hdr'])) {		// TODO refactor with code in editpage
							if ($_REQUEST['hdr'] === 0) {
								list($real_start, $real_len) = $tikilib->get_wiki_section($info['data'], 1);
								$real_len = $real_start;
								$real_start = 0;
							} else {
								list($real_start, $real_len) = $tikilib->get_wiki_section($info['data'], $_REQUEST['hdr']);
							}
							$info['data'] = substr($info['data'], $real_start, $real_len);
						}
						require_once('lib/diff/difflib.php');
						if ($info['is_html'] == 1) {
							$diffold = $tikilib->htmldecode($info['data']);
						} else {
							$diffold = $info['data'];
						}
						$_REQUEST['allowHtml'] = isset($_REQUEST['allowHtml']) ? $_REQUEST['allowHtml'] : $info['is_html'];
						if ($_REQUEST['allowHtml']) {
							$diffnew = $tikilib->htmldecode($data);
						} else {
							$diffnew = $data;
						}
						$data = diff2($diffold, $diffnew, $_REQUEST["diff_style"]);
						$smarty->assign_by_ref('diffdata', $data);
						
						$smarty->assign('translation_mode', 'y');
						$data = $smarty->fetch('pagehistory.tpl');
					}
				} else {
					$data = $tikilib->parse_data($data, $options);
				}
				echo $data;
				
			} else {					// popup window
				$headerlib->add_js(
								'function get_new_preview() {
		$("body").css("opacity", 0.6);
		location.replace("' . $tikiroot . 'tiki-auto_save.php?editor_id=' . $_REQUEST['editor_id'] . '&autoSaveId=' . $_REQUEST['autoSaveId'] . '");
	}
	$(window).load(function(){
		if (typeof opener != "undefined") {
			opener.ajaxPreviewWindow = this;
		}
	}).unload(function(){
	if (typeof opener.ajaxPreviewWindow != "undefined") {
		opener.ajaxPreviewWindow = null;
	}
});'
				);
				$smarty->assign('headtitle', tra('Preview'));
				$data = '<div id="c1c2"><div id="wrapper"><div id="col1"><div id="tiki-center" class="wikitext">';
				if (has_autosave($_REQUEST['editor_id'], $_REQUEST['autoSaveId'])) {
					$parserlib = TikiLib::lib('parser');
					$data .= $parserlib->parse_data($editlib->partialParseWysiwygToWiki(get_autosave($_REQUEST['editor_id'], $_REQUEST['autoSaveId'])), $options);
				} else {
					if ($autoSaveIdParts[1] == 'wiki_page') {
						global $wikilib; include_once('lib/wiki/wikilib.php');
						$canBeRefreshed = false;
						$data .= $wikilib->get_parse($autoSaveIdParts[2], $canBeRefreshed);
					}
				}
				$data .= '</div></div></div></div>';
				$smarty->assign_by_ref('mid_data', $data);
				$smarty->assign('mid', '');
				$smarty->display("tiki_full.tpl");
			}
		}	// end wiki preview
	}
}
