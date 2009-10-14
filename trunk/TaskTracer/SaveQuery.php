<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * save query condition.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require('Include/Init.inc.php');

$QueryType = $_POST['QueryType'];
if($_POST['SaveQuery'])
{
    $QueryStr = baseGetGroupQueryStr($_POST);
    $_SESSION[$QueryType . 'QueryCondition'] = $QueryStr;

    $TPL->assign('QueryType', $QueryType);
    $TPL->display('SaveQuery.tpl');
}

if(isset($_POST['QueryTitle']))
{
    $_POST['QueryTitle'] = trim($_POST['QueryTitle']);

    if($_POST['QueryTitle'] == '')
    {
        jsAlert($_LANG['NoQueryTitle']);
        jsGoTo('back');
        exit;
    }

    $UserName = $_SESSION['TestUserName'];
    $QueryTitle = trim($_POST['QueryTitle']);

    // check the querytitle
    $TempQuery = dbGetRow('TestUserQuery', "", "QueryTitle = '{$QueryTitle}' AND QueryType = '{$QueryType}' AND UserName = '{$UserName}'");
    if(!empty($TempQuery))
    {
        jsAlert($_LANG['DuplicateQueryTitle']);
        jsGoTo('back');
        exit;
    }
    else
    {
        $QueryStr = addslashes($_SESSION[$QueryType . 'QueryCondition']);
        $QueryID = dbInsertRow('TestUserQuery',"'{$UserName}','{$QueryType}','{$QueryTitle}','{$QueryStr}',now()", 'UserName,QueryType,QueryTitle,QueryString,AddDate');
        jsGoTo("UserControl.php","parent.LeftBottomFrame");
        jsGoTo($QueryType . 'List.php');
    }
}
?>
