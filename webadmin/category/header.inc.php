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
    <h3 class="col-md-2">Categories</h3>
    <div class="col-md-10">
        <a href=".?action=add">Add New Category</a> |  
        <a href=".?action=list">List Categories</a> 
            </div>
</div>

<div class="category_content">
