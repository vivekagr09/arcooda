<?php
include_once __DIR__ . '/common.inc.php';
include_once __DIR__ . '/templates/header.tpl.php';

$action   = 'view';
$id       = intval($posted['id']);
$staff_id = $_GLOBALS['staff']['id'];

$weeklyspecial          = $section_handler->fromDatabase($id);
$weeklyspecial_comments = $section_handler->commentSearch(array('weeklyspecial_id' => $id, 'comment_type' => 'comment'));

?>
<h2>WeeklySpecial Comments <?=$section_handler->formatNumber($id)?> - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/templates/comments.tpl.php';

include_once __DIR__ . '/templates/footer.tpl.php';
