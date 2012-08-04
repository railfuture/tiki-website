<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_file.php 41905 2012-06-11 16:51:02Z robertplummer $

define('WIKIPLUGIN_FILE_PAGE_LAST_MOD', 'PAGE_LAST_MOD');
define('WIKIPLUGIN_FILE_PAGE_VIEW_DATE', 'PAGE_VIEW_DATE');

function wikiplugin_file_info()
{
	global $prefs;
	$info = array(
		'name' => tra('File'),
		'documentation' => 'PluginFile',
		'description' => tra('Link to a file that\'s attached or in a file gallery or archive. See PluginFiles for more functionality.'),
		'prefs' => array( 'wikiplugin_file' ),
		'body' => tra('Label for the link to the file (ignored if the file is a wiki attachment)'),
		'icon' => 'img/icons/file-manager.png',
		'tags' => array( 'basic' ),		
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Type'),
				'description' => tra('Indicate whether the file is in a file gallery or is a wiki page attachment'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
				), //rest filled in below
			),
			'name' => array(
				'required' => true,
				'name' => tra('Name'),
				'description' => tra(
								'Identify an attachment by entering its file name, which will show as a link to the file.
								 If the page parameter is empty, it must be a file name of an attachment to the page where the plugin is used.'
				),
				'default' => '',
				'parent' => array('name' => 'type', 'value' => 'attachment'),
			),
 			'desc' => array(
				'required' => false,
				'name' => tra('Custom Description'),
				'description' => tra('Custom text that will be used for the link instead of the file name or file description'),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'advanced' => true,
				'default' => '',
			),
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('Name of the wiki page the file is attached to. Defaults to the wiki page where the plugin is used if empty.'),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'default' => '',
				'advanced' => true,
			),
			'showdesc' => array(
				'required' => false,
				'name' => tra('Attachment Description'),
				'description' => tra('Show the attachment description as the link label instead of the attachment file name.'),
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1),  
					array('text' => tra('No'), 'value' => 0),
				),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'default' => '',
				'advanced' => true,
			),
			'image' =>array(
				'required' => false,
				'name' => tra('Image'),
				'description' => tra('Indicates that this attachment is an image, and should be displayed inline using the img tag'),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'advanced' => true,
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
			),
			'fileId' => array(
				'required' => true,
				'name' => tra('File ID'),
				'description' => tra('File ID of a file in a file gallery or an archive.') . ' ' . tra('Example value:') . ' 42',
				'type' => 'fileId',
				'area' => 'fgal_picker_id',
				'filter' => 'digits',
				'default' => '',
				'parent' => array('name' => 'type', 'value' => 'gallery'),
			),
			'date' => array(
				'required' => false,
				'name' => tra('Date'),
				'description' => tra('For an archive file, the archive created just before this date will be linked to. Special values : PAGE_LAST_MOD and PAGE_VIEW_DATE.'),
				'parent' => array('name' => 'type', 'value' => 'gallery'),
				'default' => '',
				'advanced' => true,
			),
			'showicon' => array(
				'required' => false,
				'name' => tra('Show Icon'),
				'description' => tra('Show an icon version of the file or file type with the link to the file.'),
				'filter' => 'alpha',
				'parent' => array('name' => 'type', 'value' => 'gallery'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
				'advanced' => true,
			),
		)
	);
	if ($prefs['feature_file_galleries'] == 'y') {
		$info['params']['type']['options'][] = 	array('text' => tra('File Gallery File/Archive'), 'value' => 'gallery');
	}
	if ($prefs['feature_wiki_attachments'] == 'y') {
		$info['params']['type']['options'][] = 	array('text' => tra('Wiki Page Attachment'), 'value' => 'attachment');
	}
	return $info;
}

function wikiplugin_file( $data, $params )
{
	global $tikilib, $prefs, $info, $page_view_date;
	if (isset($params['fileId'])) {
		global $filegallib; include_once ('lib/filegals/filegallib.php');
		if ($prefs['feature_file_galleries'] != 'y') {
			return;
		}
		$fileId = $params['fileId'];
		if (isset($params['date'])) {
			static $wikipluginFileDate = 0;
			if (empty($params['date'])) {
				if (empty($wikipluginFileDate)) {
					return tra('The date has not been set');
				}
				$date = $wikipluginFileDate;
			} else {
				if (strcmp($params['date'], WIKIPLUGIN_FILE_PAGE_LAST_MOD) == 0) {
					// Page last modification date
					$date = $info['lastModif'];

				} else if (strcmp($params['date'], WIKIPLUGIN_FILE_PAGE_VIEW_DATE) == 0) {
					// Current date parameter
					$date = (isset($page_view_date)) ? $page_view_date : time();

				} else if (($date = strtotime($params['date'])) === false) {
					return tra('Incorrect date format');
				}
				$wikipluginFileDate = $date;
			}
			$fileId = $filegallib->getArchiveJustBefore($fileId, $date);
			if (empty($fileId)) {
				return tra('No such file');
			}
		} else {
			$info = $filegallib->get_file_info($fileId);
			if (empty($info)) {
				return tra('Incorrect parameter').' fileId';
			}
		}
			
		if (empty($data)) { // to avoid problem with parsing
			$data = empty($info['name'])?$info['filename']: $info['name'];
		}
		if (isset($params['showicon']) & $params['showicon'] == "y") {
			return "{img src=tiki-download_file.php?fileId=$fileId&amp;thumbnail=y&amp;x=16 link=tiki-download_file.php?fileId=$fileId} [tiki-download_file.php?fileId=$fileId|$data]";
		} else {
			return "[tiki-download_file.php?fileId=$fileId|$data]";
		}
	}

	if ($prefs['feature_wiki_attachments'] != 'y') {
		return "<span class='warn'>" . tra("Wiki attachments are disabled."). "</span>";
	}	
	$filedata = array();
	$filedata["name"] = '';
	$filedata["desc"] = '';
	$filedata["showdesc"] = '';
	$filedata["page"] = '';
	$filedata["image"] = '';

	$filedata = array_merge($filedata, $params);

	if ( ! $filedata["name"] ) {
		return;
	}

	$forward = array();
	$forward['file'] = $filedata['name'];
	$forward['inline'] = 1;
	$forward['page'] = $filedata['page'];
	if ($filedata['showdesc'])
		$forward['showdesc'] = 1;
	if ($filedata['image'])
		$forward['image'] = 1;
	$middle = $filedata["desc"];

	return TikiLib::lib('parser')->plugin_execute('attach', $middle, $forward);
}
