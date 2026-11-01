<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceChargesFromDatabase, invoiceCourierQuotesFromDatabase, invoiceProductsFromDatabase
moduleLoad('member'); //memberContactsFromDatabase, memberCouriersFromDatabase, memberForwardersFromDatabase, memberNotifyPartiesFromDatabase, memberShippingAddressesFromDatabase, memberFromDatabase, memberRequestsFromDatabase

include_once __DIR__ . '/header.inc.php';

include_once 'function.productOfferPriceFinal.inc.php';

$action     = 'add';
$invoice_id = 0;
$copy_id    = intval($posted['copy_id']);
$staff_id   = $_GLOBALS['staff']['id'];

$invoice                = invoiceFromDatabase($copy_id);
$invoice_products       = invoiceProductsFromDatabase($copy_id);
$invoice_charges        = invoiceChargesFromDatabase($copy_id);
$invoice_courier_quotes = invoiceCourierQuotesFromDatabase($copy_id);
$invoice_history        = array();
$invoice_admin_notes    = array();
$invoice_assets         = array();
$invoice_payments       = array();
$invoice_porders        = array();
$invoice_freight_quotes = array();

$invoice['history_count'] = 0;

$member_information = memberFromDatabase($invoice['member_id']);
$member_addresses   = memberShippingAddressesFromDatabase($invoice['member_id']);
$member_notify      = memberNotifyPartiesFromDatabase($invoice['member_id']);
$member_requests    = memberRequestsFromDatabase($invoice['member_id']);
$member_contacts    = memberContactsFromDatabase($invoice['member_id']);
$member_forwarders  = memberForwardersFromDatabase($invoice['member_id']);
$member_couriers    = memberCouriersFromDatabase($invoice['member_id']);

$invoice['invoice_status'] = 'proforma';
$invoice['invoice_action'] = '';
unset($invoice['proforma_tracknumber']);
unset($invoice['invoice_id']);
unset($invoice['products_locate_date']);
unset($invoice['products_locate_time']);
$invoice['products_located'] = 'n';
unset($invoice['ship_locate_date']);
unset($invoice['ship_locate_time']);
$invoice['ship_located'] = 'n';
unset($invoice['payments_locate_date']);
unset($invoice['payments_locate_time']);
$invoice['payments_located'] = 'n';
unset($invoice['supply_locate_date']);
unset($invoice['supply_locate_time']);
$invoice['supply_located'] = 'n';
unset($invoice['proforma_date']);
unset($invoice['proforma_time']);

$new_total            = $new_online            = $new_cost            = $new_totalau            = $new_totalus            = $new_costau            = $new_costus            = 0;
$products_need_update = array();
foreach ($invoice_products as $key => $product) {
    $current_price = $product['product_offer'];
    unset($invoice_products[$key]['invoice_id']);
    // need to update the pricing for all the products now.
    $prices         = productOfferPriceFinal($product['product_id'], $product['product_qty'], $invoice['member_id'], $invoice['site_id']);
    $new_price_au   = convertCurrency($prices['product_offer_au'], 'AUD');
    $new_price_us   = convertCurrency($prices['product_offer_us'], 'USD', '', true);
    $ncost_price_au = convertCurrency($prices['cost_price_au'], 'AUD');
    $ncost_price_us = convertCurrency($prices['cost_price_us'], 'USD', '', true);
    if ('AUD' == $invoice['invoice_currency']) {
        $new_price   = $new_price_au;
        $ncost_price = $ncost_price_au;
    } elseif ('USD' == $invoice['invoice_currency']) {
        $new_price   = $new_price_us;
        $ncost_price = $ncost_price_us;
    } else {
        $new_price   = convertCurrency($prices['product_offer'], 'USD', $invoice['invoice_currency'], true);
        $ncost_price = convertCurrency($prices['cost_price'], 'USD', $invoice['invoice_currency'], true);
    }
    //Store in an array to be used later.
    if ($new_price < $current_price) {
        $products_need_update[] = $product['product_id'];
    }

    //$invoice_products[$key]['product_offer']=$new_price;
    //$invoice_products[$key]['product_offerau']=$new_price_au;
    //$invoice_products[$key]['product_offerus']=$new_price_us;
    $invoice_products[$key]['product_cost']   = $ncost_price;
    $invoice_products[$key]['product_costau'] = $ncost_price_au;
    $invoice_products[$key]['product_costus'] = $ncost_price_us;

    //$new_total+= ($new_price*$product['product_qty']);
    //$new_online+= ($new_price*$product['product_qty']);
    //$new_totalau+= ($new_price_au*$product['product_qty']);
    //$new_totalus+= ($new_price_us*$product['product_qty']);
    $new_cost += ($ncost_price * $product['product_qty']);
    $new_costau += ($ncost_price_au * $product['product_qty']);
    $new_costus += ($ncost_price_us * $product['product_qty']);
}
//$invoice['products_offer']=$new_total;
//$invoice['products_offerau']=$new_totalau;
//$invoice['products_offerus']=$new_totalus;
$invoice['products_cost']   = $new_cost;
$invoice['products_costau'] = $new_costau;
$invoice['products_costus'] = $new_costus;

//$new_total+=$invoice['ship_offer']+$invoice['charges_offer'];
//$new_online+=$invoice['ship_online']+$invoice['charges_online'];
//$new_totalau+=$invoice['ship_offerau']+$invoice['charges_offerau'];;
//$new_totalus+=$invoice['ship_offerus']+$invoice['charges_offerus'];;
$new_cost += $invoice['ship_cost'] + $invoice['charges_cost'];
$new_costau += $invoice['ship_costau'] + $invoice['charges_costau'];
$new_costus += $invoice['ship_costus'] + $invoice['charges_costus'];

//$invoice['invoice_offer']=$new_total;
//$invoice['invoice_offerau']=$new_totalau;
//$invoice['invoice_offerus']=$new_totalus;
//$invoice['invoice_online']=$new_online;
$invoice['invoice_cost']   = $new_cost;
$invoice['invoice_costau'] = $new_costau;
$invoice['invoice_costus'] = $new_costus;

foreach (array_keys($invoice_charges) as $key) {
    unset($invoice_charges[$key]['invoice_id']);
}

foreach (array_keys($invoice_courier_quotes) as $key) {
    unset($invoice_courier_quotes[$key]['invoice_id']);
}

$invoice_history        = array();
$invoice_courier_quotes = array();
$invoice_payments       = array();
$invoice_porders        = array();
$invoice_freight_quotes = array();

$invoice['history_count'] = count($invoice_history);
$invoice_type             = array_search($invoice['invoice_status'], array(
    'inquiry',
    'enquiry',
    'closed',
    'proforma',
    'sales',
    'processed',
    'cancelled',
    'return',
    'completed',
    'purchase',
));

/*if($posted['site_id']>0)
$invoice['site_id']=intval($posted['site_id']);*/
//else
//    $invoice['site_id']=11;

if ('' != $posted['status']) {
    $invoice['invoice_status'] = $posted['status'];
}

?>
<h2>Add New <?= ($display_inquiry) ? 'Inquiry' : 'Invoice' ?></h2>
<?php

include __DIR__ . '/templates/addedit_invoice.tpl.php';

include_once __DIR__ . '/footer.inc.php';
