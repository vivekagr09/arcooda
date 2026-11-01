<?php
include_once __DIR__ . '/common.inc.php';

register_shutdown_function(function($a) {
    include_once $a;
}, __DIR__ . '/footer.inc.php');

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

?>
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="script.js"></script>

<div class="row">
    <div class="col-md-10" style="float:right">
        <a href=".?action=add">Add New</a> |  
        <a href=".?action=list">List</a> 
    </div>
    <h3 class="col-md-2">Product Series</h3>
</div>

<div class="series_content">
