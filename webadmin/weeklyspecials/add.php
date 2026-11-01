<?php
include_once __DIR__ . '/common.inc.php';
include_once __DIR__ . '/templates/header.tpl.php';

$action   = 'add';
$id       = 0;
$staff_id = $_GLOBALS['staff']['id'];

$weeklyspecial          = array();
$weeklyspecial_lock     = false;
$weeklyspecial_comments = array();

?>
<h2>Add New Weekly Special - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/templates/addedit.tpl.php';

include_once __DIR__ . '/templates/footer.tpl.php';
