<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * list bugs.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require('Include/Init.inc.php');

if($_SESSION['TestCurrentProjectID'] - 0 > 0 && $_SESSION['BugQueryCondition'] == '')
{
    //$_SESSION['BugQueryCondition'] = "ProjectID = '{$_SESSION[TestCurrentProjectID]}'";
}
if($_GET['ProjectID'] - 0 > 0)
{
    $_SESSION['BugQueryCondition'] = "ProjectID = '{$_GET[ProjectID]}'";
    testSetCurrentProject($_GET['ProjectID']);
}
if($_GET['ModuleID'] - 0 > 0)
{
    $_SESSION['TestCurrentModuleID'] = $_GET['ModuleID'];
    $_SESSION['BugQueryCondition'] = "ModuleID IN ({$_GET['ChildModuleIDs']})";
}
else
{
    $_SESSION['TestCurrentModuleID'] = 0;
}

if($_POST['PostQuery'])
{
    $QueryStr = baseGetGroupQueryStr($_POST);
    $_SESSION['BugQueryCondition'] = $QueryStr;
}

if($_REQUEST['QueryID'])
{
    $QueryInfo = dbGetRow('TestUserQuery', '', "QueryID='{$_REQUEST[QueryID]}' AND QueryType='Bug'");
    $_SESSION['BugQueryCondition'] = $QueryInfo['QueryString'];
}

$WHERE = array();
$URL = array();

$WHERE[] = $_SESSION['TestUserACLSQL'];

if($_SESSION['BugQueryCondition'] != '')
{
    $WHERE[] = $_SESSION['BugQueryCondition'];
}

if($_GET['OrderBy'])
{
    $OrderByList = explode('|', $_GET['OrderBy']);
    $OrderByColumn = $OrderByList[0];
    $OrderByType = $OrderByList[1];
    $OrderBy = join(' ', $OrderByList);
    $URL[] = 'OrderBy=' . $_GET['OrderBy'];
    $_SESSION['BugOrderBy']['OrderBy'] = $OrderBy;
    $_SESSION['BugOrderBy']['OrderByColumn'] = $OrderByColumn;
    $_SESSION['BugOrderBy']['OrderByType'] = $OrderByType;
}
else
{
    if(empty($_SESSION['BugOrderBy']))
    {
        $_SESSION['BugOrderBy']['OrderBy'] = ' BugID DESC';
        $_SESSION['BugOrderBy']['OrderByColumn'] = 'BugID';
        $_SESSION['BugOrderBy']['OrderByType'] = 'DESC';
    }
    $OrderBy = $_SESSION['BugOrderBy']['OrderBy'];
    $OrderByColumn = $_SESSION['BugOrderBy']['OrderByColumn'];
    $OrderByType = $_SESSION['BugOrderBy']['OrderByType'];
}

if($_GET['QueryMode'])
{
    $QueryModeList = explode('|', $_GET['QueryMode']);
    $QueryColumn = $QueryModeList[0];
    $QueryValue = $QueryModeList[1];
    $WHERE = array();
    $WHERE[] = $_SESSION['TestUserACLSQL'];
    $QueryCondition = "";
    if(preg_match('/date/i', $QueryColumn))
    {
        $QueryCondition =  $QueryColumn . ' ' . sysStrToDateSql($QueryValue);
    }
    else
    {
        $QueryCondition = "{$QueryColumn}='{$QueryValue}'";
    }
    $_SESSION['BugQueryCondition'] = $QueryCondition;
    $WHERE[] = $QueryCondition;
    $URL[] = 'QueryMode=' . $_GET['QueryMode'];
}
$Url = '?' . join('&', $URL);
$WHERE[] = "IsDroped = '0'";
$Where = join(' AND ', $WHERE);

$FieldsToShow = testSetCustomFields('Bug');

/* Get pagination */
$Pagination = new Page('BugInfo', '', '', '', 'WHERE ' . $Where . ' ORDER BY ' . $OrderBy, $Url, $MyDB);
$LimitNum = $Pagination->LimitNum();
$TPL->assign('PaginationDetailInfo', $Pagination->getDetailInfo());

$ColumnArray = @array_keys($FieldsToShow);
$Columns = 'BugID,BugStatus,' . join(',',$ColumnArray);


$OrderColumnList = $ColumnArray;
$OrderByTypeList = array();
foreach($OrderColumnList as $OrderColumn)
{
    if($OrderColumn == $OrderByColumn)
    {
        $OrderByTypeList[$OrderColumn] = $OrderTypeReverseArray[$OrderByType];
    }
    else
    {
        $OrderByTypeList[$OrderColumn] = $OrderByType;
    }
}

if($_GET['Export'] == 'HtmlTable')
{
    $Columns = '';
    $LimitNum = '';
}

$BugList = dbGetList('BugInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
$UserNameList = testGetOneDimUserList();
$BugList = testSetBugListMultiInfo($BugList, $UserNameList);
$TPL->assign('BugList', $BugList);

$TPL->assign('OrderByColumn', $OrderByColumn);
$TPL->assign('OrderByType', $OrderByType);
$TPL->assign('QueryMode', $_GET['QueryMode']);
$TPL->assign('QueryColumn', $QueryColumn);
$TPL->assign('OrderByTypeList', $OrderByTypeList);
$TPL->assign('BaseTarget', '_self');
$TPL->assign('TestMode', 'Bug');

if($_GET['Export'] == 'HtmlTable')
{
    $TPL->assign('DataList', $BugList);
    $TPL->assign('FieldsToShow', $_LANG["BugFields"]);
    $TPL->display('ExportList.tpl');
    exit;
}

$TPL->display('BugList.tpl');
?>
