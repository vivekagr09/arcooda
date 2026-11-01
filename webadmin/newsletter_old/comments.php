<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action   = 'view';
$id       = intval($posted['id']);
$staff_id = $_GLOBALS['staff']['id'];

$item          = $section_handler->fromDatabase($id);
$item_comments = $section_handler->commentSearch(array($section_handler->section_name . '_id' => $id, 'comment_type' => 'comment'));

?>
<h2>Comments <?=$section_handler->formatNumber($id)?> - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/comments.tpl.php';

include_once __DIR__ . '/footer.inc.php';
