{* $Id: list.tpl 36563 2011-08-29 00:56:56Z marclaporte $ *}
<div class="adminoptionbox preference clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}" style="text-align: left;">
  <label for="{$p.id|escape}">{$p.name|escape|breakline}:</label>
	<select name="{$p.preference|escape}" id="{$p.id|escape}">
		{foreach from=$p.options key=value item=label}
			<option value="{$value|escape}"{if $value eq $p.value} selected="selected"{/if} {$p.params}>{$label|escape}</option>
		{/foreach}
	</select>
	{include file="prefs/shared-flags.tpl"}
	{if $p.shorthint}
		<em>{$p.shorthint|simplewiki}</em>
	{/if}
	{if $p.detail}
		<br/>{$p.detail|simplewiki}
	{/if}		
	{if $p.hint}
		<br/><em>{$p.hint|simplewiki}</em>
	{/if}
	{include file="prefs/shared-dependencies.tpl"}
	{jq}
if ($('.{{$p.preference|escape}}_childcontainer').length) {
	$('#{{$p.id|escape}}').change( function( e ) {
		$('.{{$p.preference|escape}}_childcontainer').hide();
		if( $(this).val().length ) {
			$('.{{$p.preference|escape}}_childcontainer.' + $(this).val()).show();
		}
	} ).change();
}
	{/jq}
</div>
