<h2>{tr}Pages like:{/tr}

{if $page_exists eq 'n'}
'{$page|escape}'
{else}
<a href="tiki-index.php?page={$page|escape:"url"}" class="wiki">{$page|escape}</a>
{/if}</h2>
<br />
{if $likepages|@count ge '2'}
<ul>
{section name=back loop=$likepages}
{if $likepages[back] ne $pagegae}
<li><a href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]|escape}</a></li>
{/if}
{sectionelse}
{tr}No pages found{/tr}
{/section}
</ul>
{else}
{tr}No pages found{/tr}
{/if}
<br />

{include file='tiki-page_bar.tpl'}

