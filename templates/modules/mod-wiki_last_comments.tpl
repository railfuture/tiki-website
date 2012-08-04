{* $Id: mod-wiki_last_comments.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{if ($type eq 'wiki page' and $prefs.feature_wiki eq 'y')
	or ($type eq 'article' and $prefs.feature_articles eq 'y')}
	{tikimodule error=$module_params.error title=$tpl_module_title name="wiki_last_comments" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{modules_list list=$comments nonums=$nonums}
		{section name=ix loop=$comments}
			<li>
				<a class="linkmodule" href="{$comments[ix].object|sefurl:$type}&amp;comzone=show#threadId{$comments[ix].threadId}" title="{$comments[ix].commentDate|tiki_short_datetime}, {tr}by{/tr} {$comments[ix].userName|username}{if $moretooltips eq 'y'} {tr}on{/tr} {$comments[ix].name|escape}{/if}">
				{if $moretooltips ne 'y'}
					<strong>{$comments[ix].name|escape}</strong>: 
				{/if}
					{$comments[ix].title|escape}
				</a>
			</li>
		{/section}
	{/modules_list}
	{/tikimodule}
{/if}
