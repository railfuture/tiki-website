{* $Id: filter.tpl 42317 2012-07-09 21:25:37Z jonnybradley $ *}
<form method="get" action="{$filter_action|escape}" class="filter">
	<label>
		{tr}Content{/tr}
		<input type="text" name="filter~content" value="{$filter_content|escape}"/>
		{add_help show='y' title="{tr}Search Help{/tr}" id="unified_search_help"}
			{include file='unified_search_help.tpl'}
		{/add_help}
	</label>
	{if $prefs.feature_search_show_object_filter eq 'y'}
		<label>
			{tr}Type{/tr}
			<select name="filter~type">
				<option value="">{tr}Any{/tr}</option>
				{foreach from=$filter_types key=k item=t}
					<option value="{$k|escape}"{if $t eq $filter_type} selected="selected"{/if}>{$t|escape}</option>
				{/foreach}
			</select>
		</label>
	{/if}
	{if $prefs.feature_categories eq 'y' and $tiki_p_view_category eq 'y' and $prefs.search_show_category_filter eq 'y'}
		<fieldset>
			<legend>{tr}Categories{/tr}</legend>

			<a class="category-lookup" href="#">{tr}Lookup{/tr}</a>
			
			<input type="text" name="filter~categories" class="category-wizard" value="{$filter_categories|escape}"/>

			<label>
				<input type="checkbox" name="filter~deep"{if $filter_deep} checked="checked"{/if}/>
				{tr}Deep search{/tr}
			</label>
		</fieldset>

		<div class="category-picker" title="{tr}Select Categories{/tr}" style="display:none;">
			{$filter_category_picker}
		</div>
	{/if}
	{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and $prefs.search_show_tag_filter eq 'y'}
		<fieldset>
			<legend>{tr}Tags{/tr}</legend>

			<input type="text" name="filter~tags" class="tag-wizard" value="{$filter_tags|escape}"/>

			<a class="tag-lookup" href="#">{tr}Lookup{/tr}</a>
		</fieldset>

		<div class="tag-picker" title="{tr}Select Tags{/tr}" style="display:none;">
			{$filter_tags_picker}
		</div>
	{/if}
	{if $prefs.feature_multilingual eq 'y'}
		{if $prefs.search_default_interface_language neq 'y'}
			<fieldset>
				<legend>{tr}Language{/tr}</legend>
				<select name="filter~language">
					<option value="">{tr}Any{/tr}</option>
					{foreach from=$filter_languages item=l}
						<option value="{$l.value|escape}"{if $filter_language eq $l.value} selected="selected"{/if}>{$l.name|escape}</option>
					{/foreach}
				</select>
				<label>
					<input type="checkbox" name="filter~language_unspecified"{if $filter_language_unspecified} checked="checked"{/if}/>
					{tr}Include objects without a specified language{/tr}
				</label>
			</fieldset>
		{else}
			<input type="hidden" name="filter~language" value="{$prefs.language}">
		{/if}
	{/if}
	<input type="submit" value="{tr}Search{/tr}"/>
</form>
{jq}
	$('.filter:not(.init)').addClass('init').each(function () {

{{if $prefs.feature_categories eq 'y'}}
		var categoryInput = $('.category-wizard', this).fancy_filter('init', {
			map: {{$filter_categmap}}
		});

		var categoryPicker = $('.category-picker', this).dialog({
			height: $(window).height()-200,
			width: 'auto',
			autoOpen: false,
			modal: true,
			buttons: {
				"{tr}Add to filter{/tr}": function () {
					$(':checked', this).each(function () {
						categoryInput.fancy_filter('add', {
							token: $(this).val(),
							label: $(this).parent().text(),
							join: ' or '
						});
					});
					$(this).dialog('close');
				},
				"{tr}Cancel{/tr}": function () {
					$(this).dialog('close');
				}
			},
			close: function () {
				$(':checked', this).attr('checked', false);
			}
		});

		$('.category-lookup', this).click(function () {
			categoryPicker.dialog('open');
			return false;
		});
{{/if}}

{{if $prefs.feature_freetags eq 'y'}}
		var tagInput = $('.tag-wizard', this).fancy_filter('init', {
			map: {{$filter_tagmap}}
		});

		$('.tag-picker a', this).click(function () {
			$(this).toggleClass('highlight');

			return false;
		});
		var tagPicker = $('.tag-picker', this).dialog({
			autoOpen: false,
			modal: true,
			buttons: {
				"{tr}Add to filter{/tr}": function () {
					$('.highlight', this).each(function () {
						tagInput.fancy_filter('add', {
							token: $(this).attr('href'),
							label: $(this).text(),
							join: ' and '
						});
					});
					$(this).dialog('close');
				},
				"{tr}Cancel{/tr}": function () {
					$(this).dialog('close');
				}
			},
			close: function () {
				$(':checked', this).attr('checked', false);
			}
		});

		$('.tag-lookup', this).click(function () {
			tagPicker.dialog('open');
			return false;
		});
{{/if}}
	});
{/jq}
