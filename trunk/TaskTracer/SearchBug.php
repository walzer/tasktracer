<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * search bug form.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require('Include/Init.inc.php');

$FieldName = 'Field';
$OperatorName = 'Operator';
$ValueName = 'Value';
$AndOrName = 'AndOr';

$FieldList = array();
$OperatorList = array();
$ValueList = array();
$AndOrList = array();
$FieldCount = $_CFG['QueryFieldNumber'];
$Attrib = 'class="FullSelect"';

$FieldListSelectItem = array(0=>'ProjectName',1=>'OpenedBy',2=>'ModulePath',3=>'AssignedTo',4=>'BugID',5=>'BugTitle');
$OperatorListSelectItem = array(0=>'LIKE', 2=>'LIKE', 5=>'LIKE');

for($I=0; $I<$FieldCount; $I ++)
{
    $FieldListOnChange = ' onchange="setQueryForm('.$I.');"';
    $OperatorListOnChange = ' onchange="setQueryValue('.$I.');"';
    $FieldList[$I] = htmlSelect($_LANG['BugQueryField'], $FieldName . $I, $Mode, $FieldListSelectItem[$I], $Attrib.$FieldListOnChange);
    $OperatorList[$I] = htmlSelect($_LANG['Operators'], $OperatorName . $I, $Mode, $OperatorListSelectItem[$I], $Attrib.$OperatorListOnChange);
    $ValueList[$I] = '<input id="'.$ValueName.$I.'" name="' . $ValueName.$I .'" type="text" size="5" style="width:95%;"/>';
    $AndOrList[$I] = htmlSelect($_LANG['AndOr'], $AndOrName . $I, $Mode, $SelectItem, $Attrib);
}

$SimpleProjectList = array(''=>'')+testGetValidSimpleProjectList();

$AutoTextValue['ProjectNameText']=jsArray($SimpleProjectList);
$AutoTextValue['ProjectNameValue']=jsArray($SimpleProjectList);
$AutoTextValue['SeverityText']=jsArray($_LANG['BugSeveritys']);
$AutoTextValue['SeverityValue']=jsArray($_LANG['BugSeveritys'], 'Key');
$AutoTextValue['PriorityText']=jsArray($_LANG['BugPriorities']);
$AutoTextValue['PriorityValue']=jsArray($_LANG['BugPriorities'], 'Key');
$AutoTextValue['TypeText']=jsArray($_LANG['BugTypes']);
$AutoTextValue['TypeValue']=jsArray($_LANG['BugTypes'], 'Key');
$AutoTextValue['StatusText']=jsArray($_LANG['BugStatus']);
$AutoTextValue['StatusValue']=jsArray($_LANG['BugStatus'], 'Key');
$AutoTextValue['OSText']=jsArray($_LANG['BugOS']);
$AutoTextValue['OSValue']=jsArray($_LANG['BugOS'], 'Key');
$AutoTextValue['BrowserText']=jsArray($_LANG['BugBrowser']);
$AutoTextValue['BrowserValue']=jsArray($_LANG['BugBrowser'], 'Key');
$AutoTextValue['MachineText']=jsArray($_LANG['BugMachine']);
$AutoTextValue['MachineValue']=jsArray($_LANG['BugMachine'], 'Key');
$AutoTextValue['ResolutionText']=jsArray($_LANG['BugResolutions']);
$AutoTextValue['ResolutionValue']=jsArray($_LANG['BugResolutions'], 'Key');
$AutoTextValue['HowFoundText']=jsArray($_LANG['BugHowFound']);
$AutoTextValue['HowFoundValue']=jsArray($_LANG['BugHowFound'], 'Key');
$AutoTextValue['SubStatusText']=jsArray($_LANG['BugSubStatus']);
$AutoTextValue['SubStatusValue']=jsArray($_LANG['BugSubStatus'], 'Key');

$AutoTextValue['FieldType'] = jsArray($_CFG['FieldType']);
$AutoTextValue['FieldOperationTypeValue'] = jsArray($_CFG['FieldTypeOperation']);
$AutoTextValue['FieldOperationTypeText'] = jsArray($_LANG['FieldTypeOperationName']);

$UserList = testGetCurrentUserNameList('PreAppend');
$ACUserList = array(''=>'', 'Active' => 'Active') + $UserList+array('Closed'=>'Closed');
$UserList = array(''=>'') + $UserList;
$AutoTextValue['ACUserText']=jsArray($ACUserList);
$AutoTextValue['ACUserValue']=jsArray($ACUserList, 'Key');
$AutoTextValue['UserText']=jsArray($UserList);
$AutoTextValue['UserValue']=jsArray($UserList, 'Key');

$TPL->assign('AutoTextValue', $AutoTextValue);
$TPL->assign("FieldList", $FieldList);
$TPL->assign("OperatorList", $OperatorList);
$TPL->assign("ValueList", $ValueList);
$TPL->assign("AndOrList", $AndOrList);
$TPL->assign("FieldCount",$FieldCount);

$TPL->display('Search.tpl');
?>
