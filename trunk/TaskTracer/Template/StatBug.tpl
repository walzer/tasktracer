<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type"     content="text/html; charset={$Lang.Charset}">
<meta http-equiv="Content-Language" content="{$Lang.Charset}">
<style type="text/css">
{$CssStyle}
</style>
<title>{$Lang.ProductName}</title>
</head>
<body style="margin:16px;">
 <table width="98%" align="center" class="CommonTable BugMode">
    <caption>{$Lang.OpenedBugsInLastWeek} ({$OneWeekBefore} - {$Yesterday})</caption>
    <tr>
      <th>{$Lang.DefaultBugQueryFields.OpenedBy}</th>
      <th>{$Lang.TotalCount}</th>
      <th>{$Lang.BugStatus.Active}</th>
      {foreach from=$Lang.BugResolutions key=ResolutionKey value=ResolutionValue}
      {if $ResolutionValue neq ''}<th>{$ResolutionValue}</th>{/if}
      {/foreach}
    </tr>
    {foreach from=$StatBugOfThisWeek key=UserName item=StatInfo}
    <tr>
      <th align="center">{$TestUserList[$UserName]|default:$UserName}</th>
      <td align="center">{$StatInfo.TotalCount}</td>
      <td align="center">{$StatInfo.Active}</td>
      {foreach from=$Lang.BugResolutions key=ResolutionKey value=ResolutionValue}
      {if $ResolutionValue neq ''}
      <td align="center">{$StatInfo[$ResolutionKey]}</td>{/if}
      {/foreach}
    </tr>
    {/foreach}
    <tr>
      <th>{$Lang.TotalCount}</th>
      <td align="center">{$StatBugOfThisWeekTotal.TotalCount}</td>
      <td align="center">{$StatBugOfThisWeekTotal.Active}</td>
      {foreach from=$Lang.BugResolutions key=ResolutionKey value=ResolutionValue}
      {if $ResolutionValue neq ''}
      <td align="center">{$StatBugOfThisWeekTotal[$ResolutionKey]}</td>{/if}
      {/foreach}
    </tr>
  </table>
  <br />
  <!-- <table width="98%" align="center" class="CommonTable BugMode">
     <caption>Bug {$Lang.ReportForms} [{$FirstDate} - {$Yesterday}]</caption>
     <tr>
       <th>{$Lang.DefaultBugQueryFields.OpenedBy}</th>
       <th>{$Lang.TotalCount}</th>
       <th>{$Lang.BugStatus.Active}</th>
       {foreach from=$Lang.BugResolutions key=ResolutionKey value=ResolutionValue}
       {if $ResolutionValue neq ''}<th>{$ResolutionValue}</th>{/if}
       {/foreach}
     </tr>
     {foreach from=$StatBugOfAllTime key=UserName item=StatInfo}
     <tr>
       <th align="center">{$TestUserList[$UserName]|default:$UserName}</th>
       <td align="center">{$StatInfo.TotalCount}</td>
       <td align="center">{$StatInfo.Active}</td>
       {foreach from=$Lang.BugResolutions key=ResolutionKey value=ResolutionValue}
       {if $ResolutionValue neq ''}
       <td align="center">{$StatInfo[$ResolutionKey]}</td>{/if}
       {/foreach}
     </tr>
     {/foreach}
     <tr>
       <th>{$Lang.TotalCount}</th>
       <td align="center">{$StatBugOfAllTimeTotal.TotalCount}</td>
       <td align="center">{$StatBugOfAllTimeTotal.Active}</td>
       {foreach from=$Lang.BugResolutions key=ResolutionKey value=ResolutionValue}
       {if $ResolutionValue neq ''}
       <td align="center">{$StatBugOfAllTimeTotal[$ResolutionKey]}</td>{/if}
       {/foreach}
     </tr>
  </table> -->
  <table width="98%" align="center" class="CommonTable BugMode">
     <caption>{$Lang.StaleBugsForOneWeek} (< {$OneWeekBefore})</caption>
     <tr>
       <th>{$Lang.DefaultBugQueryFields.AssignedTo}</th>
       <th>{$Lang.TotalCount}</th>
       <th>{$Lang.BugStatus.Active}</th>
       {foreach from=$Lang.BugResolutions key=ResolutionKey value=ResolutionValue}
       {if $ResolutionValue neq ''}<th>{$ResolutionValue}</th>{/if}
       {/foreach}
     </tr>
     {foreach from=$StatStaleBugs key=UserName item=StatInfo}
     <tr>
       <th align="center">{$TestUserList[$UserName]|default:$UserName}</th>
       <td align="center">{$StatInfo.TotalCount}</td>
       <td align="center">{$StatInfo.Active}</td>
       {foreach from=$Lang.BugResolutions key=ResolutionKey value=ResolutionValue}
       {if $ResolutionValue neq ''}
       <td align="center">{$StatInfo[$ResolutionKey]}</td>{/if}
       {/foreach}
     </tr>
     {/foreach}
     <tr>
       <th>{$Lang.TotalCount}</th>
       <td align="center">{$StatStaleBugsTotal.TotalCount}</td>
       <td align="center">{$StatStaleBugsTotal.Active}</td>
       {foreach from=$Lang.BugResolutions key=ResolutionKey value=ResolutionValue}
       {if $ResolutionValue neq ''}
       <td align="center">{$StatStaleBugsTotal[$ResolutionKey]}</td>{/if}
       {/foreach}
     </tr>
  </table>
</body>
</html>
