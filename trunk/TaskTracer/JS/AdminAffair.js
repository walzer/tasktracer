function HideAffairFields(param)
{	
	switch (param)
	{
		case 0: // 新增
			document.getElementById("groupAffairID").style.display = "none";
			document.getElementById("groupParentID").style.display = "";
			document.getElementById("groupModuleID").style.display = "";
			document.getElementById("groupAffairName").style.display = "";
			document.getElementById("groupManhour").style.display = "";
			break;
		case 1: // 编辑
			document.getElementById("groupAffairID").style.display = "";
			document.getElementById("groupParentID").style.display = "";
			document.getElementById("groupModuleID").style.display = "";
			document.getElementById("groupAffairName").style.display = "";
			document.getElementById("groupManhour").style.display = "";			
			break;
		case 2: // 删除
			document.getElementById("groupAffairID").style.display = "";
			document.getElementById("groupParentID").style.display = "none";
			document.getElementById("groupModuleID").style.display = "none";
			document.getElementById("groupAffairName").style.display = "none";
			document.getElementById("groupManhour").style.display = "none";
			break;
		default:
			break;
	}	
}

/************
function ShowCreateType(bShow)
{
	if (bShow)
	{
		document.getElementById("CreateType").style.display = "";
		document.getElementById("ShowType").style.display = "none";
		document.getElementById("HideType").style.display = "";
		
		var manhour = document.getElementById("Manhour");
		manhour.value = -1;
		manhour.readOnly = true;
	}
	else
	{
		document.getElementById("CreateType").style.display = "none";
		document.getElementById("ShowType").style.display = "";
		document.getElementById("HideType").style.display = "none";
		document.getElementById("ScopeComplexityMatrix").style.display = "none";

		var manhour = document.getElementById("Manhour");
		manhour.value = 0;
		manhour.readOnly = false;
	}
}
**************/

function ShowMatrix(param)
{
	switch (param)
	{
		case 0: // 复用
			document.getElementById("CopyMatrix").style.display = "";
			document.getElementById("ScopeComplexityMatrix").style.display = "none";
			break;
		case 1: // 新建
			document.getElementById("CopyMatrix").style.display = "none";
			document.getElementById("ScopeComplexityMatrix").style.display = "";
			break;
		case 2: // 修改
			break;
		case 3: // 删除
			document.getElementById("CopyMatrix").style.display = "none";
			document.getElementById("ScopeComplexityMatrix").style.display = "none";
			break;
	}
}

function addRow() 
{
	// 矩阵中追加一行（复杂度）
	var MatrixTable = document.getElementById("ScopeComplexityTable");
	
	// 末尾加一行
	var NewRow = MatrixTable.insertRow( MatrixTable.rows.length );
	
	for ( var i = 0; i < MatrixTable.rows(0).cells.length; i++ )
	{
		// 新加的行里加入列
		var CellObj = NewRow.insertCell(i);
		var RowNum = MatrixTable.rows.length + 63; // 0->A 1->B
		
		if (i == 0)
		{
			var ColName = String.fromCharCode(RowNum);  // 0 -> A
			CellObj.innerHTML = ColName;
		}
		else
		{
			CellObj.innerHTML = "<input type='text' name=SC_" + 
								i.toString() + 
								String.fromCharCode(RowNum.toString()) +  
								" maxlength='2' size='5'>";
		}
	}
	
	// 复杂度描述表中追加一行
	var DescTable = document.getElementById("ComplexityDesc");
	var NewComplexity = DescTable.insertRow( DescTable.rows.length );

	var Cell_1 = NewComplexity.insertCell(0);
	Cell_1.innerHTML = "<b>" + String.fromCharCode(NewComplexity.rowIndex + 64) + "</b>";
	var Cell_2 = NewComplexity.insertCell(1);
	Cell_2.innerHTML = "<input type=text name=Complexity_" + NewComplexity.rowIndex.toString() + " maxlength=100 size=40>";
}

function addCol()
{
	// 矩阵中增加一列（规模度）
	var MatrixTable = document.getElementById("ScopeComplexityTable");
	
	for ( var i = 0; i < MatrixTable.rows.length; i++ )
	{
		var RowObj = MatrixTable.rows(i);
		var CellObj = RowObj.insertCell( RowObj.cells.length );
				
		if (i == 0)
		{
			CellObj.innerHTML = "<b>" + CellObj.cellIndex.toString() + "</b>";
		}
		else
		{
			CellObj.innerHTML = "<input type=text name=SC_" + 
								CellObj.cellIndex.toString() + 
								String.fromCharCode(i + 64) +  
								" maxlength=2 size=5>";
		}
	}
	
	// 规模度描述表中追加一行
	var DescTable = document.getElementById("ScopeDesc");
	var NewScope = DescTable.insertRow( DescTable.rows.length );
	
	var Cell_1 = NewScope.insertCell(0);
	Cell_1.innerHTML = "<b>" + NewScope.rowIndex.toString() + "</b>";
	var Cell_2 = NewScope.insertCell(1);
	Cell_2.innerHTML = "<input type=text name=Scope_" + NewScope.rowIndex.toString() + " maxlength=100 size=40>";
}

function delRow()
{
	var MatrixTable = document.getElementById("ScopeComplexityTable");
	MatrixTable.deleteRow( MatrixTable.rows.length - 1 );
	
	var DescTable = document.getElementById("ComplexityDesc");
	DescTable.deleteRow( DescTable.rows.length - 1);
}

function delCol()
{
	var DestTable = document.getElementById("ScopeComplexityTable");
	
	for (var i = 0; i < DestTable.rows.length; i++)
	{
		var RowObj = DestTable.rows(i);
		RowObj.deleteCell(RowObj.cells.length - 1);
	}
	
	var DescTable = document.getElementById("ScopeDesc");
	DescTable.deleteRow( DescTable.rows.length - 1);
}