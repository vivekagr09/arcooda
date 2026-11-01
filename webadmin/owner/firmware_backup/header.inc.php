<?php
include_once __DIR__ . "/common.inc.php";

global $DB;

include_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/header.inc.php";

$items = $DB->query_first("SELECT COUNT(*) AS `total` FROM `firmware`");
?>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%" style="text-align:center"><b>Firmware</b></td>
		<td class="pageMenu" style="text-align:right;" width="80%">
			<a href=".?action=add">Add New Firmware</a> |
			<a href=".?action=list">List All Firmware (<?= $items['total']?>)</a>
		</td>
	</tr>
</table>
<div class="meta_content" style="width:100%;">
<br />

