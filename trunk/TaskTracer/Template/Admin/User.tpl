{include file="Admin/AdminHeader.tpl"}
<body>
{include file="Admin/AdminTopNav.tpl"}
  <span class="AdminNav">
    &lt;<a href="AdminUserList.php">{$Lang.BackToUserList}</a>&#124;
    <strong>{$UserInfo.UserName}</strong>
  </span>
  {include file="ActionMessage.tpl"}
  <form id="UserForm" action="" method="post" onsubmit="xajax_xAdmin{$ActionType}(xajax.getFormValues('UserForm'));return false;">
  <div id="UForm" class="CommonForm AdminForm">
    <h1>{if $ActionType eq 'AddUser'}{$Lang.AddUser}{else}{$Lang.EditUser}{/if}</h1>
    <dl>
      <dt><label>{$Lang.UserName}</label></dt>
      <dd>
        {if $ActionType eq 'AddUser'}<input type="text" id="UserName" name="UserName" maxlength="20" value="" />{else}
        {$UserInfo.UserName}<input type="hidden" id="UserName" name="UserName" maxlength="20" value="{$UserInfo.UserName}" />
        {/if}
      </dd>
    </dl>
    <dl>
      <dt><label for="RealName">{$Lang.RealName}</label></dt>
      <dd><input type="text" id="RealName" name="RealName" value="{$UserInfo.RealName}" />{if $ActionType eq 'AddUser'}{$Lang.RealNameNote}{/if}</dd>
    </dl>
    <dl>
      <dt><label for="UserPassword">{$Lang.UserPassword}</label></dt>
      <dd><input type="text" id="UserPassword" name="UserPassword" value="" />{if $ActionType eq 'AddUser'}{$Lang.RawUserPasswordNote}{/if}</dd>
    </dl>
    <dl>
      <dt><label for="Email">{$Lang.Email}</label></dt>
      <dd><input type="text" id="Email" name="Email" value="{$UserInfo.Email}" />{if $ActionType eq 'AddUser'}{$Lang.EmailNote}{/if}</dd>
    </dl>
    <dl class="Line"></dl>
    <dl>
      <input type="submit" name="SaveUser" value="{$Lang.Save}" />
      <input type="hidden" id="UserID" name="UserID" value="{$UserInfo.UserID}" />
    </dl>
  </div>
  </form>
</body>
</html>
