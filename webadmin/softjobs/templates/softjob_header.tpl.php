<?php
global $_GLOBALS;

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

$row = $DB->query_first(sprintf(
	"SELECT COUNT(*) AS `total` FROM `highwaygroup`.`bugs` WHERE `status` <> 3 AND `website` = %d",
	$_GLOBALS['site_settings']['site_id']
), MYSQLI_ASSOC);
?>
<link rel="stylesheet" type="text/css" href="style.css" />

<script type="text/javascript" src="softjob_funcs.js.php"></script>

<iframe style="width:168px;height:202px;visibility:hidden;z-index:65535;position:absolute;top:-202px;border:2px ridge;" name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="/webadmin/calendar/ipopeng.htm" scrolling="no" frameborder="0"></iframe>

<div class="softjob_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Jobs</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;">
				<a href="index.php?action=add" title="Click to add a new softjob" target="_blank">Add New Job</a> |
				<a href="index.php?action=list&status=1|2" title="Click to list all softjobs">List Jobs(<?= $row['total'] ?>)</a>
			</td>
		</tr>
	</table>

	<div align="center">
		<br />