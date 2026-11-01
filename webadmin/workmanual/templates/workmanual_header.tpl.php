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

$row=$DB->query_first("SELECT COUNT(*) AS `total` FROM `highwaygroup`.`workmanuals`");
?>
<link rel="stylesheet" type="text/css" href="/webadmin/workmanual/style.css" />

<script type="text/javascript" src="workmanual_funcs.js.php"></script>

<iframe style="width:168px;height:202px;visibility:hidden;z-index:65535;position:absolute;top:-202px;border:2px ridge;" name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="/webadmin/calendar/ipopeng.htm" scrolling="no" frameborder="0"></iframe>

<?php
if(!has_permission('WORK_MAN',$allowed_sections)) die('Access Denied');
?>

<div class="workmanual_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Work Manuals</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;">
<?php
if(has_permission('WORK_MAN_W',$allowed_sections))
{
?>
				<a href="index.php?action=add" title="Click to add a new workmanual" target="_blank">Add New Work Manual</a> |
<?php
}
?>
				<a href="index.php?action=list" title="Click to list all workmanuals">List Work Manuals(<?= $row['total'] ?>)</a>
			</td>
		</tr>
	</table>

	<div align="center">
		<br />