<?php
/***********************
 * Purchase Order Manipulation Functions
 * Filename: modify_po.inc.php
 ***********************
 * Purpose:
 *    Magic functions that actually change a purchase order.
 * External Variables used:
 *    $config['db_prefix']
 *    $DB
 * Functions defined:
 *    doAddPO($args)            returns true on success, false otherwise
 *    doEditPO($args)            returns item_count on success, false otherwise
 *    doRemoveItem($args)        returns 0 on success, -1 or 1 on error.
 *        $args is the subset of the $_REQUEST array from the add/edit/remove forms
 *        required to do something useful.
 ***********************/

if (!function_exists('getUSfromRate')) {
    function getUSfromRate($code = 'USD')
    {
        global $DB;
        if ('USD' == $code) {
            return '1.000000';
        }
        if ($row = $DB->query_first(sprintf(
            "SELECT `usfrom` FROM `highwaygroup`.`currency` WHERE `code` = %s LIMIT 1",
            formatSQLString($code)
        ), MYSQLI_ASSOC)) {
            return $row['usfrom'];
        }
        return '0.000000';
    }
}

///////////////////////////////////////////////////////////////////////////////
// ADD FUNCTIONS
///////////////////////////////////////////////////////////////////////////////

// Uses data posted from showAddForm() to add a purchase order
function spo_doAddPO($args)
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];
    $id  = $args['id'];

    // Sanity Check
    // Empty P/O?
    $poempty = true;
    foreach ($args['order'] as $o) {
        if (("" != $o['id']) && ("" != $o['qty']) && (0 != $o['qty']) && ("" != $o['name']) && ("" != $o['price'])) {
            $poempty = false;
            break;
        }
    }

    if ($poempty) {
        return false;
    }

    // General P/O Info
    $new_po_sql = "INSERT INTO {$dbp}purchase_order SET
				supplier_id='{$args['supplier_id']}',
				shipvia_id='{$args['sv_id']}',
				destination_id='{$args['send_to']}',
				terms_id='{$args['ship_terms']}',
				creation_date=UNIX_TIMESTAMP(NOW()),
				shipping_charge='{$args['ship_charge']}',
				payment_method='{$args['pay_method']}',
				from_company='{$args['from_company']}',
				status='1',
				supplier_contact='{$args['contact_id']}',
				orderer_user_id='{$GLOBALS['user_details']['id']}',
				order_currency='{$args['order_currency']}',
				po_notes='{$args['po_notes']}'";

    if ($args['authed']) {
        $new_po_sql .= ",
				authorised='{$args['authed']}',
				authorised_by='{$GLOBALS['user_details']['id']}',
				authorised_date=UNIX_TIMESTAMP(NOW())";
    }

    if ($args['paid']) {
        calcStaticPrices($id);
        $new_po_sql .= ",
				paid='{$args['paid']}',
				paid_by='{$GLOBALS['user_details']['id']}',
				paid_date=UNIX_TIMESTAMP(NOW())";
    }

    if ($args['received']) {
        $new_po_sql .= ",
				received='{$args['received']}',
				received_by='{$GLOBALS['user_details']['id']}',
				received_date=UNIX_TIMESTAMP(NOW())";
    }

    if ($args['checked']) {
        $new_po_sql .= ",
				checked='{$args['checked']}',
				checked_by='{$GLOBALS['user_details']['id']}',
				checked_date=UNIX_TIMESTAMP(NOW())";
    }

    $DB->query($new_po_sql);
    $po_id = $DB->insert_id();

    // PO Created -> Mod History
    $mh_sql = "INSERT INTO {$dbp}purchaseorder_history SET
			user_id='{$GLOBALS['user_details']['id']}',
			time=UNIX_TIMESTAMP(NOW()),
			action_id='1',
			porder_id='$po_id',
			object_id=0";
    $DB->query($mh_sql);

    // P/O Details
    $item_count = 0;
    foreach ($args['order'] as $o) {
        if (("" != $o['id']) && ("" != $o['qty']) && (0 != $o['qty']) && ("" != $o['name']) && ("" != $o['price'])) {
            $po_details_sql = "INSERT INTO {$dbp}purchase_order_details SET
						 porder_id='$po_id',
						 product_id='{$o['id']}',
						 qty='{$o['qty']}',
						 name='{$o['name']}',
						 price='{$o['price']}',
						 currency='{$o['curr']}'";
            $DB->query($po_details_sql);
            $item_count++;
        }
    }

    return true;
}

///////////////////////////////////////////////////////////////////////////////
// EDIT FUNCTIONS
///////////////////////////////////////////////////////////////////////////////

// Uses data posted from showEditForm() to update a purchase order
function spo_doUpdatePO($args)
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];
    $id  = $args['id'];

    // Pull current details from DB
    $old_porder = checkIDGetPO();

    // Sanity Check
    // Empty P/O?
    // initial value of $poempty is true if the new product entry is not filled in, else false.
    $o       = $args['order'];
    $poempty = !(("" != $o['id']) && ("" != $o['qty']) && (0 != $o['qty']) && ("" != $o['name']) && ("" != $o['price']));
    foreach ($args['oldorder'] as $o) {
        if (!$poempty) {
            break;
        }

        if (("" != $o['id']) && ("" != $o['qty']) && (0 != $o['qty'])) {
            $poempty = false;
        }
    }

    if ($poempty) {
        return false;
    }
    // Sanity is assured from here.

    // General P/O Info
    // Check that this is neccessary
    if (true) {
        $update_po_sql = "UPDATE {$dbp}purchase_order SET
					shipvia_id='{$args['sv_id']}',
					destination_id='{$args['send_to']}',
					terms_id='{$args['ship_terms']}',
					shipping_charge='{$args['ship_charge']}',
					payment_method='{$args['pay_method']}',
					status='{$args['status']}',
					supplier_contact='{$args['contact_id']}',
					payment_method='{$args['pay_method']}',
					order_currency='{$args['order_currency']}',
					po_notes='{$args['po_notes']}'";

        if ($args['authed'] && !$old_porder['authorised_date']) {
            $update_po_sql .= ",
					authorised='{$args['authed']}',
					authorised_by='{$GLOBALS['user_details']['id']}',
					authorised_date=UNIX_TIMESTAMP(NOW())";
        }

        if ($args['paid'] && !$old_porder['paid_date']) {
            calcStaticPrices($id);
            $update_po_sql .= ",
					paid='{$args['paid']}',
					paid_by='{$GLOBALS['user_details']['id']}',
					paid_date=UNIX_TIMESTAMP(NOW())";
        }

        if ($args['received'] && !$old_porder['received_date']) {
            $update_po_sql .= ",
					received='{$args['received']}',
					received_by='{$GLOBALS['user_details']['id']}',
					received_date=UNIX_TIMESTAMP(NOW())";
        }

        if ($args['checked'] && !$old_porder['checked_date']) {
            $update_po_sql .= ",
					checked='{$args['checked']}',
					checked_by='{$GLOBALS['user_details']['id']}',
					checked_date=UNIX_TIMESTAMP(NOW())";
        }

        if (($args['checked'] && !$old_porder['checked_date']) || !$args['status']) {
            $update_po_sql .= ",
					days_open='" . intval((time() - $old_porder['creation_date']) / 86400) . "'";
        }

        // File Upload Checky
        $uploadfile = $_SERVER['DOCUMENT_ROOT'] . "/webadmin/new_suppliers/receipt_files/$id-" . $_FILES['payment_receipt']['name'];
        if ($_FILES['payment_receipt']['size'] && !$_FILES['payment_receipt']['error']) {
            if (move_uploaded_file($_FILES['payment_receipt']['tmp_name'], $uploadfile)) {
                // Upload succeeded - save new name in DB
                $update_po_sql .= ",
					receipt_fname='{$_FILES['payment_receipt']['name']}'";
            }
        }

        $update_po_sql .= "WHERE
					porder_id='$id'";

        $DB->query($update_po_sql);
    }

    // P/O Details
    $item_count = 0;
    foreach ($args['oldorder'] as $o) {
        if (("" != $o['qty']) && (0 != $o['qty']) && ($o['updated'])) {
            $po_details_sql = "UPDATE {$dbp}purchase_order_details SET
						qty='{$o['qty']}'
					WHERE
						porder_id='$id' AND
						product_id='{$o['id']}'";
            $DB->query($po_details_sql);
            $item_count++;
        } elseif ((("" == $o['qty']) || (0 == $o['qty'])) && ($o['updated'])) {
            // Quantity -> 0 <==> Delete Item
            $po_remi_sql = "DELETE FROM {$dbp}purchase_order_details
					WHERE porder_id='$id' AND product_id='{$o['id']}'";
            $DB->query($po_remi_sql);
            $item_count++;
        }
    }

    // Any New Items
    foreach ($args['order'] as $o) {
        if (("" != $o['id']) && ("" != $o['qty']) && (0 != $o['qty']) && ("" != $o['name']) && ("" != $o['price'])) {
            $po_details_sql = "INSERT INTO {$dbp}purchase_order_details SET
						 porder_id='$id',
						 product_id='{$o['id']}',
						 qty='{$o['qty']}',
						 name='{$o['name']}',
						 price='{$o['price']}',
						 currency='{$o['curr']}'";
            $DB->query($po_details_sql);
            $item_count++;
        }
    }

    if ($args['admin_notes']) {
        $note_sql = "INSERT INTO {$dbp}purchaseorder_adminnotes SET
				porder_id='$id',
				user_id='{$GLOBALS['user_details']['id']}',
				date=UNIX_TIMESTAMP(NOW()),
				note='{$args['admin_notes']}'";
        $DB->query($note_sql);
    }

    return $item_count;
}

function spo_doRemoveItem($args)
{
    global $DB, $config;
    $dbp        = $config['db_prefix'];
    $id         = $args['po_id'];
    $item_id    = $args['item_id'];
    $args['id'] = $id; // To make showEditForm() happy

    if (!(isset($item_id)) && $id) {
        // Nothing to delete, error occurs
        return -1;
    }

    // Check if there's more items here
    $po_check_sql = "SELECT porder_id FROM {$dbp}purchase_order_details WHERE porder_id='$id'";
    $DB->query($po_check_sql);
    if ($DB->num_rows() < 2) {
        // Refuse to delete last item
        return 1;
    }

    // Delete the item
    $po_remi_sql = "DELETE FROM {$dbp}purchase_order_details
			WHERE porder_id='$id' AND product_id='$item_id'";
    $DB->query($po_remi_sql);

    return 0;
}

////////////////////////////////
// Misc Functions
////////////////////////////////

function calcStaticPrices($porder_id)
{
    global $DB, $config;
    $dbp = $config['db_prefix'];

    $po_info   = $DB->query_first("SELECT order_currency FROM {$dbp}purchase_order WHERE porder_id='$porder_id'");
    $exch_rate = getUSfromRate($po_info[0]);

    $update_sql = "UPDATE {$dbp}purchase_order_details SET static_price=price*$exch_rate WHERE porder_id='$porder_id'";
    $DB->query($update_sql);
}
