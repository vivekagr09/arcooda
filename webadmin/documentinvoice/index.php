<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('asset'); //assetTableInfoFromType, assetToDatabase, assetFromCompatible, assetGenerateUniqueName
moduleLoad('invoice'); //invoiceExists

$action   = $posted['action'];
$staff_id = staffFromUser($_SERVER['REMOTE_USER']);

$dinvoice_id = intval($posted['dinvoice_id']);
if (invoiceExists($posted['invoice_id'])) {
    $dinvoice_id = intval($posted['invoice_id']);
}

//Update Actions
switch ($action) {
//case 'do_add':
//    unset($posted['dinvoice_id']);
    case 'do_edit':
        $new_sinvoice          = documentinvoiceFromPost($posted);
        $new_sinvoice_products = documentinvoiceProductsFromPost($posted);
 
        $new_sinvoice_charges  = documentinvoiceChargesFromPost($posted);
        //if($_SERVER['REMOTE_USER']=='Jwalit') print_array($posted);

        $old_sinvoice          = documentinvoiceFromDatabase($dinvoice_id);

        $old_sinvoice_products = documentinvoiceProductsFromDatabase($dinvoice_id);
        $old_sinvoice_charges  = documentinvoiceChargesFromDatabase($dinvoice_id);
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
            $message[] = 'Document invoice changed:';
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
        documentinvoiceToDatabase($sinvoice);
        documentinvoiceProductsToDatabase($dinvoice_id, $sinvoice_products);
        documentinvoiceChargesToDatabase($dinvoice_id, $sinvoice_charges);

        if ($new_sinvoice['revision_id'] > $old_sinvoice['revision_id'] && 'Daniel' == $_SERVER['REMOTE_USER']) {
            $asset_type = 'invoice';
            $table_info = assetTableInfoFromType($asset_type);
            $asset      = array(
                'invoice_id'    => $dinvoice_id,
                'staff_id'      => $_GLOBALS['staff']['staff_id'],
                'file_type'     => 'application/pdf',
                'asset_caption' => 'Dummy ' . documentinvoiceFormatNumber($dinvoice_id, $sinvoice['revision_id'], $sinvoice['site_id']),
            );
            $asset['file_orig'] = formatFileSafe($asset['asset_caption'] . '.pdf');
            $asset['asset_id']  = intval(assetToDatabase($asset_type, assetFromCompatible($asset_type, $asset)));
            $asset['filename']  = assetGenerateUniqueName($asset_type, assetFromCompatible($asset_type, $asset)) . '.pdf';

            if (($fp = fopen($table_info['directory'] . $asset['filename'], 'wb')) !== false) {
                include __DIR__ . '/templates/pdf_documentinvoice.tpl.php';
                fwrite($fp, $sinvoice_buffer);
                fclose($fp);
                $asset['filesize'] = filesize($table_info['directory'] . $asset['filename']);
                assetToDatabase($asset_type, assetFromCompatible($asset_type, $asset));
            }
            unset($sinvoice_buffer);
        }

        if ('' != $posted['admin_notes']) {
            documentinvoiceHistoryToDatabase($dinvoice_id, array('sinvoice' => $dinvoice_id, 'message_date' => formatDateTime(), 'message_sender' => $staff_id, 'message_notes' => $posted['admin_notes'], 'message_type' => 1));
        }

        if (count($message)) {
            documentinvoiceHistoryToDatabase($dinvoice_id, array(
                'dinvoice_id'    => $dinvoice_id,
                'message_date'   => formatDateTime(),
                'message_sender' => $staff_id,
                'message_notes'  => implode("\n", $message),
            ));
        }

        $action = 'edit';
        break;
}

$get_vars  = $posted;
$keep_only = array('dinvoice_id', 'invoice_id', 'status_id', 'order_by', 'search_for', 'page', 'order', 'status', 'handler', 'return_message', 'format','invoice_time');
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
        header('Location: documentinvoice_edit.php?' . http_build_query($get_vars));
        break;
    case 'view':
        /*if(!isset($get_vars['format'])) $get_vars['format']='pdf';*/
        header('Location: documentinvoice_view.php?' . http_build_query($get_vars));
        break;
    case 'shiplabel':
        header('Location: documentinvoice_shiplabel.php?' . http_build_query($get_vars));
        break;
    case 'search':
        header('Location: documentinvoice_search.php?' . http_build_query($get_vars));
        break;
    case 'select':
        header('Location: documentinvoice_select.php?' . http_build_query($get_vars));
        break;
    case 'list':
    default:
        unset($get_vars['invoice_id']);
        header('Location: documentinvoice_list.php?' . http_build_query($get_vars));
}

die();
