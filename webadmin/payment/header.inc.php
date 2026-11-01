<?php
include_once __DIR__ . '/common.inc.php';

$popup_window = (isset($_GET['return_message'])) ? true : false;

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<style type="text/css">
/*<![CDATA[*/
body,p,td{font-family:Arial;font-size:12px}
a:hover{color:#FF0000}
/*]]>*/
		</style>
	</head>
	<body>
<?php
}

$row = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM %s",
    paymentDatabaseFromPortal($posted['portal'])
));
?>
<link rel="stylesheet" type="text/css" href="style.css" />

<script type="text/javascript" src="payment_funcs.js"></script>

<div class="payment_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Payments</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;">
				<a href=".?action=add" title="Click to add a new payment" target="_blank">Add New Payment</a> |
				<a href=".?action=list" title="Click to list all payments">List Payments(<?=$row['total']?>)</a>
			</td>
		</tr>
	</table>

	<div align="center">
		<br />