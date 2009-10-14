{include file="XmlHeader.tpl"}
{include file="Header.tpl"}
<script>if(parent.window==window)window.location='index.php';</script>
<body class="{$TestMode}Mode PaddingBoy" onclick="hiddenDivCustomSetTable()" style ="overflow:auto;" onload="initShowGotoBCR();">
{include file="CustomField.tpl"}
  <table class="CommonTable ListTable BugMode">
    <tr>
      <td colspan="{$FieldToShowCount}" class="TdCaption">
      <table style="font-size:12px;width:100%;">
        <tr>
          <td style="text-align:left;border:0;width:30%">
            {$Lang.Pagination.Result} {$PaginationDetailInfo.FromNum}-{$PaginationDetailInfo.ToNum}/{$PaginationDetailInfo.RecTotal}
{$PaginationDetailInfo.RecPerPageList}
          </td>
          <td style="border:0;text-align:center;width:40%">
            {$PaginationDetailInfo.PrePageSymImgLink}&nbsp;&nbsp;
            {$PaginationDetailInfo.NextPageLiteralLink}&nbsp;
            {$PaginationDetailInfo.NextPageSymImgLink}
          </td>
          <td style="text-align:right;border:0;width:30%">{literal}
            <a href="javascript:void(0);" id="CustomSetLink" onclick="x=event.clientX+document.body.scrollLeft;y=event.clientY;showDivCustomSetTable(x,y);">{$Lang.CustomDisplay}</a>|
            <a href="?Export=HtmlTable" target="_blank"><img src="Image/export.gif"/>&nbsp;{$Lang.ExportHtmlTable}</a>|
            <a href="Report.php?ReportMode={$TestMode}" target="'{$TestMode}Report"><img src="Image/report.gif"/>&nbsp;{$Lang.ReportForms}</a>
          </td>
        </tr>
      </table>
    </td>
    </tr>
    <tr>
      <td colspan="{$FieldToShowCount}">
    <div id="ListSubTable" style="height:350px;overflow-y:auto;overflow-x:auto">
    <table class="CommonTable CommonSubTable {$TestMode}Mode">
    <colgroup>
      {foreach from=$FieldsToShow key=Field item=FieldName}
      <col{if $QueryColumn eq $Field} class="SortActive"{/if}{if $QueryColumn eq 'BugSeverity'}style="width:3%;"{/if}></col>
      {/foreach}
    <colgroup>
    <tr>
      {foreach from=$FieldsToShow key=Field item=FieldName}
      <th align="{if $Field == 'BugID' || $Field == 'BugSeverity'}center{else}left{/if}">
        <nobr><a href="?OrderBy={$Field}|{$OrderByTypeList[$Field]}&QueryMode={$QueryMode}">{if $Field == 'BugSeverity'}Sev{elseif $Field == 'BugPriority'}Pri{else}{$FieldName}{/if}</a>{if $OrderByColumn == $Field}{$OrderTypeArrowArray[$OrderByType]}{/if}</nobr>
      </th>
      {/foreach}
    </tr>
    {foreach from=$BugList item=BugInfo}
    <tr class="BugStatus{$BugInfo.BugStatus}">
      {foreach from=$FieldsToShow key=Field item=FieldName}
      <td align="{if $Field == 'BugID' || $Field == 'BugSeverity' || $Field == 'BugPriority'}center{else}left{/if}"{if $QueryColumn eq $Field} class="SortActive"{/if}>
        {assign var="FieldValue" value=$Field."Name"}
          {if $Field == 'BugID' || $Field == 'BugSeverity' || $Field == 'BugPriority'}<nobr>{$BugInfo[$FieldValue]|default:$BugInfo[$Field]}</nobr>
          {elseif $Field == 'BugTitle'}
          <a href="Bug.php?BugID={$BugInfo.BugID}" title="{$BugInfo.BugTitle}" class="FullLink" target="_blank"><nobr>{$BugInfo.ListTitle}</nobr></a>
          {elseif $Field == 'OpenedDate' || $Field == 'AssignedDate' || $Field == 'ResolvedDate' || $Field == 'ClosedDate' || $Field == 'LastEditedDate'}
            {if $BugInfo[$Field] != $CFG.ZeroTime}
            <a href="?QueryMode={$Field}|{$BugInfo[$Field]|date_format:"%Y-%m-%d"}"><nobr>{$BugInfo[$Field]|date_format:"%Y-%m-%d"}</nobr></a>
            {/if}
          {else}
          <a href="?QueryMode={$Field}|{$BugInfo[$Field]}"><nobr>{$BugInfo[$FieldValue]|default:$BugInfo[$Field]}</nobr></a>
          {/if}
      </td>
      {/foreach}
    </tr>
    {/foreach}
    </table>
    </div>
     </td>
    </tr>
  </table>
{include file="ResizeList.tpl"}
</script>
</body>
</html>
