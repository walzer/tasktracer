{include file="Header.tpl"}
<body class="{$TestMode}Mode" onload="this.focus();initShowGotoBCR();">
  <div id="TopNavMain">
    <a id="TopNavLogo" href="./" target="_top">{$Lang.ProductName}</a>
    <span id="TopNavAbout">
      <a name="UserNameLink">{$Lang.Welcome},{$TestRealName}</a>
      <a href="EditMyInfo.php" target="_blank">[{$Lang.EditPer}]</a>{if $TestIsAdmin || $TestIsProjectAdmin}
      <a href="Admin/" target="_blank">[{$Lang.Admin}]</a>{/if}
      <a href="Logout.php?Logout=Yes">[{$Lang.Logout}]</a>
      <a href="{$Lang.BFHomePage}/help" target="_blank">[{$Lang.Manual}]</a>
      <a href="Doc/AboutBugFree.txt" target="_blank">[{$Lang.AboutBF}]</a>
      <a href="{$Lang.BFHomePage}" target="_blank">[{$Lang.BFHome}]</a>
    </span>
  </div>
  <div id="TopBCR" class="BaseTab">
    <ul>
      <li id="TopNavProjectListLi">{$TopNavProjectList}</li>
      <li id="TopNavBugLi" class="Cram{if $TestMode eq 'Bug'} Active{/if}"><a href="index.php?TestMode=Bug" id="TopNavBug" target="_top">[{$Lang.TopNav.Bug}]</a></li>
      <li class="SpaceTab">&nbsp;</li>
      <li id="TopNavCaseLi" class="Cram{if $TestMode eq 'Case'} Active{/if}"><a href="index.php?TestMode=Case" id="TopNavCase" target="_top">[{$Lang.TopNav.TestCase}]</a></li>
      <li class="SpaceTab">&nbsp;</li>
      <li id="TopNavResultLi" class="Cram{if $TestMode eq 'Result'} Active{/if}"><a href="index.php?TestMode=Result" id="TopNavResult" target="_top">[{$Lang.TopNav.TestResult}]</a></li>
      <li class="EndTab">&nbsp;</li>
    </ul>
    <span id="Open" name="Open" style="float:left;margin-left:600px;margin-top:-27px;">
      <a href="Bug.php?ActionType=OpenBug" id="OpenBug" class="BigButton OpenBug" target="_blank" {if $TestMode neq 'Bug'}style="display:none;"{/if}>
        {$Lang.OpenBug}
      </a>
      <a href="Case.php?ActionType=OpenCase" id="OpenCase" class="BigButton OpenCase" target="_blank"{if $TestMode neq 'Case'} style="display:none;"{/if}>
        {$Lang.OpenCase}
      </a>
    </span>
  </div>
</body>
</html>
