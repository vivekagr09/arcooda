<?php
$row = $DB->query_first("SELECT COUNT(*) AS `total` FROM `world`.`seaports`");
?>
<table width="100%" border="0" cellspacing="1" cellpadding="5"><tr>
	<td bgcolor="#EEEEEE" width="20%"><div align="center"><b>Seaports</b></div></td>
	<td bgcolor="#EEEEEE" width="60%"><div align="right">
		<a href="/webadmin/world/seaport/seaports.php?action=add">Add New Seaport</a> |
		<a href="/webadmin/world/seaport/seaports.php?action=view">View All Seaports (<?php echo $row['total'] ?>)</a>
	</div></td>
</tr></table>
