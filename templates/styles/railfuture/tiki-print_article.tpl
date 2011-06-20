{* Index we display a wiki page here *}
{include file="header.tpl"}
<div id="tiki-main">
<div class="articletitle">
<span class="titlea">{$title}</span><br />
</div>
<div class="articleheading">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td width="25%" valign="top">
{if $useImage eq 'y'}
  {if $hasImage eq 'y'}
    <img alt="{tr}Article image{/tr}" border="0" src="article_image.php?id={$articleId}" />
  {else}
    <img alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topicId}" />
  {/if}
{else}
  <img alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topicId}" />
{/if}
</td><td width="75%" valign="top">
<span class="articleheading">{$parsed_heading}</span>
</td></tr>
</table>
</div>
<div class="articlebody">
{$parsed_body}
</div>
<span class="titleb">{tr}By:{/tr}{$authorName} {tr}on:{/tr} {$publishDate|tiki_short_datetime} ({$size} {tr}bytes{/tr} {$reads} {tr}reads{/tr})</span><br />
</div>
{include file="footer.tpl"}
