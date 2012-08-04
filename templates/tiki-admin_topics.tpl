{* $Id: tiki-admin_topics.tpl 40551 2012-03-30 10:19:56Z gezzzan $ *}

{title admpage="articles" help="Articles"}{tr}Admin Article Topics{/tr}{/title}

<h2>{tr}Add topic{/tr}</h2>

<form enctype="multipart/form-data" action="tiki-admin_topics.php" method="post">
	<table class="formcolor">
		<tr>
			<td>{tr}Name{/tr}</td>
			<td><input type="text" name="name" /></td>
		</tr>
		<tr>
			<td>{tr}Image{/tr}</td>
			<td>
				<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
				<input name="userfile1" type="file" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" name="addtopic" value="{tr}Add{/tr}" />
			</td>
		</tr>
	</table>
</form>

<h2>{tr}Topics{/tr}</h2>
<table class="normal">
	<tr>
		<th>{tr}ID{/tr}</th>
		<th>{tr}Name{/tr}</th>
		<th>{tr}Image{/tr}</th>
		<th>{tr}Active{/tr}</th>
		<th>{tr}Articles{/tr}</th>
		{if $prefs.feature_submissions eq 'y'}<th>{tr}Submissions{/tr}</th>{/if}
		<th>{tr}Actions{/tr}</th>
	</tr>
	{cycle print=false values="even,odd"}
	{section name=user loop=$topics}
		<tr class="{cycle}">
			<td class="integer">{$topics[user].topicId}</td>
			<td class="text">
				<a class="link" href="tiki-view_articles.php?topic={$topics[user].topicId}">{$topics[user].name|escape}</a>
			</td>
			<td class="text">
				{if $topics[user].image_size}
					<img alt="{tr}topic image{/tr}" src="article_image.php?image_type=topic&amp;id={$topics[user].topicId}&amp;reload=1" />
				{else}
					&nbsp;
				{/if}
			</td>
			<td class="text">{$topics[user].active}</td>
			<td class="integer">{$topics[user].arts}</td>
			{if $prefs.feature_submissions eq 'y'}<td>{$topics[user].subs}</td>{/if}
			<td class="action">
				 <a class="link" href="tiki-edit_topic.php?topicid={$topics[user].topicId}">{icon _id='page_edit'}</a>
				{if $topics[user].individual eq 'y'}
					<a title="{tr}Active Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$topics[user].name|escape:"url"}&amp;objectType=topic&amp;permType=cms&amp;objectId={$topics[user].topicId}">{icon _id='key_active' alt="{tr}Active Permissions{/tr}"}</a>
				{else}
					<a title="{tr}Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$topics[user].name|escape:"url"}&amp;objectType=topic&amp;permType=cms&amp;objectId={$topics[user].topicId}">{icon _id='key' alt="{tr}Permissions{/tr}"}</a>
				{/if}
				{if $topics[user].active eq 'n'}
					<a class="link" href="tiki-admin_topics.php?activate={$topics[user].topicId}">{icon _id='accept' alt="{tr}Activate{/tr}" title="{tr}Inactive - Click to Activate{/tr}"}</a>
				{else}
					<a class="link" href="tiki-admin_topics.php?deactivate={$topics[user].topicId}">{icon _id='delete' alt="{tr}Deactivate{/tr}" title="{tr}Active - Click to Deactivate{/tr}"}</a>
				{/if}
				<a class="link" href="tiki-admin_topics.php?remove={$topics[user].topicId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
				<a class="link" href="tiki-admin_topics.php?removeall={$topics[user].topicId}">{icon _id='cross_admin' alt="{tr}Remove with articles{/tr}"}</a>
			</td>
		</tr>
	{sectionelse}
		{if $prefs.feature_submissions eq 'y'}{norecords _colspan=7}{else}{norecords _colspan=6}{/if}
	{/section}
</table>
