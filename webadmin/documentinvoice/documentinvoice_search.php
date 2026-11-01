<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

//Add a search box
$site_id   = intval($posted['site_id']);
$status_id = $posted['status'];
$search    = $posted['search_for'];
include __DIR__ . '/templates/search_invoices.tpl.php';

include_once __DIR__ . '/footer.inc.php';
