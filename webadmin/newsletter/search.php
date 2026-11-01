<?php
include_once('common.inc.php');
include_once('templates/header.tpl.php');

$search_for=$posted['search_for'];









?>
<h2>Search Weekly Specials - <?php echo date('d/m/Y',$_SERVER['REQUEST_TIME']) ?></h2>
<?php

include 'templates/search.tpl.php';

include_once 'templates/footer.tpl.php';
?>