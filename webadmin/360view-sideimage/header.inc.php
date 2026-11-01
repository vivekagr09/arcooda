<?php
include_once __DIR__ . '/common.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

$row = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM %s",
    "`360_side_images`"
), MYSQLI_ASSOC);
?>

<br />

<table width="100%" border="0" cellspaing="2" cellpadding="2"><tbody><tr>
    <td bgcolor="#EEEEEE" width="20%"><div align="center"><b>360 View side images</b></div></td>
    <td bgcolor="#EEEEEE" width="80%"><div align="right">
        <a href="/webadmin/360view-sideimage/addedit.php?action=do_add">Add New Side images</a>
        |
        <a href="/webadmin/360view-sideimage/">List Side images (<?= $row['total'] ?>)</a>
    </div></td>
</tr></tbody></table>
