{include file="XmlHeader.tpl"}
{include file="Header.tpl"}
<script>if(parent.window==window)window.location='index.php?TestMode=Result';</script>
<body class="{$TestMode}Mode PaddingBoy" onclick="hiddenDivCustomSetTable()" style ="overflow:auto;" onload="initShowGotoBCR();">
{include file="CustomField.tpl"}
  <table class="CommonTable ListTable {$TestMode}Mode">
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
          <td style="text-align:right;border:0;width:30%">
            <a href="javascript:void(0);" id="CustomSetLink" onclick="x=event.clientX+document.body.scrollLeft;y=event.clientY;showDivCustomSetTable(x,y);">{$Lang.CustomDisplay}</a>|
            <a href="?Export=HtmlTable" target="_blank"><img src="Image/export.gif"/>&nbsp;{$Lang.ExportHtmlTable}</a>|
            <a href="Report.php?ReportMode={$TestMode}" target="{$TestMode}Report"><img src="Image/report.gif"/>&nbsp;{$Lang.ReportForms}</a>
          </td>
        </tr>
      </table>
      </td>
    </tr>
    <tr>
      <td colspan="6">
    <div id="ListSubTable" style="height:290px;overflow-y:auto;overflow-x:auto">
    <table class="CommonTable CommonSubTable {$TestMode}Mode">
    <colgroup>
      {foreach from=$FieldsToShow key=Field item=FieldName}
      <col{if $QueryColumn eq $Field} class="SortActive"{/if}{if $QueryColumn eq 'ResultSeverity'}style="width:3%;"{/if}></col>
      {/foreach}
    <colgroup>
    <tr>
      {foreach from=$FieldsToShow key=Field item=FieldName}
      <th align="{if $Field == 'ResultID'}center{else}left{/if}">
        <nobr><a href="?OrderBy={$Field}|{$OrderByTypeList[$Field]}&QueryMode={$QueryMode}">{$FieldName}</a>{if $OrderByColumn == $Field}{$OrderTypeArrowArray[$OrderByType]}{/if}</nobr>
      </th>
      {/foreach}
    </tr>
    {foreach from=$ResultList item=ResultInfo}
    <tr class="ResultValue{$ResultInfo.ResultValue}">
      {foreach from=$FieldsToShow key=Field item=FieldName}
      <td align="{if $Field == 'ResultID' || $Field == 'CaseID'}center{else}left{/if}">
        {assign var="FieldValue" value=$Field."Name"}
          {if $Field == 'ResultID' || $Field == 'ResultSeverity'}<nobr>{$ResultInfo[$FieldValue]|default:$ResultInfo[$Field]}</nobr>
          {elseif $Field == 'ResultTitle'}
          <a href="Result.php?ResultID={$ResultInfo.ResultID}" title="{$ResultInfo.ResultTitle}" class="FullLink" target="_blank"><nobr>{$ResultInfo.ListTitle}</nobr></a>
          {elseif $Field == 'OpenedDate' || $Field == 'AssignedDate' || $Field == 'LastEditedDate'}
            {if $ResultInfo[$Field] != $CFG.ZeroTime}
            <a href="?QueryMode={$Field}|{$ResultInfo[$Field]|date_format:"%Y-%m-%d"}"><nobr>{$ResultInfo[$Field]|date_format:"%Y-%m-%d"}</nobr></a>
            {/if}
          {else}
          <a href="?QueryMode={$Field}|{$ResultInfo[$Field]}"><nobr>{$ResultInfo[$FieldValue]|default:$ResultInfo[$Field]}</nobr></a>
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
</body>
</html>
