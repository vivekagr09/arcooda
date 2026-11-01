<?php
include_once __DIR__ . '/common.inc.php';

$user             = get_user();
$allowed_sections = get_allowed_sections($user);
if (has_permission('CURRENCY', $allowed_sections)) {
    include 'backOrders.php';
}
