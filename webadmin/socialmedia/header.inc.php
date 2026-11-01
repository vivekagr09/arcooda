<?php
include_once __DIR__ . '/common.inc.php';

register_shutdown_function(function($a) {
    include_once $a;
}, __DIR__ . '/footer.inc.php');

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

?>
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="script.js"></script>

<table cellspacing="2" cellpadding="2" style="width:100%;"><tr>
    <td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Media Partners</td>
    <td style="text-align:right;width:80%;background-color:#eeeeee;">
        <a href=".?action=add">Add New Media Partner</a> |  
        <a href=".?action=list">List Media Partners</a> 
    </td>
</tr></table>

<div class="socialmedia_content">
