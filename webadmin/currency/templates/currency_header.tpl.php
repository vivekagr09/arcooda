<?php
$popup_window=(isset($_GET['return_message']))?true:false;

if(!$popup_window)
	include_once('webadmin_header.tpl.php');
else
{
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
</head>
<body>
<?php
}

$row=$DB->query_first("SELECT COUNT(*) AS `total` FROM `highwaygroup`.`currency`");
?>
<link rel="stylesheet" type="text/css" href="style.css" />

<iframe style="width:168px;height:202px;visibility:hidden;z-index:65535;position:absolute;top:-202px;border:2px ridge;" name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="/webadmin/calendar/ipopeng.htm" scrolling="no" frameborder="0"></iframe>

<div class="currency_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Currencies</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;">
				<a href="index.php?action=list" title="Click to list all currencys">List Currencies(<?= $row['total'] ?>)</a>
			</td>
		</tr>
	</table>

	<div align="center">
		<br />