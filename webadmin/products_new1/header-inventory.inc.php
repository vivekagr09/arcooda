<?php
include_once __DIR__ . '/common.inc.php';

$popup_window = (isset($_GET['return_message'])) ? true : false;
if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
?>
<html>
    <head>
        <style type="text/css">
/*<![CDATA[*/
body,p,td {font-family:Arial;}
a:hover {color:#FF0000}
/*]]>*/
        </style>
        <script type="text/javascript" src="/webadmin/jscript/common.php.js"></script>
        <script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
    </head>
    <body>
<?php
}

?>
<script type="text/javascript" src="common.js"></script>

<div class="product_content">
    <table cellspacing="2" cellpadding="2" style="width:100%;">
        <tr>
            <td style="text-align:center;font-weight:bold;width:20%;background:#eeeeee;"> Inventory</td>
            <td style="text-align:right;width:80%;background:#eeeeee;">
                <a href="/webadmin/products_new/?action=add_inventory">Add To Inventory</a> |
                <a href="/webadmin/products_new/?action=list_inventory">List All Inventory</a>
            </td>
        </tr>
    </table>

    <div>
        <br />
