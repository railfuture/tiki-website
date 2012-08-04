{* $Id: tiki-article_types.tpl 40551 2012-03-30 10:19:56Z gezzzan $ *}
{title admpage="articles" url="tiki-article_types.php" help=Articles}{tr}Admin Article Types{/tr}{/title}

{tabset name='tabs_articletypes'}
	{tab name="{tr}Article Types{/tr}"}
	<form enctype="multipart/form-data" action="tiki-article_types.php" method="post">
		{section name=user loop=$types}
			<h3>{tr}{$types[user].type|escape}{/tr}</h3>
			<a class="link" href="tiki-view_articles.php?type={$types[user].type|escape:url}">{tr}View articles with this type{/tr}</a>
			<table class="normal">
				<tr>
					<th>{tr}Articles{/tr}</th>
					<th>{tr}Author rating{/tr}</th>
					<th>{tr}Show before publish date{/tr}</th>
					<th>{tr}Show after expire date{/tr}</th>
					<th>{tr}Heading only{/tr}</th>
					<th>{tr}Comments{/tr}</th>
					<th>{tr}Comment can rate article{/tr}</th>
					<th>{tr}Show image{/tr}</th>
					<th>{tr}Show avatar{/tr}</th>
					<th>{tr}Show author{/tr}</th>
					<th>{tr}Show publish date{/tr}</th>
				</tr>
				{cycle print=false values="even,odd"}
				<input type="hidden" name="type_array[{$types[user].type|escape}]" />
				<tr class="{cycle}">
					<td class="integer">{$types[user].article_cnt}</td>
					<td class="checkbox">
						<input type="checkbox" name="use_ratings[{$types[user].type|escape}]" {if $types[user].use_ratings eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_pre_publ[{$types[user].type|escape}]" {if $types[user].show_pre_publ eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_post_expire[{$types[user].type|escape}]" {if $types[user].show_post_expire eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="heading_only[{$types[user].type|escape}]" {if $types[user].heading_only eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="allow_comments[{$types[user].type|escape}]" {if $types[user].allow_comments eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="comment_can_rate_article[{$types[user].type|escape}]" {if $types[user].comment_can_rate_article eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_image[{$types[user].type|escape}]" {if $types[user].show_image eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_avatar[{$types[user].type|escape}]" {if $types[user].show_avatar eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_author[{$types[user].type|escape}]" {if $types[user].show_author eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_pubdate[{$types[user].type|escape}]" {if $types[user].show_pubdate eq 'y'}checked="checked"{/if} />
					</td>
				</tr>
				<tr>
					<th>{tr}Show expire date{/tr}</th>
					<th>{tr}Show reads{/tr}</th>
					<th>{tr}Show size{/tr}</th>
					<th>{tr}Show topline{/tr}</th>
					<th>{tr}Show subtitle{/tr}</th>
					<th>{tr}Show source{/tr}</th>
					<th>{tr}Show image caption{/tr}</th>
					<th>{tr}Creator can edit{/tr}</th>
					<th colspan="2">{tr}Action{/tr}</th>
				</tr>
				<tr class="{cycle}">
					<td class="checkbox">
						<input type="checkbox" name="show_expdate[{$types[user].type|escape}]" {if $types[user].show_expdate eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_reads[{$types[user].type|escape}]" {if $types[user].show_reads eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_size[{$types[user].type|escape}]" {if $types[user].show_size eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_topline[{$types[user].type|escape}]" {if $types[user].show_topline eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_subtitle[{$types[user].type|escape}]" {if $types[user].show_subtitle eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_linkto[{$types[user].type|escape}]" {if $types[user].show_linkto eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_image_caption[{$types[user].type|escape}]" {if $types[user].show_image_caption eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="creator_edit[{$types[user].type|escape}]" {if $types[user].creator_edit eq 'y'}checked="checked"{/if} />
					</td>
					<td class="action" colspan="2">
						<center>
							{if $types[user].article_cnt eq 0}
								<a class="link" href="tiki-article_types.php?remove_type={$types[user].type|escape:url}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
							{else}
								&nbsp;
							{/if}
						</center>
					</td>
			</tr>
		</table>
		{if $prefs.article_custom_attributes eq 'y'}
			<table class="normal">
				<tr>
					<th>{tr}Custom attribute{/tr}</th>
					<th>{tr}Action{/tr}</th>
				</tr>
				{cycle print=false values="even,odd"}
				{foreach from=$types[user].attributes item=att key=attname}
					<tr class="{cycle}">
						<td>{$attname|escape}</td>
						<td class="action">
							<a class="link" href="tiki-article_types.php?att_type={$types[user].type|escape:url}&att_remove={$att.relationId|escape:url}">
								{icon _id='cross' alt="{tr}Remove{/tr}"}
							</a>
						</td>
					</tr>
				{/foreach}
				<tr>
					<td><input type="text" name="new_attribute[{$types[user].type|escape}]" value="" maxlength="56" /></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		{/if}
		<input type="submit" name="update_type" value="{tr}Save{/tr}" /><br />
		<hr />
		<br />
		{/section}
	{/tab}
	{tab name="{tr}Add Type{/tr}"}
		<h3>{tr}Add article type{/tr}</h3>
		<input type="text" name="new_type" /><input type="submit" name="add_type" value="{tr}Add{/tr}" />
	{/tab}
	</form>
{/tabset}
