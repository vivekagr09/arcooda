<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('staff');

include_once __DIR__ . '/templates/staff_header.tpl.php';

$action   = 'history';
$staff_id = intval($posted['staff_id']);

$staff         = staffFromDatabase($staff_id);
$staff_history = staffHistorySearch([
    'staff_id'        => $staff_id,
    'message_type_id' => [
        STAFF_HISTORY_TYPE_PASSWORD_CHANGE,
        STAFF_HISTORY_TYPE_SIGNATURE_CHANGE,
        STAFF_HISTORY_TYPE_PHOTO_CHANGE,
        STAFF_HISTORY_TYPE_DETAIL_CHANGE,
        STAFF_HISTORY_TYPE_TAXINFO_CHANGE,
    ],
]);

$staff['history_count'] = count($staff_history);

include __DIR__ . '/templates/staff_history.tpl.php';

include_once __DIR__ . '/templates/staff_footer.tpl.php';
