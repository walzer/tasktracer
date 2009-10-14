  <div id="TopNavMain" class="AdminMode">
    <a id="TopNavLogo" href="../">{$Lang.ProductName}</a>
    <span id="TopNavAbout">
      <a href="#"><big>{$Lang.Admin}</big></a>
    </span>
  </div>
  <div class="BaseTab">
    <ul>
      <li class="SpaceTab">&nbsp;</li>
      <li{$NavActivePro}><a href="AdminProjectList.php">{$Lang.ManageProject}</a></li>
      <li class="SpaceTab">&nbsp;</li>
      <li{$NavActiveUser}><a href="AdminUserList.php">{$Lang.ManageUser}</a></li>
      <li class="SpaceTab">&nbsp;</li>
      <li{$NavActiveGroup}><a href="AdminGroupList.php">{$Lang.ManageGroup}</a></li>
      <li class="SpaceTab">&nbsp;</li>{if $templatelite.session.TestIsAdmin}
      <li{$NavActiveUserLog}><a href="AdminUserLogList.php">{$Lang.UserLog}</a></li>{/if}
      <li class="EndTab">&nbsp;</li>
    </ul>
  </div>
