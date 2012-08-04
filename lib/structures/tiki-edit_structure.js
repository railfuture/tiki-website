// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-admin_modules.js 39475 2012-01-12 22:47:05Z sylvieg $

$(document).ready(function() {

	var tocDirty = false;

	var setupStructure = function() {
		$(".admintoc:first").nestedSortable({

			disableNesting:'no-nest',
			forcePlaceholderSize:true,
			handle:'div',
			helper:'clone',
			items:'li',
			//maxLevels: 3,
			opacity:.6,
			tabSize:20,
			tolerance:'pointer',
			toleranceElement:'> div',
			placeholder:"ui-state-highlight",
			rootID:"root",
			connectWith:"#page_list_container",

			stop:function (event, ui) {
				if ($(".save_structure a:visible").length === 0) {
					$(".save_structure a").show("fast").parent().show("fast");
					tocDirty = true;
				}
	//			$(this).removeClass("ui-state-active");
			},
			start:function (event, ui) {
	//			$(this).addClass("ui-state-active");
			}
		}).droppable({
					hoverClass:"ui-state-active",
					drop:function (event, ui) {
						if (!$(ui.draggable).hasClass("admintoclevel")) {
							var pageName = $.trim($(ui.draggable).text());
							if (!jqueryTiki.structurePageRepeat && $("li:contains('" + $.trim($(ui.draggable).text()) + "')", this).length) {
								$.getJSON($.service('object', 'report_error', {message:tr("Page only allowed once in a structure")}), function () {
									$("#error_report").css({
										position: "absolute",
										top: ui.position.top,
										left: ui.position.left,
										width: "40em"
									});
								});
							} else {
								$(this).append(
										$('<li class="ui-state-default admintoclevel new"><div>' +
												'<div class="actions"><input type="text" class="page-alias-input" value="" placeholder="Page alias..."></div>' +
												pageName + '</div></li>')
								);
								$(".save_structure a").show("fast").parent().show("fast");
								tocDirty = true;
							}
						}
					}
				})
				.disableSelection();

		$(".page-alias-input").live("change", function () {
			$(".save_structure a").show("fast").parent().show("fast");
			tocDirty = true;
		}).live("click", function () {	// for Firefox
			$(this).focus().selection($(this).val().length);
		});


		$("#page_list_container li").each(function () {
			var el = this;
			$(this).draggable({
				connectToSortable:".admintoc:first",
				revert:"invalid",
				helper:"clone",
				start:function (event, ui) {
					$(this).css("z-index", 1000);
				},
				stop:function (event, ui) {
					$("#save_structure").show("fast").data("dragged", $(el).attr("id"));
					$(this).css("z-index", "auto");
				}
			}).mouseover(function (event, ui) {	// sortable gets muddled when dragging so disable it
				$(".admintoc:first").sortable("option", "disabled", true);
			}).mouseout(function (event, ui) {
				$(".admintoc:first").sortable("option", "disabled", false);
			}).disableSelection();
		});
	};

	$(window).bind("beforeunload", function( e ) {
		if (tocDirty) {
			var m = tr("You have unsaved changes to your structure, are you sure you want to leave the page without saving?");
			var e = e || window.event;
			if (e && !($.browser.safari || $.browser.webkit)) {
				e.returnValue = m;
			}
			return m;
		}
	});

	setupStructure();

	$(".save_structure").click(function(){

		var $sortable = $(this).parent().find(".admintoc:first");
		$sortable.modal(tr("Saving..."));

		var fakeId = 1000000;
		$(".admintoclevel.new").each(function() {
			$(this).attr("id", "node_" + fakeId);
			fakeId++;
		});

		var ary = $sortable.nestedSortable('toArray', {startDepthCount: 0, listType:"ol"});

		for (var i = 0; i < ary.length; i++ ) {
			if (ary[i]["item_id"] != "root") {
				ary[i]["page_alias"] = $(".page-alias-input", "#node_" + ary[i]["item_id"]).val();
				if (ary[i]["item_id"] >= 1000000) {		// new pages
					ary[i]["page_name"] = $("#node_" + ary[i]["item_id"] + " > div").text();
				}
			} else {
				ary[i]["structure_id"] = $sortable.data("params").page_ref_id;
			}
		}


		$.post($.service("wiki", "save_structure"), {data: $.toJSON(ary), params: $.toJSON($sortable.data("params"))}, function (data) {
			$sortable.modal();
			if (data) {
				$sortable.replaceWith(data.html);
				setupStructure();
				$(".save_structure").hide();
				tocDirty = false;
			}
		}, "json");
		return false;
	});

});

function movePageToStructure(element) {
	var id = $(element).parents(".admintoclevel:first").attr("id").match(/\d*$/);
	if (id) {
		id = id[0];
	}
	$("input[name=page_ref_id]", "#move_dialog").val(id);
	$("#move_dialog").dialog({
		title: tr("Move page")
	});
}

function addNewPage(element) {
	var id = $(element).parents(".admintoclevel:first").attr("id").match(/\d*$/);
	if (id) {
		id = id[0];
	}
	$("input[name=page_ref_id]", "#newpage_dialog").val(id);

	$("#newpage_dialog").dialog({
		title: tr("Add page")

	});
}

