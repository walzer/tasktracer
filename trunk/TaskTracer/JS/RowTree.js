function ChangeParent(id, status)
{
	var ob = document.getElementById("case_img_" + id.toString());
		
	if (status == 1)
	{
		// now opened, it will close at next click
		ob.innerHTML = ob.innerHTML.replace(/ShowChildrenRow/g, "HideChildrenRow");
		ob.innerHTML = ob.innerHTML.replace("closed.gif", "opened.gif");
		ob.innerHTML = ob.innerHTML.replace("ChangeParent(" + id.toString() + ", 1)",
											"ChangeParent(" + id.toString() + ", 0)");
	}
	else
	{
		// now closed, it will open at next click
		ob.innerHTML = ob.innerHTML.replace(/HideChildrenRow/g, "ShowChildrenRow");
		ob.innerHTML = ob.innerHTML.replace("opened.gif", "closed.gif");
		ob.innerHTML = ob.innerHTML.replace("ChangeParent(" + id.toString() + ", 0)",
											"ChangeParent(" + id.toString() + ", 1)");
	}
}

function ShowChildrenRow(id)
{
	var ob = document.getElementById("case_" + id.toString());
	ob.style.display = "";
}

function HideChildrenRow(id)
{
	var ob = document.getElementById("case_" + id.toString());
	ob.style.display = "none";
}