<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * search result form.
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

$FieldListSelectItem = array(0=>'ProjectName',1=>'OpenedBy',2=>'ModulePath',3=>'AssignedTo',4=>'ResultID',5=>'ResultTitle');
$OperatorListSelectItem = array(0=>'LIKE', 2=>'LIKE', 5=>'LIKE');

for($I=0; $I<$FieldCount; $I ++)
{
    $FieldListOnChange = ' onchange="setQueryForm('.$I.');"';
    $OperatorListOnChange = ' onchange="setQueryValue('.$I.');"';
    $FieldList[$I] = htmlSelect($_LANG['ResultQueryField'], $FieldName . $I, $Mode, $FieldListSelectItem[$I], $Attrib.$FieldListOnChange);
    $OperatorList[$I] = htmlSelect($_LANG['Operators'], $OperatorName . $I, $Mode, $OperatorListSelectItem[$I], $Attrib.$OperatorListOnChange);
    $ValueList[$I] = '<input id="'.$ValueName.$I.'" name="' . $ValueName.$I .'" type="text" size="5" style="width:95%;"/>';
    $AndOrList[$I] = htmlSelect($_LANG['AndOr'], $AndOrName . $I, $Mode, $SelectItem, $Attrib);
}
$SimpleProjectList = array(''=>'')+testGetValidSimpleProjectList();

$AutoTextValue['ProjectNameText']=jsArray($SimpleProjectList);
$AutoTextValue['ProjectNameValue']=jsArray($SimpleProjectList);
$AutoTextValue['ResultValueText']=jsArray($_LANG['ResultValues']);
$AutoTextValue['ResultValueValue']=jsArray($_LANG['ResultValues'], 'Key');
$AutoTextValue['StatusText']=jsArray($_LANG['ResultStatuses']);
$AutoTextValue['StatusValue']=jsArray($_LANG['ResultStatuses'], 'Key');
$AutoTextValue['OSText']=jsArray($_LANG['ResultOS']);
$AutoTextValue['OSValue']=jsArray($_LANG['ResultOS'], 'Key');
$AutoTextValue['BrowserText']=jsArray($_LANG['ResultBrowser']);
$AutoTextValue['BrowserValue']=jsArray($_LANG['ResultBrowser'], 'Key');
$AutoTextValue['MachineText']=jsArray($_LANG['ResultMachine']);
$AutoTextValue['MachineValue']=jsArray($_LANG['ResultMachine'], 'Key');
$AutoTextValue['FrequencyText']=jsArray($_LANG['ResultFrequencies']);
$AutoTextValue['FrequencyValue']=jsArray($_LANG['ResultFrequencies'], 'Key');

$AutoTextValue['FieldType'] = jsArray($_CFG['FieldType']);
$AutoTextValue['FieldOperationTypeValue'] = jsArray($_CFG['FieldTypeOperation']);
$AutoTextValue['FieldOperationTypeText'] = jsArray($_LANG['FieldTypeOperationName']);

$UserList = testGetCurrentUserNameList('PreAppend');
$UserList = testGetCurrentUserNameList('PreAppend');
$ACUserList = array(''=>'', 'Closed' => 'Closed') + $UserList;
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
