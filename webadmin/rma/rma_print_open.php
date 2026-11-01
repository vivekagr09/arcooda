<?php
include_once __DIR__ . '/common.inc.php';

$statement = "SELECT rs.*,
					DATE_FORMAT(rs.date_added, '%d-%m-%Y') as date_added,
					(TO_DAYS(NOW()) - TO_DAYS(rs.date_added)) as days
					FROM rma_service rs
					WHERE rs.status != 'closed'
					ORDER BY rs.date_added DESC";
$result = $DB->query($statement);
$items  = array();
while ($row = $DB->fetch_array($result)) {
    $items[] = $row;
}

?>
<html>
<head>
<title>Open RMA <?=date("d/m/y")?></title>
<script type="text/javascript">
//<![CDATA[
function printpage()
{
	if (window.print) {
		window.print();
	} else {
		alert('To print this page, select Print Frame from the file menu.');
	}
}
function doPrint()
{
	if (confirm("Click OK to print this document")) {
		printpage();
	}
}
//]]>
</script>
</head>
<body onload="//doPrint()">
<br>
<div align="center">
	<font size="+2"><b><u>Coinop Express Open RMA <?=date("d/m/y")?></u></b></font>
</div>
  <br>
    <center>
<?php
$username = get_user();
$name     = $DB->query_first("SELECT `first_name`, `last_name` FROM `highwaygroup`.`users` WHERE `user` = '" . $username . "'");
if (empty($name)) {
    $name = $username;
} else {
    $name = $name['first_name'] . ' ' . $name['last_name'];
}
?>
    Printed By :<?=$name;?> <br>
  </center>
<br><br>
<table border="1" align="center" width="100%" cellpadding="4" cellspacing="0">
	<tr>
		<td><b>RMA</b></td>
		<td><b>Priority</b></td>
		<td><b>Date Added</b></td>
		<td><b>Item</b></td>
		<td><b>Customer</b></td>
		<td><b>Added By</b></td>
		<td><b>Sent To</b></td>
		<td><b>Status</b></td>
		<td><b>Days</b></td>
	</tr>
<?php
foreach ($items as $record) {
?>
	<tr>
		<td><?=$record['id'] . "&nbsp;"?></td>
		<td><?=$record['priority'] . "&nbsp;"?></td>
		<td><?=$record['date_added'] . "&nbsp;"?></td>
		<td><?=$record['item'] . "&nbsp;"?></td>
		<td><?=$record['customer'] . "&nbsp;"?></td>
		<td><?=$record['added_by'] . "&nbsp;"?></td>
		<td><?=$record['sent_to'] . "&nbsp;"?></td>
		<td><?=$record['status'] . "&nbsp;"?></td>
		<td><?=$record['days'] . "&nbsp;"?></td>
	</tr>
<?php
}
?>
</table>
</body>
</html>
