<?php
global $_GLOBALS;

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] ."/webadmin/products_new/query.inc.php";
moduleOverride("member", __DIR__ . "/query.inc.php");
moduleLoad('member');

moduleLoad("invoice");
moduleLoad("freight");

if (3 != $_GLOBALS['site_settings']['site_id']) {
    $posted['site_id'] = $_GLOBALS['site_settings']['site_id'];
}

$websites_list = array(39 => 'Arcooda');
