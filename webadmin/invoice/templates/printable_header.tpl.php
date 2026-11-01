<?php
$popup_window = (isset($_GET['return_message'])) ? true : false;

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<style type="text/css">
/*<![CDATA[*/
*{color:black;background:transparent}
body,p,td{font-family:Arial;font-size:12px}
a{text-decoration:none}
a:hover{color:#FF0000}
table{border-collapse:collapse;border:1px solid #777}
td{border:1px solid #777;padding:2px;font-size:8pt}
th{border:1px solid #777;padding:2px;font-size:8pt;font-weight:bold}
/*]]>*/
		</style>
		<title>Arcade Spare Parts Admin</title>
		<script type="text/javascript" src="//code.jquery.com/jquery-1.4.2.min.js"></script>
	</head>
	<body>

<script type="text/javascript">
//<![CDATA[
function searchArray(needle, haystack)
{
	if (needle == null || haystack == null) return null;
	for (var i = 0; i < haystack.length; i++)
	if (needle == haystack[i]) return i;
	return null;
}
function updateSelect(elem)
{
	if (elem) elem.style.backgroundColor = (elem.value == "y") ? "#00FF00" : "#FF0000";
}
//]]>
</script>

<div class="invoice_content">
	<div align="center">
		<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
