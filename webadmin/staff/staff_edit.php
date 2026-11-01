<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('staff');

include_once __DIR__ . '/templates/staff_header.tpl.php';

$action   = 'edit';
$staff_id = intval($posted['staff_id']);

$staff               = staffFromDatabase($staff_id);
$staff_history       = staffHistorySearch([
    'staff_id'        => $staff_id,
    'message_type_id' => [
        STAFF_HISTORY_TYPE_PASSWORD_CHANGE,
        STAFF_HISTORY_TYPE_SIGNATURE_CHANGE,
        STAFF_HISTORY_TYPE_PHOTO_CHANGE,
        STAFF_HISTORY_TYPE_DETAIL_CHANGE,
        STAFF_HISTORY_TYPE_TAXINFO_CHANGE,
    ],
]);
$staff_admin_notes   = staffHistorySearch([
    'staff_id'        => $staff_id,
    'message_type_id' => [
        STAFF_HISTORY_TYPE_ADMIN_NOTE,
        STAFF_HISTORY_TYPE_MYSQL_CHANGE,
    ],
]);
$staff_super_funds   = staffSuperSearch(array('staff_id' => $staff_id));
$staff_bank_accounts = staffBankSearch(array('staff_id' => $staff_id));
$staff_sites         = staffWebsiteSearch($staff_id);

$staff['history_count'] = count($staff_history);
$staff_history          = array_chunk($staff_history, 5);
$staff_history          = $staff_history[0];

$doedit_url           = $get_vars;
$doedit_url['action'] = 'do_edit';
$doedit_url           = '.?' . http_build_query($doedit_url);

?>
<h2>Edit Staff - <?=ucwords($staff['first_name'] . ' ' . $staff['last_name'])?> - <?=date(_('d/m/Y'))?></h2>
<?php
include __DIR__ . '/templates/addedit_staff.tpl.php';

include_once __DIR__ . '/templates/staff_footer.tpl.php';
