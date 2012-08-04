<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Header.php 40234 2012-03-17 19:17:41Z changi67 $

/**
 * Handler class for Header
 * 
 * Letter key: ~h~
 *
 */
class Tracker_Field_Header extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'h' => array(
				'name' => tr('Header'),
				'description' => tr('Displays a header between fields to delimit a section and allow folding the fields.'),
				'readonly' => true,
				'help' => 'Header Tracker Field',
				'prefs' => array('trackerfield_header'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'level' => array(
						'name' => tr('Header Level'),
						'description' => tr('Level of the header to use for complex tracker structures needing multiple heading levels.'),
						'default' => 1,
						'filter' => 'int',
					),
					'toggle' => array(
						'name' => tr('Default State'),
						'description' => tr('Controls the section toggles'),
						'filter' => 'alpha',
						'default' => 'o',
						'options' => array(
							'o' => tr('Open'),
							'c' => tr('Closed'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		return array();
	}

	function renderInput($context = array())
	{
		return $this->renderOutput($context);
	}
	
	function renderOutput($context = array())
	{
		if ($context['list_mode'] === 'csv') {
			return;
		}
		global $prefs;
		$headerlib = TikiLib::lib('header');

		$class = null;
		$level = $this->getOption(0, 2);
		if (! is_numeric($level)) {
			$level = 2;
		}
		$toggle = $this->getOption(1);
		$inTable = isset($context['inTable']) ? $context['inTable'] : '';
		$name =  htmlspecialchars(tra($this->getConfiguration('name')));

		$data_toggle = '';
		if ($prefs['javascript_enabled'] === 'y' && ($toggle === 'o' || $toggle === 'c')) {
			$class = ' ' . ($toggle === 'c' ? 'trackerHeaderClose' : 'trackerHeaderOpen');
			$data_toggle = 'data-toggle="' . $toggle . '"';
		}
		if ($inTable) {
			$js = '
(function() {
	var processTableForHeaders = function( $table ) {
		var $hdr, $newtable = $("<table>").attr("class", $table.attr("class"));
		$("tr", $table).each(function() {	// step through each row
			if ($(".hdrField", this).length) {	// chop the table...
				var $this = $(this);
				var $sibs = $this.nextAll("tr");
				var level = $(".hdrField:first", this).data("level");
				var name = $("td:first", this).text();
				$hdr = $("<h" + level + ">").text($.trim(name));
				var toggle = $(".hdrField:first", this).data("toggle");
				if (toggle) {
					$hdr.click(function(){
						$newtable.toggle();
						$(this).toggleClass("trackerHeaderClose")
								.toggleClass("trackerHeaderOpen");
					}).addClass(toggle === "c" ? "trackerHeaderClose" : "trackerHeaderOpen");
					if (toggle === "c") $newtable.hide();

				}
				$sibs.each(function(){
					$newtable.append(this);
					$this.remove();
				});
				return false;
			}
		});
		$table.after($newtable).after($hdr);
		if ($("tr", $newtable).length) {
			processTableForHeaders($newtable);	// recurse until done
		}
	}
	$(".hdrField").parents("table").each(function() {
		processTableForHeaders($(this));
	});
})();';
		} else {
			$js = '';	// TODO div mode for plugins or something
		}
		$headerlib->add_jq_onready($js);
		
		// just a marker for jQ to find
		$html = '<span class="hdrField' . $class . '" data-level="' . $level . '" ' .
				$data_toggle .' style="display:none;"></span>';
		
		return $html;
	}

	function importRemote($value)
	{
		return '';
	}

	function exportRemote($value)
	{
		return '';
	}

	function importRemoteField(array $info, array $syncInfo)
	{
		return $info;
	}
}

