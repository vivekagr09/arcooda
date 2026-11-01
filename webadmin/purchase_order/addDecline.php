<?php
include_once __DIR__ . '/common.inc.php';

$action  = $posted["action"];
$generic = $posted['generic'];

switch ($action) {
    case "do_add":
        $action = "added";

        $DB->query("INSERT INTO `invoice_declines` (`invoice_id`, `reason`, `comments`, `created`, `updated`) VALUES (" . intval($posted["invoice_id"]) . ", " . formatSQLString($posted["reason"]) . ", " . formatSQLString($posted["comments"]) . ", NOW(), NOW())");
        $posted["id"] = $DB->insert_id();
        break;
}

switch ($action) {
    default:
    case "add":
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
	<body style="margin:0;padding:16px;"><form accept-charset="utf-8" action="" method="post">
		<input type="hidden" name="action" value="do_add" />
		<input type="hidden" name="invoice_id" value="<?php hecho($_REQUEST["invoice_id"])?>" />
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
		<center><input type="submit" value=" Add Failed Attempt " /></center>
	</form></body>
</html>
<?php
        break;

    case "added":
        $data = array();
        if (0 != $posted["id"]) {
            $data = $DB->query_first("SELECT * FROM `invoice_declines` WHERE `id` = " . intval($posted["id"]) . " LIMIT 1", MYSQLI_ASSOC);
        }

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
		<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($data)?>;
if (window.opener.setDeclineData)
{
	window.opener.setDeclineData(data);
	window.close();
}
//]]>
		</script>
	</head>
	<body>
	</body>
</html>
<?php
        break;
}
