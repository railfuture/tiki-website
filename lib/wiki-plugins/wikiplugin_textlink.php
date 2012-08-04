<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_textlink.php 41156 2012-04-29 00:24:02Z robertplummer $

function wikiplugin_textlink_info()
{
	return array(
		'name' => tra('TextLink'),
		'documentation' => 'PluginTextlink',
		'description' => tra('Links your article to a site using forwardlink protocol'),
        'keywords' => array('forward', 'forwardlink', 'forwardlink-protocol', 'forwardlinkprotocol', 'protocol'),
		'prefs' => array( 'feature_wiki', 'wikiplugin_textlink', 'feature_forwardlinkprotocol' ),
		'icon' => 'img/icons/link.png',
		'body' => tra('Text to link to forwardlink'),
		'params' => array(			
			'clipboarddata' => array(
				'required' => true,
				'name' => tra('ClipboardData'),
				'default' => false
			),
		),
	);
}

function wikiplugin_textlink($data, $params)
{
    global $tikilib, $headerlib, $caching, $page;
    static $textlinkI = 0;
	++$textlinkI;
	$i = $textlinkI;
	
	$params = array_merge(array("clipboarddata" => ""), $params);
	extract($params, EXTR_SKIP);
	
	$clipboarddata = json_decode(stripslashes(trim(urldecode($clipboarddata))));
	if (empty($clipboarddata)) return $data;
	
	$clipboarddata->href = urldecode($clipboarddata->href);
	
	$phraser = new JisonParser_Phraser_Handler();
	$id = implode("", $phraser->sanitizeToWords($data));
	
	Feed_ForwardLink_Send::add(
					array(
						"page"=> $page,
						"forwardlink"=> $clipboarddata,
						"textlink"=> array(
							"text"=> 	$data,
							"href"=> 	$tikilib->tikiUrl() . "tiki-index.php?page=$page#" . $id,
							"id"=>		$clipboarddata->hash. "_" . $page . "_" . $id
						)
					)
	);
	$data = addslashes(htmlspecialchars($data));
	$date = $tikilib->get_short_date($clipboarddata->date);

	$clipboarddata->href = addslashes(htmlspecialchars($clipboarddata->href));
	$clipboarddata->text = addslashes(htmlspecialchars($clipboarddata->text));

	if (!empty($clipboarddata->href)) {
		$headerlib
			->add_jsfile("lib/jquery/tablesorter/jquery.tablesorter.js")
			->add_cssfile("lib/jquery_tiki/tablesorter/themes/tiki/style.css")
			->add_jq_onready(<<<JQ
				$('#page-data').bind('rangyDone', function() {
					$('#$id').click(function() {
						var table = $('<div><table class="tablesorter">' +
							'<thead>' + 
								'<tr>' +
									'<th>' + tr('Date') + '</th>' +
									'<th>' + tr('Click below to read Citing blocks') + '</th>' +
								'</tr>' +
							'</thead>' +
							'<tbody>' +
								'<tr>' +
									'<td>$date</td>' +
									'<td><a href="$clipboarddata->href" class="read">Read</a></td>' +
								'</tr>' +
							'</tbody>' +
						'</table></div>')
							.dialog({
								title: tr("ForwardLinks To: ") + "$data",
								modal: true
							})
							.tablesorter();
						
						table.find('.read').click(function() {
							$('<form action="$clipboarddata->href" method="post">' + 
								'<input type="hidden" name="phrase" value="$clipboarddata->text" />' +
							'</form>')
								.appendTo('body')
								.submit();
							return false;
						});
						return false;
					});
				});
JQ
			);
		
    	return "~np~<span class='textlink'>~/np~".$data."~np~</span><a href='" .$clipboarddata->href ."' id='" . $id . "'>*</a>~/np~";
	} else {
    	return $data;
	}
}
