{* $Id: include_messages.tpl 36194 2011-08-15 19:57:12Z marclaporte $ *}

<form action="tiki-admin.php?page=messages" method="post" name="messages">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="messagesprefs" value="{tr}Change preferences{/tr}" />
	</div>

	<fieldset class="admin">
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_messages visible="always"}
	</fieldset>

	<fieldset class="admin">
		<legend>{tr}Settings{/tr}</legend>

		{preference name=allowmsg_by_default}
		{preference name=allowmsg_is_optional}
		{preference name=messu_mailbox_size}
		{preference name=messu_archive_size}
		{preference name=messu_sent_size}
		{preference name=user_selector_realnames_messu}

	</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="messagesprefs" value="{tr}Change preferences{/tr}" />
	</div>
</form>
