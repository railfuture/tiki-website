{* $Id: table.tpl 38414 2011-10-21 10:40:20Z jonnybradley $ *}
<table>
	<tr>
		{foreach from=$column item=col}
			<th>
				{if $col.sort}
					{self_link _sort_arg='sort_mode' _sort_field=$col.sort}{$col.label|escape}{/self_link}
				{else}
					{$col.label|escape}
				{/if}
			</th>
		{/foreach}
	</tr>
	{foreach from=$results item=row}
		<tr>
			{foreach from=$column item=col}
				{if $col.mode eq 'raw'}
					<td>{$row[$col.field]}</td>
				{else}
					<td>{$row[$col.field]|escape}</td>
				{/if}
			{/foreach}
		</tr>
	{/foreach}
</table>
{pagination_links resultset=$results}{/pagination_links}
