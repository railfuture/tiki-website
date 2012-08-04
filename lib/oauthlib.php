<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: oauthlib.php 40097 2012-03-09 21:55:13Z pkdille $

class OAuthLib extends TikiDb_Bridge
{
	function is_authorized($provider_key)
	{
		return ! is_null($this->retrieve_token($provider_key));
	}

	function do_request($provider_key, $arguments)
	{
		$configuration = $this->get_configuration($provider_key);

		if (! $configuration) {
			return false;
		}

		$access = $this->retrieve_token($provider_key);

		if ($access) {
			$client = $access->getHttpClient($configuration);

			if (isset($configuration['secretAsGet'])) {
				$client->setParameterGet($configuration['secretAsGet'], $access->getTokenSecret());
			}
		} else {
			$client = TikiLib::lib('tiki')->get_http_client();
		}

		$client->setUri($arguments['url']);

		if (isset($arguments['post'])) {
			$client->setMethod(Zend_Http_Client::POST);
			foreach ($arguments['post'] as $key => $value) {
				$client->setParameterPost($key, $value);
			}
		}

		if (isset($arguments['get'])) {
			foreach ($arguments['get'] as $key => $value) {
				$client->setParameterGet($key, $value);
			}
		}

		try {
			return $client->request();
		} catch (Zend_Http_Exception $e) {
			return null;
		}
	}

	function request_token($provider_key)
	{
		try {
			$consumer = $this->get_consumer($provider_key);

			if ($consumer) {
				$_SESSION['OAUTH_REQUEST_' . $provider_key] = serialize($consumer->getRequestToken());
				$consumer->redirect();
			}
		} catch (Zend_Oauth_Exception $e) {
			$oauth_ex = $e->getPrevious();
			$prevErr = '';
			if ($oauth_ex != null)
				$prevErr = $oauth_ex->getMessage();
			die($e->getMessage() . '. Origin: ' . $prevErr);
		}
	}

	function request_access($provider_key)
	{
		$consumer = $this->get_consumer($provider_key);
		$key = 'OAUTH_REQUEST_' . $provider_key;

		if ($consumer && isset($_SESSION[$key])) {
			try {
				$accessToken = $consumer->getAccessToken($_GET, unserialize($_SESSION[$key]));

				$this->store_token($provider_key, $accessToken);

				unset($_SESSION[$key]);
			} catch (Zend_Oauth_Exception $e) {
				$oauth_ex = $e->getPrevious();
				$prevErr = '';
				if ($oauth_ex != null)
					$prevErr = $oauth_ex->getMessage();
				die($e->getMessage() . '. Origin: ' . $prevErr);
			}
		}
	}

	private function store_token($provider_key, $accessToken)
	{
		$tikilib = TikiLib::lib('tiki');

		$tikilib->set_preference('oauth_token_' . $provider_key, serialize($accessToken));
	}

	private function retrieve_token($provider_key)
	{
		$tikilib = TikiLib::lib('tiki');

		$token = $tikilib->get_preference('oauth_token_' . $provider_key);

		return $token ? unserialize($token) : null;
	}

	private function get_configuration($provider_key)
	{
		global $prefs;
		$tikilib = TikiLib::lib('tiki');

		switch ($provider_key) {
		case 'zotero':
			return array(
				'callbackUrl' => $tikilib->tikiUrl(
								'tiki-ajax_services.php',
								array('oauth_callback' => $provider_key,)
				),
				'siteUrl' => 'https://www.zotero.org/oauth',
				'requestTokenUrl' => 'https://www.zotero.org/oauth/request',
				'accessTokenUrl' => 'https://www.zotero.org/oauth/access',
				'authorizeUrl' => 'https://www.zotero.org/oauth/authorize',
				'consumerKey' => $prefs['zotero_client_key'],
				'consumerSecret' => $prefs['zotero_client_secret'],
				'secretAsGet' => 'key', // Tiki-specific
			);
		}
	}

	private function get_consumer($provider_key)
	{
		if ($configuration = $this->get_configuration($provider_key)) {
			return new Zend_Oauth_Consumer($configuration);
		}
	}
}

