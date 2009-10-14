{include file="Header.tpl"}
<body>
<form id="SaveQueryForm" name="SaveQueryForm" action=""  method="post">
  <div id="SaveQuery" class="CommonForm">
    <dl>
      <dt>{$Lang.QueryTitle}</dt>
      <dd><input id="QueryTitle" name="QueryTitle" value="" maxlength="150" /></dd>
      <dd>
        <input type="button" id="SubmitButton" name="SubmitButton" value="{$Lang.SaveQuery}" accesskey="S" onclick="submitForm('SaveQueryForm');" />
        <input type="hidden" name="QueryType" value="{$QueryType}" />
      </dd>
    </dl>
  </div>
</form>
{literal}
<script type="text/javascript">
//<![CDATA[
focusInputEndPos('QueryTitle');
//]]>
</script>
{/literal}
</body>
</html>
