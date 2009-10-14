<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * search case form.
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

$FieldListSelectItem = array(0=>'ProjectName',1=>'OpenedBy',2=>'ModulePath',3=>'AssignedTo',4=>'CaseID',5=>'CaseTitle');
$OperatorListSelectItem = array(0=>'LIKE', 2=>'LIKE', 5=>'LIKE');

for($I=0; $I<$FieldCount; $I ++)
{
    $FieldListOnChange = ' onchange="setQueryForm('.$I.');"';
    $OperatorListOnChange = ' onchange="setQueryValue('.$I.');"';
    $FieldList[$I] = htmlSelect($_LANG['CaseQueryField'], $FieldName . $I, $Mode, $FieldListSelectItem[$I], $Attrib.$FieldListOnChange);
    $OperatorList[$I] = htmlSelect($_LANG['Operators'], $OperatorName . $I, $Mode, $OperatorListSelectItem[$I], $Attrib.$OperatorListOnChange);
    $ValueList[$I] = '<input id="'.$ValueName.$I.'" name="' . $ValueName.$I .'" type="text" size="5" style="width:95%;"/>';
    $AndOrList[$I] = htmlSelect($_LANG['AndOr'], $AndOrName . $I, $Mode, $SelectItem, $Attrib);
}

$AutoTextValue['PriorityText']=jsArray($_LANG['CasePriorities']);
$AutoTextValue['PriorityValue']=jsArray($_LANG['CasePriorities'], 'Key');
$AutoTextValue['TypeText']=jsArray($_LANG['CaseTypes']);
$AutoTextValue['TypeValue']=jsArray($_LANG['CaseTypes'], 'Key');
$AutoTextValue['StatusText']=jsArray($_LANG['CaseStatuses']);
$AutoTextValue['StatusValue']=jsArray($_LANG['CaseStatuses'], 'Key');

$AutoTextValue['MethodText']=jsArray($_LANG['CaseMethods']);
$AutoTextValue['MethodValue']=jsArray($_LANG['CaseMethods'], 'Key');
$AutoTextValue['PlanText']=jsArray($_LANG['CasePlans']);
$AutoTextValue['PlanValue']=jsArray($_LANG['CasePlans'], 'Key');
$AutoTextValue['MarkForDeletionText']=jsArray($_LANG['MarkForDeletions']);
$AutoTextValue['MarkForDeletionValue']=jsArray($_LANG['MarkForDeletions'], 'Key');
$AutoTextValue['ScriptStatusText']=jsArray($_LANG['ScriptStatuses']);
$AutoTextValue['ScriptStatusValue']=jsArray($_LANG['ScriptStatuses'], 'Key');


$SimpleProjectList = array(''=>'')+testGetValidSimpleProjectList();

$AutoTextValue['ProjectNameText']=jsArray($SimpleProjectList);
$AutoTextValue['ProjectNameValue']=jsArray($SimpleProjectList);

$UserList = testGetCurrentUserNameList('PreAppend');
$ACUserList = array(''=>'', 'Active' => 'Active') + $UserList;
$UserList = array(''=>'') + $UserList;
$AutoTextValue['ACUserText']=jsArray($ACUserList);
$AutoTextValue['ACUserValue']=jsArray($ACUserList, 'Key');
$AutoTextValue['UserText']=jsArray($UserList);
$AutoTextValue['UserValue']=jsArray($UserList, 'Key');
$AutoTextValue['ScriptedByText']=jsArray($UserList);
$AutoTextValue['ScriptedByValue']=jsArray($UserList, 'Key');

$AutoTextValue['FieldType'] = jsArray($_CFG['FieldType']);
$AutoTextValue['FieldOperationTypeValue'] = jsArray($_CFG['FieldTypeOperation']);
$AutoTextValue['FieldOperationTypeText'] = jsArray($_LANG['FieldTypeOperationName']);

$TPL->assign('AutoTextValue', $AutoTextValue);
$TPL->assign("FieldList", $FieldList);
$TPL->assign("OperatorList", $OperatorList);
$TPL->assign("ValueList", $ValueList);
$TPL->assign("AndOrList", $AndOrList);
$TPL->assign("FieldCount",$FieldCount);

$TPL->display('Search.tpl');

?>
