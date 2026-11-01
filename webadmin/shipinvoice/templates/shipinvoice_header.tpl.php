<?php
$popup_window = (isset($_GET['return_message'])) ? true : false;

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
?>
<html>
<head>
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

$staff_id = staffFromUser($_SERVER['REMOTE_USER']);
?>
<link rel="stylesheet" type="text/css" href="/webadmin/invoice/style.css" />

<script type="text/javascript" src="/webadmin/jscript/common.php.js" defer="defer"></script>
<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
<script type="text/javascript" src="shipinvoice_funcs.js.php"></script>

<div class="invoice_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Dummy Shipping Invoices</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;">
				<a href=".?action=list">List All Dummy Invoices</a>
			</td>
		</tr>
	</table>
	<div align="center">
		<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
