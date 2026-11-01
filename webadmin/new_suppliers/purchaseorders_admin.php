<?php
/***********************
 * Purchase Order Admin Core Logic
 * Filename: purchaseorder_admin.php
 ***********************
 * Purpose:
 *    Do all the relevant stuff for purchase orders
 * Configuration Variables used:
 *    $config['db_prefix']
 *    $config['template_path']
 * Functions defined:
 *    checkIDGetPOrder() -    Check to see if $_REQUEST['id'] exists and is valid
 *                Returns the purchase order array if it's ok, will also
 *                grab a lock on the record.
 *    listSuppliers() -    Grab ALL Supplier ID's and names and whack them in an array
 *    listContacts($arg) -    Grab contact ID's and names related to a supplier id ($arg) and
 *                whack it in an array. If $arg is not set, grab ALL contacts
 ***********************/
include_once __DIR__ . '/common.inc.php';

moduleLoad('template');

include_once "spo_config.php";

include_once "functions/country.inc.php"; // Country Functions (requires DB)
include_once "functions/currency.inc.php"; // Currency Functions (requires DB)
include_once "functions/lock.inc.php"; // DB Locking (requires DB)
include_once "functions/menu.inc.php"; // Menu updater (requires DB)
include_once "functions/user.inc.php"; // User Functions (requires DB)
include_once "functions/addresses.inc.php"; // Address-related functions (requires DB)
include_once "functions/shipping.inc.php"; // Courier-related functions (requires DB)
include_once "functions/supplier_contact.inc.php"; // Supplier Magic (Requires DB)

include_once "modify_po.inc.php"; // PO Manipulation Functions (Requires DB)

$user_details = getUserByServer($config['db_prefix']);

// Handle request for action
switch ($_REQUEST['action']) {
    case "add":
        $content = showAddForm();
        break;
    case "edit":
        $content = showEditForm();
        break;
    case "list":
        $content = listPurchaseOrders();
        break;
    case "do_add":
        $content = doAddPO();
        break;
    case "do_edit":
        $content = doUpdatePO();
        break;
    case "do_rmi":
        $content = doRemoveItem();
        break;
    default:
        $content = listPurchaseOrders();
        break;
}

$counts = menuCounters($config['db_prefix']);

$content['header_links']["Add New Purchase Order"]                        = "purchaseorders_admin.php?action=add";
$content['header_links']["List Open Purchase Orders ({$counts["open"]})"] = "purchaseorders_admin.php?action=list&show=open";
$content['header_links']["List All Purchase Orders ({$counts["all"]})"]   = "purchaseorders_admin.php?action=list&show=all";

outputHTML($content, $msg, $config['template_path']);

// Prototype Output-producing function (19 lines)
function prototype()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];
    $id  = $_REQUEST['id'];

    /* CODE HERE */

    // Do Template
    $page = new Template($config['template_path']);
    $page->set("blah", $blah);
    $ret['content'] = $page->fetch("template");
    $ret['msg']     = "";
    $ret['title']   = "page title";
    return $ret;
}

// Function for the listing of Purchase Order Summaries
function listPurchaseOrders()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];

    // List length variables
    if (array_key_exists('per_page', $_REQUEST)) {
        $per_page = max(1, intval($_REQUEST['per_page']));
    } else {
        $per_page = 50;
    }
    $start = 0;
    if (array_key_exists('page', $_REQUEST)) {
        $start = $_REQUEST['page'] * $per_page;
    }

    // Get Basic info from the Purchase Order
    $po_sql = "	SELECT
				po.porder_id, sup.supplier_name, creation_date, po.status,
				po.authorised, po.paid, po.received, po.checked, po.shipping_charge,
				po.from_company, po.orderer_user_id, po.days_open, po.order_currency
			FROM
				{$dbp}purchase_order AS po
			JOIN
				{$dbp}suppliers AS sup
				USING(supplier_id)
			";
    if ("open" == $_REQUEST['show']) {
        $po_sql .= "WHERE po.status='1' AND po.checked='0'";
    }
    if (array_key_exists('supplier_id', $_REQUEST) && $_REQUEST['supplier_id']) {
        if (array_key_exists('show', $_REQUEST) && "open" == $_REQUEST['show']) {
            $po_sql .= " AND `po`.`supplier_id` = " . intval($_REQUEST['supplier_id']);
        } else {
            $po_sql .= "WHERE `po`.`supplier_id` = " . intval($_REQUEST['supplier_id']);
        }
    }
    if (!array_key_exists('by', $_REQUEST)) {
        $_REQUEST['by'] = 'po.porder_id';
    }

    $_REQUEST['dir'] = (array_key_exists('dir', $_REQUEST)) ? strtoupper($_REQUEST['dir']) : '';
    if ("ASC" != $_REQUEST['dir'] && "DESC" != $_REQUEST['dir']) {
        $_REQUEST['dir'] = "DESC";
    }

    $po_sql .= " ORDER BY {$_REQUEST['by']} {$_REQUEST['dir']}";

    // Total number of items
    $DB->query($po_sql);
    $total_items = $DB->num_rows();

    // More searching
    $po_sql .= "\nLIMIT $start, $per_page";

    $orders = array();
    $po_result = $DB->query($po_sql);
    while ($order = $DB->fetch_array($po_result)) {
        // Days Open
        if (!$order['days_open']) {
            $order['days_open'] = intval((time() - $order['creation_date']) / 86400) . "*";
        }

        // Decode Status Codes
        if (!$order['status']) {
            $order['status']      = "Cancelled";
            $order['status_code'] = "cancelled";
        } else {
            // open, authorised, paid, received
            $order['status']      = "Awaiting Approval";
            $order['status_code'] = "awaiting_approval";
            if ($order['authorised']) {
                $order['status']      = "Awaiting Payment";
                $order['status_code'] = "awaiting_payment";
                if ($order['paid']) {
                    $order['status']      = "Awaiting Goods";
                    $order['status_code'] = "awaiting_goods";
                    if ($order['received']) {
                        $order['status']      = "Awaiting Goods Checking";
                        $order['status_code'] = "awaiting_check";
                        if ($order['checked']) {
                            $order['status']      = "Processed";
                            $order['status_code'] = "processed";
                        }
                    }
                }
            }
        }

        // Get total price and qty's from the po details - fetch "static" prices too if it's been paid
        $pl_sql = " SELECT" .
        "	qty," .
//                ($order['paid']?"    static_price,":"").
        "	price," .
            "	currency" .
            " FROM " . $dbp . "purchase_order_details" .
            " WHERE porder_id='" . $order['porder_id'] . "'";
        $DB->query($pl_sql);
        $order['item_count']  = 0;
        $order['total_price'] = 0;
        //$order['static_price'] = 0;
        while ($tmp = $DB->fetch_array()) {
            $order['item_count'] += $tmp['qty'];
            $order['total_price'] += $tmp['qty'] * $tmp['price'];
            //$order['static_price'] += $tmp['qty'] * $tmp['static_price'];
        }
        $order['total_price'] += $order['shipping_charge'];

        // Get user details
        $order['user'] = getUserByID($order['orderer_user_id']);

        // Save this order in the array
        $orders[] = $order;
    }

    // Do Template
    $page = new Template($config['template_path']);
    $page->set("orders", $orders);

    // Page-Limit Links
    $page_last = $start + $per_page;
    if ($page_last > $total_items) {
        $page_last = $total_items;
    }
    $page->set("page_first", $start + 1);
    $page->set("page_last", $page_last);
    $page->set("total_items", $total_items);
    $page->set("page_links", makePageLimitLinks($search_args, $total_items, (array_key_exists('page', $_REQUEST)) ? $_REQUEST['page'] : 0, $per_page));

    $ret['content'] = $page->fetch("purchaseorders_front.tpl.php");
    $ret['msg']     = "";
    $ret['title']   = (strtolower($_REQUEST['show']) == "open") ? "Open Purchase Orders" : "All Purchase Orders";
    return $ret;
}

///////////////////////////////////////////////////////////////////////////////
// ADD FUNCTIONS
///////////////////////////////////////////////////////////////////////////////

// Displays the Add Purchase Order form
function showAddForm()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];
    $id  = $_REQUEST['id'];

    // set PO varibales if they already exist (i.e. we had an error condition)
    $porder['supplier_id']    = $_REQUEST['supplier_id'];
    $porder['shipvia_id']     = $_REQUEST['sv_id'];
    $porder['destination_id'] = $_REQUEST['send_to'];
    $porder['ship_terms']     = $_REQUEST['ship_terms'];
    $porder['ship_charge']    = $_REQUEST['ship_charge'];
    $porder['pay_method']     = $_REQUEST['pay_method'];
    $porder['contact_id']     = $_REQUEST['contact_id'];
    $porder['po_notes']       = $_REQUEST['po_notes'];

    // Do Template
    $page = new Template($config['template_path']);
    $page->set("mode", "add");
    //$page->set("order", array(0, 1, 2, 3, 4, 5, 6));
    $page->set("suppliers", listSuppliers());
    $page->set("contacts", listContacts());
    $page->set("currency", makeCurrencyArray());
    $page->set("couriers", makeCouriersArray());
    $page->set("dests", makeAddressArray($dbp));
    $page->set("terms", makeTermsArray($dbp));
    $page->set("form_dest", "?action=do_add");
    $page->set("paymethods", makePayMethodList($dbp));
    $page->set("porder", $porder);
    $ret['content'] = $page->fetch("edit_purchaseorder.tpl.php");
    $ret['title']   = "New Purchase Order";
    return $ret;
}

// Uses data posted from showAddForm() to add a purchase order
function doAddPO()
{
    if (spo_doAddPO($_REQUEST)) {
        $ret = listPurchaseOrders();
        $ret['msg'] .= "$item_count items added to PO<br />\r\n";
    } else {
        // Quit the function!
        $ret        = showAddForm();
        $ret['msg'] = "<div style=\"color: red\">ERROR! Please enter some products.</div><br />\r\n";
    }
    return $ret;
}

///////////////////////////////////////////////////////////////////////////////
// EDIT FUNCTIONS
///////////////////////////////////////////////////////////////////////////////

// Shows the edit form
function showEditForm()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];
    $id  = $_REQUEST['id'];

    // Check for existance of id &
    $porder = checkIDGetPO();
    if (!$porder) {
        $ret        = listPurchaseOrders();
        $ret['msg'] = "Valid ID not supplied!";
        return $ret;
    }

    // Find Supplier Name
    $sn_sql = "SELECT supplier_name FROM {$dbp}suppliers WHERE supplier_id='{$porder['supplier_id']}'";
    $sn     = $DB->query_first($sn_sql);
    $sn     = $sn['supplier_name'];

    // Get order details from DB
    $po_details_sql = "SELECT * FROM {$dbp}purchase_order_details WHERE porder_id='$id'";
    $DB->query($po_details_sql);
    while ($pod = $DB->fetch_array()) {
        $po_details[] = $pod;
    }

    // Get Admin Notes
    $get_notes_sql = "SELECT * FROM {$dbp}purchaseorder_adminnotes WHERE porder_id='$id' ORDER BY date DESC";
    $DB->query($get_notes_sql);
    while ($note = $DB->fetch_array()) {
        $note['user']  = getUserByID($note['user_id'], $dbp);
        $admin_notes[] = $note;
    }

    // Get Mod History
    $get_hist_sql = "SELECT * FROM {$dbp}purchaseorder_history
				INNER JOIN {$dbp}update_actions USING(action_id)
				WHERE porder_id='$id'";
    $DB->query($get_hist_sql);
    while ($hist = $DB->fetch_array()) {
        $hist['user']  = getUserByID($hist['user_id'], $dbp);
        $mod_history[] = $hist;
    }

    // Get names for things
    $porder['authorised_by'] = getUserByID($porder['authorised_by'], $dbp);
    $porder['paid_by']       = getUserByID($porder['paid_by'], $dbp);
    $porder['received_by']   = getUserByID($porder['received_by'], $dbp);
    $porder['checked_by']    = getUserByID($porder['checked_by'], $dbp);

    // Do Template
    $page = new Template($config['template_path']);
    $page->set("mode", "edit");
    $page->set("order", $po_details);
    $page->set("supplier_name", $sn);
    $page->set("porder", $porder);
    $page->set("contacts", listContacts($porder['supplier_id']));
    $page->set("currency", makeCurrencyArray());
    $page->set("couriers", makeCouriersArray());
    $page->set("dests", makeAddressArray($dbp));
    $page->set("terms", makeTermsArray($dbp));
    $page->set("admin_notes", $admin_notes);
    $page->set("order_history", $mod_history);
    $page->set("form_dest", "?action=do_edit&id=$id");
    $page->set("paymethods", makePayMethodList($dbp));
    $ret['content'] = $page->fetch("edit_purchaseorder.tpl.php");
    $ret['msg']     = "";
    $ret['title']   = "Edit Purchase Order";

    return $ret;
}

// Uses data posted from showEditForm() to update a purchase order
function doUpdatePO()
{
    if ($item_count = spo_doUpdatePO($_REQUEST)) {
        $ret = listPurchaseOrders();
        $ret['msg'] .= "$item_count items updated.<br />\r\n";
    } else {
        $ret        = showEditForm();
        $ret['msg'] = "<div style=\"color: red\">ERROR! Reufsing to delete all products from a purchase order.</div><br />\r\n";
    }
    return $ret;
}

function doRemoveItem()
{
    $fval = spo_doRemoveItem($_REQUEST);
    if (!$fval) {
        $ret        = showEditForm();
        $ret['msg'] = "Product $item_id removed from Purchase Order.<br />";
    } elseif (1 == $fval) {
        // Refuse to delete last item
        $ret        = showEditForm();
        $ret['msg'] = "<div style=\"color: red\">ERROR: Refusing to remove last product from a Purchase Order.</div><br />";
    } elseif (-1 == $fval) {
        // Nothing to delete, error occurs
        $ret        = showEditForm();
        $ret['msg'] = "<div style=\"color: red\">Invalid request. Cannot remove products.</div><br />";
    }
    return $ret;
}

///////////////////////////////////////////////////////////////////////////////
// USEFUL FUNCTIONS
///////////////////////////////////////////////////////////////////////////////

function checkIDGetPO()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];

    if (!array_key_exists("id", $_REQUEST)) {
        // We have no ID
        return false;
    } else {
        $id = $_REQUEST['id'];
    }

    if (locked("{$dbp}purchase_order", array("porder_id" => $id), true)) {
        if (!my_lock("{$dbp}porder", array("porder_id" => $id))) {
            return false;
        }
    }
    lock("{$dbp}purchase_order", array("porder_id" => $id));

    // Grab Supplier info from DB
    $porder_sql = "SELECT * FROM {$dbp}purchase_order WHERE porder_id='$id'";
    $DB->query($porder_sql);
    if (!$DB->num_rows()) {
        // If no rows, we have a bad ID
        return false;
    }
    $porder = $DB->fetch_array();

    return $porder;
}
