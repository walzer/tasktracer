{literal}
<script>
function resizeList()
{
var h1 = document.body.clientHeight;
var h2 = document.documentElement.clientHeight;
var h3 = (h2>h1)?h2:h1;
var hsub = 51;
(h2>h1&&h2!=0)?hsub=49:"";
(h2<h1&&h2!=0)?hsub=45:"";
xajax.$('ListSubTable').style.height = h3-hsub + 'px';
}
resizeList();
</script>
{/literal}
