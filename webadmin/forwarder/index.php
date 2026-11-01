<?php
include_once __DIR__ . '/common.inc.php';

$action = $_REQUEST['action'];
unset($_REQUEST['action']);

switch ($action) {
    case 'do_add':
        unset($_REQUEST['id']);
    case 'do_edit':
        if (!array_key_exists('item', $_REQUEST)) {
            break;
        }

        $item       = forwarderFromPost($_REQUEST['item']);
        $item['id'] = forwarderToDatabase($item);
        if (array_key_exists('item_warehouses_affected', $_REQUEST)) {
            $item_warehouses = forwarderWarehousesFromPost($_REQUEST['item_warehouses']);
            foreach ($item_warehouses as $k => $v) {
                $item_warehouses[$k]['forwarder_id'] = $item['id'];
            }
            forwarderWarehousesToDatabase($item['id'], $item_warehouses);
        }
        if (array_key_exists('item_contacts_affected', $_REQUEST)) {
            $item_contacts = forwarderContactsFromPost($_REQUEST['item_contacts']);
            foreach ($item_contacts as $k => $v) {
                $item_contacts[$k]['forwarder_id'] = $item['id'];
            }
            forwarderContactsToDatabase($item['id'], $item_contacts);
        }
        if (array_key_exists('item_accounts_affected', $_REQUEST)) {
            $item_accounts = forwarderAccountsFromPost($_REQUEST['item_accounts']);
            foreach ($item_accounts as $k => $v) {
                $item_accounts[$k]['forwarder_id'] = $item['id'];
            }
            forwarderAccountsToDatabase($item['id'], $item_accounts);
        }
        if (array_key_exists('comment', $_REQUEST) && '' !== $_REQUEST['comment']) {
            forwarderCommentToDatabase($item['id'], array(
                'staff'   => $_GLOBALS['staff']['user'],
                'comment' => $_REQUEST['comment'],
            ));
        }

        $_REQUEST = array('id' => $item['id']);
        $action   = 'edit';
        break;
    case 'do_delete':
        forwarderDelete($_REQUEST['id']);
        unset($_REQUEST['id']);
        $action = 'list';
        break;
    case 'lock':
        forwarderSetLock($_REQUEST['id'], $_GLOBALS['staff']['id']);
        unset($_REQUEST['id']);
        $action = 'list';
        break;
    case 'unlock':
        forwarderSetLock($_REQUEST['id']);
        unset($_REQUEST['id']);
        $action = 'list';
        break;
}

switch ($action) {
    case 'delete':
    case 'history':
    case 'quote_add':
    case 'quote_view':
    case 'search':
    case 'select':
    case 'summary':
    case 'view':
        redirect($action . '.php?' . http_build_query($_REQUEST));
        break;
    case 'add':
        unset($_REQUEST['id']);
        redirect('add.php?' . http_build_query($_REQUEST));
        break;
    case 'edit':
        $item_lock = forwarderGetLock($_REQUEST['id']);
        if (false === $item_lock || $item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            forwarderSetLock($_REQUEST['id'], $_GLOBALS['staff']['id']);
        }
        redirect('edit.php?' . http_build_query($_REQUEST));
        break;
    case 'list':
    default:
        redirect('list.php?' . http_build_query($_REQUEST));
        break;
}
die();
