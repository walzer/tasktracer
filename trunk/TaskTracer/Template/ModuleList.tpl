{include file="Header.tpl"}
<body class="{$TestMode}Mode"  onload="initShowGotoBCR();">
  <link href="Css/TreeMenu.css" rel="stylesheet" type="text/css">
  <script language="JavaScript" src="JS/TreeMenu.js" type="text/javascript"></script>
  <div id="ModuleList" class="CommonDiv" style="margin:2px 0 4px 4px;width:96%;">
    {$ModuleTree}
  </div>
  <!--
  <div id="ProjectDoc" class="CommonDiv" style="margin-top:10px;width:210px;">
  ◆ <br />
  ◆
  </div>-->
  <script type="text/javascript">createTreeMenu("ModuleList");</script>
</body>
</html>
