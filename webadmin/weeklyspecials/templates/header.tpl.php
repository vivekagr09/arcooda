<?php
$popup_window = (isset($_GET['return_message'])) ? true : false;

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
?>
<html>
<head>
<style type="text/css">
body,p,td
{
	font-family:Arial;
	font-size:12px;
}
a:hover
{
	color:#FF0000;
}
</style>
<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
</head>
<body>
<?php
}
?>
<script type="text/javascript" src="/webadmin/jscript/common.php.js"></script>
<link rel="stylesheet" type="text/css" href="style.css" />

<div class="weeklyspecial_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">WeeklySpecials</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;"><a href="index.php?action=add">Add New WeeklySpecial</a> | <a href="index.php?action=search">Search WeeklySpecials</a> | <a href="index.php?action=list">List All WeeklySpecials</a></td>
		</tr>
	</table>

	<div align="center">
		<br />
