<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'list';

$storage_list    = productGetOptionList('storage');

$linked_id  = array_key_exists('linked_id', $_REQUEST) ? intval($_REQUEST['linked_id']) : 0;
$order      = array_key_exists('order', $_REQUEST) ? $_REQUEST['order'] : 'desc';
$page_id    = array_key_exists('page_id', $_REQUEST) ? intval($_REQUEST['page_id']) : 0;
$order_by   = array_key_exists('order_by', $_REQUEST) ? strtolower($_REQUEST['order_by']) : 'id';
$search_for = array_key_exists('search_for', $_REQUEST) ? $_REQUEST['search_for'] : '';
$custom_template = array_key_exists('custom_template', $_REQUEST) ? $_REQUEST['custom_template'] : '';

if ('asc' != $order) {
    $order = 'desc';
}

$criteria = array();
if (array_key_exists('search_for', $_REQUEST)) {
    foreach (array('search_for') as $k) {
        if (array_key_exists($k, $_REQUEST) && !empty($_REQUEST[$k])) {
            $criteria['any_text'] = (is_array($_REQUEST[$k])) ? $_REQUEST[$k] : preg_split('/[^a-zA-Z0-9_]+/', $_REQUEST[$k]);
        }
    }
}
foreach (productGetOptionList('all_fields') as $k => $v) {
    if (array_key_exists($k, $_REQUEST) && !empty($_REQUEST[$k])) {
        $criteria[$k] = (is_array($_REQUEST[$k])) ? $_REQUEST[$k] : preg_split('/[^a-zA-Z0-9_]+/', $_REQUEST[$k]);
    }
}

foreach (productGetOptionList('joined_fields') as $join => $columns) {
    foreach ($columns as $k => $v) {
        if (array_key_exists($k, $_REQUEST) && !empty($_REQUEST[$k])) {
            $criteria[$k] = (is_array($_REQUEST[$k])) ? $_REQUEST[$k] : preg_split('/[^a-zA-Z0-9_]+/', $_REQUEST[$k]);
        }
    }
}
if (array_key_exists('custom_template', $_REQUEST)) {
        $criteria['terms_condition_custom'] =  preg_split('/[^0-9]+/', $_REQUEST['custom_template']);
}
include __DIR__ . '/list.tpl.php';

include_once __DIR__ . '/footer.inc.php';
