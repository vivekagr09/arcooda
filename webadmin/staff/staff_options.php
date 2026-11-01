<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

include_once __DIR__ . '/templates/staff_header.tpl.php';

$action   = 'options';
$staff_id = intval($posted['staff_id']);
if ($staff_id <= 0) {
    $staff_id = $_GLOBALS['staff']['id'];
}

$staff = staffFromDatabase($staff_id);

$dooptions_url           = $get_vars;
$dooptions_url['action'] = 'do_options';
$dooptions_url           = '.?' . http_build_query($dooptions_url);

?>
<h2>Staff Options - <?=ucwords($staff['first_name'] . ' ' . $staff['last_name'])?> - <?=date(_('d/m/Y'))?></h2>

<form method="post" action="<?=$dooptions_url?>">
<input type="hidden" name="staff_id" value="<?=$staff['staff_id']?>" />
<?php

include __DIR__ . '/templates/staff_options.tpl.php';

?>
<input type="button" value="Submit Changes" onclick="this.form.submit();" />

</form>
<?php

include_once __DIR__ . '/templates/staff_footer.tpl.php';
