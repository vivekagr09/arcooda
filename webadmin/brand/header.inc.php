<?php
include_once __DIR__ . '/common.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

$temp = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM %s",
    BRAND_TABLENAME
), MYSQLI_ASSOC);
?>

<br />

<table width="100%" border="0" cellspaing="2" cellpadding="2"><tbody><tr>
    <td bgcolor="#EEEEEE" width="20%"><div align="center"><b>Brand</b></div></td>
    <td bgcolor="#EEEEEE" width="80%"><div align="right">
        <a href="/webadmin/brand/addedit.php?action=do_add">Add New Brand</a>
        |
        <a href="/webadmin/brand/">List Brand (<?=$temp['total']?>)</a>
    </div></td>
</tr></tbody></table>
