{* $Id: tiki-user_watches.tpl 41668 2012-05-31 16:28:26Z xavidp $ *}

{title help="User+Watches"}{tr}User Watches and preferences{/tr}{/title}
{include file='tiki-mytiki_bar.tpl'}

{if $email_ok eq 'n'}
	{remarksbox type="warning" title="{tr}Warning{/tr}"}
		{tr}You need to set your email to receive email notifications.{/tr}
		{icon _id="arrow_right" href="tiki-user_preferences.php"}
	{/remarksbox}
{/if}

{tabset name="user_watches"}
	{if $prefs.feature_daily_report_watches eq 'y'}
	{tab name="{tr}Report Preferences{/tr}"}
		{if isset($remove_user_watch_error) && $remove_user_watch_error}
			{remarksbox type="error" title="{tr}Error{/tr}"}{tr}You are not allowed to remove this notification !{/tr}{/remarksbox}
		{else}
			{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use reports to summarise notifications about objects you are watching.{/tr}{/remarksbox}
		{/if}
		
		<form action="tiki-user_reports.php" method="post">
			<input type="hidden" name="report_preferences" value="true"/>
			<p><input type="checkbox" name="use_daily_reports" value="true" {if $report_preferences != false}checked{/if}/> {tr}Use reports{/tr}</p>
		
			<p>
			{tr}Interval in which you want to get the reports{/tr}
			<select name="interval">
					<option value="daily" {if $report_preferences.interval eq "daily"}selected{/if}>{tr}Daily{/tr}</option>
					<option value="weekly" {if $report_preferences.interval eq "weekly"}selected{/if}>{tr}Weekly{/tr}</option>
					<option value="monthly" {if $report_preferences.interval eq "monthly"}selected{/if}>{tr}Monthly{/tr}</option>
			</select>
			</p>
			
			<div style="float:left; margin-right: 50px;">
			    <input type="radio" name="view" value="short"{if $report_preferences.view eq "short"} checked="checked"{/if} /> {tr}Short report{/tr}<br />
		    	<input type="radio" name="view" value="detailed"{if $report_preferences.view eq "detailed" OR $report_preferences eq false} checked="checked"{/if} /> {tr}Detailed report{/tr}<br />
			</div>
			<div style="float:left; margin-right: 50px;">
			    <input type="radio" name="type" value="html"{if $report_preferences.type eq "html" OR $report_preferences eq false} checked="checked"{/if} /> {tr}HTML-Email{/tr}<br />
		    	<input type="radio" name="type" value="plain"{if $report_preferences.type eq "plain"} checked="checked"{/if} /> {tr}Plain text{/tr}<br />
		    </div>
			<div>
				<input type="checkbox" name="always_email" value="1"{if $report_preferences.always_email eq 1 OR $report_preferences eq false} checked="checked"{/if}/> {tr}Send me an email also if nothing happened{/tr}
			</div>
			
			<p><input type="submit" name="submit" value=" {tr}Apply{/tr} " /></p>
		</form>
	{/tab}
	{/if}
	{tab name="{tr}My watches{/tr}"}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use "watches" to monitor wiki pages or other objects.{/tr} {tr}Watch new items by clicking the {icon _id=eye} button on specific pages.{/tr}{/remarksbox}

{if $add_options|@count > 0}
	<h3>{tr}Add Watch{/tr}</h3>
	<form action="tiki-user_watches.php" method="post">
	<table class="formcolor">
	<tr>
		<td>{tr}Event:{/tr}</td>
		<td>
			<select name="event" id="type_selector">
				<option>{tr}Select event type{/tr}</option>
				{foreach key=event item=type from=$add_options}
					<option value="{$event|escape}">{$type.label|escape}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	{if $prefs.feature_categories eq 'y'}
	<tr id="categ_list">
		<td>{tr}Category{/tr}</td>
		<td>
			<select class="categwatch-select" name="categwatch" id="langwatch_categ">
				{foreach item=c from=$categories}
					<option value="{$c.categId|escape}">{$c.name|escape}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	{/if}
	{if $prefs.feature_multilingual eq 'y'}
	<tr id="lang_list">
		<td>{tr}Language{/tr}</td>
		<td>
			<select name="langwatch">
				{foreach item=l from=$languages}
					<option value="{$l.value|escape}">{$l.name|escape}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	{/if}
	<tr><td>&nbsp;</td>
	<td><input type="submit" name="add" value="{tr}Add{/tr}" /></td>
	</tr>
	</table>
	</form>
	{jq}
		$('#type_selector').change( function() {
			var type = $(this).val();
	
			$('#lang_list').hide();
			$('#categ_list').hide();
	
			if( type == 'wiki_page_in_lang_created' ) {
				$('#lang_list').show();
			}
	
			if( type == 'category_changed_in_lang' ) {
				$('#lang_list').show();
				$('#categ_list').show();
			}
		} ).trigger('change');
	{/jq}
{/if}
<h3>{tr}Watches{/tr}</h3>
<form action="tiki-user_watches.php" method="post" id='formi'>
{tr}Show:{/tr}<select name="event" onchange="javascript:document.getElementById('formi').submit();">
<option value=""{if $smarty.request.event eq ''} selected="selected"{/if}>{tr}All watched events{/tr}</option>
{foreach from=$events key=name item=description}
<option value="{$name|escape}"{if $name eq $smarty.request.event} selected="selected"{/if}>
	{if $name eq 'blog_post'}
		{tr}A user submits a blog post{/tr}
	{elseif $name eq 'forum_post_thread'}
		{tr}A user posts a forum thread{/tr}
	{elseif $name eq 'forum_post_topic'}
		{tr}A user posts a forum topic{/tr}
	{elseif $name eq 'wiki_page_changed'}
		{if $prefs.wiki_watch_comments eq 'y'}
			{tr}A user edited or commented on a wiki page{/tr}
		{else}
			{tr}A user edited a wiki page{/tr}
		{/if}
	{else}
		{$description}
	{/if}
</option>
{/foreach}
</select>
</form>
<br />
<form action="tiki-user_watches.php" method="post">
<table class="normal">
	<tr>
		{if $watches}
			<th style="text-align:center;"></th>
		{/if}
		<th>{tr}Event{/tr}</th>
		<th>{tr}Object{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{foreach item=w from=$watches}
		<tr class="{cycle}">
			{if $watches}
				<td class="checkbox">
					<input type="checkbox" name="watch[{$w.watchId}]" />
				</td>
			{/if}
			<td class="text">
				{if $w.event eq 'blog_post'}
					{tr}A user submits a blog post{/tr}
				{elseif $w.event eq 'forum_post_thread'}
					{tr}A user posts a forum thread{/tr}
				{elseif $w.event eq 'forum_post_topic'}
					{tr}A user posts a forum topic{/tr}
				{elseif $w.event eq 'wiki_page_changed'}
					{if $prefs.wiki_watch_comments eq 'y'}
						{tr}A user edited or commented on a wiki page{/tr}
					{else}
						{tr}A user edited a wiki page{/tr}
					{/if}
				{elseif isset($w.label)}
				{$w.label}
				{/if}
				({$w.event})
			</td>
			<td class="text"><a class="link" href="{$w.url}">{tr}{$w.type}:{/tr} {$w.title|escape}</a></td>
		</tr>
	{foreachelse}
		{norecords _colspan=2}
	{/foreach}
</table>
{if $watches}
	{tr}Perform action with checked:{/tr} <input type="submit" name="delete" value="{tr}Delete{/tr}" />
{/if}
</form>
{/tab}
{/tabset}
