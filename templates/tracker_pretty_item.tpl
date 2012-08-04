{strip}
{* $Id: tracker_pretty_item.tpl 37415 2011-09-19 15:40:26Z lphuberdeau $ *}
{* param item, fields, wiki(wiki:page or tpl:tpl), list_mode, perms, default_group, listfields *}
{if !isset($list_mode)}{assign var=list_mode value="n"}{/if}
{foreach from=$fields item=field}
	{if $field.type ne 'x'
	  and (empty($listfields) or in_array($field.fieldId, $listfields)) 
	  and ($field.type ne 'p' or $field.options_array[0] ne 'password')}
		{capture name=value}
			{trackeroutput item=$item field=$field list_mode=$list_mode showlinks=$context.showlinks url=$context.url}
		{/capture}
		{set var="f_"|cat:$field.fieldId value=$smarty.capture.value}
	{else}
		{set var="f_"|cat:$field.fieldId value=''}
	{/if}
{/foreach}
{set var=f_created value=$item.created}
{set var=f_lastmodif value=$item.lastModif}
{set var=f_itemId value=$item.itemId}
{set var=f_status value=$item.status}
{set var=f_itemUser value=$item.itemUser}
{* ------------------------------------ *}
{include file="$wiki" item=$item}
{/strip}
