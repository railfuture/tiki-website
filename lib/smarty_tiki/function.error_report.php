<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.error_report.php 40220 2012-03-16 19:50:45Z changi67 $

function smarty_function_error_report($params, $smarty)
{
	$errorreportlib = TikiLib::lib('errorreport');
	$errors = $errorreportlib->get_errors();

	$pre = '<div id="error_report">';
	$post = '</div>';

	TikiLib::lib('header')->add_js(
					'
	$("#error_report").ajaxComplete(function (e, jqxhr) {
		var error = jqxhr.getResponseHeader("X-Tiki-Error");
		if (error) {
			if ($("ul", this).length === 0) {
				$(this).append($(error)[0].childNodes);
			} else {
				$("ul", this).append($(error).find("li"));
			}
		}
	});
	$("#error_report .clear").live("click", function () {
		$("#error_report").empty();
		return false;
	});
	'
	);
	
	if (count($errors)) {
		$smarty->loadPlugin('smarty_block_remarksbox');

		$repeat = false;
		return $pre . smarty_block_remarksbox(
						array(
							'type' => 'errors',
							'title' => tra('Error(s)'),
						),
						'<a class="clear" style="float: right;" href="#">' .
						tr('Clear errors') . '</a><ul><li>' .
						implode('</li><li>', $errors) . '</li></ul>',
						$smarty,
						$repeat
		) . $post;
	} else {
		return $pre . $post;
	}
}

