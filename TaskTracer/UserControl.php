<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * user control.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require('Include/Init.inc.php');

function xUpdateUserControl($TestMode)
{
    global $_LANG;

    $_SESSION['UpdateUserControl'] = 'UpdateUserControl';
    $LimitNum = '10';

    //$TestMode = $_SESSION['TestMode'];
    $TestUserName = $_SESSION['TestUserName'];

    $objResponse = new xajaxResponse();
    sleep(1); //we'll do nothing for two seconds

    $AssignedStr = '';
    $OpenedStr = '';
    $QueryStr = '';


    if($TestMode == 'Bug')
    {
        $Columns = 'BugID,BugTitle';
        $Where = "OpenedBy = '{$TestUserName}' AND BugStatus <> 'Closed'";
        $OrderBy = 'LastEditedDate DESC';
        $OpenedList = dbGetList('BugInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
        $OpenedList = testSetBugListMultiInfo($OpenedList);

        $Where = "AssignedTo = '{$TestUserName}' AND BugStatus <> 'Closed'";
        $AssignedList = dbGetList('BugInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
        $AssignedList = testSetBugListMultiInfo($AssignedList);


        foreach($AssignedList as $Item)
        {
          $AssignedStr .= "{$Item[BugID]}&nbsp;<a href=\"Bug.php?BugID={$Item[BugID]}\" title=\"{$Item[BugTitle]}\" target=\"_blank\">{$Item[UCTitle]}</a><br />";
        }

        foreach($OpenedList as $Item)
        {
          $OpenedStr .= "{$Item[BugID]}&nbsp;<a href=\"Bug.php?BugID={$Item[BugID]}\" title=\"{$Item[BugTitle]}\" target=\"_blank\">{$Item[UCTitle]}</a><br />";
        }
    }
    elseif($TestMode == 'Case')
    {
        $Columns = 'CaseID,CaseTitle';
        $Where = "OpenedBy = '{$TestUserName}'";
        $OrderBy = 'LastEditedDate DESC';
        $OpenedList = dbGetList('CaseInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
        $OpenedList = testSetCaseListMultiInfo($OpenedList);

        $Where = "AssignedTo = '{$TestUserName}'";
        $AssignedList = dbGetList('CaseInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
        $AssignedList = testSetCaseListMultiInfo($AssignedList);

        foreach($AssignedList as $Item)
        {
          $AssignedStr .= "{$Item[CaseID]}&nbsp;<a href=\"Case.php?CaseID={$Item[CaseID]}\" title=\"{$Item[CaseTitle]}\" target=\"_blank\">{$Item[UCTitle]}</a><br />";
        }

        foreach($OpenedList as $Item)
        {
          $OpenedStr .= "{$Item[CaseID]}&nbsp;<a href=\"Case.php?CaseID={$Item[CaseID]}\" title=\"{$Item[CaseTitle]}\" target=\"_blank\">{$Item[UCTitle]}</a><br />";
        }
    }
    elseif($TestMode == 'Result')
    {
        $Columns = 'ResultID,ResultTitle';
        $Where = "OpenedBy = '{$TestUserName}'";
        $OrderBy = 'LastEditedDate DESC';
        $OpenedList = dbGetList('ResultInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
        $OpenedList = testSetResultListMultiInfo($OpenedList);

        $Where = "AssignedTo = '{$TestUserName}'";
        $AssignedList = dbGetList('ResultInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
        $AssignedList = testSetResultListMultiInfo($AssignedList);

        foreach($AssignedList as $Item)
        {
          $AssignedStr .= "{$Item[ResultID]}&nbsp;<a href=\"Result.php?ResultID={$Item[ResultID]}\" title=\"{$Item[ResultTitle]}\" target=\"_blank\">{$Item[UCTitle]}</a><br />";
        }

        foreach($OpenedList as $Item)
        {
          $OpenedStr .= "{$Item[ResultID]}&nbsp;<a href=\"Result.php?ResultID={$Item[ResultID]}\" title=\"{$Item[ResultTitle]}\" target=\"_blank\">{$Item[UCTitle]}</a><br />";
        }
    }

    $objResponse->addAssign('UCDiv0', 'innerHTML', $AssignedStr);
    $objResponse->addAssign('UCDiv1', 'innerHTML', $OpenedStr);

    return $objResponse;
}

sysXajaxRegister("xUpdateUserControl");

$LimitNum = '10';

$TestMode = $_SESSION['TestMode'];
$TestUserName = $_SESSION['TestUserName'];

if($_GET['DelQueryID'] != '')
{
    dbDeleteRow('TestUserQuery', "QueryID='{$_GET[DelQueryID]}' AND UserName='{$TestUserName}'");
    jsGoto('UserControl.php');
    exit;
}

if($TestMode == 'Bug')
{
    $Columns = 'BugID,BugTitle';
    $Where = "OpenedBy = '{$TestUserName}' AND BugStatus <> 'Closed'";
    $OrderBy = 'LastEditedDate DESC';
    $OpenedList = dbGetList('BugInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
    $OpenedList = testSetBugListMultiInfo($OpenedList);

    $Where = "AssignedTo = '{$TestUserName}' AND BugStatus <> 'Closed'";
    $AssignedList = dbGetList('BugInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
    $AssignedList = testSetBugListMultiInfo($AssignedList);
}
elseif($TestMode == 'Case')
{
    $Columns = 'CaseID,CaseTitle';
    $Where = "OpenedBy = '{$TestUserName}'";
    $OrderBy = 'LastEditedDate DESC';
    $OpenedList = dbGetList('CaseInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
    $OpenedList = testSetCaseListMultiInfo($OpenedList);

    $Where = "AssignedTo = '{$TestUserName}'";
    $AssignedList = dbGetList('CaseInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
    $AssignedList = testSetCaseListMultiInfo($AssignedList);
}
elseif($TestMode == 'Result')
{
    $Columns = 'ResultID,ResultTitle';
    $Where = "OpenedBy = '{$TestUserName}'";
    $OrderBy = 'LastEditedDate DESC';
    $OpenedList = dbGetList('ResultInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
    $OpenedList = testSetResultListMultiInfo($OpenedList);

    $Where = "AssignedTo = '{$TestUserName}'";
    $AssignedList = dbGetList('ResultInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
    $AssignedList = testSetResultListMultiInfo($AssignedList);
}

$Where = "UserName = '{$TestUserName}' AND QueryType = '{$TestMode}'";
$OrderBy = 'QueryTitle ASC';
$QueryList = dbGetList('TestUserQuery','', $Where, '', $OrderBy);

$TPL->assign('AssignedList', $AssignedList);
$TPL->assign('OpenedList', $OpenedList);
$TPL->assign('QueryList', $QueryList);
$TPL->assign('TestMode', $_SESSION['TestMode']);

$TPL->display('UserControl.tpl');
?>
