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

function IsValidParent($ParentID)
{
	if ($ParentID <= 0)
	{
		$FailedMsg = "ParentID must > 0";
		return $FailedMsg;
	}
	
	$Parent = dbGetRow('AffairList', 'ParentID', "AffairID='{$ParentID}'");
	
	if ($Parent > 0)
	{
		$FailedMsg = "Invalid ParentID, this affair can't be a parent";
		return $FailedMsg;
	}
	
	return "";
	
}

function CopyMatrix($src, $dest)
{
	$affair = dbGetList('AffairManhour', '*', "AffairID like '{$src},%' OR AffairID like '%,{$src},' OR AffairID like '%,{$src},%,'");
	
	
	$scope = dbGetList('AffairQuotiety', '*', "Scope IS NOT NULL AND (AffairID like '{$src},%' OR AffairID like '%,{$src},' OR AffairID like '%,{$src},%,') ORDER BY Scope ASC");
	$complexity = dbGetList('AffairQuotiety', '*', "Complexity IS NOT NULL AND (AffairID like '{$src},%' OR AffairID like '%,{$src},' OR AffairID like '%,{$src},%,') ORDER BY Complexity ASC");
 	
 	print_r($affair);
 	echo '<br>';
 	print_r($scope);
 	echo '<br>';
 	print_r($complexity);
 	
 	/******
 	for ($affair as $key => $value)
 	{
 		dbUpdateRow('AffairManhour', 
 						'AffairID', $value['AffairID'] + $dest + ','
 						"index = {$value[index]}");
 	}
 	****/
}

/// progress start
$ModifyAction = $_POST['ModifyAffairAction'];
$ParentID = $_POST['ParentID'];
$Success = 0;

switch ($ModifyAction)
{
	case 'New':
	
		if ($ParentID > 0)
		{
			$FailedMsg = IsValidParent($ParentID);
			
			if (!empty($FailedMsg))
			{
				break;
			}
		}
		
		if (empty($_POST['ParentModuleID']))
		{
			$_POST['ParentModuleID'] = 0;
		}
				
    	$AffairID = dbInsertRow('AffairList', 
    							"'{$_POST[ProjectID]}','{$_POST[ParentModuleID]}','{$_POST[ParentID]}','{$_POST[AffairName]}','{$_POST[Manhour]}'",
                            	'ProjectID,ModuleID,AffairParent,AffairName,Manhour');
        
		break;
		
	case 'Edit':
		if ($_POST['AffairID'] < 0)
		{
			$FailedMsg = "'AffairID' is required when Edit Affair";
			break;
		}
		
		if ($ParentID > 0)
		{
			$FailedMsg = IsValidParent($ParentID);
			
			if (!empty($FailedMsg))
			{
				break;
			}
		}
		
		// 挨个字段检查

		$new = $_POST;
		$new['ModuleID'] = $_POST['ParentModuleID']; 
		$new['AffairParent'] = $_POST['ParentID'];  // 就这个字段名字和别人不一样
		
		// 取旧数据比对
		$old = dbGetRow('AffairList', '*', "AffairID = '{$_POST[AffairID]}'");

		// 凡是提交为空的字段，都保持原先数据库中的值不变
		foreach ($old as $key => $value)
		{
			if (empty($new[$key]))
			{
				$new[$key] = $old[$key];
			}
		} 		
		
        dbUpdateRow('AffairList', 	'ProjectID', "'{$new[ProjectID]}'",
                                 	'ModuleID', "'{$new[ModuleID]}'",
                                 	'AffairParent', "'{$new[AffairParent]}'",
                                 	'AffairName', "'{$new[AffairName]}'",
                                 	'Manhour', "'{$new[Manhour]}'",
                                 	"AffairID = '{$_POST[AffairID]}'");
		
		break;
		
	case 'Delete':
		dbDeleteRow('AffairList', "AffairID = '{$_POST[AffairID]}'");
		break;
}

if (!empty($FailedMsg))
{
	echo "<script> alert({$FailedMsg}); </script>";
}
else
{
	// echo "operation success";
	echo "<script> window.location = 'AdminAffair.php?ProjectID={$_POST[ProjectID]}&ModuleID={$_POST[ParentModuleID]}' </script>";
}