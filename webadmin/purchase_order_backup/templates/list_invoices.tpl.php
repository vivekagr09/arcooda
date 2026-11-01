<!-- list_invoices.tpl -->

<?php
moduleLoad('invoice'); //invoiceHistoryFromDatabase
moduleLoad('member'); //memberRequestsFromDatabase, memberGetValue

$invoice_declines = array();
if (false && count($invoice_list)) {
    $commweb2_resultcode = array();
    $query               = $DB->query("SELECT `result_code`, `result_message` FROM `commweb2_resultcode`");
    while ($row = $DB->fetch_assoc($query)) {
        $commweb2_resultcode[$row["result_code"]] = $row["result_message"];
    }

    $DB->free_result($query);

    $anz_resultcode = array();
    $query          = $DB->query("SELECT `result_code`, `result_message` FROM `anz_resultcode`");
    while ($row = $DB->fetch_assoc($query)) {
        $anz_resultcode[$row["result_code"]] = $row["result_message"];
    }

    $DB->free_result($query);

    $commweb_resultcode = array();
    $query              = $DB->query("SELECT `result_code`, `result_message` FROM `commweb_resultcode`");
    while ($row = $DB->fetch_assoc($query)) {
        $commweb_resultcode[$row["result_code"]] = $row["result_message"];
    }

    $DB->free_result($query);

    $card_types = array(
        "AE" => "American Express",
        "MC" => "Mastercard",
        "VC" => "Visa",
    );

    $query = $DB->query("SELECT *, UNIX_TIMESTAMP(`created`) AS `created_time`, 'red' AS `color` FROM `invoice_declines` WHERE `invoice_id` IN(" . implode(", ", $invoice_list) . ")");
    while ($row = $DB->fetch_assoc($query)) {
        $invoice_declines[$row["invoice_id"]][] = $row;
    }

    $DB->free_result($query);

    $query = $DB->query("SELECT `MerchTxnRef`, `BatchNo`, `Amount`, `Card`, `TxnResponseCode`, IF(`AcqResponseCode` = 'null', '--', `AcqResponseCode`) AS `AcqResponseCode` FROM `highwaygroup`.`credit_transactions` WHERE `BatchNo` != '0' AND `MerchTxnRef` IN(" . implode(", ", $invoice_list) . ")" /* AND (`TxnResponseCode` != '0' OR `AcqResponseCode` != '00')"*/);
    while ($row = $DB->fetch_assoc($query)) {
        $split                                   = str_split($row["BatchNo"], 2);
        $invoice_declines[$row["MerchTxnRef"]][] = array(
            "reason"       => $commweb2_resultcode[$row["TxnResponseCode"] . $row["AcqResponseCode"]] . " (" . $card_types[$row["Card"]] . ")",
            "comments"     => sprintf(
                _("Requested amount: $%s AUD\nResponse code: %s%s\nCard Type: %s"),
                bcmul($row["Amount"], "0.01", 2),
                $row["TxnResponseCode"],
                $row["AcqResponseCode"],
                $row["Card"]
            ),
            "created_time" => mktime(0, 0, 0, intval($split[2]), intval($split[3]), intval($split[0] . $split[1])),
            "color"        => ('0' != $row["TxnResponseCode"] || '00' != $row["AcqResponseCode"]) ? 'red' : 'green',
        );
    }
    $DB->free_result($query);
    $query = $DB->query("SELECT * FROM `paypal_transaction` WHERE `invoice` IN(" . implode(", ", $invoice_list) . ")" /* AND `payment_status` != 'Completed'"*/);
    while ($row = $DB->fetch_assoc($query)) {
        $invoice_declines[$row["invoice"]][] = array(
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

$sort_url           = $get_vars;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$tmp      = $sort_url;
$sort_url = 'index.php?' . http_build_query($sort_url);
$sort_url .= '&order_by=';

$asps_url = '.?' . http_build_query($tmp);
$asps_url .= '&order_id=';

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['order_id']);
$tmp      = $edit_url;
$edit_url = 'index.php?' . http_build_query($edit_url);
$edit_url .= '&order_id=';

$aspe_url = '.?' . http_build_query($tmp);
$aspe_url .= '&order_id=';

$view_url           = $get_vars;
$view_url['action'] = 'view';
unset($view_url['order_id']);
$tmp      = $view_url;
$view_url = 'index.php?' . http_build_query($view_url);
$view_url .= '&order_id=';

$aspv_url = '.?' . http_build_query($tmp);
$aspv_url .= '&order_id=';

$sview_url           = $get_vars;
$sview_url['action'] = 'shipview';
$sview_url['no_sig'] = 'please';
unset($sview_url['order_id']);
$tmp       = $sview_url;
$sview_url = 'index.php?' . http_build_query($sview_url);
$sview_url .= '&order_id=';

$aspsv_url = '.?' . http_build_query($tmp);
$aspsv_url .= '&order_id=';

$member_url           = $get_vars;
$member_url['action'] = 'edit';
unset($member_url['supplier_id']);
$member_url = '/webadmin/supplier_new/?' . http_build_query($member_url) . '&supplier_id=';

$unlock_url           = $get_vars;
$unlock_url['action'] = 'unlock';
unset($unlock_url['order_id']);
$tmp        = $unlock_url;
$unlock_url = 'index.php?' . http_build_query($unlock_url);
$unlock_url .= '&order_id=';

$aspu_url = '.?' . http_build_query($tmp);
$aspu_url .= '&order_id=';

$select_url           = $get_vars;
$select_url['action'] = 'select';
unset($select_url['order_id']);
$tmp        = $select_url;
$select_url = 'index.php?' . http_build_query($select_url);
$select_url .= '&order_id=';

$aspsl_url = '.?' . http_build_query($tmp);
$aspsl_url .= '&order_id=';

global $_GLOBALS;
// $permissions      = getInquiryPermissions($_GLOBALS['staff']['user']);
$producttype_list = getBaseProductTypes();
?>
<!-- list_invoices.tpl -->

<table class="wide spacetable table_grid" align="center">
	<tr>
		<!-- <th><a href="<?php hecho($sort_url . 'site_id')?>">Site</a><?=('site_id' == $order_by) ? $ord_img : ''?></th> -->
		<th><a href="<?php hecho($sort_url . 'order_id')?>"><?=($display_both) ? 'Inquiry/Invoice' : (($display_inquiry) ? 'Inquiry' : 'Invoice')?> #</a><?=('order_id' == $order_by) ? $ord_img : ''?></th>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'open_date')?>">Open Date</a><?=('open_date' == $order_by) ? $ord_img : ''?></th>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'bill_name')?>">Company</a><?=('bill_name' == $order_by) ? $ord_img : ''?></th>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'invoice_offer')?>">Invoice Value</a><?=('invoice_offer' == $order_by) ? $ord_img : ''?></th>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'producttype_id')?>">USD Value</a><?=('producttype_id' == $order_by) ? $ord_img : ''?></th>
<?php
/*if (!in_array($posted['site_id'], array(3, 20, 26))) {
?>
        <th style="min-width:54px;"><a href="<?php hecho($sort_url . 'invoice_offerau')?>">AUD Value</a><?=('invoice_offerau' == $order_by) ? $ord_img : ''?></th>
<?php
}*/
if (in_array(2, $permissions)) {
?>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'invoice_profit')?>">Invoice Profit</a><?=('invoice_profit' == $order_by) ? $ord_img : ''?></th>
<?php
}
?>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'product_locate_date')?>">Supplier</a><?=('product_locate_date' == $order_by) ? $ord_img : ''?></th>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'freight_locate_date')?>">Order Approved</a><?=('freight_locate_date' == $order_by) ? $ord_img : ''?></th>
<?php
//if ($display_invoice) {
?>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'payments_locate_date')?>">Payment Made</a><?=('payments_locate_date' == $order_by) ? $ord_img : ''?></th>
<?php
//}
?>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'status')?>">Goods Received</a><?=('status' == $order_by) ? $ord_img : ''?></th>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'handler_id')?>">Order Status</a><?=('handler_id' == $order_by) ? $ord_img : ''?></th>
        <th style="min-width:54px;"><a href="<?php hecho($sort_url . 'handler_id')?>">Order  BY</a><?=('handler_id' == $order_by) ? $ord_img : ''?></th>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'staff_id')?>">Last Staff</a><?=('staff_id' == $order_by) ? $ord_img : ''?></th>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'write_date')?>">Last Modified</a><?=('write_date' == $order_by) ? $ord_img : ''?></th>
		<th style="min-width:54px;"><a href="<?php hecho($sort_url . 'open_date')?>">Days Open</a><?=('open_date' == $order_by) ? $ord_img : ''?></th>
		<th style="min-width:54px;">&nbsp;</th>
	</tr>

<?php
$inquiry_list = array();

foreach ($invoice_list as $invoice_id) {
    $invoice = purchaseOrderFromDatabase($invoice_id);
        $invoice_lock     = purchaseOrderGetLock($invoice_id);
        $invoice_jobs     = purchaseOrderJobsFromDatabase($invoice_id);
        $invoice_products = purchaseOrderProductsFromDatabase($invoice_id);
        $invoice_charges  = purchaseOrderChargesFromDatabase($invoice_id);
        $invoice_payments = purchaseOrderPaymentsFromDatabase($invoice_id);
        if (0 != $invoice['supplier_id']) {
            $member_requests = array();
        }
        else {
            $member_requests = array(array('comments' => 'Invoice is not linked to a member'));
        }

        $edit_url = $invoice_edit_url;
        $view_url = $invoice_view_url;

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
        if (false !== $invoice_lock) {
            $class = ' class="locked"';
            if ($invoice_lock['staff_id'] != $staff_id) {
                $staff_list   = purchaseOrderGetOptionList('staff_id');
                $lock_tooltip = hecho('This purchase order is currently locked to ' . $staff_list[$invoice_lock['staff_id']], 1);
            }
        }
        if ($invoice_lock['staff_id'] == $staff_id) {
            $class = ' class="mylock"';
        }

        $payment_due = $invoice['invoice_offer'];
        foreach ($invoice_payments as $payment) {
            $payment_due = bcsub($payment_due, convertCurrency($payment['payment_amount'], $payment["payment_currency"], $invoice["invoice_currency"]), 2);
        }

        $payments_located = '<i class="invoiceicon-' . $invoice['payments_located'] . '"></i>';
        if (count($invoice_payments) && bccomp($payment_due, "0.05", 2) >= 0) {
            $payments_located = '<img src="images/status/deposit.gif" alt="Deposit" />';
        }

        if (count($invoice_payments) && bccomp($payment_due, "0.05", 2) < 0 && $invoice['invoice_offer'] > 0.0) {
            $payments_located = '<i class="invoiceicon-y"></i>';
        }

        $products_located = '<img src="images/status/' . (('y' == $invoice['products_located'] || $invoice['products_found'] == $invoice['products_needed']) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['products_located'] || $invoice['products_found'] == $invoice['products_needed']) ? 'Yes' : 'No') . '" />';
        $payment_received = '<img src="images/status/' . (('y' == $invoice['payments_located'] || $invoice['products_found'] == $invoice['payments_located']) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['payments_located'] || $invoice['products_found'] == $invoice['payments_located']) ? 'Yes' : 'No') . '" />';
         $order_approved = '<img src="images/status/' . (('processed' == $invoice['order_action'] ) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['products_located'] || $invoice['products_found'] == $invoice['products_needed']) ? 'processed' : 'No') . '" />';
          $goods_received = '<img src="images/status/' . (('y' == $invoice['goods_checked'] ) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['goods_checked'] || $invoice['goods_checked'] == $invoice['goods_checked']) ? 'checked' : 'Not Checked') . '" />';
          $ship_located = '<i class="invoiceicon-' . $invoice['ship_located'] . '"></i>';
        if ('y' != $invoice['products_located'] && $invoice['products_found'] < $invoice['products_needed']) {
            $products_located = '<span style="color:red;">' . $invoice['products_found'] . '/' . $invoice['products_needed'] . '</span>';
        }
        $ship_located = '<i class="invoiceicon-' . $invoice['ship_located'] . '"></i>';
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

        $img_dir      = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/images/';
        $product_type = '';
        if (file_exists($img_dir . strtolower($producttype_list[$invoice['producttype_id']]) . ".jpg")) {
            $product_type = '<img src="images/' . strtolower($producttype_list[$invoice['producttype_id']]) . '.jpg" />';
        } else {
            $product_type = '<img src="images/other.jpg" />';
        }

        $products_tooltip = array();
        $notfound         = array();
        $found            = array();
        $symbol           = getCurrencySymbol($invoice_currency);
        foreach ($invoice_products as $product) {
            $product_tr         = '<tr><td style="text-align:right;">' . $product['product_qty'] . '</td><td>x</td><td nowrap="nowrap">#' . purchaseOrderProductFormatNumber($product['product_id'], $product['sub_id']) . ': ' . $product['product_name'] . '</td><td>@</td><td style="text-align:right;">' . convertCurrency($product['product_offer'], $invoice_currency) . '</td><td>=' . $symbol . '</td><td style="text-align:right;">' . convertCurrency($product['product_offer'] * $product['product_qty'], $invoice_currency) . '</td><td>' . $invoice_currency . '</td></tr>';
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

        $display_name = $invoice['bill_name'];

        $payment_tooltip = '<table style="width:300px;text-align:center"><tr><td>Amount :</td><td>$' . $invoice['invoice_offer'] . ' ' . $invoice['invoice_currency'] . '</td></tr><tr><td>Payment Method :</td><td>' . $invoice['paymenttype_id'] . '</td></tr>';
        //if ($payment_due > 0) $payment_tooltip.= '<tr><td>Payment Outstanding :</td><td>$' . sprintf('%.2f', $payment_due) . ' ' . $invoice['invoice_currency'] . '</td></tr>';
        if ($payment_due > 0) {
            $payment_tooltip .= '<tr><td>Payment Outstanding :</td><td>$' . convertCurrency($payment_due, $invoice["invoice_currency"]) . ' ' . $invoice['invoice_currency'] . '</td></tr>';
        }

        $decline_highlight = false;
        if (array_key_exists($invoice_id, $invoice_declines)) {
            foreach ($invoice_declines[$invoice_id] as $row) {
                if ("red" == $row["color"]) {
                    $decline_highlight = true;
                }

                $payment_tooltip .= '<tr><td>' . date("d/m/Y", $row["created_time"]) . '</td> <td style="color:' . $row["color"] . ';">' . nl2br(hecho($row["reason"], 1)) . '</td></tr>';
            }
        }

        $payment_tooltip .= '</table>';

        $office_tooltip = 'Office Invoice<br />--------------------------<br />' . $products_tooltip;

        $customer_tooltip   = array($invoice['bill_address_full']);
        $customer_highlight = false;
        if (count($member_requests)) {
            $customer_tooltip[] = '';
            $customer_tooltip[] = "Supplier Warnings :";
            foreach ($member_requests as $request) {
                if ('' != $request['comments']) {
                    $customer_tooltip[] = '-' . $request['comments'];
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
        if ("" != $invoice["def_country"]) {
            $country_range[] = $invoice["def_country"];
        }
        $country_range = array_unique($country_range);
        if (count($country_range) > 1) {
            $customer_tooltip[] = '';
            $customer_tooltip[] = "Warning : Multiple countries detected - (" . implode(", ", $country_range) . ")";
            $customer_highlight = true;
        }

        $customer_tooltip = nl2br(implode("\n", $customer_tooltip));
        $lh               = ($invoice['add_to_inventory']=='y')?true:false;
        $imgsrc           = '/webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_rect' . ((2 == $invoice['site_id'] && 'dom' == $invoice['he_id']) ? '_dom' : '') . '.png';

?>
	<tr<?=$class?>>
		<!-- <td style="text-align:center;"><img src="<?=$imgsrc?>" alt="" /></td> -->
		<td <?=$class?> style="<?=$lh ? 'background-color:#d8cbde' : ''?>">
			<a href="<?php hecho($view_url . $invoice['order_id'])?>" target="_blank" onmouseover="<?php hecho("tt_in(" . jecho($office_tooltip, 1) . ");")?>" onmouseout="tt_out();"><?php hecho($invoice["invoice_number"])?></a><br />
			<br />

			<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-style:none;"><tr style="background:transparent;">
				<!-- <td style="width:33%;text-align:left;padding:0;border-style:none;">
					<a href="<?php hecho(((in_array($invoice['site_id'], array(20, 26))) ? $aspsv_url : $sview_url) . $invoice['invoice_id'])?>" target="_blank" title="Shipping Invoice"><img src="images/shippingprinter.gif" border="0" alt="S" /></a>
				</td>
				<td style="width:33%;text-align:center;padding:0;border-style:none;">
					<a href="<?php hecho("index.php?action=shiplabel&invoice_id=" . $invoice["invoice_id"])?>" target="_blank" title="Shipping Label"><img src="images/labelprinter.png" border="0" alt="L" /></a>
				</td> -->
				<td style="width:33%;text-align:right;padding:0;border-style:none;">
				</td>
			</tr></table>
		</td>
		<td style="text-align:center;"><?=date('d/m/Y', $invoice['open_time'])?></td>
        <td style="text-align:center;"><img src="/img/payment_status/sites/site_<?php hecho($invoice['site_id'])?>/logo_invoice.png" width="100px;"></td>
		<td style="text-align:center;<?=$ph ? 'background-color:#CC66FF' : ''?>">$<?=$invoice['order_cost'] . ' ' . $invoice['invoice_currency']?></td>
		<!--<td style="text-align:center;">$<?=$invoice['invoice_offerus']?></td>-->
		<td style="text-align:center;"><?=convertCurrency($invoice['order_cost'], $invoice["invoice_currency"], 'USD')?></td>
        <td style="text-align:center;" onmouseover="<?php hecho("tt_in(" . jecho($customer_tooltip, 1) . ")")?>" onmouseout="tt_out()">
<?php
        if ($customer_highlight) {
?>
            <div style="border:2px solid #FF1493;height:57px;"><br />
<?php
        }
        if (0 != $invoice['supplier_id']) {
?>
                <a href="<?php hecho($member_url . $invoice['supplier_id'])?>" target="_blank">
<?php
        }
?>
                    <?php hecho($display_name)?>
<?php
        if (0 != $invoice['supplier_id']) {
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
        <td> <?= $order_approved?> </td>
		 <td> <?= $payment_received?> </td>

<?php

        $status_tooltip = array();

        $invoice_history = purchaseOrderHistorySearch(array('order_id' => $invoice_id, 'message_type_id' => array(INVOICE_MESSAGE_TO_CUSTOMER, INVOICE_MESSAGE_INVOICE_SENT)));
        if (count($invoice_history)) {
            $invoice_history = invoiceHistoryFromDatabase(reset($invoice_history));
            $status_tooltip[] = sprintf(
                "%s - %s",
                date('d/m/Y', $invoice_history['message_time']),
                'Last Contacted' //$invoice_history['message_title']
            );
        } else {
            $status_tooltip[] = 'Not Contacted';
        }

        $handler_status_list = invoiceGetOptionList('handler_status');
        if ('' !== $invoice['handler_status']) $status_tooltip[] = $handler_status_list[$invoice['handler_status']];

        if ('' !== $invoice['handler_note']) $status_tooltip[] = hecho($invoice['handler_note'], 1);

        $status_tooltip = 'tt_in(' . jecho(implode("<br />", $status_tooltip), 1) . ');';
?>
		<td style="text-align:center;"><?=$goods_received?></td>
		<td style="text-align:center;"><img src="/img/payment_status/<?php hecho($invoice['order_action'])?>.png" width="50px;"></td>
		<td style="text-align:center;"><?php hecho($all_staff_list[$invoice['staff_id']])?></td>
        <td style="text-align:center;"><?php hecho($all_staff_list[$invoice['staff_id']])?></td>
		<td style="text-align:center;<?=(($invoice['write_time'] + ($_GLOBALS['site_options']['WARNING_IDLE_DAYS'] * 60 * 60 * 24)) < time()) ? 'background-color:yellow;' : ''?>"><?=date('d/m/Y', $invoice['write_time'])?></td>
		<td style="text-align:center;"><?=$invoice['days_open']?></td>
		<td style="text-align:center;" onmouseover="<?php hecho("tt_in(" . jecho($lock_tooltip, 1) . ");")?>" onmouseout="tt_out();">
<?php
if ($popup_window) {
?>
			<input type="button" value=" Select " onclick="window.location='<?=$select_url . $invoice['order_id']?>'" />
			<!-- <input type="button" valueorder_idEdit " onclick="window.open('<?=$edit_url . $invoice['invoice_id']?>');" /> -->
<?php
} else {
?>
    <a href="<?php hecho($edit_url . $invoice['order_id'])?>" target="_blank">Edit</a>
<?php
    if (false === $invoice_lock || $invoice_lock['staff_id'] == $staff_id) {
        //Edit_url usually goes here
} else {
?>
			<a href="<?php hecho($view_url . $invoice['order_id'])?>" target="_blank">View</a>
<?php
}
            if (false !== $invoice_lock && $invoice_lock['staff_id'] == $staff_id) {
?>
			<a href="<?php hecho($unlock_url . $invoice['order_id'])?>">Unlock</a>
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
Cells marked in <span style="background-color:#d8cbde;">light purple</span> indicate inventory has been added.<br />
<?php
}
?>

<!-- /list_invoices.tpl -->
