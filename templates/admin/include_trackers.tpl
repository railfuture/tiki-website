{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To configure your trackers, select "Trackers" in the application menu, or{/tr} <a class="rbox-link" href="tiki-list_trackers.php">{tr}Click Here{/tr}</a>.{/remarksbox}

<form action="tiki-admin.php?page=trackers" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="trkset" value="{tr}Change preferences{/tr}" />
	</div>
{tabset}
	{tab name="{tr}Settings{/tr}"}
	<fieldset class="admin">
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_trackers visible="always"}
	</fieldset>
	<fieldset class="admin">
		<legend>{tr}Tracker settings{/tr}</legend>
		{preference name=user_selector_threshold}
		{preference name=user_selector_realnames_tracker}
		{preference name=feature_reports}
		{preference name="tracker_remote_sync"}
		{preference name="tracker_refresh_itemlink_detail"}
	</fieldset>

	<fieldset class="admin">
	  <legend>{tr}Tracker attachment preferences{/tr}</legend>
		  <table class="admin">
			<tr>
			  <td>
				{tr}Use database to store files:{/tr}
			  </td>
			  <td>
				<input type="radio" name="t_use_db" value="y" {if $prefs.t_use_db eq 'y'}checked="checked"{/if}/>
			  </td>
			</tr>

			<tr>
			  <td>
				{tr}Use a directory to store files:{/tr}</td>
			  <td>
				<input type="radio" name="t_use_db" value="n" {if $prefs.t_use_db eq 'n'}checked="checked"{/if}/> {tr}Path:{/tr}
				<br />
				<input type="text" name="t_use_dir" value="{$prefs.t_use_dir|escape}" size="50" />
			  </td>
			</tr>

		  </table>
	</fieldset>

	{/tab}
	{tab name="{tr}Plugins{/tr}"}
	<fieldset class="admin">
		<legend>{tr}Plugins{/tr}</legend>
		{preference name=wikiplugin_tracker}
		{preference name=wikiplugin_trackerlist}
		{preference name=wikiplugin_trackerfilter}
		{preference name=wikiplugin_trackerif}
		{preference name=wikiplugin_trackerstat}
		{preference name=wikiplugin_miniquiz}
		{preference name=wikiplugin_vote}
		{preference name=wikiplugin_trackercomments}
		{preference name=wikiplugin_trackeritemfield}
		{preference name=wikiplugin_trackerprefill}
		{preference name=wikiplugin_trackertimeline}
		{preference name=wikiplugin_trackertoggle}
		{preference name=wikiplugin_prettytrackerviews}
		{preference name=wikiplugin_trackerpasscode}
		{preference name=wikiplugin_trackeritemcopy}
	</fieldset>
	{/tab}
	{tab name="{tr}Field Types{/tr}"}
	<fieldset class="admin">
		<legend>{tr}Field Types{/tr}</legend>
		{foreach from=$fieldPreferences item=name}
			{preference name=$name}
		{/foreach}
	</fieldset>
	
	{/tab}
{/tabset}
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="trkset" value="{tr}Change preferences{/tr}" />
	</div>
</form>


<fieldset class="admin">
  <legend>{tr}Tracker attachments{/tr}</legend>
    <div class="admin">
{if $attachements}
      <form action="tiki-admin.php?page=trackers" method="post">
        <input type="text" name="find" value="{$find|escape}" />
        <input type="submit" name="action" value="{tr}Find{/tr}" />
      </form>
{/if}
      {cycle values="odd,even" print=false}
      <table class="normal">
        <tr>
          <th>
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=user_{if $sort_mode eq 'attId'}asc{else}desc{/if}">{tr}ID{/tr}</a>
          </th>
          <th>
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=user_{if $sort_mode eq 'user'}asc{else}desc{/if}">{tr}User{/tr}</a>
          </th>
          <th>
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=filename_{if $sort_mode eq 'filename'}asc{else}desc{/if}">{tr}Name{/tr}</a>
          </th>
          <th>
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=filesize_{if $sort_mode eq 'filesize'}asc{else}desc{/if}">{tr}Size{/tr}</a>
          </th>
          <th>
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=filetype_{if $sort_mode eq 'filetype'}asc{else}desc{/if}">{tr}Type{/tr}</a>
          </th>
          <th>
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=hits_{if $sort_mode eq 'hits'}asc{else}desc{/if}">{tr}dls{/tr}</a>
          </th>
          <th>
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=itemId_{if $sort_mode eq 'itemId'}asc{else}desc{/if}">{tr}Item{/tr}</a>
          </th>
          <th>
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=path_{if $sort_mode eq 'path'}asc{else}desc{/if}">{tr}Storage{/tr}</a>
          </th>
          <th>
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=created_{if $sort_mode eq 'created'}asc{else}desc{/if}">{tr}Created{/tr}</a>
          </th>
          <th>{tr}Switch storage{/tr}</th>
        </tr>
        
        {section name=x loop=$attachements}
        <tr class={cycle}>
          <td class="id"><a href="tiki-download_item_attachment.php?attId={$attachements[x].attId}" title="{tr}Download{/tr}">{$attachements[x].attId}</a></td>
          <td class="username">{$attachements[x].user}</td>
          <td class="text">{$attachements[x].filename}</td>
          <td class="integer">{$attachements[x].filesize|kbsize}</td>
          <td class="text">{$attachements[x].filetype}</td>
          <td class="integer">{$attachements[x].hits}</td>
          <td class="integer">{$attachements[x].itemId}</td>
          <td class="text">{if $attachements[x].path}file{else}db{/if}</td>
          <td class="date">{$attachements[x].created|tiki_short_date}</td>
          <td class="action">
            <a href="tiki-admin.php?page=trackers&amp;attId={$attachements[x].attId}&amp;action={if $attachements[x].path}move2db{else}move2file{/if}">{icon _id='arrow_refresh' title="{tr}Switch storage{/tr}"}</a>
          </td>
        </tr>
		{sectionelse}
			{norecords _colspan=10}
        {/section}
      </table>
      
		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
    </div>
{if $attachements}
    <table>
      <tr>
        <td>
          <form action="tiki-admin.php?page=trackers" method="post">
            <input type="hidden" name="all2db" value="1" />
            <input type="submit" name="action" value="{tr}Change all to db{/tr}" />
          </form>
        </td>
        <td>
          <form action="tiki-admin.php?page=trackers" method="post">
            <input type="hidden" name="all2file" value="1" />
            <input type="submit" name="action" value="{tr}Change all to file{/tr}" />
          </form>
        </td>
      </tr>
    </table>
{/if}
</fieldset>

