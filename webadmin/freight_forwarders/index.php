<?php
include_once __DIR__ . '/forwarders_common.inc.php';

global $_GLOBALS;

$action       = $posted['action'];
$staff_id     = $_GLOBALS['staff']['id'];
$forwarder_id = intval($posted['id']);

//Update Actions
switch ($action) {
    case 'do_add':
        unset($posted['id']);
    case 'do_edit':
        $forwarder = forwardersFromPost($posted['forwarder']);
        $old_forwarder = forwardersFromDatabase($forwarder['id']);
        $forwarder_exists = forwardersExists($forwarder['id']);

        $posted['id'] = forwardersToDatabase($forwarder);
        $forwarder_id = intval($posted['id']);

        if (is_array($posted['warehouse']) || $posted['warehouse_affected'] != '') {
            $forwarder_warehouses = forwardersWarehousesFromDatabase($forwarder_id);
            foreach (explode(',', $posted['warehouse_affected']) as $warehouse_id) {
                unset($forwarder_warehouses[$warehouse_id]);
            }

            foreach (forwardersWarehousesFromPost($forwarder_id, $posted['warehouse']) as $warehouse) {
                $forwarder_warehouses[] = $warehouse;
            }

            forwardersWarehousesToDatabase($forwarder_id, $forwarder_warehouses);
        }
        if (is_array($posted['contact']) || $posted['contacts_affected'] != '') {
            $forwarder_contacts = forwardersContactsFromDatabase($forwarder_id);
            foreach (explode(',', $posted['contacts_affected']) as $contact_id) {
                unset($forwarder_contacts[$contact_id]);
            }

            foreach (forwardersContactsFromPost($forwarder_id, $posted['contact']) as $contact) {
                $forwarder_contacts[] = $contact;
            }

            forwardersContactsToDatabase($forwarder_id, $forwarder_contacts);
        }
/*
if(is_array($posted['account']))
{
$forwarder_accounts=forwardersAccountsFromPost($posted['account'],$forwarder_id);
forwardersAccountsToDatabase($forwarder_id,$forwarder_accounts);
}
 */
/*
if($posted['forwarder_comment']!='')
{
$message=array('forwarder_id'=>$forwarder_id, 'message_sender'=>$staff_id, 'message_sentto'=>'Forwarders System', 'message_title'=>'Admin Note Added', 'message_notes'=>$posted['forwarder_comment']);
forwardersHistoryToDatabase($forwarder_id,$message);
}
 */
        $action = 'edit';
        break;
    case 'do_delete':
        forwardersDelete($forwarder_id);
        $action = 'list';
        break;
    case 'lock':
        forwardersSetLock($forwarder_id, $staff_id);
        $action = 'list';
        break;
    case 'unlock':
        forwardersSetLock($forwarder_id);
        $action = 'list';
        break;
}

$get_vars = $posted;
$keep_only = array('id', 'quote_id', 'order_by', 'search_for', 'page', 'order', 'return_message', 'country', 'country_from', 'country_to', 'advanced', 'sea_air', 'port_from', 'port_to', 'currency', 'type', 'active');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (!in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

//Redirect Actions
switch ($action) {
    case 'add':
        unset($get_vars['id']);
//    $get_vars['action']='add';
        //    header('Location: edit_forwarder.php?'.http_build_query($get_vars));
        header('Location: forwarders_add.php?' . http_build_query($get_vars));
        break;
    case 'edit':
        $forwarder_lock = forwardersGetLock($forwarder_id);
        if ($forwarder_lock === false || $forwarder_lock['staff_id'] == $staff_id) {
            forwardersSetLock($forwarder_id, $staff_id);
        }

//    header('Location: edit_forwarder.php?action=edit&id='.$forwarder_id);
        header('Location: forwarders_edit.php?' . http_build_query($get_vars));
        break;
    case 'delete':
        header('Location: forwarders_delete.php?' . http_build_query($get_vars));
        break;
    case 'history':
//    header('Location: forwarders_history.php?'.http_build_query($get_vars));
    //    break;
    case 'summary':
    case 'view':
//    header('Location: forwarders_view.php?'.http_build_query($get_vars));
    //    break;
    case 'view_quote':
        header('Location: freight_quote_view.php?' . http_build_query($get_vars));
        break;
    case 'add_quote':
        header('Location: forwarders_addQuote.php?' . http_build_query($get_vars));
        break;
    case 'search':
        header('Location: forwarders_search.php?' . http_build_query($get_vars));
        break;
    case 'select':
        header('Location: forwarders_select.php?' . http_build_query($get_vars));
        break;
    case 'list':
    default:
        unset($get_vars['id']);
        header('Location: forwarders_list.php?' . http_build_query($get_vars));
        break;
}

die();
