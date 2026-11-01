<!-- list.tpl -->

<?php
moduleLoad('member'); //memberWarningsFromDatabase, memberGetValue
moduleLoad('shipinvoice');

global $_GLOBALS;
global $DB;

define("INQUIRY_PERMISSION_COST", 2);

$invoice_declines = array();
if (count($invoice_list)) {
    $credit_resultcodes = array();
    if ($query = $DB->query(sprintf(
        "SELECT `result_code`, `result_message` FROM `highwaygroup`.`credit_resultcodes`"
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $credit_resultcodes[$row["result_code"]] = $row["result_message"];
        }
        $DB->free_result($query);
    }

    $card_types = array(
        "AE" => "American Express",
        "MC" => "Mastercard",
        "VC" => "Visa",
    );

    if ($query = $DB->query(sprintf(
        "SELECT *, UNIX_TIMESTAMP(`created`) AS `created_time`, 'red' AS `color` FROM `highwaygroup`.`invoice_declines` WHERE `invoice_id` IN(%s)",
        implode(", ", $invoice_list)
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $invoice_declines[$row["invoice_id"]][] = $row;
        }
        $DB->free_result($query);
    }

    if ($query = $DB->query(sprintf(
        "SELECT `MerchTxnRef`, `Amount`, `TxnResponseCode`, `AcqResponseCode`, `BatchNo`, `Card`, `3DSenrolled` FROM `highwaygroup`.`credit_transactions` WHERE `TxnResponseCode` IS NOT NULL AND `MerchTxnRef` IS NOT NULL AND (`TxnResponseCode` <> '0' OR `AcqResponseCode` <> '00') AND (`MerchTxnRef` REGEXP '\\\\sinvoice:[A-Z]{2,3}#" . "(%s)[^\\\\d]' OR `MerchTxnRef` IN('%s'))",
        implode("|", $invoice_list),
        implode("', '", $invoice_list)
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $response_code                           = $row['TxnResponseCode'] . ((is_null($row['AcqResponseCode'])) ? '--' : $row['AcqResponseCode']);
            $split                                   = str_split($row["BatchNo"], 2);
            $invoice_declines[$row["MerchTxnRef"]][] = array(
                "reason"       => $credit_resultcodes[$response_code] . " (" . $card_types[$row["Card"]] . ")",
                "comments"     => sprintf(
                    _("Requested amount: $%s AUD\nResponse code: %s%s\nCard Type: %s\n3DS Enrolled: %s"),
                    bcmul($row["Amount"], "0.01", 2),
                    $row["TxnResponseCode"],
                    $row["AcqResponseCode"],
                    $row["Card"],
                    ('Y' === $row["3DSenrolled"]) ? 'Yes' : 'No'
                ),
                "created_time" => mktime(0, 0, 0, intval($split[2]), intval($split[3]), intval($split[0] . $split[1])),
                "color"        => ('0' === $row["TxnResponseCode"] && '00' === $row["AcqResponseCode"]) ? 'green' : 'red',
            );
        }
        $DB->free_result($query);
    }

    if ($query = $DB->query(sprintf(
        "SELECT `invoice`, `mc_currency`, `mc_gross`, `payer_status`, `payment_status`, `payment_date`, `pending_reason` FROM `highwaygroup`.`paypal_transaction` WHERE `invoice` IN('%s')",
        implode("', '", $invoice_list)
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $invoice_declines[$row["invoice"]][] = array(
                "reason"       => $row["payment_status"] . " (PayPal)",
                "comments"     => ("Pending" == $row["payment_status"]) ? sprintf("Payment amount: $%s %s\nPayer status: %sPending Reason: %s", $row["mc_gross"], $row["mc_currency"], $row["payer_status"], $row["pending_reason"]) : sprintf("Payment amount: $%s %s", $row["mc_gross"], $row["mc_currency"]),
                "created_time" => strtotime($row["payment_date"]),
                "color"        => ('Completed' != $row["payment_status"]) ? 'red' : 'green',
            );
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
$order_by,
$_GET,
$invoice_list,
$invoice_declines,
$display_inquiry,
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

$urls = array();
$temp = array_merge($_GET, array(
    "action" => "list",
    "order"  => ("desc" == $order) ? "asc" : "desc",
));
unset($temp["page"]);
unset($temp["order_by"]);
$urls["edit"] = ".?action=edit&id=";
$temp         = array_merge($_GET, array(
    "action" => "unlock",
));
unset($temp["id"]);
$urls["select"]        = ".?action=select&id=";
$urls["sort"]          = ".?" . http_build_query($temp) . "&order_by=";
$urls["unlock"]        = ".?" . http_build_query($temp) . "&id=";
$urls["view_invoice"]  = ".?action=view&id=";
$urls["view_shipping"] = ".?action=shipview&no_sig=please&id=";

$urls["view_dummy"]  = "/webadmin/shipinvoice/?action=view&id=";
$urls["edit_member"] = "/webadmin/member/?action=edit&id=";

?>
<table class="wide spacetable table_grid" style="margin:auto">
	<tr>
<?php
if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
?>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'site_id')?>"><?=htmlspecialchars(_("Site"))?></a><?=('site_id' == $order_by) ? $ord_img : ''?></th>
<?php
}
?>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'invoice_id')?>"><?=htmlspecialchars(_(($display_inquiry) ? 'Inquiry #' : 'Invoice #'))?></a><?=('invoice_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'open_date')?>"><?=htmlspecialchars(_("Open Date"))?></a><?=('open_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'bill_name')?>"><?=htmlspecialchars(_("Customer"))?></a><?=('bill_name' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'invoice_offerau')?>"><?=htmlspecialchars(_("Invoice Value"))?></a><?=('invoice_offerau' == $order_by) ? $ord_img : ''?></th>
		<!-- <th><a href="<?=htmlspecialchars($urls["sort"] . 'producttype_id')?>"><?=htmlspecialchars(_("Product Type"))?></a><?=('producttype_id' == $order_by) ? $ord_img : ''?></th> -->
<?php
if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
?>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'invoice_offerau')?>"><?=htmlspecialchars(_("AUD Value"))?></a><?=('invoice_offerau' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'invoice_offerus')?>"><?=htmlspecialchars(_("USD Value"))?></a><?=('invoice_offerus' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'invoice_profit')?>"><?=htmlspecialchars(_("Invoice Profit"))?></a><?=('invoice_profit' == $order_by) ? $ord_img : ''?></th>
<?php
}
?>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'product_locate_date')?>"><?=htmlspecialchars(_("Products Located"))?></a><?=('product_locate_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'freight_locate_date')?>"><?=htmlspecialchars(_("Freight Located"))?></a><?=('freight_locate_date' == $order_by) ? $ord_img : ''?></th>
<?php
if (!$display_inquiry) {
?>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'payments_locate_date')?>"><?=htmlspecialchars(_("Payment Made"))?></a><?=('payments_locate_date' == $order_by) ? $ord_img : ''?></th>
<?php
}
?>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'status')?>"><?=htmlspecialchars(_("Status"))?></a><?=('status' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'handler_id')?>"><?=htmlspecialchars(_("Handler"))?></a><?=('handler_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'staff_id')?>"><?=htmlspecialchars(_("Last Staff"))?></a><?=('staff_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'write_date')?>"><?=htmlspecialchars(_("Last Modified"))?></a><?=('write_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($urls["sort"] . 'open_date')?>"><?=htmlspecialchars(_("Days Open"))?></a><?=('open_date' == $order_by) ? $ord_img : ''?></th>
		<th>&nbsp;</th>
	</tr>
<?php
$invoice_locks = invoiceGetLocks();

foreach ($invoice_list as $id) {
    $invoice = invoiceFromDatabase($id);
    //$invoice_lock = invoiceGetLock($id);
    $invoice_lock = false;
    if (array_key_exists($id, $invoice_locks)) {
        $invoice_lock = $invoice_locks[$id];
    }

    $invoice_jobs     = invoiceJobsFromDatabase($id);
    $invoice_products = invoiceProductsFromDatabase($id);
    $invoice_charges  = invoiceChargesFromDatabase($id);
    $invoice_payments = invoicePaymentsFromDatabase($id);
    if (0 != $invoice['member_id']) {
        $member_warnings = memberWarningsFromDatabase($invoice['member_id']);
    } else {
        $member_warnings = array(array('comments' => 'Invoice is not linked to a member'));
    }

    $invoice_currency = $invoice['invoice_currency'];
    $class            = '';
    if ($alt = !$alt) {
        $class = ' class="alt"';
    }

    if (count($invoice_jobs) > 0) {
        $class = ' class="highlight"';
    }

    if ($invoice['staff_id'] != $invoice['handler_id']) {
        $class = ' class="highlight"';
    }

    if (0 != $invoice['needby_time'] && time() > $invoice['needby_time'] - 604800) {
        $class = ' class="need_soon"';
    }

    if (0 != $invoice['needby_time'] && time() > $invoice['needby_time']) {
        $class = ' class="need_timeout"';
    }

    $lock_tooltip = '';
    if (!empty($invoice_lock)) {
        $class = ' class="locked"';
        if ($invoice_lock['staff_id'] != $_GLOBALS['staff']['id']) {
            $staff_list   = invoiceGetOptionList('staff_id');
            $lock_tooltip = htmlspecialchars('This invoice/inquiry is currently locked to ' . $staff_list[$invoice_lock['staff_id']]);
        }
    }
    if (!empty($invoice_lock) && $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
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

    $products_located = '<img src="images/status/' . (('y' == $invoice['products_located'] || $invoice['products_found'] == $invoice['products_needed']) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['products_located'] || $invoice['products_found'] == $invoice['products_needed']) ? 'Yes' : 'No') . '" />';

    $ship_located = '<img src="images/status/' . (('y' == $invoice['ship_located']) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['ship_located']) ? 'Yes' : 'No') . '" />';
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
        //Inquiry
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
        //Invoice
        $status_image = $invoice['invoice_action'];
        if ("return" == $invoice["invoice_status"]) {
            $status_image = "returned";
        }

        if ("completed" == $invoice["invoice_status"]) {
            $status_image = "processed";
        }

        //if ($invoice_type < 5) {
        //    if ($invoice['ship_located'] == 'n') $status_image = 'shipping';
        //    if ($invoice['payments_located'] == 'n') $status_image = 'payment';
        //}
    }

    $products_tooltip = array();
    $notfound         = array();
    $found            = array();
    $symbol           = getCurrencySymbol($invoice_currency, 'HTML');
    foreach ($invoice_products as $product) {
        $product_tr         = '<tr><td style="text-align:right;">' . $product['product_qty'] . '</td><td>x</td><td nowrap="nowrap">#' . invoiceProductFormatNumber($product['product_id'], $product['sub_id']) . ': ' . $product['product_name'] . '</td><td>@</td><td style="text-align:right;">' . convertCurrency($product['product_offer'], $invoice_currency) . '</td><td>=' . $symbol . '</td><td style="text-align:right;">' . convertCurrency($product['product_offer'] * $product['product_qty'], $invoice_currency) . '</td><td>' . $invoice_currency . '</td></tr>';
        $products_tooltip[] = $product_tr;
        if ('n' == $product['supplier_found']) {
            $notfound[] = $product_tr;
        }

        if ('y' == $product['supplier_found'] || 's' == $product['supplier_found']) {
            $found[] = $product_tr;
        }
    }
    foreach ($invoice_charges as $charge) {
        $charge_tr          = '<tr><td style="text-align:right;">' . $charge['charge_qty'] . '</td><td>x</td><td nowrap="nowrap">C' . sprintf("%02d", $charge['charge_id']) . ': ' . $charge['charge_name'] . '</td><td>@</td><td style="text-align:right;">' . convertCurrency($charge['charge_offer'], $invoice_currency) . '</td><td>=' . $symbol . '</td><td style="text-align:right;">' . convertCurrency($charge['charge_offer'] * $charge['charge_qty'], $invoice_currency) . '</td><td>' . $invoice_currency . '</td></tr>';
        $products_tooltip[] = $charge_tr;
    }
    $ship_offer = convertCurrency($invoice['ship_offer'], $invoice_currency);
    if ('' != $invoice['ship_product_name']) {
        $products_tooltip[] = '<tr><td style="text-align:right;">1</td><td>x</td><td nowrap>F' . str_pad($invoice['ship_id'], 2, '0', STR_PAD_LEFT) . (('y' == $invoice['ship_insurance']) ? 'I' : 'N') . ' ' . $invoice['ship_product_name'] . '</td><td>@</td> <td>' . $ship_offer . '</td><td>=' . $symbol . '</td><td style="text-align:right;">' . $ship_offer . '</td><td>' . $invoice_currency . '</td></tr>';
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

    $payment_tooltip = '<table style="width:300px;text-align:center"><tr><td>Amount :</td><td>$' . $invoice['invoice_offer'] . ' ' . $invoice['invoice_currency'] . '</td></tr><tr><td>Payment Method :</td><td>' . $invoice['paymenttype_id'] . '</td></tr>';
//if ($payment_due > 0) $payment_tooltip.= '<tr><td>Payment Outstanding :</td><td>$' . sprintf('%.2f', $payment_due) . ' ' . $invoice['invoice_currency'] . '</td></tr>';
    if ($payment_due > 0) {
        $payment_tooltip .= '<tr><td>Payment Outstanding :</td><td>$' . convertCurrency($payment_due, $invoice["invoice_currency"]) . ' ' . $invoice['invoice_currency'] . '</td></tr>';
    }

    $decline_highlight = false;
    if (array_key_exists($id, $invoice_declines)) {
        foreach ($invoice_declines[$id] as $row) {
            if ("red" == $row["color"]) {
                $decline_highlight = true;
            }

            $payment_tooltip .= '<tr><td>' . date("d/m/Y", $row["created_time"]) . '</td> <td style="color:' . $row["color"] . ';">' . nl2br(htmlspecialchars($row["reason"])) . '</td></tr>';
        }
    }

    $payment_tooltip .= '</table>';

    $office_tooltip = 'Office Invoice<br />--------------------------<br />' . $products_tooltip;

    $customer_tooltip   = array($invoice['bill_address_full']);
    $customer_highlight = false;
    if (count($member_warnings)) {
        $customer_tooltip[] = '';
        $customer_tooltip[] = "Member Warnings :";
        foreach ($member_warnings as $warning) {
            if ('' != $warning['comments']) {
                $customer_tooltip[] = '-' . $warning['comments'];
            }
        }

        $customer_highlight = true;
    }
    if ("Daniel" == $_SERVER["REMOTE_USER"]) {
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
    }

    $customer_tooltip = nl2br(implode("\n", $customer_tooltip));
    $lh               = ($invoice['invoice_profit'] < 0 || $invoice['invoice_profitau'] < 0);
    $imgsrc           = '/webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_rect' . ((2 == $invoice['site_id'] && 'dom' == $invoice['he_id']) ? '_dom' : '') . '.png';

?>
	<tr<?=$class?>>
<?php
    if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
?>
		<td style="text-align:center;"><img src="<?=$imgsrc?>" alt="" /></td>
<?php
    }
?>
		<td <?=$class?> style="<?=$lh ? 'background-color:#CCFF66' : ''?>">
			<a href="<?=htmlspecialchars($urls["view"] . $invoice['invoice_id'])?>" target="_blank" onmouseover="<?=htmlspecialchars("tt_in(" . json_encode($office_tooltip) . ");")?>" onmouseout="tt_out();"><?=htmlspecialchars(invoiceFormatNumber($invoice['invoice_id'], $invoice['revision_id'], $invoice['site_id']))?></a><br />
			<br />

			<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-style:none;"><tr style="background:transparent;">
				<td style="width:33%;text-align:left;padding:0;border-style:none;">
					<a href="<?=htmlspecialchars(((in_array($invoice['site_id'], array(20, 26))) ? $aspsv_url : $sview_url) . $invoice['invoice_id'])?>" target="_blank" title="Shipping Invoice"><img src="images/shippingprinter.gif" border="0" alt="S" /></a>
				</td>
				<td style="width:33%;text-align:center;padding:0;border-style:none;">
					<a href="<?=htmlspecialchars(".?action=shiplabel&invoice_id=" . $invoice["invoice_id"])?>" target="_blank" title="Shipping Label"><img src="images/labelprinter.png" border="0" alt="L" /></a>
				</td>
				<td style="width:33%;text-align:right;padding:0;border-style:none;">

<?php
    if (shipinvoiceExists($id)) {
?>
					<a href="<?=htmlspecialchars(((in_array($invoice['site_id'], array(20, 26))) ? $aspdv_url : $dview_url) . $invoice['invoice_id'])?>" target="_blank" title="Dummy Invoice"><img src="images/dummyprinter.gif" border="0" alt="D" /></a>
<?php
    }
?>
				</td>
			</tr></table>
		</td>
		<td style="text-align:center;"><?=date('d/m/Y', $invoice['open_time'])?></td>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode($customer_tooltip) . ')')?>" onmouseout="tt_out()">
<?php
    if ($customer_highlight) {
?>
			<div style="border:2px solid #FF1493;height:57px;"><br />
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
    if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
?>
		<td style="text-align:center;<?=$ph ? 'background-color:#CC66FF' : ''?>">$<?=$invoice["invoice_offerau"]?></td>
		<td style="text-align:center;<?=$ph ? 'background-color:#CC66FF' : ''?>">$<?=$invoice["invoice_offerus"]?></td>
<?php
        $prof = '$' . $invoice['invoice_profitau'] . ' AUD';
        if (in_array($posted['site_id'], array(20, 26))) {
            $prof = '$' . $invoice['invoice_profitus'] . ' USD';
        }

?>
		<td style="text-align:center;<?=$ph ? 'background-color:#CC66FF' : ''?>"><?=$prof?></td>
<?php
    }
?>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars("tt_in(" . json_encode($notfound_tooltip) . ");")?>" onmouseout="tt_out();"><?=$products_located?></td>
<?php
    if (trim('' != $invoice['ship_needs'])) {
?>
		<td style="text-align:center;background-color:orange;" onmouseover="<?=htmlspecialchars("tt_in(" . json_encode($invoice['ship_needs']) . ")")?>" onmouseout="tt_out();"><?=$ship_located?></td>
<?php
    } else {
?>
		<td style="text-align:center;"><?=$ship_located?></td>
<?php
    }
    if (!$display_inquiry) {
        if ($decline_highlight) {
?>
		<td style="text-align:center;background-color:orange;" onmouseover="<?=htmlspecialchars("tt_in(" . json_encode($payment_tooltip) . ");")?>" onmouseout="tt_out();"><?=$payments_located?></td>
<?php
        } else {
?>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars("tt_in(" . json_encode($payment_tooltip) . ");")?>" onmouseout="tt_out();"><?=$payments_located?></td>
<?php
        }
    }
?>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars("tt_in(" . json_encode($invoice['handler_note']) . ")")?>" onmouseout="tt_out()"><img src="images/status/<?=$status_image?>.gif" alt="<?=htmlspecialchars($status_image)?>" /></td>
		<td style="text-align:center;"><?=htmlspecialchars($all_staff_list[$invoice['handler_id']])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($all_staff_list[$invoice['staff_id']])?></td>
		<td style="text-align:center;<?=(($invoice['write_time'] + ($_GLOBALS['site_options']['WARNING_IDLE_DAYS'] * 60 * 60 * 24)) < time()) ? 'background-color:yellow;' : ''?>"><?=date('d/m/Y', $invoice['write_time'])?></td>
		<td style="text-align:center;"><?=$invoice['days_open']?></td>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars("tt_in(" . json_encode($lock_tooltip) . ");")?>" onmouseout="tt_out();">
<?php
    if ($popup_window) {
?>
			<input type="button" value=" Select " onclick="window.location='<?=$urls["select"] . $invoice['invoice_id']?>'" />
			<input type="button" value=" Edit " onclick="window.open('<?=$urls["edit"] . $invoice['invoice_id']?>');" />
<?php
    } else {
        if (empty($invoice_lock) || $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            $href = $urls["edit"] . $invoice['invoice_id'];
            if (in_array($invoice['site_id'], array(20, 26)) && !in_array($posted['site_id'], array(20, 26))) {
                $href = $aspe_url . $invoice['invoice_id'];
            }

?>
			<a href="<?=htmlspecialchars($href)?>" target="_blank">Edit</a>
<?php
        } else {
?>
			<a href="<?=htmlspecialchars($urls["view_invoice"] . $invoice['invoice_id'])?>" target="_blank">View</a>
<?php
        }
        if (!empty($invoice_lock) && $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
?>
			<a href="<?=htmlspecialchars($urls["unlock"] . $invoice['invoice_id'])?>">Unlock</a>
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
Rows marked in <span style="background-color:#EE90A0;">red</span> are locked by another user<br />
Rows marked in <span style="background-color:#90A0EE;">blue</span> are locked by you<br />
Cells marked in <span style="background-color:yellow;">yellow</span> have been idle for <?=$_GLOBALS['site_options']['WARNING_IDLE_DAYS']?> or more days<br />
Cells marked in <span style="background-color:#CC66FF;">purple</span> don't have a cost price.<br />
Cells marked in <span style="border:2px solid orange;">orange</span> have special freight requests from the customer<br />
Cells marked in <span style="border:2px solid #FF1493;">deep pink</span> have member warnings.<br />
Cells marked in <span style="background-color:#CCFF66;">light green</span> indicate a loss for the invoice.<br />
<?php
}

/**
 * Finish caching the template
 */
//if (is_object($CH)) $CH->close();
?>

<!-- /list.tpl -->
