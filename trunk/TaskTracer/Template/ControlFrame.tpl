<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"     content="text/html; charset={$Lang.Charset}">
<meta http-equiv="Content-Language" content="{$Lang.Charset}">
<link href="Css/Basic.css" rel="stylesheet" type="text/css">
<title>{$HeaderTitle|default:$Lang.ProductName}</title>
<script src="JS/Main.js"></script>
</head>
{literal}
<script language="Javascript">
function expandLeft()
{
    var MainFrame = parent.document.getElementById('MainFrame');
    var Control   = document.getElementById("ControlLeft");
    if(MainFrame.cols == "21%,9,*")
    {
        MainFrame.cols = "0,9,*";
        Control.src    = "Image/late_right.gif";
        TestCookie.write('CloseLeftFrame', 1, 1);
    }
    else
    {
        MainFrame.cols = "21%,9,*";
        Control.src    = "Image/late_left.gif";
        TestCookie.write('CloseLeftFrame', 0, 1);
    }
}
</script>
{/literal}
<body class="{$TestMode}Mode"  height="100%" topmargin="0" marginheight="0" leftmargin="0" marginwidth="0">
<a href="#" onclick="expandLeft();">
<div style="text-align:left;height:98%;margin:0;padding:0;cursor:hand;" onmouseover="this.style.backgroundColor='#DBDFE7';"  onmouseout="this.style.backgroundColor='#F6F6F6';">
<table height="100%" style="border:0;border-collapse:collapse;">
<tr><td style="padding:0;vertical-align:middle;margin:0;border:0;">
<img id="ControlLeft" src="Image/late_{if $CloseLeftFrame}right{else}left{/if}.gif" />
</td></tr>
</table>
</div>
</a>
</body>
</html>
