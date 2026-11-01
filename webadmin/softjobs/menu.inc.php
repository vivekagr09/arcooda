<?php
global $_GLOBALS;
global $DB;

$temp1 = basename(__DIR__);

?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Software Jobs</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/<?= $temp1 ?>/?action=add">Add New Job/Error</a></li>
<?php
$temp2 = [];
if ($res = $DB->query(sprintf(
    "SELECT COUNT(*) AS `total`, `status` FROM `highwaygroup`.`bugs` WHERE `website` = %d GROUP BY `status`",
    $_GLOBALS['site_settings']['site_id']
))) {
    while ($row = $DB->fetch_assoc($res)) {
        $temp2[$row['status']] = $row['total'];
    }
    $DB->free_result($res);
}
?>
		<li><a href="/webadmin/<?= $temp1 ?>/?status=1">Open Jobs/Errors (<?=intval($temp2[1])?>)</a></li>
		<li><a href="/webadmin/<?= $temp1 ?>/?status=2">Fixing Jobs/Errors (<?=intval($temp2[2])?>)</a></li>
		<li><a href="/webadmin/<?= $temp1 ?>/?status=3">Closed Jobs/Errors (<?=intval($temp2[3])?>)</a><br /><br /></li>
<?php

if ($temp2 = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`bugs` WHERE `status` <> 3 AND `website` = %d AND `assigned` = %d",
    $_GLOBALS['site_settings']['site_id'],
    $_GLOBALS['staff']['id']
), MYSQLI_ASSOC) && $temp2['total'] > 0) {
?>
		<li><a href="/webadmin/<?= $temp1 ?>/?status=1|2&amp;assigned=<?=$_GLOBALS['staff']['id']?>">Assigned Jobs (<?=$temp2['total']?>)</a></li>
<?php
}

if ($temp2 = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`bugs` WHERE `status` <> 3 AND `website` = %d AND `reporter` = %d",
    $_GLOBALS['site_settings']['site_id'],
    $_GLOBALS['staff']['id']
), MYSQLI_ASSOC) && $temp2['total'] > 0) {
?>
		<li><a href="/webadmin/<?= $temp1 ?>/?status=1|2&amp;reporter=<?=$_GLOBALS['staff']['id']?>">Reported Jobs (<?=$temp2['total']?>)</a></li>
<?php
}
?>
	</ul></td></tr>
</table>
