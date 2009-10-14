{include file="Admin/AdminHeader.tpl"}
<body>
<link href="../Css/TreeMenu.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../JS/TreeMenu.js" type="text/javascript"></script>
<script language="JavaScript" src="../JS/AdminAffair.js" type="text/javascript"></script> 

<!-- 头 -->
{include file="Admin/AdminTopNav.tpl"}
  <span class="AdminNav">
    &lt;<a href="AdminProjectList.php">{$Lang.BackToProjectList}</a>&#124;
    <strong>{if $ModuleType eq 'Bug'}{$Lang.ManageBugModule}{elseif  $ModuleType eq 'Case'}{$Lang.ManageCaseModule}{/if}</strong>
  </span>
  {include file="ActionMessage.tpl"}
  <div id="ModuleList" class="CommonDiv" style="margin-top:3px;width:20%;height:60%;overflow:auto;background-color:#FFFFFF;">
    {$ModuleList}
  </div>
   
  <!-- 新增/编辑/删除事务 -->
  <div style="margin-top:3px; margin-right:5px; width:90%;overflow:auto;">
	
	<hr />
	<form id="AffairForm" name="AffairForm" action="UpdateAffair.php" enctype="multipart/form-data" method="post">
	
	<dl>
		<dt>
			<b>{$Lang.AdminAffair.title1}</b> <!-- 标题1 -->
		</dt>
		<dt style="display:none">
			<input type="text" id="ProjectID" name="ProjectID" value="{$ProjectID}" />
		</dt>
	</dl>
	
	<table>
		<tr id = "groupActionType"> <!-- 操作类型 -->
			<td>{$Lang.AdminAffair.ActionType}</td>
			<td>
				<select id="ModifyAffairAction" name="ModifyAffairAction" onchange="HideAffairFields(this.selectedIndex);">				
					<option value="New"> {$Lang.AdminAffair.New} </option>
					<option value="Edit"> {$Lang.AdminAffair.Edit} </option>
					<option value="Delete"> {$Lang.AdminAffair.Delete} </option>
				</select>
			</td>
		</tr>
		<tr id = "groupAffairID" style="display:none">  <!-- 事务ID -->
			<td>{$Lang.AdminAffair.AffairID}</td>
			<td>
				<input type="text" id="AffairID" name="AffairID" maxlength="4" size="20"/>
			</td>
		</tr>
		<tr id = "groupParentID"> <!-- 父类ID -->
			<td>{$Lang.AdminAffair.ParentID}</td>
			<td>
				<select id="ParentID" name="ParentID">
					<option>&nbsp;</option>
					{foreach from=$AffairList item="AffairClass"}
						<option value="{$AffairClass.parent.AffairID}"> {$AffairClass.parent.AffairName} </option>
					{/foreach}
				</select>
				<!-- input type="text" id="ParentID" name="ParentID" maxlength="4" size="20"/ -->
			</td>
		</tr>
		<tr id = "groupModuleID"> <!-- 模块ID -->
			<td>{$Lang.AdminAffair.ModuleID}</td>
			<td>{$SelectAddModuleList}</td>
			<!-- 这里给模块ID增加一个value = null, 提交后不改变原来的值，用为默认  -->
			<script type="text/javascript">
				var y = document.createElement('option');
			  	y.text='NULL';
			  	var x = document.getElementById("ParentModuleID");
			    x.add(y); // ie only
			</script>
		</tr>
		<tr id = "groupAffairName"> <!-- 事务名称 -->
			<td>{$Lang.AdminAffair.AffairName}</td>
			<td><input type="text" id="AffairName" name="AffairName" maxlength="255" size="100%"></td>
		</tr>
		<tr id = "groupManhour"> <!-- 理想工时 -->
			<td>{$Lang.AdminAffair.Manhour}</tr>
	  		<td><input type="text" id="Manhour" name="Manhour" maxlength="2" size="20"></td>
		</tr>
	</table>
	
	<!-- 提交事务 -->
	<table><tr><td>
	<input type="button" value="{$Lang.AdminAffair.Submit}" name="SubmitAffair" id="SubmitAffair" onclick="document.AffairForm.submit();"/>
	</td></tr></table>
	
	</form>  <!-- 这里结束了事务编辑的FORM -->
	
	<hr /> 
<!------------------------------------------------------------------------------------------------------------------------->
	<!-- 设置规模度和复杂度矩阵 -->
	<form id="MatrixForm" name="MatrixForm" action="UpdateScopeComplexity.php" enctype="multipart/form-data" method="post">
	<dl>
		<dt>
			<b>{$Lang.AdminAffair.title2}</b> <!-- 标题2 -->
		</dt>
		<!-- 隐藏ProjectID和ModuleID, 让UpdateScopeComplexity.php最后页面跳转时用 -->
		<dt style="display:none">
			<input type="text" id="ProjectID" name="ProjectID" value="{$ProjectID}" />
			<input type="text" id="ModuleID" name="ModuleID" value="{$ModuleID}" />
		</dt>
	</dl>


	
	<!-- 规模度和复杂度，复用或新建 -->

	<table id="CreateType">
		<tr>
			<td>{$Lang.AdminAffair.ActionType}</td>
			<td>
				<select id="MatrixAction" name="MatrixAction" onchange="ShowMatrix(this.selectedIndex);">
					<option value="Copy"> {$Lang.AdminAffair.CopyMatrix} </option>
					<option value="Create"> {$Lang.AdminAffair.CreateMatrix} </option>
					<option value="Delete">{$Lang.AdminAffair.DeleteMatrix} </option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{$Lang.AdminAffair.MatrixDestAffairID}</td>
			<td><input type="text" id="MatrixToAffairID" name="MatrixToAffairID" maxlength="4" size="20"/></td>
		</tr>
		<tr id="CopyMatrix">
			<td>{$Lang.AdminAffair.CopyFromAffairID}</td>
			<td>
				<input type="text" id="CopyMatrixFrom" name="CopyMatrixFrom" maxlength="4" size="4">
			</td>
		</tr>
	</table>
		
	<table id="ScopeComplexityMatrix" style="display:none">
		<tr><td>			    
		    <!-- 追加一行 -->
			<input type="button" name="addRowBtn" value="{$Lang.AdminAffair.AddRow}" onclick="addRow();" />
			<!-- 追加一列 -->
			<input type="button" name="addColBtn" value="{$Lang.AdminAffair.AddCol}" onclick="addCol();" />
			<!-- 删除末行 -->
			<input type="button" name="DelRowBtn" value="{$Lang.AdminAffair.DelRow}" onclick="delRow();" />
			<!-- 删除末列 -->
			<input type="button" name="DelColBtn" value="{$Lang.AdminAffair.DelCol}" onclick="delCol();" />		
		</td></tr>
		<tr><td>
			<table id="ScopeComplexityTable" border="1" align="left">
				<tr>
					<td><b>{$Lang.AdminAffair.TableTitle}</b></td>
					<td><b>1</b></td>
					<td><b>2</b></td>
				</tr>
				<tr>
					<td>A</td>
					<td><input type="text" name="SC_1A" maxlength="2" size="5"></td>
					<td><input type="text" name="SC_2A" maxlength="2" size="5"></td>
				</tr>
				<tr>
					<td>B</td>
					<td><input type="text" name="SC_1B" maxlength="2" size="5"></td>
					<td><input type="text" name="SC_2B" maxlength="2" size="5"></td>
				</tr>
			</table>
		</td></tr>
		<tr><td>
	    	<!-- 复杂度描述 -->
	    	<table id="ComplexityDesc" border="1" align="left">
	    		<tr>
	    			<td><b>{$Lang.Affair.complexity}</b></td>
	    			<td><b>{$Lang.AdminAffair.Description}</b></td>
	    		</tr>
	    		<tr>
	    			<td><b>A</b></td>
	    			<td><input type="text" name="Complexity_1" maxlength="100" size="40"></td>
	    		</tr>
	    		<tr>
	    			<td><b>B</b></td>
	    			<td><input type="text" name="Complexity_2" maxlength="100" size="40"></td>
	    		</tr>
	    	</table>
	    </td></tr>
		<tr><td>
	    	<!-- 规模度描述 -->
	    	<table id="ScopeDesc" border="1" align="left">
	    		<tr>
	    			<td><b>{$Lang.Affair.scope}</b></td>
	    			<td><b>{$Lang.AdminAffair.Description}</b></td>
	    		</tr>
	    		<tr>
	    			<td><b>1</b></td>
	    			<td><input type="text" id="Scope_1" name="Scope_1" maxlength="100" size="40"></td>
	    		</tr>
	    		<tr>
	    			<td><b>2</b></td>
	    			<td><input type="text" id="Scope_2" name="Scope_2" maxlength="100" size="40"></td>
	    		</tr>
	    	</table>
		</td></tr>
	</table>

	<!-- 提交矩阵 -->
	<table><tr><td>
	<input type="button" value="{$Lang.AdminAffair.Submit}" name="SubmitMatrix" id="SubmitMatrix" onclick="document.MatrixForm.submit();"/>
	</td></tr></table>
	</form>  <!-- 这里结束了事务编辑的FORM -->
	
	<hr /> 
<!------------------------------------------------------------------------------------------------------------------------------->
  
  	<!--- 显示已有的事务列表 -->
	{foreach from=$AffairList item="AffairClass"}
		<!-- 先显示大类的名称 -->
		<dl><dt><b>[{$AffairClass.parent.AffairID}] &nbsp; {$AffairClass.parent.AffairName} </b></dt></dl>
		<!-- 然后显示小项 -->
		
		<table border="1" width=100%>
			<tr>
				<td>ID</td>
				<td>DESC</td>
				<td>MANHOUR</td>
			</tr>
		{foreach from=$AffairClass.children item="affair"}
			<tr>
				<td width=5%> <!-- 事务ID -->
					<b>{$affair.AffairID}</b>  
				</td>
				
				<td width=60%> <!-- 事务名称 -->
					{$affair.AffairName}  
				</td>
				
				<td width=35%> <!-- 工时 -->
					{if $affair.Manhour >= 0} 
						<b>{$affair.Manhour}</b> 
					{else}
						{$Lang.Affair.scope}: <!-- 规模 -->
						<select name="scope[{$affair.AffairID}]" {$AffairSelectStatus}>
							<option value="1" {$affair.scope[0].selected}>
								{$affair.scope[0].Description}
							</option>
							<option value="2" {$affair.scope[1].selected}>
								{$affair.scope[1].Description}
							</option>
							<option value="3" {$affair.scope[2].selected}>
								{$affair.scope[2].Description}
							</option>
						</select>
						<br>
						{$Lang.Affair.complexity}: <!-- 复杂度 -->
						<select name="complexity[{$affair.AffairID}]" {$AffairSelectStatus}>
							<option value="A" {$affair.complexity[0].selected}> {$affair.complexity[0].Description} </option>
							<option value="B" {$affair.complexity[1].selected}> {$affair.complexity[1].Description} </optoin>
							<option value="C" {$affair.complexity[2].selected}> {$affair.complexity[2].Description} </optoin>
						</select>
					{/if}
				</td>
			</tr>
		{/foreach}
		</table>
	{/foreach}
  </div>
  
  <script type="text/javascript">createTreeMenu("ModuleList");</script>

</body>
</html>
