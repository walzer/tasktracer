{include file="XmlHeader.tpl"}
{include file="Header.tpl"}
<script>if(parent.window==window)window.location='index.php?TestMode=Case';</script>
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
 
 <script language="JavaScript" src="JS/RowTree.js" type="text/javascript"></script>      
    <div id="ListSubTable" style="height:350px;overflow-y:auto;overflow-x:auto">
    <table class="CommonTable CommonSubTable {$TestMode}Mode">
    
    <colgroup>
      {foreach from=$FieldsToShow key=Field item=FieldName}
      <col{if $QueryColumn eq $Field} class="SortActive"{/if}{if $QueryColumn eq 'CaseSeverity'}style="width:3%;"{/if}></col>
      {/foreach}
    <colgroup>
    
    <tr>
      {foreach from=$FieldsToShow key=Field item=FieldName}
      <th align="{if $Field == 'CaseID' || $Field == 'CasePriority'}center{else}left{/if}">
        <nobr><a href="?OrderBy={$Field}|{$OrderByTypeList[$Field]}&QueryMode={$QueryMode}">{$FieldName}</a>{if $OrderByColumn == $Field}{$OrderTypeArrowArray[$OrderByType]}{/if}</nobr>
      </th>
      {/foreach}
    </tr>
    
    {foreach from=$CaseList item=CaseInfo}
    <tr id="case_{$CaseInfo.CaseID}">    


      {foreach from=$FieldsToShow key=Field item=FieldName}
      <td align="{if $Field == 'CaseID' || $Field == 'CasePriority'}center{else}left{/if}" height=16>
        {assign var="FieldValue" value=$Field."Name"}
          {if $Field == 'CaseID' || $Field == 'CaseSeverity'}<nobr>{$CaseInfo[$FieldValue]|default:$CaseInfo[$Field]}</nobr>
          {elseif $Field == 'CaseTitle'}
            
            <!-- Walzer Add for WBS -->
			<div id="case_img_{$CaseInfo.CaseID}">
            {$CaseInfo.Prefix}
            {if $CaseInfo.Children}
			<img align="top" src="Image/TreeMenu/opened.gif" onclick="ChangeParent({$CaseInfo.CaseID}, 0);{foreach from=$CaseInfo.Children item="leaf"}HideChildrenRow({$leaf});{/foreach}">
          	{else}
          	&nbsp;&nbsp;&nbsp;&nbsp;
          	{/if}
          	<a href="Case.php?CaseID={$CaseInfo.CaseID}" title="{$CaseInfo.CaseTitle}" align="top" target="_blank">
				{if $CaseInfo.Children} <b><nobr>{$CaseInfo.ListTitle}</nobr></b>
				{else} <nobr>{$CaseInfo.ListTitle}</nobr>
				{/if}
			</a>
          	</div>
          	<!-- Walzer End -->
          	
          {elseif $Field == 'OpenedDate' || $Field == 'AssignedDate' || $Field == 'LastEditedDate'}
            {if $CaseInfo[$Field] != $CFG.ZeroTime}
            <a href="?QueryMode={$Field}|{$CaseInfo[$Field]|date_format:"%Y-%m-%d"}"><nobr>{$CaseInfo[$Field]|date_format:"%Y-%m-%d"}</nobr></a>
            {/if}
          {else}
          <a href="?QueryMode={$Field}|{$CaseInfo[$Field]}"><nobr>{$CaseInfo[$FieldValue]|default:$CaseInfo[$Field]}</nobr></a>
          {/if}
      </td>
      {/foreach}
      
    </tr>
    {/foreach}
    
    </table>  <!-- end table class="CommonTable CommonSubTable {$TestMode}Mode" -->
    </div> <!-- end ListSubTable -->

     </td>
    </tr>
  </table>
{include file="ResizeList.tpl"}
</body>
</html>
