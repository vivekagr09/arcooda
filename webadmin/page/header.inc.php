<?php
include_once __DIR__ . '/common.inc.php';

$popup_window = isset($_GET['return_message']);

if (!$popup_window) {

	include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
?>
<script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
<?php

} else {

?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
<link rel="stylesheet" href="style.css" type="text/css" />
<style type="text/css">
body,p,td {font-family:Verdana, Arial, Helvetica, sans-serif;font-size:12px;}
a:hover {color:#f00;}
body {margin:0px;}
@media print { .print_hide {display:none;} }
</style>
<script type="text/javascript" src="/webadmin/jscript/common.php.js" defer="defer"></script>
</head>
<body>
<?php

}
?>
<link rel="stylesheet" type="text/css" href="style.css" />

<div class="content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Custom Pages</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;">
				<a href="index.php?action=add" target="_blank">Add New Page</a> |
				<a href="index.php?action=list">List All Pages (<?= count(pageSearch()) ?>)</a>
			</td>
		</tr>
	</table>

	<div align="center">
		<br />
