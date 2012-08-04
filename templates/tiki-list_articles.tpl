{* $Id: tiki-list_articles.tpl 39832 2012-02-14 09:59:27Z gezzzan $ *}

{title help="Articles" admpage="articles"}{tr}Articles{/tr}{/title}

<div class="navbar">
	{if $tiki_p_edit_article eq 'y'}
		{button href="tiki-edit_article.php" _text="{tr}New Article{/tr}"}
	{/if}
	{button href="tiki-view_articles.php" _text="{tr}View Articles{/tr}"}

	{if $prefs.feature_submissions == 'y' && ($tiki_p_approve_submission == "y" || $tiki_p_remove_submission == "y" || $tiki_p_edit_submission == "y")}
		{button href="tiki-list_submissions.php" _text="{tr}View Submissions{/tr}"}
	{/if}
</div>

{if $listpages or ($find ne '') or ($types ne '') or ($topics ne '') or ($lang ne '') or ($categId ne '')}
	{include file='find.tpl' find_show_languages='y' find_show_categories_multi='y' find_show_num_rows='y' find_show_date_range='y'}
{/if}

{if $mapview}
{wikiplugin _name="googlemap" type="objectlist" width="400" height="400"}{/wikiplugin}
{/if}

<form name="checkform" method="get" action="{$smarty.server.PHP_SELF}">
	<input type="hidden" name="maxRecords" value="{$maxRecords|escape}" />
	{assign var=numbercol value=1}
	<table class="normal">
		<tr>
			<th class="auto">
				{if $listpages}
				   {select_all checkbox_names='checked[]'}
				{/if}
			</th>
			{if $prefs.art_list_title eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th>{self_link _sort_arg='sort_mode' _sort_field='title'}{tr}Title{/tr}{/self_link}</th>
			{/if}
			{if $prefs.art_list_id eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th>{tr}Id{/tr}</th>
			{/if}
			{if $prefs.art_list_type eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th>{self_link _sort_arg='sort_mode' _sort_field='type'}{tr}Type{/tr}{/self_link}</th>
			{/if}
			{if $prefs.art_list_topic eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th>{self_link _sort_arg='sort_mode' _sort_field='topicName'}{tr}Topic{/tr}{/self_link}</th>
			{/if}
			{if $prefs.art_list_date eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th>{self_link _sort_arg='sort_mode' _sort_field='publishDate'}{tr}Publish Date{/tr}{/self_link}</th>
			{/if}
			{if $prefs.art_list_expire eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th>{self_link _sort_arg='sort_mode' _sort_field='expireDate'}{tr}Expiry Date{/tr}{/self_link}</th>
			{/if}
			{if $prefs.art_list_visible eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th><span>{tr}Visible{/tr}</span></th>
			{/if}
			{if $prefs.art_list_lang eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th>{self_link _sort_arg='sort_mode' _sort_field='lang'}{tr}Language{/tr}{/self_link}</th>
			{/if}
			{if $prefs.art_list_author eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th>{self_link _sort_arg='sort_mode' _sort_field='author'}{tr}User{/tr}{/self_link}</th>
			{/if}
			{if $prefs.art_list_authorName eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th>{self_link _sort_arg='sort_mode' _sort_field='authorName'}{tr}Author{/tr}{/self_link}</th>
			{/if}
			{if $prefs.art_list_rating eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th style="text-align:right;">
					{self_link _sort_arg='sort_mode' _sort_field='rating'}{tr}Rating{/tr}{/self_link}
				</th>
			{/if}
			{if $prefs.art_list_reads eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th style="text-align:right;">
					{self_link _sort_arg='sort_mode' _sort_field='nbreads'}{tr}Reads{/tr}{/self_link}
				</th>
			{/if}
			{if $prefs.art_list_size eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th style="text-align:right;">{self_link _sort_arg='sort_mode' _sort_field='size'}{tr}Size{/tr}{/self_link}</th>
			{/if}
			{if $prefs.art_list_img eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th>{tr}Image{/tr}</th>
			{/if}
			<th>{self_link _sort_arg='sort_mode' _sort_field='ispublished'}{tr}Published{/tr}{/self_link}</th>
			{if $tiki_p_edit_article eq 'y' or $tiki_p_remove_article eq 'y' or isset($oneEditPage) or $tiki_p_read_article}
				{assign var=numbercol value=$numbercol+1}
				<th>{tr}Actions{/tr}</th>
			{/if}
		</tr>
		{cycle values="odd,even" print=false}
		{section name=changes loop=$listpages}
			<tr class="{cycle}">
				<td class="checkbox">
					<input type="checkbox" name="checked[]" value="{$listpages[changes].articleId|escape}" {if $listpages[changes].checked eq 'y'}checked="checked" {/if}/>
				</td>
				{if $prefs.art_list_title eq 'y'}
					<td class="text">
						{if $tiki_p_read_article eq 'y'}
							{object_link type=article id=$listpages[changes].articleId title=$listpages[changes].title|truncate:$prefs.art_list_title_len:"...":true}
						{else}
							{$listpages[changes].title|truncate:$prefs.art_list_title_len:"...":true|escape}
						{/if}
					</td>
				{/if}
				{if $prefs.art_list_id eq 'y'}
					<td class="integer">{$listpages[changes].articleId}</td>
				{/if}
				{if $prefs.art_list_type eq 'y'}
					<td class="text">{tr}{$listpages[changes].type|escape}{/tr}</td>
				{/if}
				{if $prefs.art_list_topic eq 'y'}
					<td class="text">{$listpages[changes].topicName|escape}</td>
				{/if}
				{if $prefs.art_list_date eq 'y'}
					<td class="date">{$listpages[changes].publishDate|tiki_short_datetime}</td>
				{/if}
				{if $prefs.art_list_expire eq 'y'}
					<td class="date">{$listpages[changes].expireDate|tiki_short_datetime}</td>
				{/if}
				{if $prefs.art_list_visible eq 'y'}
					<td class="text">{tr}{$listpages[changes].disp_article}{/tr}</td>
				{/if}
				{if $prefs.art_list_lang eq 'y'}
					<td class="text">{tr}{$listpages[changes].lang}{/tr}</td>
				{/if}
				{if $prefs.art_list_author eq 'y'}
					<td class="text">{$listpages[changes].author|escape}</td>
				{/if}
				{if $prefs.art_list_authorName eq 'y'}
					<td class="text">{$listpages[changes].authorName|escape}</td>
				{/if}
				{if $prefs.art_list_rating eq 'y'}
					<td class="integer">{$listpages[changes].rating}</td>
				{/if}
				{if $prefs.art_list_reads eq 'y'}
					<td class="integer">{$listpages[changes].nbreads}</td>
				{/if}
				{if $prefs.art_list_size eq 'y'}
					<td class="integer">{$listpages[changes].size|kbsize}</td>
				{/if}
				{if $prefs.art_list_img eq 'y'}
					<td class="text">{tr}{$listpages[changes].hasImage}{/tr}/{tr}{$listpages[changes].useImage}{/tr}</td>
				{/if}
				<td style="text-align:center;">{$listpages[changes].ispublished}</td>
				<td class="action">
					{if $tiki_p_read_article eq 'y'}
						<a href="{$listpages[changes].articleId|sefurl:article}" title="{$listpages[changes].title|escape}">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>
					{/if}
					{if $tiki_p_edit_article eq 'y' or ($listpages[changes].author eq $user and $listpages[changes].creator_edit eq 'y')}
						<a class="link" href="tiki-edit_article.php?articleId={$listpages[changes].articleId}">{icon _id='page_edit'}</a>
					{/if}
					{if $tiki_p_admin_cms eq 'y' or $tiki_p_assign_perm_cms eq 'y'}
						<a class="link" href="tiki-objectpermissions.php?objectName={$listpages[changes].title|escape:'url'}&amp;objectType=article&amp;permType=cms&amp;objectId={$listpages[changes].articleId}">{icon _id='key' alt="{tr}Permissons{/tr}"}</a>
					{/if}
					{if $tiki_p_remove_article eq 'y'}
						&nbsp;
						<a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].articleId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=$numbercol}
		{/section}
		<tr>
			{assign var=numbercol value=$numbercol+1}
			<td colspan="{$numbercol}">
				{if $listpages}
					<p align="left"> {*on the left to have it close to the checkboxes*}
						{button _text="{tr}Select Duplicates{/tr}" _onclick="checkDuplicateRows(this,'td:not(:eq(2))'); return false;"}
						<label>{tr}Perform action with checked:{/tr}
							<select name="submit_mult">
								<option value=""></option>
								<option value="remove_articles" >{tr}Remove{/tr}</option>
							</select>
						</label>
						<input type="submit" value="{tr}OK{/tr}" />
					</p>
				{/if}
			</td>
		</tr>
	</table>

	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
</form>
