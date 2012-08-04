{* $Id: tiki-share.tpl 40019 2012-03-03 20:39:25Z gezzzan $ *}
{title help="share"}
	{if $report != 'y'}{tr}Share this page{/tr}{else}{tr}Report this page{/tr}{/if}
{/title}

{if isset($sent) && empty($errors)}
	<div class="simplebox highlight">
		{icon _id=accept alt="{tr}OK{/tr}" style="vertical-align:middle" align="left"}
		{tr}Page shared:{/tr}<br />
		{if isset($emailSent)}
			<div>
				{tr}The link was sent via e-Mail to the following addresses:{/tr} {$addresses|escape}
			</div>
		{/if}
		{if isset($tweetId)}
			<div>
				<a href="http://www.twitter.com/"><img src="img/icons/twitter_t_logo_32.png" /> </a>
				{tr}The link was sent via Twitter{/tr}
			</div>
		{/if}
		{if isset($facebookId) and $facebookId!=false}
			<div>
				<img src="img/icons/facebook-logo_32.png" /> </a>{tr}The link was posted on your Facebook wall{/tr}
			</div>
		{/if}
		{if isset($messageSent)}
			<div>{tr}The link was sent as message to{/tr} {$messageto|escape}</div>
		{/if}
		{if isset($threadId) and $threadId>0}
			<div>
				{tr}The link was published in a{/tr} <a href="tiki-view_forum_thread.php?comments_parentId={$threadId}&forumId={$forumId}">{tr}forum{/tr}</a>
				<br />
				{foreach from=$feedbacks item=feedback}
					{$feedback}
					<br />
				{/foreach}
			</div>
		{/if}
	</div>
{/if}

{if !empty($errors)}
	<div class="simplebox highlight">
		{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle" align="left"}
		{foreach from=$errors item=m name=errors}
			{$m}
			{if !$smarty.foreach.errors.last}<br />{/if}
		{/foreach}
	</div>
{/if}

{if !isset($sent) && empty($errors)}
	<form method="post" action="tiki-share.php?url={$url|escape:url}" id="share-form">
		<input type="hidden" name="url" value="{$url|escape:url}" />
		<input type="hidden" name="report" value="{$report}" />
		<table class="formcolor">
			{if $prefs.share_display_links eq 'y'}
				<tr>
					<td>{tr}Link{/tr}</td>
					<td><a href="{$prefix}{$url}">{$prefix}{$url}</a></td>
				</tr>
				{if $report != 'y'}
					<tr>
						<td>{tr}Short link{/tr}</td>
						<td>{$shorturl}</td>
					</tr>
				{/if}
			{/if}
			<tr>
				<td>{tr}Subject{/tr}</td>
				<td>
					<input style="width:95%;" type="text" name="subject" value="{$subject|escape|default:"{tr}Have a look at this page{/tr}"}" />
				</td>
			</tr>
	
			<tr>
				<td>
					{tr}Your message{/tr}
				</td>
	
				<td>
					<textarea name="comment" style="width:95%;" rows="10" id='comment'>{$comment|escape|@default:"{tr}I found an interesting page that I thought you would like.{/tr}"}</textarea>
				</td>
			</tr>
	
			<tr>
				<td rowspan="2">
					<img src="img/icons/large/evolution48x48.png" alt="{tr}e-Mail{/tr}"/>
					<br />
					{tr}Send via e-Mail{/tr}
				</td>
				<td>
					{if $report !='y' && !$twitterRegistered && !$facebookRegistered && !$prefs.feature_forums}
						<input type="radio" name="do_email" value="1" checked="checked" {if $prefs.disableJavascript!='y'}onclick="toggleBlock('emailtable')" {/if}/>
						{tr}Yes{/tr}
						<input type="radio" name="do_email" value="0" {if $prefs.disableJavascript!='y'}onclick="toggleBlock('emailtable')" {/if}/>
						{tr}No{/tr}
					{else}
						<input type="hidden" name="do_email" value="1" />&nbsp;
					{/if}
				</td>
			</tr>
			<tr id="emailrow">
				<td>
					<table class="formcolor" id="emailtable">
						{if $report!='y'}
							<tr>
								<td>{tr}Recipient(s){/tr}</td>
								<td>
									{if $prefs.feature_jquery_autocomplete == 'y'}
										{user_selector contact='true' user = '' multiple='true' editable='y' mustmatch='false' group='all' name='addresses' id='addresses' style='width:95%' user_selector_threshold=0}
										<br />
										<em>{tr}Separate multiple email addresses with a comma and a space{/tr}</em>
									{else}
										<input style="width:95%;" type="text" size="60" name="addresses" value="{$addresses|escape}"/>
										<br />
										<em>{tr}Separate multiple email addresses with a comma.{/tr}</em>
									{/if}
								</td>
							</tr>
						{/if}
						{if $prefs.share_display_name_and_email eq 'y'}
								<tr>
									<td>{tr}Your name{/tr}</td>
									<td>
										<input style="width:95%;" type="text" name="name" value="{$name}" />
									</td>
								</tr>
								<tr>
									<td>
										{tr}Your email{/tr}{if empty($email)} <strong class="mandatory_star">*</strong>{/if}
									</td>
									<td>
										<div class="mandatory_field">
											<input style="width:95%;" type="text" name="email" value="{$email}" />
										</div>
									</td>
								</tr>
						{else}
								<tr>
									<td colspan="2">
										<input type="hidden" value="{$name}" name="name" />
										<input type="hidden" value="{$email}" name="email" />
									</td>
								</tr>
						{/if}
					</table>
				</td>
			</tr>
			{if $twitterRegistered}
				<tr>
					<td rowspan="2">
						<img src="img/icons/twitter_t_logo_32.png" alt="Twitter" />
						<br />
						{tr}Tweet via Twitter{/tr}
					</td>
					<td>
						{if $twitter}
							<input type="radio" name="do_tweet" value="1" checked="checked" {if $prefs.disableJavascript!='y'}onclick="toggleBlock('twittertable')" {/if}/>
							{tr}Yes{/tr} 	
							<input type="radio" name="do_tweet" value="0" {if $prefs.disableJavascript!='y'}onclick="toggleBlock('twittertable')" {/if}/>
							{tr}No{/tr}
						{else}
							{remarksbox type="note" title="{tr}Note{/tr}"}
								<p><a href="tiki-socialnetworks.php">{tr}Authorize with Twitter first{/tr}</a></p>
							{/remarksbox}
						{/if}
					</td>
				</tr>
				<tr id="twitterrow">
					<td>
						{if $twitter}
							<table class="formcolor" id="twittertable">
								<tr>
									<td>{tr}Tweet{/tr}</td>
									<td>
										<input type="text" name="tweet" maxlength="140" style="width:95%;" id="tweet" value="{$subject|escape|default:"{tr}Have a look at {/tr}"} {$shorturl}" />
									</td>
								</tr>
							</table>
						{else}
							&nbsp;
						{/if}
					</td>
				</tr>
			{/if}
	
			{if $facebookRegistered}
				<tr>
					<td rowspan="2">
						<img src="img/icons/facebook-logo_32.png" alt="Facebook" />
						<br />
							{tr}Put on my Facebook wall{/tr}
					</td>
					<td>
						{if $facebook}
							<input type="radio" name="do_fb" value="1" checked="checked" {if $prefs.disableJavascript!='y'}onclick="toggleBlock('fbtable')" {/if}/>
							{tr}Yes{/tr}
							<input type="radio" name="do_fb" value="0" {if $prefs.disableJavascript!='y'}onclick="toggleBlock('fbtable')" {/if}/>
							{tr}No{/tr}
						{else}
							{remarksbox type="note" title="{tr}Note{/tr}"}
								<p><a href="tiki-socialnetworks.php">{tr}Authorize with Facebook first{/tr}</a></p>
							{/remarksbox}
						{/if}
					</td>
				</tr>
				<tr id="fbrow">
					<td>
						{if $facebook}
							<table class="formcolor" id="fbtable">
								<tr>
									<td>{tr}Link text{/tr}</td>
									<td>
										<input type="text" name="fblinktitle" id="fblinktitle" value="{$fblinktitle|escape}" style="width: 95%;" />
										<br />
										<em>{tr}This will be the title for the URL{/tr}</em>
									</td>
								</tr>
								<tr>
									<td>{tr}Like this post{/tr}</td>
									<td>
										<input type="radio" name="fblike" value="1" {if $fblike==1}checked="checked" {/if}/>
										{tr}Yes{/tr}
										<input type="radio" name="fblike" value="0" {if $fblike==0}checked="checked" {/if}/>
										{tr}No{/tr}
									</td>
								</tr>
							</table>
						{else}
							&nbsp;
						{/if}
					</td>
				</tr>
			{/if}
			
			{if $prefs.share_token_notification eq 'y'}
				<tr>
					<td>{tr}Subscribe notification{/tr}</td>
					<td><input type="checkbox" value="y" name="share_token_notification" {if $share_token_notification eq 'y'}checked="checked" {/if} /></td>
				</tr>
			{/if}
			
			{if $prefs.share_can_choose_how_much_time_access eq 'y' && $prefs.auth_token_access eq 'y'}
				<tr>
					<td>{tr}How much time recipients can access to this page{/tr}</td>
					<td>
						{if $prefs.share_max_access_time eq -1}
							<input type="text" name="how_much_time_access" value="{$how_much_time_access|default:1}"/>
						{else}
							<select id="how_much_time_access" name="how_much_time_access">
								{section name=share_max_access start=1 loop=$prefs.share_max_access_time+1}
								   {html_options values=$smarty.section.share_max_access.index output=$smarty.section.share_max_access.index}
							  {/section}
							</select>
						{/if}
						&nbsp;{tr}time{/tr}
					</td>
				</tr>
			{/if}
			
			{if $prefs.feature_messages eq 'y' && $report != 'y'}
				<tr>
					<td rowspan="2">
						<img src="img/icons/large/messages48x48.png" alt="{tr}Messages{/tr}" />
						<br />
						{tr}Send message{/tr}
					</td>
					<td>
						{if $send_msg=='y'}
							<input type="radio" name="do_message" value="1" checked="checked" {if $prefs.disableJavascript!='y'}onclick="toggleBlock('messagetable')" {/if}/>
							{tr}Yes{/tr}
							<input type="radio" name="do_message" value="0" {if $prefs.disableJavascript!='y'}onclick="toggleBlock('messagetable')" {/if}/>
							{tr}No{/tr}
						{else}
							{remarksbox type="note" title="{tr}Note{/tr}"}
								<p>{tr}You do not have the permission to send messages or you did not allow other users to send you messages.{/tr}</p>
							{/remarksbox}
						{/if}
					</td>
				</tr>
				<tr id="messagerow">
					<td>
						{if $send_msg}
							<table class="formcolor" id="messagetable">
								<tr>
									<td>{tr}Recipient(s){/tr}</td>
									<td>
										{if $prefs.feature_jquery_autocomplete == 'y'}
											{user_selector user = '' editable='y' multiple='true' name='messageto' style='width:95%' user_selector_threshold=0}
										{else}
											<input style="width:95%;" type="text" size="60" name="messageto" value="{$messageto|escape}"/>
										{/if}
										<br />
										<em>{tr}Separate multiple recipients with a semicolon.{/tr}</em>
									</td>
								</tr>
								<tr>
									<td>{tr}Priority{/tr}</td>
									<td>
										<select name="priority" id="mess-prio">
											<option value="1" {if $priority eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
											<option value="2" {if $priority eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
											<option value="3" {if $priority eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
											<option value="4" {if $priority eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
											<option value="5" {if $priority eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
										</select>
									</td>
								</tr>
							</table>
						{else}
							&nbsp;
						{/if}
					</td>
				</tr>
			{/if}
			{if $prefs.feature_forums eq 'y' && $report != 'y'}
				<tr>
					<td rowspan="2">
						<img src="img/icons/large/stock_index48x48.png" alt="{tr}Forums{/tr}" />
						<br />
						{tr}Post on forum{/tr}
					</td>
					<td>
						{if count($forums)>0}
							<input type="radio" name="do_forum" value="1" checked="checked" {if $prefs.disableJavascript!='y'}onclick="toggleBlock('forumtable')" {/if}/>
							{tr}Yes{/tr}
							<input type="radio" name="do_forum" value="0" {if $prefs.disableJavascript!='y'}onclick="toggleBlock('forumtable')" {/if}/>
							{tr}No{/tr}
						{else}
							{remarksbox type="note" title="{tr}Note{/tr}"}
								<p>{tr}There is no forum where you can post a message.{/tr}</p>
							{/remarksbox}
						{/if}
					</td>
				</tr>
				<tr id="forumrow">
					<td>
						{if count($forums)>0}
							<table class="formcolor" id="forumtable">
								<tr>
									<td>{tr}Forum{/tr}</td>
									<td>
										<select name="forumId" id="forumId" style="width:95%;">
											{foreach from=$forums item="forum"}
												<option value="{$forum.forumId}"{if $forum.forumId==$forumId} selected="selected"{/if}>
													{$forum.name}{if $forum.forum_use_password!='n'} ({tr}password-protected{/tr}){/if}
												</option>
											{/foreach}
										</select>
									</td>
								</tr>
								<tr>
									<td>{tr}Password{/tr}</td>
									<td>
										<input type="password" name="forum_password" style="width:95%;"/>
									</td>
								</tr>
								{if $prefs.feature_contribution eq 'y'}
									{include file='contribution.tpl'}
								{/if}
							</table>
						{else}
							&nbsp;
						{/if}
					</td>
				</tr>
			{/if}
			{if $prefs.feature_antibot eq 'y' && $user eq ''}
				{include file='antibot.tpl' td_style="formcolor"}
			{/if}
			<tr>
				<td></td>
				<td>
					<input type="submit" class="button" name="send" value="{tr}Share{/tr}" />
					{if $share_access}
						<input type="hidden" name="share_access" value="1" />
					{/if}
					{if $prefs.auth_tokens_share eq 'y' and $user!='' and $report !='y'}
						<input type="checkbox" name="share_access" value="1" id="share_access" {if $share_access}checked="checked" {/if}/>
						<label for="share_access">{tr}Share access rights{/tr}</label>
					{/if}
				</td>
			</tr>
		</table>
	</form>
{else}
	<a href="javascript:window.history.go(-2);">{tr}Return to previous page{/tr}</a>
{/if}
