<?php
return;

include_once __DIR__ . '/suppliers_common.php';
include_once __DIR__ . '/templates/suppliers_header.tpl.php';

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

//Add a search box
$site_id    = intval($posted['site_id']);
$status_id  = $posted['status'];
$search_for = $posted['search_for'];
include __DIR__ . '/templates/search_suppliers.tpl.php';

include_once __DIR__ . '/templates/suppliers_footer.tpl.php';
