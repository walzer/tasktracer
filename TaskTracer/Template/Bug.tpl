{include file="Header.tpl"}
<body class="BugMode BugMain" onload="TestMode='Bug';initShowGotoBCR();">
  {if $ActionType eq 'Edited' or $ActionType eq 'Resolved' or $ActionType eq 'Closed' or $ActionType eq 'Activated'}
  {assign var="EditMode" value= 'true'}
  {/if}

  {if $ActionType eq 'OpenBug'}
    <form id="BugForm" name="BugForm" action="PostAction.php?Action=OpenBug" target="PostActionFrame" enctype="multipart/form-data" method="post">
  {elseif $EditMode eq 'true'}
    <form id="BugForm" name="BugForm" action="PostAction.php?Action=EditBug" target="PostActionFrame" enctype="multipart/form-data" method="post">
  {else}
    <form id="BugForm" name="BugForm" action="Bug.php?BugID={$BugInfo.BugID}" method="post" target="_self">
  {/if}

<div id="TopNavMain" class="BugMode">
  <a id="TopNavLogo" href="./" target="_top">{$Lang.ProductName}</a>
    {if  $ActionType eq 'OpenBug'}
        <span id="TopBCRId">{$ActionTypeName}</span>
    {else}
        <span id="TopBCRId">{$ActionTypeName} #{$BugInfo.BugID}</span>
    {/if}
</div>

<!-- Set the Toolbar buttons status by Lichuan Liu -->
{if $PreBugID eq 0 or $EditMode eq 'true'}{assign var="PreButtonStatus" value = 'disabled="disabled"'}{/if}
{if $NextBugID eq 0 or $EditMode eq 'true'}{assign var="NextButtonStatus" value = 'disabled="disabled"'}{/if}
{if $ActionType eq 'OpenBug' or $EditMode eq 'true'}{assign var="EditButtonStatus" value = 'disabled="disabled"'}{/if}
{if $BugInfo.BugStatus neq 'Active' or $ActionType eq 'OpenBug'or $EditMode eq 'true'}{assign var="ResolveBugButtonStatus" value = 'disabled="disabled"'}{/if}
{if $BugInfo.BugStatus neq 'Resolved' or $ActionType eq 'OpenBug'or $EditMode eq 'true'}{assign var="CloseBugButtonStatus" value = 'disabled="disabled"'}{/if}
{if $BugInfo.BugStatus eq 'Active' or $EditMode eq 'true'}{assign var="ActiveBugButtonStatus" value = 'disabled="disabled"'}{/if}
{if $ActionType eq ''}{assign var="SaveButtonStatus" value = 'disabled="disabled"'}{/if}

<div id="ButtonList">
  <input type="button" class="ActionButton" accesskey="P" value="{$Lang.PreButton}" onclick="location.href='Bug.php?BugID={$PreBugID}'" {$PreButtonStatus}/>
  <input type="button" class="ActionButton" accesskey="N" value="{$Lang.NextButton}" onclick="location.href='Bug.php?BugID={$NextBugID}'" {$NextButtonStatus}/>
  <input type="button" class="ActionButton" accesskey="E" value="{$Lang.EditBugButton}" onclick="xajax.$('ActionType').value='Edited';submitForm('BugForm')" {$EditButtonStatus}}/>
  <input type="button" class="ActionButton" accesskey="C" value="{$Lang.CopyBugButton}" onclick="xajax.$('ActionType').value='OpenBug';submitForm('BugForm');" {$EditButtonStatus} />
  <input type="button" class="ActionButton" accesskey="R" value="{$Lang.ResolveBugButton}" onclick="xajax.$('ActionType').value='Resolved';submitForm('BugForm');" {$ResolveBugButtonStatus} />
  <input type="button" class="ActionButton" accesskey="L" value="{$Lang.CloseBugButton}" onclick="xajax.$('ActionType').value='Closed';submitForm('BugForm');" {$CloseBugButtonStatus} />
  <input type="button" class="ActionButton" accesskey="A" value="{$Lang.ActiveBugButton}" onclick="xajax.$('ActionType').value='Activated';submitForm('BugForm');" {$ActiveBugButtonStatus} />
  <input type="button" class="ActionButton" accesskey="S" value="{$Lang.SaveButton}" name="SubmitButton" id="SubmitButton" onclick="this.disabled='disabled';NeedToConfirm=false;document.BugForm.submit();" {$SaveButtonStatus}/>
</div>

<div id="BugMain" class="CommonForm BugMode">

  <div id="BugSummaryInfo">
    <dl style="line-height:17pt">
      <dt>{$Lang.BugFields.BugTitle}</dt>
      <dd>
          {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
            <input type="text" id="BugTitle" name="BugTitle" class="MyInput RequiredField" value="{$BugInfo.BugTitle}" maxlength="150" size="100%"/>
          {else}
            <input type="text" id="BugTitle" name="BugTitle" readonly=true class="MyInput ReadOnlyField" value="{$BugInfo.BugTitle}" maxlength="150" size="100%"/>
        {/if}
      </dd>
    </dl>
    <dl style="line-height:17pt">
      <dt>{$Lang.BugFields.ProjectName}/{$Lang.BugFields.ModulePath}</dt>
         {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
            <dd>{$ProjectList}</dd>
             <dd id="SlaveModuleList">{$ModuleList}</dd>
        {else}
          <dd>{$BugInfo.ProjectName}{$BugInfo.ModulePath}</dd>
        {/if}
    </dl>
  </div>

  <div id="BugMainInfo">
        <table style="width: 100%">
            <tr>
                <td style="width: 30%" valign="top">
              <fieldset class="Normal FloatLeft" style="width: 95%">
                <legend>{$Lang.BugStatusInfo}</legend>
                <dl>
                  <dt>{$Lang.BugFields.BugStatus}</dt>
                  <dd>
                    {if $ActionType eq 'Activated'}{$Lang.BugStatus.Active}
                    {elseif $ActionType eq 'Closed'}{$Lang.BugStatus.Closed}
                    {elseif $ActionType eq 'Resolved'}{$Lang.BugStatus.Resolved}
                    {else}{$BugInfo.BugStatusName}{/if}
					  <!-- Walzer Add 偷偷把BugStatus POST上去  -->
					  <input type="hidden" name="BugStatus" value="{$BugInfo.BugStatus}" />
                  </dd>
                </dl>
                <dl style="line-height:17pt">
                  <dt>{$Lang.BugFields.AssignedTo}</dt>
                  <dd>
                               {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                          <span id="AssignedToUserList">{$AssignedToUserList}</span>
                      {else}
                                    {$BugInfo.AssignedToName}
                                {/if}
                  </dd>
                </dl>
                <dl style="line-height:17pt">
                  <dt>{$Lang.BugFields.MailTo}</dt>
                  <dd>
                               {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                          <input id="MailTo" name="MailTo" type="text" value="{$BugInfo.MailTo}" size="20"  maxlength="255" AUTOCOMPLETE="OFF" />
                      {else}
                          <input id="MailTo" name="MailTo" type="text" value="{$BugInfo.MailToName}" size="20"  maxlength="255" readonly=true class="MyInput ReadOnlyField" AUTOCOMPLETE="OFF" />
                      {/if}
                  </dd>
                </dl>
                <dl style="line-height:17pt">
                  <dt>{$Lang.BugFields.BugSeverity}</dt>
                  <dd>
                               {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                          {$BugSeverityList}
                      {else}
                                    {$BugInfo.BugSeverityName}
                      {/if}
                  </dd>
                </dl>
                <dl style="line-height:17pt">
                  <dt>{$Lang.BugFields.BugPriority}</dt>
                  <dd>
                               {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                          {$BugPriorityList}
                      {else}
                                    {$BugInfo.BugPriorityName}
                                {/if}
                  </dd>
                </dl>
                <dl style="line-height:17pt">
                  <dt>{$Lang.BugFields.BugType}</dt>
                  <dd>
                               {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                          {$BugTypeList}
                      {else}
                          {$BugInfo.BugTypeName}
                                {/if}
                  </dd>
                </dl>
                {if $ProjectID != 4}  <!-- 网游不想要这些字段 -->
                <dl style="line-height:17pt">
                  <dt>{$Lang.BugFields.HowFound}</dt>
                  <dd>
                               {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                          {$BugHowFoundList}
                      {else}
                          {$BugInfo.HowFoundName}
                                {/if}
                  </dd>
                </dl>

                <dl style="line-height:17pt">
                  <dt>{$Lang.BugFields.BugOS}</dt>
                  <dd>
                               {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                          {$BugOSList}
                      {else}
                          {$BugInfo.BugOSName}
                                {/if}
                  </dd>
                </dl>
                <dl style="line-height:17pt">
                  <dt>{$Lang.BugFields.BugBrowser}</dt>
                  <dd>
                               {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                          {$BugBrowserList}
                      {else}
                          {$BugInfo.BugBrowserName}
                                {/if}
                  </dd>
                </dl>
                {/if}

                <dl>
                  <dt>{$Lang.BugFields.LastEditedBy}</dt>
                  <dd>{$BugInfo.LastEditedByName}</dd>
                </dl>
                <dl>
                  <dt>{$Lang.BugFields.LastEditedDate}</dt>
                  <dd>{$BugInfo.LastEditedDate|date_format:"%Y-%m-%d"}</dd>
                </dl>
              </fieldset>
                </td>
                <td style="width: 40%" valign="top">
                <fieldset class="Normal FloatLeft" style="width: 95%">
                  <legend>{$Lang.BugOpenedInfo}</legend>
                  <dl>
                    <dt>{$Lang.BugFields.OpenedBy}</dt>
                    <dd>
                                {if $ActionType eq 'OpenBug'}
                            {$templatelite.session.TestRealName}
                        {else}
                            {$BugInfo.OpenedByName}
                        {/if}
                    </dd>
                  </dl>
                  <dl>
                    <dt>{$Lang.BugFields.OpenedDate}</dt>
                    <dd>
                                {if $ActionType eq 'OpenBug'}
                            {$templatelite.now|date_format:"%Y-%m-%d"}
                        {else}
                            {$BugInfo.OpenedDate|date_format:"%Y-%m-%d"}
                        {/if}
                    </dd>
                  </dl>
                  {if $ProjectID != 4}  <!-- 网游不想要的字段 -->
                  <dl style="line-height:17pt">
                    <dt>{$Lang.BugFields.OpenedBuild}</dt>
                    <dd id="BuildContainer">
                      {if $ActionType eq 'OpenBug' or $ActionType eq 'Edited'}
                        <input type=text name="OpenedBuildInput" id="OpenedBuild" size="35" maxlength="100" value="{$BugInfo.OpenedBuild}" />
                      {else}
                        <input type=text name="OpenedBuildInput" id="OpenedBuildInput" class="MyInput ReadOnlyField" size="35" readonly=true value="{$BugInfo.OpenedBuild}" />
                      {/if}
                    </dd>
                  </dl>
                  {/if}
                </fieldset>
                <fieldset class="Normal FloatLeft" style="width: 95%">
                  <legend>{$Lang.BugResolvedInfo}</legend>
                  <dl>
                    <dt>{$Lang.BugFields.ResolvedBy}</dt>
                    <dd>
                        {if $ActionType eq 'Activated'}
                        {elseif $ActionType eq 'Resolved'}
                          {$ActionRealName}
                        {else}
                          {$BugInfo.ResolvedByName}
                        {/if}
                    </dd>
                  </dl>
                  <dl>
                    <dt>{$Lang.BugFields.ResolvedDate}</dt>
                    <dd>
                        {if $ActionType eq 'Activated'}
                        {elseif $ActionType eq 'Resolved'}
                            {$ActionDate}
                {else}
                  {if $BugInfo.ResolvedDate neq $CFG.ZeroTime}{$BugInfo.ResolvedDate|date_format:"%Y-%m-%d"} {/if}
                {/if}
                    </dd>
                  </dl>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.BugFields.ResolvedBuild}</dt>
                  <dd id="BuildContainer">
                    {if $ActionType eq 'Activated'}
                      {elseif $ActionType eq 'Resolved' or ($BugInfo.BugStatus neq 'Active' and $ActionType eq 'Edited')}
                        <input type=text name="ResolvedBuildInput" id="ResolvedBuild" size="35" class="MyInput RequiredField" maxlength="100" value="{$BugInfo.ResolvedBuild}" />
                      {else}
                        <input type=text name="ResolvedBuildInput1" id="ResolvedBuildInput1" size="35" class="MyInput ReadOnlyField" readonly=true value="{$BugInfo.ResolvedBuild}" />
						{if $BugInfo.BugStatus neq 'Active'}<input type="hidden" name="ResolvedBuildInput"  value="{$BugInfo.ResolvedBuild}" />{/if}
                        {/if}
                  </dd>
                  </dl>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.BugFields.Resolution}</dt>
                    <dd>
                    {if $ActionType eq 'Activated'}
                      {elseif $ActionType eq 'Resolved' or ($BugInfo.BugStatus neq 'Active' and $ActionType eq 'Edited')}
                                    {$ResolutionList}
                                {else}
                                    {$BugInfo.ResolutionName}{if $BugInfo.BugStatus neq 'Active'}<input type="hidden" name="Resolution" value="{$BugInfo.Resolution}" />{/if}
                                {/if}
                  </dd>
                  </dl>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.BugFields.DuplicateID}</dt>
                    <dd>
                    {if $ActionType eq 'Activated'}
                      {elseif $ActionType eq 'Resolved' or ($BugInfo.BugStatus neq 'Active' and $ActionType eq 'Edited')}
                          <input type="text" name="DuplicateID" id="DuplicateID" size="35" class="MyInput RequiredField" value="{$BugInfo.DuplicateID}" {if $BugInfo.Resolution neq "Duplicate"}style="display:none;"{/if} />
                        {else}
                        {foreach from=$BugInfo.DuplicateIDList item="DuplicateID"}
                            <a href="Bug.php?BugID={$DuplicateID}" target="_blank">{$DuplicateID}</a>
                        {/foreach}{if $BugInfo.Resolution eq 'Duplicate'}<input type="hidden" name="DuplicateID" value="{$BugInfo.DuplicateID}" />{/if}
                                {/if}
                    </dd>
                  </dl>
                </fieldset>
                <fieldset class="Normal FloatLeft"  style="width: 95%">
                  <legend>{$Lang.BugClosedInfo}</legend>
                  <dl>
                    <dt>{$Lang.BugFields.ClosedBy}</dt>
                    <dd>
                      {if $ActionType eq 'Activated'}
                        {elseif $ActionType eq 'Closed'}
                          {$ActionRealName}
                        {else}
                          {$BugInfo.ClosedByName}
                      {/if}
                    </dd>
                  </dl>
                  <dl>
                    <dt>{$Lang.BugFields.ClosedDate}</dt>
                    <dd>
                      {if $ActionType eq 'Activated'}
                      {elseif $ActionType eq 'Closed'}
                        {$ActionDate}
                      {else}
                        {if $BugInfo.ClosedDate neq $CFG.ZeroTime}{$BugInfo.ClosedDate|date_format:"%Y-%m-%d"}{/if}{/if}
                    </dd>
                  </dl>
                </fieldset>
                </td>
                <td style="width: 30%" valign="top">
                {if $ProjectID != 4}  <!-- 网游不想要的字段 -->
                <fieldset class="Normal FloatLeft" style="width: 95%">
                  <legend>{$Lang.BugOtherInfo}</legend>
                   <dl style="line-height:17pt">
                    <dt>{$Lang.BugFields.BugSubStatus}</dt>
                    <dd>
                       {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                          {$BugSubStatusList}      
                       {else}
                          {$BugInfo.BugSubStatusName}
                       {/if}
                    </dd>
                  </dl>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.BugFields.BugMachine}</dt>
                    <dd>
                               {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                            <input type="text" name="BugMachine" id="BugMachine" size="20" class="MyInput"  maxlength="80" value="{$BugInfo.BugMachine}" />
                        {else}
                            <input type="text" name="BugMachine" id="BugMachine" size="20" readonly=true class="MyInput ReadOnlyField"  maxlength="80" value="{$BugInfo.BugMachine}" />
                        {/if}
                    </dd>
                  </dl>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.BugFields.BugKeyword}</dt>
                    <dd>
                        {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                            <input type="text" name="BugKeyword" id="BugKeyword" size="20" class="MyInput" maxlength="80" value="{$BugInfo.BugKeyword}" />
                        {else}
                            <input type="text" name="BugKeyword" id="BugKeyword" size="20" readonly=true  class="MyInput ReadOnlyField" maxlength="80" value="{$BugInfo.BugKeyword}" />
                        {/if}
                    </dd>
                  </dl>
                </fieldset>
                {$/if}
                <fieldset class="Normal FloatLeft" style="width: 95%">
                  <legend>{$Lang.BugConditionInfo}</legend>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.BugFields.LinkID}</dt>
                    <dd>
                       {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                         <input type="text" name="LinkID" id="LinkID" size="20" class="MyInput" maxlength="80" value="{$BugInfo.LinkID}" />
                       {else}
                         {foreach from=$BugInfo.LinkIDList item="LinkID"}
                           <a href="Bug.php?BugID={$LinkID}" target="_blank">{$LinkID}</a>
                         {/foreach}
                       {/if}
                    </dd>
                  </dl>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.BugFields.CaseID}</dt>
                    <dd>
                		{if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                			<input type="text" name="CaseID" id="CaseID" size="20" class="MyInput" maxlength="80" value="{$BugInfo.CaseID}" />
                		{else}
							<a href="Case.php?CaseID={$BugInfo.CaseID}" target="_blank">{$BugInfo.CaseID}</a>
                    		<input type="hidden" name="CaseID" value="{$BugInfo.CaseID}" />
                    	{/if}

                        
                    	<!--------------
                       {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                         <input type="text" name="CaseID" id="CaseID" size="20" class="MyInput" maxlength="80" value="{$BugInfo.CaseID}" />
                       {else}
                         {foreach from=$BugInfo.CaseIDList item="CaseID"}
                           <a href="Case.php?CaseID={$CaseID}" target="_blank">{$CaseID}</a>
                         {/foreach}
                       {/if}
                       ------------->
                    </dd>
                  </dl>
                  <dl style="line-height:17pt">
                    <dt>{$Lang.BugFields.ResultID}</dt>
                    <dd>
                      <a href="Result.php?ResultID={$BugInfo.ResultID}" target="_blank">{$BugInfo.ResultID}</a>
                      <input type="hidden" name="ResultID" value="{$BugInfo.ResultID}" />
                    </dd>
                  </dl>
                </fieldset>
                <fieldset class="Normal FloatLeft" style="width: 94%">
                  <legend>{$Lang.BugFiles}</legend>
                   <dl>
                    <dd style="text-align:left;">
                      {assign var="FileList" value=$BugInfo.FileList}
                      {include file="FileInfo.tpl"}
                    </dd>
                  </dl>
                </fieldset>
                 </td>
            </tr>
        </table>
    </div>
  	  

		<!-- Walzer Add Affair List with Manhours here -->
		{if $ActionType != 'OpenBug' and $EditMode != 'true'}{assign var="AffairInputStatus" value = 'disabled="disabled"'}{/if}
		{if $ActionType != 'OpenBug' and $EditMode != 'true'}{assign var="AffairSelectStatus" value = 'disabled="disabled"'}{/if}
	
		<div id="AffairList">
	        <table style="width: 100%">
				<tr>
					<td style = "width: 100%" valign="top">
						<fieldset class="Normal FloatLeft" style="width: 98%">
							<legend>{$Lang.Affair.title}</legend>
								<!-- Walzer在这里加入了任务分值的显示 -->
				                <dl style="line-height:17pt">
				                	<!-- 事务列表统计出的总工时 -->
				                	<dt>{$Lang.BugFields.BugScore}</dt>
				                	<dd>
				                		{if $ActionType eq 'OpenBug' or $EditMode eq 'true' }
				                			<input type="text" id="BugScore" name="BugScore" readonly=true class="MyInput ReadOnlyField" value="{$BugInfo.BugScore}" maxlength="5" size="5"/>
				                		{else}
				                			<input type="text" id="BugScore" name="BugScore" readonly=true class="MyInput ReadOnlyField" value="{$BugInfo.BugScore}" maxlength="5" size="5"/>
				                		{/if}
				                	</dd>
		                        <!-- 显示额外的事务工时 -->
		                        	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                        	<dt>{$Lang.Affair.unlisted_score}</dt>
			         				<dd>
										{if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
											<input type="text" id="UnlistedScore" name="UnlistedScore" value="{$BugInfo.UnlistedScore}" maxlength="5" size="5"/>
										{else}
											<input type="text" id="UnlistedScore" name="UnlistedScore" readonly=true class="MyInput ReadOnlyField" value="{$BugInfo.UnlistedScore}" maxlength="5" size="5"/>
										{/if}
			                        </dd>
				                <!-- Walzer在这里加入了任务质量的显示 -->
				                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				                	<dt>{$Lang.BugFields.FixQuality}</dt>
				                	<dd>
										{if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
											<input type="text" id="FixQuality" name="FixQuality" value="{$BugInfo.FixQuality}" maxlength="5" size="5"/>
										{else}
											<input type="text" id="FixQuality" name="FixQuality" readonly=true class="MyInput ReadOnlyField" value="{$BugInfo.FixQuality}" maxlength="5" size="5"/>
										{/if}
				                	</dd>
				                </dl>
				                
		  						<dl style="line-height:17pt">
									<!-- 状态 -->
		  							<dt>{$Lang.BugFields.DeadlineStatus}</dt>
		  							<dd>
		  								<input type="text" readonly=true class="MyInput ReadOnlyField" value="{$BugInfo.DeadlineStatus}" maxlength="11" size="11"/>
		  							</dd>
		  							<!-- 预期完成时间 -->
		  							<dt>{$Lang.BugFields.Deadline}</dt>
		  							<dd>
										{if $ActionType eq 'OpenBug' or $EditMode eq 'true' }
				                			<input type="text" id="Deadline" name="Deadline" value="{$BugInfo.Deadline}" maxlength="20" size="20"/>
				                		{else}
				                			<input type="text" id="Deadline" name="Deadline" readonly=true class="MyInput ReadOnlyField" value="{$BugInfo.Deadline}" maxlength="20" size="20"/>
				                		{/if}
		  							</dd>
		  						</dl>
					                
				                <dl style="line-height:17pt">&nbsp;</dl>
				                
								{foreach from=$AffairList item="AffairClass"}
									<!-- 先显示大类的名称 -->
									<dl><dt>{$AffairClass.parent.AffairName}</dt></dl>
									<!-- 然后显示小项 -->
									{foreach from=$AffairClass.children item="affair"}
										<dl><dd>
											<!-- 显示事务名称，复选框 -->
											&nbsp;&nbsp;
											<input type="checkbox" name="affair_checkbox[]" value={$affair.AffairID} {$affair.checked} {$AffairInputStatus}>
												{if $affair.Manhour > 0} [{$affair.Manhour}]
												{else} [?]
												{/if}
												{$affair.AffairName}
											</input>
											
											<!-- 需要显示规模和复杂度的情况 -->
											{ if $affair.Manhour < 0 } 
												&nbsp;&nbsp;{$Lang.Affair.scope}: <!-- 规模 -->
												<select name="scope[{$affair.AffairID}]" {$AffairSelectStatus}>
													{section name=i loop=$affair.scope}
														<option value={$affair.scope[i].value} {$affair.scope[i].selected}>
															{$affair.scope[i].Description}
														</option>
													{/section}
													
													<!---
													<option value="1" {$affair.scope[0].selected}>
														{$affair.scope[0].Description}
													</option>
													<option value="2" {$affair.scope[1].selected}>
														{$affair.scope[1].Description}
													</option>
													<option value="3" {$affair.scope[2].selected}>
														{$affair.scope[2].Description}
													</option>
													---->
												</select>
												&nbsp;&nbsp;{$Lang.Affair.complexity}: <!-- 复杂度 -->
												<select name="complexity[{$affair.AffairID}]" {$AffairSelectStatus}>
													{section name=i loop=$affair.complexity}
														<option value={$affair.complexity[i].value} {$affair.complexity[i].selected}>
															{$affair.complexity[i].Description}
														</option>
													{/section}
													
													<!-- 
													<option value="A" {$affair.complexity[0].selected}> {$affair.complexity[0].Description} </option>
													<option value="B" {$affair.complexity[1].selected}> {$affair.complexity[1].Description} </optoin>
													<option value="C" {$affair.complexity[2].selected}> {$affair.complexity[2].Description} </optoin>
													-->
												</select>
											{ /if }
										</dd></dl>
	                        		{/foreach}
	                        	{/foreach}
						</fieldset>
					</td>
				</tr>
	        </table>
		</div>
  
  
  <div id="BugHistory">
      <table style="width: 100%">
            <tr>
              <td style="width: 50%" valign="top">
                <fieldset class="Normal FloatLeft" id="BugHistoryInfo" style="width: 98%">
                  <legend>{$Lang.Comments}</legend>
                  {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                      <textarea id="ReplyNote" name="ReplyNote" rows="5" style="overflow-y:visible;" ></textarea>
                  {/if}
                  {include file="BugHistory.tpl"}
                </fieldset>
              </td>
                <td style="width: 38%" valign="top">
                    <fieldset class="Normal FloatLeft" id="BugReproInfo" style="width: 98%">
                  <legend>{$Lang.BugReproInfo}</legend>
                  {if $ActionType eq 'OpenBug' or $EditMode eq 'true'}
                    <textarea id="ReproSteps" name="ReproSteps" rows="11" style="overflow-y:visible;">{if $BugInfo.ReproSteps eq ''}
{$Lang.DefaultReproSteps.Step}
{$DefaultBugValue.StepInfo}
{$Lang.DefaultReproSteps.Result}
{$Lang.DefaultReproSteps.ResultInfo}
{$Lang.DefaultReproSteps.ExpectResult}
{$DefaultBugValue.ExpectResult}
{$Lang.DefaultReproSteps.Note}{else}
{$BugInfo.ReproSteps}{/if}</textarea>
                  {else}
                    <p style="overflow: auto">{$BugInfo.ReproSteps|replace:" ":"&nbsp;"|bbcode2html}</p>
                  {/if}
                </fieldset>
              </td>
            </tr>
        </table>
  </div>
</div>

<input type="hidden" id="BugID" name="BugID" value="{$BugInfo.BugID}" />
<input type="hidden" id="DeleteFileIDs" name="DeleteFileIDs" value="" />
<input type="hidden" id="ActionType" name="ActionType" value="{$ActionType}" />
<input type="hidden" id="ActionObj" name="ActionObj" value="Bug" />
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
{if $ActionType eq 'OpenBug'}
   {literal}focusInputEndPos('BugTitle');{/literal}
{elseif $ActionType eq 'Resolved'}
   {literal}xajax.$('ResolvedBuild').focus();{/literal}
{/if}
{literal}
//]]>
</script>
{/literal}
</body>
</html>
