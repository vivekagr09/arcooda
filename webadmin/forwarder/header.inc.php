<?php
include_once __DIR__ . '/common.inc.php';

register_shutdown_function(function ($a) {include_once $a;}, __DIR__ . '/footer.inc.php');

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
?>
<!DOCTYPE html>
<html>
	<head>
		<style type="text/css">
body, p, td { font-family:Arial; font-size:12px; }
a:hover { color:#f00; }
		</style>
	</head>
	<body>
<?php
}

?>
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="funcs.js"></script>

<div class="forwarder_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;"><tr>
		<td style="text-align:center;font-weight:bold;width:20%;background-color:#eee;">Forwarders</td>
		<td style="text-align:right;width:80%;background-color:#eee;">
			<a href=".?action=add">Add New Forwarder</a> |
			<a href=".?action=list">List All Forwarders (<?=count(forwarderSearch($_GLOBALS['criteria_options']['forwarder']))?>)</a>
		</td>
	</tr></table>
	<div style="text-align:center;">
		<br />