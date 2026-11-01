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

//$row=$DB->query_first("SELECT COUNT(*) AS total FROM workmanuals");
?>
<link rel="stylesheet" type="text/css" href="/webadmin/permissions/style.css" />

<script type="text/javascript" src="permissions_funcs.js.php"></script>

<?php
//if(!in_array('OPTIONS',$allowed_sections)) die('Access Denied');
?>

<div class="permissions_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Site Permissions</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;"><!--
				<a href="index.php?action=add" title="Click to add a new notification" target="_blank">Add New Notification</a> |
				<a href="index.php?action=list" title="Click to list all notifications">List Notifications(<?php echo $row['total'] ?>)</a>
			--></td>
		</tr>
	</table>

	<div align="center">
		<br />