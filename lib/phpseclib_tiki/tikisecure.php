<?php

// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tikisecure.php 40090 2012-03-08 19:30:00Z robertplummer $

class TikiSecure
{
	var $certName = "Tiki Secure Certificate";
	var $bits = 1024;
	var $type = "file";
	
	function __construct($certName = "", $bits = 0)
	{
		if (!empty($certName)) $this->certName = $certName;
		if ($bits > 0) $this->bits = $bits;
	}
	
	function typeFile()
	{
		$this->type = "file";
	}
	
	function typeFileGallery()
	{
		$this->type = "filegallery";
	}
	
	function encrypt($data = "")
	{
		$keys = $this->getKeys();
		
		$path = get_include_path();
		set_include_path("lib/phpseclib/");
		require_once('Crypt/RSA.php');
		$rsa = new Crypt_RSA();
		
		$rsa->loadKey($keys->privatekey);
		
		set_include_path($path);
		
		return $rsa->encrypt($data);
	}
	
	function decrypt($cipher)
	{
		if ($this->hasKeys() == false) return "";
		
		$keys = $this->getKeys();

		$rsa = new Crypt_RSA();
		
		$rsa->loadKey($keys->privatekey);
		$rsa->loadKey($keys->publickey);
		
		return $rsa->decrypt($cipher);
	}
	
	function hasKeys()
	{
		if ($this->type == "filegallery")
			return FileGallery_File::filename($this->certName)->exists();
		
		if ($this->type == "file") {
			return file_exists("temp/" . $this->certName);
		}
	}
	
	function getKeys()
	{
		//Get existing certificate if it exists
		if ($this->hasKeys()) {
			if ($this->type == "filegallery") {
				$keys = json_decode(FileGallery_File::filename($this->certName)->data());
			}
			
			if ($this->type == "file") {
				$keys = json_decode(file_get_contents("temp/" . $this->certName));
			}
		} else {
			$keys = $this->newKeys();
		}
		
		return $keys;
	}
	
	private function newKeys()
	{
		set_time_limit(30000);
		$path = get_include_path();
		set_include_path("lib/phpseclib/");
		require_once('Crypt/RSA.php');
		
		$rsa = new Crypt_RSA();
		$keys = $rsa->createKey($this->bits);
		
		set_include_path($path);
		
		if ($this->type == "filegallery") {
			FileGallery_File::filename($this->certName)
				->setParam("description", $this->certName)
				->replace(json_encode($keys));
		}
		
		if ($this->type == "file") {
			file_put_contents("temp/" . $this->certName, json_encode($keys));
		}
		
		return $keys;
	}
	
	static function timestamp($hash, $clientTime = "", $requester = "")
	{
		$me = new self($requester, 2048);
		$keys = $me->getKeys();
		
		return (object)array(
			"timestamp"=> 	urlencode($me->encrypt($hash . $clientTime . time())),
			"authority"=> 	TikiLib::tikiUrl(),
			"requester"=> 	$requester,
			"publickey"=> 	$keys->publickey,
			"href"=>		TikiLib::tikiUrl() . "tiki-tskeys.php"
		);
	}
	
	static function openTimestamp($timestamp = array(), $requester)
	{
		$me = new self($requester, 2048);
		$timestamp->timestamp = $me->decrypt($timestamp->timestamp);
		return $timestamp;
	}
}