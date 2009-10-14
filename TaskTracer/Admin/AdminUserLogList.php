<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * admin user log list.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require_once("../Include/Init.inc.php");

baseJudgeAdminUserLogin('SysAdmin');

/* Get pagination */
$Pagination = new Page('TestUserLog', '', '', '', 'ORDER BY LogID DESC', '', $MyDB);
$LimitNum = $Pagination->LimitNum();

/* Get userlog list */
$UserLogList = dbGetList('TestUserLog', '*', '', '', 'LogID DESC', $LimitNum);

/* Assign */
$TPL->assign('PaginationHtml', $Pagination->show());
$TPL->assign('UserLogList', $UserLogList);

/* Display the template file. */
$TPL->assign('NavActiveUserLog', ' class="Active"');
$TPL->display('Admin/UserLogList.tpl');
?>
