{* $Id: include_copyright.tpl 36194 2011-08-15 19:57:12Z marclaporte $ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Copyright allows to determine a copyright for all the objects of tiki{/tr}.{/remarksbox}

<form action="tiki-admin.php?page=copyright" method="post">
	<div class="input_submit_container clear" style="text-align: right;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
	<input type="hidden" name="setcopyright" />
	
	<fieldset class="admin">
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_copyright visible="always"}
	</fieldset>

	<fieldset>
		<legend>{tr}Copyright management{/tr}</legend>
		{preference name=wikiLicensePage}
		{preference name=wikiSubmitNotice}

		<div class="adminoptionbox">
			<div class="adminoptionlabel">{tr}Enable copyright management for:{/tr}</div>
			<div class="adminoptionboxchild">
				{preference name=wiki_feature_copyrights}
				{preference name=articles_feature_copyrights}
				{preference name=blogs_feature_copyrights}
				{preference name=faqs_feature_copyrights}
			</div>
		</div>
	</fieldset>

	<div class="input_submit_container clear" style="text-align: center;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
