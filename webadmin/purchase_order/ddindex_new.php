<?php
/**
 * @var mixed[]  $posted
 * @var string[] $all_staff_list
 */
include_once __DIR__ . "/common.inc.php";

global $DB;
global $_GLOBALS;

$action          = $posted['action'];
$invoice_id      = intval($posted['invoice_id']);
$create_purchase = false;
if ('do_purchase' == $action) {
    $create_purchase = true;
    $action          = 'do_edit';
}

//Update Actions
switch ($action) {
    case 'do_add':
        unset($posted['invoice_id']);
    case 'do_edit':
        if (count($posted) < 20) {
            $action = "list";
            break;
        }
        $change_list = array();

        /**
         * Read new invoice from the posted form
         */
        $invoice                  = invoiceFromPost($posted);
        $invoice_products         = invoiceProductsFromPost($posted);
        $invoice_charges          = invoiceChargesFromPost($posted);
        $invoice_courier_quotes   = invoiceCourierQuotesFromPost($posted);
        $invoice_forwarder_quotes = invoiceForwarderQuotesFromPost($posted);

        /**
         * Read old invoice from the database
         */
        $old_invoice          = invoiceFromDatabase($invoice_id);
        $old_invoice_products = invoiceProductsFromDatabase($invoice_id);
        $old_invoice_charges  = invoiceChargesFromDatabase($invoice_id);
        //$old_invoice_courier_quotes = invoiceCourierQuotesFromDatabase($invoice_id);
        //$old_invoice_forwarder_quotes = invoiceForwarderQuotesFromDatabase($invoice_id);

        /**
         * Check if the invoice already exists
         */
        $invoice_exists = invoiceExists($invoice_id);
        if (!$invoice_exists) {
            $change_list[] = 'New Invoice Created';
        }

        /**
         * Check if we need to save a revision of the invoice
         */
        if ($invoice_exists && ($invoice['do_revision'] || $invoice_products['do_revision'] || $invoice_charges['do_revision'])) {
            mail('backup@arcooda.com', 'Revision Made', "Invoice:\n" . print_r($invoice, true) . "\n\nProducts:\n" . print_r($invoice_products, true) . "\n\nCharges:\n" . print_r($invoice_charges, true) . "\n");

            /**
             * Generate a new asset
             */
            $asset_type = 'invoice';
            $table_info = assetTableInfoFromType($asset_type);
            $asset      = array(
                'invoice_id'    => $invoice_id,
                'staff_id'      => $_GLOBALS['staff']['id'],
                'asset_caption' => 'Proforma ' . invoiceFormatNumber($invoice_id, $old_invoice['revision_id'], $old_invoice['site_id']),
            );
            $asset['asset_id'] = intval(assetToDatabase($asset_type, assetFromCompatible($asset_type, $asset)));

            /**
             * Save a copy of the revision in the new asset
             */
            $invoice_buffer = 0;
            $size_failure   = false;
            $_STACK[]       = $invoice;
            $_STACK[]       = $invoice_products;
            $_STACK[]       = $invoice_charges;

            /**
             * @var mixed $invoice_buffer
             * @var bool  $size_failure
             */
            include __DIR__ . '/templates/jpeg_invoice.tpl.php';

            if ($size_failure) {
                imagedestroy($invoice_buffer);
                $asset['file_type'] = 'application/pdf';
                $asset['file_orig'] = formatFileSafe($asset['asset_caption'] . '.pdf');
                $asset['filename']  = assetGenerateUniqueName($asset_type, assetFromCompatible($asset_type, $asset)) . ".pdf";
                include __DIR__ . '/templates/pdf_invoice.tpl.php';
                if (($fp = fopen($table_info['directory'] . $asset['filename'], 'wb')) !== false) {
                    fwrite($fp, $invoice_buffer);
                    fclose($fp);
                }
            } else {
                $asset['file_type'] = 'image/gif';
                $asset['file_orig'] = formatFileSafe($asset['asset_caption'] . '.gif');
                $asset['filename']  = assetGenerateUniqueName($asset_type, assetFromCompatible($asset_type, $asset)) . ".gif";
                imagegif($invoice_buffer, $table_info['directory'] . $asset['filename']);
                imagedestroy($invoice_buffer);
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
            $invoice['revision_id']++;
            $change_list[] = 'New Revision Created';
        }

        $freightquotes_affected = array();
        /*
        $freightquotes_affected = explode(',', $posted['freightquote_affected']);
        foreach ($freightquotes_affected as $key => $quote_id) {
        if (intval($quote_id) == 0) {
        unset($freightquotes_affected[$key]);
        }
        }
         */

        if ('y' == $posted['process_invoice']) {
            //ie tracking number entered, so need to process the invoice and
            $invoice['invoice_status'] = $invoice['invoice_action'] = 'processed';
        }

        die('Currently undergoing repair');

        /**
         * Save invoice changes to database
         */
        $invoice_id = invoiceToDatabase($invoice);
        invoiceProductsToDatabase($invoice_id, $invoice_products);
        invoiceChargesToDatabase($invoice_id, $invoice_charges);
        invoiceCourierQuotesToDatabase($invoice_id, $invoice_courier_quotes);
        invoiceForwarderQuotesToDatabase($invoice_id, $invoice_forwarder_quotes, $freightquotes_affected);

        /**
         * Detect if a voucher was used and process it
         */
        if (0 != $posted['voucher_used']) {
            foreach ($invoice_charges as $charge) {
                if ('C22' == $charge['product_id']) {
                    $DB->query("UPDATE `gift_voucher` SET `status` = 'processed', `process_date` = " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", `invoice_id` = " . intval($invoice_id) . " WHERE `id` = " . intval($posted['voucher_used']) . " LIMIT 1");
                    $change_list[] = 'Processed Voucher #' . intval($posted['voucher_used']);
                    unset($posted['voucher_used']);
                    break;
                }
            }
        }

        /**
         * Detect changes in shipping/reservation status
         */
        $reserve_list = array();
        $ship_list    = array();
        $unship_list  = array();
        foreach ($invoice_products as $k => $v) {
            switch ($invoice['invoice_status']) {
                case 'proforma':
                case 'sales':
                    if (in_array($v['product_status'], array('quoted', 'reserved')) && in_array($v['generic'], array('n'))) {
                        $reserve_list[] = $k;
                    }

                    if ('shipped' != $invoice['invoice_action']) {
                        break;
                    }

                case 'processed':
                    $reserve_list = array();
                    if (in_array($v['product_status'], array('quoted', 'reserved')) && in_array($v['generic'], array('n', 'c'))) {
                        $ship_list[] = $k;
                    }

                    break;
                case 'cancelled':
                    if ('returned' == $invoice['invoice_action'] && 'shipped' == $v['product_status'] && in_array($v['generic'], array('n', 'c'))) {
                        $unship_list[] = $k;
                    }

                    break;
            }
        }

        /**
         * Relocate inventory according to product status
         */
        $DB->query("DELETE FROM hg_products.products_reserved WHERE invoice_id=" . intval($invoice_id) . " LIMIT 1");
        foreach ($reserve_list as $k) {
            $v = $invoice_products[$k];
            if ($v['product_qty'] > 0 && 'n' == $v['generic']) {
                $DB->query("INSERT INTO hg_products.products_reserved (invoice_id, product_id, sub_id, qty) VALUES (" . intval($invoice_id) . "," . intval($v['product_id']) . "," . intval($v['sub_id']) . "," . intval($v['product_qty']) . ")");
                if (0 != $v['adapter_id']) {
                    $DB->query("INSERT INTO hg_products.products_reserved (invoice_id, product_id, sub_id, qty) VALUES (" . intval($invoice_id) . "," . intval($v['adapter_id']) . ",0," . intval($v['product_qty']) . ")");
                }

                $invoice_products[$k]['product_status'] = 'reserved';
            }
        }
        foreach ($ship_list as $k) {
            $v      = $invoice_products[$k];
            $avail  = productGetValue($v['product_id'], 'qty', $v['sub_id']);
            $avail2 = productGetValue($v['adapter_id'], 'qty', 0);
            if ('n' == $v['generic']) {
                $avail -= max(0, $v['product_qty']);
            }

            $freight_cost = 0.0;
            if (0.0 != $invoice['invoice_cost']) {
                $freight_cost = convertCurrency($v['product_cost'] * $invoice['ship_cost'] / $invoice['invoice_cost'], $invoice['invoice_currency']);
            }

            $freight_costus = 0.0;
            if (0.0 != $invoice['invoice_costus']) {
                $freight_costus = convertCurrency($v['product_costus'] * $invoice['ship_costus'] / $invoice['invoice_costus'], 'USD');
            }

            $freight_costau = 0.0;
            if (0.0 != $invoice['invoice_costau']) {
                $freight_costau = convertCurrency($v['product_costau'] * $invoice['ship_costau'] / $invoice['invoice_costau'], 'AUD');
            }

            if ($v['product_qty'] > 0) {
                switch ($v['site_id']) {
                    case 9:
                    case 16:
                        $DB->query("INSERT INTO hg_products.products_inventory (product_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type) VALUES (" . intval($v['product_id']) . ", " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? (0 - max(0, $v['product_qty'])) : 0) . ", 'Sold with invoice I#" . intval($invoice_id) . "', " . intval($avail) . ", " . $v['product_costus'] . ", " . $freight_costus . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ")");
                        if (0 != $v['adapter_id']) {
                            $DB->query("INSERT INTO hg_products.products_inventory (product_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type) VALUES (" . intval($v['adapter_id']) . ", " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? (0 - max(0, $v['product_qty'])) : 0) . ", 'Sold with invoice I#" . intval($invoice_id) . " (Product #" . $v['product_id'] . ")', " . intval($avail2) . ", " . $v['product_costus'] . ", " . $freight_costus . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail2) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ")");
                        }

                        break;
                    case 19:
                        $DB->query("INSERT INTO hg_products.products_inventory (product_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type) VALUES (" . intval($v['product_id']) . ", " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? (0 - max(0, $v['product_qty'])) : 0) . ", 'Sold with invoice I#" . intval($invoice_id) . "', " . intval($avail) . ", " . $v['product_costus'] . ", " . $freight_costus . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ")");
                        if (0 != $v['adapter_id']) {
                            $DB->query("INSERT INTO hg_products.products_inventory (product_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type) VALUES (" . intval($v['adapter_id']) . ", " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? (0 - max(0, $v['product_qty'])) : 0) . ", 'Sold with invoice I#" . intval($invoice_id) . " (Product #" . $v['product_id'] . ")', " . intval($avail2) . ", " . $v['product_costus'] . ", " . $freight_costus . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail2) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ")");
                        }

                        break;
                }
            }

            $DB->query("INSERT INTO hg_products.products_inventory (product_id, sub_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type, staff, currency, supplier_id, supplier) VALUES (" . intval($v['product_id']) . ", " . intval($v['sub_id']) . ", " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? (0 - max(0, $v['product_qty'])) : 0) . ", " . "'Sold with invoice I#" . intval($invoice_id) . "', " . intval($avail) . ", " . $v['product_cost'] . ", " . $freight_cost . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ", " . formatSQLString($_GLOBALS['staff']['user']) . ", " . formatSQLString($invoice['invoice_currency']) . ", " . $v['supplier_id'] . ", " . formatSQLString($v['supplier_name']) . ")");
            if (0 != $v['adapter_id']) {
                $DB->query("INSERT INTO hg_products.products_inventory (product_id, sub_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type, staff, currency, supplier_id, supplier) VALUES (" . intval($v['adapter_id']) . ", 0, " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? (0 - max(0, $v['product_qty'])) : 0) . ", " . "'Sold with invoice I#" . intval($invoice_id) . " (Product #" . productFormatNumber($v['product_id'], $v['sub_id']) . ")', " . intval($avail2) . ", " . $v['product_cost'] . ", " . $freight_cost . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail2) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ", " . formatSQLString($_GLOBALS['staff']['user']) . ", " . formatSQLString($invoice['invoice_currency']) . ", " . $v['supplier_id'] . ", " . formatSQLString($v['supplier_name']) . ")");
            }

            $invoice_products[$k]['product_status'] = 'shipped';
        }
        foreach ($unship_list as $k) {
            $v     = $invoice_products[$k];
            $avail = productGetValue($v['product_id'], 'qty', $v['sub_id']);
            $avail = productGetValue($v['adapter_id'], 'qty', 0);
            if ('n' == $v['generic']) {
                $avail += max(0, $v['product_qty']);
            }

            $freight_cost = 0.0;
            if (0.0 != $invoice['invoice_cost']) {
                $freight_cost = convertCurrency($v['product_cost'] * $invoice['ship_cost'] / $invoice['invoice_cost'], $invoice['invoice_currency']);
            }

            $freight_costus = 0.0;
            if (0.0 != $invoice['invoice_costus']) {
                $freight_costus = convertCurrency($v['product_costus'] * $invoice['ship_costus'] / $invoice['invoice_costus'], 'USD');
            }

            $freight_costau = 0.0;
            if (0.0 != $invoice['invoice_costau']) {
                $freight_costau = convertCurrency($v['product_costau'] * $invoice['ship_costau'] / $invoice['invoice_costau'], 'AUD');
            }

            if ($v['product_qty'] > 0) {
                switch ($v['site_id']) {
                    case 9:
                    case 16:
                        $DB->query("INSERT INTO hg_products.products_inventory (product_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type) VALUES (" . intval($v['product_id']) . ", " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? max(0, $v['product_qty']) : 0) . ", 'Product returned with invoice I#" . intval($invoice_id) . "', " . intval($avail) . ", " . $v['product_costus'] . ", " . $freight_costus . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ")");
                        if (0 != $v['adapter_id']) {
                            $DB->query("INSERT INTO hg_products.products_inventory (product_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type) VALUES (" . intval($v['adapter_id']) . ", " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? max(0, $v['product_qty']) : 0) . ", 'Product returned with invoice I#" . intval($invoice_id) . " (Product " . productFormatNumber($v['product_id'], $v['sub_id']) . ")', " . intval($avail2) . ", " . $v['product_costus'] . ", " . $freight_costus . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail2) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ")");
                        }

                        break;
                    case 19:
                        $DB->query("INSERT INTO hg_products.products_inventory (product_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type) VALUES (" . intval($v['product_id']) . ", " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? max(0, $v['product_qty']) : 0) . ", 'Product returned with invoice I#" . intval($invoice_id) . "', " . intval($avail) . ", " . $v['product_costus'] . ", " . $freight_costus . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ")");
                        if (0 != $v['adapter_id']) {
                            $DB->query("INSERT INTO hg_products.products_inventory (product_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type) VALUES (" . intval($v['adapter_id']) . ", " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? max(0, $v['product_qty']) : 0) . ", 'Product returned with invoice I#" . intval($invoice_id) . " (Product " . productFormatNumber($v['product_id'], $v['sub_id']) . ")', " . intval($avail2) . ", " . $v['product_costus'] . ", " . $freight_costus . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail2) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ")");
                        }

                        break;
                }
            }

            $DB->query("INSERT INTO hg_products.products_inventory (product_id, sub_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type, staff, currency, supplier_id, supplier) VALUES (" . intval($v['product_id']) . ", " . intval($v['sub_id']) . ", " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? max(0, $v['product_qty']) : 0) . ", " . "'Product returned with invoice I#" . intval($invoice_id) . "', " . intval($avail) . ", " . $v['product_cost'] . ", " . $freight_cost . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ", " . formatSQLString($_GLOBALS['staff']['user']) . ", " . formatSQLString($invoice['invoice_currency']) . ", " . $v['supplier_id'] . ", " . formatSQLString($v['supplier_name']) . ")");
            if (0 != $v['adapter_id']) {
                $DB->query("INSERT INTO hg_products.products_inventory (product_id, sub_id, date, movement, comments, available, unit_cost, unit_freight, unit_cost_au, unit_freight_au, stock_left, stock_type, staff, currency, supplier_id, supplier) VALUES (" . intval($v['adapter_id']) . ", 0, " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . (('n' == $v['generic']) ? max(0, $v['product_qty']) : 0) . ", " . "'Product returned with invoice I#" . intval($invoice_id) . " (Product " . productFormatNumber($v['product_id'], $v['sub_id']) . ")', " . intval($avail2) . ", " . $v['product_cost'] . ", " . $freight_cost . ", " . $v['product_costau'] . ", " . $freight_costau . ", " . intval($avail2) . ", " . ((2 == $v['product_condition']) ? "'USED'" : "'NEW'") . ", " . formatSQLString($_GLOBALS['staff']['user']) . ", " . formatSQLString($invoice['invoice_currency']) . ", " . $v['supplier_id'] . ", " . formatSQLString($v['supplier_name']) . ")");
            }

            $invoice_products[$k]['product_status'] = 'returned';
        }
        invoiceProductsToDatabase($invoice_id, $invoice_products);

        /**
         * Find and list all product changes
         */
        $match_array = array('product_id', 'sub_id', 'site_id', 'generic', 'product_name');
        $temp1       = $invoice_products;
        $temp2       = $old_invoice_products;
        foreach ($temp1 as $k1 => $v1) {
            foreach ($temp2 as $k2 => $v2) {
                $match = true;
                foreach ($match_array as $k => $v) {
                    if ($v1[$k] != $v2[$k]) {
                        $match = false;
                    }
                }

                if ($match) {
                    $change = array();
                    foreach ($v1 as $k => $v) {
                        if ($v1[$k] != $v2[$k]) {
                            $change[] = 'changed ' . $k . ' from: ' . formatSQLString($v2[$k]) . ' to: ' . formatSQLString($v1[$k]);
                        }
                    }

                    if (count($change)) {
                        $change_list[] = 'Product ' . implode(", ", $change);
                    }

                    unset($temp1[$k1]);
                    unset($temp2[$k2]);
                    break;
                }
            }
        }

        foreach ($temp2 as $k => $v) {
            $change_list[] = 'Product Removed: ' . implode(", ", $v);
        }

        foreach ($temp1 as $k => $v) {
            $change_list[] = 'Product Added: ' . implode(", ", $v);
        }

        if ('processed' == $invoice['invoice_status']) {
            foreach ($reserve_list as $product_str => $product_qty) {
                $product_id = 0;
                $sub_id     = 0;
                extract(productStrSplit($product_str, 2));
                $product_item = productFromDatabase($product_id, $invoice['site_id'], $sub_id);
                $available    = $product_item['qty'] - $product_qty;
                $DB->query("DELETE FROM hg_products.products_reserved WHERE invoice_id=" . intval($old_invoice['invoice_id']) . " AND product_id=" . $product_id . " AND sub_id=" . $sub_id);
                $used = (2 != $used_list[$product_id]) ? false : true;
                switch ($invoice['site_id']) {
                    case 9:
                    case 16:
                        productReduceStock($product_id, $product_qty, $used);
                        $DB->query("INSERT INTO hg_products.products_inventory (date, product_id, movement, comments, available, unit_cost, unit_cost_au, stock_type) VALUES (" . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . $product_id . ", -" . $product_qty . ", 'Sold with invoice " . invoiceFormatNumber($invoice_id) . (($used) ? " (Used Stock)" : "") . "', " . intval($available) . ", 0.0, 0.0, '" . (($used) ? "USED" : "NEW") . "')");
                        $DB->query("UPDATE convert_convert.products SET total_sales=total_sales+" . abs($product_qty) . ", last_sold=NOW() WHERE id=" . $product_id);
                        $remStock   = productGetValue($product_id, 'qty');
                        $alertLevel = $product_item['alert_level'];
                        if ($remStock <= $alertLevel && $alertLevel > 0) {
                            $vars = array('[PRODUCT_ID]' => $product_id, '[STOCK]' => $remStock);
                            notifyActionMade(NOTIFY_PRODUCT_INVENTORY_LOW, 9, $vars);
                        }
                        if ($used) {
                            $f_stk = productGetValue($product_id, 'floor_stock');
                            $rem   = $f_stk - $product_qty;
                            if ($rem < 0) {
                                $rem = 0;
                            }

                            $DB->query("UPDATE convert_convert.products SET floor_stock=" . intval($rem) . " WHERE id=$product_id");
                        }
                        break;
                    case 19:
                        productReduceStock($product_id, $product_qty, $used);
                        $DB->query("INSERT INTO hg_products.products_inventory (date, product_id, movement, comments, available, unit_cost, unit_cost_au, stock_type) VALUES ( " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . $product_id . ", -" . $product_qty . ", 'Sold with invoice " . invoiceFormatNumber($invoice_id) . (($used) ? " (Used Stock)" : "") . "', " . intval($available) . ", 0.0, 0.0, '" . (($used) ? "USED" : "NEW") . "')");
                        $remStock   = productGetValue($product_id, 'qty');
                        $alertLevel = $product_item['alert_level'];
                        if ($remStock <= $alertLevel && $alertLevel > 0) {
                            $vars = array('[PRODUCT_ID]' => $product_id, '[STOCK]' => $remStock);
                            notifyActionMade(NOTIFY_PRODUCT_INVENTORY_LOW, 19, $vars);
                        }
                        if ($used) {
                            $f_stk = productGetValue($product_id, 'floor_stock');
                            $rem   = $f_stk - $product_qty;
                            if ($rem < 0) {
                                $rem = 0;
                            }

                        }
                        break;
                    case 20:
                        productReduceStock($product_id, $product_qty, $used);
                        $DB->query("INSERT INTO spcom_spcom.products_inventory (date, product_id, movement, comments, available, unit_cost, stock_type) VALUES (" . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . $product_id . ", -" . $product_qty . ", 'Sold with invoice " . invoiceFormatNumber($invoice_id) . "', " . intval($available) . ", 0.0, 'NEW')");
                        $DB->query("UPDATE spcom_spcom.products SET total_sales=total_sales+" . abs($product_qty) . ", last_sold=NOW() WHERE id=" . $product_id);
                        $remStock   = productGetValue($product_id, 'qty'); //$product_item['qty'];
                        $alertLevel = $product_item['alert_level'];
                        if ($remStock <= $alertLevel && $alertLevel > 0) {
                            $vars = array('[PRODUCT_ID]' => $product_id, '[STOCK]' => $remStock);
                            notifyActionMade(NOTIFY_PRODUCT_INVENTORY_LOW, 20, $vars);
                        }
                        if ($used) {
                            $f_stk = productGetValue($product_id, 'floor_stock');
                            $rem   = $f_stk - $product_qty;
                            if ($rem < 0) {
                                $rem = 0;
                            }

                            $DB->query("UPDATE spcom_spcom.products SET floor_stock=" . intval($rem) . " WHERE id=$product_id");
                        }
                        break;
                    default:
                        productReduceStockHE($product_id, $sub_id, $product_qty);
                        $DB->query("INSERT INTO hg_products.products_inventory (date, product_id, sub_id, movement, comments, available, unit_cost, unit_cost_au, stock_type) VALUES (" . formatSQLDateTime($_SERVER['REQUEST_TIME']) . "," . intval($product_id) . "," . intval($sub_id) . ",-" . intval($product_qty) . "," . formatSQLString('Sold with invoice ' . invoiceFormatNumber($invoice_id)) . "," . intval($available) . ",0.0,0.0,'NEW')");
                        // now need to update the last sold date for the product.
                        $DB->query("UPDATE hg_products.products SET total_sales=total_sales+" . abs($product_qty) . ", last_sold=NOW() WHERE id=" . intval($product_id) . " AND sub_id=" . intval($sub_id));
                        $remStock = productHEGetValue($product_id, 'qty', $sub_id);
                        // Now if the qty remaining is 0 or less, de-activate it...
                        if ($remStock <= 0) {
                            $row    = $DB->query_first("SELECT * FROM hg_products.products WHERE id=" . $product_id . " AND sub_id=" . $sub_id, MYSQLI_ASSOC);
                            $active = explode(',', $row['active_id']);
                            $tkey   = array_search($invoice['site_id'], $active);
                            if (false !== $tkey) {
                                unset($active[$tkey]);
                            }

                            $active = (count($active)) ? implode(',', $active) : '';
                            $DB->query("UPDATE hg_products.products SET active='n', active_id=" . formatSQLString($active) . " WHERE id=" . $product_id . " AND sub_id=" . $sub_id . " LIMIT 1");
                        }
                        $alertLevel = $product_item['alert_level'];
                        if ($remStock <= $alertLevel && $alertLevel > 0) {
                            $vars = array('[PRODUCT_ID]' => $product_id, '[SUB_ID]' => $sub_id, '[STOCK]' => $remStock);
                            notifyActionMade(NOTIFY_PRODUCT_INVENTORY_LOW, 2, $vars);
                        }
                        if ($used) {
                            $f_stk = productGetValue($product_id, 'floor_stock');
                            $rem   = $f_stk - $product_qty;
                            if ($rem < 0) {
                                $rem = 0;
                            }

                            $DB->query("UPDATE hg_products.products SET floor_stock=" . intval($rem) . " WHERE id=$product_id AND sub_id=$sub_id");
                        }
                        break;
                }
            }

            //now have to check if the freight selected is prepaid. If yes then need to remove from stock.
            if (isPrepaidService($invoice['ship_id'])) {
                // firstly check if the stock has already been reduced...
                $row = $DB->query_first("SELECT `id` FROM `freight_services_inventory` WHERE `invoice_id` = " . $invoice_id, MYSQLI_ASSOC);
                if (!$row) {
                    prepaidServiceReduceStock($invoice['ship_id'], 1);
                    $stock = prepaidServiceGetValue($invoice['ship_id'], 'qty');
                    // now need to add to inventory..
                    $DB->query("INSERT INTO `freight_services_inventory` (`date`, `product_id`, `movement`, `comments`, `available`, `unit_cost`, `stock_left`, `stock_reserved`, `invoice_id`) VALUES (" . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", " . $invoice['ship_id'] . ", -1, 'Sold with invoice " . invoiceFormatNumber($invoice_id) . "', " . intval($stock) . ", " . $invoice['ship_offerau'] . ", 0, 0, " . intval($invoice_id) . ")");
                    //now need to update the prepaid service.
                    $DB->query("UPDATE `freight_services` SET `last_sold` = " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", `total_sales` = `total_sales` + 1 WHERE `id` = " . $invoice['ship_id']);
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
                    'last_modified' => substr(formatSQLDateTime($_SERVER['REQUEST_TIME']), 1, -1),
                    'last_staff'    => $invoice['ship_staff_id'],
                    'invoice_id'    => $invoice_id,
                );
                //Fix for crappy function:
                $export = array('action' => 'do_add', 'export' => $export);
                exportToDatabase($export);
            }
        }

        if ('shipped' == $invoice['invoice_action'] && 'shipped' != $old_invoice['invoice_action']) {
            notifyActionMade(5, $invoice['site_id'], invoiceTemplateVars($invoice, $invoice_products, $invoice_charges));
        }

        if ('shipping' == $invoice['invoice_action'] && 'shipping' != $old_invoice['invoice_action']) {
            notifyActionMade(4, $invoice['site_id'], invoiceTemplateVars($invoice, $invoice_products, $invoice_charges));
        }

        $amount          = sprintf('%.2f', $posted['payment_made']);
        $payment_options = invoiceGetOptionList("payment_options");
        if (0.00 != $amount) {
            $pay_txt = ('' != $posted['payment_notes']) ? $posted['payment_notes'] : $payment_options[$posted['payment_options']];
            $payment = array(
                'invoice_id'       => $invoice_id,
                'staff_id'         => $_GLOBALS['staff']['id'],
                'payment_time'     => $_SERVER['REQUEST_TIME'],
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

        if (is_array($posted['flag_email_customer']) && count($posted['flag_email_customer'] > 0)) {
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
                'message_type_id' => INVOICE_MESSAGE_FROM_CUSTOMER,
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
            $email->Body     = nl2br(formatHTMLSafe($posted['email_message_staff'])) . '<br /><br /><br /><a href="' . formatHTMLSafe('http://' . $_SERVER['HTTP_HOST'] . '/webadmin/invoice/index.php?action=edit&invoice_id=' . $invoice_id) . '" target="_blank">View Invoice</a>';
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
                'message_type_id' => INVOICE_MESSAGE_TO_STAFF,
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
                'message_type_id' => INVOICE_MESSAGE_ADMIN,
                'message_title'   => 'Admin Note Added',
                'message_notes'   => $posted['admin_notes'],
            );
            invoiceHistoryToDatabase($invoice_id, $message);
        }

        $action = 'edit';
        unset($posted['skip_job']);
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
$keep_only = array('invoice_id', 'newsletter_id', 'visitor_id', 'status_id', 'site_id', 'order_by', 'search_for', 'page', 'order', 'status', 'handler', 'return_message', 'payments_located', 'payments_made', 'invoice_action', 'member_id', 'invoice_time', 'label_type', 'invoice_list', 'format', 'no_sig');
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
        if (false === $invoice_lock || $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
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
        $invoice_lock = invoiceGetLock($invoice_id);
        if (false === $invoice_lock || $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            invoiceSetLock($invoice_id, $_GLOBALS['staff']['id']);
            if (isset($posted['process_job'])) {
                $job_info = invoiceJobFromDatabase(intval($posted['process_job']));
                if (1 == $job_info['job_type'] && $job_info['invoice_id'] == $invoice_id) {
                    $invoice          = invoiceFromDatabase($invoice_id);
                    $invoice_products = invoiceProductsFromDatabase($invoice_id);
                    $invoice_charges  = invoiceChargesFromDatabase($invoice_id);
                    if ($invoice['site_id'] <= 0) {
                        $invoice['site_id'] = 11;
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
                    $email->IsHTML(false);

                    //Add Addresses
                    $email->AddAddress($headers['To']);
                    $email->AddBcc('backup@arcooda.com');
                    $email->AddBcc($staff_email);
                    if (9 == $invoice['site_id']) {
                        $email->AddBcc('sales@converters.tv');
                    }

                    foreach (explode(',', $headers['Cc']) as $mailto) {
                        $email->AddCc(trim($mailto));
                    }

                    foreach (explode(',', $headers['Bcc']) as $mailto) {
                        $email->AddBcc(trim($mailto));
                    }

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
                            $email->AddAttachment($file, $attach['file_orig'], 'base64', $attach['file_type']);
                            $string_attachments[] = $attach['file_orig'];
                        }
                    }

                    $notify_vars                        = invoiceTemplateVars($invoice, $invoice_products, $invoice_charges);
                    $notify_vars['[TXT_ATTACHMENTS]'][] = $email->Body;

                    if ('attached' == $headers['DummyInvoice']) {
                        $sinvoice_id          = $invoice_id;
                        $string_attachments[] = 'Dummy/Shipping Invoice';
                        $sinvoice             = shipinvoiceFromDatabase($sinvoice_id);
                        $sinvoice_products    = shipinvoiceProductsFromDatabase($sinvoice_id);
                        $invoice_charges      = shipinvoiceChargesFromDatabase($sinvoice_id);

                        $sinvoice_buffer = 0;

                        /**
                         * @var bool $size_failure
                         * @var mixed $sinvoice_buffer
                         */
                        include $_SERVER["DOCUMENT_ROOT"] . '/webadmin/shipinvoice/templates/jpeg_shipinvoice.tpl.php';

                        if ($size_failure) {
                            imagedestroy($sinvoice_buffer);

                            /**
                             * @var bool $size_failure
                             * @var mixed $sinvoice_buffer
                             */
                            include $_SERVER["DOCUMENT_ROOT"] . '/webadmin/shipinvoice/templates/pdf_shipinvoice.tpl.php';

                            $email->AddStringAttachment($sinvoice_buffer, sprintf(
                                'sinvoice%06d.pdf',
                                $sinvoice_id
                            ), 'base64', 'application/pdf');
                            $notify_vars['[ATTACHMENTS]'][] = array(
                                'contents'     => $sinvoice_buffer,
                                'file_name'    => sprintf(
                                    'sinvoice%06d.pdf',
                                    $sinvoice_id
                                ),
                                'content_type' => 'application/pdf',
                            );
                        } else {
                            ob_start();
                            imagegif($sinvoice_buffer);
                            imagedestroy($sinvoice_buffer);
                            $sinvoice_buffer = ob_get_clean();
                            $email->AddStringAttachment($sinvoice_buffer, sprintf(
                                'invoice%06d.gif',
                                $sinvoice_id
                            ), 'base64', 'image/gif');
                            $notify_vars['[ATTACHMENTS]'][] = array(
                                'contents'     => $sinvoice_buffer,
                                'file_name'    => sprintf(
                                    'sinvoice%06d.gif',
                                    $sinvoice_id
                                ),
                                'content_type' => 'image/gif',
                            );
                        }
                        unset($sinvoice_buffer);
                    }

                    if ('attached' == $headers['CreditForm']) {
                        $string_attachments[] = 'Credit Card Form';
                        $string_buffer        = file_get_contents('/home/common/creditform.pdf');
                        $email->AddStringAttachment($string_buffer, 'CreditCardForm.pdf', 'base64', 'application/pdf');
                        $notify_vars['[ATTACHMENTS]'][] = array(
                            'contents'     => $string_buffer,
                            'file_name'    => 'CreditCardForm.pdf',
                            'content_type' => 'application/pdf',
                        );
                        unset($string_buffer);
                    }

                    if ('attached' == $headers['Invoice']) {
                        $string_attachments[] = 'Invoice';
                        $invoice_products     = invoiceProductsFromDatabase($invoice_id);
                        $invoice_charges      = invoiceChargesFromDatabase($invoice_id);

                        $invoice_buffer = 0;
                        /**
                         * @var bool  $size_failure
                         * @var mixed $invoice_buffer
                         */
                        include __DIR__ . '/templates/jpeg_invoice.tpl.php';

                        if ($size_failure || 'y' == $posted['attach_pdf']) {
                            imagedestroy($invoice_buffer);

                            /**
                             * @var bool  $size_failure
                             * @var mixed $invoice_buffer
                             */
                            include __DIR__ . '/templates/pdf_invoice.tpl.php';

                            $email->AddStringAttachment($invoice_buffer, sprintf(
                                'invoice%06d.pdf',
                                $invoice_id
                            ), 'base64', 'application/pdf');
                            $notify_vars['[ATTACHMENTS]'][] = array(
                                'contents'  => $invoice_buffer,
                                'file_name' => sprintf(
                                    'invoice%06d.pdf',
                                    $invoice_id
                                ), 'content_type' => 'application/pdf',
                            );
                        } else {
                            ob_start();
                            imagegif($invoice_buffer);
                            imagedestroy($invoice_buffer);
                            $invoice_buffer = ob_get_clean();
                            $email->AddStringAttachment($invoice_buffer, sprintf(
                                'invoice%06d.gif',
                                $invoice_id
                            ), 'base64', 'image/gif');
                            $notify_vars['[ATTACHMENTS]'][] = array(
                                'contents'  => $invoice_buffer,
                                'file_name' => sprintf(
                                    'invoice%06d.gif',
                                    $invoice_id
                                ), 'content_type' => 'image/gif',
                            );
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
                    imagedestroy($invoice_buffer);

                    $email->AddStringAttachment($invoice_gif, 'invoice'.str_pad($invoice_id,6,'0',STR_PAD_LEFT).'.gif', 'base64', 'image/gif');
                    $notify_vars['[ATTACHMENTS]'][]=array('contents'=>$invoice_gif, 'file_name'=>'invoice'.str_pad($invoice_id,6,'0',STR_PAD_LEFT).'.gif', 'content_type'=>'image/gif');
                    unset($invoice_gif);
                    }
                     */
                    $result                         = $email->Send();
                    $notify_vars['[EMAIL_SUBJECT]'] = $email->Subject;
                    $notify_vars['[EMAIL_BODY]']    = $email->Body;
                    notifyActionMade(NOTIFY_GENERIC, $invoice['site_id'], $notify_vars, false);
                    unset($notify_vars);

                    $message_notes = $posted['job_content'];
                    if (count($string_attachments)) {
                        $message_notes .= "\n\nAttachments with this email : " . implode(', ', $string_attachments);
                    }

                    $message = array(
                        'invoice_id'      => $invoice_id,
                        'message_sentto'  => $invoice['bill_name'],
                        'message_sender'  => $all_staff_list[$_GLOBALS['staff']['id']],
                        'message_type_id' => INVOICE_MESSAGE_TO_CUSTOMER,
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
                    //memberSetValue($invoice['member_id'],substr(formatSQLDateTime($_SERVER['REQUEST_TIME']),1,-1));
                    $email->ClearAttachments();
                    $email->ClearAddresses();
                    $DB->query("DELETE FROM `invoice_jobs` WHERE `job_id` = " . intval($posted['process_job']));
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
                $DB->query("DELETE FROM `invoice_jobs` WHERE `job_id` = " . intval($posted['delete_job']));
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
        header('Location: invoice_edit.php?' . http_build_query($get_vars));
        break;

    case 'history':
        header('Location: invoice_history.php?' . http_build_query($get_vars));
        break;

    case 'summary':
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

    case 'purchase_orders':
        header('Location: /webadmin/orders/?invoice_id=' . $get_vars['invoice_id']);
        break;

    case 'copy':
        $get_vars['copy_id'] = $get_vars['invoice_id'];
        unset($get_vars['invoice_id']);
        if (20 != $_GLOBALS['site_settings']['site_id'] && invoiceGetValue($get_vars['copy_id'], 'site_id') == 20) {
            header('Location: http://www.arcadespareparts.com/webadmin/invoice/invoice_copy.php?' . http_build_query($get_vars));
        } elseif (26 != $_GLOBALS['site_settings']['site_id'] && invoiceGetValue($get_vars['copy_id'], 'site_id') == 26) {
            header('Location: http://www.factoryclearancehouse.com/webadmin/invoice/invoice_copy.php?' . http_build_query($get_vars));
        } else {
            header('Location: invoice_copy.php?' . http_build_query($get_vars));
        }

        break;

    case 'list':
    default:
        unset($get_vars['invoice_id']);
        header('Location: invoice_list.php?' . http_build_query($get_vars));
}
?>
