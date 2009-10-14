<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * list cases.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require('Include/Init.inc.php');

if($_SESSION['TestCurrentProjectID'] - 0 > 0)
{
    //$_SESSION['CaseQueryCondition'] = "ProjectID = '{$_SESSION[TestCurrentProjectID]}'";
}
if($_GET['ProjectID'] - 0 > 0)
{
    $_SESSION['CaseQueryCondition'] = "ProjectID = '{$_GET[ProjectID]}'";
    testSetCurrentProject($_GET['ProjectID']);
}
if($_GET['ModuleID'] - 0 > 0)
{
    $_SESSION['CaseQueryCondition'] = "ModuleID IN ({$_GET['ChildModuleIDs']})";
}
$_SESSION['TestCurrentModuleID'] = $_GET['ModuleID'];

if($_POST['PostQuery'])
{
    $QueryStr = baseGetGroupQueryStr($_POST);
    $_SESSION['CaseQueryCondition'] = $QueryStr;
}

if($_REQUEST['QueryID'])
{
    $QueryInfo = dbGetRow('TestUserQuery', '', "QueryID='{$_REQUEST[QueryID]}' AND QueryType='Case'");
    $_SESSION['CaseQueryCondition'] = $QueryInfo['QueryString'];
}

$WHERE = array();
$URL = array();

$WHERE[] = $_SESSION['TestUserACLSQL'];

if($_SESSION['CaseQueryCondition'] != '')
{
    $WHERE[] = $_SESSION['CaseQueryCondition'];
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
    $OrderBy = ' CaseID DESC';
    $OrderByColumn = 'CaseID';
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
    $_SESSION['CaseQueryCondition'] = $QueryCondition;
    $WHERE[] = $QueryCondition;
    $URL[] = 'QueryMode=' . $_GET['QueryMode'];
}
$Url = '?' . join('&', $URL);
$WHERE[] = "IsDroped = '0'";
$Where = join(' AND ', $WHERE);

$FieldsToShow = testSetCustomFields('Case');

/* Get pagination */
$Pagination = new Page('CaseInfo', '', '', '', 'WHERE ' . $Where . ' ORDER BY ' . $OrderBy, $Url, $MyDB);
$LimitNum = $Pagination->LimitNum();
$TPL->assign('PaginationDetailInfo', $Pagination->getDetailInfo());

$ColumnArray = @array_keys($FieldsToShow);
$Columns = 'CaseID, ParentID,' . join(',',$ColumnArray);

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

$CaseList = dbGetList('CaseInfo',$Columns, $Where, '', $OrderBy, $LimitNum);
$UserNameList = testGetOneDimUserList();
$CaseList = testSetCaseListMultiInfo($CaseList, $UserNameList);

// Walzer Add，按照模块的父子关系排列，递归
function SortByInherit($SrcList, $ParentID, $Level)
{
	$DestList = array();
	// 先找list里头父类为$ParentID的
	foreach($SrcList as $SrcItem)
	{
		if($SrcItem['ParentID'] == $ParentID)
		{
			// 在标题上根据递归层次增加缩进
			$prefix = null;
			for($i = 0; $i < $Level; $i++)
			{
				$prefix .= "&nbsp;&nbsp;";	
			}
			
			$SrcItem['Prefix'] = $prefix;
			// $SrcItem['ListTitle'] = $prefix." ".$SrcItem['ListTitle'];
			
			// 标记递归层次
			$SrcItem['Level'] = $Level;			
			
			// 递归
			$Children = SortByInherit($SrcList, $SrcItem['CaseID'], $Level + 1); 
			
			if (empty($Children))
			{
				array_push($DestList, $SrcItem);
			}
			else
			{
						
				// 将下级递归得到的子模块合并到返回结果中
				$SrcItem['Children'] = array();
								
				foreach($Children as $key => $value)
				{
					array_push($SrcItem['Children'], $value['CaseID']);
				}
				
				array_push($DestList, $SrcItem);
				
				// 做两次循环实在不太优雅。暂且如此了
				foreach($Children as $key => $value)
				{
					array_push($DestList, $value);
				}
			}
		}
	}
	
	return $DestList;
}

$MyCaseList = SortByInherit($CaseList, 0, 0);
// print_r($MyCaseList);
// walzer();
$TPL->assign('CaseList', $MyCaseList);
$TPL->assign('OrderByColumn', $OrderByColumn);
$TPL->assign('OrderByType', $OrderByType);
$TPL->assign('QueryMode', $_GET['QueryMode']);
$TPL->assign('QueryColumn', $QueryColumn);
$TPL->assign('OrderByTypeList', $OrderByTypeList);
$TPL->assign('BaseTarget', '_self');
$TPL->assign('TestMode', 'Case');

if($_GET['Export'] == 'HtmlTable')
{
    $TPL->assign('DataList', $CaseList);
    $TPL->assign('FieldsToShow', $_LANG["CaseFields"]);
    $TPL->display('ExportList.tpl');
    exit;
}

$TPL->display('CaseList.tpl');
?>
