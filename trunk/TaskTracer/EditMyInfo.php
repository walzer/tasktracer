<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * edit user info.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require_once("Include/Init.inc.php");

$UserInfo = dbGetRow('TestUser', '', "UserName = '{$_SESSION[TestUserName]}'");

$TPL->assign('UserInfo', $UserInfo);

//$UserInfo = ;
$TPL->display('EditMyInfo.tpl');
?>
