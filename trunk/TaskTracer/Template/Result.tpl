{include file="Header.tpl"}
<body class="ResultMode ResultMain" onload="TestMode='Result';initShowGotoBCR();">

{if $ActionType eq 'Edited'}
    {assign var="EditMode" value= 'true'}
{/if}

{if $ActionType eq 'OpenResult'}
    <form id="ResultForm" name="ResultForm" action="PostAction.php?Action=OpenResult" target="PostActionFrame" enctype="multipart/form-data" method="post">
{elseif $EditMode eq 'true'}
  <form id="ResultForm" name="ResultForm" action="PostAction.php?Action=EditResult" target="PostActionFrame" enctype="multipart/form-data" method="post">
{else}
  <form id="ResultForm" name="ResultForm" action="Result.php?ResultID={$ResultInfo.ResultID}" method="post" target="_self">
{/if}

<div id="TopNavMain" class="ResultMode">
  <a id="TopNavLogo" href="./" target="_top">{$Lang.ProductName}</a>
    {if  $ActionType eq 'OpenResult'}
        <span id="TopBCRId">{$Lang.OpenResult}</span>
    {else}
        <span id="TopBCRId">{$Lang.ViewResult} #{$ResultInfo.ResultID}</span>
    {/if}
</div>

<!-- Set the Toolbar buttons status by Lichuan Liu -->
{if $PreResultID eq 0 or $EditMode eq 'true'}{assign var="PreButtonStatus" value = 'disabled="disabled"'}{/if}
{if $NextResultID eq 0 or $EditMode eq 'true'}{assign var="NextButtonStatus" value = 'disabled="disabled"'}{/if}
{if $ActionType eq 'OpenResult' or $EditMode eq 'true'}{assign var="EditButtonStatus" value = 'disabled="disabled"'}{/if}
{if $ActionType eq 'OpenResult' or $EditMode eq 'true' or $ResultInfo.ResultValue eq 'Pass'}{assign var="OpenBugButtonStatus" value = 'disabled="disabled"'}{/if}
{if $ActionType eq ''}{assign var="SaveButtonStatus" value = 'disabled="disabled"'}{/if}

<div id="ButtonList">
  <input type="button" class="ActionButton" accesskey="P" value="{$Lang.PreButton}" onclick="location.href='Result.php?ResultID={$PreResultID}'" {$PreButtonStatus}/>
  <input type="button" class="ActionButton" accesskey="N" value="{$Lang.NextButton}" onclick="location.href='Result.php?ResultID={$NextResultID}'" {$NextButtonStatus}/>
  <input type="button" class="ActionButton" accesskey="E" value="{$Lang.EditResultButton}" onclick="xajax.$('ActionType').value='Edited';submitForm('ResultForm')" {$EditButtonStatus}}/>
  <input type="button" class="ActionButton" accesskey="T" value="{$Lang.OpenBugButton}" {$OpenBugButtonStatus}  onclick="openWindow('Bug.php?ActionType=OpenBug&ResultID={$ResultInfo.ResultID}&CaseID={$ResultInfo.CaseID}','OpenBug');"/>
  <input type="button" class="ActionButton" accesskey="S" value="{$Lang.SaveButton}" name="SubmitButton" id="SubmitButton" onclick="this.disabled='disabled';NeedToConfirm=false;document.ResultForm.submit();" {$SaveButtonStatus}/>
</div>

<div id="ResultMain" class="CommonForm ResultMode">

  <div id="ResultSummaryInfo">
    <dl style="line-height:17pt">
      <dt>{$Lang.ResultFields.ResultTitle}</dt>
      <dd>
          {if $ActionType eq 'OpenResult'}
            <input type="text" id="ResultTitle" name="ResultTitle" class="MyInput RequiredField" value="{$CaseInfo.CaseTitle}" maxlength="150" size="100%"/>
          {elseif $EditMode eq 'true'}
            <input type="text" id="ResultTitle" name="ResultTitle" class="MyInput RequiredField" value="{$ResultInfo.ResultTitle}" maxlength="150" size="100%"/>
          {else}
            <input type="text" id="ResultTitle" name="ResultTitle" readonly=true class="MyInput ReadOnlyField" value="{$ResultInfo.ResultTitle}" maxlength="150" size="100%"/>
          {/if}
      </dd>
    </dl>
    <dl style="line-height:17pt">
      <dt>{$Lang.ResultFields.ProjectName}/{$Lang.ResultFields.ModulePath}</dt>
         {if $ActionType eq 'OpenResult'}
            <dd>
               {$CaseInfo.ProjectName}
               <input type="hidden" name="ProjectID" value="{$CaseInfo.ProjectID}" />
            </dd>
             <dd>
               {$CaseInfo.ModulePath}
               <input type="hidden" name="ModuleID" value="{$CaseInfo.ModuleID}" />
             </dd>
        {else}
          <dd>{$ResultInfo.ProjectName}{$ResultInfo.ModulePath}</dd>
        {/if}
    </dl>
  </div>

  <div id="ResultMainInfo">
        <table style="width: 100%">
            <tr>
                <td style="width: 30%" valign="top">
              <fieldset class="Normal FloatLeft" style="width: 95%">
                <legend>{$Lang.ResultStatusInfo}</legend>
                <dl style="line-height:17pt">
                  <dt>{$Lang.ResultFields.ResultValue}</dt>
                  <dd>
                      {if $ActionType eq 'OpenResult'}
                      {$ResultValueSelectList}
                      {elseif $EditMode eq 'true'}
                      {$ResultValueSelectList}
                      {else}
                      {$ResultInfo.ResultValueName}
                      {/if}
                  </dd>
                </dl>
                <dl style="line-height:17pt">
                  <dt>{$Lang.ResultFields.ResultStatus}</dt>
                  <dd>
                      {if $ActionType eq 'OpenResult' or $EditMode eq 'true'}
                      {$ResultStatusSelectList}
                      {else}
                      {$ResultInfo.ResultStatusName}
                      {/if}
                  </dd>
                </dl>
                <dl style="line-height:17pt">
                  <dt>{$Lang.ResultFields.AssignedTo}</dt>
                  <dd>
                               {if $ActionType eq 'OpenResult' or $EditMode eq 'true'}
                          <span id="AssignedToUserList">{$AssignedToUserList}</span>
                      {else}
                                    {$ResultInfo.AssignedToName}
                                {/if}
                  </dd>
                </dl>
                <dl style="line-height:17pt">
                  <dt>{$Lang.ResultFields.MailTo}</dt>
                  <dd>
                      {if $ActionType eq 'OpenResult' or $EditMode eq 'true'}
                          <input id="MailTo" name="MailTo" type="text" value="{$ResultInfo.MailTo}" size="16" maxlength="255" AUTOCOMPLETE="OFF" />
                      {else}
                          <input id="MailTo" name="MailTo" type="text" value="{$ResultInfo.MailToName}" size="16" maxlength="255" readonly=true class="ReadOnlyField" AUTOCOMPLETE="OFF" />
                      {/if}
                  </dd>
                </dl>
                <dl>
                  <dt>{$Lang.ResultFields.LastEditedBy}</dt>
                  <dd>{$ResultInfo.LastEditedByName}</dd>
                </dl>
                <dl>
                  <dt>{$Lang.ResultFields.LastEditedDate}</dt>
                  <dd>{$ResultInfo.LastEditedDate|date_format:"%Y-%m-%d"}</dd>
                </dl>
              </fieldset>
                </td>
                <td style="width: 40%" valign="top">
                <fieldset class="Normal FloatLeft" style="width: 95%">
                  <legend>{$Lang.ResultOpenedInfo}</legend>
                  <dl>
                    <dt>{$Lang.ResultFields.OpenedBy}</dt>
                    <dd>
                                {if $ActionType eq 'OpenResult'}
                            {$templatelite.session.TestRealName}
                        {else}
                            {$ResultInfo.OpenedByName}
                        {/if}
                    </dd>
                  </dl>
                  <dl>
                    <dt>{$Lang.ResultFields.OpenedDate}</dt>
                    <dd>
                                {if $ActionType eq 'OpenResult'}
                            {$templatelite.now|date_format:"%Y-%m-%d"}
                        {else}
                            {$ResultInfo.OpenedDate|date_format:"%Y-%m-%d"}
                        {/if}
                    </dd>
                  </dl>
                </fieldset>
                <fieldset class="Normal FloatLeft" style="width: 95%">
                  <legend>{$Lang.ResultEnvInfo}</legend>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.ResultFields.ResultBuild}</dt>
                    <dd id="BuildContainer">
                      {if $ActionType eq 'OpenResult' or $EditMode eq 'true'}
                        <input type=text name="ResultBuild" id="ResultBuild" size="25" class="MyInput RequiredField" maxlength="100" value="{$ResultInfo.ResultBuild}" />
                      {else}
					    <input type=text name="ResultBuild" id="ResultBuildInput" size="35" class="MyInput ReadOnlyField" readonly=true value ="{$ResultInfo.ResultBuild}" />
                      {/if}
                    </dd>
                  </dl>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.ResultFields.ResultOS}</dt>
                  <dd id="BuildContainer">
                      {if $ActionType eq 'OpenResult' or $EditMode eq 'true'}
                      {$ResultOSSelectList}
                      {else}
                          {$ResultInfo.ResultOSName}
                        {/if}
                  </dd>
                  </dl>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.ResultFields.ResultBrowser}</dt>
                  <dd id="BuildContainer">
                      {if $ActionType eq 'OpenResult' or $EditMode eq 'true'}
                          {$ResultBrowserSelectList}
                      {else}
                          {$ResultInfo.ResultBrowserName}
                      {/if}
                  </dd>
                  </dl>
                </fieldset>
                </td>
                <td style="width: 30%" valign="top">
                <fieldset class="Normal FloatLeft" style="width: 95%">
                  <legend>{$Lang.ResultOtherInfo}</legend>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.ResultFields.ResultMachine}</dt>
                    <dd id="BuildContainer">
                      {if $ActionType eq 'OpenResult' or $EditMode eq 'true'}
                        <input type=text name="ResultMachine" id="ResultMachine" size="17" class="MyInput" maxlength="80" value="{$ResultInfo.ResultMachine}" />
                      {else}
                        <input type=text name="ResultMachine" id="ResultMachine" size="17" class="MyInput ReadOnlyField" readonly=true maxlength="80" value="{$ResultInfo.ResultMachine}" />
                      {/if}
                    </dd>
				  </dl>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.ResultFields.ResultKeyword}</dt>
                    <dd>
                        {if $ActionType eq 'OpenResult' or $EditMode eq 'true'}
                          <input type="text" name="ResultKeyword" id="ResultKeyword" size="17" class="MyInput" maxlength="80" value="{$ResultInfo.ResultKeyword}" />
                        {else}
                          <input type="text" name="ResultKeyword" id="ResultKeyword" size="17" class="MyInput ReadOnlyField" readonly=true maxlength="80" value="{$ResultInfo.ResultKeyword}" />
                        {/if}
                    </dd>
                  </dl>
                </fieldset>
                <fieldset class="Normal FloatLeft" style="width: 95%">
                  <legend>{$Lang.ResultConditionInfo}</legend>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.ResultFields.CaseID}</dt>
                    <dd>
                        {if $ActionType eq 'OpenResult'}
                        <a href="Case.php?CaseID={$CaseInfo.CaseID}" target="_blank">{$CaseInfo.CaseID}</a>
                        <input type="hidden" name="CaseID" value="{$CaseInfo.CaseID}" />
                        {else}
                        <a href="Case.php?CaseID={$ResultInfo.CaseID}" target="_blank">{$ResultInfo.CaseID}</a>
                      {/if}
                    </dd>
                  </dl>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.ResultFields.BugID}</dt>
                    <dd>
                        {foreach from=$ResultInfo.BugIDList item="BugID"}
                            <a href="Bug.php?BugID={$BugID}" target="_blank">{$BugID}</a>
                        {/foreach}
                    </dd>
                  </dl>
                </fieldset>
                <fieldset class="Normal FloatLeft" style="width: 94%">
                  <legend>{$Lang.ResultFiles}</legend>
                   <dl>
                    <dd style="text-align:left;">
                      {assign var="FileList" value=$ResultInfo.FileList}
                      {include file="FileInfo.tpl"}
                    </dd>
                </fieldset>
                 </td>
            </tr>
        </table>
    </div>
 	
  <div id="ResultHistory">
      <table style="width: 100%">
            <tr>
              <td style="width: 50%" valign="top">
                <fieldset class="Normal FloatLeft" id="ResultHistoryInfo" style="width: 98%">
                  <legend>{$Lang.Comments}</legend>
                  {if $ActionType eq 'OpenResult' or $EditMode eq 'true'}
                      <textarea id="ReplyNote" name="ReplyNote" rows="5" style="overflow-y:visible;" ></textarea>
                  {/if}
                  {include file="ResultHistory.tpl"}
                </fieldset>
              </td>
                <td style="width: 38%" valign="top">
                    <fieldset class="Normal FloatLeft" id="ResultStepsInfo" style="width: 96%">
                  <legend>{$Lang.ResultStepsInfo}</legend>
                  {if $ActionType eq 'OpenResult' or $EditMode eq 'true'}
                    <textarea id="ResultSteps" name="ResultSteps" rows="11" style="overflow-y:visible;">{if $ActionType eq 'OpenResult'}
{$CaseInfo.CaseSteps}{else}
{$ResultInfo.ResultSteps}{/if}</textarea>
                  {else}
                    <p style="overflow: auto">{$ResultInfo.ResultSteps|replace:" ":"&nbsp;"|bbcode2html}</p>
                  {/if}
                </fieldset>
              </td>
            </tr>
        </table>
  </div>

</div>

<input type="hidden" id="ResultID" name="ResultID" value="{$ResultInfo.ResultID}" />
<input type="hidden" id="DeleteFileIDs" name="DeleteFileIDs" value="" />
<input type="hidden" id="ActionType" name="ActionType" value="{$ActionType}" />
<input type="hidden" id="ActionObj" name="ActionObj" value="Result" />
<input type="hidden" id="TestUserName" name="TestUserName" value="{$templatelite.session.TestUserName}" />
<input type="hidden" id="TestRealName" name="TestRealName" value="{$templatelite.session.TestRealName}" />
<input type="hidden" id="ToDisabledObj" name="ToDisabledObj" value="SubmitButton" />
<input type="hidden" id="CurrentProjectID" name="CurrentProjectID" value="{$ProjectID}" />
<input type="hidden" id="LastAcitonID" name="LastActionID" value="{$LastActionID}" />


</form>
{include file="PostActionFrame.tpl"}
{literal}
<script type="text/javascript">
//<![CDATA[
function superAddObjValue(objID,addValue)
{
    xajax.$(objID).value += ',' + addValue;
}
{/literal}
{if $ActionType neq ''}
{literal}
setConfirmExitArrays();
initSelectDiv('MailTo','selectDivProjectUserList','getInputSearchValueByComma','setValueByComma');
xajax.$('ReplyNote').focus();
{/literal}
{/if}
{if $ActionType eq 'OpenResult'}
    {literal}focusInputEndPos('ResultBuild');{/literal}
{/if}
{literal}
//]]>
</script>
{/literal}
</body>
</html>
