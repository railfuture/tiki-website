{* $Id: wikiplugin_subscribegroups.tpl 37273 2011-09-15 01:48:36Z nkoth $ *}
<div class="subscribeGroups">
{if !empty($userGroups)}
<h3>{tr}Groups you are in{/tr}</h3>
{cycle values="odd,even" print=false}
<table class="normal">
{foreach from=$userGroups key=gr item=type}
	<tr class="{cycle}">
	<td>
		{if !empty($allGroups.$gr.groupHome)}<a href="{$allGroups.$gr.groupHome|escape:url}" class="groupLink">{/if}
		{if $type eq 'included'}{$gr|escape} <i>{tr}(This is an included group){/tr}</i>
		{elseif $type eq 'leader'}{$gr|escape} <i>{tr}(You are a leader){/tr}</i>
		{else}{$gr|escape}{/if}
		{if !empty($allGroups.$gr.groupHome)}</a>{/if}
		{if $showdefault eq 'y' and $default_group eq $gr}{icon _id='group' alt="{tr}Your default group{/tr}"}{/if}
		{if $showgroupdescription eq 'y'}<div>{$allGroups.$gr.groupDesc|escape}</div>{/if}
	</td>
	<td>
		{if $type ne 'included' and $type ne 'leader' and ($alwaysallowleave eq 'y' or $allGroups.$gr.userChoice eq 'y')}
			<a href="{$smarty.server.REQUEST_URI}{if strstr($smarty.server.REQUEST_URI, '?')}&amp;{else}?{/if}unassign={$gr|escape:'url'}" class="button">{tr}Leave Group{/tr}</a>
		{/if}
		{if $type eq 'leader'}
			<a class="button" href="{$managementpages.$gr|sefurl:wiki}">{tr}Manage Group{/tr}</a>
		{/if}
		{if $showdefault eq 'y' and ($default_group ne $gr or !empty($defaulturl))}
			<a href="{$smarty.server.REQUEST_URI}{if strstr($smarty.server.REQUEST_URI, '?')}&amp;{else}?{/if}default={$gr|escape:'url'}" title="{tr}Change default group{/tr}">{icon _id='group' alt="{tr}Change default group{/tr}"}</a>
		{/if}
	</td>
	</tr>
{/foreach}
</table>
{/if}

{if $showsubscribe ne 'n' && !empty($possibleGroups) && $subscribestyle eq 'dropdown'}
<form method="post">
<select name="assign" onchange="this.form.submit();">
<option value=""><i>{if !empty($subscribe)}{$subscribe|escape}{else}{tr}Subscribe to a group{/tr}{/if}</i></option>
{foreach from=$possibleGroups item=gr}
	<option value="{$gr|escape}">
		{$gr|escape}
		{if $showgroupdescription eq 'y' and !empty($allGroups.$gr.groupDesc)} ({$allGroups.$gr.groupDesc|escape}){/if}
	</option>
{/foreach}
</select>
</form>
{elseif $showsubscribe ne 'n' && !empty($possibleGroups) && $subscribestyle eq 'table'}
<h3{if !empty($userGroups)} style="margin-top: 15px;"{/if}>{tr}Groups you can join{/tr}</h3>
<form method="post">
<table class="normal">
{foreach from=$possibleGroups item=gr}
	<tr>
	<td class="{cycle}">
	<input name="assign[]" type="checkbox" value="{$gr|escape}" /> 
	{if !in_array($gr, $privategroups)}<a href="{$allGroups.$gr.groupHome|escape:url}" class="groupLink">{else}<span class="groupLink">{/if}{if isset($basegroupnames.$gr)}{$basegroupnames.$gr|escape} <i>{tr}This group requires approval to join{/tr}</i>{else}{$gr|escape}{/if}{if !in_array($gr, $privategroups)}</a>{else}</span>{/if}
	{if $showgroupdescription eq 'y'}<div style="padding-left: 25px;">{$allGroups.$gr.groupDesc|escape}</div>{/if}
	</td>
	</tr>
{/foreach}
</table>
<input type="submit" value="{if !empty($subscribe)}{$subscribe|escape}{else}{tr}Subscribe to groups{/tr}{/if}" />
</form>{/if}
</div>
