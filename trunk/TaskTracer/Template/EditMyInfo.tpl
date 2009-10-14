{include file="Header.tpl"}
<body class="BugMode">
<div id="TopNavMain" class="BugMode">
  <a id="TopNavLogo" href="./" target="_top">{$Lang.ProductName}</a>
    <span id="TopNavAbout">
      <a href="#" target="_self"><big>{$Lang.EditPer}</big></a>
    </span>
</div>
<form id="EditMyInfoForm" name="EditMyInfoForm" action="PostAction.php?Action=EditMyInfo" target="PostActionFrame" enctype="multipart/form-data" method="post">
  <div class="CommonForm">
    <dl>
      <dt>{$Lang.UserName}</dt>
      <dd>{$UserInfo.UserName}</dd>
    </dl>
    <dl>
      <dt>{$Lang.RealName}</dt>
      <dd><input type="text" id="RealName" name="RealName" value="{$UserInfo.RealName}" /></dd>
    </dl>
    <dl>
      <dt>{$Lang.Email}</dt>
      <dd><input type="text" id="Email" name="Email" value="{$UserInfo.Email}" /></dd>
    </dl>
    <dl>
      <dt>{$Lang.RawUserPassword}</dt>
      <dd><input type="password" id="RawUserPassword" name="RawUserPassword" value="" /></dd>
    </dl>
    <dl>
      <dt>{$Lang.UserPassword}</dt>
      <dd><input type="password" id="UserPassword" name="UserPassword" value="" /></dd>
    </dl>
    <dl>
      <dt>{$Lang.RepeatUserPassword}</dt>
      <dd><input type="password" id="RepeatUserPassword" name="RepeatUserPassword" value="" /></dd>
    </dl>
    <dl>
      <dt>&nbsp;</dt>
      <dd>
        <input type="button" id="SubmitButton" name="SubmitButton" value="{$Lang.EditMyInfoButton}" accesskey="E" class="ActionButton" onclick="submitForm('EditMyInfoForm');" />
      </dd>
    </dl>
  <input type="hidden" id="ToDisabledObj" name="ToDisabledObj" value="SubmitButton" />
  <input type="hidden" id="UserName" name="UserName" value="{$UserInfo.UserName}" />
  </div>
</form>
{include file="PostActionFrame.tpl"}
{literal}
<script type="text/javascript">
//<![CDATA[
focusInputEndPos('RawUserPassword');
//]]>
</script>
{/literal}
</body
</html>
