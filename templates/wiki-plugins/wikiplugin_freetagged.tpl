{* $Id: wikiplugin_freetagged.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{if isset($objects) && count($objects) gt 0}
<ul class="freetagged clearfix">
	{foreach item=row from=$objects}
		<li class="{$row.type|stringfix:' ':'_'}">
			{if $h_level gt 0}<h{$h_level}>{/if}<a href="{$row.href|escape}">{$row.name|escape}</a>{if $h_level gt 0}</h{$h_level}>{/if}
			{if !empty($row.description) or !empty($row.img)}<p>
				<em>{$row.description}</em>
				{$row.img}
			</p>{/if}
			{if !empty($row.date)}<p class="editdate">
				{$row.date|tiki_short_datetime}
			</p>{/if}
		</li>
	{/foreach}
</ul>
{/if}
