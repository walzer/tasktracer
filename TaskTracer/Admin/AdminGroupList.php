<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * list group.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require_once("../Include/Init.inc.php");

baseJudgeAdminUserLogin();

$Where = '';
$Where = '1';

/* Get pagination */
$Pagination = new Page('TestGroup', '', '', '', 'WHERE ' . $Where . ' ORDER BY GroupID DESC', '', $MyDB);
$LimitNum = $Pagination->LimitNum();

/* Get group list */
$GroupList = testGetGroupList($Where, 'GroupID DESC', $LimitNum);
foreach($GroupList as $key => $GroupInfo)
{
    if($_SESSION['TestIsAdmin'] || (preg_match('/,'. $_SESSION['TestUserName'] . ',/', $GroupInfo['GroupManagers'])) || $_SESSION['TestUserName'] == $GroupInfo['AddedBy'])
    {
        $GroupList[$key]['IsEditable'] = true;
    }
    else
    {
        $GroupList[$key]['IsEditable'] = false;
    }
}

$UserList = testGetOneDimUserList();

/* Assign */
$TPL->assign('PaginationHtml', $Pagination->show());
$TPL->assign('GroupList', $GroupList);
$TPL->assign('UserList', $UserList);

/* Display the template file. */
$TPL->assign('NavActiveGroup', ' class="Active"');
$TPL->display('Admin/GroupList.tpl');
?>
