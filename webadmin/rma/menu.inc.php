<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>RMA Service</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/rma/rma_view_all.php?action=add" target="_blank">Add RMA</a></li>
<?php
		$temp = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `rma_service` WHERE `status` = 'open'"
        ), MYSQLI_ASSOC);
?>
		<li><a href="/webadmin/rma/rma_view_open.php?table=rma_service" target="_blank">Open RMA(<?= $temp['total'] ?>)</a></li>
<?php
		$temp = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `rma_service`"
        ), MYSQLI_ASSOC);
?>
		<li><a href="/webadmin/rma/rma_view_all.php?table=rma_service" target="_blank">View All (<?= $temp['total'] ?>)</a></li>
	</ul></td></tr>
</table>
