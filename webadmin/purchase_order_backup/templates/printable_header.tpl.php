<?php
global $_GLOBALS;

$popup_window = (isset($_GET['return_message'])) ? true : false;
$staff_id     = $_GLOBALS['staff']['id'];

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<style type="text/css">
/*<![CDATA[*/
*{color:#000;background-color:transparent}
body,p,td{font-family:Arial;font-size:12px}
a{text-decoration:none}
a:hover{color:red}
table{border-collapse:collapse;border:1px solid #777}
td{border:1px solid #777;padding:2px 2px 2px 2px;font-size:8pt}
th{border:1px solid #777;padding:2px 2px 2px 2px;font-size:8pt;font-weight:700}
/*]]>*/
		</style>
		<script type="text/javascript" src="/jquery/jquery.js"></script>
	</head>
	<body>

<script type="text/javascript" src="common.js"></script>

<iframe style="width:168px;height:202px;visibility:hidden;z-index:65535;position:absolute;top:-202px;border:2px ridge;" name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="/webadmin/calendar/ipopeng.htm" scrolling="no" frameborder="0"></iframe>

<div class="invoice_content">
	<div align="center">
		<br />

<!-- /printable_header.tpl -->
