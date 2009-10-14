<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * bug stat info.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require_once("Include/Init.inc.php");
set_time_limit(0);

//* Get today, yesterday and a week befor. */
$Today         = date("Y-m-d");
$Yesterday     = date("Y-m-d",time() - 24 * 3600);
$OneWeekBefore = date("Y-m-d",time() - 24 * 3600 * 7);

/* Get the add date of the first bug. */
$FirstBug =  array_pop(dbGetList('BugInfo', '','', '', 'BugID ASC', '1'));
$FirstDate = substr($FirstBug["OpenedDate"],0,10);

/* Get statistics of the week and all time. */
$Columns = "OpenedBy, Resolution, Count(*) AS BugCount";
$StaleBugsColumns = "AssignedTo, Resolution, Count(*) AS BugCount";

$BugOfThisWeek = dbGetList('BugInfo', $Columns, "OpenedDate <= '{$Yesterday}' AND OpenedDate >= '{$OneWeekBefore}'",'OpenedBy,Resolution','BugCount DESC, OpenedBy ASC');
$BugOfAllTime = dbGetList('BugInfo', $Columns, "OpenedDate <= '{$Yesterday}' AND OpenedDate >= '{$FirstDate}'",'OpenedBy,Resolution','BugCount DESC, OpenedBy ASC');
$StaleBugs= dbGetList('BugInfo', $StaleBugsColumns, "LastEditedDate < '{$OneWeekBefore}' AND BugStatus != 'Closed' AND IsDroped <> 1",'AssignedTo,Resolution','BugCount DESC, AssignedTo ASC');

$TestUserList = testGetOneDimUserList();
$StatBugOfThisWeek = array();
$StatBugOfThisWeekTotal = array();
foreach($BugOfThisWeek as $Key => $Value)
{
    $RealName = $TestUserList[$Value['OpenedBy']] != '' ? $TestUserList[$Value['OpenedBy']] : $Value['OpenedBy'];
    $BugOfThisWeek[$Key]['RealName'] = $RealName;
    if($Value['Resolution'] == '')
    {
        $Value['Resolution'] = 'Active';
    }
    $StatBugOfThisWeek[$Value['OpenedBy']]['TotalCount'] += $Value['BugCount'];
    $StatBugOfThisWeek[$Value['OpenedBy']][$Value['Resolution']] += $Value['BugCount'];
    $StatBugOfThisWeekTotal['TotalCount'] += $Value['BugCount'];
    $StatBugOfThisWeekTotal[$Value['Resolution']] += $Value['BugCount'];
}

$StatBugOfAllTime = array();
$StatBugOfAllTimeTotal = array();
foreach($BugOfAllTime as $Key => $Value)
{
    $RealName = $TestUserList[$Value['OpenedBy']] != '' ? $TestUserList[$Value['OpenedBy']] : $Value['OpenedBy'];
    $BugOfAllTime[$Key]['RealName'] = $RealName;
    if($Value['Resolution'] == '')
    {
        $Value['Resolution'] = 'Active';
    }
    $StatBugOfAllTime[$Value['OpenedBy']]['TotalCount'] += $Value['BugCount'];
    $StatBugOfAllTime[$Value['OpenedBy']][$Value['Resolution']] += $Value['BugCount'];
    $StatBugOfAllTimeTotal['TotalCount'] += $Value['BugCount'];
    $StatBugOfAllTimeTotal[$Value['Resolution']] += $Value['BugCount'];
}

$StatStaleBugs= array();
$StatStaleBugsTotal= array();
foreach($StaleBugs as $Key => $Value)
{
    $RealName = $TestUserList[$Value['AssignedTo']] != '' ? $TestUserList[$Value['AssignedTo']] : $Value['AssignedTo'];
    $StaleBugs[$Key]['RealName'] = $RealName;
    if($Value['Resolution'] == '')
    {
        $Value['Resolution'] = 'Active';
    }
    $StatStaleBugs[$Value['AssignedTo']]['TotalCount'] += $Value['BugCount'];
    $StatStaleBugs[$Value['AssignedTo']][$Value['Resolution']] += $Value['BugCount'];
    $StatStaleBugsTotal['TotalCount'] += $Value['BugCount'];
    $StatStaleBugsTotal[$Value['Resolution']] += $Value['BugCount'];
}

$StatBugOfThisWeek = @sysSortArray($StatBugOfThisWeek, 'TotalCount', 'SORT_DESC', 'SORT_NUMERIC', 'Active', 'SORT_DESC', 'SORT_NUMERIC');
$StatBugOfAllTime = @sysSortArray($StatBugOfAllTime, 'TotalCount', 'SORT_DESC', 'SORT_NUMERIC', 'Active', 'SORT_DESC', 'SORT_NUMERIC');
$StatStaleBugs= @sysSortArray($StatStaleBugs, 'TotalCount', 'SORT_DESC', 'SORT_NUMERIC', 'Active', 'SORT_DESC', 'SORT_NUMERIC');

$CssStyle = join("",file($_CFG['RealRootPath'] . "/Css/Mail.css"));
$TPL->assign('CssStyle',$CssStyle);
$TPL->assign('FirstDate', $FirstDate);
$TPL->assign('Today', $Today);
$TPL->assign('Yesterday', $Yesterday);
$TPL->assign('OneWeekBefore', $OneWeekBefore);
$TPL->assign('TestUserList', $TestUserList);
$TPL->assign('StatBugOfThisWeek', $StatBugOfThisWeek);
$TPL->assign('StatBugOfThisWeekTotal', $StatBugOfThisWeekTotal);
//$TPL->assign('StatBugOfAllTime', $StatBugOfAllTime);
//$TPL->assign('StatBugOfAllTimeTotal', $StatBugOfAllTimeTotal);
$TPL->assign('StatStaleBugs', $StatStaleBugs);
$TPL->assign('StatStaleBugsTotal', $StatStaleBugsTotal);

$StatContent = $TPL->fetch('StatBug.tpl');

$MailReportUserList = testGetUserList('UserName ' . dbCreateIN(join(',', $_CFG['MailReportUser'])));
$ReportEmailList = array();
foreach($_CFG['MailReportUser'] as $User)
{
    if($MailReportUserList[$User]['Email'] != '')
    {
        $ReportEmailList[] = $MailReportUserList[$User]['Email'];
    }
    else
    {
        $ReportEmailList[] = $User;
    }
}

$To = join(',',$ReportEmailList);
@sysMail($To,'','Bug ' . $_LANG['ReportForms'],$StatContent);
?>
