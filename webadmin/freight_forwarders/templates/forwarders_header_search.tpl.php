<?php
$popup_window=(isset($_GET['return_message']))?true:false;

if(!$popup_window)
	include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/header.inc.php');
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
?>
<link rel="stylesheet" type="text/css" href="/webadmin/freight_forwarders/style.css" />

<script type="text/javascript" src="/webadmin/functions/tooltip.1.0.2.js"></script>
<script type="text/javascript" src="forwarders_funcs.js.php"></script>

<div class="forwarders_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Freight Search</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;">
				<a href="forwarders_search.php?sea_air=air">View All Air Quotes</a> |
				<a href="forwarders_search.php?sea_air=sea">View All Sea Quotes</a> |
				<a href="forwarders_search.php?">View All Quotes</a>
			</td>
		</tr>
	</table>

	<div align="center">
		<br />