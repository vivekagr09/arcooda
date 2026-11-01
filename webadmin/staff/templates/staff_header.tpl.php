<?php
global $DB;

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

$row = $DB->query_first("SELECT COUNT(*) AS total FROM `highwaygroup`.`users` WHERE active='y'");
?>
<link rel="stylesheet" type="text/css" href="/webadmin/staff/style.css" />

<script type="text/javascript" src="staff_funcs.js.php"></script>

<div class="staff_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="width:30%;background:#eee;text-align:center"><b><?=htmlspecialchars(_('Staff Members'))?></b></td>
			<td style="width:70%;background:#eee;text-align:right">
<?php
if (has_permission('USERS', $allowed_sections)) {
?>
				<a href=".?action=add" title="Click to add a new report">Add New Staff</a> |
<?php
}
?>
				<a href=".?action=list" title="Click to list all your reports">List Active Staff (<?=$row['total']?>)</a>
			</td>
		</tr>
	</table>

	<div style="text-align:center">
		<br />
