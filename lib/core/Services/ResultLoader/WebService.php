<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: WebService.php 40234 2012-03-17 19:17:41Z changi67 $

class Services_ResultLoader_WebService
{
	private $client;
	private $offsetKey;
	private $countKey;
	private $resultKey;

	function __construct($client, $offsetKey, $countKey, $resultKey)
	{
		$this->client = $client;
		$this->offsetKey = $offsetKey;
		$this->countKey = $countKey;
		$this->resultKey = $resultKey;
	}

	function __invoke($offset, $count)
	{
		$this->client->setParameterPost(
						array(
							$this->offsetKey => $offset,
							$this->countKey => $count,
						)
		);
		$this->client->setHeaders('Accept', 'application/json');

		$response = $this->client->request('POST');

		if (! $response->isSuccessful()) {
			throw new Services_Exception(tr('Remote service unaccessible (%0)', $response->getStatus()), 400);
		}

		$out = json_decode($response->getBody(), true);
		return $out[$this->resultKey];
	}
}

