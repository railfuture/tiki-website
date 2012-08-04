<div class="box">
<div class="box-title">
{tr}Login{/tr}
{assign var="input_size" value="18"}
</div>
<div class="box-data">
    {if $user}
      {tr}logged as{/tr}: {$user}<br />
      <a class="linkmodule" href="tiki-logout.php">{tr}Logout{/tr}</a><br />
      {if $tiki_p_admin eq 'y'}
        <form action="{$login_url}" method="post">
        {tr}user{/tr}:
        <input type="text" name="username" size="8" />
        <input type="submit" name="su" value="{tr}set{/tr}" />
        </form>
      {/if}
    {else}
     <form name="loginbox" action="{$login_url}" method="post" {if $feature_challenge eq 'y'}onsubmit="doChallengeResponse()"{/if}> 
     {if $feature_challenge eq 'y'}
     <script language="javascript" type='text/javascript' src="lib/md5.js"></script>   
     {literal}
     <script language='Javascript' type='text/javascript'>
     <!--
     function doChallengeResponse() {
       hashstr = document.loginbox.user.value +
       document.loginbox.pass.value +
       document.loginbox.email.value;
       str = document.loginbox.user.value + 
       MD5(hashstr) +
       document.loginbox.challenge.value;
       document.loginbox.response.value = MD5(str);
       document.loginbox.pass.value='';
       /*
       document.login.password.value = "";
       document.logintrue.username.value = document.login.username.value;
       document.logintrue.response.value = MD5(str);
       document.logintrue.submit();
       */
       document.loginbox.submit();
       return false;
     }
     // -->
    </script>
    {/literal}
     <input type="hidden" name="challenge" value="{$challenge|escape}" />
     <input type="hidden" name="response" value="" />
     {/if}
      <table border="0">
      <tr>
        <td>
          <table>
          <tr><td class="module">{tr}user{/tr}:</td></tr>
          <tr><td><input type="text" name="user"  size="{$input_size}" /></td></tr>
          {if $feature_challenge eq 'y'} <!-- quick hack to make challenge/response work until 1.8 tiki auth overhaul -->
          <tr><td class="module">{tr}email{/tr}:</td></tr>
          <tr><td><input type="text" name="email"  size="{$input_size}" /></td></tr>
          {/if}
          <tr><td class="module">{tr}pass{/tr}:</td></tr>
          <tr><td><input type="password" name="pass" size="{$input_size}" /></td></tr>
          <tr><td><input type="submit" name="login" value="{tr}login{/tr}" /></td></tr>
          {if $rememberme ne 'disabled'}
          <tr><td class="module">{tr}Remember me{/tr} <input type="checkbox" name="rme" value="on"/></td></tr>
          {/if}
          <tr>
          {if $forgotPass eq 'y' and $allowRegister eq 'y'}
            <td valign="bottom">
              <a class="linkmodule" href="tiki-register.php">{tr}Create account...{/tr}</a><br/>
              <a class="linkmodule" href="tiki-remind_password.php">{tr}Lost password...{/tr}</a></br>
            </td>
          {/if}
          {if $forgotPass eq 'y' and $allowRegister ne 'y'}
            <td valign="bottom"><a class="linkmodule" href="tiki-remind_password.php">{tr}Lost password...{/tr}</a></td>
          {/if}
          {if $forgotPass ne 'y' and $allowRegister eq 'y'}
            <td valign="bottom"><a class="linkmodule" href="tiki-register.php">{tr}Create account...{/tr}</a></td>
          {/if}
          {if $forgotPass ne 'y' and $allowRegister ne 'y'}
          <td valign="bottom">&nbsp;</td>
          {/if}
          </tr>
          {if $http_login_url ne '' or $https_login_url ne ''}
          <tr>
          <td valign="bottom">
            <a class="linkmodule" href="{$http_login_url}">{tr}Standard login...{/tr}</a><br/>
            <a class="linkmodule" href="{$https_login_url}">{tr}Secure login...{/tr}</a><br/>
          </td>
          </tr>
          {/if}
          {if $show_stay_in_ssl_mode eq 'y'}
            <tr>
              <td>
                {tr}stay in ssl mode{/tr}:&nbsp;
                <input type="checkbox" name="stay_in_ssl_mode" {if $stay_in_ssl_mode eq 'y'}checked="checked"{/if} />
              </td>
            </tr>
          {/if}
          </table>
        </td>
      </tr>
      </table>

      {if $show_stay_in_ssl_mode ne 'y'}
        <input type="hidden" name="stay_in_ssl_mode" value="{$stay_in_ssl_mode|escape}" />
      {/if}
      </form>
    {/if}
</div>
</div>
