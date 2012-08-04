{if $status neq 'DONE'}
	<form method="post" action="{service controller="comment" action="archive"}">
		{if $do eq 'archive'}
			<p>{tr}Are you sure you want to archive this comment?{/tr}</p>
		{else}
			<p>{tr}Are you sure you want to unarchive this comment?{/tr}</p>
		{/if}
		<p>
			<input type="hidden" name="do" value="{$do|escape}"/>
			<input type="hidden" name="threadId" value="{$threadId|escape}"/>
			<input type="hidden" name="confirm" value="1"/>
			<input type="submit" value="{tr}Confirm{/tr}"/>
		</p>
	</form>
{/if}
{object_link type=$type id=$objectId title="{tr}Return{/tr}"}
