<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Geolocation.php 39469 2012-01-12 21:13:48Z changi67 $

class Search_GlobalSource_Geolocation implements Search_GlobalSource_Interface
{
	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$geolib = TikiLib::lib('geo');
		$coordinates = $geolib->get_coordinates_string($objectType, $objectId);
		$alreadyLocated = isset($data['geo_located']) && $date['geo_located'] == 'y';

		return array(
			'geo_located' => $typeFactory->identifier(($coordinates || $alreadyLocated) ? 'y' : 'n'),
			'geo_location' => $typeFactory->identifier($coordinates),
		);
	}

	function getProvidedFields()
	{
		return array(
			'geo_located',
			'geo_location',
		);
	}
	
	function getGlobalFields()
	{
		return array(
		);
	}
}

