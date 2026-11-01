<?php
/***********************
 * Purchase Order Display Core Logic
 * Filename: view_purchaseorder.php
 ***********************
 * Purpose:
 *    Do all the relevant stuff for displaying purchase orders
 * Configuration Variables used:
 *    $config['db_prefix']
 *    $config['template_path']
 * Functions defined:
 ***********************/

include_once __DIR__ . '/common.inc.php';

moduleLoad('template');

require_once "spo_config.php";

require_once "functions/user.inc.php";
require_once "functions/shipping.inc.php";

$dbp = $config['db_prefix'];

$page = new Template($config['template_path']);

$country_list = getCountryList(false);

//echo "<pre>".print_r($_REQUEST, 1)."</pre>";

$from_company = 0;
$order_items  = array();

if (isset($_REQUEST['preview']) && $_REQUEST['preview']) {
    // Grab stuff from $_REQUEST and fill in extra details

    if (array_key_exists("from_company", $_REQUEST)) {
        // Preview from ADD form
        foreach ($_REQUEST['order'] as $o) {
            if ($o['qty'] && $o['name'] && $o['name']) {
                // See if "new" item added
                $order_items[] = $o;
            }
        }

        $date            = time();
        $orderer         = getUserByServer($dbp);
        $shipping_charge = $_REQUEST['shipping_charge'];
        $from_company    = $_REQUEST['from_company'];
    } else {
        // Preview from EDIT form
        // Build order array
        foreach ($_REQUEST['oldorder'] as $i) {
            // Fill in extra details (name & price)
            $np_sql = "SELECT name, price FROM {$dbp}purchase_order_details
					WHERE product_id='{$i['id']}' AND porder_id='{$_REQUEST['id']}'";
            $r          = $DB->query_first($np_sql);
            $t['name']  = $r['name'];
            $t['price'] = $r['price'];
            $t['id']    = $i['id'];
            $t['qty']   = $i['qty'];

            $order_items[] = $t;
        }
        if ($_REQUEST['order']['qty'] && $_REQUEST['order']['name'] && ("Add New Item..." != $_REQUEST['order']['name'])) {
            // See if "new" item added
            $order_items[] = $_REQUEST['order'];
        }

        // Get stuff from PO table
        $po_sql = "SELECT orderer_user_id, creation_date, shipping_charge FROM {$dbp}purchase_order
				WHERE porder_id='{$_REQUEST['id']}'";
        $po              = $DB->query_first($po_sql);
        $date            = $po['creation_date'];
        $orderer         = $po['orderer_user_id'];
        $shipping_charge = $po['shipping_charge'];

        // Get orderer
        $orderer = getUserByID($orderer, $dbp);

        // Find from_company in database
        $tmp          = $DB->query_first("SELECT from_company FROM {$dbp}purchase_order WHERE porder_id='{$_REQUEST['id']}'");
        $from_company = $tmp['from_company'];
    }

    // Get Supplier & Contact details
    $cont_sql = "SELECT contact_name, contact_email, supplier_id FROM {$dbp}suppliers_contacts WHERE contact_id='{$_REQUEST['contact_id']}'";
    $cont     = $DB->query_first($cont_sql);

    $supp = $DB->query_first(sprintf(
        "SELECT `supplier_name`, `address`, `phone`, `fax`, `country_code` FROM %ssuppliers WHERE `supplier_id` = %d LIMIT 1",
        $dbp,
        $cont['supplier_id']
    ), MYSQLI_ASSOC);

    $supp['c_name'] = $country_list[$supp['country_code']] ?? $supp['country_code'];

    // Get Shipping Destination
    $dest_sql = "SELECT * FROM {$dbp}ship_destinations WHERE dest_id='{$_REQUEST['send_to']}'";
    $dest     = $DB->query_first($dest_sql);

    // Get courier details
    $couriers = makeCouriersArray($sv_id);

    // Get payment method
    $pay_method = $DB->query_first("SELECT * FROM {$dbp}payment_methods WHERE paymethod_id='{$_REQUEST['pay_method']}'");

    // Get Ship Terms
    $terms = $DB->query_first("SELECT * FROM {$dbp}shipping_terms WHERE term_id='{$_REQUEST['ship_terms']}'");

    // Dump to template
    $page->set("date", $date);
    $page->set("supplier", $supp);
    $page->set("contact", $cont);
    $page->set("dest", $dest);
//    $page->set("courier", $cour);
    $page->set("courier", $couriers[0]);
    $page->set("orderer", $orderer);
    $page->set("shipping_charge", $shipping_charge);
    $page->set("order_notes", $_REQUEST['po_notes']);
    $page->set("porder_id", $_REQUEST['id']);
    $page->set("order_currency", $_REQUEST['order_currency']);
    $page->set("payment_type", $pay_method['text']);
    $page->set("order_products", $order_items);
    $page->set("term", $terms['term']);
} elseif (isset($id) && $id) {
    // Grab stuff from the DB

    // build order array
    $np_sql = "SELECT product_id, qty, name, price, currency FROM {$dbp}purchase_order_details
			WHERE porder_id='{$_REQUEST['id']}'";
    $npq = $DB->query($np_sql);
    while ($r = $DB->fetch_array($npq)) {
        $t['name']     = $r['name'];
        $t['price']    = $r['price'];
        $t['currency'] = $r['currency'];
        $t['id']       = $r['product_id'];
        $t['qty']      = $r['qty'];

        $order_items[] = $t;
    }

    // Get stuff from PO table
    $po_sql = "SELECT orderer_user_id, creation_date, shipping_charge,
				supplier_contact, destination_id, shipvia_id, po_notes,
				payment_method, st.term, from_company, order_currency
       			FROM {$dbp}purchase_order po
			INNER JOIN {$dbp}shipping_terms st ON st.term_id=po.terms_id
			WHERE porder_id='{$_REQUEST['id']}'";
    $po              = $DB->query_first($po_sql);
    $date            = $po['creation_date'];
    $orderer         = $po['orderer_user_id'];
    $shipping_charge = $po['shipping_charge'];
    $contact_id      = $po['supplier_contact'];
    $order_currency  = $po['order_currency'];
    $send_to         = $po['destination_id'];
    $sv_id           = $po['shipvia_id'];
    $term            = $po['term'];
    $from_company    = $po['from_company'];

    // Get orderer
    $orderer = getUserByID($orderer, $dbp);

    // Get Supplier & Contact details
    $cont_sql = "SELECT contact_name, contact_email, supplier_id FROM {$dbp}suppliers_contacts
			WHERE contact_id='$contact_id'";
    $cont     = $DB->query_first($cont_sql);
    $supp_sql = sprintf(
        "SELECT `supplier_name`, `address`, `phone`, `fax`, `country_code` FROM %ssuppliers WHERE `supplier_id` = %d LIMIT 1",
        $dbp,
        $cont['supplier_id']
    );
    $supp = $DB->query_first($supp_sql);

    $supp['c_name'] = $country_list[$supp['country_code']] ?? $supp['country_code'];

    // Get Shipping Destination
    $dest_sql = "SELECT * FROM {$dbp}ship_destinations WHERE dest_id='$send_to'";
    $dest     = $DB->query_first($dest_sql);

    // Get courier details
    $couriers = makeCouriersArray($sv_id);

    // Get payment method
    $pay_method = $DB->query_first("SELECT * FROM {$dbp}payment_methods WHERE paymethod_id='{$po['payment_method']}'");

    // Dump to template
    $page->set("date", $date);
    $page->set("supplier", $supp);
    $page->set("contact", $cont);
    $page->set("dest", $dest);
//    $page->set("courier", $cour);
    $page->set("courier", $couriers[0]);
    $page->set("orderer", $orderer);
    $page->set("shipping_charge", $shipping_charge);
    $page->set("order_currency", $order_currency);
    $page->set("term", $term);
    $page->set("order_notes", $po['po_notes']);
    $page->set("porder_id", $_REQUEST['id']);
    $page->set("order_products", $order_items);
    $page->set("payment_type", $pay_method['text']);
} else {
    die("No PO to view!");
}

// Do Template
if ($from_company) {
    echo $page->fetch("viewFWPurchaseOrder.tpl.php");
} else {
    echo $page->fetch("viewPurchaseOrder.tpl.php");
}
