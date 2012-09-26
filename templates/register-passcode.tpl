{if $prefs.user_register_prettytracker eq 'y' and $prefs.user_register_prettytracker_tpl}
	<input type="password" name="passcode" id="passcode" onkeypress="regCapsLock(event)" />
	&nbsp;<strong class='mandatory_star'>*</strong>
{else}
	{if $prefs.useRegisterPasscode eq 'y'}
		<tr>
			<td><label for="passcode">{tr}Passcode to register:{/tr}</label></td>
			<td>
				<input type="password" name="passcode" id="passcode" onkeypress="regCapsLock(event)" value="{if !empty($smarty.post.passcode)}{$smarty.post.passcode}{/if}" />
				<em>{tr}Not your password.{/tr} {tr}To request a passcode, {if $prefs.feature_contact eq 'y'}<a href="tiki-contact.php">{/if}
				contact the system administrator{if $prefs.feature_contact eq 'y'}</a>{/if}{/tr}.</em>
			</td>
		</tr>
	{/if}
{/if}
