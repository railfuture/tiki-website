{if $feature_articles eq 'y'}
<div class="box">
<div class="box-title">
{tr}{$modLastArticlesTitle}{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastArticles}
<tr><td class="module">&nbsp;<a class="linkmodule" href="tiki-read_article.php?articleId={$modLastArticles[ix].articleId}">{$modLastArticles[ix].title}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}