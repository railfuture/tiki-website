{if strlen($blog_data.post_heading) > 0 and $prefs.feature_blog_heading eq 'y'}
  {eval var=$blog_data.post_heading}
{else}
	{include file='blog_post_heading.tpl'}
{/if}

<div class="blogpost post post_single">
	{include file='blog_wrapper.tpl' blog_post_context='view_blog_post'}
	{include file='blog_post_related_content.tpl'}
</div>

{if $prefs.feature_blogposts_comments == 'y' && $blog_data.allow_comments == 'y'
		&& ($tiki_p_read_comments == 'y'
			|| $tiki_p_post_comments == 'y'
			|| $tiki_p_edit_comments == 'y')}
	<div id="comment-container" data-target="{service controller=comment action=list type="blog post" objectId=$postId}"></div>
	{jq}
		var id = '#comment-container';
		$(id).comment_load($(id).data('target'));
	{/jq}
{/if}
