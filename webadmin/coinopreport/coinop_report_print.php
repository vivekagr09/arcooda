<?php
$id = $_POST['idarray'];
if ($id) {
    $items = implode(",", $id);
?>
<script type="text/javascript">
//<![CDATA[
function openWindow()
{
    window.open('coinop_report_print_window.php?somedate=<?=$somedate?>&ids=<?=$items?>');
}
openWindow();
history.back();
//]]>
</script>
<?php
} else {
?>
<blockquote>
	You must select items to before they can be printed<br />
	<form accept-charset="utf-8" autocomplete="off" method="post"><input type="button" value="Go Back" onclick="history.back()" /></form>
</blockquote>
<?php
}
