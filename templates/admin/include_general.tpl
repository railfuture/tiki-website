{* $Id: include_general.tpl 42500 2012-07-31 16:51:15Z robertplummer $ *}

<form action="tiki-admin.php?page=general" class="admin" method="post">
	<input type="hidden" name="new_prefs" />
	<div class="heading input_submit_container" style="text-align: right;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
	{if !empty($error_msg)}
		{remarksbox type='warning' title="{tr}Warning{/tr}" icon='error'}
			{$error_msg}
		{/remarksbox}
	{/if}

	{tabset name="admin_general"}
		{tab name="{tr}General Preferences{/tr}"}
			<fieldset>
				<legend>{tr}Release Check{/tr}</legend>
				<div class="adminoptionbox">{tr}Tiki version:{/tr}
					<strong>
						{if !empty($lastup)}
							{tr}Last update from SVN{/tr} ({$tiki_version}): {$lastup|tiki_long_datetime}
								{if $svnrev}
									- REV {$svnrev}
								{/if}
						{else}
							{$tiki_version}
						{/if}
					</strong>
					({$db_engine_type})
					{button href="tiki-install.php" _text="{tr}Reset or upgrade your database{/tr}"}
				</div>

				<div class="adminoptionbox">
					{preference name=feature_version_checks}
					<div id="feature_version_checks_childcontainer">
						{preference name=tiki_version_check_frequency}
					</div>
					{button href="tiki-admin.php?page=general&amp;forcecheck=1" _text="{tr}Check for updates now{/tr}."}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Site Identity{/tr}</legend>
				{preference name=sender_email}
				{preference name=browsertitle}
				{preference name=site_title_location}
				{preference name=site_title_breadcrumb}

				<div class="adminoptionbox">
					{tr}Go to <a href="tiki-admin.php?page=look" title=""><strong>Look & Feel</strong></a> section for additional site related customization preferences{/tr}.
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Mail{/tr}</legend>
				{preference name=default_mail_charset}
				{preference name=mail_crlf}
				{preference name=zend_mail_handler}
				<div class="adminoptionboxchild zend_mail_handler_childcontainer smtp">
					{preference name=zend_mail_smtp_server}

					{preference name=zend_mail_smtp_auth}
					<div class="adminoptionboxchild zend_mail_smtp_auth_childcontainer login plain crammd5">
						<p>{tr}These values will be stored in plain text in the database:{/tr}</p>
						{preference name=zend_mail_smtp_user}
						{preference name=zend_mail_smtp_pass}
					</div>

					{preference name=zend_mail_smtp_port}
					{preference name=zend_mail_smtp_security}
				</div>
				<div class="adminoptionbox">
					<label for="testMail">{tr}Email to send a test mail{/tr}</label>
					<input type="text" name="testMail" id="testMail" />
				</div>
				{preference name=email_footer}
			</fieldset>
			<fieldset>
				<legend>{tr}Newsletter{/tr}</legend>
				{preference name=newsletter_throttle}
				<div class="adminoptionboxchild" id="newsletter_throttle_childcontainer">
					{preference name=newsletter_pause_length}
					{preference name=newsletter_batch_size}
				</div>
				{preference name=newsletter_external_client}
			</fieldset>

			<fieldset>
				<legend>{tr}Logging and Reporting{/tr}</legend>
				<div class="adminoptionbox">
					{preference name=error_reporting_level}
					<div class="adminoptionboxchild">
						{preference name=error_reporting_adminonly label="{tr}Visible to admin only{/tr}"}
						{preference name=smarty_notice_reporting label="{tr}Include Smarty notices{/tr}"}
					</div>
				</div>
				{preference name=disableJavascript}

				{preference name=log_mail}
				{preference name=log_sql}
				<div class="adminoptionboxchild" id="log_sql_childcontainer">
					{preference name=log_sql_perf_min}
				</div>
			</fieldset>

		{/tab}

		{tab name="{tr}General Settings{/tr}"}
			<fieldset>
				<legend>{tr}Server{/tr}</legend>
				{preference name=tmpDir}
				{preference name=use_proxy}
				<div class="adminoptionboxchild" id="use_proxy_childcontainer">
					{preference name=proxy_host}
					{preference name=proxy_port}

					{preference name=proxy_user}
					{preference name=proxy_pass}
				</div>

				{preference name=http_skip_frameset}
				{preference name=feature_loadbalancer}
			</fieldset>

			<fieldset>
				<legend>{tr}Multi-domain{/tr}</legend>
				{preference name=multidomain_active}
				{preference name=multidomain_switchdomain}
				<div class="adminoptionboxchild" id="multidomain_active_childcontainer">
					{preference name=multidomain_config}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Sessions{/tr}</legend>
				{remarksbox type="note" title="{tr}Advanced configuration warning{/tr}"}
					{tr}Note that storing session data in the database is an advanced systems administration option, and is for admins who have comprehensive access and understanding of the database, in order to deal with any unexpected effects.{/tr}
				{/remarksbox}
				<div style="padding:.5em;" align="left">
					{icon _id=information style="vertical-align:middle"} {tr}Changing this feature will immediately log you out when you save this preference.{/tr} {if $prefs.forgotPass ne 'y'}If there is a chance you have forgotten your password, enable "Forget password" feature.<a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
				</div>
				{preference name=session_storage}
				{preference name=session_lifetime}
				{preference name=session_cookie_name}
			</fieldset>

			<fieldset>
				<legend>{tr}Site Terminal{/tr}</legend>
				{preference name=site_terminal_active}
				<div class="adminoptionboxchild" id="site_terminal_active_childcontainer">
					{preference name=site_terminal_config}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Contact{/tr}</legend>
				{preference name=feature_contact}
				<div class="adminoptionboxchild" id="feature_contact_childcontainer">
					{preference name=contact_anon}
					{preference name=contact_user}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Stats{/tr}</legend>
				{preference name=feature_stats}
				{preference name=feature_referer_stats}
				{preference name=count_admin_pvs}
			</fieldset>

			<fieldset>
				<legend>{tr}Print{/tr}</legend>
				{preference name=print_pdf_from_url}
				<div class="adminoptionboxchild print_pdf_from_url_childcontainer webkit">
					{preference name=print_pdf_webkit_path}
				</div>
				<div class="adminoptionboxchild print_pdf_from_url_childcontainer webservice">
					{preference name=print_pdf_webservice_url}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Miscellaneous{/tr}</legend>
				{preference name=feature_help}
				<div class="adminoptionboxchild" id="feature_help_childcontainer">
					{preference name=helpurl}
				</div>
			</fieldset>
		{/tab}

		{tab name="{tr}Navigation{/tr}"}
			<fieldset>
				<legend>{tr}Menus{/tr}</legend>
				<em>{tr}Create and edit menus {/tr}</em><a href="tiki-admin_menus.php"><em>{tr}here{/tr}</em></a>
				<div class="adminoptionbox">
					{preference name=feature_cssmenus}
					{preference name=menus_item_names_raw_teaser}
					<div class="adminoptionboxchild" id="menus_item_names_raw_teaser_childcontainer">	
						{preference name=menus_item_names_raw}
					</div>
					{preference name=feature_userlevels}
					{preference name=feature_featuredLinks}
					{preference name=feature_menusfolderstyle}
					{preference name=menus_items_icons}
					<div id="menus_items_icons_childcontainer">
						{preference name='menus_items_icons_path'}
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Home Page{/tr}</legend>
				<div class="adminoptionbox">
					{preference name=useGroupHome}
					<div id="useGroupHome_childcontainer">
						{preference name=limitedGoGroupHome}
					</div>
				</div>

				{preference name=tikiIndex defaul=$prefs.site_tikiIndex}

				{preference name=useUrlIndex}
				<div class="adminoptionboxchild" id="useUrlIndex_childcontainer">
					{preference name=urlIndex}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Redirects{/tr}</legend>
				{preference name=tiki_domain_prefix}
				{preference name=tiki_domain_redirects}
				{preference name=feature_redirect_on_error}
				{preference name='feature_wiki_1like_redirection'}
				{preference name='permission_denied_login_box' mode='invert'}
				<div class="adminoptionboxchild" id="permission_denied_login_box_childcontainer">
					{tr}or{/tr}
					<br />
					{preference name=permission_denied_url}
				</div>
				{preference name='url_anonymous_page_not_found'}
				{preference name='url_after_validation'}
				{preference name='feature_alternate_registration_page'}
			</fieldset>

			<fieldset>
				<legend>{tr}User{/tr}</legend>
				{preference name='urlOnUsername'}
			</fieldset>

			<fieldset>
				<legend>{tr}Site Access{/tr}</legend>
				{preference name=site_closed}
				<div class="adminoptionboxchild" id="site_closed_childcontainer">
					{preference name=site_closed_msg}
				</div>

				{preference name=use_load_threshold}
				<div class="adminoptionboxchild" id="use_load_threshold_childcontainer">
					{preference name=load_threshold}
					{preference name=site_busy_msg}
				</div>
			</fieldset>

			<fieldset>
				<legend class="heading">{tr}Breadcrumbs{/tr}</legend>

				{preference name=feature_breadcrumbs}
				<div class="adminoptionboxchild" id="feature_breadcrumbs_childcontainer">
					{preference name=feature_siteloclabel}
					{preference name=feature_siteloc}
					{preference name=feature_sitetitle}
					{preference name=feature_sitedesc}
				</div>
			</fieldset>


		{/tab}

		{tab name="{tr}Date and Time{/tr}"}
			{preference name=server_timezone}
			{preference name=users_prefs_display_timezone}
			{preference name=long_date_format}
			<em>{tr}Sample:{/tr} {$now|tiki_long_date}</em>

			{preference name=short_date_format}
			<em>{tr}Sample:{/tr} {$now|tiki_short_date}</em>

			{preference name=long_time_format}
			<em>{tr}Sample:{/tr} {$now|tiki_long_time}</em>

			{preference name=short_time_format}
			<em>{tr}Sample:{/tr} {$now|tiki_short_time}</em>

			{preference name=display_field_order}
			{preference name=users_prefs_display_12hr_clock}
			{preference name=tiki_same_day_time_only}
			{preference name=wikiplugin_now}
			{preference name=wikiplugin_countdown}
			{preference name=wikiplugin_timesheet}
			{preference name=wikiplugin_convene}

			<div class="adminoptionbox">
				{assign var="fcnlink" value="http://www.php.net/manual/en/function.strftime.php"}
				<a class="link" target="strftime" href="{$fcnlink}">{tr}Date and Time Format Help{/tr}</a>{help url="Date+and+Time"}
			</div>
		{/tab}

		{tab name="{tr}Change admin password{/tr}"}
			<div style="padding:1em;" align="left">
				<p>{tr}Change the <strong>Admin</strong> password:{/tr} <a href="tiki-adminusers.php?find=admin">{tr}User administration{/tr}</a></p>
			</div>
		{/tab}
	{/tabset}

	<div class="heading input_submit_container" style="text-align: center;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
