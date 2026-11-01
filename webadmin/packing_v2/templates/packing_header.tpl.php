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
<script type="text/javascript" src="/webadmin/jscript/common.php.js" defer="defer"></script>
<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
</head>
<body>
<?php
}

?>
<link rel="stylesheet" type="text/css" href="style.css" />

<div class="packing_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Packing</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;">
			</td>
		</tr>
	</table>

	<div align="center">
		<br />
