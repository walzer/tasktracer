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

// ����AffairManhour���AffairQuotiety���AffairID�ֶ�
// ��AffairID�ֶ���ɾ��$id���ֺͶ���
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

// ������������ľ��������
function CopyMatrix($src, $dest)
{
	// ���src���Ƿ��ȷ�о���
	
	// ��ģ��-���Ӷ�-��ʱ����
	$matrix = dbGetList('AffairManhour', '*', "AffairID like '{$src},%' OR AffairID like '%,{$src},' OR AffairID like '%,{$src},%,'");
	
	// ����������
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
 	
 	// ���Ŀ�꣬�Ƿ��ȷ���������
 	$DestAffair = dbGetRow('AffairList', '*', "AffairID = '{$dest}'");
 	
 	if (empty($DestAffair))
 	{
 		return "Dest affair is not exist!";
 	}
 	 	
 	// ���Ŀ�꣬�Ƿ���AffairManhour��AffairQuotiety���е�AffairID�ֶ����Ѱ���
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
 	
 	// ��ʼ����
 	
	// �Ȱ�Ŀ������Ĺ�ʱ�ĳ�-1
	dbUpdateRow('AffairList',
					'Manhour', -1,
					"AffairID = '{$dest}'");
					
 	
	// �ھ����м����µ�����ID
 	foreach ($matrix as $key => $value)
 	{
 		$dest_str = vsprintf("%s%d,", array($value['AffairID'],$dest));
 		// echo 'src: '.$value['AffairID'].'<br>dest: '.$dest_str.'<br>';
 		
 		dbUpdateRow('AffairManhour', 
 						'AffairID', "'{$dest_str}'",
 						"id = '{$value[id]}'");
 	}
	
	// �ڹ�ģ�������м����µ�����ID
	foreach ($scope as $key => $value)
	{
		$dest_str = vsprintf("%s%d,", array($value['AffairID'],$dest));
 		// echo 'src: '.$value['AffairID'].'<br>dest: '.$dest_str.'<br>';
 		
 		dbUpdateRow('AffairQuotiety', 
 						'AffairID', "'{$dest_str}'",
 						"id = '{$value[id]}'");
	}
	
	// �ڸ��Ӷ������м����µ�����ID
	foreach ($complexity as $key => $value)
	{
		$dest_str = vsprintf("%s%d,", array($value['AffairID'],$dest));
 		// echo 'src: '.$value['AffairID'].'<br>dest: '.$dest_str.'<br>';
 		
 		dbUpdateRow('AffairQuotiety', 
 						'AffairID', "'{$dest_str}'",
 						"id = '{$value[id]}'");
	}
}

// ɾ�����������
function DeleteMatrix($dest)
{
 	// ���Ŀ�꣬�Ƿ��ȷ���������
 	$DestAffair = dbGetRow('AffairList', '*', "AffairID = '{$dest}'");
 	
 	if (!empty($DestAffair))
 	{
	 	// AffairList = 0
		dbUpdateRow('AffairList',
						'Manhour', 0,
					"AffairID = '{$dest}'");
 	}
 		
 	// ���Ŀ�꣬�Ƿ���AffairManhour���е�AffairID�ֶ����Ѱ���
 	$matrix = dbGetList('AffairManhour', '*', "AffairID like '{$dest},%' OR AffairID like '%,{$dest},' OR AffairID like '%,{$dest},%,'");
	
	if (!empty($matrix))
 	{
		// �ھ����AffairID�ֶ���ɾ������ID
	 	foreach ($matrix as $key => $value)
	 	{
	 		$dest_str = RemoveIdFromString($value['AffairID'], $dest);
	 		// echo '[matrix] src: '.$value['AffairID'].'<br>dest: '.$dest_str.'<br>';
	 		
	 		if ($dest_str == ',')
	 		{
	 			// �����и����Ѿ�û�������������ˣ����԰���ɾ��
	 			dbDeleteRow('AffairManhour', "id = '{$value[id]}'");
	 		}
	 		else
	 		{
	 			// �������������õ����
		 		dbUpdateRow('AffairManhour', 
		 		 				'AffairID', "'{$dest_str}'",
		 		 			"id = '{$value[id]}'");
	 		}
	 	}
 	}
	
	// ���Ŀ�꣬�Ƿ���AffairQuotiety�ֶ����Ѱ���
	$Descript = dbGetList('AffairQuotiety', '*', "AffairID like '{$dest},%' OR AffairID like '%,{$dest},' OR AffairID like '%,{$dest},%,'");	

	if (!empty($Descript))
	{
		// �ڹ�ģ��������AffairID��ɾ������ID
		foreach ($Descript as $key => $value)
		{
			$dest_str = RemoveIdFromString($value['AffairID'], $dest); 		
	 		// echo '[Desciprtion] src: '.$value['AffairID'].'<br>dest: '.$dest_str.'<br>';
	 		
	 		if ($dest_str == ',')
	 		{
	 			// �����и����Ѿ�û�������������ˣ����԰���ɾ��
	 			dbDeleteRow('AffairQuotiety', "id = '{$value[id]}'");
	 		}
	 		else
	 		{
	 			// �������������õ����
		 		dbUpdateRow('AffairQuotiety', 
		 						'AffairID', "'{$dest_str}'",
		 					"id = '{$value[id]}'");
	 		}
		}
	} 
}

function CreateMatrix($param)
{
	// ���Ŀ�꣬�Ƿ��ȷ���������
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
	 				// ��һ�о��Ѿ�û��ֵ�ˣ�˵���ñ��ѽ���
	 				$out_of_row = true;
	 				break;
	 			}
	 			else
	 			{
	 				// �ڳ��˵�һ������ĵط�û��ֵ��˵�������ѿգ�����һ�п�
	 				break;
	 			}
	 		}
	 		
			$complexity = chr(ord($complexity) + 1);
 		}
 		
 		// ��һ��
 		$scope = $scope + 1;
 		$complexity = 'A';
 	}
 	
 	// ��ȡ��ģ������
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
 	
 	// ��ȡ���Ӷ�����
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

	// ��ʼд���ݿ�
	
	// ��ƴ�ճ�AffairID�ַ�������ID����Ҫ�Ӷ���	
 	$strAffairID = implode('', array($param['MatrixToAffairID'], ','));
 	
 	// ��$matrixд�����ݿ��AffairManhour����ͷ
 	foreach ($matrix as $scope => $value)
 	{
 		foreach ($value as $complexity => $manhour)
 		{
    		$MatrixID = dbInsertRow('AffairManhour', "'{$strAffairID}','{$scope}','{$complexity}','{$manhour}'",
                                            'AffairID,scope,complexity,manhour');
 		}
 	}

 	// �ѹ�ģ������д�����ݿ���
 	foreach ($descScope as $key => $value)
 	{
 		dbInsertRow('AffairQuotiety',  "'{$key}','{$value}','{$strAffairID}'",
 										'Scope, Description, AffairID');
 	}
 	
 	// �Ѹ��Ӷ�����д�����ݿ���
 	foreach ($descComplexity as $key => $value)
 	{
 		dbInsertRow('AffairQuotiety', "'{$key}','{$value}', '{$strAffairID}'",
 										'Complexity, Description, AffairID');
 	}
 	
 	// ��ָ��ID�����񣬹�ʱ�ĳ�-1
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