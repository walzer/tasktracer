<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * admin user list.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require_once("../Include/Init.inc.php");

baseJudgeAdminUserLogin();

/* Get pagination */
$DBName = !empty($_CFG['UserDB']) ? 'MyUserDB' : 'MyDB';
global $$DBName;

$Where = "(1)";
if(isset($_GET['SearchUser']))
{
    $SearchUser = trim($_GET['SearchUser']);
}

if($SearchUser != '')
{
    $Where .= " AND ( BINARY {$_CFG[UserTable][UserName]} like '%{$SearchUser}%' ";
    $Where .= " OR BINARY {$_CFG[UserTable][RealName]} like '%{$SearchUser}%' ";
    $Where .= " OR BINARY {$_CFG[UserTable][Email]} like '%{$SearchUser}%' )";
}

if(!empty($_CFG['UserDB']))
{
    $PageWhere = "WHERE {$Where} ORDER BY {$_CFG[UserTable][UserName]} DESC";
    $OrderBy = "{$_CFG[UserTable][UserName]} DESC";
}
else
{
    //$PageWhere = "WHERE IsDroped = '0' ORDER BY UserID DESC";
    $PageWhere = "WHERE {$Where} ORDER BY UserID DESC";
    $OrderBy = "UserID DESC";
}
$OrderBy = !empty($_CFG['UserDB']) ? "{$_CFG[UserTable][UserName]} DESC" : "UserID DESC";
$Pagination = new Page($_CFG['UserTable']['TableName'], '', '', '', $PageWhere, '?SearchUser='.$SearchUser, $$DBName);
$LimitNum = $Pagination->LimitNum();

/* Get user list */
$UserList = testGetAllUserList($Where, $OrderBy, $LimitNum);
$UserNameList = testGetOneDimUserList();


foreach($UserList as $UserName => $UserInfo)
{
    $GroupACL = dbGetList('TestGroup', '', "GroupUser LIKE '%,{$UserInfo[UserName]},%'");
    $UserGroupList = array();
    foreach($GroupACL as $Key => $GroupInfo)
    {
        $UserGroupList[$GroupInfo['GroupID']] = $GroupInfo['GroupName'];
    }
    $UserList[$UserName]['UserGroupListHTML'] = htmlSelect($UserGroupList, 'UserGroupList','', '', 'class="FullSelect"');
}

/* Assign */
$TPL->assign('PaginationHtml', $Pagination->show());
$TPL->assign('UserList', $UserList);
$TPL->assign('UserNameList', $UserNameList);

/* Display the template file. */
$OtherUserDB = !empty($_CFG['UserDB']) ? 1 : 0;
$TPL->assign('OtherUserDB', $OtherUserDB);
$TPL->assign('NavActiveUser', ' class="Active"');
$TPL->assign('SearchUser', $SearchUser);
$TPL->display('Admin/UserList.tpl');
?>
