<?php
include_once __DIR__ . '/common.inc.php';

$qry     = "SELECT COUNT(*) AS `total` FROM `world`.`time_zones`";
$section = $DB->query_first($qry);
?>
<table width="100%" border="0" cellspacing="1" cellpadding="5"><tr>
	<td bgcolor="#EEEEEE" width="20%"><div align="center"><b>Time Zones</b></div></td>
	<td bgcolor="#EEEEEE" width="60%"><div align="right">
		<a href="/webadmin/world/time/times.php?action=add">Add New Time Zone</a> |
		<a href="/webadmin/world/time/times.php?action=view">View All Time Zones (<?=$section['total']?>)</a> |
		<a href="/webadmin/world/time/times.php?action=update">Update Time Zones (<?=$section['total']?>)</a>
	</div></td>
</tr></table>
