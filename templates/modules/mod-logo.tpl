{* $Id: mod-logo.tpl 40585 2012-04-01 14:44:50Z jonnybradley $ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="logo" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
   <div id="sitelogo"{if $prefs.sitelogo_bgcolor ne ''} style="background-color: {$prefs.sitelogo_bgcolor};" {/if} class="floatleft">
      {if $prefs.sitelogo_src}<a href="./" title="{$prefs.sitelogo_title}"{if $prefs.mobile_mode eq "y"} rel="external"{/if}><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>
      {/if}
   </div>
   <div id="sitetitles" class="floatleft">
      <div id="sitetitle">
		  {if !empty($prefs.sitetitle)}<a href="./"{if $prefs.mobile_mode eq "y"} rel="external"{/if}>{$prefs.sitetitle}</a>{/if}
	  </div>
      <div id="sitesubtitle">{if !empty($prefs.sitesubtitle)}{$prefs.sitesubtitle}{/if}</div>
   </div>
{/tikimodule}
