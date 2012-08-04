{* $Id: tiki-show_page.tpl 41164 2012-04-29 06:01:12Z lindonb $ *}
{if !isset($pageLang)}
	{if isset($info.lang)}
		{assign var='pageLang' value=$info.lang}
	{else}
		{assign var='pageLang' value=''}
	{/if}
{/if}
		
{if !isset($hide_page_header) or !$hide_page_header}
	{if $prefs.feature_siteloc eq 'page' and $prefs.feature_breadcrumbs eq 'y'}
		{if $prefs.feature_siteloclabel eq 'y'}{tr}Location : {/tr}{/if}
		{breadcrumbs type="trail" loc="page" crumbs=$crumbs}
		{if $prefs.feature_page_title eq 'y'}
			{breadcrumbs type="pagetitle" loc="page" crumbs=$crumbs machine_translate=$machine_translate_to_lang source_lang=$pageLang target_lang=$machine_translate_to_lang}
		{/if}
	{/if}

{include file='tiki-flaggedrev_approval_header.tpl'}

{/if} {*hide_page_header*}

{if !$prefs.wiki_topline_position or $prefs.wiki_topline_position eq 'top' or $prefs.wiki_topline_position eq 'both'}
	{include file='tiki-wiki_topline.tpl'}
{/if}

{if $print_page ne 'y'}
	{if $prefs.page_bar_position eq 'top'}
		{include file='tiki-page_bar.tpl'}
	{/if}
{/if}

{if isset($saved_msg) && $saved_msg neq ''}
	{remarksbox type="note" title="{tr}Note{/tr}"}{$saved_msg}{/remarksbox}
{/if}

{if $user and $prefs.feature_user_watches eq 'y' and $category_watched eq 'y'}
	<div class="categbar" style="clear: both; text-align: right">
		{tr}Watched by categories:{/tr}
		{section name=i loop=$watching_categories}
			<a href="tiki-browse_categories.php?parentId={$watching_categories[i].categId}">{$watching_categories[i].name|escape}</a>&nbsp;
		{/section}
	</div>
{/if}

{if $prefs.feature_urgent_translation eq 'y'}
	{section name=i loop=$translation_alert}
		<div class="cbox">
			<div class="cbox-title">
				{icon _id=information style="vertical-align:middle"} {tr}Content may be out of date{/tr}
			</div>
			<div class="cbox-data">
				<p>
					{tr}An urgent request for translation has been sent. Until this page is updated, you can see a corrected version in the following pages:{/tr}
				</p>
				<ul>
					{section name=j loop=$translation_alert[i]}
						<li>
							<a href="{$translation_alert[i][j].page|sefurl:wiki:with_next}no_bl=y">
								{$translation_alert[i][j].page|escape}
							</a>
							({$translation_alert[i][j].lang})
							{if $editable and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y'} 
								<a href="tiki-editpage.php?page={$page|escape:'url'}&amp;source_page={$translation_alert[i][j].page|escape:'url'}&amp;oldver={$translation_alert[i][j].last_update|escape:'url'}&amp;newver={$translation_alert[i][j].current_version|escape:'url'}&amp;diff_style=htmldiff" title="{tr}update from it{/tr}">
									{icon _id=arrow_refresh alt="{tr}update from it{/tr}" style="vertical-align:middle"}
								</a>
							{/if}
						</li>
					{/section}
				</ul>
			</div>
		</div>
	{/section}
{/if}

<article id="top" class="wikitext clearfix{if $prefs.feature_page_title neq 'y'} nopagetitle{/if}">
	{if !isset($hide_page_header) or !$hide_page_header}
		{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and isset($freetags.data[0]) and $prefs.freetags_show_middle eq 'y'}
			{include file='freetag_list.tpl'}
		{/if}

		{if $pages > 1 and $prefs.wiki_page_navigation_bar neq 'bottom'}
			<div class="center navigation_bar pagination position_top">
				<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$first_page}">{icon _id='resultset_first' alt="{tr}First page{/tr}"}</a>

				<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$prev_page}">{icon _id='resultset_previous' alt="{tr}Previous page{/tr}"}</a>

				<small>{tr _0=$pagenum _1=$pages}page: %0/%1{/tr}</small>

				<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$next_page}">{icon _id='resultset_next' alt="{tr}Next page{/tr}"}</a>

				<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$last_page}">{icon _id='resultset_last' alt="{tr}Last page{/tr}"}</a>
			</div>
		{/if}

		{if $prefs.feature_page_title eq 'y'}
			<h1 class="pagetitle">{breadcrumbs type="pagetitle" loc="page" crumbs=$crumbs machine_translate=$machine_translate_to_lang source_lang=$pageLang target_lang=$machine_translate_to_lang}</h1>
		{/if}

		{if $structure eq 'y' and ($prefs.wiki_structure_bar_position ne 'bottom')}
			{include file='tiki-wiki_structure_bar.tpl'}
		{/if}

		{if $prefs.feature_wiki_ratings eq 'y'}
			{include file='poll.tpl'}
		{/if}

		{if $prefs.wiki_simple_ratings eq 'y' && $tiki_p_assign_perm_wiki_page eq 'y'}
			<form method="post" action="">
				{rating type="wiki page" id=$page_id}
			</form>
		{/if}
	{/if} {*hide_page_header*}

	{if $machine_translate_to_lang != ''}
		{remarksbox type="warning" title="{tr}Warning{/tr}" highlight="y"}
			{tr}This text was automatically translated by Google Translate from the following page: {/tr}<a href="tiki-index.php?page={$page}">{$page}</a>
		{/remarksbox}
	{/if}
	
	<div id="page-data">
		{if isset($pageLang) and ($pageLang eq 'ar' or $pageLang eq 'he')}
			<div style="direction:RTL; unicode-bidi:embed; text-align: right; {if $pageLang eq 'ar'}font-size: large;{/if}">
				{$parsed}
			</div>
		{else}
			{$parsed}
		{/if}
	</div>
	
	{* Information below the wiki content must not overlap the wiki content that could contain floated elements *}
	<hr class="hrwikibottom" /> 

	{if $structure eq 'y' and (($prefs.wiki_structure_bar_position eq 'bottom') or ($prefs.wiki_structure_bar_position eq 'both'))}
		{include file='tiki-wiki_structure_bar.tpl'}
	{/if}

	{if $pages > 1 and $prefs.wiki_page_navigation_bar neq 'top'}
		<br />
		<div class="center navigation_bar pagination position_bottom">
			<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$first_page}">{icon _id='resultset_first' alt="{tr}First page{/tr}"}</a>

			<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$prev_page}">{icon _id='resultset_previous' alt="{tr}Previous page{/tr}"}</a>

			<small>{tr _0=$pagenum _1=$pages}page: %0/%1{/tr}</small>

			<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$next_page}">{icon _id='resultset_next' alt="{tr}Next page{/tr}"}</a>

			<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$last_page}">{icon _id='resultset_last' alt="{tr}Last page{/tr}"}</a>
		</div>
	{/if}
</article> {* End of main wiki page *}

{if $has_footnote eq 'y'}
	<div class="wikitext" id="wikifootnote">{$footnote}</div>
{/if}

<p class="editdate">
	{if isset($wiki_authors_style) && $wiki_authors_style neq 'none'}
		{include file='wiki_authors.tpl'}
	{/if}

	{include file='show_copyright.tpl'}

	{if $print_page eq 'y'}
		<br />
		{capture name=url}{$base_url}{$page|sefurl}{if !empty($smarty.request.itemId)}&amp;itemId={$smarty.request.itemId}{/if}{/capture}
		{tr}The original document is available at{/tr} <a href="{$smarty.capture.url}">{$smarty.capture.url}</a>
	{/if}
</p>

{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categoryobjects eq 'y'}
	{$display_catobjects}
{/if}
{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.category_morelikethis_algorithm ne ''}
	{include file='category_related_objects.tpl'}
{/if}

{if $prefs.wiki_topline_position eq 'bottom' or $prefs.wiki_topline_position eq 'both'}
	{include file='tiki-wiki_topline.tpl'}
{/if}

{if $print_page ne 'y'}
	{if (!$prefs.page_bar_position or $prefs.page_bar_position eq 'bottom' or $prefs.page_bar_position eq 'both') and $machine_translate_to_lang == ''}
		{include file='tiki-page_bar.tpl'}
	{/if}
{/if}
