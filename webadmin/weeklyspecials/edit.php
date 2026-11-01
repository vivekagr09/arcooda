<?php
include_once __DIR__ . '/common.inc.php';
include_once __DIR__ . '/templates/header.tpl.php';

$action   = 'edit';
$id       = intval($posted['id']);
$staff_id = $_GLOBALS['staff']['id'];

$weeklyspecial          = $section_handler->fromDatabase($id);
$weeklyspecial_lock     = $section_handler->getLock($id);
$weeklyspecial_comments = $section_handler->commentSearch(array('weeklyspecial_id' => $id, 'comment_type' => 'comment'));

if (count($weeklyspecial_comments) > 5) {
    $weeklyspecial_comments = reset(array_chunk($weeklyspecial_comments, 5));
}

?>
<h2>Edit Weekly Special <?=$section_handler->formatNumber($id)?> - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/templates/addedit.tpl.php';

include_once __DIR__ . '/templates/footer.tpl.php';
