<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_include.php 40971 2012-04-17 19:58:15Z robertplummer $

function wikiplugin_include_info() 
{
	return array(
		'name' => tra('Include'),
		'documentation' => 'PluginInclude',
		'description' => tra('Include content from a wiki page'),
		'prefs' => array('wikiplugin_include'),
		'icon' => 'img/icons/page_copy.png',
		'tags' => array( 'basic' ),
		'params' => array(
			'page' => array(
				'required' => true,
				'name' => tra('Page Name'),
				'description' => tra('Wiki page name to include.'),
				'filter' => 'pagename',
				'default' => '',
			),
			'start' => array(
				'required' => false,
				'name' => tra('Start'),
				'description' => tra('When only a portion of the page should be included, specify the marker from which inclusion should start.'),
				'default' => '',
			),
			'stop' => array(
				'required' => false,
				'name' => tra('Stop'),
				'description' => tra('When only a portion of the page should be included, specify the marker at which inclusion should end.'),
				'default' => '',
			),
			'nopage_text' => array(
				'required' => false,
				'name' => tra('Nopage Text'),
				'description' => tra('Text to show when no page is found.'),
				'default' => '',
			),
			'pagedenied_text' => array(
				'required' => false,
				'name' => tra('Page Denied Text'),
				'description' => tra('Text to show when the page exists but is denied to the user.'),
				'default' => '',
			),
		),
	);
}

function wikiplugin_include($dataIn, $params, $offset)
{
	global $tikilib,$userlib,$user, $killtoc;
    static $included_pages, $data;

	$killtoc = true;
	$max_times = 5;
	$params = array_merge(array( 'nopage_text' => '', 'pagedenied_text' => '' ), $params);
	extract($params, EXTR_SKIP);
	if (!isset($page)) {
		return ("<b>missing page for plugin INCLUDE</b><br />");
	}
	$memo = $page;
	if (isset($start)) $memo .= "/$start";
	if (isset($end)) $memo .= "/$end";
    if ( isset($included_pages[$memo]) ) {
        if ( $included_pages[$memo]>=$max_times ) {
            return '';
        }
        $included_pages[$memo]++;
    } else {
        $included_pages[$memo] = 1;
        // only evaluate permission the first time round
        // evaluate if object or system permissions enables user to see the included page
    	$data[$memo] = $tikilib->get_page_info($page);
    	if (!$data[$memo]) {
    		$text = $nopage_text;
    	}
		$perms = $tikilib->get_perm_object($page, 'wiki page', $data[$memo], false);
        if ($perms['tiki_p_view'] != 'y') {
            $included_pages[$memo] = $max_times;
            $text = $pagedenied_text;
            return($text);
        }
    }

	if ($data[$memo]) {
		$text = $data[$memo]['data'];
		if (isset($start) || isset($stop)) {
			$explText = explode("\n", $text);
			if (isset($start) && isset($stop)) {
				$state = 0;
				foreach ($explText as $i => $line) {
					if ($state == 0) {
						// Searching for start marker, dropping lines until found
						unset($explText[$i]);	// Drop the line
						if (0 == strcmp($start, trim($line))) {
							$state = 1;	// Start retaining lines and searching for stop marker
						}
					} else {
						// Searching for stop marker, retaining lines until found
						if (0 == strcmp($stop, trim($line))) {
							unset($explText[$i]);	// Stop marker, drop the line
							$state = 0; 		// Go back to looking for start marker
						}
					}
				}
			} else if (isset($start)) {
				// Only start marker is set. Search for it, dropping all lines until
				// it is found.
				foreach ($explText as $i => $line) {
					unset($explText[$i]); // Drop the line
					if (0 == strcmp($start, trim($line))) {
						break;
					}
				}
			} else {
				// Only stop marker is set. Search for it, dropping all lines after
				// it is found.
				$state = 1;
				foreach ($explText as $i => $line) {
					if ($state == 0) {
						// Dropping lines
						unset($explText[$i]);
					} else {
						// Searching for stop marker, retaining lines until found
						if (0 == strcmp($stop, trim($line))) {
							unset($explText[$i]);	// Stop marker, drop the line
							$state = 0; 		// Start dropping lines
						}
					}
				}
			}	
			$text = implode("\n", $explText);
		}
	}
	
	$parserlib = TikiLib::lib('parser');
	$options = null;
	if (!empty($_REQUEST['page'])) {
		$options['page'] = $_REQUEST['page'];
	}
	$parserlib->parse_wiki_argvariable($text, $options);
	// append an edit button
	global $smarty;
	if (isset($perms) && $perms['tiki_p_edit'] === 'y') {
		global $smarty;
		$smarty->loadPlugin('smarty_block_ajax_href');
		$smarty->loadPlugin('smarty_function_icon');
		$tip = tra('Include Plugin'). ' | ' . tra('Edit the included page:').' &quot;' . $page . '&quot;';
		$text .= '<a class="editplugin tips" '.	// ironically smarty_block_self_link doesn't work for this! ;)
				smarty_block_ajax_href(array('template' => 'tiki-editpage.tpl'), 'tiki-editpage.php?page='.urlencode($page).'&returnto='.urlencode($GLOBALS['page']), $smarty, $tmp = false) . '>' .
				smarty_function_icon(array( '_id' => 'page_edit', 'title' => $tip, 'class' => 'icon tips'), $smarty) . '</a>';
	}
	return $text;
}
