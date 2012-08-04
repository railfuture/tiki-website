{* $Id: tiki-edit_article.tpl 41833 2012-06-07 12:28:33Z jonnybradley $ *}
{* Note: if you edit this file, make sure to make corresponding edits on tiki-edit_submission.tpl*}

{include file='tiki-articles-js.tpl'}

{title help="Articles" admpage="articles"}
	{if $articleId}
		{tr}Edit:{/tr} {$arttitle}
	{else}
		{tr}Edit article{/tr}
	{/if}
{/title}

<div class="navbar">
	{button href="tiki-list_articles.php" _text="{tr}List Articles{/tr}"}
	{button href="tiki-view_articles.php" _text="{tr}View Articles{/tr}"}
</div>

{if $preview}
	<h2>{tr}Preview{/tr}</h2>
	
	{include file='article.tpl'}
{/if}

{if !empty($errors)}
	<div class="simplebox highlight">
		{tr}One of the email addresses you typed is invalid{/tr}
		<br />
		{foreach from=$errors item=m name=errors}
			{$m}
			{if !$smarty.foreach.errors.last}<br />{/if}
		{/foreach}
	</div>
{/if}

<form enctype="multipart/form-data" method="post" action="tiki-edit_article.php" id='editpageform'>
	<input type="hidden" name="articleId" value="{$articleId|escape}" />
	<input type="hidden" name="previewId" value="{$previewId|escape}" />
	<input type="hidden" name="imageIsChanged" value="{$imageIsChanged|escape}" />
	<input type="hidden" name="image_data" value="{$image_data|escape}" />
	<input type="hidden" name="useImage" value="{$useImage|escape}" />
	<input type="hidden" name="image_type" value="{$image_type|escape}" />
	<input type="hidden" name="image_name" value="{$image_name|escape}" />
	<input type="hidden" name="image_size" value="{$image_size|escape}" />
	{if !empty($translationOf)}
		<input type="hidden" name="translationOf" value="{$translationOf|escape}" />
	{/if}
	<table class="formcolor">
		<tr id='show_topline' {if $types.$type.show_topline eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Topline{/tr} *</td>
			<td>
				<input type="text" name="topline" value="{$topline|escape}" size="60" />
			</td>
		</tr>
		<tr>
			<td>{tr}Title{/tr}</td>
			<td>
				<input type="text" name="title" value="{$arttitle|escape}" maxlength="255" size="60" />
			</td>
		</tr>
		<tr id='show_subtitle' {if $types.$type.show_subtitle eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Subtitle{/tr} *</td>
			<td>
				<input type="text" name="subtitle" value="{$subtitle|escape}" size="60" />
			</td>
		</tr>
		<tr id='show_linkto' {if $types.$type.show_linkto eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Source{/tr} ({tr}URL{/tr}) *</td>
			<td>
				<input type="text" name="linkto" value="{$linkto|escape}" size="60" />{if $linkto neq ''}<a href="{$linkto|escape}" target="_blank">{tr}View{/tr}</a>{/if}
			</td>
		</tr>
		{if $prefs.feature_multilingual eq 'y'}
			<tr id='show_lang'>
				<td>{tr}Language{/tr}</td>
				<td>
					<select name="lang">
						<option value="">{tr}All{/tr}</option>
						{section name=ix loop=$languages}
							<option value="{$languages[ix].value|escape}"{if $lang eq $languages[ix].value} selected="selected"{/if}>{$languages[ix].name}</option>
						{/section}
					</select>
					<br />
					{if $articleId != 0}
						{tr _0="tiki-edit_article.php?translationOf=$articleId"}To translate, do not change the language and the content.
						Instead, <a href="%0">create a new translation</a> in the new language.{/tr}
					{/if}
					{if $translations}
						<p>{tr}Edit Translations{/tr}:</p>
						{section loop=$translations name=t}
						{if $articleId != $translations[t].objId}
								{$translations[t].lang|escape}: <a href="tiki-edit_article.php?articleId={$translations[t].objId|escape}">{$translations[t].objName|escape}</a><br />
							{/if}
						{/section}
					{/if}
					{if empty($translationOf)}
						<p>{tr}Attach existing article ID as translation{/tr}: <input name="translationOf" type="text" size="4" /></p>
					{/if}	
				</td>
			</tr>
		{/if}
		<tr id='show_author' {if $types.$type.show_author eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Author Name{/tr}</td>
			<td>
				<input type="text" name="authorName" value="{$authorName|escape}" />
			</td>
		</tr>
		<tr>
			<td>{tr}Topic{/tr}</td>
			<td>
				<select name="topicId">
					{section name=t loop=$topics}
						<option value="{$topics[t].topicId|escape}" {if $topicId eq $topics[t].topicId}selected="selected"{/if}>{$topics[t].name|escape}</option>
					{/section}
					<option value="" {if $topicId eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
				</select>
				{if $tiki_p_admin_cms eq 'y'}
					<a href="tiki-admin_topics.php" class="link">{tr}Admin Topics{/tr}</a>
				{/if}
			</td>
		</tr>
		<tr>
			<td>{tr}Type{/tr}</td>
			<td>
				<select id='articletype' name='type' onchange='javascript:chgArtType();'>
					{foreach from=$types key=typei item=prop}
						<option value="{$typei|escape}" {if $type eq $typei}selected="selected"{/if}>{tr}{$typei|escape}{/tr}</option>
					{/foreach}
				</select>
				{if $tiki_p_admin_cms eq 'y'}
					<a href="tiki-article_types.php" class="link">{tr}Admin Types{/tr}</a>
				{/if}
			</td>
		</tr>
		<tr id='use_ratings' {if $types.$type.use_ratings eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Author Rating{/tr}</td>
			<td>
				<select name='rating'>
					<option value="10" {if $rating eq 10}selected="selected"{/if}>10</option>
					<option value="9.5" {if $rating eq "9.5"}selected="selected"{/if}>9.5</option>
					<option value="9" {if $rating eq 9}selected="selected"{/if}>9</option>
					<option value="8.5" {if $rating eq "8.5"}selected="selected"{/if}>8.5</option>
					<option value="8" {if $rating eq 8}selected="selected"{/if}>8</option>
					<option value="7.5" {if $rating eq "7.5"}selected="selected"{/if}>7.5</option>
					<option value="7" {if $rating eq 7}selected="selected"{/if}>7</option>
					<option value="6.5" {if $rating eq "6.5"}selected="selected"{/if}>6.5</option>
					<option value="6" {if $rating eq 6}selected="selected"{/if}>6</option>
					<option value="5.5" {if $rating eq "5.5"}selected="selected"{/if}>5.5</option>
					<option value="5" {if $rating eq 5}selected="selected"{/if}>5</option>
					<option value="4.5" {if $rating eq "4.5"}selected="selected"{/if}>4.5</option>
					<option value="4" {if $rating eq 4}selected="selected"{/if}>4</option>
					<option value="3.5" {if $rating eq "3.5"}selected="selected"{/if}>3.5</option>
					<option value="3" {if $rating eq 3}selected="selected"{/if}>3</option>
					<option value="2.5" {if $rating eq "2.5"}selected="selected"{/if}>2.5</option>
					<option value="2" {if $rating eq 2}selected="selected"{/if}>2</option>
					<option value="1.5" {if $rating eq "1.5"}selected="selected"{/if}>1.5</option>
					<option value="1" {if $rating eq 1}selected="selected"{/if}>1</option>
					<option value="0.5" {if $rating eq "0.5"}selected="selected"{/if}>0.5</option>
				</select>
			</td>
		</tr>
		{if $prefs.geo_locate_article eq 'y'}
			<tr>
				<td>{tr}Location{/tr}</td>
				<td>
					{$headerlib->add_map()}
					<div class="map-container" data-target-field="geolocation" style="height: 250px; width: 250px;"></div>
					<input type="hidden" name="geolocation" value="{$geolocation_string}" />
				</td>
			</tr>
		{/if}
		<tr id='show_image_1' {if $types.$type.show_image eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Own Image{/tr}</td>
			<td>
				<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
				<input name="userfile1" type="file" onchange="document.getElementById('useImage').checked = true;"/>
				{icon _id='help' alt="{tr}If not the topic image{/tr}"}
			</td>
		</tr>
		{if $hasImage eq 'y'}
			<tr>
				<td>{tr}Own Image{/tr}</td>
				<td>{$image_name} [{$image_type}] ({$image_size} {tr}bytes{/tr})</td>
			</tr>
			<tr>
				<td>{tr}Own Image{/tr}</td>
				{if $imageIsChanged eq 'y'}
					<td>
						<img alt="{tr}Article image{/tr}" src="article_image.php?image_type=preview&amp;id={$previewId}" />
					</td>
				{else}
					<td>
						<img alt="{tr}Article image{/tr}" src="article_image.php?image_type=article&amp;id={$articleId}" />
					</td>
				{/if}
			</tr>
		{/if}
		<tr id='show_image_2' {if $types.$type.show_image eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Use own image{/tr}</td>
			<td>
				<input type="checkbox" name="useImage" id="useImage" {if $useImage eq 'y'}checked='checked'{/if}/>
			</td>
		</tr>
		<tr id='show_image_3' {if $types.$type.show_image eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Float text around image{/tr}</td>
			<td>
				<input type="checkbox" name="isfloat" {if $isfloat eq 'y'}checked='checked'{/if}/>
			</td>
		</tr>
		<tr id='show_image_4' {if $types.$type.show_image eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Maximum dimensions of custom image in view mode (Read Article){/tr}</td>
			<td>
				<label>{tr}Width{/tr}</label> <input type="text" name="image_x"{if $image_x > 0} value="{$image_x|escape}"{/if} /> {tr}pixels{/tr}
				{icon _id='help' alt="{tr}If different than the uploaded image{/tr}"}<br />
				<label>{tr}Height{/tr} <input type="text" name="image_y"{if $image_y > 0} value="{$image_y|escape}"{/if} /></label> {tr}pixels{/tr}
			</td>
		</tr>
		<tr id='show_image_5' {if $types.$type.show_image eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Maximum dimensions of custom image in list mode (View Articles){/tr}</td>
			<td>
				<label>{tr}Width{/tr}</label> <input type="text" name="list_image_x" value="{$list_image_x|escape}" /> {tr}pixels{/tr}
				{icon _id='help' alt="{tr}If different than in view mode{/tr}"}<br />
				<label>{tr}Height{/tr}</label> <input type="text" name="list_image_y" value="{$list_image_y|escape}" /> {tr}pixels{/tr}
				
			</td>
		</tr>
		<tr id='show_image_caption' {if $types.$type.show_image_caption eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Image caption{/tr} *</td>
			<td>
				<input type="text" name="image_caption" value="{$image_caption|escape}" size="60" />
				{icon _id='help' alt="{tr}If not the topic name{/tr}"}
			</td>
		</tr>

		{if $prefs.feature_cms_templates eq 'y' and $tiki_p_use_content_templates eq 'y'}
			<tr>
				<td>{tr}Apply template{/tr}</td>
				<td>
					<select name="templateId" onchange="javascript:document.getElementById('editpageform').submit();">
						<option value="0">{tr}none{/tr}</option>
						{section name=ix loop=$templates}
							<option value="{$templates[ix].templateId|escape}">{tr}{$templates[ix].name}{/tr}</option>
						{/section}
					</select>
				</td>
			</tr>
		{/if}

		{include file='categorize.tpl'}

		<tr>
			<td colspan="2">
				{tr}Heading:{/tr}
			</td>
		</tr>
		<tr>
			<td colspan="2">
				{if $types.$type.heading_only eq 'y'}
					{textarea name="heading" rows="5" cols="80" Height="200px" id="subheading"}{$heading}{/textarea}
				{else}
					{textarea _simple="y" name="heading" rows="5" cols="80" Height="200px" id="subheading" comments="y"}{$heading}{/textarea}
				{/if}
			</td>
		</tr>


		<tr id='heading_only' {if $types.$type.heading_only ne 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td colspan="2">
				{tr}Body:{/tr}
			</td>
		</tr>
		<tr id='heading_only2' {if $types.$type.heading_only ne 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td colspan="2">
				{textarea name="body" id="body"}{$body}{/textarea}
			</td>
		</tr>

		<tr id='show_pubdate' {if $types.$type.show_pubdate eq 'y' || $types.$type.show_pre_publ ne 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Publish Date{/tr}</td>
			<td>
				{html_select_date prefix="publish_" time=$publishDate start_year="-10" end_year="+10" field_order=$prefs.display_field_order}
				{tr}at{/tr}
				<span dir="ltr">
					{html_select_time prefix="publish_" time=$publishDate display_seconds=false use_24_hours=$use_24hr_clock}
					&nbsp;
					{$siteTimeZone}
				</span>
			</td>
		</tr>

		<tr id='show_expdate' {if $types.$type.show_expdate eq 'y' || $types.$type.show_post_expire ne 'y'}style="display:;"{else}style="display:none;"{/if}>
			<td>{tr}Expiration Date{/tr}</td>
			<td>
				{html_select_date prefix="expire_" time=$expireDate start_year="-10" end_year="+10" field_order=$prefs.display_field_order}
				{tr}at{/tr} 
				<span dir="ltr">
					{html_select_time prefix="expire_" time=$expireDate display_seconds=false use_24_hours=$use_24hr_clock}
					&nbsp;
					{$siteTimeZone}
				</span>
			</td>
		</tr>

		{if $tiki_p_use_HTML eq 'y'}
			{if $smarty.session.wysiwyg neq 'y'}
				<tr>
					<td>{tr}Allow full HTML{/tr} <em>({tr}Keep any HTML tag.{/tr})</em>
					<br><em>{tr}If not enabled, Tiki will retain some HTML tags (a, p, pre, img, hr, b, i){/tr}.</em></td>
					<td>
						<input type="checkbox" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}/>
					</td>
				</tr>
			{else}
				<tr>
					<td></td>
					<td>
						<input type="checkbox" name="allowhtml" checked="checked" style="display:none;"/>
					</td>
				</tr>
			{/if}
		{/if}
		
		{if $prefs.feature_cms_emails eq 'y' and $articleId eq 0}
			<tr>
				<td>
					{tr}Emails to be notified (separated with commas){/tr}
				</td>
				<td>
					<input type="text" name="emails" value="{$emails|escape}" size="60" />
					<br />
					{if !empty($userEmail) and $userEmail ne $prefs.sender_email}
						{tr}From:{/tr} {$userEmail|escape}
						<input type="radio" name="from" value="{$userEmail|escape}"{if empty($from) or $from eq $userEmail} checked="checked"{/if} /> 
						{$prefs.sender_email|escape}
						<input type="radio" name="from" value="{$prefs.sender_email|escape}"{if $from eq $prefs.sender_email} checked="checked"{/if} />
					{/if}
				</td>
			</tr>
		{/if}
		{include file='freetag.tpl'}
		{if isset($all_attributes)}
			{foreach from=$all_attributes item=att key=attname}
			{assign var='attid' value=$att.itemId|replace:'.':'_'}
			{assign var='attfullname' value=$att.itemId}
			<tr id={$attid} {if $types.$type.$attid eq 'y'}style="display:;"{else}style="display:none;"{/if}>
				<td>{$attname|escape}</td>
				<td><input type="text" name="{$attfullname}" value="{$article_attributes.$attfullname|escape}" size="60" maxlength="255" /></td>
			</tr>
			{/foreach}
		{/if}
		<tr>
				<td>{tr}Published{/tr}</td>
				<td>
					<input type="checkbox" name="ispublished" {if $ispublished eq 'y'}checked="checked"{/if}/>
				</td>
		</tr>
	</table>
	
	<div align="center">
		<input type="submit" class="wikiaction" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;"/>
		<input type="submit" class="wikiaction" name="save" value="{tr}Save{/tr}"  onclick="this.form.saving=true;needToConfirm=false;" />
		{if $articleId}<input type="submit" class="wikiaction tips" title="{tr}Cancel{/tr}|{tr}Cancel the edit, you will lose your changes.{/tr}" name="cancel_edit" value="{tr}Cancel Edit{/tr}"  onclick="needToConfirm=false;" />{/if}
	</div>
{if $smarty.session.wysiwyg neq 'y'}
	{jq}
$("#editpageform").submit(function(evt) {
	var isHtml = false;
	if (this.saving && !$("input[name=allowhtml]:checked").length) {
		$("textarea", this).each(function(){
			if ($(this).val().match(/<([A-Z][A-Z0-9]*)\b[^>]*>(.*?)<\/\1>/i)) {
				isHtml = true;
			}
		});
		if (isHtml) {
			this.saving = false;
			return confirm(tr('You appear to be using HTML in your article but have not selected "Allow full HTML".\nThis will result in HTML tags being removed.\nDo you want to save your edits anyway?'));
		}
	}
	return true;
}).attr('saving', false);
	{/jq}
{/if}
</form>

<br />
