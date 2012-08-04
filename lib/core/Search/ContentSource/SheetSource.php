<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: SheetSource.php 40234 2012-03-17 19:17:41Z changi67 $

class Search_ContentSource_SheetSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getDocuments()
	{
		return $this->db->table('tiki_sheets')->fetchColumn('sheetId', array());
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$sheetlib = TikiLib::lib('sheet');

		$info = $sheetlib->get_sheet_info($objectId);

		$values = $this->db->table('tiki_sheet_values');
		$contributors = $values->fetchColumn(
						$values->expr('DISTINCT `user`'), 
						array(
							'sheetId' => $objectId,
						)
		);
		$lastModif = $values->fetchOne(
						$values->max('begin'), 
						array(
							'sheetId' => $objectId,
						)
		);

		$loader = new TikiSheetDatabaseHandler($objectId);
		$writer = new TikiSheetCSVHandler('php://output');

		$grid = new TikiSheet;
		$grid->import($loader);

		ob_start();
		$grid->export($writer);
		$text = ob_get_contents();
		ob_end_clean();
		
		$data = array(
			'title' => $typeFactory->sortable($info['title']),
			'description' => $typeFactory->sortable($info['description']),
			'modification_date' => $typeFactory->timestamp($lastModif),
			'contributors' => $typeFactory->multivalue($contributors),

			'sheet_content' => $typeFactory->plaintext($text),

			'view_permission' => $typeFactory->identifier('tiki_p_view_sheet'),
		);

		return $data;
	}

	function getProvidedFields()
	{
		return array(
			'title',
			'description',
			'modification_date',
			'contributors',

			'sheet_content',

			'view_permission',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,
			'description' => true,

			'sheet_content' => false,
		);
	}
}

