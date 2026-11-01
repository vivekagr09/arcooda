<?php
/**
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/templates/staff_header.tpl.php';

$action   = 'add';
$staff_id = 0;

$staff               = array('start_time' => time());
$staff_history       = array();
$staff_admin_notes   = array();
$staff_super_funds   = array();
$staff_bank_accounts = array();
$staff_sites         = array();
$staff_emails        = array();

$staff['history_count'] = 0;

$doadd_url           = $get_vars;
$doadd_url['action'] = 'do_add';
unset($doadd_url['staff_id']);
$doadd_url = '.?' . http_build_query($doadd_url);

?>
<h2>Add New Staff - <?=date(_('d/m/Y'))?></h2>
<?php
include __DIR__ . '/templates/addedit_staff.tpl.php';

include_once __DIR__ . '/templates/staff_footer.tpl.php';
