{popup_init src="lib/overlib.js"}

<!--Check to see if there is an editing conflict-->
{if $editpageconflict == 'y'}
<script language='Javascript' type='text/javascript'>
<!-- Hide Script
	alert('{tr}This page is being edited by{/tr} {$semUser}. {tr}Proceed at your own peril{/tr}.')
//End Hide Script-->
</script>
{/if}

<!--<a {popup sticky="true" trigger="onClick" caption="Special characters help" text="kj"}>foo</a><br />-->
{if $preview}
{include file="tiki-preview.tpl"}
{/if}
<h1>{tr}Edit{/tr}: {$page}</h1>
{if $page eq 'SandBox'}
<div class="wikitext">
{tr}The SandBox is a page where you can practice your editing skills, use the preview feature to preview the appeareance of the page, no versions are stored for this page.{/tr}
</div>
{/if}
<form  enctype="multipart/form-data" method="post" action="tiki-editpage.php" id='editpageform'>
<div style="margin-bottom:1px;">
{if !$lock}
  {if $tiki_p_edit eq 'y' or $page eq 'SandBox'}
    {if $beingEdited eq 'y'}
      <span class="tabbut"><a title="{$semUser}" href="tiki-editpage.php?page={$page|escape:"url"}" class="tablink">{tr}edit{/tr}</a></span>
    {else}
      <span class="tabbut"><a href="tiki-editpage.php?page={$page|escape:"url"}" class="tablink">{tr}edit{/tr}</a></span>
    {/if}
  {/if}
{/if}

{if $page ne 'SandBox'}
  {if $tiki_p_remove eq 'y'}
    <span class="tabbut"><a href="tiki-removepage.php?page={$page|escape:"url"}&amp;version=last" class="tablink">{tr}remove{/tr}</a></span>
  {/if}
  {if $tiki_p_rename eq 'y'}
    <span class="tabbut"><a href="tiki-rename_page.php?page={$page|escape:"url"}" class="tablink">{tr}rename{/tr}</a></span>
  {/if}
{/if}

{if $page ne 'SandBox'}
  {if $tiki_p_admin_wiki eq 'y' or ($user and ($user eq $page_user) and ($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y'))}
    {if $lock}
      <span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=unlock" class="tablink">{tr}unlock{/tr}</a></span>
    {else}
      <span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=lock" class="tablink">{tr}lock{/tr}</a></span>
    {/if}
  {/if}
  {if $tiki_p_admin_wiki eq 'y'}
    <span class="tabbut"><a href="tiki-pagepermissions.php?page={$page|escape:"url"}" class="tablink">{tr}perms{/tr}</a></span>
  {/if}
{/if}

{if $page ne 'SandBox'}
  {if $feature_history eq 'y'}
    <span class="tabbut"><a href="tiki-pagehistory.php?page={$page|escape:"url"}" class="tablink">{tr}history{/tr}</a></span>
  {/if}
{/if}

{if $feature_likePages eq 'y'}
  <span class="tabbut"><a href="tiki-likepages.php?page={$page|escape:"url"}" class="tablink">{tr}similar{/tr}</a></span>
{/if}

{if $feature_wiki_undo eq 'y' and $canundo eq 'y'}
  <span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;undo=1" class="tablink">{tr}undo{/tr}</a></span>
{/if}


{if $tiki_p_admin_wiki eq 'y'}
<span class="tabbut"><a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}" class="tablink">{tr}export{/tr}</a></span>
{/if}

{if $feature_wiki_discuss eq 'y'}
  <span class="tabbut"><a href="tiki-view_forum.php?forumId={$wiki_forum_id}&comments_postComment=post&comments_title={$page|escape:"url"}&comments_data={"Use this thread to discuss the [tiki-index.php?page="}{$page}{"|"}{$page}{"] page."|escape:"url"}&comment_topictype=n" class="tablink">{tr}discuss{/tr}</a></span>
{/if}

<span class="tabbut"><a href="javascript:flip('edithelpzone');" class="linkbut">{tr}Wiki quick help{/tr}</a></span>
</div>

<table class="normal">
<tr><td class="formcolor">{tr}Quicklinks{/tr}:</td><td class="formcolor">
{assign var=area_name value="editwiki"}
{include file=tiki-edit_help_tool.tpl}
</td></tr>

{include file=categorize.tpl}

{if $feature_wiki_templates eq 'y' and $tiki_p_use_content_templates eq 'y'}
<tr><td class="formcolor">{tr}Apply template{/tr}:</td><td class="formcolor">
<select name="templateId" onChange="javascript:document.getElementById('editpageform').submit();">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$templates}
<option value="{$templates[ix].templateId|escape}">{tr}{$templates[ix].name}{/tr}</option>
{/section}
</select>
</td></tr>
{/if}
{if $feature_smileys eq 'y'}
<tr><td class="formcolor">{tr}Smileys{/tr}:</td><td class="formcolor">
<table>
     <tr>
          <td><a href="javascript:setSomeElement('editwiki','(:biggrin:)');"><img src="img/smiles/icon_biggrin.gif" alt="big grin" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:confused:)');"><img src="img/smiles/icon_confused.gif" alt="confused" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:cool:)');"><img src="img/smiles/icon_cool.gif" alt="cool" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:cry:)');"><img src="img/smiles/icon_cry.gif" alt="cry" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:eek:)');"><img src="img/smiles/icon_eek.gif" alt="eek" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:evil:)');"><img src="img/smiles/icon_evil.gif" alt="evil" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:exclaim:)');"><img src="img/smiles/icon_exclaim.gif" alt="exclaim" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:frown:)');"><img src="img/smiles/icon_frown.gif" alt="frown" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:idea:)');"><img src="img/smiles/icon_idea.gif" alt="idea" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:lol:)');"><img src="img/smiles/icon_lol.gif" alt="lol" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:mad:)');"><img src="img/smiles/icon_mad.gif" alt="mad" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:mrgreen:)');"><img src="img/smiles/icon_mrgreen.gif" alt="mr green" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:neutral:)');"><img src="img/smiles/icon_neutral.gif" alt="neutral" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:question:)');"><img src="img/smiles/icon_question.gif" alt="question" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:razz:)');"><img src="img/smiles/icon_razz.gif" alt="razz" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:redface:)');"><img src="img/smiles/icon_redface.gif" alt="redface" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:rolleyes:)');"><img src="img/smiles/icon_rolleyes.gif" alt="rolleyes" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:sad:)');"><img src="img/smiles/icon_sad.gif" alt="sad" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:smile:)');"><img src="img/smiles/icon_smile.gif" alt="smile" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:surprised:)');"><img src="img/smiles/icon_surprised.gif" alt="surprised" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:twisted:)');"><img src="img/smiles/icon_twisted.gif" alt="twisted" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:wink:)');"><img src="img/smiles/icon_wink.gif" alt="wink" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:arrow:)');"><img src="img/smiles/icon_arrow.gif" alt="arrow" border="0" /></a></td>
      </tr>    
      </table>
</td>
</tr>
{/if}
<!--<a class="link" href="javascript:setSomeElement('editwiki',"''text here''");">i</a>-->
{if $feature_wiki_description}
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><input size="80" class="wikitext" type="text" name="description" value="{$description|escape}" /></td>
{/if}
<tr><td class="formcolor">{tr}Edit{/tr}:</td><td class="formcolor">
<textarea id='editwiki' class="wikiedit" name="edit" rows="22" wrap="virtual" cols="80">{$pagedata|escape}</textarea>
</td>
{if $feature_wiki_footnotes eq 'y'}
{if $user}
<tr><td class="formcolor">{tr}Footnotes{/tr}:</td><td class="formcolor"><textarea name="footnote" rows="8" cols="80">{$footnote|escape}</textarea></td>
{/if}
{/if}

{if $page ne 'SandBox'}
<tr><td class="formcolor">{tr}Comment{/tr}:</td><td class="formcolor"><input size="80" class="wikitext" type="text" name="comment" value="{$commentdata|escape}" /></td>
{/if}
{if $wiki_feature_copyrights  eq 'y'}
<tr><td class="formcolor">{tr}Copyright{/tr}:</td><td class="formcolor">
<table border="0">
<tr><td class="formcolor">{tr}Title:{/tr}</td><td><input size="40" class="wikitext" type="text" name="copyrightTitle" value="{$copyrightTitle|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Year:{/tr}</td><td><input size="4" class="wikitext" type="text" name="copyrightYear" value="{$copyrightYear|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Authors:{/tr}</td><td><input size="40" class="wikitext" name="copyrightAuthors" type="text" value="{$copyrightAuthors|escape}" /></td></tr>
</table>
</td>
{/if}
{if $tiki_p_use_HTML eq 'y'}
<tr><td class="formcolor">{tr}Allow HTML{/tr}: </td><td class="formcolor"><input type="checkbox" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}/></td>
{/if}
{if $wiki_spellcheck eq 'y'}
<tr><td class="formcolor">{tr}Spellcheck{/tr}: </td><td class="formcolor"><input type="checkbox" name="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if}/></td>
{/if}
{if $tiki_p_admin_wiki eq 'y'}
<tr><td class="formcolor">{tr}Import page{/tr}:</td><td class="formcolor">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000">
<input name="userfile1" type="file">
<a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}&amp;all=1" class="link">{tr}export all versions{/tr}</a>
</td></tr>
{/if}
{if $feature_wiki_pictures eq 'y' and $tiki_p_upload_picture eq 'y'}
<tr><td class="formcolor">{tr}Upload picture{/tr}</td><td class="formcolor">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000">
<input name="picfile1" type="file">
</td></tr>
{/if}

<input type="hidden" name="page" value="{$page|escape}" />
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" /></td>

{if $wiki_feature_copyrights  eq 'y'}
<tr><td class="formcolor">{tr}License{/tr}:</td><td class="formcolor"><a href="tiki-index.php?page={$wikiLicensePage}">{tr}{$wikiLicensePage}{/tr}</a></td>
{if $wikiSubmitNotice neq ""}
<tr><td class="formcolor">{tr}Important{/tr}:</td><td class="formcolor"><b>{tr}{$wikiSubmitNotice}{/tr}</b></td>
{/if}
{/if}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor">
{if $tiki_p_minor eq 'y'}
<input type="checkbox" name="isminor" />{tr}Minor{/tr}
{/if}
<input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" /> <a class="link" href="tiki-index.php?page={$page|escape:"url"}">{tr}cancel edit{/tr}</a></td>
</tr>
</table>
</form>
<br />
<!--<a href="javascript:replaceSome('editwiki','foo','bar');">foo2bar</a>-->
{include file=tiki-edit_help.tpl}
