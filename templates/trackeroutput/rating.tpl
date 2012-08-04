{if $tiki_p_tracker_view_ratings eq 'y'}
	{if $context.list_mode eq 'csv'}
		{$field.value}/{$field.voteavg}
	{else}
		{strip}
			{capture name=stat}
				{if empty($field.numvotes)}
					{tr}Number of votes:{/tr} 0
				{else}
					{tr}Number of votes:{/tr} {$field.numvotes|default:"0"}, {tr}Average:{/tr} {$field.voteavg|default:"0"}
					{if $tiki_p_tracker_vote_ratings eq 'y'}
						, {if isset($field.my_rate) && $field.my_rate !== false}{tr}Your rating:{/tr} {$field.my_rate}{else}{tr}You did not vote yet{/tr}{/if}
					{/if}
				{/if}
			{/capture}
		{/strip}
		{capture name=myvote}{tr}My rating:{/tr} {$field.my_rate}{/capture}
		<span class="rating">
		<span style="white-space:nowrap">
		{section name=i loop=$field.rating_options}
			{if $tiki_p_tracker_vote_ratings eq 'y' and isset($field.my_rate) and $field.rating_options[i] === $field.my_rate and $context.search_render neq 'y'}
				<span class="highlight">
					{if $field.numvotes && $field.voteavg >= $field.rating_options[i]}
				   		{if $field.mode eq 'radio'}{tr}{$field.labels[i]}{/tr}: {/if}{icon _id='star' alt=$field.rating_options[i] title=$smarty.capture.myvote}
					{else}
						{if $field.mode eq 'radio'}{tr}{$field.labels[i]}{/tr}: {/if}{if $field.mode eq 'radio'}{icon _id='star' alt=$field.rating_options[i] title=$smarty.capture.myvote}{else}{icon _id='star_grey' alt=$field.rating_options[i] title=$smarty.capture.myvote}{/if}
					{/if}
				</span>
			{else}
				{if ($tiki_p_tracker_vote_ratings eq 'y' && (!isset($field.my_rate) || $field.my_rate === false)) ||
					($tiki_p_tracker_revote_ratings eq 'y' && isset($field.my_rate) && $field.my_rate !== false)}
					{capture name=thisvote}{tr}Click to vote for this value:{/tr} {$field.rating_options[i]}{/capture}
					{if $context.search_render eq 'y'}
						<a href="{$smarty.server.REQUEST_URI}" onclick="sendVote(this,{$item.itemId},{$field.fieldId},{$field.rating_options[i]});return false;">
					{else}
						<a href="{$smarty.server.REQUEST_URI}{if empty($smarty.server.QUERY_STRING)}?{else}&amp;{/if}itemId={$item.itemId}&amp;ins_{$field.fieldId}={$field.rating_options[i]}&amp;vote=y">
					{/if}
				{/if}
				{if $field.numvotes && $field.voteavg >= $field.rating_options[i]}
					{if $field.mode eq 'radio'}{tr}{$field.labels[i]}{/tr}: {/if}{if $field.mode eq 'radio'}{icon _id='star_grey' alt=$field.rating_options[i] title=$smarty.capture.thisvote}{else}{icon _id='star' alt=$field.rating_options[i] title=$smarty.capture.thisvote}{/if}
				{else}
					{if $field.mode eq 'radio'}{tr}{$field.labels[i]}{/tr}: {/if}{icon _id='star_grey' alt=$field.rating_options[i] title=$smarty.capture.thisvote}
				{/if}
				{if ($tiki_p_tracker_vote_ratings eq 'y' && (!isset($field.my_rate) || $field.my_rate === false)) ||
					($tiki_p_tracker_revote_ratings eq 'y' && isset($field.my_rate) && $field.my_rate !== false)}
					</a>
				{/if}	
			{/if}
			{assign var='previousvote' value=$field.rating_options[i]}
		{/section}
		</span>
		{if $item.itemId}
			<small title="{tr}Votes{/tr}">
				({$field.numvotes})
			</small>
			{icon _id='help' title=$smarty.capture.stat}
		{/if}
		{if $tiki_p_tracker_revote_ratings eq 'y' and  isset($field.my_rate) and in_array($field.my_rate, $field.options_array)}
			{if $context.search_render eq 'y'}
				<a href="{$smarty.server.REQUEST_URI}" onclick="sendVote(this,{$item.itemId},{$field.fieldId},'NULL');return false;">x</a>
			{else}
				<a href="{$smarty.server.REQUEST_URI}{if empty($smarty.server.QUERY_STRING)}?{else}&amp;{/if}itemId={$item.itemId}&amp;ins_{$field.fieldId}=NULL&amp;vote=y" title="{tr}Click to delete your vote{/tr}">x</a>
			{/if}
		{/if}
		<span>
	{/if}
{/if}
