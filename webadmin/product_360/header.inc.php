<?php
include_once __DIR__ . '/common.inc.php';

register_shutdown_function(function ($a) {
    include_once $a;
}, __DIR__ . '/footer.inc.php');

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

?>
<link rel="stylesheet" type="text/css" href="style.css" />
<div class="product_content">
    <table cellspacing="2" cellpadding="2" style="width:100%;">
        <tr>
            <td style="text-align:center;width:20%;background-color:#EEEEEE;"><b>360 Views</b></td>
            <td style="text-align:right;width:80%;background-color:#EEEEEE;">
                <a href="codefiles.zip">Download Code</a>
                <a href=".?action=add">Add New Product</a>
                | <a href=".?action=list">View 360 Products</a>
            </td>
        </tr>
    </table>
    <br />
