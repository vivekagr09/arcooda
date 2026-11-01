<?php
$popup_window = (isset($_GET['return_message'])) ? true : false;

if (!$popup_window) {
    include_once 'webadmin_header.tpl.php';
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
</head>
<body>
<?php
}

$projects = projectSearch(array('site_id' => $posted['site_id']));
?>
<link rel="stylesheet" type="text/css" href="/webadmin/project/style.css" />

<script type="text/javascript" src="project_funcs.js.php"></script>

<div class="project_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Projects</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;">
				<a href="index.php?action=add" title="Click to add a new project" target="_blank">Add New Project</a> |
				<a href="index.php?action=list" title="Click to list all projects">List Projects(<?php echo count($projects); ?>)</a>
			</td>
		</tr>
	</table>

	<div align="center">
		<br />