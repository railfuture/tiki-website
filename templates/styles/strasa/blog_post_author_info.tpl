{* $Id: blog_post_author_info.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
<div class="author_info">
	{if $blog_data.use_author eq 'y' || $blog_data.add_date eq 'y'}
		{tr}Published {/tr}
	{/if}
	{if $blog_data.use_author eq 'y'}
		{tr}by{/tr} {$post_info.user|userlink} 
	{/if}
	{if $blog_data.add_date eq 'y'}
		{tr}on{/tr} {$post_info.created|tiki_short_date}
	{/if}
	{if $blog_data.show_avatar eq 'y'}
		{$post_info.avatar}
	{/if}
	{if $blog_post_context ne 'print'}
		{include file='blog_post_author_actions.tpl'}
	{/if}
</div>
