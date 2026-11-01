<?php
$_GLOBALS["load_ext_options"] = false;

include_once($_SERVER["DOCUMENT_ROOT"] . "/webadmin/common.inc.php");
global $_GLOBALS;
moduleLoad('staff');
$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);
if (!has_permission('COUPON', $allowed_sections)) {
    redirect('/webadmin');
}
moduleOverride("gift_coupon", dirname(__FILE__) . "/query.inc.php");

moduleLoad("gift_coupon");
?>
