<?php
include_once __DIR__ . '/common.inc.php';

switch ($_POST['action']) {
    case "do_add":
		$_GET['id']     = invoiceDeclineAdd($_POST['invoice_id'], $_POST['reason'], $_POST['comments']);
        $_GET['action'] = "added";
		redirect('?' . http_build_query($_GET));
		die();
}

switch ($_GET['action']) {
    default:
        //no break
    case "add":
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
	</head>
	<body style="margin:0;padding:16px;"><form action="" method="post">
		<input type="hidden" name="action" value="do_add" />
		<input type="hidden" name="invoice_id" value="<?=htmlspecialchars($_GET["invoice_id"])?>" />
		<table style="border:1px solid blue;background-color:#9cf;width:100%;">
			<tr>
				<th style="width:25%;">Date</th>
				<td><input type="text" name="date" value="" /></td>
			</tr>
			<tr>
				<th>Reason</th>
				<td><input type="text" name="reason" value="" style="width:99%;" /></td>
			</tr>
			<tr>
				<th>Comments</th>
				<td><textarea name="comments" rows="5" cols="40" style="width:99%;"></textarea></td>
			</tr>
		</table>
		<br />
		<div style="text-align:center"><input type="submit" value=" Add Failed Attempt " /></div>
	</form></body>
</html>
<?php
        break;

    case "added":
        $data = invoiceDeclineFromDatabase($_GET['id']);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>
		<script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($data)?>;
if (window.opener.setDeclineData) {
	window.opener.setDeclineData(data);
	window.close();
}
//]]>
		</script>
	</body>
</html>
<?php
        break;
}
