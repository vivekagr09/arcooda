<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';
global $_GLOBALS;
moduleLoad('staff');
$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);
if (!has_permission('SUP', $allowed_sections)) {
    redirect('/webadmin');
}
moduleOverride('distributor', __DIR__ . '/query.inc.php');
moduleLoad('distributor');
moduleLoad('product'); 
