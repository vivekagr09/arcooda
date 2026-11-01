<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceChargesFromDatabase, invoiceProductsFromDatabase, invoiceExists, invoiceFromDatabase

include_once __DIR__ . '/header.inc.php';

$sinvoice_id = intval($posted['sinvoice_id']);
if (invoiceExists($posted['invoice_id'])) {
    $sinvoice_id = intval($posted['invoice_id']);
}

$staff_id = staffFromUser($_SERVER['REMOTE_USER']);

$action = 'edit';
$harmonisedCode = null;
$countryOfOrigion = null;
$productMaterials = null;
$productusage = null;
$invoice_frieight_tracking_service = invoiceGetOptionList('freight_tracking');
$invoice_status =  [
    'working'   => 'Open',
    'feedback'    => 'Open',
    'nointerest'     => 'Closed',
    'payment'  => 'Awaiting Payment',
    'approval' => 'Awaiting Shipping',
    'shipping' => 'Awaiting Shipping',
    'shipped'    => 'Shipped',
    'shipped-loan'    => 'Shipped',
    'processed' => 'Processed',
    'cancelled'     => 'Closed',
    'nohelp'  => 'Closed',
    'returned' => 'Open',
    'sent_factory' => 'Open',
    'prepare_factory' => 'Open',
    'prepare_customer' => 'Open',
    'sent_customer' => 'Open',
    'return_completed' => 'Processed',
    'return_cancelled' => 'Closed',
];
$all_staff_list = array();
if ($query = $DB->query(
    "SELECT `id`, `first_name`, `last_name` FROM `highwaygroup`.`users`"
)) {
    while ($row = $DB->fetch_assoc($query)) {
        $all_staff_list[$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
    }
    $DB->free_result($query);
}
$icontracking = $DB->query_first(sprintf(
    "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE  deleted_at IS NULL AND freight_type = 'fix'",
    '`freight_tracking`',
));
$sinvoice_addresses=0;
if ($row = $DB->query_first(sprintf(
    "SELECT `id` FROM `invoice_address` WHERE  `flag`='0' AND `main`='y' LIMIT 1"
), MYSQLI_ASSOC)) {       
    $sinvoice_addresses = 1 ;  //$row['id'];
}

if (shipinvoiceExists($sinvoice_id)) {
    $sinvoice          = shipinvoiceFromDatabase($sinvoice_id);
    $sinvoice_products = shipinvoiceProductsFromDatabase($sinvoice_id);
    $sinvoice_charges  = shipinvoiceChargesFromDatabase($sinvoice_id);
    $sinvoice_messages = shipinvoiceHistorySearch(array('sinvoice_id' => $sinvoice_id, 'message_type' => 0));
    $sinvoice_comments = shipinvoiceHistorySearch(array('sinvoice_id' => $sinvoice_id, 'message_type' => 1));
    $sinvoice_addresses = ($sinvoice['invoice_address'] != '' && $sinvoice['invoice_address'] != 0) ? $sinvoice['invoice_address'] : $sinvoice_addresses;
} else {
    $sinvoice          = invoiceFromDatabase($sinvoice_id);
    $shorted_sinvoice_products = $sinvoice_products = invoiceProductsFromDatabase($sinvoice_id);
    usort($shorted_sinvoice_products, function($a, $b) {
        return $b['product_offerus'] <=> $a['product_offerus'];
    });
    foreach ($shorted_sinvoice_products as $spitem) {
        if (!empty($spitem['harmonised_code'])) {
            $harmonisedCode = $spitem['harmonised_code'];
            $countryOfOrigion = $spitem['origin_country'];
            $productMaterials = $spitem['product_materials'];
            $productusage = $spitem['product_usage'];
            break;
        }
    }
    $sinvoice_charges  = invoiceChargesFromDatabase($sinvoice_id);
    $sinvoice_messages = array();
    $sinvoice_comments = array();

    foreach ($sinvoice_products as $k => $v) {
        if ($v['product_id'] > 0 && $v['sub_id'] > 0) {
            $sinvoice_products[$k]["product_id"] = sprintf('%05d - %04d', $v['product_id'], $v['sub_id']);
        }

        if ('' != $v['generic_code'] && '0' == $v['product_id']) {
            $sinvoice_products[$k]["product_id"] = $v['generic_code'];
        }

        //if ($v['product_condition'] == 2) $sinvoice_products[$k]["product_id"].= 'U';
    }
}




?>
<h2>Edit Ship Invoice - <?=shipinvoiceFormatNumber($sinvoice_id)?></h2>
<?php

include __DIR__ . '/templates/addedit_shipinvoice.tpl.php';

include_once __DIR__ . '/footer.inc.php';
