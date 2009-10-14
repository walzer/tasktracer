{include file="Admin/AdminHeader.tpl"}
<body>
{include file="Admin/AdminTopNav.tpl"}
  <table class="Commontable AdminTable">
    <caption>{$Lang.UserLogList}{$PaginationHtml}</caption>
    <tr>
      <th>{$Lang.LogID}</th>
      <th>{$Lang.UserName}</th>
      <th>{$Lang.LoginIP}</th>
      <th>{$Lang.LoginTime}</th>
    </tr>
    {foreach item=UserLog from=$UserLogList}
    <tr>
      <td>{$UserLog.LogID}</td>
      <td>{$UserLog.UserName}</td>
      <td>{$UserLog.LoginIP}</td>
      <td>{$UserLog.LoginTime}</td>
    </tr>
    {/foreach}
  </table>
</body>
</html>
