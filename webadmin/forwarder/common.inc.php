<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleOverride('forwarder', __DIR__ . '/query.inc.php');
moduleLoad('forwarder');

if (!array_key_exists('forwarder', $_GLOBAL['criteria_options'])) {
    $_GLOBALS['criteria_options']['forwarder'] = array(
        'active'  => 'y',
        'site_id' => $_GLOBALS['site_settings']['site_id'],
    );
}
