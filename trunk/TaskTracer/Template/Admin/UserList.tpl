{include file="Admin/AdminHeader.tpl"}
<body>
{include file="Admin/AdminTopNav.tpl"}
  <table class="CommonTable AdminTable">
    <caption>{$Lang.UserList}{$PaginationHtml}{if !$OtherUserDB}<a href="AdminUser.php?ActionType=AddUser" class="BigButton">{$Lang.AddUser}</a>{/if}
    <form name="SearchUserForm" method="get"><input type="text" id="SearchUser" name="SearchUser" value="{$SearchUser}" /><input type="submit" /></form>
    </caption>
    <tr>
      {if !$OtherUserDB}
      <th>{$Lang.UserID}</th>{/if}
      <th>{$Lang.UserName}</th>
      <th>{$Lang.RealName}</th>
      <th>{$Lang.Email}</th>
      <th width="20%">{$Lang.GroupList}</th>
      {if !$OtherUserDB}
      <th>{$Lang.Edit}</th>{/if}
      <th>{$Lang.LastModifiedBy}</th>
      <th>{$Lang.LastTime}</th>
      <th>{$Lang.AddedBy}</th>
      <th>{$Lang.AddTime}</th>
    </tr>
    {foreach item=User from=$UserList}
    <tr{if $User.IsDroped eq '1'} bgcolor="#E0E0E0"{/if}>
      {if !$OtherUserDB}
      <td>{$User.UserID}</td>{/if}
      <td>{$User.UserName}</td>
      <td>{$User.RealName}</td>
      <td>{$User.Email}</td>
      <td>{$User.UserGroupListHTML}</td>
      {if !$OtherUserDB}
      <td>
	    {if $templatelite.session.TestIsAdmin || $templatelite.SESSION.TestUserName eq $User.AddedBy || $templatelite.SESSION.TestUserName eq $User.UserName}
          <a href="AdminUser.php?ActionType=EditUser&UserID={$User.UserID}">{$Lang.Edit}</a>
          {if $templatelite.SESSION.TestUserName neq $User.UserName}|
            {if $User.IsDroped eq '0'}
              <a href="AdminUser.php?ActionType=EditUser&UserID={$User.UserID}&amp;IsDroped=0" onclick="return confirm('{$Lang.ConfirmDelUser}');">{$Lang.DropUser}</a>
            {else}
              <a href="AdminUser.php?ActionType=EditUser&UserID={$User.UserID}&amp;IsDroped=1" onclick="return confirm('{$Lang.ConfirmActiveUser}');">{$Lang.ActiveUser}</a>
            {/if}
 		  {/if}
        {/if}
      </td>
      <td>{assign var="UserName" value=$User.LastEditedBy}{$UserNameList[$UserName]}</td>
	  <td>{if $User.LastDate neq $CFG.ZeroTime}{$User.LastDate}{/if}</td>
	  <td>{assign var="UserName" value=$User.AddedBy}{$UserNameList[$UserName]}</td>
	  <td>{if $User.AddDate neq $CFG.ZeroTime}{$User.AddDate}{/if}</td>{/if}
    </tr>
    {/foreach}
  </table>
{literal}
<script type="text/javascript">
//<![CDATA[
xajax.$('SearchUser').focus();
//]]>
</script>
{/literal}
</body>
</html>
