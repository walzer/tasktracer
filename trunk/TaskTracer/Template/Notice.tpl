<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type"     content="text/html; charset={$Lang.Charset}">
<meta http-equiv="Content-Language" content="{$Lang.Charset}">
<style type="text/css">
{$CssStyle}
</style>
<title>{$Lang.Title}</title>
</head>
<body style="margin:16px;">
 <table width="98%" align="center" class="CommonTable BugMode">
    <tr>
      <th>{$Lang.DefaultBugQueryFields.BugID}</th>
      <th>{$Lang.DefaultBugQueryFields.BugSeverity}</th>
      <th>{$Lang.DefaultBugQueryFields.BugTitle}</th>
      <th>{$Lang.DefaultBugQueryFields.BugStatus}</th>
      <th>{$Lang.DefaultBugQueryFields.OpenedBy}</th>
      <th>{$Lang.DefaultBugQueryFields.AssignedTo}</th>
      <th>{$Lang.DefaultBugQueryFields.ResolvedBy}</th>
      <th>{$Lang.DefaultBugQueryFields.Resolution}</th>
    </tr>
    {foreach from=$UserBugList item=BugInfo}
    <tr>
      <td align="center">
        <a target="_blank" href="{$BaseUrl}/Bug.php?BugID={$BugInfo.BugID}">{$BugInfo.BugID}</a>
      </td>
      <td align="center">{$BugInfo.BugSeverityName}</td>
      <td title="{$BugInfo.BugTitle}" >{$BugInfo.ListTitle}</td>
      <td align="center">{$BugInfo.BugStatusName}</td>
      <td align="center">{$BugInfo.OpenedByName}</td>
      <td align="center">{$BugInfo.AssignedToName}</td>
      <td align="center">{$BugInfo.ResolvedByName}</td>
      <td align="center">{$BugInfo.ResolutionName}</td>
    </tr>
    {/foreach}
  </table>
</body>
</html>
