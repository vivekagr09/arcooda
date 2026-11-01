<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include '../header.inc.php';
include '../currency.php';
include 'forward_menu.php';
?>
<table width="70%" cellspacing="0" cellpadding="3" align="center">
	<tr bgcolor="#CCCCCC">
		<td>ID</td>
		<td>Company</td>
		<td>Country</td>
		<td>&nbsp;</td>
	</tr>
<?php
$country_list = getCountryList(false);

if ($data = $DB->query(sprintf(
    "SELECT `f`.`fow_id`, `f`.`fow_company` FROM `fowarder` AS `f`"
))) {
    $alt = false;
    while ($row = $DB->fetch_assoc($data)) {
?>
    <tr<?= ($alt = !$alt) ? ' bgcolor="#FFFFCC"' : '' ?>>
        <td><?= htmlspecialchars($row['fow_id']) ?></td>
        <td><?= htmlspecialchars($row['fow_company']) ?></td>
        <td><?= htmlspecialchars($country_list[$row['fow_country']] ?? $row['fow_country']) ?></td>
        <td width="90">
            <a href="edit_forward.php?id=<?= htmlspecialchars($row['fow_id']) ?>">Edit</a> /
            <a href="del_forwarder.php?id=<?= htmlspecialchars($row['fow_id']) ?>">Delete</a>
        </td>
    </tr>
<?php
    }
    $DB->free_result($data);
}
?>
</table>
<?php include '../footer.inc.php';?>