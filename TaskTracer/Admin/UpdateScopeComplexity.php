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

// 处理AffairManhour表和AffairQuotiety表的AffairID字段
// 从AffairID字段中删除$id数字和逗号
function RemoveIdFromString($SrcString, $id)
{
	$temp = explode(',',$SrcString);
	$dest_arr = array();
	foreach ($temp as $i => $j)
	{
		if (!empty($j) && $j != $id)
		{
			array_push($dest_arr, $j);
		}
	}
	$dest_str = implode(',', $dest_arr);
	$dest_str .= ',';
	
	return $dest_str;
}

// 复用其他事务的矩阵和描述
function CopyMatrix($src, $dest)
{
	// 检查src，是否的确有矩阵
	
	// 规模度-复杂度-工时矩阵
	$matrix = dbGetList('AffairManhour', '*', "AffairID like '{$src},%' OR AffairID like '%,{$src},' OR AffairID like '%,{$src},%,'");
	
	// 文字描述表
	$scope = dbGetList('AffairQuotiety', '*', "Scope IS NOT NULL AND (AffairID like '{$src},%' OR AffairID like '%,{$src},' OR AffairID like '%,{$src},%,') ORDER BY Scope ASC");
	$complexity = dbGetList('AffairQuotiety', '*', "Complexity IS NOT NULL AND (AffairID like '{$src},%' OR AffairID like '%,{$src},' OR AffairID like '%,{$src},%,') ORDER BY Complexity ASC");
 	
 	if (empty($matrix) || empty($scope) || empty($complexity))
 	{ 		
 		$FailedMsg = 'The source affair isnot have a Scope-Complexity-Manhour matrix';
 		return $FailedMsg;
 	}
 	
 	/********
 	print_r($matrix);
 	echo '<br>';
 	print_r($scope);
 	echo '<br>';
 	print_r($complexity);
 	******/
 	
 	// 检查目标，是否的确有这个事务
 	$DestAffair = dbGetRow('AffairList', '*', "AffairID = '{$dest}'");
 	
 	if (empty($DestAffair))
 	{
 		return "Dest affair is not exist!";
 	}
 	 	
 	// 检查目标，是否在AffairManhour和AffairQuotiety表中的AffairID字段中已包含
 	$testMatrix = dbGetList('AffairManhour', '*', "AffairID like '{$dest},%' OR AffairID like '%,{$dest},' OR AffairID like '%,{$dest},%,'");
	$testScope = dbGetList('AffairQuotiety', '*', "Scope IS NOT NULL AND (AffairID like '{$dest},%' OR AffairID like '%,{$dest},' OR AffairID like '%,{$dest},%,') ORDER BY Scope ASC");
	$testComplexity = dbGetList('AffairQuotiety', '*', "Complexity IS NOT NULL AND (AffairID like '{$dest},%' OR AffairID like '%,{$dest},' OR AffairID like '%,{$dest},%,') ORDER BY Complexity ASC");
 	
 	if (!empty($testMatrix) || !empty($testScope) || !empty($testComplexity))
 	{
 		echo '<br> testMatrix: ';
		print_r($testMatrix);
	 	echo '<br> testScope: ';
	 	print_r($testScope);
	 	echo '<br> testComplexity: ';
	 	print_r($testComplexity);
 		return "Dest affair is already using Scope-Complexity-Manhour matrix";
 	}
 	
 	// 开始设置
 	
	// 先把目标事务的工时改成-1
	dbUpdateRow('AffairList',
					'Manhour', -1,
					"AffairID = '{$dest}'");
					
 	
	// 在矩阵中加入新的事务ID
 	foreach ($matrix as $key => $value)
 	{
 		$dest_str = vsprintf("%s%d,", array($value['AffairID'],$dest));
 		// echo 'src: '.$value['AffairID'].'<br>dest: '.$dest_str.'<br>';
 		
 		dbUpdateRow('AffairManhour', 
 						'AffairID', "'{$dest_str}'",
 						"id = '{$value[id]}'");
 	}
	
	// 在规模度描述中加入新的事务ID
	foreach ($scope as $key => $value)
	{
		$dest_str = vsprintf("%s%d,", array($value['AffairID'],$dest));
 		// echo 'src: '.$value['AffairID'].'<br>dest: '.$dest_str.'<br>';
 		
 		dbUpdateRow('AffairQuotiety', 
 						'AffairID', "'{$dest_str}'",
 						"id = '{$value[id]}'");
	}
	
	// 在复杂度描述中加入新的事务ID
	foreach ($complexity as $key => $value)
	{
		$dest_str = vsprintf("%s%d,", array($value['AffairID'],$dest));
 		// echo 'src: '.$value['AffairID'].'<br>dest: '.$dest_str.'<br>';
 		
 		dbUpdateRow('AffairQuotiety', 
 						'AffairID', "'{$dest_str}'",
 						"id = '{$value[id]}'");
	}
}

// 删除矩阵和描述
function DeleteMatrix($dest)
{
 	// 检查目标，是否的确有这个事务
 	$DestAffair = dbGetRow('AffairList', '*', "AffairID = '{$dest}'");
 	
 	if (!empty($DestAffair))
 	{
	 	// AffairList = 0
		dbUpdateRow('AffairList',
						'Manhour', 0,
					"AffairID = '{$dest}'");
 	}
 		
 	// 检查目标，是否在AffairManhour表中的AffairID字段中已包含
 	$matrix = dbGetList('AffairManhour', '*', "AffairID like '{$dest},%' OR AffairID like '%,{$dest},' OR AffairID like '%,{$dest},%,'");
	
	if (!empty($matrix))
 	{
		// 在矩阵的AffairID字段中删除事务ID
	 	foreach ($matrix as $key => $value)
	 	{
	 		$dest_str = RemoveIdFromString($value['AffairID'], $dest);
	 		// echo '[matrix] src: '.$value['AffairID'].'<br>dest: '.$dest_str.'<br>';
	 		
	 		if ($dest_str == ',')
	 		{
	 			// 矩阵中该项已经没有其他事务共用了，可以把它删了
	 			dbDeleteRow('AffairManhour', "id = '{$value[id]}'");
	 		}
	 		else
	 		{
	 			// 还有其他事务共用的情况
		 		dbUpdateRow('AffairManhour', 
		 		 				'AffairID', "'{$dest_str}'",
		 		 			"id = '{$value[id]}'");
	 		}
	 	}
 	}
	
	// 检查目标，是否在AffairQuotiety字段中已包含
	$Descript = dbGetList('AffairQuotiety', '*', "AffairID like '{$dest},%' OR AffairID like '%,{$dest},' OR AffairID like '%,{$dest},%,'");	

	if (!empty($Descript))
	{
		// 在规模度描述的AffairID中删除事务ID
		foreach ($Descript as $key => $value)
		{
			$dest_str = RemoveIdFromString($value['AffairID'], $dest); 		
	 		// echo '[Desciprtion] src: '.$value['AffairID'].'<br>dest: '.$dest_str.'<br>';
	 		
	 		if ($dest_str == ',')
	 		{
	 			// 矩阵中该项已经没有其他事务共用了，可以把它删了
	 			dbDeleteRow('AffairQuotiety', "id = '{$value[id]}'");
	 		}
	 		else
	 		{
	 			// 还有其他事务共用的情况
		 		dbUpdateRow('AffairQuotiety', 
		 						'AffairID', "'{$dest_str}'",
		 					"id = '{$value[id]}'");
	 		}
		}
	} 
}

function CreateMatrix($param)
{
	// 检查目标，是否的确有这个事务
 	$DestAffair = dbGetRow('AffairList', '*', "AffairID = '{$param['MatrixToAffairID']}'");
 	
 	if (empty($DestAffair))
 	{
 		return "Dest affair is not exist!";
 	}
 	
 	$matrix = array();
 	$out_of_row = false;
 	$scope = 1;
 	$complexity = 'A';
 
 	while ($out_of_row == false)
 	{
 		while ($out_of_col == false)
 		{
	 		$key = implode('', array('SC_', $scope, $complexity));
	 		
	 		if ($param[$key])
	 		{
	 			$matrix[$scope][$complexity] = $param[$key];
	 			// echo $scope.$complexity.' = '.$param[$key].'<br>';
	 		}
	 		else
	 		{
	 			if ($complexity == 'A')
	 			{
	 				// 第一列就已经没有值了，说明该表已结束
	 				$out_of_row = true;
	 				break;
	 			}
	 			else
	 			{
	 				// 在除了第一列以外的地方没有值，说明该行已空，换下一行看
	 				break;
	 			}
	 		}
	 		
			$complexity = chr(ord($complexity) + 1);
 		}
 		
 		// 下一行
 		$scope = $scope + 1;
 		$complexity = 'A';
 	}
 	
 	// 获取规模度描述
 	$scope = 1;
 	while (1)
 	{
 		$key = implode('', array('Scope_', $scope));
 		
 		if ($param[$key])
 		{
 			$descScope[$scope] = $param[$key];
			$scope++;
 		}
 		else
 		{
 			break;
 		}
 	}
 	
 	// 获取复杂度描述
 	$complexity = 1;
 	$alpha = 'A';
 	while (1)
 	{
 		$key = implode('', array('Complexity_', $complexity));
 		
 		if ($param[$key])
 		{
 			$descComplexity[$alpha] = $param[$key];
 			$complexity++;
 			$alpha = chr(ord($alpha) + 1);
 		}
 		else
 		{
 			break;
 		}
 	}

	// 开始写数据库
	
	// 先拼凑出AffairID字符串，在ID后面要加逗号	
 	$strAffairID = implode('', array($param['MatrixToAffairID'], ','));
 	
 	// 把$matrix写到数据库的AffairManhour表里头
 	foreach ($matrix as $scope => $value)
 	{
 		foreach ($value as $complexity => $manhour)
 		{
    		$MatrixID = dbInsertRow('AffairManhour', "'{$strAffairID}','{$scope}','{$complexity}','{$manhour}'",
                                            'AffairID,scope,complexity,manhour');
 		}
 	}

 	// 把规模度描述写到数据库里
 	foreach ($descScope as $key => $value)
 	{
 		dbInsertRow('AffairQuotiety',  "'{$key}','{$value}','{$strAffairID}'",
 										'Scope, Description, AffairID');
 	}
 	
 	// 把复杂度描述写到数据库里
 	foreach ($descComplexity as $key => $value)
 	{
 		dbInsertRow('AffairQuotiety', "'{$key}','{$value}', '{$strAffairID}'",
 										'Complexity, Description, AffairID');
 	}
 	
 	// 把指定ID的事务，工时改成-1
	dbUpdateRow('AffairList',
					'Manhour', -1,
				"AffairID = '{$param['MatrixToAffairID']}'");
}

/// progress start


switch ($_POST['MatrixAction'])
{
	case 'Copy':
		$FailedMsg = CopyMatrix($_POST['CopyMatrixFrom'], $_POST['MatrixToAffairID']);        
		break;
	case 'Create':
		$FailedMsg = CreateMatrix($_POST);
		break;
	case 'Delete':
		$FailedMsg = DeleteMatrix($_POST['MatrixToAffairID']);
		break;
}

if (!empty($FailedMsg))
{
	echo "<script> alert('{$FailedMsg}'); </script>";
}
else
{
	// echo "operation success";
	echo "<script> window.location = 'AdminAffair.php?ProjectID={$_POST[ProjectID]}&ModuleID={$_POST[ModuleID]}' </script>";
}