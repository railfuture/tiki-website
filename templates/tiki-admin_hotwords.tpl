{* $Id: tiki-admin_hotwords.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{title help="Hotwords"}{tr}Admin Hotwords{/tr}{/title}

<h2>{tr}Add Hotword{/tr}</h2>

<form method="post" action="tiki-admin_hotwords.php">
	<table class="formcolor">
		<tr>
			<td>{tr}Word{/tr}</td>
			<td><input type="text" name="word" /></td>
		</tr>
		<tr>
			<td>{tr}URL{/tr}</td>
			<td><input type="text" name="url" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" name="add" value="{tr}Add{/tr}" />
			</td>
		</tr>
	</table>
</form>

<h2>{tr}Hotwords{/tr}</h2>
{if $words}
	{include file='find.tpl'}
{/if}
<table class="normal">
	<tr>
		<th>
			<a href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'word_desc'}word_asc{else}word_desc{/if}">{tr}Word{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a>
		</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{section name=user loop=$words}
		<tr class="{cycle}">
			<td class="text">{$words[user].word}</td>
			<td class="text">{$words[user].url}</td>
			<td class="action">
				<a class="link" href="tiki-admin_hotwords.php?remove={$words[user].word|escape:"url"}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={$sort_mode}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=3}
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
