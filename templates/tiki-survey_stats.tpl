{* $Id: tiki-survey_stats.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{title}{tr}Stats for surveys{/tr}{/title}

<div class="navbar">
	{button href="tiki-list_surveys.php" _text="{tr}List Surveys{/tr}"}
	{button href="tiki-survey_stats.php" _text="{tr}Survey Stats{/tr}"}
	{if $tiki_p_admin_surveys eq 'y'}
		{button href="tiki-admin_surveys.php" _text="{tr}Admin Surveys{/tr}"}
	{/if}
</div>

{include file='find.tpl'}

<table class="normal">
<tr>
<th><a href="tiki-survey_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Survey{/tr}</a></th>
<th><a href="tiki-survey_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'taken_desc'}taken_asc{else}taken_desc{/if}">{tr}taken{/tr}</a></th>
<th><a href="tiki-survey_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></th>
<th><a href="tiki-survey_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastTaken_desc'}lastTaken_asc{else}lastTaken_desc{/if}">{tr}Last taken{/tr}</a></th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_survey_stats eq 'y') or ($channels[user].individual_tiki_p_view_survey_stats eq 'y')}
<tr class="{cycle}">
<td class="text"><a class="tablename" href="tiki-survey_stats_survey.php?surveyId={$channels[user].surveyId}">{$channels[user].name|escape}</a></td>
<td class="text">{$channels[user].taken}</td>
<td class="date">{$channels[user].created|tiki_short_datetime}</td>
<td class="date">{$channels[user].lastTaken|tiki_short_datetime}</td>
</tr>
{/if}
{sectionelse}
	{norecords _colspan=4}
{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
