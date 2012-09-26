<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 42542 2012-08-06 18:48:41Z lphuberdeau $

class Services_File_Controller
{
	var $defaultGalleryId = 1;

	function setUp()
	{
		global $prefs;

		if ($prefs['feature_file_galleries'] != 'y') {
			throw new Services_Exception_Disabled('feature_file_galleries');
		}
	}

	function action_upload($input)
	{
		$gal_info = $this->checkTargetGallery($input);

		$size = $input->size->int();
		$name = $input->name->text();
		$type = $input->type->text();
		$data = $input->data->none();
		$fileId = $input->fileId->int();

		$data = base64_decode($data);

		$mimelib = TikiLib::lib('mime');
		$type = $mimelib->from_content($name, $data);

		if ($fileId) {
			$this->updateFile($gal_info, $name, $size, $type, $data, $fileId);
		} else {
			$fileId = $this->uploadFile($gal_info, $name, $size, $type, $data);
		}

		if ($fileId === false) {
			throw new Services_Exception(tr('File could not be uploaded. Restrictions apply.'), 406);
		}

		return array(
			'size' => $size,
			'name' => $name,
			'type' => $type,
			'fileId' => $fileId,
			'galleryId' => $galleryId,
			'md5sum' => md5($data),
		);
	}

	function action_remote($input)
	{
		global $prefs;
		if ($prefs['fgal_upload_from_source'] != 'y') {
			throw new Services_Exception(tr('Upload from source disabled.'), 403);
		}

		$gal_info = $this->checkTargetGallery($input);
		$url = $input->url->url();

		if (! $url) {
			return array(
				'galleryId' => $gal_info['galleryId'],
			);
		}

		$filegallib = TikiLib::lib('filegal');

		if ($file = $filegallib->lookup_source($url)) {
			return $file;
		}
		
		$info = $filegallib->get_info_from_url($url);

		if (! $info) {
			throw new Services_Exception(tr('Data could not be obtained.'), 412);
		}

		if ($input->reference->int()) {
			$info['data'] = 'REFERENCE';
		}

		$fileId = $this->uploadFile($gal_info, $info['name'], $info['size'], $info['type'], $info['data']);

		if ($fileId === false) {
			throw new Services_Exception(tr('File could not be uploaded. Restrictions apply.'), 406);
		}

		$filegallib->attach_file_source($fileId, $url, $info, $input->reference->int());

		return array(
			'size' => $info['size'],
			'name' => $info['name'],
			'type' => $info['type'],
			'fileId' => $fileId,
			'galleryId' => $gal_info['galleryId'],
			'md5sum' => md5($info['data']),
		);
	}

	function action_refresh($input)
	{
		global $prefs;
		if ($prefs['fgal_upload_from_source'] != 'y') {
			throw new Services_Exception(tr('Upload from source disabled.'), 403);
		}

		if ($prefs['fgal_source_show_refresh'] != 'y') {
			throw new Services_Exception(tr('Manual refresh disabled.'), 403);
		}

		$filegallib = TikiLib::lib('filegal');
		$ret = $filegallib->refresh_file($input->fileId->int());

		return array(
			'success' => $ret,
		);
	}

	private function checkTargetGallery($input)
	{
		$galleryId = $input->galleryId->int();

		if (empty($galleryId)) $galleryId = $this->defaultGalleryId;

		if (! $gal_info = $this->getGallery($galleryId)) {
			throw new Services_Exception(tr('Requested gallery does not exist.'), 404);
		}

		$perms = Perms::get('file gallery', $galleryId);
		if (! $perms->upload_files) {
			throw new Services_Exception(tr('Permission denied.'), 403);
		}

		return $gal_info;
	}

	private function getGallery($galleryId)
	{
		$filegallib = TikiLib::lib('filegal');
		return $filegallib->get_file_gallery_info($galleryId);
	}

	private function uploadFile($gal_info, $name, $size, $type, $data)
	{
		$filegallib = TikiLib::lib('filegal');
		return $filegallib->upload_single_file($gal_info, $name, $size, $type, $data);
	}

	private function updateFile($gal_info, $name, $size, $type, $data, $fileId)
	{
		$filegallib = TikiLib::lib('filegal');
		return $filegallib->update_single_file($gal_info, $name, $size, $type, $data, $fileId);
	}
}

