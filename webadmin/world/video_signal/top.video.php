<?php
include_once __DIR__ . '/common.inc.php';

$qry     = "SELECT COUNT(*) AS `total` FROM `world`.`video_signal`";
$section = $DB->query_first($qry);
?>
<table width="100%" border="0" cellspacing="1" cellpadding="5"><tr>
	<td bgcolor="#EEEEEE" width="20%"><div align="center"><b>Video Signals</b></div></td>
	<td bgcolor="#EEEEEE" width="60%"><div align="right">
		<a href="/webadmin/world/video_signal/video.php?action=add">Add New Video Signal</a> |
		<a href="/webadmin/world/video_signal/video.php?action=view">View All Video Signals (<?=$section['total']?>)</a>
	</div></td>
</tr></table>
