<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * list results.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require('Include/Init.inc.php');

if($_SESSION['TestCurrentProjectID'] - 0 > 0)
{
    //$_SESSION['ResultQueryCondition'] = "ProjectID = '{$_SESSION[TestCurrentProjectID]}'";
}
if($_GET['ProjectID'] - 0 > 0)
{
    $_SESSION['ResultQueryCondition'] = "ProjectID = '{$_GET[ProjectID]}'";
    testSetCurrentProject($_GET['ProjectID']);
}
if($_GET['ModuleID'] - 0 > 0)
{
    $_SESSION['ResultQueryCondition'] = "ModuleID IN ({$_GET['ChildModuleIDs']})";
}
$_SESSION['TestCurrentModuleID'] = $_GET['ModuleID'];

if($_POST['PostQuery'])
{
    $QueryStr = baseGetGroupQueryStr($_POST);
    $_SESSION['ResultQueryCondition'] = $QueryStr;
}

if($_REQUEST['QueryID'])
{
    $QueryInfo = dbGetRow('TestUserQuery', '', "QueryID='{$_REQUEST[QueryID]}' AND QueryType='Result'");
    $_SESSION['ResultQueryCondition'] = $QueryInfo['QueryString'];
}

$WHERE = array();
$URL = array();

$WHERE[] = $_SESSION['TestUserACLSQL'];

if($_SESSION['ResultQueryCondition'] != '')
{
    $WHERE[] = $_SESSION['ResultQueryCondition'];
}

if($_GET['OrderBy'])
{
    $OrderByList = explode('|', $_GET['OrderBy']);
    $OrderByColumn = $OrderByList[0];
    $OrderByType = $OrderByList[1];
    $OrderBy = join(' ', $OrderByList);
    $URL[] = 'OrderBy=' . $_GET['OrderBy'];
}
else
{
    $OrderBy = ' ResultID DESC';
    $OrderByColumn = 'ResultID';
    $OrderByType = 'DESC';
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
    $_SESSION['ResultQueryCondition'] = $QueryCondition;
    $WHERE[] = $QueryCondition;
    $URL[] = 'QueryMode=' . $_GET['QueryMode'];
}
$Url = '?' . join('&', $URL);
$WHERE[] = "IsDroped = '0'";
$Where = join(' AND ', $WHERE);

$FieldsToShow = testSetCustomFields('Result');

/* Get pagination */
$Pagination = new Page('ResultInfo', '', '', '', 'WHERE ' . $Where . ' ORDER BY ' . $OrderBy, $Url, $MyDB);
$LimitNum = $Pagination->LimitNum();
$TPL->assign('PaginationDetailInfo', $Pagination->getDetailInfo());

$ColumnArray = @array_keys($FieldsToShow);
$Columns = 'ResultID,' . join(',',$ColumnArray);

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

$ResultList = dbGetList('ResultInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
$UserNameList = testGetOneDimUserList();
$ResultList = testSetResultListMultiInfo($ResultList, $UserNameList);

$TPL->assign('ResultList', $ResultList);
$TPL->assign('OrderByColumn', $OrderByColumn);
$TPL->assign('OrderByType', $OrderByType);
$TPL->assign('QueryMode', $_GET['QueryMode']);
$TPL->assign('QueryColumn', $QueryColumn);
$TPL->assign('OrderByTypeList', $OrderByTypeList);
$TPL->assign('BaseTarget', '_self');
$TPL->assign('TestMode', 'Result');

if($_GET['Export'] == 'HtmlTable')
{
    $TPL->assign('DataList', $ResultList);
    $TPL->assign('FieldsToShow', $_LANG["ResultFields"]);
    $TPL->display('ExportList.tpl');
    exit;
}

$TPL->display('ResultList.tpl');
?>
