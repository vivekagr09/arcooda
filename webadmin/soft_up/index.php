<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

include_once '../header.inc.php';
include_once 'header.php';
?>
<br />
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSoftwareUpdates\');">Software Updates</a>')?>

<div id="sectionSoftwareUpdates">
	<table class="wide spacetable" align="center">
		<tr>
			<th>Date</th>
			<th>Staff</th>
			<th>Title</th>
			<th>Comments</th>
			<th>&nbsp;</th>
		</tr>
<?php
$update_list = array();
if ($data = $DB->query(sprintf(
    "SELECT *, UNIX_TIMESTAMP(`up_date`) AS `up_time` FROM `software_updates` ORDER BY `up_id` DESC"
))) {
    while ($row = $DB->fetch_assoc($data)) {
        $update_list[] = $row;
    }
    $DB->free_result($data);
}

$spacer = false;

$alt = false;
foreach ($update_list as $row) {
    if ($_GLOBALS['staff']['id'] == $row['up_staff']) {
        $EditLink = ' / <a href="edit.php?id=' . $row['up_id'] . '">Edit</a>';
    } else {
        $EditLink = '';
    }

    $row2 = $DB->query_first(sprintf(
		"SELECT COUNT(*) FROM `software_comments` WHERE `up_id` = %d LIMIT 1",
		$row['up_id']
	), MYSQLI_ASSOC);

    if (!$spacer && (time() - $row['up_time']) > 60 * 60 * 24 * 14) {
        $spacer = true;
?>
		<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>><td colspan="5"><hr /></td></tr>
<?php
}
?>
		<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
			<td style="text-align:center;white-space:nowrap;">&nbsp;<?=date("d-m-Y", $row['up_time'])?></td>
			<td style="text-align:center;white-space:nowrap;"><?=$row['up_staff']?></td>
			<td style="text-align:center;white-space:nowrap;width:100%;"><?=$row['up_title']?></td>
			<td style="text-align:center;white-space:nowrap;"><?=$row2[0]?></td>
			<td style="text-align:center;white-space:nowrap;"><a href="view.php?id=<?=$row['up_id']?>">View</a><?=$EditLink?></td>
		</tr>
<?php
}
?>
	</table>
</div>
<?php
include_once 'footer.php';
include_once '../footer.inc.php';
?>