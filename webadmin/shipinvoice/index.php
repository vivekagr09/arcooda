<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('asset'); //assetTableInfoFromType, assetToDatabase, assetFromCompatible, assetGenerateUniqueName
moduleLoad('invoice'); //invoiceExists

$action   = $posted['action'];
$staff_id = staffFromUser($_SERVER['REMOTE_USER']);

$sinvoice_id = intval($posted['sinvoice_id']);
if (invoiceExists($posted['invoice_id'])) {
    $sinvoice_id = intval($posted['invoice_id']);
}

//Update Actions
switch ($action) {
//case 'do_add':
//    unset($posted['sinvoice_id']);
    case 'do_edit':
        $new_sinvoice          = shipinvoiceFromPost($posted);
        $new_sinvoice_products = shipinvoiceProductsFromPost($posted);
 
        $new_sinvoice_charges  = shipinvoiceChargesFromPost($posted);
        //if($_SERVER['REMOTE_USER']=='Jwalit') print_array($posted);

        $old_sinvoice          = shipinvoiceFromDatabase($sinvoice_id);
        $old_sinvoice_products = shipinvoiceProductsFromDatabase($sinvoice_id);
        $old_sinvoice_charges  = shipinvoiceChargesFromDatabase($sinvoice_id);
        //if($_SERVER['REMOTE_USER']=='Jwalit') print_array($old_sinvoice_charges);

        $sinvoice          = $new_sinvoice;
        $sinvoice_products = $new_sinvoice_products;
        $sinvoice_charges  = $new_sinvoice_charges;
        //if($_SERVER['REMOTE_USER']=='Jwalit') { print_array($sinvoice_charges); die(); }

        foreach ($old_sinvoice as $key => $value) {
            if (str_replace(array("\r", "\n"), '', $new_sinvoice[$key]) == str_replace(array("\r", "\n"), "", $value)) {
                unset($new_sinvoice[$key]);
                unset($old_sinvoice[$key]);
            }
        }

        $message = array();
        if (count($new_sinvoice)) {
            $message[] = 'Shipping invoice changed:';
            foreach ($new_sinvoice as $key => $value) {
                $message[] = $key . ' changed from "' . $old_sinvoice[$key] . '" to "' . $value . '"';
            }

            $new_sinvoice['revision_id'] = $sinvoice['revision_id'] + 1;
            $old_sinvoice['revision_id'] = $sinvoice['revision_id'];
            $sinvoice['revision_id']++;
        }
        if (!isset($posted['is_harmonised_code']) || 'y' != $posted['is_harmonised_code']) {
            $sinvoice['is_harmonised_code'] = 'n';
        }
        if (!isset($posted['is_origin_country']) || 'y' != $posted['is_origin_country']) {
            $sinvoice['is_origin_country'] = 'n';
        }
        if (!isset($posted['is_invoice_usage']) || 'y' != $posted['is_invoice_usage']) {
            $sinvoice['is_invoice_usage'] = 'n';
        }
        if (!isset($posted['is_invoice_materials']) || 'y' != $posted['is_invoice_materials']) {
            $sinvoice['is_invoice_materials'] = 'n';
        }
        if (!isset($posted['is_product_harmonised_code']) || 'y' != $posted['is_product_harmonised_code']) {
            $sinvoice['is_product_harmonised_code'] = 'n';
        }
        if (!isset($posted['is_product_origin_country']) || 'y' != $posted['is_product_origin_country']) {
            $sinvoice['is_product_origin_country'] = 'n';
        }
        if (!isset($posted['is_product_materials']) || 'y' != $posted['is_product_materials']) {
            $sinvoice['is_product_materials'] = 'n';
        }
        if (!isset($posted['is_product_usage']) || 'y' != $posted['is_product_usage']) {
            $sinvoice['is_product_usage'] = 'n';
        }
        shipinvoiceToDatabase($sinvoice);
        shipInvoiceProductsToDatabase($sinvoice_id, $sinvoice_products);
        shipInvoiceChargesToDatabase($sinvoice_id, $sinvoice_charges);

        if ($new_sinvoice['revision_id'] > $old_sinvoice['revision_id'] && 'Daniel' == $_SERVER['REMOTE_USER']) {
            $asset_type = 'invoice';
            $table_info = assetTableInfoFromType($asset_type);
            $asset      = array(
                'invoice_id'    => $sinvoice_id,
                'staff_id'      => $_GLOBALS['staff']['staff_id'],
                'file_type'     => 'application/pdf',
                'asset_caption' => 'Dummy ' . shipinvoiceFormatNumber($sinvoice_id, $sinvoice['revision_id'], $sinvoice['site_id']),
            );
            $asset['file_orig'] = formatFileSafe($asset['asset_caption'] . '.pdf');
            $asset['asset_id']  = intval(assetToDatabase($asset_type, assetFromCompatible($asset_type, $asset)));
            $asset['filename']  = assetGenerateUniqueName($asset_type, assetFromCompatible($asset_type, $asset)) . '.pdf';

            if (($fp = fopen($table_info['directory'] . $asset['filename'], 'wb')) !== false) {
                include __DIR__ . '/templates/pdf_shipinvoice.tpl.php';
                fwrite($fp, $sinvoice_buffer);
                fclose($fp);
                $asset['filesize'] = filesize($table_info['directory'] . $asset['filename']);
                assetToDatabase($asset_type, assetFromCompatible($asset_type, $asset));
            }
            unset($sinvoice_buffer);
        }

        if ('' != $posted['admin_notes']) {
            shipinvoiceHistoryToDatabase($sinvoice_id, array('sinvoice' => $sinvoice_id, 'message_date' => formatDateTime(), 'message_sender' => $staff_id, 'message_notes' => $posted['admin_notes'], 'message_type' => 1));
        }

        if (count($message)) {
            shipinvoiceHistoryToDatabase($sinvoice_id, array(
                'sinvoice_id'    => $sinvoice_id,
                'message_date'   => formatDateTime(),
                'message_sender' => $staff_id,
                'message_notes'  => implode("\n", $message),
            ));
        }

        $action = 'edit';
        break;
}

$get_vars  = $posted;
$keep_only = array('sinvoice_id', 'invoice_id', 'status_id', 'order_by', 'search_for', 'page', 'order', 'status', 'handler', 'return_message', 'format','invoice_time');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (!in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

//Redirect Actions
switch ($action) {
    case 'edit':
        header('Location: shipinvoice_edit.php?' . http_build_query($get_vars));
        break;
    case 'view':
        /*if(!isset($get_vars['format'])) $get_vars['format']='pdf';*/
        header('Location: shipinvoice_view.php?' . http_build_query($get_vars));
        break;
    case 'shiplabel':
        header('Location: shipinvoice_shiplabel.php?' . http_build_query($get_vars));
        break;
    case 'search':
        header('Location: shipinvoice_search.php?' . http_build_query($get_vars));
        break;
    case 'select':
        header('Location: shipinvoice_select.php?' . http_build_query($get_vars));
        break;
    case 'list':
    default:
        unset($get_vars['invoice_id']);
        header('Location: shipinvoice_list.php?' . http_build_query($get_vars));
}

die();
