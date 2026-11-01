<?php
/**
 * @var mixed[]  $posted
 * @var string[] $all_staff_list
 */

include_once __DIR__ . "/common.inc.php";

use PHPMailer\PHPMailer\PHPMailer;

moduleLoad('asset'); //assetTableInfoFromType, assetToDatabase, assetFromCompatible, assetGenerateUniqueName, assetSearch, assetToCompatible, assetFromDatabase
moduleLoad('invoice'); //invoiceCourierQuotesFromPost, invoiceCourierQuotesFromDatabase, invoiceHistoryToDatabase, invoiceProductsFromDatabase
moduleLoad('member'); //memberSetValue
moduleLoad('notify'); //notifyActionMade
moduleLoad('phpmailer');
moduleLoad('product');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
global $DB;
global $_GLOBALS;

$action          = $posted['action'];
$order_id      = intval($posted['order_id']);
$create_purchase = false;
if ('do_purchase' == $action) {
    $create_purchase = true;
    $action          = 'do_edit';
}

//Update Actions
switch ($action) {
    case 'do_add':
        unset($posted['order_id']);
    case 'do_edit':
        if (count($posted) < 20) {
            $action = "list";
            break;
        }
        $change_list = array();
        $posted['proforma_comments'] = (array_key_exists('proforma_comments', $posted)) ? ((is_array($posted['proforma_comments'])) ? implode(',', $posted['proforma_comments']) : $posted['proforma_comments']) : '';
        //$posted['proforma_comments'] = implode(',', $posted['proforma_comments']);

        /**
         * Read new invoice from the posted form
         */
        echo '<pre>'; print_r($posted); die;
        $order                  = purchaseorderFromPost($posted);
        $order_products         = purchaseorderProductsFromPost($posted);
        $order_charges          = purchaseorderChargesFromPost($posted);
        //$invoice_courier_quotes   = invoiceCourierQuotesFromPost($posted);
        //$invoice_forwarder_quotes = invoiceForwarderQuotesFromPost($posted);

        /**
         * Read old invoice from the database
         */
        $old_order         = purchaseOrderFromDatabase($order['order_id']);
        $old_order_products = purchaseOrderProductsFromDatabase($order['order_id']);
        $old_order_charges  = purchaseorderChargesFromDatabase($order['order_id']);
        //$old_invoice_courier_quotes = invoiceCourierQuotesFromDatabase($invoice['invoice_id']);
        //$old_invoice_forwarder_quotes = invoiceForwarderQuotesFromDatabase($invoice['invoice_id']);

        /**
         * Check if the invoice already exists
         */
        $order_exists = purchaseOrderExists($order['order_id']);
        if (!$order_exists) {
            $change_list[] = 'New Invoice Created';
        }

        /**
         * Check if we need to save a revision of the invoice
         */
        if ($order_exists && ($order['do_revision'] || $order_products['do_revision'] || $order_charges['do_revision'])) {

            /**
             * Generate a new asset
             */
            $asset_type = 'purchase_order';
            $table_info = assetTableInfoFromType($asset_type);
            $asset      = array(
                'invoice_id'    => $invoice_id,
                'staff_id'      => $_GLOBALS['staff']['id'],
                'asset_caption' => 'Proforma ' . invoiceFormatNumber($invoice_id, $old_invoice['revision_id'], $old_invoice['site_id']),
            );
            $asset['file_orig'] = formatFileSafe($asset['asset_caption'] . '.pdf');
            $asset['asset_id']  = intval(assetToDatabase($asset_type, assetFromCompatible($asset_type, $asset)));

            /**
             * Save a copy of the revision in the new asset
             */
            $invoice_buffer = null;
            $_STACK[]       = $invoice;
            $_STACK[]       = $order_products;
            $_STACK[]       = $order_charges;

            /*
            $size_failure = false;
            include __DIR__ . '/templates/jpeg_invoice.tpl.php';
            if ($size_failure)
            {
                if (function_exists('imagedestroy')) {
                    imagedestroy($invoice_buffer);
                }
                $invoice_buffer = false;
             */
            $asset['file_type'] = 'application/pdf';
            $asset['filename']  = assetGenerateUniqueName($asset_type, assetFromCompatible($asset_type, $asset)) . ".pdf";

            /**
             * @var string $invoice_buffer
             */
            include __DIR__ . '/templates/pdf_invoice.tpl.php';

            if (($fp = fopen($table_info['directory'] . $asset['filename'], 'wb')) !== false) {
                fwrite($fp, $invoice_buffer);
                fclose($fp);
            }
            /*
            }
            else
            {
                $asset['file_type'] = 'image/gif';
                $asset['filename'] = assetGenerateUniqueName($asset_type, assetFromCompatible($asset_type, $asset)) . ".gif";
                imagegif($invoice_buffer, $table_info['directory'] . $asset['filename']);
                if (function_exists('imagedestroy')) {
                    imagedestroy($invoice_buffer);
                }
                $invoice_buffer = false;
            }
             */
            unset($invoice_buffer);
            $asset['filesize'] = filesize($table_info['directory'] . $asset['filename']);
            assetToDatabase($asset_type, assetFromCompatible($asset_type, $asset));
            $order_charges  = array_pop($_STACK);
            $order_products = array_pop($_STACK);
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
            $order['order_status'] = $order['order_status'] = 'processed';
        }

        /**
         * Save invoice changes to database
         */
        if(array_key_exists('certificate', $posted)){
            $order['certificate']=implode(',', $posted['certificate']);
        }
        $order_id = $order['order_id'] = purchaseOrderToDatabase($order);
        purchaseOrderProductsToDatabase($order_id, $order_products);
        purchaseOrderChargesToDatabase($order_id, $order_charges);
        $orderImages=(array_key_exists('order_image',$posted))?$posted['order_image']:[];
        addAndUpdateOrderImage($order_id,$orderImages,2);
        $paymentImages=(array_key_exists('payment_image',$posted))?$posted['payment_image']:[];
        addAndUpdateOrderImage($order_id,$paymentImages,3);
        if($old_order['inventory_order_id']){
            deleteInventoryOrder($old_order['inventory_order_id']);
        }
        if($posted['add_to_inventory']=='y'){
            $stock             = $_REQUEST['stock'];
            $stock['order_id'] = productOrderToDatabase($stock);
            purchaseOrderSetValue($order_id, 'inventory_order_id', $stock['order_id']);
             foreach ($_REQUEST['stock_additions'] as $stock_addition) {
                $item_array=[];
                /**
                 * Find the product id
                 */
                $item_id = $stock_addition['product_id'];

                /**
                 * Get and set the before/after quantities
                 */
                $qty     = productGetValue($item_id, 'qty');
                $new_qty = $stock_addition['product_qty'] + $qty;
                productSetValue($item_id, 'qty', $new_qty);
                $item_array= array(
                    'product_id'       => $item_id,
                    'order_id'         => $stock['order_id'],
                    'movement'         => $stock_addition['product_qty'],
                    'color_id'         => $stock_addition['color_id'],
                    'comments'         => sprintf(
                        'Supplier: %s Ref: %s (Cost: %s%s %s + Freight: %s%s %s%s)%s',
                        $supplier_list[$stock['supplier_id']],
                        $stock['reference'],
                        getCurrencySymbol($stock['currency']),
                        convertCurrency($stock_addition['unit_cost'], $stock['currency']),
                        $stock['currency'],
                        getCurrencySymbol($stock['currency']),
                        convertCurrency($stock_addition['unit_freight'], $stock['currency']),
                        $stock['currency'],
                        ('USD' != $stock['currency']) ? sprintf(
                            ', Cost: $%s USD + Freight: $%s USD',
                            convertCurrency($stock_addition['unit_cost'], $stock['currency'], 'USD'),
                            convertCurrency($stock_addition['unit_freight'], $stock['currency'], 'USD')
                        ) : '',
                        ('' != $stock['comment']) ? "\n" . $stock['comment'] : ''
                    ),
                    'available'        => $new_qty,
                    'unit_cost'        => $stock_addition['unit_cost'],
                    'unit_freight'     => $stock_addition['unit_freight'],
                    'unit_cost_usd'    => convertCurrency($stock_addition['unit_cost'], $stock['currency'], 'USD'),
                    'unit_freight_usd' => convertCurrency($stock_addition['unit_freight'], $stock['currency'], 'USD'),
                    'unit_cost_aud'    => convertCurrency($stock_addition['unit_cost'], $stock['currency'], 'AUD'),
                    'unit_freight_aud' => convertCurrency($stock_addition['unit_freight'], $stock['currency'], 'AUD'),
                    'unit_currency'    => $stock['currency'],
                    'stock_left'       => max(0, $new_qty),
                    'stock_type'       => 'NEW',
                    'currency'         => $stock['currency'],
                    'supplier_id'      => $stock['supplier_id'],
                    'user'             => $_GLOBALS['staff']['user'],
                );
                $inventory_id = productInventorySingleToDatabase($item_array);
                if ($new_qty > 0) {
                    productSetValue($item_id, 'available', 1);
                }
            }

        }
        //Make a list of what should be deducted
        $deduct_list = array();
        $deduct_used = array();
        switch ($order['order_status']) {
            case 'sales':
            case 'proforma':
                if ('shipped' != $invoice['invoice_action']) {
                    break;
                }

            case 'processed':
                $deduct_list = $reserve_list;
                $deduct_used = $reserve_used;
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
        /*if (count($old_reserve_list)) {
            $DB->query(sprintf(
                "DELETE FROM `products_reserved` WHERE `invoice_id` IN(%d, %d)",
                $old_invoice['invoice_id'],
                $invoice_id
            ));
        }*/

       /* foreach ($reserve_list as $product_str => $product_qty) {
            if ($product_qty > 0) {
                list($product_id, $colour_id) = explode(' ', $product_str);
                $DB->query(sprintf(
                    "INSERT INTO `products_reserved` (`invoice_id`, `product_id`, `colour_id`, `qty`) VALUES (%d, %d, %d, %d)",
                    $invoice_id,
                    $product_id,
                    $colour_id,
                    $product_qty
                ));
            }
        }*/

        //Deduct the stock
      /*  if (0 !== $old_invoice['invoice_id'] && $old_invoice['site_id'] == $invoice['site_id']) {
            foreach ($deduct_change as $product_str => $product_qty) {
                if (0 !== $product_qty) {
                    list($product_id, $colour_id) = explode(' ', $product_str);
                    if ($colour_id) {
                        $res = $DB->query(sprintf(
                            "UPDATE `products_inventory_stock` SET `updated_at` = NOW(), `qty` = `qty` - %d WHERE `product_id` = %d AND `color_id`=%d LIMIT 1",
                            $product_qty,
                            $product_id,
                            $colour_id
                        ));
                        if ($DB->affected_rows() == 0) {
                            $DB->query(sprintf(
                                "INSERT INTO `products_inventory_stock` (`product_id`, `color_id`, `qty`, `user_id`) VALUES (%d, %d, %d, %d)",
                                $product_id,
                                $colour_id,
                                0 - $product_qty,
                                0
                            ));
                        }
                    }

                    //Update product
                    $DB->query(sprintf(
                        "UPDATE `products` SET `last_sold` = NOW(), `qty` = `qty` - %d WHERE `id` = %d LIMIT 1",
                        $product_qty,
                        $product_id
                    ));
                    $product_item = $DB->query_first(sprintf(
                        "SELECT `qty`, `alert_level`, `when_sold_out` FROM `products` WHERE `id` = %d LIMIT 1",
                        $product_id
                    ), MYSQLI_ASSOC);

                    //Update inventory
                    $DB->query(sprintf(
                        "INSERT INTO `products_inventory` (`product_id`, `color_id`, `invoice_id`, `movement`, `comments`, `available`, `stock_type`, `stock_left`) VALUES (%d, %d, %d, %s, '%s with invoice I#%d', %d, 'NEW', %d)",
                        $product_id,
                        $colour_id,
                        $invoice_id,
                        0 - $product_qty,
                        ($product_qty > 0) ? ('Sold') : ('Returned'),
                        $invoice_id,
                        $product_item['qty'],
                        ($product_qty > 0) ? $product_qty : 0
                    ));
                    for ($i = 0; $i < $product_qty; $i++) {
                        $DB->query(sprintf(
                            "UPDATE `products_inventory` SET `stock_left` = `stock_left` - 1 WHERE `stock_left` > 0 AND `product_id` = %d AND `color_id` = %d AND `stock_type` = 'NEW' ORDER BY `id` ASC LIMIT 1",
                            $product_id,
                            $colour_id
                        ));
                    }

                    //Send Notifications
                    if ($product_item['qty'] <= $product_item['alert_level'] && $product_item['alert_level'] > 0) {
                        if (function_exists('notifyActionMade')) {
                            notifyActionMade(NOTIFY_PRODUCT_INVENTORY_LOW, $invoice['site_id'], [
                                '[PRODUCT_ID]' => $product_id,
                                '[COLOUR_ID]'  => $colour_id,
                                '[STOCK]'      => $product_item['qty'],
                            ]);
                        }
                    }

                    //Update cost pricing
                    //productUpdateCostPrice($product_str);
                    //productUpdateAverageCostPrice($product_str);

                    if ($product_item['qty'] <= 0) {
                        //Update availability
                        if ('active' == $product_item['when_sold_out']) {
                            $DB->query(sprintf(
                                "UPDATE `products` SET `availability` = 11 WHERE `id` = %d AND `availability` = 1 LIMIT 1",
                                $product_id
                            ));
                        }

                        //Update active status
                        else {
                            $DB->query(sprintf(
                                "UPDATE `products` SET `active` = 'n' WHERE `id` = %d LIMIT 1",
                                $product_id
                            ));
                        }

                    }
                }
            }
        }*/

        //Log changes
       /* if (count($reserve_change) || count($deduct_change)) {
            $message = array(
                'invoice_id'      => $invoice_id,
                'message_sender'  => $_GLOBALS['staff']['id'],
                'message_sentto'  => 'Invoice System',
                'message_type_id' => 4,
                'message_title'   => 'Product Movement',
                'message_notes'   => array(),
            );
            foreach ($reserve_change as $product_str => $product_qty) {
                list($product_id, $colour_id) = explode(' ', $product_str);
                if ($product_qty > 0) {
                    $message['message_notes'][] = 'Reserved: ' . $product_qty . ' x Product ' . productFormatNumber($product_id, $colour_id);
                } else {
                    $message['message_notes'][] = 'Unreserved: ' . (0 - $product_qty) . ' x Product ' . productFormatNumber($product_id, $colour_id);
                }

            }
            foreach ($deduct_change as $product_str => $product_qty) {
                list($product_id, $colour_id) = explode(' ', $product_str);
                if ($product_qty > 0) {
                    $message['message_notes'][] = 'Sold: ' . $product_qty . ' x Product ' . productFormatNumber($product_id, $colour_id);
                } else {
                    $message['message_notes'][] = 'Returned: ' . (0 - $product_qty) . ' x Product ' . productFormatNumber($product_id, $colour_id);
                }

            }
            $message['message_notes'] = implode("\n", $message['message_notes']);
            invoiceHistoryToDatabase($invoice_id, $message);
        }*/

        /*
        if ('shipped' == $invoice['invoice_action'] && 'shipped' != $old_invoice['invoice_action']) {
            if (function_exists('notifyActionMade')) {
                notifyActionMade(NOTIFY_INVOICE_SHIPPED, $invoice['site_id'], invoiceTemplateVars($invoice, $invoice_products, $invoice_charges));
            }
        }
        if ('shipping' == $invoice['invoice_action'] && 'shipping' != $old_invoice['invoice_action']) {
            if (function_exists('notifyActionMade')) {
                notifyActionMade(NOTIFY_INVOICE_AWAITING_SHIPPING, $invoice['site_id'], invoiceTemplateVars($invoice, $invoice_products, $invoice_charges));
            }
        }
        */

        $amount          = sprintf('%.2f', $posted['payment_made']);
        $payment_options = invoiceGetOptionList("payment_options");
        if (0.00 != $amount) {
            $pay_txt = ('' != $posted['payment_notes']) ? $posted['payment_notes'] : $payment_options[$posted['payment_options']];
            $payment = array(
                'order_id'       => $order_id,
                'staff_id'         => $_GLOBALS['staff']['id'],
                'payment_time'     => time(),
                'payment_text'     => $pay_txt,
                'payment_amountau' => convertCurrency($amount, $invoice['invoice_currency'], 'AUD'),
                'payment_amountus' => convertCurrency($amount, $invoice['invoice_currency'], 'USD'),
                'payment_amount'   => $amount,
                'payment_currency' => $invoice['invoice_currency'],
            );
            purchaseOrderPaymentToDatabase($order_id, $payment);
        }

        $email_attach = array();
        if (is_array($posted['attachment_flag'])) {
            foreach ($posted['attachment_flag'] as $key => $value) {
                if ('' != $value) {
                    $email_attach[] = $key;
                }
            }
        }

        if (is_array($posted['flag_email_customer']) && count($posted['flag_email_customer']) > 0) {
            $job = array('order_id' => $order_id, 'job_type' => 1);

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
            purchaseOrderJobToDatabase($order_id, $job);
        }

        if ('' != $posted['email_received_customer']) {
            $message = array(
                'order_id'      => $order_id,
                'message_sender'  => $invoice['bill_name'],
                'message_sentto'  => $all_staff_list[$_GLOBALS['staff']['id']],
                'message_type_id' => INVOICE_MESSAGE_FROM_CUSTOMER,
                'message_title'   => 'Email From Customer',
                'message_notes'   => $posted['email_received_customer'],
            );
            purchaseordereHistoryToDatabase($order_id, $message);
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
            $email->Subject  = "RE:Purchase " . purchaseOrderFormatNumber($order_id, $order['revision_id'], $order['site_id']);
            $email->Body     = nl2br(htmlspecialchars($posted['email_message_staff'])) . '<br /><br /><br /><a href="' . htmlspecialchars(relative_url('index.php?action=edit&invoice_id=' . $invoice_id)) . '" target="_blank">View Invoice</a>';
            $email->IsHTML(true);
            foreach ($email_list as $mailto) {
                $email->AddAddress($mailto);
            }

            $result = $email->Send();
            $email->ClearAddresses();
            $email->ClearAttachments();

            $message = array(
                'order_id'      => $order_id,
                'message_sender'  => $_GLOBALS['staff']['id'],
                'message_sentto'  => implode(',', $email_list),
                'message_type_id' => INVOICE_MESSAGE_TO_STAFF,
                'message_title'   => $email->Subject,
                'message_notes'   => $posted['email_message_staff'],
            );
            purchaseordereHistoryToDatabase($order_id, $message);

            unset($email);
        }

        if ('' != $posted['admin_notes']) {
            $message = array(
                'order_id'      => $order_id,
                'message_sender'  => $_GLOBALS['staff']['id'],
                'message_sentto'  => 'Invoice System',
                'message_type_id' => INVOICE_MESSAGE_ADMIN,
                'message_title'   => 'Admin Note Added',
                'message_notes'   => $posted['admin_notes'],
            );
            invoiceHistoryToDatabase($order_id, $message);
        }
        $posted['order_id'] = $order_id;

        $action = 'edit';
        unset($posted['skip_job']);
        break;

    case 'purchase_preview':
        include __DIR__ . '/templates/invoice_purchase_preview.php';
        die();
    case 'lock':
        purchaseorderSetLock($order_id, $_GLOBALS['staff']['id']);
        $action = 'list';
        break;

    case 'unlock':
        purchaseorderSetLock($order_id);
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

        $vars["p"] = base64_url_encode(jecho($pass, 1));
        $vars["f"] = base64_url_encode(jecho($fail, 1));
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
        $supplier            = supplierFromDatabase($supplier_id, true);
        $supplier_contacts   = supplierContactsSearch(array('supplier_id' => $supplier_id));
        $supplier['contact'] = supplierContactFromDatabase(reset($supplier_contacts));
        $contact_name        = $supplier['contact']['name'];
        $contact_email       = $supplier['contact']['email'];
        $contact_phone       = $supplier['contact']['phone'];
        $sup_defaults        = reset($products_list);
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
                'currency'     => 'USD',
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
                'price'               => $product['product_costus'],
                'sup_price'           => $product['product_costus'],
                'total'               => $product['product_costus'] * $product['product_qty'],
                'total_aud'           => $product['product_costau'] * $product['product_qty'],
                'total_usd'           => $product['product_costus'] * $product['product_qty'],
                'sup_total'           => $product['product_costus'] * $product['product_qty'],
                'sup_total_aud'       => $product['product_costau'] * $product['product_qty'],
                'sup_total_usd'       => $product['product_costus'] * $product['product_qty'],
                'currency'            => 'USD',
                'hidden_currency'     => 'USD',
                'sup_currency'        => 'USD',
                'sup_hidden_currency' => 'USD',
            );
            $total += $product['product_costus'] * $product['product_qty'];
        }
        $porder['status']['pay_outstanding'] = $total;
        orderToDatabase($porder);
    }

    $action = 'purchase_orders';
    //$action = 'edit';
}

$get_vars  = $posted;
$keep_only = array('order_id','supplierproduct', 'visitor_id', 'status_id', 'site_id', 'order_by', 'search_for', 'page', 'order', 'status', 'handler', 'handler_id', 'return_message', 'payments_located', 'payments_made', 'invoice_action', 'member_id', 'invoice_time', 'label_type', 'invoice_list', 'format', 'prod_string', 'no_sig', 'producttype_id', 'tax_month', 'tax_year', 'tax_fyear', 'supplier_id', 'report_type','order_action');
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
        $invoice_lock = purchaseOrderGetLock($order_id);
        if (false === $invoice_lock || $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            purchaseorderSetLock($order_id, $_GLOBALS['staff']['id']);
            $invoice_lock = purchaseOrderGetLock($order_id);
        }
        header('Content-Type: text/javascript');
        jecho($invoice_lock);
        die();
    case 'add':
        unset($get_vars['order_id']);
        header('Location: invoice_add.php?' . http_build_query($get_vars));
        break;

    case 'edit':
        $invoice_lock = purchaseOrderGetLock($order_id);
        if (false === $invoice_lock || $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            purchaseorderSetLock($order_id, $_GLOBALS['staff']['id']);
            if (isset($posted['process_job'])) {
                $job_info = purchaseOrderJobFromDatabase(intval($posted['process_job']));
                if (1 == $job_info['job_type'] && $job_info['order_id'] == $order_id) {
                    $invoice = purchaseOrderFromDatabase($order_id);
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

                    $email->Sender = $staff_email;
                    //$email->From = $staff_email;
                    $email->From = 'sales@arcooda.com';
                    //$email->FromName = trim($staff_info['first_name'] . ' ' . $staff_info['last_name']);
                    $email->FromName = "Jody Luo";

                    $email->Mailer  = "sendmail";
                    $email->CharSet = "utf-8";
                    $email->Subject = $headers['Subject'];
                    $email->Body    = $posted['job_content'];
                    $email->IsHTML(false);

                    //Add Addresses
                    $email->AddAddress($headers['To']);
                    $email->AddBcc('backup@highwaygroup.net');
                    $email->AddBcc($staff_email);

                    $site_emails = array(
                        1  => array("Highwaygames", "sales@highwaygames.com"),
                        2  => array("Highway Entertainment", "sales@highway.net.au"),
                        3  => array("Coinopexpress", "sales@coinopexpress.com"),
                        5  => array("Channel Beat", "sales@channelbeat.com"),
                        9  => array("Converters.tv", "sales@converters.tv"),
                        10 => array("Josifovski Admin", "sales@josifovski.com"),
                        11 => array("Highway Group", "sales@highwaygroup.net"),
                        13 => array("Home Pinballs", "sales@homepinballs.com"),
                        14 => array("The Games Network", "sales@thegamesnetwork.tv"),
                        15 => array("Fortunewave", "sales@fortunewave.com"),
                        16 => array("Car Converters", "sales@carconverters.com.au"),
                        17 => array("Boxer Machine", "sales@boxermachine.com"),
                        18 => array("Xbox Arcade Machine", "sales@xboxarcademachines.com"),
                        19 => array("Converters.com.hk", "sales@converters.com.hk"),
                        20 => array("Arcade Spare Parts", "sales@arcadespareparts.com"),
                        21 => array("Great Delta", "sales@greatdelta.com"),
                        22 => array("GameGateAsia", "sales@gamegateasia.com"),
                        23 => array("Radikal Darts Australia", "sales@radikaldarts.com.au"),
                        24 => array("ArcadeGames.net", "sales@arcadegames.net"),
                        25 => array("CYP Converters", "sales@cypconverters.com.au"),
                        26 => array("Factory Clearance House", "sales@factoryclearancehouse.com"),
                        27 => array("SVOS", "sales@svos.com.au"),
                        28 => array("Destiny Bridal", "sales@destinybridal.com.au"),
                        29 => array("Images By Angelo", "sales@imagesbyangelo.com.au"),
                        30 => array("Highway Sales", "sales@highwaysales.net"),
                        31 => array("Amusements", "sales@amusements.com.au"),
                        32 => array("FotoInc", "sales@fotoinc.com.au"),
                        33 => array("Highway Media", "sales@highwaymedia.com.au"),
                    );
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
                            $file   = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/purchaseorder/attachment/' . $attach['file_name'];
                            $email->AddAttachment($file, $attach['file_orig'], 'base64', $attach['file_type']);
                            $string_attachments[] = $attach['file_orig'];
                        }
                    }

                    $notify_vars                        = invoiceTemplateVars($invoice, $invoice_products, $invoice_charges);
                    $notify_vars['[TXT_ATTACHMENTS]'][] = $email->Body;

                    if ('attached' == $headers['Invoice']) {
                        $string_attachments[] = 'Invoice';
                        $invoice_products     = purchaseOrderProductsFromDatabase($invoice_id);
                        $invoice_charges      = purchaseOrderChargesFromDatabase($invoice_id);

                        $invoice_buffer = 0;
                        /*
                        include __DIR__ . '/templates/jpeg_invoice.tpl.php';

                        if ($size_failure || 'y' == $posted['attach_pdf']) {
                            if (function_exists('imagedestroy')) {
                                imagedestroy($invoice_buffer);
                            }
                            $invoice_buffer = false;
                         */
                        include __DIR__ . '/templates/pdf_invoice.tpl.php';

                        $email->AddStringAttachment($invoice_buffer, 'invoice' . str_pad($invoice_id, 6, '0', STR_PAD_LEFT) . '.pdf', 'base64', 'application/pdf');
                        $notify_vars['[ATTACHMENTS]'][] = array(
                            'contents'     => $invoice_buffer,
                            'file_name'    => 'invoice' . str_pad($invoice_id, 6, '0', STR_PAD_LEFT) . '.pdf',
                            'content_type' => 'application/pdf',
                        );
                       
                        unset($invoice_buffer);
                    }
                 
                    $result                         = $email->Send();
                    $notify_vars['[EMAIL_SUBJECT]'] = $email->Subject;
                    $notify_vars['[EMAIL_BODY]']    = $email->Body;
                    if (function_exists('notifyActionMade')) {
                        notifyActionMade(NOTIFY_GENERIC, $invoice['site_id'], $notify_vars, false);
                    }
                    unset($notify_vars);

                    $message_notes = $posted['job_content'];
                    if (count($string_attachments)) {
                        $message_notes .= "\n\nAttachments with this email : " . implode(', ', $string_attachments);
                    }

                    $message = array(
                        'order_id'      => $order_id,
                        'message_sentto'  => $invoice['bill_name'],
                        'message_sender'  => $all_staff_list[$_GLOBALS['staff']['id']],
                        'message_type_id' => INVOICE_MESSAGE_TO_CUSTOMER,
                        'message_title'   => $headers['Subject'],
                        'message_notes'   => $message_notes,
                    );
                    purchaseOrderHistoryToDatabase($order_id, $message);
                    //memberSetValue($invoice['member_id'], '', formatDateTime());
                    $email->ClearAttachments();
                    $email->ClearAddresses();
                    $DB->query(sprintf(
                        "DELETE FROM `purchase_order_jobs` WHERE `job_id` = %d LIMIT 1",
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
                    "DELETE FROM `purchase_order_jobs` WHERE `job_id` = %d LIMIT 1",
                    $posted['delete_job']
                ));
            }

            $invoice_jobs = purchaseOrderJobsFromDatabase($order_id);
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
        header('Location: invoice_copy.php?' . http_build_query($get_vars));
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

    case 'list':
    default:
        unset($get_vars['invoice_id']);
        header('Location: invoice_list.php?' . http_build_query($get_vars));
}
