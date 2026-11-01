<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$id = 0;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
}

$data  = brandFromDatabase($id);
$field = array('id', 'name', 'text', 'dscription', 'img', 'show_front');

if (!empty($_POST)) {
	brandDelete($id);
    echo "<script>window.location.href='/webadmin/brand'</script>";
}

if (count($data) > 0) {
?>
	<center>
		<h2 style="color: red;">Do you want to delete this item?</h2>
		<table>
<?php
    foreach ($field as $f) {
?>
			<tr>
				<td align="right"><b><?=$f?>:</b></td>
				<td>
<?php
        if ('img' == $f) {
?>
					<img src="<?=$data[$f]?>" width="32"/>
<?php
        } else {
?>
					<?=$data[$f]?>
<?php
        }
?>
				</td>
			</tr>
<?php
    }
?>
		</table>
		<br>
		<form action="?id=<?=$id?>" method="post">
			<input type="hidden" name="action" value="do_delete" />
			<input type="submit" value="Delete" />
		</form>
	</center>
<?php
}

include_once __DIR__ . '/footer.inc.php';