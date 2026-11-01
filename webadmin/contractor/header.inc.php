<?php
include_once __DIR__ . '/common.inc.php';
$popup_window = (isset($_GET['return_message'])) ? true : false;

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>

        <link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
        <link rel="stylesheet" href="/webadmin/css/favicons.css" type="text/css" />
        <link rel="stylesheet" href="style.css" type="text/css" />

        <script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
    </head>
    <body>
<?php
}
?>

<script type="text/javascript" src="/webadmin/jscript/common.php.js"></script>
<link type="text/css" rel="stylesheet" href="style.css" />
<iframe style="width:168px;height:202px;visibility:hidden;z-index:65535;position:absolute;top:-202px;border:2px ridge;" name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="/webadmin/calendar/ipopeng.htm" scrolling="no" frameborder="0"></iframe>

<div class="contractor_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Contractor</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;"><a href=".?action=add">Add New Contractor</a> | <a href=".?action=search">Search Contractor</a> | <a href=".?action=list">List All Contractor</a></td>
		</tr>
	</table>
	<br /><br />
