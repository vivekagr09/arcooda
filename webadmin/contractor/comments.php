<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action        = 'view';
$contractor_id = intval($posted['contractor_id']);
$staff_id      = $_GLOBALS['staff']['id'];

$contractor_comments = contractorCommentSearch(array('contractor_id' => $contractor_id, 'comment_type' => 'comment'));

?>
<h2>Contractor Comments <?=contractorFormatNumber($contractor_id)?> - <?=date('d/m/Y')?></h2>
<?php

include __DIR__ . '/comments.tpl.php';

include_once __DIR__ . '/footer.inc.php';
