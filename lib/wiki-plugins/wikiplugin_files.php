<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_files.php 42757 2012-08-25 19:13:48Z jonnybradley $

function wikiplugin_files_info()
{
	global $prefs;
	return array(
		'name' => tra('Files'),
		'documentation' => 'PluginFiles',
		'description' => tra('List files by ID, gallery or category, or show a slideshow of image files'),
		'introduced' => 7.0,
		'prefs' => array( 'feature_file_galleries', 'wikiplugin_files' ),
		'body' => tra('Title for the file listing, or the link text when slideshow = y'),
		'icon' => 'img/icons/large/file-manager.png',
		'tags' => array( 'basic' ),		
		'params' => array(
			'galleryId' => array(
				'required' => false,
				'name' => tra('File Galleries ID'),
				'description' => tra('To list only files contained in these file galleries (multiple IDs separated by colon)') .
									($prefs['feature_use_fgal_for_user_files'] === 'y' ? '.<br> ' . tra('Or enter a username for user files (hint: enter {{user}} for current logged in user).') : ''),
				'default' => '',
				'separator' => ':',
			),
			'categId' => array(
				'required' => false,
				'name' => tra('Category ID'),
				'description' => tra('To restrict files listed to those belonging to one or more categories. Enter a single category or ID or list of them separated by colon'),
				'default' => '',
				'advanced' => true,
			),
			'fileId' => array(
				'required' => false,
				'name' => tra('File ID'),
				'description' => tra('To list only specified files, enter their file IDs separated by colon'),
				'type' => 'fileId',
				'area' => 'fgal_picker_id',
				'default' => '',
				'separator' => ':',
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort Order'),
				'description' => tra('Order ascending or descending based on any field in the file gallery table. Default is name_asc'),
				'default' => 'name_asc',
				'filter' => 'text'
			),
			'showaction' => array(
				'required' => false,
				'name' => tra('Show Action'),
				'description' => tra('Show a column with icons for the various actions the user can take with each file (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showfind' => array(
				'required' => false,
				'name' => tra('Show Find'),
				'description' => tra('Show a search box above the list (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'description' => tra('Show the title of the file gallery (shown by default). Also shown on slide show pop up window if a single galleryId is used.'),
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showid' => array(
				'required' => false,
				'name' => tra('Show ID'),
				'description' => tra('Show the ID number of each file (shown by default)'),
				'default' => 'y',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showicon' => array(
				'required' => false,
				'name' => tra('Show Icon'),
				'description' => tra('Show an icon for each file depicting the file type'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showname' => array(
				'required' => false,
				'name' => tra('Show Name'),
				'description' => tra('Show the name given to the file upon upload into the file gallery (shown by default). Set to Yes (y) to show as a caption in a slide show.'),
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showfilename' => array(
				'required' => false,
				'name' => tra('Show Filename'),
				'description' => tra('Show each file\'s filename (shown by default except in slide show). Set to Yes (y) to show as a caption in a slide show.'),
				'filter' => 'alpha',
				'default' => 'y',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showsize' => array(
				'required' => false,
				'name' => tra('Show Size'),
				'description' => tra('Show the size of each file in kilobytes (shown by default except in slide show)'),
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showdescription' => array(
				'required' => false,
				'name' => tra('Show Description'),
				'description' => tra('Show the description of the file given upon upload into the file gallery (shown by default except in slide show). Set to Yes (y) to show as a caption in a slide show.'),
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showcreated' => array(
				'required' => false,
				'name' => tra('Show Creation Date'),
				'description' => tra('Show the date each file was created (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showmodified' => array(
				'required' => false,
				'name' => tra('Show Last Modification Date'),
				'description' => tra('Show the date each file was last modified (shown by default)'),
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showhits' => array(
				'required' => false,
				'name' => tra('Show Hits'),
				'description' => tra('Show the number of hits each file has received (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showlockedby' => array(
				'required' => false,
				'name' => tra('Show Locked By'),
				'description' => tra('For locked files, show the user name of the user who locked it (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showauthor' => array(
				'required' => false,
				'name' => tra('Show Author'),
				'description' => tra('Show the user name of the user who is the author of the file (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showcreator' => array(
				'required' => false,
				'name' => tra('Show Creator'),
				'description' => tra('Show the user name of the user who is the creator of the file (not shown by default)'),
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showgallery' => array(
				'required' => false,
				'name' => tra('Show Parent Gallery Name'),
				'description' => tra('Show the name of the parent gallery'),
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showfiles' => array(
				'required' => false,
				'name' => tra('Show File Count'),
				'description' => tra('For galleries included in the list (where the file gallery includes other galleries), show the number of files in each of those galleries (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'slideshow' => array(
				'required' => false,
				'name' => tra('Show Slideshow'),
				'description' => tra('Show a link that produces a pop-up slide show when clicked (not set by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'slidewidth' => array(
				'required' => false,
				'name' => tra('Slideshow Width'),
				'description' => tra('Set width of slideshow popup.'),
				'filter' => 'digits',
				'default' => 600,
				'since' => '9.0',
				'advanced' => true,
				'parent' => array('name' => 'slideshow', 'value' => 'y'),
			),
			'slideheight' => array(
				'required' => false,
				'name' => tra('Slideshow Height'),
				'description' => tra('Set height of slideshow popup.'),
				'filter' => 'digits',
				'default' => 500,
				'since' => '9.0',
				'advanced' => true,
				'parent' => array('name' => 'slideshow', 'value' => 'y'),
			),
			'showcomment' => array(
				'required' => false,
				'name' => tra('Show Comment'),
				'description' => tra('Show comments for each file (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
				'advanced' => true,
			),
			'showlasteditor' => array(
				'required' => false,
				'name' => tra('Show Last Editor'),
				'description' => tra('Show the user name of the user who last modified the file (shown by default)'),
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showthumb' => array(
				'required' => false,
				'name' => tra('Show Image Thumb'),
				'description' => tra('Show Image thumb'),
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'creator' => array(
				'required' => false,
				'name' => tra('Creator'),
				'description' => tra('Show only files created by this user'),
				'advanced' => true,
			),
			'showupload' => array(
				'required' => false,
				'name' => tra('Show Upload'),
				'description' => tra('Show a simple upload form to the gallery (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'max' => array(
				'required' => false,
				'name' => tra('Max'),
				'description' => tra('Number of rows (default: -1 = all)'),
				'default' => -1,
				'advanced' => true,
			),
			'recursive' => array(
				'required' => false,
				'name' => tra('Recursive'),
				'description' => tra('Recursive'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'withsubgals' => array(
				'required' => false,
				'name' => tra('With sub-galleries'),
				'description' => tra('With sub-galleries'),
				'filter' => 'alpha',
				'default' => 'y',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),	 	)
	 );
}
function wikiplugin_files($data, $params)
{
	global $prefs, $tikilib, $smarty, $tiki_p_admin, $tiki_p_admin_files_galleries, $user;
	if ($prefs['feature_file_galleries'] != 'y') {
		return('');
	}
	global $filegallib; include_once('lib/filegals/filegallib.php');
	$default = array('showfind'=>'n', 'showtitle'=>'y', 'showupload' => 'n', 'showgallery' => 'n', 'max' => -1, 'showthumb' => 'n', 'recursive' => 'n', 'withsubgals'=>'y');
	$params = array_merge($default, $params);
	$filter = '';
	extract($params, EXTR_SKIP);

	if ($prefs['feature_categories'] != 'y') {
		if (isset($categId))
			unset($categId);
	} else {
		global $categlib; include_once('lib/categories/categlib.php');
	}

	$files = array();
	if (isset($categId) && strstr($categId, ':')) {
		$categId = explode(':', $categId);
	}
	static $iplugin = 0;
	++$iplugin;
	if (isset($_REQUEST["wp_files_sort_mode$iplugin"])) {
		$sort = $_REQUEST["wp_files_sort_mode$iplugin"];
	}
	if (empty($showgallery)) {
		$show_parentName = empty($galleryId)? 'y': 'n';
	} else {
		$show_parentName = $showgallery;
	}
	$smarty->assign('show_parentName', $show_parentName);
	$smarty->assign('show_thumb', $showthumb);

	$filter = empty($creator)?'':array('creator'=>$creator);
	if (!isset($sort))
		$sort = 'name_asc';
	if (isset($galleryId)) {
		$galId = $galleryId[0];
		if ($prefs['feature_use_fgal_for_user_files'] === 'y' && !is_numeric($galId)) {	// if not number could be a userfiles gallery
			$galId = $filegallib->get_user_file_gallery($galId);
			$galleryId = array($galId);
		}
		$gal_info = $filegallib->get_file_gallery($galId);

		if ($tiki_p_admin != 'y' && $tiki_p_admin_files_galleries != 'y' && $gal_info['user'] != $user) {
			$p_view_file_gallery = $tikilib->user_has_perm_on_object($user, $galId, 'file gallery', 'tiki_p_view_file_gallery') ? 'y' : 'n';
			if ($p_view_file_gallery != 'y')
				return;
			$p_download_files = $tikilib->user_has_perm_on_object($user, $gal, 'file gallery', 'tiki_p_download_files') ? 'y' : 'n';
			if ($showupload == 'y' && $tikilib->user_has_perm_on_object($user, $galId, 'file gallery', 'tiki_p_upload_files')) {
				$params['showupload'] = 'y';
			}
			$p_admin_file_galleries = $tikilib->user_has_perm_on_object($user, $galId, 'file gallery', 'tiki_p_admin_file_galleries') ? 'y' : 'n';
			$p_edit_gallery_file = $tikilib->user_has_perm_on_object($user, $galId, 'file gallery', 'tiki_p_edit_gallery_file') ? 'y' : 'n';
		} else {
			$p_download_files = 'y';
			$p_view_file_gallery = 'y';
			$p_admin_file_galleries = 'y';
			$p_edit_gallery_file = 'y';
			if ($showupload == 'y') {
				$params['showupload'] = 'y';
			}
		}
		if (!empty($slideshow) && $slideshow == 'y') {
			if ($prefs['javascript_enabled'] != 'y') return;
			if (empty($data)) $data = tra('Slideshow');
			// set caption field indicator for images in slide show
			if (isset($showdescription) && $showdescription == 'y') {
				$caption = 'd';
			} elseif (isset($showname) && $showname == 'y') {
				$caption = 'n';
			} elseif (isset($showfilename) && $showfilename == 'y') {
				$caption = 'f';
			} else {
				$caption = false;
			}
			// set title indicator for slideshow popup window
			// only shows a title if a single galleryId is used
			$windowtitle = "&amp;windowtitle=";
			$windowtitle .= $showtitle == 'n' || empty($galleryId) || count($galleryId) > 1 ? 'none': urlencode($gal_info['name']);
			
			$creatorparam = empty($creator) ? '': "&amp;find_creator=" . urlencode($creator);
			return "~np~<a onclick=\"javascript:window.open('tiki-list_file_gallery.php?galleryId=$galleryId[0]&amp;sort_mode=" . $sort . "&amp;caption=" . $caption . $creatorparam . $windowtitle . "&amp;slideshow','','menubar=no,width=" . $slidewidth . ",height=" . $slideheight . ",resizable=yes'); return false\" href=\"#\">".tra($data).'</a>~/np~';
		}
		$find = isset($_REQUEST['find'])?  $_REQUEST['find']: '';
		$fs = $filegallib->get_files(0, $max, $sort, $find, $galleryId, false, $withsubgals=='y', false, true, false, $show_parentName=='y', true, $recursive, '', false, false, false, $filter);
		if (isset($categId)) {
			$objects = $categlib->list_category_objects($categId, 0, -1, 'itemId_asc', 'file');
			$objects_in_categs = array();
			foreach ($objects['data'] as $o) {
				$objects_in_categs[] = $o['itemId'];
			}
		}
		for ($i = 0, $count_fs_data = count($fs['data']); $i < $count_fs_data; ++$i) {
			if (isset($categId)) { // filter the files
				if (!in_array($fs['data'][$i]['fileId'], $objects_in_categs)) {
					continue;
				}
			}
			$fs['data'][$i]['p_download_files'] = $p_download_files;
			$fs['data'][$i]['p_view_file_gallery'] = $p_view_file_gallery;
			$fs['data'][$i]['p_admin_file_galleries'] = $p_admin_file_galleries;
			$fs['data'][$i]['p_edit_gallery_file'] = $p_edit_gallery_file;
			$fs['data'][$i]['galleryType'] = $gal_info['type'];
			$fs['data'][$i]['lockable'] = $gal_info['lockable'];
			$files[] = $fs['data'][$i];
		}
	} elseif (isset($categId)) {
		// galls of this category
		$objects = $categlib->list_category_objects($categId, 0, -1, 'itemId_asc', 'file gallery');
		// get the files of the gallery
		foreach ($objects['data'] as $og) {
			$gal_info = $filegallib->get_file_gallery($og['itemId']);
			if ($tiki_p_admin != 'y' && $tiki_p_admin_files_galleries != 'y' && $gal_info['user'] != $user) {
				$p_view_file_gallery = $tikilib->user_has_perm_on_object($user, $gal_info['galleryId'], 'file gallery', 'tiki_p_view_file_gallery') ? 'y' : 'n';
				if ($p_view_file_gallery != 'y')
					continue;
				$p_download_files = $tikilib->user_has_perm_on_object($user, $gal_info['galleryId'], 'file gallery', 'tiki_p_download_files') ? 'y' : 'n';
				$p_admin_file_galleries = $tikilib->user_has_perm_on_object($user, $gal_info['galleryId'], 'file gallery', 'tiki_p_admin_file_galleries') ? 'y' : 'n';
				$p_edit_gallery_file = $tikilib->user_has_perm_on_object($user, $gal_info['galleryId'], 'file gallery', 'tiki_p_edit_gallery_file') ? 'y' : 'n';
			} else {
				$p_download_files = 'y';
				$p_view_file_gallery = 'y';
				$p_admin_file_galleries = 'y';
				$p_edit_gallery_file = 'y';
			}

			$fs = $filegallib->get_files(0, $max, $sort, '', $og['itemId'], false, $withsubgals=='y', false, true, false, $show_parentName=='y', true, $recursive, '', false, false, false, $filter);			                                                      
			if ($fs['cant']) {
				for ($i = 0, $count_fs_data = count($fs['data']); $i < $count_fs_data; ++$i) {
					$fs['data'][$i]['gallery'] = $gal_info['name'];
					$fs['data'][$i]['galleryId'] = $gal_info['galleryId'];
					$fs['data'][$i]['p_download_files'] = $p_download_files;
					$fs['data'][$i]['p_view_file_gallery'] = $p_view_file_gallery;
					$fs['data'][$i]['p_admin_file_galleries'] = $p_admin_file_galleries;
					$fs['data'][$i]['galleryType'] = $gal_info['type'];
					$fs['data'][$i]['lockable'] = $gal_info['lockable'];
					$fs['data'][$i]['p_edit_gallery_file'] = $p_edit_gallery_file;
				}
				$files = array_merge($files, $fs['data']);
			}
		}
		// files from this categ
		$objects = $categlib->list_category_objects($categId, 0, -1, 'itemId_asc', 'file');
		foreach ($objects['data'] as $of) {
			if ($info = wikiplugin_files_check_perm_file($of['itemId'])) {
				$files[] = $info;
			}
		}
		$gal_info = $filegallib->default_file_gallery();
	} elseif (isset($fileId)) {
		foreach ($fileId as $id) {
			if ($info = wikiplugin_files_check_perm_file($id)) {
				$files[] = $info;
			}
		}
		$gal_info = $filegallib->default_file_gallery();
	}
	$smarty->assign_by_ref('files', $files);
	if (isset($data))
		$smarty->assign_by_ref('data', $data);
	else
		$smarty->assign('data', '');
	include_once('fgal_listing_conf.php');
	$gal_info['show_checked' ] = 'n'; // the multiple action will not work
	if (!empty($showid)) $gal_info['show_id'] = $showid;
	if (!empty($showicon)) $gal_info['show_icon'] = $showicon;
	if (!empty($showsize)) $gal_info['show_size'] = $showsize;
	if (!empty($showdescription)) $gal_info['show_description'] = $showdescription;
	if (!empty($showcreated)) $gal_info['show_created'] = $showcreated;
	if (!empty($showcreator)) $gal_info['show_creator'] = $showcreator;
	if (!empty($showauthor)) $gal_info['show_author'] = $showauthor;
	if (!empty($showmodified)) {
		$gal_info['show_lastmodif'] = $gal_info['show_modified'] = $showmodified;
	}
	if (!empty($showlockedby)) $gal_info['show_lockedby'] = $showlockedby;
	if (!empty($showhits)) $gal_info['show_hits'] = $showhits;
	if (!empty($showfiles)) $gal_info['show_files'] = $showfiles;
	if (!empty($showdeleteafter)) $gal_info['show_deleteAfter'] = $showdeleteafter;
	if (!empty($showchecked)) $gal_info['show_checked'] = $showchecked;
	if (!empty($showshare)) $gal_info['show_share'] = $showshare;
	if (!empty($showaction)) $gal_info['show_action'] = $showaction;
	if (!empty($showcomment)) $gal_info['show_comment'] = $showcomment;
	if (!empty($showlasteditor)) $gal_info['show_last_user'] = $showlasteditor;
	if (!empty($showname) && $showname == 'y' && !empty($showfilename) && $showfilename == 'y') $gal_info['show_name'] = 'a';
	if (!empty($showname) && $showname == 'y' && !empty($showfilename) && $showfilename == 'n') $gal_info['show_name'] = 'n';
	if (!empty($showname) && $showname == 'n' && !empty($showfilename) && $showfilename == 'y') $gal_info['show_name'] = 'f';
	if (!empty($showname) && $showname == 'n' && !empty($showfilename) && $showfilename == 'n') $gal_info['show_name'] = '';
	$smarty->assign_by_ref('gal_info', $gal_info);

	if (isset($categId)) {
		if (is_array($categId)) {
			foreach ($categId as $cat) {
				$category[] = $categlib->get_category_name($cat);
			}
		} else {
			$category = $categlib->get_category_name($categId);
		}
		$smarty->assign_by_ref('category', $category);
	} else
		$smarty->assign('category', '');
	$smarty->assign_by_ref('params', $params);
	$smarty->assign('sort_arg', "wp_files_sort_mode$iplugin");
	return '~np~'.$smarty->fetch('wiki-plugins/wikiplugin_files.tpl').'~/np~';
}
function  wikiplugin_files_check_perm_file($fileId)
{
		global $filegallib, $tikilib, $tiki_p_admin, $user, $tiki_p_admin_files_galleries;
		$info = $filegallib->get_file_info($fileId);
		$gal_info = $filegallib->get_file_gallery($info['galleryId']);
		if ($tiki_p_admin != 'y' && $tiki_p_admin_files_galleries != 'y' && $gal_info['user'] != $user) {
			$info['p_view_file_gallery'] = $tikilib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_view_file_gallery') ? 'y' : 'n';
			if ($info['p_view_file_gallery'] != 'y') {
				return false;
			}
			$info['p_download_files'] = $tikilib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_download_files') ? 'y' : 'n';
			$info['p_admin_file_galleries'] = $tikilib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_admin_file_galleries') ? 'y' : 'n';
			$info['p_edit_gallery_file'] = $tikilib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_edit_gallery_file') ? 'y' : 'n';
		} else {
			$info['p_download_files'] = 'y';
			$info['p_view_file_gallery'] = 'y';
			$info['p_admin_file_galleries'] = 'y';
			$info['p_edit_gallery_file'] = 'y';
		}
		$info['gallery'] = $gal_info['name'];
		$info['galleryType'] = $gal_info['type'];
		$info['lockable'] = $gal_info['lockable'];
		$info['id'] = $info['fileId'];
		$info['parentName'] = $gal_info['name'];
		$info['size'] = $info['filesize'];
		return $info;
}
