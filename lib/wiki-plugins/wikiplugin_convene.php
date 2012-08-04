<?php                                                                                  
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project           
//                                                                                     
// All Rights Reserved. See copyright.txt for details and a complete list of authors.  
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.  
// $Id: wikiplugin_convene.php 41667 2012-05-31 16:16:17Z robertplummer $

function wikiplugin_convene_info()
{
	return array(
		'name' => tra('Convene'),
		'documentation' => 'PluginConvene',
		'description' => tra('Convene an event with schedule and members'),
		'introduced' => 9.0,
		'prefs' => array('wikiplugin_convene','feature_calendar'),
		'body' => tra('Convene data generated from user input'),
		'icon' => 'img/icons/arrow_in.png',
		'filter' => 'rawhtml_unsafe',
		'tags' => array( 'basic' ),	
		'params' => array(
			'title' => array(
				'required' => false,
				'name' => tra('Title of Event'),
				'default' => tra('Convene'),
			),
			'calendarid' => array(
				'required' => false,
				'name' => tra('Calendar ID'),
				'description' => tra('ID number for the site calendar where to store the date for the events with maximum votes'),
				'filter' => 'digits',
				'default' => '',
			),
			'dateformat' => array(
				'required' => false,
				'name' => tra('Date and time format'),
				'description' => tra('Display date and time in short or long format, according to the site wide setting'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Short'), 'value' => 'short'), 
					array('text' => tra('Long'), 'value' => 'long')
				)
			),
		)
	);
}

function wikiplugin_convene($data, $params)
{
	global $tikilib, $headerlib, $page, $tiki_p_edit;

	static $conveneI = 0;
	++$conveneI;
	$i = $conveneI;
	
		
	$params = array_merge(
					array(
						"title" => "Convene",
						"calendarid" => "1",
						"dateformat" => "short"
					), 
					$params
	);

	extract($params, EXTR_SKIP);

	$dataString = $data . '';
	$dataArray = array();
	
	$existingUsers = json_encode(TikiLib::lib("user")->get_users_names());
	
	//start flat static text to prepared array
	$lines = explode("\n", trim($data));
	sort($lines);
	foreach ($lines as $line) {
		$line = trim($line);

		if (!empty($line)) {
			$parts = explode(':', $line);
			$dataArray[trim($parts[0])] = trim($parts[1]);
		}
	}
	
	$data = TikiFilter_PrepareInput::delimiter('_')->prepare($dataArray);
	//end flat static text to prepared array
	
	//start get users from array
	$users = array();
	foreach (end($data['dates']) as $user => $vote) {
		$users[] = $user;
	}
	//end get users from array
	
	
	//start votes summed together
	$votes = array();
	foreach ($data['dates'] as $stamp => $date) {
		foreach ($date as $vote) {
			if (empty($votes[$stamp])) $votes[$stamp] = 0;
			$votes[$stamp] += $vote;
		}
	}
	//end votes summed together
	
	
	//start find top vote stamp
	$topVoteStamp = 0;
	foreach ($votes as $stamp => $vote) {
		if (
			!isset($votes[$topVoteStamp]) || (
				isset($votes[$topVoteStamp]) &&
				$vote > $votes[$topVoteStamp]
			)
		) {
			$topVoteStamp = $stamp;
		}
	}
	//end find top vote stamp
	
	
	//start reverse array for easy listing as table
	$rows = array();
	foreach ($data['dates'] as $stamp => $date) {
		foreach ($date as $user => $vote) {
			if (isset($rows[$user][$stamp])) $rows[$user][$stamp] = array();
			 
			$rows[$user][$stamp] = $vote;
		}
	}
	//end reverse array for easy listing as table
	
	$result = "";
	
	//start date header
	$dateHeader = "";
	foreach ($votes as $stamp => $totals) {
		if (!empty($dateformat) && $dateformat == "long") {
			$dateHeader .= "<td class='conveneHeader'>". $tikilib->get_long_datetime($stamp) .
				($tiki_p_edit == 'y' ? " <button class='conveneDeleteDate$i icon ui-widget-header ui-corner-all' data-date='$stamp'><img src='img/icons/delete.png' class='icon' width='16' height='16' title='" . tr("Delete Date") . "'/></button>" : "").
			"</td>";
		} else {
			$dateHeader .= "<td class='conveneHeader'>". $tikilib->get_short_datetime($stamp) .
				($tiki_p_edit == 'y' ? " <button class='conveneDeleteDate$i icon ui-widget-header ui-corner-all' data-date='$stamp'><img src='img/icons/delete.png' class='icon' width='16' height='16' title='" . tr("Delete Date") . "'/></button>" : "").
			"</td>";
		}
	}
	$result .= "
		<tr class='conveneHeaderRow'>
			<td></td>
			$dateHeader
		</tr>";
	//end date header
	
	
	//start user list and votes 
	$userList = "";
	foreach ($rows as $user => $row) {
		$userList .= "<tr class='conveneVotes conveneUserVotes$i'>";
		$userList .= "<td>". ($tiki_p_edit == 'y' ? "<button class='conveneUpdateUser$i icon ui-widget-header ui-corner-all'><img src='img/icons/pencil.png' class='icon' width='16' height='16' title='" . tr("Edit User/Save changes") . "' /></button><button data-user='$user' title='" . tr("Delete User") . "' class='conveneDeleteUser$i icon ui-widget-header ui-corner-all'><img src='img/icons/delete.png' class='icon' width='16' height='16' /></button> " : "") . $user . "</td>";
		foreach ($row as $stamp => $vote) {
			if ($vote == 1) {
				$class = 	"ui-state-default convene-ok";
				$text = 	"<img src='img/icons/tick.png' alt='" . tr('Ok') . "' class='vote icon' width='16' height='16' />";
			} elseif ($vote == -1) {
				$class = 	"ui-state-default convene-no";
				$text = 	"<img src='img/icons/cross.png' alt='" . tr('Not ok') . "' class='vote icon' width='16' height='16' />";
			} else {
				$class = 	"ui-state-default convene-unconfirmed";
				$text = 	"<img src='img/icons/grey_question.png' alt='" . tr('Unconfirmed') . "' class='vote icon' width='16' height='16' />";
			}
			
			$userList .= "<td class='$class'>". $text
				."<input type='hidden' name='dates_" . $stamp . "_" . $user . "' value='$vote' class='conveneUserVote$i' />"
				."</td>";
		}
		$userList .= "</tr>";
	}
	$result .= $userList;
	//end user list and votes
	
	
	//start add new user and votes
	$result .= "<tr class='conveneFooterRow'>";


	$result .= "<td>".(
		$tiki_p_edit == 'y'
			?
				"<input class='conveneAddUser$i' value='" . tr("Add User") . "' /><input type='button' value='" . tr('Add User') . "' class='conveneAddUserButton$i' />"
			: ""
		).
	"</td>";
	//end add new user and votes
	
	
	//start last row with auto selected date(s)
	$lastRow = "";
	foreach ($votes as $stamp => $total) {
		$pic = "";
		if ($total == $votes[$topVoteStamp]) {
			$pic .= ($tiki_p_edit != "y" ? "<img src='img/icons/tick.png' class='icon' width='16' height='16' title='" . tr("Selected Date") . "' />" : "");
			if ($tiki_p_edit == 'y') {
				$pic .= "<button class='icon ui-widget-header ui-corner-all' onclick='document.location = $(this).find(\"a\").attr(\"href\"); return false;'><a href='tiki-calendar_edit_item.php?todate=$stamp&calendarId=$calendarid' title='" . tr("Add as Calendar Event") . "'><img src='img/icons/calendar_add.png' class='icon' width='16' height='16' /></a></button>";
			}
		}
		
		$lastRow .= "<td class='conveneFooter'>". $total ."&nbsp;$pic</td>";
	}
	$result .= $lastRow;

	$result .= "<td style='width: 20px;'>" . (
		$tiki_p_edit == 'y'
			?
				"<input type='button' class='conveneAddDate$i' value='" . tr('Add Date') . "'/>"
			: ""
	)."</td>";

	$result .= "</tr>";
	//end last row with auto selected date(s)
	
	
	$result = <<<FORM
			<form id='pluginConvene$i'>
				<table cellpadding="2" cellspacing="2" border="0" style="width: 100%;">$result</table>
			</form>
FORM;
	
	$conveneData = json_encode(
					array(
						"dates" => $data['dates'],
						"users" => $users,
						"votes" => $votes,
						"topVote" => $votes[$topVoteStamp],
						"rows" =>	$rows,
						"data" => $dataString,
					)
	);

	$n = '\n';
	$regexN = '/[\r\n]+/g';
	
	$headerlib->add_jsfile("lib/jquery/jquery-ui-timepicker-addon.js");
	$headerlib->add_jq_onready(
<<<JQ
		
		var convene$i = $.extend({
			fromBlank: function(user, date) {
				if (!user || !date) return;
				this.data = "dates_" + Date.parseUnix(date) + "_" + user;
				this.save();
			},
			updateUsersVotes: function() {
				var data = [];
				$('.conveneUserVotes$i').each(function() {
					$('.conveneUserVote$i').each(function() {
						data.push($(this).attr('name') + ' : ' + $(this).val());
					});
				});
				
				this.data = data.join('$n');
				
				this.save();
			},
			addUser: function(user) {
				if (!user) return;
				
				var data = [];
				
				for(date in this.dates) {
					data.push("dates_" + date + "_" + user);
				}
				
				this.data += '$n' + data.join('$n');
				
				this.save();
			},
			deleteUser: function(user) {
				if (!user) return;
				var data = '';
				
				for(date in this.dates) {
					for(i in this.users) {
						if (this.users[i] != user) {
							data += 'dates_' + date + '_' + this.users[i] + ' : ' + this.dates[date][this.users[i]] + '$n';
						}
					}
				}
				
				this.data = data;
				
				this.save();
			},
			addDate: function(date) {
				if (!date) return;
				date = Date.parseUnix(date);
				var addedData = '';
				
				for(user in this.users) {
					addedData += 'dates_' + date + '_' + this.users[user] + ' : 0$n';
				}
				
				this.data = (this.data + '$n' + addedData).split($regexN).sort();
				
				//remove empty lines
				for(line in this.data) {
					if (!this.data[line]) this.data.splice(line, 1);
				}
				
				this.data = this.data.join('$n');
				
				this.save();
			},
			deleteDate: function(date) {
				if (!date) return;
				date += '';
				var addedData = '';
				
				for(user in this.users) {
					addedData += 'dates_' + date + '_' + this.users[user] + ' : 0$n';
				}

				var lines = convene$i.data.split($regexN);
				var newData = [];
				for(line in lines) {
					if (!(lines[line] + '').match(date)) {
						 newData.push(lines[line]);
					}
				}

				this.data = newData.join('$n');
				this.save();
			},
			save: function() {
				$.modal(tr("Loading..."));
				
				$('<form id="conveneSave$i" method="post" action="tiki-wikiplugin_edit.php">'+
					'<div>'+
						'<input type="hidden" name="page" value="$page"/>'+
						'<input type="hidden" name="content" value="' + $.trim(this.data) + '"/>'+
						'<input type="hidden" name="index" value="$i"/>'+
						'<input type="hidden" name="type" value="convene"/>'+
						'<input type="hidden" name="params[title]" value="$title"/>'+
					'</div>'+
				'</form>')
				.appendTo('body')
				.submit();
			}
		}, $conveneData);
		
		
		//handle a blank convene
		if ("$tiki_p_edit" == 'y') {
			$('#conveneBlank$i').each(function() {
				var table = $('<table>' +
					'<tr>' +
						'<td>' +
							'User: <input type="text" style="width: 100px;" id="conveneNewUser$i" />' +
						'</td>' +
						'<td>' +
							'Date/Time: <input style="width: 100px;" id="conveneNewDatetime$i" />' +
						'</td>' +
						'<td style="vertical-align: middle;">' +
							'<input type="button" id="conveneNewUserAndDate$i" value="' + tr("Add User & Date") + '" />' +
						'</td>' +
					'</tr>' +
				'</table>').appendTo(this);
				
				$('#conveneNewUser$i').autocomplete({
					source: $existingUsers
				});
				
				$('#conveneNewDatetime$i').datetimepicker();
				
				$('#conveneNewUserAndDate$i').click(function() {
					convene$i.fromBlank($('#conveneNewUser$i').val(), $('#conveneNewDatetime$i').val());
				});
			});
		} else {
			$('#conveneBlank$i').each(function() {
				$('<div />').text(tr("Login to edit Convene")).appendTo(this);
			});
		}
		
		$('.conveneAddDate$i').click(function() {
			var dialogOptions = {
				modal: true,
				title: tr("Add Date"),
				buttons: {}
			};
			
			dialogOptions.buttons[tr("Add")] = function() {
				convene$i.addDate(o.find('input:first').val());
				o.dialog('close');
			}
			
			var o = $('<div><input type="text" style="width: 100%;" /></div>')
				.dialog(dialogOptions);
			
			o.find('input:first')
				.datetimepicker()
				.focus();
			return false;
		});
		
		$('.conveneDeleteDate$i')
			.click(function() {
				convene$i.deleteDate($(this).data("date"));
				return false;
			});
		
		$('.conveneDeleteUser$i')
			.click(function() {
				convene$i.deleteUser($(this).data("user"));
				return false;
			});
		
		$('.conveneUpdateUser$i').toggle(function() {
			$('.conveneUpdateUser$i').not(this).hide();
			$('.conveneDeleteUser$i').hide();
			$('.conveneDeleteDate$i').hide();
			$('.conveneMain$i').hide();
			$(this).parent().parent()
				.addClass('ui-state-highlight')
				.find('td').not(':first')
				.addClass('conveneTd$i')
				.removeClass('ui-state-default')
				.addClass('ui-state-highlight');
			
			$(this).find('img').attr('src', 'img/icons/accept.png');
			var parent = $(this).parent().parent();
			parent.find('.vote').hide();
			parent.find('input').each(function() {
				$('<select>' +
					'<option value="">' + tr('Unconfirmed') + '</option>' +
				    '<option value="-1">' + tr('Not ok') + '</option>' +
				    '<option value="1">' + tr('Ok') + '</option>' +
				'</select>')
					.val($(this).val())
					.insertAfter(this)
					.change(function() {
						var cl = '';

						switch($(this).val() * 1) {
							case 1:     cl = 'convene-ok';break;
							case -1:    cl = 'convene-no';break;
							default:    cl = 'convene-unconfirmed';
						}

						$(this)
							.parent()
							.removeClass('convene-no convene-ok convene-unconfirmed')
							.addClass(cl);

						convene$i.updateUsers = true;
					});
			});
		}, function () {
			$('.conveneUpdateUser$i').show();
			$('.conveneDeleteUser$i').show();
			$('.conveneDeleteDate$i').show();
			$(this).parent().parent()
				.removeClass('ui-state-highlight')
				.find('.conveneTd$i')
				.removeClass('ui-state-highlight')
				.addClass('ui-state-default');
			
			$('.conveneMain$i').show();
			$(this).find('img').attr('src', 'img/icons/pencil.png');
			var parent = $(this).parent().parent();
			parent.find('select').each(function(i) {
				parent.find('input.conveneUserVote$i').eq(i).val( $(this).val() );

				$(this).remove();
			});
			
			if (convene$i.updateUsers) {
				convene$i.updateUsersVotes();
			}
		});
		
		$('.conveneAddUser$i')
			.click(function() {
				if (!$(this).data('clicked')) {
					$(this)
						.data('initval', $(this).val())
						.val('')
						.data('clicked', true);
				}
			})
			.blur(function() {
				if (!$(this).val()) {
					$(this)
						.val($(this).data('initval'))
						.data('clicked', '');

				}
			})
			.keydown(function(e) {
				var user = $(this).val();

				if (e.which == 13) {//enter
					convene$i.addUser(user);
					return false;
				}
			})
			.autocomplete({
				source: $existingUsers
			});

			$('.conveneAddUserButton$i').click(function() {
				convene$i.addUser($('.conveneAddUser$i').val());
			});
		
		$('#pluginConvene$i .icon').css('cursor', 'pointer');
JQ
);
	
	if (empty($dataString)) {
		$result = "<div id='conveneBlank$i'></div>";
	}
	
	return
<<<RETURN
~np~
	<div class="ui-widget-content ui-corner-all">
		<div class="ui-widget-header ui-corner-top">
			<h5 style="margin: 5px;">$title</h5>
		</div>
			$result
	</div>
~/np~
RETURN;
}
