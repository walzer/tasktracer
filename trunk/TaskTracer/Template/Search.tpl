{include file="Header.tpl"}
<body class="{$TestMode}Mode PaddingBoy" onload="initShowGotoBCR();">
{literal}
<script type="text/javascript">
//<![CDATA[
{/literal}
var FieldCount = {$FieldCount};
{foreach from=$AutoTextValue key=key item=item}
var {$key} = {$item}
{/foreach}
{literal}
//]]>
</script>
{/literal}
<form id="Search{$TestMode}" name="Search{$TestMode}" action="{$TestMode}List.php" method='post' target='RightBottomFrame'>
  <table class="CommonTable SearchTable {$TestMode}Mode">
    <caption>
        {$Lang.QueryCondition}
        <input type="checkbox" name="AutoComplete" id="AutoComplete" checked="checked" onclick="resetQueryForm();" class="NoBorder" style="display:none"/>
        <!--<label for="AutoComplete">Auto</label>-->
    </caption>
    <colgroup>
      <col span="1" width="7%">
      <col span="3" width="13%">
      <col span="1" width="7%">
      <col span="1" width="7%">
      <col span="3" width="13%">
    </colgroup>
    <tr>
      <th>1<span style="display:none">{$AndOrList.0}</span></th>
      <td>{$FieldList.0}</td>
      <td>{$OperatorList.0}</td>
      <td id="ValueTd0">{$ValueList.0}</td>
      <td rowspan="{math equation="x/y" x=$FieldCount y=2}" id="AndORTd">
        <input type="radio" name="AndOrGroup" id="AndGroup" value="AND" checked="checked" class="NoBorder"/>
        <label for="AndGroup">{$Lang.AndOr.And}</label><br />
        <input type="radio" name="AndOrGroup" id="OrGroup" value="OR" style="border:0;" />
        <label for="OrGroup">{$Lang.AndOr.Or}</label>
      </td>
      <th>2<span style="display:none">{$AndOrList.1}</span></th>
      <td>{$FieldList.1}</td>
      <td>{$OperatorList.1}</td>
      <td id="ValueTd1">{$ValueList.1}</td>
    </tr>
    {foreach from=$AndOrList key=Key value=Value}{if $Key>1}{if $Key%2 == 0}
    <tr>
      <td>{$AndOrList[$Key]}</td>
      <td>{$FieldList[$Key]}</td>
      <td>{$OperatorList[$Key]}</td>
      <td id="ValueTd{$Key}">{$ValueList[$Key]}</td>{else}
      <td>{$AndOrList[$Key]}</td>
      <td>{$FieldList[$Key]}</td>
      <td>{$OperatorList[$Key]}</td>
      <td id="ValueTd{$Key}">{$ValueList[$Key]}</td>
    </tr>{/if}{/if}
    {/foreach}
    <tr>
      <th colspan="9" class="TopLine">
        <input type="submit" name="PostQuery"  onclick="document.Search{$TestMode}.action='{$TestMode}List.php';" />
        <input type="submit" name="SaveQuery" value="{$Lang.SaveQuery}" onclick="document.Search{$TestMode}.action='SaveQuery.php';" />
        <input type="reset"  onclick="location.reload(true);" />
        <input type="hidden" name="QueryType" value="{$TestMode}" />
      </th>
    </tr>
  </table>
</form>
{literal}
<script type="text/javascript">
//<![CDATA[
function resetQueryForm()
{
    for(var i=0;i<FieldCount;i++)
    {
        setQueryForm(i);
    }
}
resetQueryForm();
//]]
</script>
{/literal}
</body>
</html>
