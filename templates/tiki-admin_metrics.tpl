{* $Id: tiki-admin_metrics.tpl 40035 2012-03-04 21:22:53Z gezzzan $ *}

{title help="Metrics" admpage="metrics"}{tr}Admin Metrics{/tr}{/title}

<div class="navbar">
	{button href="#metrics" _text="{tr}Metrics{/tr}"}
	{button href="#tabs" _text="{tr}Tabs{/tr}"}
	{button href="#assign" _text="{tr}Assign Metrics{/tr}"}
	{button href="#assigned" _text="{tr}Assigned Metrics{/tr}"}
	{button href="#editcreate" _text="{tr}Edit/Create Metrics{/tr}"}
	{button href="#editcreatetab" _text="{tr}Edit/Create Tab{/tr}"}
</div>

<h2>{tr}Metrics{/tr}</h2>
<table class="normal" id="metrics">
	<tr class="first">
		<th>{tr}Name{/tr}</td>
		<th>{tr}Range{/tr}</td>
		<th>{tr}Data Type{/tr}</td>
		<th>{tr}Query{/tr}</td>
		<th>{tr}Action{/tr}</td>
	</tr>
	{if !empty($metrics_list)}
		{cycle print=false values="odd,even"}
		{foreach from=$metrics_list key=i item=metric}
			<tr class="{cycle}">
				<td class="text">{$metric.metric_name|escape}</td>
				<td class="text">{$metric.metric_range|escape}</td>
				<td class="text">{$metric.metric_datatype|escape}</td>
				<td class="text">{$metric.metric_query|escape}</td>
				<td class="action">
					<a class="link" href="tiki-admin_metrics.php?metric_edit={$i|escape:'url'}#editcreate" title="{tr}Edit{/tr}"><img src="img/icons/page_edit.png" width="16" height="16" alt="{tr}Edit{/tr}" /></a>
					<a class="link" href="tiki-admin_metrics.php?assign_metric_new={$i|escape:'url'}#assign" title="{tr}Assign{/tr}"><img src="img/icons/accept.png" width="16" height="16" alt="{tr}Assign{/tr}" /></a>
					<a class="link" href="tiki-admin_metrics.php?metric_remove={$i|escape:'url'}" title="{tr}Delete{/tr}"><img src="img/icons/cross.png" width="16" height="16" alt="{tr}Delete{/tr}" /></a>
				</td>
			</tr>
		{/foreach}
	{else}
		{norecords _colspan=5}
	{/if}
</table>

<h2>{tr}Tabs{/tr}</h2>
<table class="normal" id="tabs">
	<tr class="first">
		<th>{tr}Name{/tr}</td>
		<th>{tr}Weight{/tr}</td>
		<th>{tr}Action{/tr}</td>
	</tr>
	{if !empty($tabs_list)}
		{cycle print=false values="odd,even"}
		{foreach from=$tabs_list key=i item=tab}
			<tr class="{cycle}">
				<td class="text">{$tab.tab_name|escape}</td>
				<td class="integer">{$tab.tab_order|escape}</td>
				<td class="action">
					<a class="link" href="tiki-admin_metrics.php?tab_edit={$i|escape:'url'}#editcreatetab" title="{tr}Edit{/tr}"><img src="img/icons/page_edit.png" width="16" height="16" alt="{tr}Edit{/tr}" /></a>
					<a class="link" href="tiki-admin_metrics.php?tab_remove={$i|escape:'url'}" title="{tr}Delete{/tr}"><img src="img/icons/cross.png" width="16" height="16" alt="{tr}Delete{/tr}" /></a>
				</td>
			</tr>
		{/foreach}
	{else}
		{norecords _colspan=3}
	{/if}
</table>

<h2>{tr}Assigned Metrics{/tr}</h2>
<table class="normal" id="assigned_metrics">
	<tr class="first">
		<th>{tr}Metric Name{/tr}</td>
		<th>{tr}Tab Name{/tr}</td>
		<th>{tr}Action{/tr}</td>
	</tr>
	{if !empty($metrics_assigned_list)}
		{cycle print=false values="odd,even"}
		{foreach from=$metrics_assigned_list key=i item=assigned_item}
			<tr class="{cycle}">
				<td class="text">{$metrics_list[$assigned_item.metric_id].metric_name|escape}</td>
				<td class="text">{$tabs_list[$assigned_item.tab_id].tab_name|escape}</td>
				<td class="action">
					<a class="link" href="tiki-admin_metrics.php?assign_metric_edit={$i|escape:'url'}#assign" title="{tr}Edit{/tr}"><img src="img/icons/page_edit.png" width="16" height="16" alt="{tr}Edit{/tr}" /></a>
					<a class="link" href="tiki-admin_metrics.php?assign_remove={$i|escape:'url'}" title="{tr}Unassign{/tr}"><img src="img/icons/cross.png" width="16" height="16" alt="{tr}Delete{/tr}" /></a>
				</td>
			</tr>
		{/foreach}
	{else}
		{norecords _colspan=3}
	{/if}
</table>

<h2 id="assign">
	{if $assign_metric eq ''}
		{tr}Assign metric{/tr}
	{else}
		{tr}Edit this assigned metric:{/tr} {$assign_name}
	{/if}
</h2>
{if $preview eq 'y'}
	<h3>{tr}Preview{/tr}</h3>
	{$preview_data}
{/if}

<form method="post" action="tiki-admin_metrics.php#assign">
	<input type="hidden" name="assigned_id" value="{$assigned_id}" />
	<table class="formcolor">
		<tr>
			<td>{tr}Metric Name{/tr}</td>
			<td>
				<select name="assign_metric">
					{foreach from=$metrics_list key=i item=metric}
						<option value="{$i|escape}" {if $assign_metric eq $i}selected="selected"{/if}>{$metric.metric_name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td>{tr}Tab for metric{/tr}</td>
			<td>
				<select name="assign_tab">
					{foreach from=$tabs_list key=i item=tab}
						<option value="{$i|escape}" {if $assign_tab eq $i}selected="selected"{/if}>{$tab.tab_name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="assign" value="{tr}Assign{/tr}" /></td>
		</tr>
	</table>
</form>

<h2 id="editcreate">
	{if !isset($metric_id)}
		{tr}Create new metric{/tr}
	{else}
		{tr}Edit this metric:{/tr} {$metric_name|escape}
	{/if}
</h2>

<div class="rbox" name="tip">
	<div class="rbox-title" name="tip">{tr}Hints{/tr}</div>  
	<div class="rbox-data" name="tip">
		<ul>
			<li>{tr}For list-based metrics, include the "LIMIT #" in your query.{/tr}</li>
		</ul>
	</div>
</div>

<form name="editmetric" method="post" action="tiki-admin_metrics.php#editcreate">
	<table id="admin_metrics_add" class="formcolor">
		<tr>
			<td>{tr}Name (must be unique){/tr}</td>
			<td><input type="text" name="metric_name" value="{$metric_name|escape}" /></td>
		</tr>
		<tr>
			<td>{tr}Range{/tr}</td>
			<td>
				<select name="metric_range">
					{foreach from=$metric_range_all key=rangeid item=rangename}
						<option value="{$rangeid}" {if (isset($metric_range) && ($metric_range eq $rangename))}selected="selected"{/if}>{$rangename}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td>{tr}Data Type{/tr}</td>
			<td>
				<select name="metric_datatype">
				{foreach from=$metric_datatype_all key=datatypeid item=datatypename}
					<option value="{$datatypeid}" {if (isset($metric_datatype) && ($metric_datatype eq $datatypename))}selected="selected"{/if}>{$datatypename}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td>{tr}DSN{/tr}</td>
			<td>
				<select name="metric_dsn">
					<option value="local" {if isset($metric_dsn) && $metric_dsn eq 'local'}selected="selected"{/if}>{tr}Local (Tiki database){/tr}</option>
					{foreach from=$dsn_list key=datatypeid item=dsn}
						<option value="{$dsn.name}" {if isset($metric_dsn) && $metric_dsn eq $dsn.name}selected="selected"{/if}>{$dsn.name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td>{tr}Query{/tr}</td>
			<td>
				<textarea id="metric_query" name="metric_query" rows="10">{$metric_query|escape}</textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="metric_submit" value="{if !isset($metric_id)}{tr}Create Metric{/tr}{else}{tr}Edit Metric{/tr}{/if}" /></td>
		</tr>
	</table>
	<input type="hidden" name="metric_id" value="{$metric_id|escape}">
</form>

<h2 id="editcreatetab">
	{if !isset($tab_id)}
		{tr}Create new tab{/tr}
	{else}
		{tr}Edit this tab:{/tr} {$tab_name|escape}
	{/if}
</h2>

<form name="edittab" method="post" action="tiki-admin_metrics.php#editcreatetab">
	<table id="admin_metrics_add_tab" class="formcolor">
		<tr>
			<td>{tr}Name (must be unique){/tr}</td>
			<td><input type="text" name="tab_name" value="{$tab_name|escape}" /></td>
		</tr>
		<tr>
			<td>{tr}Weight (must be integer){/tr}</td>
			<td><input type="text" name="tab_order" value="{$tab_order|escape}" /></td>
		</tr>
		<tr>
			<td>{tr}Content{/tr}</td>
			<td>
				<textarea id="tab_content" name="tab_content" rows="10">{$tab_content|escape}</textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="tab_submit" value="{if !isset($tab_id)}{tr}Create Tab{/tr}{else}{tr}Edit Tab{/tr}{/if}" /></td>
		</tr>
	</table>
	<input type="hidden" name="tab_id" value="{$tab_id|escape}">
</form>
