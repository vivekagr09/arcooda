<?php
include_once __DIR__ . '/common.inc.php';

use PHPMailer\PHPMailer\PHPMailer;

moduleLoad('asset'); //assetTableInfoFromType, assetToDatabase, assetFromCompatible, assetGenerateUniqueName, assetSearch, assetToCompatible, assetFromDatabase
moduleLoad('member'); //memberSurveyToDatabase, memberSetValue
moduleLoad('notify'); //notifyActionMade, notifyActionRecipients
moduleLoad('phpmailer');
moduleLoad('product'); //productStrSplit
error_reporting(E_ALL);
ini_set('display_errors', 1);
$action          = (array_key_exists('action', $posted)) ? $posted['action'] : 'list';
$invoice_id      = (array_key_exists('invoice_id', $posted)) ? intval($posted['invoice_id']) : 0;
$create_purchase = false;
if ('do_purchase' == $action) {
    $create_purchase = true;
    $action          = 'do_edit';
}
//Update Actions
switch ($action) {
    case 'do_add':
        unset($posted['invoice_id']);
        //no break
    case 'do_edit':
        if (count($posted) < 20) {
            $action = "list";
            break;
        }
        $change_list = array();

        if (isset($posted['mark_paid_on_invoice']) === false) {
            $posted['mark_paid_on_invoice'] = 0;
        } else {
            $posted['mark_paid_on_invoice'] = 1;
        }

        /**
         * Read new invoice from the posted form
         */
        $invoice                  = invoiceFromPost($posted);
        $invoice_products         = invoiceProductsFromPost($posted);
        $invoice_charges          = invoiceChargesFromPost($posted);
        $invoice_courier_quotes   = invoiceCourierQuotesFromPost($posted);
        $invoice_forwarder_quotes = invoiceForwarderQuotesFromPost($posted);
        $invoice_survey           = invoiceSurveyFromPost($posted);
        $invoice_dashboard_history = invoiceDashboardHistoryFromPost($posted);
        /**
         * Read old invoice from the database
         */
        $old_invoice          = invoiceFromDatabase($invoice['invoice_id']);
        $old_invoice_products = invoiceProductsFromDatabase($invoice['invoice_id']);
        $old_invoice_charges  = invoiceChargesFromDatabase($invoice['invoice_id']);
        $old_invoice_survey   = invoiceFromDatabase($invoice['invoice_id']);
        // $old_invoice_dashboard_history = invoiceDashboardHistoryFromDatabase($posted);
        //$old_invoice_courier_quotes = invoiceCourierQuotesFromDatabase($invoice['invoice_id']);
        //$old_invoice_forwarder_quotes = invoiceForwarderQuotesFromDatabase($invoice['invoice_id']);
        $inventory_tables = array(
            0  => '`hg_products`.`products_inventory`',
            20 => '`spcom_spcom`.`products_inventory`',
            26 => '`spcom_spcom`.`products_inventory`',
        );
        $product_tables = array(
            0  => '`hg_products`.`products`',
            9  => '`convert_convert`.`products`',
            16 => '`convert_convert`.`products`',
            20 => '`spcom_spcom`.`products`',
            25 => '`convert_convert`.`products`',
            26 => '`fchcom_fchcom`.`products`',
            33 => '`hwmedia_hwmedia`.`products`',
        );

        $old_inventory_table = $inventory_tables[(array_key_exists($old_invoice['site_id'], $inventory_tables)) ? $old_invoice['site_id'] : 0];
        $new_inventory_table = $inventory_tables[(array_key_exists($invoice['site_id'], $inventory_tables)) ? $invoice['site_id'] : 0];

        $new_product_table = $product_tables[(array_key_exists($invoice['site_id'], $product_tables)) ? $invoice['site_id'] : 0];

        /**
         * Check if the invoice already exists
         */
        $invoice_exists = invoiceExists($invoice['invoice_id']);
        if (!$invoice_exists) {
            $change_list[] = 'New Invoice Created';
        }

        /**
         * Check if we need to save a revision of the invoice
         */


        //$freightquotes_affected = explode(',', $posted['freightquote_affected']);
        $freightquotes_affected = array();
        foreach ($freightquotes_affected as $key => $quote_id) {
            if (intval($quote_id) == 0) {
                unset($freightquotes_affected[$key]);
            }
        }

        if ('y' == $posted['process_invoice']) {
            //ie tracking number entered, so need to process the invoice and
            $invoice['invoice_status'] = $invoice['invoice_action'] = 'processed';
        }
        if (!isset($posted['is_harmonised_code']) || 'y' != $posted['is_harmonised_code']) {
            $invoice['is_harmonised_code'] = 'n';
        }
        if (!isset($posted['is_origin_country']) || 'y' != $posted['is_origin_country']) {
            $invoice['is_origin_country'] = 'n';
        }
        if (!isset($posted['is_invoice_usage']) || 'y' != $posted['is_invoice_usage']) {
            $invoice['is_invoice_usage'] = 'n';
        }
        if (!isset($posted['is_invoice_materials']) || 'y' != $posted['is_invoice_materials']) {
            $invoice['is_invoice_materials'] = 'n';
        }
        if (!isset($posted['is_product_harmonised_code']) || 'y' != $posted['is_product_harmonised_code']) {
            $invoice['is_product_harmonised_code'] = 'n';
        }
        if (!isset($posted['is_product_origin_country']) || 'y' != $posted['is_product_origin_country']) {
            $invoice['is_product_origin_country'] = 'n';
        }
        if (!isset($posted['is_product_usage']) || 'y' != $posted['is_product_usage']) {
            $invoice['is_product_usage'] = 'n';
        }
         if (!isset($posted['is_product_materials']) || 'y' != $posted['is_product_materials']) {
            $invoice['is_product_materials'] = 'n';
        }
        /**
         * Save invoice changes to database
         */
        //echo '<pre>'; print_r($invoice); die;
         if ($invoice_exists && ($invoice['do_revision'] || $invoice_products['do_revision'] || $invoice_charges['do_revision'])) {
             $invoice['revision_id']++;
        }
        
        $invoice_id = $invoice['invoice_id'] = invoiceToDatabase($invoice);
        // Save packing information if present
        if ($invoice_id) {
            $packing_info = invoicePackingInformationFromPost($posted, $invoice_id);
            if (!empty($packing_info)) {
                invoicePackingInformationToDatabase($invoice_id, $packing_info);
            }
        }

        invoiceProductsToDatabase($invoice_id, $invoice_products);
        invoiceChargesToDatabase($invoice_id, $invoice_charges);
        invoiceCourierQuotesToDatabase($invoice_id, $invoice_courier_quotes);
        invoiceForwarderQuotesToDatabase($invoice_id, $invoice_forwarder_quotes, $freightquotes_affected);
        invoiceSurveyToDatabase($invoice_id, $invoice_survey);
        memberSurveyToDatabase($invoice['member_id'], $invoice_survey);
        invoiceGiftsToDatabase($invoice_id, $invoice_products);
        if ($invoice_exists && ($invoice['do_revision'] || $invoice_products['do_revision'] || $invoice_charges['do_revision'])) {
            $asset_type = 'invoice';
            $table_info = assetTableInfoFromType($asset_type);
            $asset      = array(
                'invoice_id'    => $invoice_id,
                'staff_id'      => $_GLOBALS['staff']['id'],
                'asset_caption' => 'Proforma ' . invoiceFormatNumber($invoice_id, $old_invoice['revision_id'], $old_invoice['site_id']),
            );
            $asset['file_orig'] = formatFileSafe($asset['asset_caption'] . $ext);
            $asset['asset_id']  = intval(assetToDatabase($asset_type, assetFromCompatible($asset_type, $asset)));

            /**
             * Save a copy of the revision in the new asset
             */
            $invoice_buffer = 0;
            $size_failure   = false;
            $invoice_gifts    = invoiceGiftsFromDatabasePrint($invoice_id);
            $_STACK[]       = $invoice;
            $_STACK[]       = $invoice_products;
            $_STACK[]       = $invoice_charges;
            $_STACK[]       = $invoice_gifts;
            include __DIR__ . '/templates/jpeg_invoice.tpl.php';
            if ($size_failure) {
                if (function_exists('imagedestroy')) {
                    imagedestroy($invoice_buffer);
                }
                $invoice_buffer     = false;
                $asset['file_type'] = 'application/pdf';
                $asset['filename']  = assetGenerateUniqueName($asset_type, assetFromCompatible($asset_type, $asset)) . ".pdf";
                include __DIR__ . '/templates/pdf_invoice.tpl.php';
                if (($fp = fopen($table_info['directory'] . $asset['filename'], 'wb')) !== false) {
                    fwrite($fp, $invoice_buffer);
                    fclose($fp);
                }
            } else {
                $asset['file_type'] = 'image/gif';
                $asset['filename']  = assetGenerateUniqueName($asset_type, assetFromCompatible($asset_type, $asset)) . ".gif";
                imagegif($invoice_buffer, $table_info['directory'] . $asset['filename']);
                if (function_exists('imagedestroy')) {
                    imagedestroy($invoice_buffer);
                }
                $invoice_buffer = false;
            }
            unset($invoice_buffer);
            $asset['filesize'] = filesize($table_info['directory'] . $asset['filename']);
            assetToDatabase($asset_type, assetFromCompatible($asset_type, $asset));
            $invoice_charges  = array_pop($_STACK);
            $invoice_products = array_pop($_STACK);
            $invoice          = array_pop($_STACK);

            /**
             * Increment the revision
             */
            $change_list[] = 'New Revision Created';
        }
        if (array_key_exists('invoice_attachment', $posted) && is_array($posted['invoice_attachment']) && count($posted['invoice_attachment']) > 0) {
            foreach ($posted['invoice_attachment'] as $key => $attachment) {
                 $DB->query(sprintf(
                    "UPDATE `highwaygroup`.`invoice_assets` SET `asset_caption` = '".$attachment['asset_caption']."' WHERE `asset_id` = %d LIMIT 1",
                    $attachment['id'],
                ));
            }
        }
        /**
         * To insert display customer message
         */

        if($old_invoice['display_to_customer'] != $invoice_dashboard_history['display_to_customer']) {
            invoiceDashboardHistoryToDatabase($invoice_dashboard_history);
            $message = array(
                'invoice_id'      => $invoice_id,
                'message_sender'  => $_GLOBALS['staff']['id'],
                'message_sentto'  => 'Invoice System',
                'message_type_id' =>  4,
                'message_title'   => 'Display to Customer',
                'message_notes'   => $invoice_dashboard_history['display_to_customer'],
            );
            invoiceHistoryToDatabase($invoice_id, $message);

        }

        /**
         * Detect if a voucher was used and process it
         */
        if (0 != $posted['voucher_used']) {
            foreach ($invoice_charges as $charge) {
                if (22 == $charge['charge_id']) {
                    $DB->query(sprintf(
                        "UPDATE `highwaygroup`.`gift_voucher` SET `status` = 'processed', `process_date` = NOW(), `invoice_id` = %d WHERE `id` = %d LIMIT 1",
                        $invoice_id,
                        $posted['voucher_used']
                    ));
                    $change_list[] = 'Processed Voucher #' . intval($posted['voucher_used']);
                    unset($posted['voucher_used']);
                    break;
                }
            }
        }

        /**
         * Detect changes in shipping/reservation status
         */

        //Load previous reservations
        $old_reserve_list = array();
        if (0 != $old_invoice['invoice_id']) {
            if ($query = $DB->query(sprintf(
                "SELECT `product_id`, `sub_id`, `qty` FROM `hg_products`.`products_reserved` WHERE `invoice_id` = %d",
                $old_invoice['invoice_id']
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $old_reserve_list[productStrJoin($row['product_id'], $row['sub_id'])] += $row['qty'];
                }
                $DB->free_result($query);
            }

            foreach ($old_reserve_list as $k => $v) {
                if (0 == $v) {
                    unset($old_reserve_list[$k]);
                }
            }
        }

        //Load previous deductions
        $old_deduct_list = array();
        if (0 != $old_invoice['invoice_id'] && $old_inventory_table === $new_inventory_table) {
            $query = $DB->query(sprintf(
                "SELECT `product_id`, `sub_id`, `movement` FROM %s WHERE `invoice_id` = %d",
                $old_inventory_table,
                $old_invoice['invoice_id']
            ));
            while ($row = $DB->fetch_assoc($query)) {
                $old_deduct_list[productStrJoin($row['product_id'], $row['sub_id'])] -= $row['movement'];
            }
            $DB->free_result($query);

            foreach ($old_deduct_list as $k => $v) {
                if (0 == $v) {
                    unset($old_deduct_list[$k]);
                }
            }
        }

        //Make a list of what should be reserved
        $reserve_list = array();
        $reserve_used = array();
        foreach ($invoice_products as $product) {
            if ('n' == $product['generic']) {
                $product_str = productStrJoin(intval($product['product_id']), intval($product['sub_id']));
                $reserve_list[$product_str] += $product['product_qty'];
                if (2 == $product['product_condition']) {
                    $reserve_used[$product_str] += $product['product_qty'];
                }

                if ($product['adapter_id'] > 0) {
                    $reserve_list[$product['adapter_id']] += $product['product_qty'];
                }
                /*
                if ($product['power_id'] > 0) {
                    $reserve_list[$product['power_id']]+= $product['product_qty'];
                }
                */
            } elseif ('b' == $product['generic']) {
                $query = $DB->query(sprintf(
                    "SELECT `product_id`, `sub_id`, `product_qty` FROM `hwent_hwent`.`bulk_offers_products` WHERE `bulk_id` = %d",
                    substr($product['generic_code'], 3)
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $product_str = productStrJoin(intval($row['product_id']), intval($row['sub_id']));
                    $reserve_list[$product_str] += $product['product_qty'] * $row['product_qty'];
                }
                $DB->free_result($query);
            }
        }

        //Applied Shared Inventories
        if ('`hg_products`.`products`' === $new_product_table) {
            foreach ($reserve_list as $product_str => $product_qty) {
                $product_id = 0;
                $sub_id     = 0;
                extract(productStrSplit($product_str, 2));
                $row = $DB->query_first(sprintf(
                    "SELECT `same_as`, `same_as_sub` FROM %s WHERE `id` = %d AND `sub_id` = %d LIMIT 1",
                    $new_product_table,
                    $product_id,
                    $sub_id
                ), MYSQLI_ASSOC);
                if ($row['same_as'] > 0) {
                    $reserve_list[productStrJoin($row['same_as'], $row['same_as_sub'])] += $product_qty;
                    unset($reserve_list[$product_str]);
                }
            }
        }

        //Make a list of what should be deducted
        $deduct_list = array();
        $deduct_used = array();
        switch ($invoice['invoice_status']) {
            case 'sales':
                //no break
            case 'proforma':
                if ('shipped' != $invoice['invoice_action']) {
                    break;
                }
                //no break
            case 'processed':
                $deduct_list = $reserve_list;
                $deduct_used = $reserve_used;
                //no break
            default:
                $reserve_list = array();
                $reserve_used = array();
                break;
        }
        foreach ($deduct_list as $k => $v) {
            if ($v <= 0) {
                unset($deduct_list[$k]);
            }
        }

        //Make a list of the difference between $old_reserve_list and $reserve_list
        $reserve_change = array();
        foreach ($old_reserve_list as $k => $v) {
            $reserve_change[$k] = $reserve_list[$k] - $v;
        }

        foreach ($reserve_list as $k => $v) {
            $reserve_change[$k] = $v - $old_reserve_list[$k];
        }

        foreach ($reserve_change as $k => $v) {
            if (0 == $v) {
                unset($reserve_change[$k]);
            }
        }

        //Make a list of the difference between $old_deduct_list and $deduct_list
        $deduct_change = array();
        foreach ($old_deduct_list as $k => $v) {
            $deduct_change[$k] = $deduct_list[$k] - $v;
        }

        foreach ($deduct_list as $k => $v) {
            $deduct_change[$k] = $v - $old_deduct_list[$k];
        }

        foreach ($deduct_change as $k => $v) {
            if (0 == $v) {
                unset($deduct_change[$k]);
            }
        }

        //Reserve the stock
        if (count($old_reserve_list)) {
            $DB->query(sprintf(
                "DELETE FROM `hg_products`.`products_reserved` WHERE `invoice_id` IN(%d, %d)",
                $old_invoice['invoice_id'],
                $invoice_id
            ));
        }

        foreach ($reserve_list as $product_str => $product_qty) {
            if ($product_qty > 0) {
                $product_id = 0;
                $sub_id     = 0;
                extract(productStrSplit($product_str, 2));
                $DB->query(sprintf(
                    "INSERT INTO `hg_products`.`products_reserved` (`invoice_id`, `product_id`, `sub_id`, `qty`) VALUES (%d, %d, %d, %d)",
                    $invoice_id,
                    $product_id,
                    $sub_id,
                    $product_qty
                ));
            }
        }

        //Deduct the stock
        if (0 !== $old_invoice['invoice_id'] && $old_invoice['site_id'] == $invoice['site_id']) {
            foreach ($deduct_change as $product_str => $product_qty) {
                if (0 !== $product_qty) {
                    $product_id = 0;
                    $sub_id     = 0;
                    extract(productStrSplit($product_str, 2));

                    //TODO:Poor hack, come back to this
                    $used = array_key_exists($product_str, $deduct_used);

                    //Update product
                    $DB->query(sprintf(
                        "UPDATE %s SET `last_sold` = NOW(), `qty` = `qty` - %d, `total_sales` = `total_sales` + %d%s WHERE `id` = %d%s LIMIT 1",
                        $new_product_table,
                        $product_qty,
                        $product_qty,
                        ($used) ? '' : sprintf(
                            ", `floor_stock` = IF(`floor_stock` > %d, `floor_stock` - %d, 0)",
                            $product_qty,
                            $product_qty
                        ),
                        $product_id,
                        ('`hg_products`.`products`' === $new_product_table) ? sprintf(
                            " AND `sub_id` = %d",
                            $sub_id
                        ) : ''
                    ));
                    $product_item = $DB->query_first(sprintf(
                        "SELECT `qty`, `alert_level`, `when_sold_out` FROM %s WHERE `id` = %d%s LIMIT 1",
                        $new_product_table,
                        $product_id,
                        ('`hg_products`.`products`' === $new_product_table) ? sprintf(
                            " AND `sub_id` = %d",
                            $sub_id
                        ) : ''
                    ), MYSQLI_ASSOC);

                    //Update inventory
                    $DB->query(sprintf(
                        "INSERT INTO %s (`product_id`, `sub_id`, `invoice_id`, `date`, `movement`, `comments`, `available`, `stock_type`, `stock_left`) VALUES (%d, %d, %d, NOW(), %d, '%s with invoice I#%d%s', %d, '%s', %d)",
                        $new_inventory_table,
                        $product_id,
                        $sub_id,
                        $invoice_id,
                        0 - $product_qty,
                        ($product_qty > 0) ? ('Sold') : ('Returned'),
                        $invoice_id,
                        ($used) ? " (Used Stock)" : "",
                        $product_item['qty'],
                        ($used) ? "USED" : "NEW",
                        ($product_qty > 0) ? $product_qty : 0
                    ));
                    if ($used) {
                        for ($i = 0; $i < $product_qty; $i++) {
                            $DB->query(sprintf(
                                "UPDATE %s SET `stock_left` = `stock_left` - 1 WHERE `stock_left` > 0 AND `product_id` = %d AND `sub_id` = %d ORDER BY `stock_type` DESC,  `id` ASC LIMIT 1",
                                $new_inventory_table,
                                $product_id,
                                $sub_id
                            ));
                        }
                    } else {
                        for ($i = 0; $i < $product_qty; $i++) {
                            $DB->query(sprintf(
                                "UPDATE %s SET `stock_left` = `stock_left` - 1 WHERE `stock_left` > 0 AND `product_id` = %d AND `sub_id` = %d AND `stock_type` = 'NEW' ORDER BY `id` ASC LIMIT 1",
                                $new_inventory_table,
                                $product_id,
                                $sub_id
                            ));
                        }
                    }

                    //Send Notifications
                    if ($product_item['qty'] <= $product_item['alert_level'] && $product_item['alert_level'] > 0) {
                        if (function_exists('notifyActionMade')) {
                            notifyActionMade(NOTIFY_PRODUCT_INVENTORY_LOW, $invoice['site_id'], array(
                                '[PRODUCT_ID]' => $product_id,
                                '[SUB_ID]'     => $sub_id,
                                '[STOCK]'      => $product_item['qty'],
                            ));
                        }
                    }

                    //Update cost pricing
                    //productUpdateCostPrice($product_str);
                    //productUpdateAverageCostPrice($product_str);

                    if ($product_item['qty'] <= 0) {
                        /* Update availability */
                        if ('active' == $product_item['when_sold_out']) {
                            $DB->query(sprintf(
                                "UPDATE %s SET `availability` = 11 WHERE `id` = %d%s AND `availability` = 1 LIMIT 1",
                                $new_product_table,
                                $product_id,
                                ('`hg_products`.`products`' === $new_product_table) ? sprintf(
                                    " AND `sub_id` = %d",
                                    $sub_id
                                ) : ''
                            ));
                        } else {
                            /* Update active status */
                            $DB->query(sprintf(
                                "UPDATE %s SET `active_id` = '' WHERE `id` = %d%s LIMIT 1",
                                $new_product_table,
                                $product_id,
                                ('`hg_products`.`products`' === $new_product_table) ? sprintf(
                                    " AND `sub_id` = %d",
                                    $sub_id
                                ) : ''
                            ));
                        }
                    }
                }
            }
        }

        //Log changes
        if (count($reserve_change) || count($deduct_change)) {
            $message = array(
                'invoice_id'      => $invoice_id,
                'message_sender'  => $_GLOBALS['staff']['id'],
                'message_sentto'  => 'Invoice System',
                'message_type_id' => 4,
                'message_title'   => 'Product Movement',
                'message_notes'   => array(),
            );
            foreach ($reserve_change as $product_str => $product_qty) {
                $product_id = 0;
                $sub_id     = 0;
                extract(productStrSplit($product_str, 2));

                if ($product_qty > 0) {
                    $message['message_notes'][] = 'Reserved: ' . $product_qty . ' x Product ' . productFormatNumber($product_id, $sub_id);
                } else {
                    $message['message_notes'][] = 'Unreserved: ' . (0 - $product_qty) . ' x Product ' . productFormatNumber($product_id, $sub_id);
                }
            }
            foreach ($deduct_change as $product_str => $product_qty) {
                $product_id = 0;
                $sub_id     = 0;
                extract(productStrSplit($product_str, 2));

                if ($product_qty > 0) {
                    $message['message_notes'][] = 'Sold: ' . $product_qty . ' x Product ' . productFormatNumber($product_id, $sub_id);
                } else {
                    $message['message_notes'][] = 'Returned: ' . (0 - $product_qty) . ' x Product ' . productFormatNumber($product_id, $sub_id);
                }
            }
            $message['message_notes'] = implode("\n", $message['message_notes']);
            invoiceHistoryToDatabase($invoice_id, $message);
        }

        /*if (('processed' == $invoice['invoice_status'] || 'shipped' == $invoice['invoice_action']) && ('processed' != $old_invoice['invoice_status'] && 'shipped' != $old_invoice['invoice_action'])) {
            //now have to check if the freight selected is prepaid. If yes then need to remove from stock.
            if (isPrepaidService($invoice['ship_id'])) {
                // firstly check if the stock has already been reduced...
                $row = $DB->query_first(sprintf(
                    "SELECT `id` FROM %s WHERE `invoice_id` = %d LIMIT 1",
                    (in_array($invoice['site_id'], array(20, 26))) ? "`gdelta_gdelta`.`freight_services_inventory`" : "`highwaygroup`.`freight_services_inventory`",
                    $invoice_id
                ), MYSQLI_ASSOC);
                if (!$row) {
                    prepaidServiceReduceStock($invoice['ship_id'], 1);
                    $stock = prepaidServiceGetValue($invoice['ship_id'], 'qty');

                    // now need to add to inventory..
                    $DB->query(sprintf(
                        "INSERT INTO %s (`date`, `product_id`, `movement`, `comments`, `available`, `unit_cost`, `unit_currency`, `stock_left`, `stock_reserved`, `invoice_id`) VALUES (NOW(), %d, %d, %s, %d, %s, %s, %d, %d, %d)",
                        (in_array($invoice['site_id'], array(20, 26))) ? "`gdelta_gdelta`.`freight_services_inventory`" : "`highwaygroup`.`freight_services_inventory`",
                        $invoice['ship_id'],
                        -1,
                        formatSQLString("Sold with invoice " . invoiceFormatNumber($invoice_id) . (("" != $invoice["proforma_tracknumber"]) ? (" (Tracking# " . $invoice["proforma_tracknumber"] . ")") : "")),
                        $stock,
                        $invoice['ship_costau'],
                        "'AUD'",
                        $stock - 1,
                        0,
                        $invoice_id
                    ));

                    //now need to update the prepaid service.
                    $DB->query(sprintf(
                        "UPDATE %s SET `last_sold` = NOW(), `total_sales` = `total_sales` + 1 WHERE `id` = %d LIMIT 1",
                        (in_array($invoice['site_id'], array(20, 26))) ? "`gdelta_gdelta`.`freight_services`" : "`highwaygroup`.`freight_services`",
                        $invoice['ship_id']
                    ));
                }
            }

            $txt_products = array();
            foreach ($invoice_products as $product) {
                $txt_products[] = $product['product_qty'] . 'x ' . $product['product_name'];
            }

            $export = array(
                //'ship_via' => $invoice['ship_id'],
                'tracking' => $invoice['proforma_tracknumber'],
                'type'     => 'e',
            );
            if (-1 == $invoice['ship_id']) {
                $export = array(
                    'ship_via'   => $invoice['ship_id'],
                    'invoice_id' => $invoice_id,
                    'type'       => 'e',
                );
            }
            if ('' != $export['tracking'] && count(exportSearch($export)) == 0) {
                $export = array(
                    'customer'      => $invoice['bill_name'],
                    'ship_via'      => $invoice['ship_id'],
                    'ship_via_name' => $invoice['ship_product_name'],
                    'tracking'      => $invoice['proforma_tracknumber'],
                    'reference'     => 'Invoice ' . invoiceFormatNumber($invoice_id, $invoice['revision_id'], $invoice['site_id']),
                    'product'       => implode("\n", $txt_products),
                    'notes'         => 'Auto generated from processed invoice',
                    'last_modified' => formatDateTime(),
                    'last_staff'    => $invoice['ship_staff_id'],
                    'invoice_id'    => $invoice_id,
                );
                //Fix for crappy function:
                $export = array('action' => 'do_add', 'export' => $export);
                exportToDatabase($export);
            }
        }*/

        if ('shipped' == $invoice['invoice_action'] && 'shipped' != $old_invoice['invoice_action']) {
            if (function_exists('notifyActionMade')) {
                notifyActionMade(
                    NOTIFY_INVOICE_SHIPPED,
                    $invoice['site_id'],
                    invoiceTemplateVars(
                        $invoice,
                        $invoice_products,
                        $invoice_charges
                    )
                );
            }
        }

        if ('shipping' == $invoice['invoice_action'] && 'shipping' != $old_invoice['invoice_action']) {
            if (function_exists('notifyActionMade')) {
                notifyActionMade(
                    NOTIFY_INVOICE_AWAITING_SHIPPING,
                    $invoice['site_id'],
                    invoiceTemplateVars(
                        $invoice,
                        $invoice_products,
                        $invoice_charges
                    )
                );
            }
        }

        $amount          = sprintf('%.2f', $posted['payment_made']);
        $payment_options = invoiceGetOptionList("payment_options");
        if (0.00 != $amount) {
            $pay_txt = ('' != $posted['payment_notes']) ? $posted['payment_notes'] : $payment_options[$posted['payment_options']];
            $payment = array(
                'invoice_id'       => $invoice_id,
                'staff_id'         => $_GLOBALS['staff']['id'],
                'payment_time'     => time(),
                'payment_text'     => $pay_txt,
                'payment_amountau' => convertCurrency($amount, $invoice['invoice_currency'], 'AUD'),
                'payment_amountus' => convertCurrency($amount, $invoice['invoice_currency'], 'USD'),
                'payment_amount'   => $amount,
                'payment_currency' => $invoice['invoice_currency'],
            );
            invoicePaymentToDatabase($invoice_id, $payment);
        }

        $email_attach = array();
        if (is_array($posted['attachment_flag'])) {
            foreach ($posted['attachment_flag'] as $key => $value) {
                if ('' != $value) {
                    $email_attach[] = $key;
                }
            }
        }

        if (array_key_exists('flag_email_customer', $posted) && is_array($posted['flag_email_customer']) && count($posted['flag_email_customer']) > 0) {
            $job = array('invoice_id' => $invoice_id, 'job_type' => 1);

            $email_cc = array();
            foreach ($posted['flag_email_customer'] as $key => $value) {
                if ('' != $value && 'cc' == $posted['flag_email_bcc'][$key]) {
                    $email_cc[] = $posted['email_customer'][$key];
                }
            }

            $email_bcc = array();
            foreach ($posted['flag_email_customer'] as $key => $value) {
                if ('' != $value && 'cc' != $posted['flag_email_bcc'][$key]) {
                    $email_bcc[] = $posted['email_customer'][$key];
                }
            }

            if (count($email_cc)) {
                $email_to = array_shift($email_cc);
            } else {
                $email_to = array_shift($email_bcc);
            }

            $content   = array();
            $content[] = "To: " . $email_to;
            if (count($email_cc) > 0) {
                $content[] = "Cc: " . implode(', ', $email_cc);
            }

            if (count($email_bcc) > 0) {
                $content[] = "Bcc: " . implode(', ', $email_bcc);
            }

            $content[] = "Subject: " . $posted['email_title_customer'];
            if ('' != $posted['flag_attachments_customer'] && count($email_attach) > 0) {
                $content[] = "Attachments: " . implode(', ', $email_attach);
            }

            if ($posted['flag_quotation_customer']) {
                $content[] = "Invoice: attached";
            }

            if ($posted['flag_creditform_customer']) {
                $content[] = "CreditForm: attached";
            }
            if ($posted['flag_delivery_customer']) {
                $content[] = "DocumentInvoice: attached";
            }

            if ($posted['flag_dummy_customer']) {
                $content[] = "DummyInvoice: attached";
            }

            if ($posted['flag_checklist_customer']) {
                $content[] = "Checklist: attached";
            }

            $content[] = "";
            $content[] = $posted['email_message_customer'];
            if ($posted['flag_reply_customer'] && '' != $posted['email_received_customer']) {
                $content[] = "";
                foreach (explode("\n", $posted['email_received_customer']) as $row) {
                    while (strlen($row)) {
                        $content[] = trim("> " . substr($row, 0, 60));
                        $row       = trim(substr($row, 60));
                    }
                }
            }
            $job['job_content'] = implode("\n", $content);
            invoiceJobToDatabase($invoice_id, $job);
        }
        if ('' != $posted['email_received_customer']) {
            $message = array(
                'invoice_id'      => $invoice_id,
                'message_sender'  => $invoice['bill_name'],
                'message_sentto'  => $all_staff_list[$_GLOBALS['staff']['id']],
                'message_type_id' => 3,
                'message_title'   => 'Email From Customer',
                'message_notes'   => $posted['email_received_customer'],
            );
            invoiceHistoryToDatabase($invoice_id, $message);
        }

        if (is_array($posted['email_target_staff']) && '' != $posted['email_message_staff']) {
            $email_list = array();
            foreach ($posted['email_target_staff'] as $to_staff_id) {
                if (0 != $to_staff_id) {
                    $staff_info = staffFromDatabase($to_staff_id);
                    if (trim($staff_info['email']) != '') {
                        $email_list[] = trim($staff_info['email']);
                    }
                }
            }

            $email_list = array_unique($email_list);
            $staff_info = $_GLOBALS['staff'];

            $email           = new PHPMailer();
            $email->Sender   = $staff_info['email'];
            $email->From     = $staff_info['email'];
            $email->FromName = $all_staff_list[$_GLOBALS['staff']['id']];
            $email->Mailer   = "sendmail";
            $email->CharSet  = "utf-8";
            $email->Subject  = "RE:Invoice " . invoiceFormatNumber($invoice_id, $invoice['revision_id'], $invoice['site_id']);
            $email->Body     = nl2br(htmlspecialchars($posted['email_message_staff'])) . '<br /><br /><br /><a href="' . htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . '/webadmin/invoice/?action=edit&invoice_id=' . $invoice_id) . '" target="_blank">View Invoice</a>';
            $email->IsHTML(true);
            foreach ($email_list as $mailto) {
                $email->AddAddress($mailto);
            }

            $result = $email->Send();
            $email->ClearAddresses();
            $email->ClearAttachments();

            $message = array(
                'invoice_id'      => $invoice_id,
                'message_sender'  => $_GLOBALS['staff']['id'],
                'message_sentto'  => implode(',', $email_list),
                'message_type_id' => 5,
                'message_title'   => $email->Subject,
                'message_notes'   => $posted['email_message_staff'],
            );
            invoiceHistoryToDatabase($invoice_id, $message);

            unset($email);
        }

        if ('' != $posted['admin_notes']) {
            $message = array(
                'invoice_id'      => $invoice_id,
                'message_sender'  => $_GLOBALS['staff']['id'],
                'message_sentto'  => 'Invoice System',
                'message_type_id' => 4,
                'message_title'   => 'Admin Note Added',
                'message_notes'   => $posted['admin_notes'],
            );
            invoiceHistoryToDatabase($invoice_id, $message);
        }
        $posted['invoice_id'] = $invoice_id;

        $action = 'edit';
        unset($posted['skip_job']);
        break;
    case 'reset-packaging':
        invoicePackingInformationDelete($invoice_id);
        $action = 'edit';
        break;    
    case 'purchase_preview':
        include __DIR__ . '/templates/invoice_purchase_preview.php';
        die();
    case 'lock':
        invoiceSetLock($invoice_id, $_GLOBALS['staff']['id']);
        $action = 'list';
        break;

    case 'unlock':
        invoiceSetLock($invoice_id);
        $action = 'list';
        break;

    case 'update_tax_vals':
        $pass = array();
        $fail = array();
        if (is_array($posted["invoices"])) {
            foreach ($posted["invoices"] as $invoice) {
                $invoice_id = $invoice["invoice_id"];
                $old_id     = invoiceGetValue($invoice_id, "tax_id");
                $old_date   = invoiceGetValue($invoice_id, "tax_date");
                $new_id     = strval(intval($invoice["tax_id"]));
                $new_date   = ("" == $invoice["tax_date"]) ? null : $invoice["tax_date"];

                if ($new_id !== $old_id || $new_date !== $old_date) {
                    $valid_id   = preg_match("/^(0*[1-9][0-9]*)?$/", $invoice["tax_id"]);
                    $valid_date = preg_match("/^(2[0-9]{3}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]))?$/", $invoice["tax_date"]);
                    if ($valid_id && $valid_date) {
                        invoiceSetValue($invoice_id, "tax_id", $new_id);
                        invoiceSetValue($invoice_id, "tax_date", $new_date);
                        $pass[] = intval($invoice_id);
                    } else {
                        $fail[] = array(intval($invoice_id), intval($new_id), $new_date);
                    }
                }
            }
        }

        $vars       = array();
        $str        = explode("#", $_SERVER["HTTP_REFERER"]);
        $str        = explode("?", reset($str));
        $return_url = array_shift($str);
        if (count($str)) {
            parse_str(implode("?", $str), $vars);
        }

        $vars["p"] = base64_url_encode(json_encode($pass));
        $vars["f"] = base64_url_encode(json_encode($fail));
        $return_url .= '?' . http_build_query($vars);

        header("Location: " . $return_url);
        die();
}

if ($create_purchase) {
    $invoice              = invoiceFromDatabase($invoice_id);
    $products_by_supplier = array();
    foreach (invoiceProductsFromDatabase($invoice_id) as $product) {
        $products_by_supplier[intval($product['supplier_id'])][] = $product;
    }

    foreach ($products_by_supplier as $supplier_id => $products_list) {
        $supplier      = supplierFromDatabase($supplier_id, true);
        $contact_name  = $supplier['contact']['name'];
        $contact_email = $supplier['contact']['email'];
        $contact_phone = $supplier['contact']['phone'];
        $sup_defaults  = reset($products_list);
        foreach ($products_list as $product) {
            foreach ($product as $key => $value) {
                if (0 != $value && $sup_defaults[$key] !== $value) {
                    unset($sup_defaults[$key]);
                }
            }
        }

        $porder = array(
            'action'   => 'do_add',
            'order'    => array(
                'invoice_id'   => $invoice_id,
                'buyer'        => $invoice['site_id'],
                'supplier_id'  => $supplier_id,
                'supplier'     => $supplier['supplier_name'],
                'name'         => $contact_name,
                'attention_to' => $contact_name,
                'email'        => $contact_email,
                'phone'        => $contact_phone,
                'address'      => $supplier['address'],
                'country'      => $supplier['country'],
                'currency'     => 'AUD',
                'reference'    => 'For Invoice ' . invoiceFormatNumber($invoice_id, $invoice['revision_id'], $invoice['site_id']),
                'terms'        => $sup_defaults['product_terms'],
                'voltage'      => $sup_defaults['product_voltage'],
                'plug'         => $sup_defaults['product_plug'],
                'packing'      => $sup_defaults['product_packing'],
                'del_date'     => '1970-01-01',
                'status'       => 'approval',
            ),
            'products' => array(),
            'shipping' => array(),
            'status'   => array(
                'pay_authorised' => 'n',
                'pay_made'       => '1',
                'goods_received' => 'n',
                'checked'        => 'n',
            ),
        );
        $total = 0.0;
        foreach ($products_list as $product) {
            $porder['products'][] = array(
                'product_id'          => $product['product_id'],
                'add'                 => 'add',
                'discount'            => 'n',
                'generic'             => (0 != $product['product_id']) ? 'n' : 'y',
                'product_name'        => $product['product_name'],
                'qty'                 => $product['product_qty'],
                'notes'               => $product['supplier_notes'],
                'sup_product_id'      => $product['product_id'],
                'sup_product_name'    => $product['product_name'],
                'sup_qty'             => $product['product_qty'],
                'price'               => $product['product_costau'],
                'sup_price'           => $product['product_costau'],
                'total'               => $product['product_costau'] * $product['product_qty'],
                'total_usd'           => $product['product_costau'] * $product['product_qty'],
                'sup_total'           => $product['product_costau'] * $product['product_qty'],
                'sup_total_usd'       => $product['product_costau'] * $product['product_qty'],
                'currency'            => 'AUD',
                'hidden_currency'     => 'AUD',
                'sup_currency'        => 'AUD',
                'sup_hidden_currency' => 'AUD',
            );
            $total += $product['product_costau'] * $product['product_qty'];
        }
        $porder['status']['pay_outstanding'] = $total;
        orderToDatabase($porder);
    }

    $action = 'purchase_orders';
    //$action = 'edit';
}

$get_vars  = $posted;
$keep_only = array('invoice_id', 'newsletter_id', 'visitor_id', 'status_id', 'site_id', 'order_by', 'search_for', 'page', 'order', 'status', 'handler', 'return_message', 'payments_located', 'payments_made', 'invoice_action', 'member_id', 'invoice_time', 'label_type', 'invoice_list', 'format', 'prod_string', 'no_sig', 'tax_month', 'tax_year', 'tax_fyear', 'supplier_id', 'report_type', 'products', 'charges', 'shipping','invoice_time');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (!in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

//Redirect Actions
switch ($action) {
    case 'json_lock':
        $invoice_lock = invoiceGetLock($invoice_id);
        if (empty($invoice_lock) || $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            invoiceSetLock($invoice_id, $_GLOBALS['staff']['id']);
            $invoice_lock = invoiceGetLock($invoice_id);
        }
        header('Content-Type: text/javascript');
        jecho($invoice_lock);
        die();

    case 'add':
        unset($get_vars['invoice_id']);
        header('Location: invoice_add.php?' . http_build_query($get_vars));
        break;

    case 'edit':
        /**
         * Redirect to appropriate webadmin if required by site
         */
        $test_site_id = invoiceGetValue($invoice_id, "site_id");
        if ($test_site_id != $_GLOBALS["site_settings"]["site_id"]) {
            if (3 == $test_site_id) {
                header("Location: https://www.coinopexpress.com" . $_SERVER["REQUEST_URI"]);
                die();
            } elseif (20 == $test_site_id) {
                header("Location: https://www.arcadespareparts.com" . $_SERVER["REQUEST_URI"]);
                die();
            } elseif (26 == $test_site_id) {
                header("Location: https://www.factoryclearancehouse.com" . $_SERVER["REQUEST_URI"]);
                die();
           
            } elseif (39 == $test_site_id) {
                header("Location: https://www.arcooda.com" . $_SERVER["REQUEST_URI"]);
                die();
            
            } elseif (in_array($_GLOBALS["site_settings"]["site_id"], array(20, 26, 39))) {
                header("Location: https://www.highwaygroup.net" . $_SERVER["REQUEST_URI"]);
                die();
            }
        }

        $invoice_lock = invoiceGetLock($invoice_id);
        if (empty($invoice_lock) || $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            invoiceSetLock($invoice_id, $_GLOBALS['staff']['id']);
            if (isset($posted['process_job'])) {
                $job_info = invoiceJobFromDatabase(intval($posted['process_job']));
                if (1 == $job_info['job_type'] && $job_info['invoice_id'] == $invoice_id) {
                    $invoice = invoiceFromDatabase($invoice_id);
                    if ($invoice['site_id'] <= 0) {
                        $invoice['site_id'] = $_GLOBALS['site_settings']['site_id'];
                    }

                    $headers = array();
                    $content = explode("\n", $job_info['job_content']);
                    while (($row = array_shift($content)) != '') {
                        $row                              = explode(':', $row);
                        $headers[trim(array_shift($row))] = trim(implode(':', $row));
                    }

                    if ('' != $posted['job_subject']) {
                        $headers['Subject'] = $posted['job_subject'];
                    }

                    $staff_info  = $_GLOBALS['staff'];
                    $staff_email = staffEmailFromDatabase($_GLOBALS['staff']['id'], $invoice['site_id']);
                    if ('' == $staff_email) {
                        $staff_email = $staff_info['email'];
                    }

                    $email = new PHPMailer();

                    $email->Sender   = $staff_email;
                    $email->From     = $staff_email;
                    $email->FromName = trim($staff_info['first_name'] . ' ' . $staff_info['last_name']);

                    $email->Mailer  = "sendmail";
                    $email->CharSet = "utf-8";
                    $email->Subject = $headers['Subject'];
                    $email->Body    = $posted['job_content'];
                    $email->IsHTML(substr(trim($email->Body), 0, 1) == '<');

                    //Add Addresses
                    $email->AddAddress($headers['To']);
                    $site_emails = array();
                    if ($query = $DB->query(sprintf(
                        "SELECT `id`, `website`, `sales_email` FROM `highwaygroup`.`websites` ORDER BY `id` ASC"
                    ))) {
                        while ($row = $DB->fetch_assoc($query)) {
                            $site_emails[$row['id']] = array($row['website'], $row['sales_email']);
                        }
                        $DB->free_result($query);
                    }

                    if (array_key_exists($invoice["site_id"], $site_emails)) {
                        $email->AddBcc($site_emails[$invoice["site_id"]][1], $site_emails[$invoice["site_id"]][0]);

                        $email->Sender = $site_emails[$invoice["site_id"]][1];
                        $email->From   = $site_emails[$invoice["site_id"]][1];
                    }

                    foreach (explode(',', $headers['Cc']) as $mailto) {
                        $email->AddCc(trim($mailto));
                    }

                    foreach (explode(',', $headers['Bcc']) as $mailto) {
                        $email->AddBcc(trim($mailto));
                    }
                    //$email->AddBcc('sales@arcooda.com');
                    if ('y' == $posted['job_receipt']) {
                        $email->ConfirmReadingTo = $staff_email;
                    }
                    //Add Attachments
                    $string_attachments = array();
                    foreach (explode(',', $headers['Attachments']) as $attach_id) {
                        $invoice_assets = assetSearch('invoice', array('invoice_id' => $invoice_id));
                        $attach_id      = intval(trim($attach_id));
                        if (in_array($attach_id, $invoice_assets)) {
                            $attach = assetToCompatible('invoice', assetFromDatabase('invoice', $attach_id));
                            $file   = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/invoice/' . $attach['file_name'];
                            if (!file_exists($file)) {
                                $file   =$_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/invoice/attachment/' . $attach['file_name'];
                            }
                            $email->AddAttachment($file, $attach['file_orig'], 'base64', $attach['file_type']);
                            $string_attachments[] = $attach['file_orig'];
                        }
                    }

                    /*
                    $notify_vars                        = invoiceTemplateVars($invoice, $invoice_products, $invoice_charges);
                    $notify_vars['[TXT_ATTACHMENTS]'][] = $email->Body;
                    */

                    if ('attached' == $headers['DummyInvoice']) {
                        $sinvoice_id          = $invoice_id;
                        $string_attachments[] = 'Dummy/Shipping Invoice';
                        $sinvoice             = shipinvoiceFromDatabase($sinvoice_id);
                        $sinvoice_products    = shipinvoiceProductsFromDatabase($sinvoice_id);
                        $invoice_charges      = shipinvoiceChargesFromDatabase($sinvoice_id);

                        $sinvoice_buffer = 0;
                        include $_SERVER['DOCUMENT_ROOT'] . '/webadmin/shipinvoice/templates/jpeg_shipinvoice.tpl.php';

                        if ($size_failure) {
                            if (function_exists('imagedestroy')) {
                                imagedestroy($sinvoice_buffer);
                            }
                            $sinvoice_buffer = false;
                            include $_SERVER['DOCUMENT_ROOT'] . '/webadmin/shipinvoice/templates/pdf_shipinvoice.tpl.php';
                            $email->AddStringAttachment($sinvoice_buffer, 'sinvoice' . str_pad($sinvoice_id, 6, '0', STR_PAD_LEFT) . '.pdf', 'base64', 'application/pdf');
                            /*
                            $notify_vars['[ATTACHMENTS]'][] = array(
                                'contents'     => $sinvoice_buffer,
                                'file_name'    => 'sinvoice' . str_pad($sinvoice_id, 6, '0', STR_PAD_LEFT) . '.pdf',
                                'content_type' => 'application/pdf',
                            );
                            */
                        } else {
                            ob_start();
                            imagegif($sinvoice_buffer);
                            if (function_exists('imagedestroy')) {
                                imagedestroy($sinvoice_buffer);
                            }
                            $sinvoice_buffer = ob_get_clean();
                            $email->AddStringAttachment($sinvoice_buffer, 'invoice' . str_pad($sinvoice_id, 6, '0', STR_PAD_LEFT) . '.gif', 'base64', 'image/gif');
                            /*
                            $notify_vars['[ATTACHMENTS]'][] = array(
                                'contents'     => $sinvoice_buffer,
                                'file_name'    => 'sinvoice' . str_pad($sinvoice_id, 6, '0', STR_PAD_LEFT) . '.gif',
                                'content_type' => 'image/gif',
                            );
                            */
                        }
                        unset($sinvoice_buffer);
                    }

                    if ('attached' == $headers['DocumentInvoice']) {

                        include $_SERVER['DOCUMENT_ROOT'] . '/webadmin/documentinvoice/functions/query.inc.php';
                        $dinvoice_id          = $invoice_id;
                        $formatted_sinvoice_number = documentinvoiceFormatNumber($dinvoice_id, ((3 == $sinvoice_type || 4 == $sinvoice_type) ? $sinvoice['revision_id'] : null), $sinvoice['site_id']);
                        $sinvoice_type_text = 'DELIVERY ORDER';
                        $string_attachments[] = 'DELIVERY ORDER';
                        $sinvoice             = documentinvoiceFromDatabase($dinvoice_id);
                        $sinvoice_products    = documentinvoiceProductsFromDatabase($dinvoice_id);
                        $invoice_charges      = documentinvoiceChargesFromDatabase($dinvoice_id);
                         ob_start();
                        include $_SERVER['DOCUMENT_ROOT'] . '/webadmin/documentinvoice/templates/pdf_documentinvoice.tpl.php';
                        $invoice_buffer = ob_get_clean();
                        $email->AddStringAttachment($invoice_buffer, 'deliveryorder' . str_pad($dinvoice_id, 6, '0', STR_PAD_LEFT) . '.pdf', 'base64', 'application/pdf');
                    }
                    if ('attached' == $headers['CreditForm']) {
                        $string_attachments[] = 'Credit Card Form';
                        $string_buffer        = file_get_contents(__DIR__ . '/creditform.pdf');
                        $email->AddStringAttachment($string_buffer, 'CreditCardForm.pdf', 'base64', 'application/pdf');
                        /*
                        $notify_vars['[ATTACHMENTS]'][] = array(
                            'contents'     => $string_buffer,
                            'file_name'    => 'CreditCardForm.pdf',
                            'content_type' => 'application/pdf',
                        );
                        */
                        unset($string_buffer);
                    }
                    if ('attached' == $headers['Invoice']) {
                        $string_attachments[] = 'Invoice';
                        $invoice_products     = invoiceProductsFromDatabase($invoice_id);
                        $invoice_charges      = invoiceChargesFromDatabase($invoice_id);
                        $invoice_gifts    = invoiceGiftsFromDatabasePrint($invoice_id);
                        $invoice_buffer = 0;
                        include __DIR__ . '/templates/jpeg_invoice.tpl.php';
                        if ($size_failure || 'y' == $posted['attach_pdf']) {
                            if (function_exists('imagedestroy')) {
                                imagedestroy($invoice_buffer);
                            }
                            $invoice_buffer = false;
                            $invoice_pre_name= (substr($invoice['invoice_number'], 0, 3)=='QAR')?'inquiry_':'invoice_';

                            include __DIR__ . '/templates/pdf_invoice.tpl.php';
                            $email->AddStringAttachment($invoice_buffer, $invoice_pre_name . $invoice['invoice_number']. '.pdf', 'base64', 'application/pdf');
                            /*
                            $notify_vars['[ATTACHMENTS]'][] = array(
                                'contents'     => $invoice_buffer,
                                'file_name'    => 'invoice' . str_pad($invoice_id, 6, '0', STR_PAD_LEFT) . '.pdf',
                                'content_type' => 'application/pdf',
                            );
                            */
                        } else {
                            ob_start();
                            imagegif($invoice_buffer);
                            if (function_exists('imagedestroy')) {
                                imagedestroy($invoice_buffer);
                            }
                            $invoice_buffer = ob_get_clean();
                            $email->AddStringAttachment($invoice_buffer, 'invoice' . str_pad($invoice_id, 6, '0', STR_PAD_LEFT) . '.gif', 'base64', 'image/gif');
                            /*
                            $notify_vars['[ATTACHMENTS]'][] = array(
                                'contents'     => $invoice_buffer,
                                'file_name'    => 'invoice' . str_pad($invoice_id, 6, '0', STR_PAD_LEFT) . '.gif',
                                'content_type' => 'image/gif',
                            );
                            */
                        }
                        unset($invoice_buffer);
                    }
                    /*
                    if($headers['Invoice']=='attached')
                    {
                    $invoice_products=invoiceProductsFromDatabase($invoice_id);
                    $invoice_charges=invoiceChargesFromDatabase($invoice_id);

                    $invoice_buffer=0;
                    include __DIR__ . '/templates/jpeg_invoice.tpl.php';
                    ob_start();
                    imagegif($invoice_buffer);
                    $invoice_gif=ob_get_clean();
                    if (function_exists('imagedestroy')) {
                    imagedestroy($invoice_buffer);
                    }
                    $invoice_buffer = false;

                    $email->AddStringAttachment($invoice_gif, 'invoice'.str_pad($invoice_id,6,'0',STR_PAD_LEFT).'.gif', 'base64', 'image/gif');
                    $notify_vars['[ATTACHMENTS]'][] = array(
                    'contents' => $invoice_gif,
                    'file_name' => 'invoice' . str_pad($invoice_id, 6, '0', STR_PAD_LEFT) . '.gif',
                    'content_type' => 'image/gif',
                    );
                    unset($invoice_gif);
                    }
                     */

                    //$email->addBCC('backup@highwaygroup.net');

                    if (function_exists('notifyActionRecipients')) {
                        foreach (notifyActionRecipients(
                            NOTIFY_GENERIC,
                            $invoice['site_id']
                        ) as $email_address => $email_name) {
                            $email->addBCC($email_address, $email_name);
                        }
                    }

                    $result = $email->Send();

                    /*
                    $notify_vars['[EMAIL_SUBJECT]'] = $email->Subject;
                    $notify_vars['[EMAIL_BODY]']    = $email->Body;
                    if (function_exists('notifyActionMade')) {
                        notifyActionMade(
                            NOTIFY_GENERIC,
                            $invoice['site_id'],
                            $notify_vars
                        );
                    }
                    unset($notify_vars);
                    */

                    $message_notes = $posted['job_content'];
                    if (count($string_attachments)) {
                        $message_notes .= "\n\nAttachments with this email : " . implode(', ', $string_attachments);
                    }

                    $message = array(
                        'invoice_id'      => $invoice_id,
                        'message_sentto'  => $invoice['bill_name'],
                        'message_sender'  => $all_staff_list[$_GLOBALS['staff']['id']],
                        'message_type_id' => 2,
                        'message_title'   => $headers['Subject'],
                        'message_notes'   => $message_notes,
                    );

                    $to = array();
                    foreach (explode(',', $headers['To']) as $mailto) {
                        $to[] = trim($mailto);
                    }

                    foreach (explode(',', $headers['Cc']) as $mailto) {
                        $to[] = trim($mailto);
                    }

                    foreach (explode(',', $headers['Bcc']) as $mailto) {
                        $to[] = trim($mailto);
                    }

                    $message["message_sentto"] = trim(implode(",", array_unique($to)), ",");

                    invoiceHistoryToDatabase($invoice_id, $message);
                    invoiceSetValue($invoice_id,'write_date',date('Y-m-d H:i:s', strtotime("+5 min")));
                    //memberSetValue($invoice['member_id'], '', formatDateTime());
                    $email->ClearAttachments();
                    $email->ClearAddresses();
                    $DB->query(sprintf(
                        "DELETE FROM `highwaygroup`.`invoice_jobs` WHERE `job_id` = %d LIMIT 1",
                        $posted['process_job']
                    ));
                } else {
?>
Unable to process job, possible reasons:<br />
<ul>
	<li>This job has already been processed, possibly in another window</li>
</ul>
<?php
die();
                }
            }
            if (isset($posted['delete_job'])) {
                $DB->query(sprintf(
                    "DELETE FROM `highwaygroup`.`invoice_jobs` WHERE `job_id` = %d LIMIT 1",
                    $posted['delete_job']
                ));
            }

            $invoice_jobs = invoiceJobsFromDatabase($invoice_id);
            if (count($invoice_jobs) > 0) {
                $job_id = reset($invoice_jobs);
                if ($posted['skip_job'] > 0) {
                    $find_id = array_search($posted['skip_job'], $invoice_jobs);
                    if (false !== $find_id) {
                        $job_id = $invoice_jobs[$find_id + 1];
                    }
                }
                if ($job_id > 0) {
                    $get_vars['job_id'] = $job_id;
                    header('Location: invoice_job.php?' . http_build_query($get_vars));
                    break;
                }
            }
        }

        unset($get_vars["site_id"]);
        header('Location: invoice_edit.php?' . http_build_query($get_vars));
        break;

    case 'history':
        header('Location: invoice_history.php?' . http_build_query($get_vars));
        break;

    case 'tax_view':
        $get_vars["tax_view"] = 1;
        //no break
    case 'summary':
        //no break
    case 'view':
        header('Location: invoice_view.php?' . http_build_query($get_vars));
        break;

    case 'shipview':
        header('Location: invoice_shipview.php?' . http_build_query($get_vars));
        break;

    case 'shiplabel':
        header('Location: invoice_shiplabel.php?' . http_build_query($get_vars));
        break;

    case 'search':
        header('Location: invoice_search.php?' . http_build_query($get_vars));
        break;

    case 'select':
        header('Location: invoice_select.php?' . http_build_query($get_vars));
        break;

    case 'purchase':
        header('Location: invoice_purchase.php?' . http_build_query($get_vars));
        break;

    case 'purchase_list':
        header('Location: invoice_purchase_list.php?' . http_build_query($get_vars));
        break;

    case 'purchase_orders':
        header('Location: /webadmin/orders/?invoice_id=' . $get_vars['invoice_id']);
        break;

    case 'copy':
        $get_vars['copy_id'] = $get_vars['invoice_id'];
        unset($get_vars['invoice_id']);
        if (20 != $_GLOBALS['site_settings']['site_id'] && invoiceGetValue($get_vars['copy_id'], 'site_id') == 20) {
            header('Location: https://www.arcadespareparts.com/webadmin/invoice/invoice_copy.php?' . http_build_query($get_vars));
        } elseif (26 != $_GLOBALS['site_settings']['site_id'] && invoiceGetValue($get_vars['copy_id'], 'site_id') == 26) {
            header('Location: https://www.factoryclearancehouse.com/webadmin/invoice/invoice_copy.php?' . http_build_query($get_vars));
        /*
        } elseif (39 != $_GLOBALS['site_settings']['site_id'] && invoiceGetValue($get_vars['copy_id'], 'site_id') == 39) {
            header('Location: https://www.arcooda.com/webadmin/invoice/invoice_copy.php?' . http_build_query($get_vars));
        */
        } else {
            header('Location: invoice_copy.php?' . http_build_query($get_vars));
        }
        break;

    case 'tax_fyear':
        if (!array_key_exists("tax_fyear", $get_vars)) {
            $get_vars["tax_fyear"] = time();
        }

        header('Location: invoice_tax_list.php?' . http_build_query($get_vars));
        break;

    case 'tax_report':
        switch ($posted["report_type"]) {
            case "i":
                header('Location: invoice_tax_report.php?' . http_build_query($get_vars));
                break;

            case "e":
                header('Location: invoice_tax_print.php?' . http_build_query($get_vars));
                break;

            default:
                header('Location: invoice_tax_custom_report.php?' . http_build_query($get_vars));
                break;
        }
        break;

    case 'tax_supplier':
        header('Location: invoice_tax_supplier.php?' . http_build_query($get_vars));
        break;

    case 'tax_month':
        header('Location: invoice_tax_summary_month.php?' . http_build_query($get_vars));
        break;

    case 'tax_year':
        header('Location: invoice_tax_summary_year.php?' . http_build_query($get_vars));
        break;

    case 'tax_payment':
    case 'tax_payment_month':
        header('Location: invoice_tax_payment_month.php?' . http_build_query($get_vars));
        break;

    case 'tax_payment_year':
        header('Location: invoice_tax_payment_year.php?' . http_build_query($get_vars));
        break;

    case 'tax_list':
    case 'unallocated':
        header('Location: invoice_tax_list.php?' . http_build_query($get_vars));
        break;

    case 'tax_print':
        header('Location: invoice_tax_print.php?' . http_build_query($get_vars));
        break;
    case 'oprduct_ordered' :
      header('Location: product_ordered.php?' . http_build_query($get_vars));
        break;  
    case 'list':
    default:
        unset($get_vars['invoice_id']);
        header('Location: invoice_list.php?' . http_build_query($get_vars));
        break;
}
