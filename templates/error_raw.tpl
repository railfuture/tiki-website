{if $prefs.feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}

<div id="tiki-mid">
	<div class="cbox">
		<div class="cbox-title">{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle"} {tr}Error{/tr}</div>
		<div class="cbox-data">
			{$msg}<br /><br />
		</div>
	</div>
</div>
{if $prefs.feature_bidi eq 'y'}
</td></tr></table>
{/if}
