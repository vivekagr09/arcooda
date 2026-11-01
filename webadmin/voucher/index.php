<?php
include_once 'voucher_common.inc.php';

$action   = $posted['action'];
$staff_id = $_GLOBALS['staff']['staff_id'];
if ('' == $staff_id) {
    $_GLOBALS['staff']['staff_id'] = $staff_id = staffFromUser($_SERVER['REMOTE_USER']);
}

$voucher_id = intval($posted['voucher_id']);

//Update Actions
switch ($action) {
    case 'do_preset':
        $preset              = voucherPresetFromPost($posted);
        $posted['preset_id'] = $preset_id = voucherPresetToDatabase($preset);
        $action              = 'preset_edit';
        break;
    case 'do_add':
        unset($posted['voucher_id']);
    case 'do_edit':
        $voucher              = voucherFromPost($posted);
        $posted['voucher_id'] = $voucher_id = voucherToDatabase($voucher);
        $action               = 'edit';
        break;
    case 'lock':
        voucherSetLock($voucher_id, $staff_id);
        $action = 'list';
        break;
    case 'unlock':
        voucherSetLock($voucher_id);
        $action = 'list';
        break;
}

$get_vars = $posted;
//print_r($get_vars);
$keep_only = array('voucher_id', 'preset_id', 'search_for', 'order_by', 'order', 'page', 'return_message', 'voucher_list', 'member_id');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || !in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

switch ($action) {
    case 'add':
        header('Location: voucher_add.php?' . http_build_query($get_vars));
        break;
    case 'edit':
        $voucher_lock = voucherGetLock($voucher_id);
        if (false === $voucher_lock || $voucher_lock['staff_id'] == $staff_id) {
            voucherSetLock($voucher_id, $staff_id);
        }

        header('Location: voucher_edit.php?' . http_build_query($get_vars));
        break;
    case 'preset':
        header('Location: voucher_preset_list.php?' . http_build_query($get_vars));
        break;
    case 'preset_add':
        header('Location: voucher_preset.php?' . http_build_query($get_vars));
        break;
    case 'preset_edit':
        header('Location: voucher_preset.php?' . http_build_query($get_vars));
        break;
    case 'search':
        header('Location: voucher_search.php?' . http_build_query($get_vars));
        break;
    case 'select':
        header('Location: voucher_select.php?' . http_build_query($get_vars));
        break;
    case 'list':
    default:
        unset($get_vars['voucher_id']);
        header('Location: voucher_list.php?' . http_build_query($get_vars));
}

die();
