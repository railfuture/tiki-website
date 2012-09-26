{* $Id: list_file_gallery_content.tpl 42757 2012-08-25 19:13:48Z jonnybradley $ *}
{if empty($sort_arg)}{assign var='sort_arg' value='sort_mode'}{/if}
<table class="normal">
	<tr>
		{if $gal_info.show_checked ne 'n' and ($tiki_p_admin_file_galleries eq 'y' or $tiki_p_upload_files eq 'y')}
			{assign var=nbCols value=$nbCols+1}
			<th class="checkbox">{select_all checkbox_names='file[],subgal[]'}</th>
		{/if}

		{if ( $prefs.use_context_menu_icon eq 'y' or $prefs.use_context_menu_text eq 'y' ) and $gal_info.show_action neq 'n' and $prefs.javascript_enabled eq 'y'}
			{assign var=nbCols value=$nbCols+1}
			<th style="width:1%">&nbsp;</th>
		{/if}

		{if !empty($show_parentName) and $show_parentName eq 'y'}{* assigned in wikiplugin_files *}
			<th>
				{self_link _sort_arg=$sort_arg _sort_field='parentName'}{tr}Gallery{/tr}{/self_link}
			</th>
			{assign var=nbCols value=$nbCols+1}
		{/if}
		{if !empty($show_thumb) and $show_thumb eq 'y'}{* assigned in wikiplugin_files *}
			<th>
			</th>
			{assign var=nbCols value=$nbCols+1}
		{/if}

		{foreach from=$fgal_listing_conf item=item key=propname}
			{if isset($item.key)}
				{assign var=key_name value=$item.key}
			{else}
				{assign var=key_name value="show_$propname"}
			{/if}

			{if isset($gal_info.$key_name) and $gal_info.$key_name eq 'o'}
				{assign var=show_infos value='y'}
				{if $sort_mode eq $propname|cat:'_asc' or $sort_mode eq $propname|cat:'_desc'}
					{assign var=other_columns_selected value=$propname}
				{else}
					{capture assign=other_columns}
						{$other_columns}
						{self_link sort_mode=$propname|cat:'_asc'}{$fgal_listing_conf.$propname.name}{/self_link}<br />
					{/capture}
				{/if}
			{/if}

			{if isset($gal_info.$key_name) and ( $gal_info.$key_name eq 'y' or $gal_info.$key_name eq 'i' or $gal_info.$key_name eq 'a' or $propname eq 'name' )}
				{assign var=propval value=$item.name}
				{assign var=link_title value=''}
				{assign var=td_args value=''}
	
				{if $gal_info.$key_name eq 'i' or $propname eq 'type' or ( $propname eq 'lockedby' and $gal_info.$key_name eq 'a')}
					{if isset($item.icon)}
						{assign var=propicon value=$item.icon}
					{else}
						{assign var=propval value=$item.name[0]}
						{/if}
					{assign var=link_title value=$item.name}
					{assign var=td_args value=$td_args|cat:' style="width:1%;text-align:center"'}
				{/if}

				{if $propname eq 'name' and ( $gal_info.show_name eq 'a' or $gal_info.show_name eq 'f' )}
					{assign var=nbCols value=$nbCols+1}
					<th{$td_args}>
						{self_link _sort_arg=$sort_arg _sort_field='filename'}{tr}Filename{/tr}{/self_link}
					</th>
				{/if}

				{if !($galleryId eq 0 and $propname eq 'lockedby') and ($propname neq 'name' or ( $gal_info.show_name eq 'a' or $gal_info.show_name eq 'n' )) and ($propname neq 'description' or $gal_info.show_name neq 'n')}
					{assign var=nbCols value=$nbCols+1}
					<th{$td_args}>
							{self_link _sort_arg=$sort_arg _sort_field=$propname _title=$link_title}
							{if !empty($propicon)}{icon _id=$propicon alt=$link_title}{else}{$propval}{/if}
						{/self_link}
					</th>
				{/if}
			{/if}
		{/foreach}

		{if $other_columns neq ''}
			{capture name=over_other_columns}
				{strip}
					<div class='opaque'>
						<div class='box-title'>{tr}Other Sorts{/tr}</div>
						<div class='box-data'>
							{if $other_columns_selected neq ''}
								{self_link sort_mode='NULL'}{tr}No Additional Sort{/tr}{/self_link}
								<hr />
							{/if}
							{$other_columns}
						</div>
					</div>
				{/strip}
			{/capture}
		{/if}

		{if $other_columns_selected neq ''}
			{assign var=nbCols value=$nbCols+1}
			<th>
				{self_link _sort_arg=$sort_arg _sort_field=$other_columns_selected _title=$fgal_listing_conf.$other_columns_selected.name}
					{$fgal_listing_conf.$other_columns_selected.name}
				{/self_link}
			</th>
		{/if}

		{if ( $prefs.use_context_menu_icon neq 'y' and $prefs.use_context_menu_text neq 'y' ) or $gal_info.show_action eq 'y' or $prefs.javascript_enabled neq 'y'}
			{assign var=nbCols value=$nbCols+1}
			<th>{tr}Actions{/tr}</th>
		{/if}

		{if ( $other_columns neq '' or $other_columns_selected neq '' ) and $prefs.javascript_enabled eq 'y'}
			{assign var=nbCols value=$nbCols+1}
			<th style="width:1%">
				{if $other_columns neq ''}
					<a href='#' {popup trigger="onClick" sticky=1 mouseoff=1 fullhtml="1" text=$smarty.capture.over_other_columns|escape:"javascript"|escape:"html"} title="{tr}Other Sorts{/tr}">
				{/if}
				{icon _id='timeline_marker' alt="{tr}Other Sorts{/tr}" title=''}
				{if $other_columns neq ''}
					</a>
				{/if}
			</th>
		{/if}
	</tr>

	{cycle values="odd,even" print=false}
	{section name=changes loop=$files}

		{if ( ( ! isset($fileId) ) || $fileId == 0 ) || ( $fileId == $files[changes].id )}
			{if ( $prefs.use_context_menu_icon eq 'y' or $prefs.use_context_menu_text eq 'y' ) and $gal_info.show_action neq 'n'}
				{capture name=over_actions}
					{strip}
						<div class='opaque'>
							<div class='box-title'>{tr}Actions{/tr}</div>
							<div class='box-data'>
								{include file='fgal_context_menu.tpl' menu_icon=$prefs.use_context_menu_icon menu_text=$prefs.use_context_menu_text changes=$smarty.section.changes.index}
							</div>
						</div>
					{/strip}
				{/capture}
			{/if}

			{capture name=over_preview}{strip}
				{if $files[changes].type|truncate:6:'':true eq 'image/'}
					<div class='opaque'>
						<img src="{$files[changes].id|sefurl:thumbnail}" />
					</div>
				{/if}
			{/strip}{/capture}

			{assign var=nb_over_infos value=0}
			{capture name=over_infos}
				{strip}
					<div class='opaque'>
						<div class='box-title'>{tr}Properties{/tr}</div>
						<div class='box-data'>
							<table>
								{foreach item=prop key=propname from=$fgal_listing_conf}
									{if isset($prop.key)}
										{assign var=propkey value=$item.key}
									{else}
										{assign var=propkey value="show_$propname"}
									{/if}
									{assign var=propval value=$files[changes].$propname}

									{* Format property values *}
									{if $propname eq 'created' or $propname eq 'lastModif' or $propname eq 'lastDownload'}
										{if empty($propval)}
											{assign var=propval value=''}
										{else}
											{assign var=propval value=$propval|tiki_long_date}
										{/if}
									{elseif $propname eq 'last_user' or $propname eq 'author' or $propname eq 'creator'}
										{assign var=propval value=$propval|username}
									{elseif $propname eq 'size'}
										{assign var=propval value=$propval|kbsize:true}
									{elseif $propname eq 'backlinks'}
										{assign var=propval value=$files[changes].nbBacklinks}
									{elseif $propname eq 'description'}
							    	   {assign var=propval value=$propval|nl2br}
									{/if}

									{if isset($gal_info.$propkey) and $propval neq '' and ( $gal_info.$propkey eq 'a' or $gal_info.$propkey eq 'o' )}
										<tr><td><b>{$fgal_listing_conf.$propname.name}</b>:</td> <td>{$propval}</td></tr>
										{assign var=nb_over_infos value=$nb_over_infos+1}
									{/if}
								{/foreach}
							</table>
						</div>
					</div>
				{/strip}
			{/capture}

			{if $nb_over_infos gt 0}
				{assign var=over_infos value=$smarty.capture.over_infos}
			{else}
				{assign var=over_infos value=''}
			{/if}
			
			{assign var=nb_over_share value=0}
			{capture name=over_share}{strip}
			    <div class='opaque'>
			      <div class='box-title'>{tr}Share with:{/tr}</div>
			      <div class='box-data'>
			        <div>
			            {foreach item=prop key=propname from=$files[changes].share.data}
							<b>{$prop.email}</b>: {$prop.visit} / {$prop.maxhits}<br />
							{assign var=nb_over_share value=$nb_over_share+1}
						{/foreach}
			        </div>
			      </div>
			    </div>
			{/strip}{/capture}
			
			{if $nb_over_share gt 0}
			    {assign var=over_share value=$smarty.capture.over_share}
			{else}
			    {assign var=over_share value=''}
			{/if}
			
			
		<tr class="{cycle}">

			{if $gal_info.show_checked neq 'n' and ($tiki_p_admin_file_galleries eq 'y' or $tiki_p_upload_files eq 'y')}
				<td class="checkbox">
					{if $files[changes].isgal eq 1}
						{assign var='checkname' value='subgal'}
					{else}
						{assign var='checkname' value='file'}
					{/if}
					<input type="checkbox" name="{$checkname}[]" value="{$files[changes].id|escape}" {if $smarty.request.$checkname and in_array($files[changes].id,$smarty.request.$checkname)}checked="checked"{/if} />
				</td>
			{/if}

			{if ( $prefs.use_context_menu_icon eq 'y' or $prefs.use_context_menu_text eq 'y' ) and $gal_info.show_action neq 'n' and $prefs.javascript_enabled eq 'y'}
				<td style="white-space: nowrap">
					<a class="fgalname" title="{tr}Actions{/tr}" href="#" {popup trigger="onClick" sticky=1 mouseoff=1 fullhtml="1" center=true text=$smarty.capture.over_actions|escape:"javascript"|escape:"html"} style="padding:0; margin:0; border:0">{icon _id='wrench' alt="{tr}Actions{/tr}"}</a>
				</td>
			{/if}

			{if !empty($show_parentName) and $show_parentName eq 'y'}{* assigned in wikiplugin_files *}
				<td>
					<a href="tiki-list_file_gallery.php?galleryId={$files[changes].galleryId}">{$files[changes].parentName|escape}</a>
				</td>
			{/if}
			{if !empty($show_thumb) and  $show_thumb eq 'y'}{* assigned in wikiplugin_files *}
				<td>
					{if $files[changes].isgal == 0}
						<a href="{if $absurl == 'y'}{$base_url}{/if}tiki-download_file.php?fileId={$files[changes].fileId}&display"><img src="{if $absurl == 'y'}{$base_url}{/if}tiki-download_file.php?fileId={$files[changes].fileId}&thumbnail" /></a>
					{/if}
				</td>
			{/if}

			{foreach from=$fgal_listing_conf item=item key=propname}
				{if isset($item.key)}
					{assign var=key_name value=$item.key}
				{else}
					{assign var=key_name value="show_$propname"}
				{/if}

				{if isset($gal_info.$key_name) and ( $gal_info.$key_name eq 'y' or $gal_info.$key_name eq 'a' or $gal_info.$key_name eq 'i' or $propname eq 'name' or ( $other_columns_selected neq '' and $propname eq $other_columns_selected ) )}
					{assign var=propval value=$files[changes].$propname|escape}

					{* build link *}
					{capture assign=link}
						{strip}
							{if $files[changes].isgal eq 1}
								href="tiki-list_file_gallery.php?galleryId={$files[changes].id}{if !empty($filegals_manager)}&amp;filegals_manager={$filegals_manager|escape}{/if}{if !empty($insertion_syntax)}&amp;insertion_syntax={$insertion_syntax|escape}{/if}"
							{else}
		
								{if !empty($filegals_manager)}
									href="#" onclick="window.opener.insertAt('{$filegals_manager}','{$files[changes].wiki_syntax|escape}');checkClose();return false;" title="{tr}Click Here to Insert in Wiki Syntax{/tr}"
		
								{elseif (isset($files[changes].p_download_files) and $files[changes].p_download_files eq 'y')
	 							or (!isset($files[changes].p_download_files) and $files[changes].perms.tiki_p_download_files eq 'y')}
									{if $gal_info.type eq 'podcast' or $gal_info.type eq 'vidcast'}
										href="{$prefs.fgal_podcast_dir}{$files[changes].path}" title="{tr}Download{/tr}"
									{else}
										href="{$files[changes].id|sefurl:file}" title="{tr}Download{/tr}"
									{/if}
								{/if}
	
								{if $smarty.capture.over_preview neq ''
								    and (((isset($files[changes].p_download_files)
								           and $files[changes].p_download_files eq 'y')
									  or (!isset($files[changes].p_download_files)
									      and $files[changes].perms.tiki_p_download_files eq 'y')))}
									{literal} {/literal}{popup fullhtml="1" text=$smarty.capture.over_preview|escape:"javascript"|escape:"html"}
								{/if}
							{/if}
						{/strip}
					{/capture}
	
					{* Format property values *}
					{if $propname eq 'id' or $propname eq 'name'}
						{if $propname eq 'name' and $propval eq '' and $gal_info.show_name eq 'n'}
							{* show the filename if only name should be displayed but is empty *}
							{assign var=propval value=$files[changes].filename}
							{assign var=propval value="<a class='fgalname namealias' $link>$propval</a>"}
						{else}
							{assign var=propval value="<a class='fgalname' $link>$propval</a>"}
						{/if}
						{if $propname eq 'name' and $gal_info.show_name eq 'n'}
							{if $gal_info.max_desc gt 0}
								{assign var=desc value=$files[changes].description|truncate:$gal_info.max_desc:"...":false|nl2br}
							{else}
								{assign var=desc value=$files[changes].description|nl2br}
							{/if}
							{assign var=propval value="$propval<br><span class=\"description\">`$desc`</span>"}
						{/if}
					{elseif $propname eq 'created' or $propname eq 'lastModif' or $propname eq 'lastDownload'}
						{if empty($propval)}
							{assign var=propval value=''}
						{else}
							{assign var=propval value=$propval|tiki_short_date}
						{/if}
					{elseif $propname eq 'last_user' or $propname eq 'author' or $propname eq 'creator'}
						{assign var=propval value=$propval|userlink}
					{elseif $propname eq 'size'}
						{assign var=propval value=$propval|kbsize:true}
					{elseif $propname eq 'type'}
						{if $files[changes].isgal eq 1}
							{capture assign=propval}{icon _id='folder' class=''}{/capture}
						{else}
							{assign var=propval value=$files[changes].filename|iconify:$files[changes].type}
						{/if}
					{elseif $propname eq 'description' and $gal_info.max_desc gt 0}
						{assign var=propval value=$propval|truncate:$gal_info.max_desc:"...":false|nl2br}
					{elseif $propname eq 'description'}
						{assign var=propval value=$propval|nl2br}
					{elseif $propname eq 'lockedby' and $propval neq ''}
						{if $gal_info.show_lockedby eq 'i' or $gal_info.show_lockedby eq 'a'}
							{assign var=propval value=$propval|username}
							{capture assign=propval}{icon _id='lock_gray' class='' alt="{tr}Locked by:{/tr} "|cat:$propval}{/capture}
						{else}
							{assign var=propval value=$propval|userlink}
						{/if}
					{elseif $propname eq 'backlinks'}
						{if empty($files[changes].nbBacklinks)}
							{assign var=propval value=$files[changes].nbBacklinks}
						{else}
							{assign var=propval value=$files[changes].nbBacklinks}
							{assign var=fid value=$files[changes].id}
							{assign var=propval value="<a class='fgalbacklink' href='list-file_backlinks_ajax.php?fileId=$fid' rel='list-file_backlinks_ajax.php?fileId=$fid'>$propval</a>"}
						{/if}
					{elseif $propname eq 'deleteAfter'}
						{if empty($files[changes].deleteAfter)}
							{assign var=propval value="-"}
						{else}
							{assign var=limitdate value=$files[changes].deleteAfter+$files[changes].lastModif}
							{assign var=propval value=$limitdate|tiki_remaining_days_from_now:$prefs.short_date_format}
						{/if}
					{elseif $propname eq 'share'}
						{assign var=share_string value=$files[changes].share.string}
						{assign var=share_nb value=$files[changes].share.nb}
						{capture assign=share_capture}{strip}
							<a class='fgalname' href='#' {popup fullhtml=1 text=$over_share|escape:'javascript'|escape:'html' left=true} style='cursor:help'>{icon _id='group_link' class='' title=''}</a> ({$share_nb}) {$share_string}
						{/strip}{/capture}
						{assign var=propval value=$share_capture}
					{elseif $propname eq 'hits'}
						{if $prefs.fgal_list_hits eq 'y'}
							{if $prefs.fgal_list_ratio_hits eq 'y'}
								{assign var=hits value=$files[changes].hits}
								{assign var=maxhits value=$files[changes].maxhits}
								{if $maxhits <= 0}
									{assign var=propval value=$hits}
								{else}
									{assign var=propval value="$hits / <b>$maxhits</b>"}
								{/if}
							{else}
								{assign var=propval value=$files[changes].hits}
							{/if}
						{/if}
					{/if}
					{if $propname eq 'name' and ( $gal_info.show_name eq 'a' or $gal_info.show_name eq 'f' )}
						<td>
							{if $link neq ''}<a class='fgalname fileLink' fileId='{$files[changes].id}' type='{$files[changes].type}' {$link}>{/if}{$files[changes].filename|escape}{if $link neq ''}</a>{/if}
						</td>
					{/if}
	
					{if $other_columns_selected neq '' and $propname eq $other_columns_selected}
						{assign var=other_columns_selected_val value=$propval}
					{else}
						{if !($galleryId eq 0 and $propname eq 'lockedby') and ($propname neq 'name' or ( $gal_info.show_name eq 'a' or $gal_info.show_name eq 'n' )) and ($propname neq 'description' or $gal_info.show_name neq 'n')}
							<td>{$propval}</td>
						{/if}
					{/if}
				{/if}
			{/foreach}

			{if $other_columns_selected neq ''}
				<td>{$other_columns_selected_val}</td>
			{/if}

			{if ( $prefs.use_context_menu_icon neq 'y' and $prefs.use_context_menu_text neq 'y' ) or $gal_info.show_action eq 'y' or $prefs.javascript_enabled neq 'y'}
				<td>{include file='fgal_context_menu.tpl' changes=$smarty.section.changes.index}</td>
			{/if}

			{if ( $other_columns neq '' or $other_columns_selected neq '' ) and $prefs.javascript_enabled eq 'y'}
				<td>
					{if $show_infos eq 'y'}
						{if $over_infos eq ''}
							{icon _id='information_gray' class='' alt="{tr}No information{/tr}"}
						{else}
							<a class="fgalname" href="#" {popup fullhtml="1" text=$over_infos|escape:"javascript"|escape:"html" left=true} style="cursor:help">{icon _id='information' class='' title=''}</a>
						{/if}
					{/if}
				</td>
			{/if}
		</tr>
	{/if}
	{sectionelse}
		{norecords _colspan=$nbCols}
	{/section}
	{if $gal_info.show_checked ne 'n' and $tiki_p_admin_file_galleries eq 'y' and $prefs.javascript_enabled eq 'y'}
		<tr>
			<td colspan="{$nbCols}">
				{select_all checkbox_names='file[],subgal[]' label="{tr}Select All{/tr}"}
			</td>
		</tr>
	{/if}


</table>
{if $prefs.feature_jquery_tooltips eq 'y'}
	{jq}$('a.fgalbacklink').cluetip({showTitle:false, sticky:true});{/jq}
{/if}
