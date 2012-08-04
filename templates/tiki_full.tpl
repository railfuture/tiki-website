{* $Id: tiki_full.tpl 33949 2011-04-14 05:13:23Z chealer $ *}<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>

{* Index we display a wiki page here *}
{if $prefs.feature_bidi eq 'y'}
<div dir="rtl">
{/if}
{if $prefs.feature_ajax eq 'y'}
{include file='tiki-ajax_header.tpl'}
{/if}
<div id="main">
	<div id="tiki-center">
		<div id="role_main">
			{$mid_data}
			{show_help}
		</div>
	</div>
</div>
			
{if $prefs.feature_bidi eq 'y'}
</div>
{/if}
{include file='footer.tpl'}
<!-- Put JS at the end -->
{if $headerlib}
	{$headerlib->output_js_files()}
	{$headerlib->output_js()}
{/if}
	</body>
</html>
