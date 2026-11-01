<?php
global $_GLOBALS;

include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';
global $_GLOBALS;
moduleLoad('staff');
$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);
if (!has_permission('SUP', $allowed_sections)) {
    redirect('/webadmin');
}
moduleOverride("contractor", __DIR__ . "/query.inc.php");
moduleLoad('contractor');

if (11 != $_GLOBALS['site_settings']['site_id']) {
    $posted['site_id'] = $_GLOBALS['site_settings']['site_id'];
}

