{* Index we display a wiki page here *}
{include file="header.tpl"}
{if $feature_bidi eq 'y'}
<table dir="rtl" width="100%"><tr><td>
{/if}
<div id="tiki-main">
<div id="tiki-top">
{include file="tiki-top_bar.tpl"}
</div>
							
  {if $user and $feature_top_bar eq 'y'}
	  <div id="tiki-top-menu">
		  {if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'}
			&nbsp;<a class="tikitopmenu" href="javascript:toggle('debugconsole');">{tr}debug{/tr}</a> //
		  {/if}
		  {if $feature_userPreferences eq 'y'}
		  	&nbsp;<a class="tikitopmenu" href="tiki-user_preferences.php">{tr}Preferences{/tr}</a>
		    ::&nbsp;<a class="tikitopmenu" href="tiki-my_tiki.php">{tr}MyTiki{/tr}</a>
		  {/if}
		  {if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
		  	::&nbsp;<a class="tikitopmenu" href="messu-mailbox.php">{tr}Messages{/tr}</a>
		  {/if}
		  {if $feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
		  	::&nbsp;<a class="tikitopmenu" href="tiki-userfiles.php">{tr}User files{/tr}</a>
		  {/if}
		  {if $feature_minical eq 'y'}
		  	::&nbsp;<a class="tikitopmenu" href="tiki-minical.php">{tr}Calendar{/tr}</a>
		  {/if}
		  {if $feature_usermenu eq 'y'}
		  	::&nbsp;<a class="tikitopmenu" href="tiki-usermenu.php">{tr}Favorites{/tr}</a>
		  {/if}
 	          {if $feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
		  	::&nbsp;<a class="tikitopmenu" href="tiki-user_tasks.php">{tr}Tasks{/tr}</a>
		  {/if}
		  {if $feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
		  	::&nbsp;<a class="tikitopmenu" href="tiki-user_bookmarks.php">{tr}Bookmarks{/tr}</a>
		  {/if}
		  {if $feature_newsreader eq 'y' and $tiki_p_newsreader eq 'y'}
		  	::&nbsp;<a class="tikitopmenu" href="tiki-newsreader_servers.php">{tr}Newsreader{/tr}</a>
		  {/if}
		  {if $user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
		  	::&nbsp;<a class="tikitopmenu" href="tiki-user_assigned_modules.php">{tr}Modules{/tr}</a>
		  {/if}
		  {if $feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
		  	::&nbsp;<a class="tikitopmenu" href="tiki-webmail.php">{tr}Webmail{/tr}</a>
		  {/if}
		  {if $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		  	::&nbsp;<a class="tikitopmenu" href="tiki-notepad_list.php">{tr}Notepad{/tr}</a>
		  {/if}
		  {if $feature_user_watches eq 'y'}
		  	::&nbsp;<a class="tikitopmenu" href="tiki-user_watches.php">{tr}Watches{/tr}</a>
		  {/if}
		  &nbsp;&nbsp;
	  </div>
  
  {if $feature_usermenu eq 'y'}	
	  <div id="usermenu">
	  	&nbsp;&nbsp;<a href="tiki-usermenu.php?url={$smarty.server.REQUEST_URI|escape:"url"}" title='{tr}add{/tr}' class="linkmenu"><b>+</b></a>
	  	{section name=ix loop=$usr_user_menus}
  			&nbsp;<a {if $usr_user_menus[ix].mode eq 'n'}target='_blank'{/if} href="{$usr_user_menus[ix].url}" class="linkmenu"><b style="color:#999999;">&gt;</b>{$usr_user_menus[ix].name}</a>
  		{/section}
  		
	  </div>
  {/if}
  {/if}
 


  <div id="tiki-mid">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
{* FIXME   <tr>
      {if $feature_left_column eq 'y'}
      <td id="leftcolumn"/>
      {/if}
      <td id="centercolumn"></td>
      {if $feature_right_column eq 'y'}
      <td id="rightcolumn" />
      {/if}
    </tr>*}
    <tr>
      {if $feature_left_column eq 'y'}
      <td id="leftcolumn">
      <div id="leftcolumnheader"></div>

      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      
      </td>
      {/if}
      <td id="centercolumn">
      <div id="centercolumnheader"></div>
      <div id="tiki-center">
      {include file=$mid}
      </div>
      </td>
      {if $feature_right_column eq 'y'}
      <td id="rightcolumn">
      <div id="rightcolumnheader"><div id="rchleft"></div></div>

      <div id="rightcolumnbody">
      <div width="100%" align="right"><img src="styles/railfuture/rfboxlogo.gif" width="140" height="21" alt="Railfuture"/></div>
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}
      </div>
      </td>
      {/if}
    </tr>
    </table>
  </div>
  {if $feature_bot_bar eq 'y'}
  <div id="tiki-bot">
    {include file="tiki-bot_bar.tpl"}
  </div>
  {/if}
</div>
{if $feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
