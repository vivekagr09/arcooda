<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleOverride('forwarder', __DIR__ . '/functions/query_forwarders.inc.php');
moduleLoad('forwarder');

moduleLoad('member');

if (!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
    $posted['site_id'] = $_GLOBALS['site_settings']['site_id'];
}

$all_staff_list = array();
$query = $DB->query('SELECT `id`, `first_name`, `last_name` FROM `highwaygroup`.`users`');
while ($row = $DB->fetch_assoc($query)) {
    $all_staff_list[$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
}

$DB->free_result($query);

$staff_id = $_GLOBALS['staff']['id'];
