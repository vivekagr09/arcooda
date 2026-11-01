<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title></title>
		<style type="text/css">
/*<![CDATA[*/
body,p,td{font-family:Arial;font-size:12px}
a:hover{color:#f00}
/*]]>*/
		</style>
		<script type="text/javascript" src="//code.jquery.com/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="/phpjs/phpjs.js"></script>
		<script type="text/javascript" src="/webadmin/jscript/common.js"></script>
		<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
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
		<link rel="stylesheet" media="screen" type="text/css" href="/webadmin/invoice/style.css" />
		<link rel="stylesheet" media="print" type="text/css" href="/webadmin/invoice/print.css" />
	</head>
<?php
flush();
?>
	<body>
		<div class="invoice_content" style="padding:16px;margin:0;width:auto;">

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
