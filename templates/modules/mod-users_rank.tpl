{* $Id: mod-users_rank.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="users_rank" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if !empty($users_rank)}
	{modules_list list=$users_rank nonums=$nonums}
	{section loop=$users_rank name=u}
	  <li>
	    {*<div class="licomponent" style="display:inline">{$users_rank[u].position})&nbsp;</div>*}
	    <div class="licomponent" style="display:inline">{$users_rank[u].score}</div>
	    <div class="licomponent" style="display:inline">&nbsp;{$users_rank[u].login|userlink}</div>
	  </li>
	{/section}
	{/modules_list}
{/if}
{if $prefs.feature_friends eq 'y'}
<a style="margin-left: 20px" href="tiki-list_users.php" class="more">{tr}More...{/tr}</a>
{/if}

{/tikimodule}
