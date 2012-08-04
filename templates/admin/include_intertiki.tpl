{* $Id: include_intertiki.tpl 35618 2011-07-23 13:59:19Z lphuberdeau $ *}
{tabset name="admin_interwiki"}
	{tab name="{tr}Intertiki client{/tr}"}
		<form action="tiki-admin.php?page=intertiki" method="post" name="intertiki">
			<table class="formcolor">
				<tr>
					<td>{tr}Tiki Unique key{/tr}</td>
					<td><input type="text" name="tiki_key" value="{$prefs.tiki_key}" size="32" /></td>
				</tr>
				<tr>
					<td>
						{tr}InterTiki Slave mode{/tr}
						<br />
						<small>{tr}Warning: overrides manually registered local users{/tr}</small>
					</td>
					<td>
						{jq}
							{literal}
							function check_server_visibility(sel) {
								if (sel.selectedIndex == 0) {
									document.getElementById('admin-server-options').style.display = 'block';
									document.getElementById('admin-slavemode-options').style.display = 'none';
								} else {
									document.getElementById('admin-server-options').style.display = 'none';
									document.getElementById('admin-slavemode-options').style.display = 'block';
								}
							}	
							{/literal}
						{/jq}

						<select name="feature_intertiki_mymaster" onchange="check_server_visibility(this);">
							<option value="">{tr}No{/tr}</option>
							{foreach from=$prefs.interlist key=k item=i}
								<option value="{$k|escape}"{if $prefs.feature_intertiki_mymaster eq $k} selected="selected"{/if}>{$i.name} {tr}as master{/tr}</option>
							{/foreach}
						</select>
						<div id="admin-slavemode-options" style="display: {if $prefs.feature_intertiki_mymaster eq ''}none{else}block{/if}">
							<input type="checkbox" name="feature_intertiki_import_preferences" {if $prefs.feature_intertiki_import_preferences eq 'y'}checked="checked"{/if}/>
							{tr}Import user preferences{/tr}<br />
	
							<input type="checkbox" name="feature_intertiki_import_groups" {if $prefs.feature_intertiki_import_groups eq 'y'}checked="checked"{/if}/>
							{tr}Import user groups{/tr}
							<br />
							{tr}Limit group import (comma-separated list of imported groups, leave empty to avoid limitation){/tr}
							<br />
							<input type="text" name="feature_intertiki_imported_groups" value="{$prefs.feature_intertiki_imported_groups}" />
						</div>
					</td>
				</tr>
				<tr>
					<td>
						{tr}Intertiki shared cookie for sliding auth under same domain:{/tr}
					</td>
					<td>
						<input type="checkbox" name="feature_intertiki_sharedcookie" {if $prefs.feature_intertiki_sharedcookie eq 'y'}checked="checked"{/if}/>
					</td>
				</tr>
				{if $prefs.interlist}
					{foreach key=k item=i from=$prefs.interlist}
						<tr>
							<td colspan="2">
								<a href="tiki-admin.php?page=intertiki&amp;del={$k|escape:'url'}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
								{tr}InterTiki Server{/tr} <b>{$k}</b>
							</td>
						</tr>
						<tr>
							<td>{tr}Name{/tr}</td>
							<td><input type="text" name="interlist[{$k}][name]" value="{$i.name}" /></td>
						</tr>
						<tr>
							<td>{tr}host{/tr}</td>
							<td><input type="text" name="interlist[{$k}][host]" value="{$i.host}" /></td>
						</tr>
						<tr>
							<td>{tr}port{/tr}</td>
							<td><input type="text" name="interlist[{$k}][port]" value="{$i.port}" /></td>
						</tr>
						<tr>
							<td>{tr}Path{/tr}</td>
							<td><input type="text" name="interlist[{$k}][path]" value="{$i.path}" /></td>
						</tr>
						<tr>
							<td>{tr}Groups{/tr}</td>
							<td>
								<input type="text" name="interlist[{$k}][groups]" value="{foreach item=g from=$i.groups name=f}{$g}{if !$smarty.foreach.f.last},{/if}{/foreach}" />
							</td>
						</tr>
					{/foreach}
				{/if}
				<tr>
					<td colspan="2">{tr}Add new server{/tr}</td>
				</tr>
				<tr>
					<td>{tr}Name{/tr}</td>
					<td><input type="text" name="new[name]" value="" /></td>
				</tr>
				<tr>
					<td>{tr}host{/tr}</td>
					<td><input type="text" name="new[host]" value="" /></td>
				</tr>
				<tr>
					<td>{tr}port{/tr}</td>
					<td><input type="text" name="new[port]" value="" /></td>
				</tr>
				<tr>
					<td>{tr}Path{/tr}</td>
					<td><input type="text" name="new[path]" value="" /></td>
				</tr>
				<tr>
					<td>{tr}Groups{/tr}</td>
					<td><input type="text" name="new[groups]" value="" /></td>
				</tr>
			</table>
			<div class="heading input_submit_container" style="text-align: center">
				<input type="submit" name="intertikiclient" value="{tr}Save{/tr}" />
			</div>
		</form>
	{/tab}

	{if $prefs.feature_intertiki_mymaster eq ''}
		{tab name="{tr}Intertiki server{/tr}"}
			<form action="tiki-admin.php?page=intertiki" method="post" name="intertiki">
				<table class="admin">
					<tr>
						<td>
							{tr}Intertiki shared cookie for sliding auth under same domain:{/tr}</td>
						<td>
							<input type="checkbox" name="feature_intertiki_sharedcookie" {if $prefs.feature_intertiki_sharedcookie eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td>
							{tr}Intertiki Server enabled:{/tr}
						</td>
						<td>
							<input type="checkbox" name="feature_intertiki_server" {if $prefs.feature_intertiki_server eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td>
							{tr}Access Log file:{/tr}
						</td>
						<td>
							<input type="text" name="intertiki_logfile" value="{$prefs.intertiki_logfile}" size="42" />
						</td>
					</tr>
					<tr>
						<td>
							{tr}Errors Log file:{/tr}
						</td>
						<td>
							<input type="text" name="intertiki_errfile" value="{$prefs.intertiki_errfile}" size="42" />
						</td>
					</tr>
					<tr>
						<td colspan="2" class="input_submit_container">
							{tr}Known hosts{/tr}
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table>
								<tr>
									<td>&nbsp;</td>
									<td>{tr}Name{/tr}</td>
									<td>{tr}Key{/tr}</td>
									<td>{tr}IP{/tr}</td>
									<td>{tr}Contact{/tr}</td>
								</tr>
								{if $prefs.known_hosts}
									{foreach key=k item=i from=$prefs.known_hosts}
										<tr>
											<td>
												<a href="tiki-admin.php?page=intertiki&amp;delk={$k|escape:'url'}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
											</td>
											<td>
												<input type="text" name="known_hosts[{$k}][name]" value="{$i.name}" size="12" />
											</td>
											<td>
												<input type="text" name="known_hosts[{$k}][key]" value="{$i.key}" size="32" />
											</td>
											<td>
												<input type="text" name="known_hosts[{$k}][ip]" value="{$i.ip}" size="12" />
											</td>
											<td>
												<input type="text" name="known_hosts[{$k}][contact]" value="{$i.contact}" size="22" />
											</td>
										</tr>
									{/foreach}
								{/if}
								<tr class="formrow">
									<td>{tr}New:{/tr}</td>
									<td><input type="text" name="newhost[name]" value="" size="12" /></td>
									<td><input type="text" name="newhost[key]" value="" size="32" /></td>
									<td><input type="text" name="newhost[ip]" value="" size="12" /></td>
									<td><input type="text" name="newhost[contact]" value="" size="22" /></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<div class="heading input_submit_container" style="text-align: center">
					<input type="submit" name="intertikiserver" value="{tr}Save{/tr}" />
				</div>
			</form>
		{/tab}
	{/if}
{/tabset}

