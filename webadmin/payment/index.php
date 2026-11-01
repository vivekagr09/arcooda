<?php
include_once __DIR__ . '/common.inc.php';

$action = $posted['action'];
$portal = $posted['portal'];

//Update Actions
switch ($action) {
    case 'do_add':
        unset($posted['payment_id']);
    case 'do_edit':
        $payment_id = $posted['payment_id'];
        $payment    = paymentFromPost($posted);
        $payment_id = paymentToDatabase($payment, $portal);
        $action     = 'list';
        break;
}

$get_vars  = $posted;
$keep_only = array('order_by', 'search_for', 'page_id', 'order', 'payment_id', 'Card', 'portal', 'merchant');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || !in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

//Redirect Actions
switch ($action) {
    case 'add_cc':
    case 'add':
        unset($get_vars['payment_id']);
        header('Location: https://www.highwaygroup.net/contact/?action=credit');
        break;
    case 'edit':
        header('Location: payment_edit.php?' . http_build_query($get_vars));
        break;
    case 'view':
        header('Location: payment_view.php?' . http_build_query($get_vars));
        break;
    case 'search':
        header('Location: payment_search.php?' . http_build_query($get_vars));
        break;
    default:
    case 'list_cc':
    case 'list':
        header('Location: payment_list.php?' . http_build_query($get_vars));
        break;
}

die();
