<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_report.php 41934 2012-06-12 20:53:37Z robertplummer $

function wikiplugin_report_info()
{
	return array(
		'name' => tra('Report'),
		'documentation' => 'Report',
		'description' => tra('Build a report, and store it in a wiki page'),
		'prefs' => array( 'wikiplugin_report', 'feature_reports', 'feature_trackers' ),
		'body' => tra('The wiki syntax report settings'),
		'icon' => 'img/icons/mime/zip.png',
		'params' => array(
			'view' => array(
				'name' => tra('Report View'),
				'description' => tra('Report Plugin View'),
				'required' => true,
				'default' => 'sheet',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Sheet'), 'value' => 'sheet'), 
					array('text' => tra('Chart'), 'value' => 'chart')
				)
			),
			'name' => array(
				'name' => tra('Report Name'),
				'description' => tra('Report Plugin Name, sometimes used headings and reference'),
				'required' => true,
				'default' => 'Report Type',
			),
		),
	);
}

function wikiplugin_report( $data, $params )
{
	global $tikilib,$headerlib,$prefs,$page,$tiki_p_edit;
	static $reportI = 0;
	++$reportI;
	
	$params = array_merge(array("view"=> "sheet","name"=> ""), $params);
	
	extract($params, EXTR_SKIP);
	
	if (!empty($data)) {
		$result = "";
		$report = Report_Builder::loadFromWikiSyntax($data);
		$values = Report_Builder::fromWikiSyntax($data);
		$values = json_encode($values);
		$type = $report->type;
		
		switch($view) {
			case 'sheet':
				TikiLib::lib("sheet")->setup_jquery_sheet();
				
				$headerlib->add_jq_onready(
								"
					var me = $('#reportPlugin$reportI');
   				me
						.show()
						.visible(function() {
							me
								
								.sheet({
									editable: false,
									buildSheet: true
								});
						});"
				);
				
				$result .= "
					<style>
						#reportPlugin$reportI {
							display: none;
							width: inherit ! important;
						}
					</style>
					
					<div id='reportPlugin$reportI'>" 
						. $report->outputSheet($name) . 
					"</div>";
    			break;
				
		}
	}
	
	if ($tiki_p_edit == 'y') {
		$headerlib
			->add_jsfile("lib/core/Report/Builder.js")
			->add_js(
							"
			function editReport$reportI(me) {
				var me = $(me).removeAttr('href');
				me.serviceDialog({
					title: me.attr('title'),
					data: {
						controller: 'report',
						action: 'edit',
						index: $reportI
					},
					load: function() {
						$.reportInit();
						var values = $.parseJSON('$values');
						
						if (values) {
							$('#reportType')
								.val('$type')
								.change();
							
							values['type'] = null;
							
							$('#reportEditor').one('reportReady', function(){
								$('#reportEditor').reportBuilderImport(values);
							});
						}
					}
				});
				return false;
			}
		"
			);
		
		$result .= "
			<form class='reportWikiPlugin' data-index='$reportI' method='post' action='tiki-wikiplugin_edit.php'>
				<input type='hidden' name='page' value='$page'/>
				<input type='hidden' name='content' value=''/>
				<input type='hidden' name='index' value='$reportI'/>
				<input type='hidden' name='type' value='report' />
				<input type='hidden' name='params[name]' value='$name' />
				<input type='hidden' name='params[view]' value='$view' />
			</form>
			<span title='".tr('Edit Report')."' style='cursor: pointer;' onclick='return editReport$reportI(this);'>
				<img src='img/icons/page_edit.png' alt='$label' width='16' height='16' title='$label' class='icon' />
			</span>";
	}
	return "~np~" . $result . "~/np~"; 
}
