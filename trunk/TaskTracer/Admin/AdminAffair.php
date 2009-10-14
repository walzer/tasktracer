<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * admin module list.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require_once("../Include/Init.inc.php");

baseJudgeAdminUserLogin();

// sysXajaxRegister("xProjectSetSlaveModule,xSetModuleOwner,xProjectSetAssignedUser,xAdminAddModule,xAdminEditModule");

$ProjectID = $_GET['ProjectID'];
if($_SESSION['TestIsProjectAdmin'])
{
    $ProjectInfo = current(testGetProjectList("ProjectID = '{$_GET[ProjectID]}' AND ProjectManagers LIKE '%,{$_SESSION['TestUserName']},%'"));
}
elseif($_SESSION['TestIsAdmin'])
{
    $ProjectInfo = current(testGetProjectList("ProjectID = '{$_GET[ProjectID]}'"));
}
if(empty($ProjectInfo))
{
    sysErrorMsg();
}
$ModuleID = $_GET['ModuleID'];
$ModuleType = $_GET['ModuleType'];
// $ModuleType == "Bug" ? $ModuleType = "Bug" : $ModuleType = "Case";
$ModuleType = "Bug";

$ModuleInfo = testGetModuleInfo($ModuleID);

$ModuleTree = testGetAdminTreeModuleList($ProjectID, '?', $ModuleType);

////
// $OnChangeStr = 'onchange="';
// $OnChangeStr .= 'xajax_xSetModuleOwner(this.value, \'AddModuleOwner\');';
// $OnChangeStr .= '"';
$OnChangeStr = '';
$SelectAddModuleList = testGetSelectModuleList($ProjectID, 'ParentModuleID', $ModuleID, $OnChangeStr, $ModuleType);

/*********************
$OnChangeStr = 'onchange="';
$OnChangeStr .= 'xajax_xProjectSetSlaveModule(this.value, \'SlaveModuleList\', \'ParentModuleID\', \''. $ModuleType .'\');xajax_xProjectSetAssignedUser(this.value, \'AssignedToUserList\', \'EditModuleOwner\');';
$OnChangeStr .= '"';
$SelectProjectList = testGetAllProjectSelectList('ProjectID', $ProjectID, $OnChangeStr);

// ModuleList for edit moudle
$OnChangeStr = 'onchange="';
$OnChangeStr .= 'xajax_xSetModuleOwner(this.value, \'EditModuleOwner\');';
$OnChangeStr .= '"';
$SelectEditModuleList = testGetSelectModuleList($ProjectID, 'ParentModuleID', $ModuleInfo['ParentID'], $OnChangeStr, $ModuleType);

// ModuleList for add module
$OnChangeStr = 'onchange="';
$OnChangeStr .= 'xajax_xSetModuleOwner(this.value, \'AddModuleOwner\');';
$OnChangeStr .= '"';
$SelectAddModuleList = testGetSelectModuleList($ProjectID, 'ParentModuleID', $ModuleID, $OnChangeStr, $ModuleType);

$ProjectUserList = testGetProjectUserList($ProjectID, true);

// UserList for edit module
$SelectEditModuleUserList = htmlSelect($ProjectUserList, 'EditModuleOwner', '',$ModuleInfo['ModuleOwner'], 'class="NormalSelect"');

// UserList for add module
$SelectAddModuleUserList = htmlSelect($ProjectUserList, 'AddModuleOwner', '',$ModuleInfo['ModuleOwner'], 'class="NormalSelect"');
*******************************/

		
// Walzer Add affair-manhour here
// ���빤ʱ�ĸ�ѡ���б�
$AffairParent = array();
$DestModule = $ModuleID;

while(empty($AffairParent))
{
	// ��ָ��ProejctID��ModuleID�µ������б�
	$AffairParent = dbGetList('AffairList','*',"ProjectID='{$ProjectID}' AND ModuleID='{$DestModule}' AND AffairParent='0'");
	
	if(!empty($AffairParent))
	{
		// ������ҵ��б�������
		break;
	}
	else if($DestModule == 0)
	{
		// ���û���б����Ѿ����������ڵ��ˣ�Ҳ����
		break;
	}
	
	// û�ҵ��б���ô����TestModule�����ModuleID��ParentID���������´�ѭ�����Ҹ�ģ��������б�
	$DestModule = dbGetRow('TestModule', 'ParentID', "ModuleID='{$DestModule}'");
	$DestModule = $DestModule['ParentID'];
}

/****************
AffairList[i] -- parent
               + children  - [j] -- AffairID
                                  + checked
                                  + scope      -- [0] -- Description
                                                       + selected
                                                + [1] ..
                                  + complexity -- [0] -- Description
                                                       + selected

********************/
// ������ȡ�����µĸ���������
for ($i = 0; $i < count($AffairParent); $i++)
{
	$AffairList[$i]['parent'] = $AffairParent[$i];
		
	// echo 'parent:'.$AffairList[$i]['parent']['AffairName'].'<br>';

	$AffairList[$i]['children'] = dbGetList('AffairList', '*', "AffairParent={$AffairParent[$i]['AffairID']}");

	for ($j = 0; $j < count($AffairList[$i]['children']); $j++)
	{
		$id = $AffairList[$i]['children'][$j]['AffairID'];
		
		// ��MANHOUR<1,˵����Ҫ��AffairQuotiety���ж���SCOPE��COMPLEXITY			
		if ($AffairList[$i]['children'][$j]['Manhour'] < 0)
		{
			$AffairList[$i]['children'][$j]['scope'] = dbGetList('AffairQuotiety', 'Description', "Scope IS NOT NULL AND (AffairID like '{$id},%' OR AffairID like '%,{$id},' OR AffairID like '%,{$id},%,') ORDER BY Scope ASC");
			$AffairList[$i]['children'][$j]['complexity'] = dbGetList('AffairQuotiety', 'Description', "Complexity IS NOT NULL AND (AffairID like '{$id},%' OR AffairID like '%,{$id},' OR AffairID like '%,{$id},%,') ORDER BY Complexity ASC");
			// print_r($AffairList[$i]['children'][$j]['scope']);
			// echo '<br>';
			// print_r($AffairList[$i]['children'][$j]['complexity']);
			// walzer();
		}
	}

}

$TPL->assign('AffairList', $AffairList);

/* Assign */
$TPL->assign('ModuleID', $ModuleID);
$TPL->assign('ProjectID', $ProjectID);
// $TPL->assign('ModuleType', $ModuleType);
// $TPL->assign('ProjectInfo', $ProjectInfo);
// $TPL->assign('ModuleInfo', $ModuleInfo);
$TPL->assign('ModuleList', $ModuleTree);
// $TPL->assign('SelectProjectList', $SelectProjectList);
// $TPL->assign('SelectEditModuleList', $SelectEditModuleList);
$TPL->assign('SelectAddModuleList', $SelectAddModuleList);
// $TPL->assign('SelectEditModuleUserList', $SelectEditModuleUserList);
// $TPL->assign('SelectAddModuleUserList', $SelectAddModuleUserList);

/* Display the template file. */
// $TPL->assign('NavActivePro', ' class="Active"');
$TPL->display('Admin/Affair.tpl');
?>
