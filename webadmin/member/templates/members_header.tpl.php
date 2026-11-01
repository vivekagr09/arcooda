<?php
$popup_window = (isset($_GET['return_message'])) ? true : false;

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<style type="text/css">
/*<![CDATA[*/
body,p,td{font-family:Arial;font-size:12px}
a:hover{color:#FF0000}
/*]]>*/
		</style>
		<script type="text/javascript" src="//code.jquery.com/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="/webadmin/jscript/common.php.js"></script>
		<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
	</head>
	<body>
<?php
}
?>
		<script type="text/javascript">
//<![CDATA[
var need_to_add_shipping = false;
var need_to_add_notify   = false;
//]]>
		</script>
		<style type="text/css">
/*<![CDATA[*/
td.quarter{width:25%}
td.left_input{width:20%}
td.right_input{width:30%}
td.right_input input,td.right_input textarea,td.right_input select{width:98%}
/*]]>*/
		</style>
		<link rel="stylesheet" type="text/css" href="/webadmin/invoice/style.css" />

<div class="invoice_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;"><tr>
		<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Members</td>
		<td style="text-align:right;width:80%;background-color:#eeeeee;">
			<a href=".?action=add">Add New Member</a> |
			<a href=".?action=search">Search Members</a> |
			<a href=".?action=list">List All Members</a>
		</td>
	</tr></table>

	<div style="text-align:center">
		<br />
