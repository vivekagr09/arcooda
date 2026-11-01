<?php
include_once('common.inc.php');
include_once('templates/header.tpl.php');

$search_for=$posted['search_for'];









?>
<h2>Search Weekly Specials - <?= date('d/m/Y',time()) ?></h2>
<?php

include 'templates/search.tpl.php';

include_once 'templates/footer.tpl.php';
?>