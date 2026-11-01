<?php
include_once __DIR__ . '/common.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

$row = $DB->query_first('SELECT COUNT(*) AS `total` FROM `product_series`', MYSQLI_ASSOC);
?>

<br />

<table width="100%" border="0" cellspaing="2" cellpadding="2"><tbody><tr>
    <td bgcolor="#EEEEEE" width="20%"><div align="center"><b>Product Series</b></div></td>
    <td bgcolor="#EEEEEE" width="80%"><div align="right">
        <a href="/webadmin/product_series/addedit.php?action=do_add">Add New</a>
        |
        <a href="/webadmin/product_series/">List (<?= $row['total'] ?>)</a>
    </div></td>
</tr></tbody></table>
