{* $Id: tiki-view_tracker_item.tpl 42382 2012-07-15 20:11:47Z changi67 $ *}
{title help="trackers"}{$tracker_info.name}{/title}

{if ! isset($print_page) || $print_page ne 'y'}

	{* --------- navigation ------ *}
	<div class="navbar">
		{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
			<a href="tiki-object_watches.php?objectId={$itemId|escape:"url"}&amp;watch_event=tracker_item_modified&amp;objectType=tracker+{$trackerId}&amp;objectName={$tracker_info.name|escape:"url"}&amp;objectHref={'tiki-view_tracker_item.php?trackerId='|cat:$trackerId|cat:'&itemId='|cat:$itemId|escape:"url"}" class="icon">{icon _id='eye_group' alt="{tr}Group Monitor{/tr}" align='right' hspace='1'}</a>
		{/if}
		{if $prefs.feature_user_watches eq 'y' and $tiki_p_watch_trackers eq 'y'}
			{if $user_watching_tracker ne 'y'}
				<a href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;watch=add" title="{tr}Monitor{/tr}">{icon _id='eye' align="right" hspace="1" alt="{tr}Monitor{/tr}"}</a>
			{else}
				<a href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;watch=stop" title="{tr}Stop Monitor{/tr}">{icon _id='no_eye' align="right" hspace="1" alt="{tr}Stop Monitor{/tr}"}</a>
			{/if}
		{/if}
		{self_link print='y'}{icon _id='printer' align='right' hspace='1' alt="{tr}Print{/tr}"}{/self_link}
		{if $item_info.logs.cant}
			<a class="link" href="tiki-tracker_view_history.php?itemId={$itemId}" title="{tr}History{/tr}">{icon _id='database' align='right' alt="{tr}History{/tr}"}</a>
		{/if}
		{include file="tracker_actions.tpl"}
	</div>

	<div class="categbar" align="right">
		{if $user and $prefs.feature_user_watches eq 'y'}
			{if $category_watched eq 'y'}
				{tr}Watched by categories:{/tr}
				{section name=i loop=$watching_categories}
					<a href="tiki-browse_categories.php?parentId={$watching_categories[i].categId}">{$watching_categories[i].name|escape}</a>&nbsp;
				{/section}
			{/if}
		{/if}
	</div>

	{* ------- return/next/previous tab --- *}
	{if $canView}
		{pagination_links cant=$cant offset=$offset reloff=$smarty.request.reloff itemname="{tr}Item{/tr}"}
			{* Do not specify an itemId in URL used for pagination, because it will use the specified itemId instead of moving to another item *}
			{$smarty.server.php_self}?{query itemId=NULL trackerId=$trackerId}
		{/pagination_links}
	{/if}

	{include file='tracker_error.tpl'}
{/if}{*print_page*}

{tabset name='tabs_view_tracker_item'}

	{tab name="{tr}View{/tr}"}
		{* --- tab with view ------------------------------------------------------------------------- *}
		{if empty($tracker_info.viewItemPretty)}
			<h2>{tr}View Item{/tr}</h2>
			{if $tracker_is_multilingual}
				<div class="translations">
					<a href="{service controller=translation action=manage type=trackeritem source=$itemId}">{tr}Translations{/tr}</a>
				</div>
				{jq}
					$('.translations a').click(function () {
						var link = this;
						$(this).serviceDialog({
							title: $(link).text(),
							data: {
								controller: 'translation',
								action: 'manage',
								type: 'trackeritem',
								source: "{{$itemId|escape}}"
							}
						});
						return false;
					});
				{/jq}
			{/if}
			<table class="formcolor">
				{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
					{assign var=ustatus value=$info.status|default:"p"}
					<tr>
						<td class="formlabel">{tr}Status{/tr}</td><td>{$status_types.$ustatus.label}</td>
						<td colspan="2">{html_image file=$status_types.$ustatus.image title=$status_types.$ustatus.label alt=$status_types.$ustatus.label}</td>
					</tr>
				{/if}
				{assign var=stick value="n"}

				{foreach from=$fields key=ix item=cur_field}
					{if !($tracker_info.doNotShowEmptyField eq 'y' and empty($cur_field.value) and empty($cur_field.cat) and empty($cur_field.links) and
							$cur_field.type ne 's' and $cur_field.type ne 'STARS' and $cur_field.type ne 'h' and $cur_field.type ne 'l') and
							($cur_field.options_array[0] ne 'password')}
					
						<tr class="field{$cur_field.fieldId}">
							<td class="formlabel" >
								{$cur_field.name|escape}
							</td>
							<td class="formcontent">
								{trackeroutput field=$cur_field item=$item_info showlinks=n list_mode=n inTable=y}
							</td>
						</tr>
					{/if}
				{/foreach}
				{if $tracker_info.showCreatedView eq 'y'}
					<tr>
						<td class="formlabel">{tr}Created{/tr}</td>
						<td colspan="3" class="formcontent">{$info.created|tiki_long_datetime}{if $tracker_info.showCreatedBy eq 'y'}<br />by {if $prefs.user_show_realnames eq 'y'}{if empty($info.createdBy)}Unknown{else}{$info.createdBy|username}{/if}{else}{if empty($info.createdBy)}Unknown{else}{$info.createdBy}{/if}{/if}{/if}</td>
					</tr>
				{/if}
				{if $tracker_info.showLastModifView eq 'y'}
					<tr>
						<td class="formlabel">{tr}LastModif{/tr}</td>
						<td colspan="3" class="formcontent">{$info.lastModif|tiki_long_datetime}{if $tracker_info.showLastModifBy eq 'y'}<br />by {if $prefs.user_show_realnames eq 'y'}{if empty($info.lastModifBy)}Unknown{else}{$info.lastModifBy|username}{/if}{else}{if empty($info.lastModifBy)}Unknown{else}{$info.lastModifBy}{/if}{/if}{/if}</td>
					</tr>
				{/if}
			</table>

		{else}
			{if $canModify}
				{include file='tracker_pretty_item.tpl' item=$item_info fields=$ins_fields wiki=$tracker_info.viewItemPretty}
			{elseif $canView}
				{include file='tracker_pretty_item.tpl' item=$item_info fields=$fields wiki=$tracker_info.viewItemPretty}
			{/if}
		{/if}

	{/tab}

	{* -------------------------------------------------- tab with comments --- *}
	{if $tracker_info.useComments eq 'y' and ($tiki_p_tracker_view_comments ne 'n' or $tiki_p_comment_tracker_items ne 'n')}

		{tab name="{tr}Comments{/tr}"}

			<div id="comment-container" data-target="{service controller=comment action=list type=trackeritem objectId=$itemId}"></div>
			{jq}
				var id = '#comment-container';
				$(id).comment_load($(id).data('target'));
			{/jq}

		{/tab}
	{/if}

{* ---------------------------------------- tab with attachments --- *}
{if $tracker_info.useAttachments eq 'y' and $tiki_p_tracker_view_attachments eq 'y'}
	{tab name="{tr}Attachments{/tr} (`$attCount`)"}
		{include file='attachments_tracker.tpl'}
	{/tab}
{/if}

{* --------------------------------------------------------------- tab with edit --- *}
{if (! isset($print_page) || $print_page ne 'y') && $canModify}
	{tab name=$editTitle}
		<h2>{tr}Edit Item{/tr}</h2>

		<div class="nohighlight">
			{include file="tracker_validator.tpl"}

			{if  $tiki_p_admin_trackers eq 'y' and !empty($trackers)}	
				<form>
					<input type="hidden" name="itemId" value="{$itemId}" />
					<select name="moveto">
						{foreach from=$trackers item=tracker}
							{if $tracker.trackerId ne $trackerId}
								<option value="{$tracker.trackerId}">{$tracker.name|escape}</option>
							{/if}
						{/foreach}
					</select>
					<input type="submit" name="go" value="{tr}Move to another tracker{/tr}" />
				</form>
			{/if}

			<form enctype="multipart/form-data" action="tiki-view_tracker_item.php" method="post" id="editItemForm">
				{if $special}
					<input type="hidden" name="view" value=" {$special}" />
				{else}
					<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
					<input type="hidden" name="itemId" value="{$itemId|escape}" />
				{/if}
				{if $from}
					<input type="hidden" name="from" value="{$from}" />
				{/if}
				{section name=ix loop=$fields}
					{if !empty($fields[ix].value)}
						<input type="hidden" name="{$fields[ix].name|escape}" value="{$fields[ix].value|escape}" />
					{/if}
				{/section}
				{if $cant}
					<input type="hidden" name="cant" value="{$cant}" />
				{/if}

				{remarksbox type="note"}<em class='mandatory_note'>{tr}Fields marked with a * are mandatory.{/tr}</em>{/remarksbox}

				<table class="formcolor">
					<tr>
						<td colspan="2">
							{if count($fields) >= 5}
								<input type="submit" name="save" value="{tr}Save{/tr}" onclick="needToConfirm=false" />
								{* --------------------------- to return to tracker list after saving --------- *}
								{if $canView}
									<input type="submit" name="save_return" value="{tr}Save{/tr} &amp; {tr}Back to Items list{/tr}" onclick="needToConfirm=false" />
									{if $canRemove}
										<a class="link" href="tiki-view_tracker.php?trackerId={$trackerId}&amp;remove={$itemId}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
									{/if}
								{/if}
							{/if}
						</td>
					</tr>
					{* ------------------- *}
					{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
						<tr>
							<td class="formlabel">{tr}Status{/tr}</td>
							<td class="formcontent">
								{include file='tracker_status_input.tpl' item=$item_info form_status=edstatus}
							</td>
						</tr>
					{/if}

					{if empty($tracker_info.editItemPretty)}

						{foreach from=$ins_fields key=ix item=cur_field}
							<tr>
								<td>
									{$cur_field.name}
									{if $cur_field.isMandatory eq 'y'}
										<em class='mandatory_star'>*</em>
									{/if}
								</td>
								<td>
									{trackerinput field=$cur_field item=$item_info inTable=formcolor showDescription=y}
								</td>
							</tr>
						{/foreach}

						{trackerheader level=-1 title='' inTable='formcolor'}

					{else}
						<tr>
							<td colspan="4">
								{wikiplugin _name=tracker trackerId=$trackerId itemId=$itemId view=page wiki=$tracker_info.editItemPretty formtag='n'}{/wikiplugin}
							</td>
						</tr>
					{/if}

				{if $groupforalert ne ''}

					<tr>
						<td>{tr}Choose users to alert{/tr}</td>
						<td>
							{section name=idx loop=$listusertoalert}
								{if $showeachuser eq ''}
									<input type="hidden"  name="listtoalert[]" value="{$listusertoalert[idx].user}">
								{else}
									<input type="checkbox" name="listtoalert[]" value="{$listusertoalert[idx].user}"> {$listusertoalert[idx].user}
								{/if}
							{/section}
						</td>
					</tr>
				{/if}


				{* -------------------- antibot code -------------------- *}
				{if $prefs.feature_antibot eq 'y' && $user eq ''}
					{include file='antibot.tpl'}
				{/if}
				<tr>
					<td colspan="2">
						<input type="submit" name="save" value="{tr}Save{/tr}" onclick="needToConfirm=false" />
						{* --------------------------- to return to tracker list after saving --------- *}
						{if $canView}
							<input type="submit" name="save_return" value="{tr}Save{/tr} &amp; {tr}Back to Items List{/tr}" onclick="needToConfirm=false" />
						{/if}
						{if $canRemove}
							<a class="link" href="tiki-view_tracker.php?trackerId={$trackerId}&amp;remove={$itemId}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
						{/if}
						{if $item_info.logs.cant}
							<a class="link" href="tiki-tracker_view_history.php?itemId={$itemId}" title="{tr}History{/tr}">{icon _id='database' alt="{tr}History{/tr}"}</a>
						{/if}
						{if $tiki_p_admin_trackers eq 'y' && empty($trackers)}
							<a class="link" href="tiki-view_tracker_item.php?itemId={$itemId}&moveto" title="{tr}Move to another tracker{/tr}">{icon _id='arrow_right' alt="{tr}Move to another tracker{/tr}"}</a>
						{/if}
					</td>
				</tr>
			</table>
		
			{* ------------------- *}
		</form>

		{foreach from=$ins_fields item=cur_field}
			{if $cur_field.type eq 'x'}
				{capture name=trkaction}
					{if $cur_field.options_array[1] eq 'post'}
						<form action="{$cur_field.options_array[2]}" method="post">
					{else}
						<form action="{$cur_field.options_array[2]}" method="get">
					{/if}
					{section name=tl loop=$cur_field.options_array start=3}
						{assign var=valvar value=$cur_field.options_array[tl]|regex_replace:"/^[^:]*:/":""|escape}
						{if $info.$valvar eq ''}
							{assign var=valvar value=$cur_field.options_array[tl]|regex_replace:"/^[^\=]*\=/":""|escape}
							<input type="hidden" name="{$cur_field.options_array[tl]|regex_replace:"/\=.*$/":""|escape}" value="{$valvar|escape}" />
						{else}
							<input type="hidden" name="{$cur_field.options_array[tl]|regex_replace:"/:.*$/":""|escape}" value="{$info.$valvar|escape}" />
						{/if}
					{/section}
					<table class="formcolor">
						<tr>
							<td>{$cur_field.name}</td>
							<td><input type="submit" name="trck_act" value="{$cur_field.options_array[0]|escape}" /></td>
						<tr>
					</table>
					</form>
				{/capture}
				{assign var=trkact value=$trkact|cat:$smarty.capture.trkaction}
			{/if}
		{/foreach}
		{if $trkact}
			<h2>{tr}Special Operations{/tr}</h2>
			{$trkact}
		{/if}
	</div><!--nohighlight-->{*important comment to delimit the zone not to highlight in a search result*}
{/tab}
{/if}

{/tabset}
<br /><br />

{if isset($print_page) && $print_page eq 'y'}
	{tr}The original document is available at{/tr} <a href="{$base_url|escape}{$itemId|sefurl:trackeritem}">{$base_url|escape}{$itemId|sefurl:trackeritem}</a>
{/if}
