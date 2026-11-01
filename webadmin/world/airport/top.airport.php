<?php
include_once __DIR__ . '/common.inc.php';

$row = $DB->query_first(sprintf(
	"SELECT COUNT(*) AS `total` FROM `world`.`airports`"
));
?>
<table width="100%" border="0" cellspacing="1" cellpadding="5">
	<tr>
		<td bgcolor="#EEEEEE" width="20%">
			<div align="center"><b>Airports</b></div>
		</td>
		<td bgcolor="#EEEEEE" width="60%">
			<div align="right">
				<a href="/webadmin/world/airport/airports.php?action=add">Add New Airport</a> |
				<a href="/webadmin/world/airport/airports.php?action=view">View All Airports (<?=$row['total']?>)</a>
			</div>
		</td>
	</tr>
</table>
