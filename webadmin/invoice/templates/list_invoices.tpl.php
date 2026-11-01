<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('member'); //memberFromDatabase, memberRequestsFromDatabase, memberWarningsFromDatabase, memberGetValue
moduleLoad('shipinvoice');

global $_GLOBALS;

if (count($invoice_list)) {
    $credit_resultcode = array();
    if ($query = $DB->query(sprintf(
        "SELECT `result_code`, `result_message` FROM `highwaygroup`.`credit_resultcodes`"
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $credit_resultcode[$row["result_code"]] = $row["result_message"];
        }
        $DB->free_result($query);
    }
}

/**
 * Apply caching to the template
 */
/*
if (is_object($CH))
{
$CH->open();
$CH->addVarDependancies(array(
$order,
$get_vars,
$invoice_list,
$invoice_declines,
));
if ($CH->fetch())
{
echo "<!-- #action_buttons.tpl -->";
return;
}
}
 */

$alt     = false;
$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" alt="' . (('desc' == $order) ? 'desc' : 'asc') . '" />';
$preview_time  = mktime(0, 0, 0);
$sort_url           = $get_vars;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$tmp      = $sort_url;
$sort_url = '.?' . http_build_query($sort_url);
$sort_url .= '&order_by=';

$asps_url = 'http://www.arcadespareparts.com/webadmin/invoice/?' . http_build_query($tmp);
$asps_url .= '&invoice_id=';

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['invoice_id']);
$tmp      = $edit_url;
$edit_url = '.?' . http_build_query($edit_url);
$edit_url .= '&invoice_id=';

$aspe_url = 'http://www.arcadespareparts.com/webadmin/invoice/?' . http_build_query($tmp);
$aspe_url .= '&invoice_id=';

$view_url           = $get_vars;
$view_url['action'] = 'view';
unset($view_url['invoice_id']);
$tmp      = $view_url;
$view_url = '.?' . http_build_query($view_url);
$view_url .= '&invoice_id=';

$aspv_url = 'http://www.arcadespareparts.com/webadmin/invoice/?' . http_build_query($tmp);
$aspv_url .= '&invoice_id=';

$sview_url           = $get_vars;
$sview_url['action'] = 'shipview';
$sview_url['no_sig'] = 'please';
unset($sview_url['invoice_id']);
$tmp       = $sview_url;
$sview_url = '.?' . http_build_query($sview_url);
$sview_url .= '&invoice_id=';

$aspsv_url = 'http://www.arcadespareparts.com/webadmin/invoice/?' . http_build_query($tmp);
$aspsv_url .= '&invoice_id=';

$dview_url = '/webadmin/shipinvoice/?action=view&invoice_id=';

$aspdv_url = 'http://www.arcadespareparts.com/webadmin/shipinvoice/?action=view&invoice_id=';

$member_url           = $get_vars;
$member_url['action'] = 'edit';
unset($member_url['member_id']);
$member_url = '/webadmin/member/?' . http_build_query($member_url) . '&member_id=';

$unlock_url           = $get_vars;
$unlock_url['action'] = 'unlock';
unset($unlock_url['invoice_id']);
$tmp        = $unlock_url;
$unlock_url = '.?' . http_build_query($unlock_url);
$unlock_url .= '&invoice_id=';

$aspu_url = 'http://www.arcadespareparts.com/webadmin/invoice/?' . http_build_query($tmp);
$aspu_url .= '&invoice_id=';

$select_url           = $get_vars;
$select_url['action'] = 'select';
unset($select_url['invoice_id']);
$tmp        = $select_url;
$select_url = '.?' . http_build_query($select_url);
$select_url .= '&invoice_id=';

$aspsl_url = 'http://www.arcadespareparts.com/webadmin/invoice/?' . http_build_query($tmp);
$aspsl_url .= '&invoice_id=';
?>
<!-- list_invoices.tpl -->

<table class="wide spacetable table_grid" align="center">
	<tr>
		<th><a href="<?=htmlspecialchars($sort_url . 'site_id')?>">Site</a><?=('site_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'invoice_id')?>"><?=($display_inquiry) ? 'Inquiry' : 'Invoice'?> #</a><?=('invoice_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'open_date')?>">Open Date</a><?=('open_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'bill_name')?>">Customer</a><?=('bill_name' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'invoice_offerau')?>">Invoice Value</a><?=('invoice_offerau' == $order_by) ? $ord_img : ''?></th>
		<!-- <th><a href="<?=htmlspecialchars($sort_url . 'producttype_id')?>">Product Type</a><?=('producttype_id' == $order_by) ? $ord_img : ''?></th> -->
<?php
if (!in_array($posted['site_id'], array(3, 20, 26))) {
?>
		<th><a href="<?=htmlspecialchars($sort_url . 'invoice_offerau')?>">AUD Value</a><?=('invoice_offerau' == $order_by) ? $ord_img : ''?></th>
<?php
}
?>
		<th><a href="<?=htmlspecialchars($sort_url . 'invoice_profit')?>">Invoice Profit</a><?=('invoice_profit' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'product_locate_date')?>">Products Located</a><?=('product_locate_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'freight_locate_date')?>">Freight Located</a><?=('freight_locate_date' == $order_by) ? $ord_img : ''?></th>
<?php
if (!$display_inquiry) {
?>
		<th><a href="<?=htmlspecialchars($sort_url . 'payments_locate_date')?>">Payment Made</a><?=('payments_locate_date' == $order_by) ? $ord_img : ''?></th>
<?php
}
?>
		<th><a href="<?=htmlspecialchars($sort_url . 'status')?>">Status</a><?=('status' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'handler_id')?>">Handler</a><?=('handler_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'staff_id')?>">Last Staff</a><?=('staff_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'write_date')?>">Last Modified</a><?=('write_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'open_date')?>">Days Open</a><?=('open_date' == $order_by) ? $ord_img : ''?></th>
		<th>&nbsp;</th>
	</tr>

<?php
$invoice_locks = invoiceGetLocks();
$invoice_jobs  = invoiceJobsByInvoice();
$fright_tracking_service = invoiceGetOptionList('freight_tracking');
$alt = false;
foreach ($invoice_list as $invoice_id) {
    $invoice = invoiceFromDatabase($invoice_id);
    //$invoice_lock = invoiceGetLock($invoice_id);
    $invoice_lock = false;
    if (array_key_exists($invoice_id, $invoice_locks)) {
        $invoice_lock = $invoice_locks[$invoice_id];
    }

    //$invoice_jobs = invoiceJobsFromDatabase($invoice_id);
    $invoice_products = invoiceProductsFromDatabase($invoice_id);
    $invoice_charges  = invoiceChargesFromDatabase($invoice_id);
    $invoice_payments = invoicePaymentsFromDatabase($invoice_id);
    if (0 != $invoice['member_id']) {
        $member          = memberFromDatabase($invoice['member_id']);
        $member_requests = memberRequestsFromDatabase($invoice['member_id']);
        $member_warnings = memberWarningsFromDatabase($invoice['member_id']);
    } else {
        $member          = null;
        $member_requests = array();
        $member_warnings = array(array('comments' => 'Invoice is not linked to a member'));
    }

    $invoice_currency = $invoice['invoice_currency'];
    $class            = '';
    if ($alt = !$alt) {
        $class = ' class="alt"';
    }

    if (array_key_exists($invoice_id, $invoice_jobs)) {
        $class = ' class="highlight"';
    }

    if ($invoice['staff_id'] != $invoice['handler_id']) {
        $class = ' class="highlight"';
    }
    $styleneedby = '';
    $needbyTooltip = '';
    if (!in_array($invoice['invoice_status'],['processed', 'cancelled','closed'])){
        if (0 != $invoice['needby_time'] && time() > $invoice['needby_time'] - 604800) {
            //$class = ' class="need_soon"';
        }

        if ( $invoice['needby_time']  && time() > $invoice['needby_time']) {
            $class = ' class="need_timeout"';
        }
        if(!empty($invoice['needby_date']) && $invoice['needby_date'] != '0000-00-00 00:00:00')
        {
            $styleneedby = 'background-color:red;';
            $needbyTooltip = "Dated Needed: ".date('d/m/Y',strtotime($invoice['needby_date']))."";
        }
    }

    $lock_tooltip = '';
    if (false !== $invoice_lock) {
        $class = ' class="locked"';
        if ($invoice_lock['staff_id'] != $_GLOBALS['staff']['id']) {
            $staff_list   = invoiceGetOptionList('staff_id');
            $lock_tooltip = htmlspecialchars(sprintf(
                _('This invoice/inquiry is currently locked to %s'),
                $staff_list[$invoice_lock['staff_id']]
            ));
        }
    }
    if ($invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
        $class = ' class="mylock"';
    }

    $payment_due = $invoice['invoice_offer'];
    foreach ($invoice_payments as $payment) {
        $payment_due = bcsub($payment_due, convertCurrency($payment['payment_amount'], $payment["payment_currency"], $invoice["invoice_currency"]), 2);
    }

    $payments_located = '<img src="images/status/' . (('y' == $invoice['payments_located']) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['payments_located']) ? 'yes' : 'no') . '" />';
    if (count($invoice_payments) && bccomp($payment_due, "0.05", 2) >= 0) {
        $payments_located = '<img src="images/status/deposit.gif" alt="Deposit" />';
    }

    if (count($invoice_payments) && bccomp($payment_due, "0.05", 2) < 0 && $invoice['invoice_offer'] > 0.0) {
        $payments_located = '<img src="images/status/yes.gif" alt="Yes" />';
    }
    if (count($invoice_payments) && abs($payment_due) ==  abs($invoice['invoice_offer'])) {
        $payments_located = '<img src="images/status/payment.gif" alt="Awating Payment" />';
    }
    $products_located = '<img src="images/status/' . (('y' == $invoice['products_located'] || $invoice['products_found'] == $invoice['products_needed']) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['products_located'] || $invoice['products_found'] == $invoice['products_needed']) ? 'Yes' : 'No') . '" />';

   /* $ship_located = '<img src="images/status/' . (('y' == $invoice['ship_located']) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['ship_located']) ? 'Yes' : 'No') . '" />';*/
    $ship_located = '<img src="images/status/' . ((0 == $invoice['ship_id']) ? 'no' : 'yes') . '.gif" alt="' . (( 0== $invoice['ship_id']) ? 'No' : 'Yes') . '" />';
    if(0 == $invoice['ship_id']){
        $ship_located = '<img src="images/status/' . (('y' == $invoice['ship_located']) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['ship_located']) ? 'Yes' : 'No') . '" />';
    }
    if ('y' != $invoice['products_located'] && $invoice['products_found'] < $invoice['products_needed']) {
        $products_located = '<span style="color:red;">' . $invoice['products_found'] . '/' . $invoice['products_needed'] . '</span>';
    }

    $invoice_type = array_search($invoice['invoice_status'], array(
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

    $status_image = $invoice['invoice_status'];
    if ($invoice_type < 3) {
        /* Inquiry */
        $status_image = 'q_working';
        if ('feedback' == $invoice['invoice_action']) {
            $status_image = 'q_feedback';
        }

        if (2 == $invoice_type) {
            $status_image = 'q_cancelled';
        }

        if ('nointerest' == $invoice['invoice_action']) {
            $status_image = 'q_nointerest';
        }

        if ('nohelp' == $invoice['invoice_action']) {
            $status_image = 'q_nohelp';
        }
    } else {
        /* Invoice */
        $status_image = $invoice['invoice_action'];
        if ("return" == $invoice["invoice_status"]) {
            //$status_image = "returned";
             $status_image = "awating_customer";
            if($invoice['invoice_action'] == 'waiting_customer')
            $status_image = "awating_customer";
            if($invoice['invoice_action'] == 'Working_on_solution')
            $status_image = "working_on_solution";
        }

        if ("completed" == $invoice["invoice_status"]) {
            $status_image = "processed";
        }

        //if ($invoice_type < 5)
        //{
        //    if ($invoice['ship_located'] == 'n') $status_image = 'shipping';
        //    if ($invoice['payments_located'] == 'n') $status_image = 'payment';
        //}
    }

    $products_tooltip = array();
    $notfound         = array();
    $found            = array();
    $symbol           = getCurrencySymbol($invoice_currency, 'HTML');
    foreach ($invoice_products as $product) {
        $color_code = '';
        if($product['colour_id'] > 0) {
            $color_code = productGenericFromDatabase($product['colour_id'], 'product_colour')['code3'];
        }
        $product_tr = sprintf(
            _('<tr><td style="text-align:right;">%s</td><td>x</td><td style="white-space:nowrap;">#%s: %s</td><td>@</td><td style="text-align:right;">%s</td><td>=%s</td><td style="text-align:right;">%s</td><td>%s</td></tr>'),
            $product['product_qty'],
            htmlspecialchars(invoiceProductFormatNumber($product['product_id'], $product['sub_id'])).$color_code,
            htmlspecialchars($product['product_name']),
            convertCurrency($product['product_offer'], $invoice_currency),
            $symbol,
            convertCurrency($product['product_offer'] * $product['product_qty'], $invoice_currency),
            htmlspecialchars($invoice_currency)
        );
        $products_tooltip[] = $product_tr;
        if ('n' == $product['supplier_found']) {
            $notfound[] = $product_tr;
        }

        if ('y' == $product['supplier_found'] || 's' == $product['supplier_found']) {
            $found[] = $product_tr;
        }
    }
    $invoice_gifts    = invoiceGiftsFromDatabasePrint($invoice_id);
    if(count($invoice_gifts['gift'])){
        foreach ($invoice_gifts['gift'] as $gift) {
            $gifts_tr = sprintf(
                _('<tr><td style="text-align:right;">%s</td><td>x</td><td style="white-space:nowrap;">#%d: %s</td><td>@</td><td style="text-align:right;">%s</td><td>=%s</td><td style="text-align:right;">%s</td><td>%s</td></tr>'),
                $gift['gift_qty'],
                 htmlspecialchars(invoiceProductFormatNumber($gift['product_id'], 0)),
                $gift['gift_name'].' (FREE - Gift)',
                "0.00",
                $symbol,
                "0.00",
                htmlspecialchars($invoice_currency)
            );
            $products_tooltip[] = $gifts_tr;
        }
    }
    foreach ($invoice_charges as $charge) {
        $charge_tr = sprintf(
            _('<tr><td style="text-align:right;">%s</td><td>x</td><td style="white-space:nowrap;">C%02d: %s</td><td>@</td><td style="text-align:right;">%s</td><td>=%s</td><td style="text-align:right;">%s</td><td>%s</td></tr>'),
            $charge['charge_qty'],
            $charge['charge_id'],
            htmlspecialchars($charge['charge_name']),
            convertCurrency($charge['charge_offer'], $invoice_currency),
            $symbol,
            convertCurrency($charge['charge_offer'] * $charge['charge_qty'], $invoice_currency),
            htmlspecialchars($invoice_currency)
        );
        $products_tooltip[] = $charge_tr;
    }
    $ship_offer = convertCurrency($invoice['ship_offer'], $invoice_currency);
    if ('' != $invoice['ship_product_name']) {
        $products_tooltip[] = sprintf(
            _('<tr><td style="text-align:right;">1</td><td>x</td><td style="white-space:nowrap;">F%02d%s %s</td><td>@</td> <td>%s</td><td>=%s</td><td style="text-align:right;">%s</td><td>%s</td></tr>'),
            $invoice['ship_id'],
            ('y' == $invoice['ship_insurance']) ? 'I' : 'N',
            htmlspecialchars($invoice['ship_product_name']),
            $ship_offer,
            $symbol,
            $ship_offer,
            htmlspecialchars($invoice_currency)
        );
    }

    $products_tooltip = '<table>' . implode('', $products_tooltip) . '</table>';

    $notfound_tooltip = '';
    if (count($notfound)) {
        $notfound_tooltip = '<table><tr><td colspan="8" style="text-align:center">Products Not Found :</td></tr>' . implode('', $notfound) . '</table>';
    }

    if (count($found)) {
        $notfound_tooltip .= '<table><tr><td colspan="8" style="text-align:center">Products Found :</td></tr>' . implode('', $found) . '</table>';
    }

    $display_name = ('' != $invoice['bill_company']) ? $invoice['bill_company'] : $invoice['bill_name'];

    $payment_tooltip = sprintf(
        _('<table style="width:300px;text-align:center;"><tr><td>Amount :</td><td>$%s %s</td></tr><tr><td>Payment Method :</td><td>%s</td></tr>'),
        $invoice['invoice_offer'],
        htmlspecialchars($invoice['invoice_currency']),
        $invoice['paymenttype_id']
    );
//if ($payment_due > 0) $payment_tooltip.= '<tr><td>Payment Outstanding :</td><td>$' . sprintf('%.2f', $payment_due) . ' ' . $invoice['invoice_currency'] . '</td></tr>';
    if ($payment_due > 0) {
        $payment_tooltip .= sprintf(
            _('<tr><td>Payment Outstanding :</td><td>$%s %s</td></tr>'),
            convertCurrency($payment_due, $invoice["invoice_currency"]),
            htmlspecialchars($invoice['invoice_currency'])
        );
    }

    $invoice_declines = array();
    if ($query = $DB->query(sprintf(
        "SELECT *, UNIX_TIMESTAMP(`created`) AS `created_time`, 'red' AS `color` FROM `highwaygroup`.`invoice_declines` WHERE `invoice_id` = %d",
        $invoice_id
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $invoice_declines[] = $row;
        }
        $DB->free_result($query);
    }
    if ($query = $DB->query(sprintf(
        "SELECT `BatchNo`, `Amount`, `Card`, `Message`, `TxnResponseCode`, IF(`AcqResponseCode` = 'null', '--', `AcqResponseCode`) AS `AcqResponseCode`, `3DSenrolled` FROM `highwaygroup`.`credit_transactions` WHERE `BatchNo` != '0' AND (`MerchTxnRef` = %s OR `OrderInfo` REGEXP '^[A-Z]{2,3}#%05d[^\\\\d]' OR `MerchTxnRef` REGEXP '\\\\sinvoice:[A-Z]{2,3}#%05d[^\\\\d]')", // AND (`TxnResponseCode` != '0' OR `AcqResponseCode` != '00')",
        formatSQLString($invoice_id),
        $invoice_id,
        $invoice_id
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $split              = str_split($row["BatchNo"], 2);
            $invoice_declines[] = array(
                "reason"       => trim($credit_resultcode[$row["TxnResponseCode"] . $row["AcqResponseCode"]] . ((!empty($row['Message'])) ? ("\nMessage: " . $row['Message']) : '')),
                "comments"     => sprintf(
                    _("Requested amount: $%s AUD\nResponse code: %s%s\nCard Type: %s\n3DS Enrolled: %s"),
                    bcmul($row["Amount"], "0.01", 2),
                    $row["TxnResponseCode"],
                    $row["AcqResponseCode"],
                    $row["Card"],
                    ('Y' === $row["3DSenrolled"]) ? 'Yes' : 'No'
                ),
                "created_time" => mktime(0, 0, 0, intval($split[2]), intval($split[3]), intval($split[0] . $split[1])),
                "color"        => ('0' != $row["TxnResponseCode"] || '00' != $row["AcqResponseCode"]) ? 'red' : 'green',
            );
        }
        $DB->free_result($query);
    }
    if ($query = $DB->query(sprintf(
        "SELECT * FROM `highwaygroup`.`paypal_transaction` WHERE `invoice` = %d",
        $invoice_id
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $invoice_declines[] = array(
                "reason"       => $row["payment_status"] . " (PayPal)",
                "comments"     => ("Pending" == $row["payment_status"]) ? sprintf(
                    _("Payment amount: $%s %s\nPayer status: %sPending Reason: %s"),
                    $row["mc_gross"],
                    $row["mc_currency"],
                    $row["payer_status"],
                    $row["pending_reason"]
                ) : sprintf(
                    _("Payment amount: $%s %s"),
                    $row["mc_gross"],
                    $row["mc_currency"]
                ),
                "created_time" => strtotime($row["payment_date"]),
                "color"        => ('Completed' != $row["payment_status"]) ? 'red' : 'green',
            );
        }
        $DB->free_result($query);
    }

    $decline_highlight = false;
    if (count($invoice_declines)) {
        foreach ($invoice_declines as $row) {
            if ("red" == $row["color"]) {
                $decline_highlight = true;
            }
            $payment_tooltip .= sprintf(
                _('<tr><td>%s</td> <td style="color:%s;">%s</td></tr>'),
                date("d/m/Y", $row["created_time"]),
                $row["color"],
                nl2br(htmlspecialchars($row["reason"]))
            );
        }
    }

    /** To display payment confirmation
     * 29-06-2023
     */

     foreach($invoice_payments as $payments)
    {
        if($payments['payment_count'] == 1)
        {
            $payment_tooltip .= sprintf(
                _('<tr><td>%s</td> <td style="color:green;">%s</td></tr>'),
                date("d/m/Y", strtotime($payments["payment_date"])),
                nl2br(htmlspecialchars('Approved or completed successfully 
                    Message : Approved'))
            );
        }
    }

     /* End */

    $payment_tooltip .= '</table>';

    $office_tooltip = 'Office Invoice<br />--------------------------<br />' . $products_tooltip;

    $customer_tooltip   = array(htmlspecialchars($invoice['bill_address_full']));
    $customer_highlight = false;

    if (!is_null($member)) {
        if ('y' === $member['spammer']) {
            $customer_tooltip[] = "\nFlagged: Spammer";
            $customer_highlight = true;
        }
        if ('y' === $member['timewaster']) {
            $customer_tooltip[] = "\nFlagged: Time Waster";
            $customer_highlight = true;
        }
        if ('y' === $member['tester']) {
            $customer_tooltip[] = "\nFlagged: Tester";
            $customer_highlight = true;
        }
    }

    if (count($member_requests)) {
        $customer_tooltip[] = '';
        $customer_tooltip[] = "Member Requests :";
        foreach ($member_requests as $request) {
            if ('' != $request['comments']) {
                $customer_tooltip[] = '-' . htmlspecialchars($request['comments']);
            }
        }

        $customer_highlight = true;
    }

    if (count($member_warnings)) {
        $customer_tooltip[] = '';
        $customer_tooltip[] = "Member Warnings :";
        foreach ($member_warnings as $warning) {
            if ('' != $warning['comments']) {
                $customer_tooltip[] = '-' . htmlspecialchars($warning['comments']);
            }
        }

        $customer_highlight = true;
    }

    $country_range = array();
    if ($invoice["member_id"] > 0) {
        $ip = getCountryInfo(memberGetValue($invoice["member_id"], "signup_IP"));
        if ("--" != $ip["a2"] && "" != $ip["a2"]) {
            $country_range[] = $ip["a2"];
        }

        $query = $DB->query("SELECT `login_ip` FROM `highwaygroup`.`new_members_login_track` WHERE `member_id` = " . intval($invoice["member_id"]));
        while ($row = $DB->fetch_assoc($query)) {
            $ip = getCountryInfo($row["login_ip"]);
            if ("--" != $ip["a2"] && "" != $ip["a2"]) {
                $country_range[] = $ip["a2"];
            }
        }
        $DB->free_result($query);
    }
    if ("" != $invoice["bill_country"]) {
        $country_range[] = $invoice["bill_country"];
    }

    if ("" != $invoice["ship_country"]) {
        $country_range[] = $invoice["ship_country"];
    }

    if ("" != $invoice["notify_country"]) {
        $country_range[] = $invoice["notify_country"];
    }

    if ("" != $invoice["forwarder_country"]) {
        $country_range[] = $invoice["forwarder_country"];
    }

    //if ($invoice["supply_country"] != "") $country_range[] = $invoice["supply_country"];
    if ("" != $invoice["def_country"]) {
        $country_range[] = $invoice["def_country"];
    }

    //if ($invoice["origin_country"] != "") $country_range[] = $invoice["origin_country"];
    $country_range = array_unique($country_range);

    if (count($country_range) > 1) {
        $customer_tooltip[] = '';
        $customer_tooltip[] = "Warning : Multiple countries detected - (" . implode(", ", $country_range) . ")";
        $customer_highlight = true;
    }

    $customer_tooltip = nl2br(implode("\n", $customer_tooltip));
    $lh               = ($invoice['invoice_profit'] < 0 || $invoice['invoice_profitau'] < 0);
    $imgsrc           = '/webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_rect' . ((2 == $invoice['site_id'] && 'dom' == $invoice['he_id']) ? '_dom' : '') . '.png';

?>
	<tr<?=$class?>>
		<td style="text-align:center;"><img src="<?=$imgsrc?>" alt="" /></td>
		<td <?=$class?> style="<?=$lh ? 'background-color:#CCFF66' : ''?>">
			<a href="<?=htmlspecialchars($view_url . $invoice['invoice_id']. "&invoice_time=" . $preview_time)?>" target="_blank" onmouseover="<?=htmlspecialchars("tt_in(" . jecho($office_tooltip, 1) . ");")?>" onmouseout="tt_out();"><?=htmlspecialchars(invoiceFormatNumber($invoice['invoice_id'], $invoice['revision_id'], $invoice['site_id']))?></a><br />
			<br />

			<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-style:none;"><tr style="background:transparent;">
				<td style="width:33%;text-align:left;padding:0;border-style:none;">
					<a href="<?=htmlspecialchars(((in_array($invoice['site_id'], array(20, 26))) ? $aspsv_url : $sview_url) .$invoice['invoice_id']. "&invoice_time=" . $preview_time. '&format=pdf')?>" target="_blank" title="Shipping Invoice"><img src="images/shippingprinter.gif" border="0" alt="S" /></a>
				</td>
				<td style="width:33%;text-align:center;padding:0;border-style:none;">
					<a href="<?=htmlspecialchars(".?action=shiplabel&invoice_id=" . $invoice["invoice_id"]. "&invoice_time=" . $preview_time)?>" target="_blank" title="Shipping Label"><img src="images/labelprinter.png" border="0" alt="L" /></a>
				</td>
				<td style="width:33%;text-align:right;padding:0;border-style:none;">

<?php
    if (shipinvoiceExists($invoice_id)) {
?>
					<a href="<?=htmlspecialchars(((in_array($invoice['site_id'], array(20, 26))) ? $aspdv_url : $dview_url) . $invoice['invoice_id']. "&invoice_time=" . $preview_time)?>&format=pdf" target="_blank" title="Dummy Invoice"><img src="images/dummyprinter.gif" border="0" alt="D" /></a>
<?php
    }
?>
				</td>
			</tr></table>
		</td>
		<td  <?php if(!empty($needbyTooltip)){ ?>onmouseover="<?=htmlspecialchars('tt_in(' . jecho($needbyTooltip, 1) . ')') ?>" onmouseout="tt_out()" <?php } ?> style="text-align:center;<?= $styleneedby ?>"> <?=date('d/m/Y', $invoice['open_time'])?></td>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars('tt_in(' . jecho($customer_tooltip, 1) . ')')?>" onmouseout="tt_out()">
<?php
    if ($customer_highlight) {
?>
			<div style="border:2px solid #FF1493;height:57px;overflow:hidden;"><br />
<?php
    }
    if (0 != $invoice['member_id']) {
?>
				<a href="<?=htmlspecialchars($member_url . $invoice['member_id'])?>" target="_blank">
<?php
    }
?>
					<?=htmlspecialchars($display_name)?>
<?php
    if (0 != $invoice['member_id']) {
?>
				</a>
<?php
    }
    if ($customer_highlight) {
?>
			</div>
<?php
    }
    $ph = (0 == $invoice['invoice_cost'] || 0 == $invoice['invoice_costau']);
?>
		</td>
		<td style="text-align:center;<?=$ph ? 'background-color:#CC66FF' : ''?>">$<?=$invoice['invoice_offer'] . ' ' . $invoice['invoice_currency']?></td>
<?php
    if (!in_array($posted['site_id'], array(20, 26))) {
?>
		<td style="text-align:center;<?=$ph ? 'background-color:#CC66FF' : ''?>">$<?=$invoice["invoice_offerau"]?></td>
<?php
    }
    $prof = '$' . $invoice['invoice_profitau'] . ' AUD';
//if (in_array($posted['site_id'], array(20, 26))) $prof = '$' . convertCurrency($invoice['invoice_profit'], $invoice['invoice_currency'], 'USD') . ' USD';
    if (in_array($posted['site_id'], array(20, 26))) {
        $prof = '$' . $invoice['invoice_profitus'] . ' USD';
    }

?>
		<td style="text-align:center;<?=$ph ? 'background-color:#CC66FF' : ''?>"><?=$prof?></td>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars("tt_in(" . jecho($notfound_tooltip, 1) . ");")?>" onmouseout="tt_out();"><?=$products_located?></td>
<?php
    if (trim('' != $invoice['ship_needs'])) {
    $frightDisplayText = $invoice['ship_needs'].'<br />'.(($invoice['fright_tracking_service']) ? ('<b>Courier : </b>'.$fright_tracking_service[$invoice['fright_tracking_service']]) : '').'<br />'.(($invoice['proforma_tracknumber']) ? ('<b>Tracking Number : </b>'.$invoice['proforma_tracknumber']) : '');
    if($invoice['is_freight_checked'] == 'yes'){
            $shipdate = new DateTime($invoice['freight_ship_date']);
            $receiveddate = date('Y-m-d');
            if($invoice['freight_received_date']){
                $receiveddate = $invoice['freight_received_date'];
            }
            $receiveddata = new DateTime($receiveddate);

            $diff = $shipdate->diff($receiveddata);
             $frightDisplayText .= (!empty($invoice['proforma_tracknumber'])) ? ('<br />'.'<b>Transit Time  : </b>'.$diff->days.' days') : null;
        }
?>
		<td style="text-align:center;background-color:orange;"  onmouseover="<?=htmlspecialchars("tt_in(" . jecho($frightDisplayText, 1) . ")")?>" onmouseout="tt_out();"><?=$ship_located?></td>
<?php
    } else {
       $frightDisplayText =(($invoice['fright_tracking_service']) ? ('<b>Courier : </b>'.$fright_tracking_service[$invoice['fright_tracking_service']]) : '');
        $frightDisplayText .= (!empty($invoice['proforma_tracknumber'])) ? ('<br />'.('<b>Tracking Number : </b>'.$invoice['proforma_tracknumber'])) : null;
        if($invoice['is_freight_checked'] == 'yes'){
            $shipdate = new DateTime($invoice['freight_ship_date']);
            $receiveddate = date('Y-m-d');
            if($invoice['freight_received_date']){
                $receiveddate = $invoice['freight_received_date'];
            }
            $receiveddata = new DateTime($receiveddate);

            $diff = $shipdate->diff($receiveddata);
             $frightDisplayText .= (!empty($invoice['proforma_tracknumber'])) ? ('<br />'.'<b>Transit Time  : </b>'.$diff->days.' days') : null;
        }

?>
		<td style="text-align:center;" <?php if(!empty($frightDisplayText)) { ?> onmouseover="<?=htmlspecialchars("tt_in(" . jecho($frightDisplayText, 1) . ")")?>" onmouseout="tt_out();" <?php } ?>><?=$ship_located?></td>
<?php
    }
    if (!$display_inquiry) {
        if ($decline_highlight) {
?>
		<td style="text-align:center;background-color:orange;" onmouseover="<?=htmlspecialchars("tt_in(" . jecho($payment_tooltip, 1) . ");")?>" onmouseout="tt_out();"><?=$payments_located?></td>
<?php
        } else {
?>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars("tt_in(" . jecho($payment_tooltip, 1) . ");")?>" onmouseout="tt_out();"><?=$payments_located?></td>
<?php
        }
    }
?>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars("tt_in(" . jecho(htmlspecialchars($invoice['handler_note']), 1) . ")")?>" onmouseout="tt_out()"><img src="images/status/<?=$status_image?>.gif" alt="<?=htmlspecialchars($status_image)?>" /></td>
		<td style="text-align:center;"><?=htmlspecialchars($all_staff_list[$invoice['handler_id']])?></td>
<!-- code updated on 2nd July 2023 -->
<?php 
    $invoice_messages=invoiceHistoryCustomerMessage($invoice["write_date"],$invoice_id);
    $invoice_messages_tooltip='';
    if(is_array($invoice_messages)){
        if(count($invoice_messages)){
             $invoice_messages_tooltip .= '<table style="width:300px;text-align:center;">';
             foreach ($invoice_messages as $key => $message_val) {
                $msg=invoiceHistoryFromDatabase($message_val['message_id']);
                $invoice_messages_tooltip .= 
                    '<tr><td><b>'.$msg["message_title"].'</b></td></tr><tr><td>'.$msg["message_notes"].'</td></tr>';
                    
            
             }
            $invoice_messages_tooltip .= '</table>';
        }
    }
?>
<td style="text-align:center;<?= (is_array($invoice_messages) && count($invoice_messages)>0)?'background-color:#def;':''?>" onmouseover="<?=htmlspecialchars("tt_in(" . jecho($invoice_messages_tooltip, 1) . ");")?>" onmouseout="tt_out()"> <?=(is_array($invoice_messages) && count($invoice_messages)>0)?nl2br(htmlspecialchars('CUSTOMER')): htmlspecialchars($all_staff_list[$invoice['staff_id']])?></td>
<!-- end -->
		<td style="text-align:center;<?=(($invoice['write_time'] + ($_GLOBALS['site_options']['WARNING_IDLE_DAYS'] * 60 * 60 * 24)) < $_SERVER['REQUEST_TIME']) ? 'background-color:yellow;' : '' ?>"><?=date('d/m/Y', $invoice['write_time'])?></td>
		<td style="text-align:center;"><?=$invoice['days_open']?></td>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars("tt_in(" . jecho($lock_tooltip, 1) . ");")?>" onmouseout="tt_out();">
<?php
    if ($popup_window) {
?>
			<input type="button" value=" Select " onclick="window.location='<?=$select_url . $invoice['invoice_id']?>'" />
			<input type="button" value=" Edit " onclick="window.open('<?=$edit_url . $invoice['invoice_id']?>');" />
<?php
    } else {
        if (false === $invoice_lock || $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            $href = $edit_url . $invoice['invoice_id'];
            if (in_array($invoice['site_id'], array(20, 26)) && !in_array($posted['site_id'], array(20, 26))) {
                $href = $aspe_url . $invoice['invoice_id'];
            }

?>
			<a href="<?=htmlspecialchars($href)?>" target="_blank">Edit</a>
<?php
        } else {
?>
			<a href="<?=htmlspecialchars($edit_url . $invoice['invoice_id'])?>" target="_blank">View</a>
<?php
        }
        if (false !== $invoice_lock && $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
?>
			<a href="<?=htmlspecialchars($unlock_url . $invoice['invoice_id'])?>">Unlock</a>
<?php
        }
    }
?>
		</td>
	</tr>
<?php
}
?>
</table>

<?php
if (!$popup_window) {
?>
<br />

<span style="font-style:italic;">Notes:</span><br />
Rows marked in <span style="background-color:#90EEA0;">green</span> have new messages<br />
Cells marked in <span style="background-color:red;">red </span> have strict shipping date<br />
Rows marked in <span style="background-color:red;">red </span> have late shipment<br />
Rows marked in <span style="background-color:#EE90A0;">Pink</span> are locked by another user<br />
Rows marked in <span style="background-color:#90A0EE;">blue</span> are locked by you<br />
Cells marked in <span style="background-color:yellow;">yellow</span> have been idle for <?=$_GLOBALS['site_options']['WARNING_IDLE_DAYS']?> or more days<br />
Cells marked in <span style="background-color:#CC66FF;">purple</span> don't have a cost price.<br />
Cells marked in <span style="border:2px solid orange;">orange</span> have special freight requests from the customer<br />
Cells marked in <span style="border:2px solid #FF1493;">deep pink</span> have member warnings.<br />
Cells marked in <span style="background-color:#CCFF66;">light green</span> indicate a loss for the invoice.<br />
Cells marked in <span style="background-color:#def;">light blue</span> have new message from customer.<br />
<?php
}

/**
 * Finish caching the template
 */
//if (is_object($CH)) $CH->close();
?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
