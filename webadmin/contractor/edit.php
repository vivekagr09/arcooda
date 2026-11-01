<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action    = 'edit';
$contractor_id = $id = intval($posted['contractor_id']);
$staff_id  = $_GLOBALS['staff']['id'];

$contractor               = contractorFromDatabase($contractor_id);
$contractor_lock          = contractorGetLock($contractor_id);
$contractor_comments      = contractorCommentSearch(array('contractor_id' => $contractor_id, 'comment_type' => 'admin'));
$contractor_contacts      = contractorContactsFromDatabase($contractor_id);
$contractor_history       = contractorCommentSearch(array('contractor_id' => $contractor_id, 'comment_type' => 'email'));
$contractor_requests      = contractorCommentSearch(array('contractor_id' => $contractor_id, 'comment_type' => 'warning'));


if (count($contractor_comments) > 5) {
    $contractor_comments = reset(array_chunk($contractor_comments, 5));
}

?>
<h2>Edit contractor <?=contractorFormatNumber($contractor_id)?> - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';
