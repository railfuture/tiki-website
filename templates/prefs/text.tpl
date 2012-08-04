<div class="adminoptionbox preference clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}">
	<label for="{$p.id|escape}">{$p.name|escape}{tr}:{/tr}</label>
	{if is_array( $p.value )}
		<input name="{$p.preference|escape}" id="{$p.id|escape}" value="{$p.value|@implode:$p.separator|escape}" size="{$p.size|default:40|escape}" 
			type="text" {$p.params}/>
	{else}
		<input name="{$p.preference|escape}" id="{$p.id|escape}" value="{$p.value|escape}" size="{$p.size|default:40|escape}" 
			type="text" {$p.params}/>
	{/if}
	{if $p.shorthint}
		<em>{$p.shorthint|simplewiki}</em>
	{/if}

	{include file="prefs/shared-flags.tpl"}

	{if $p.detail}
		<br/>{$p.detail|simplewiki}
	{/if}	

	{if $p.hint}
		<br/><em>{$p.hint|simplewiki}</em>
	{/if}

	{include file="prefs/shared-dependencies.tpl"}

</div>
