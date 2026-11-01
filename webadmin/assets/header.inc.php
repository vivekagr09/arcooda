<?php
/**
 * @var string $section_plural
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

$popup_window = (isset($_GET['return_message'])) ? true : false;

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
?>
<html>
<head>
<style type="text/css">
body,p,td{font-family:Arial;font-size:12px}
a:hover{color:red}
</style>
</head>
<body>
<?php
}

$get_vars = $_GET;

$staff_id = $_GLOBALS['staff']['id'];
?>
<link rel="stylesheet" type="text/css" href="/webadmin/assets/style.css" />

<div class="assets_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background:#eee;"><?=$section_plural?></td>
			<td style="text-align:right;width:80%;background:#eee;">
			</td>
		</tr>
	</table>

	<div align="center">
		<br />