<?php
include_once __DIR__ . "/common.inc.php";

include_once __DIR__ . "/templates/bare_header.tpl.php";

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = "payments_locate_date";
if ("" != $posted["order_by"]) {
    $order_by = strtolower($posted['order_by']);
}

//Build a search criteria array
$criteria = array("invoice_status" => "processed", "invoice_action" => "processed");
foreach (invoiceGetOptionList("all_fields") as $k => $v) {
    if (array_key_exists($k, $posted)) {
        $criteria[$k] = (is_array($posted[$k])) ? $posted[$k] : preg_split("/[^a-zA-Z0-9]+/", $posted[$k]);
    }
}

if (11 != $_GLOBALS["site_settings"]["site_id"]) {
    $criteria["site_id"] = $_GLOBALS["site_settings"]["site_id"];
}

$invoice_list = invoiceSearch($criteria);

$invoice_list = invoiceSort($invoice_list, "open_date");
$invoice_list = array_reverse($invoice_list);

if ("" != $posted["p"] && count($pass = json_decode(base64_url_decode($posted["p"])))) {
?>
<div style="text-align:left;color:#070;width:95%;">
	Successfully updated:
	<ul>
<?php
foreach ($pass as $invoice_id) {
?>
		<li>I#<?=$invoice_id?></li>
<?php
}
?>
	</ul>
</div>
<?php
}

if ("" != $posted["f"] && count($fail = json_decode(base64_url_decode($posted["f"])))) {
?>
<div style="text-align:left;color:#700;width:95%;">
	Failed to Update:
	<ul>
<?php
foreach ($fail as $row) {
?>
		<li>I#<?=$row[0]?>: Tax Invoice #<?=$row[1]?>, Tax Date <?=$row[2]?></li>
<?php
}
?>
	</ul>
</div>
<?php
}

if (0 != $posted["tax_month"]) {
    $tax_month      = $posted["tax_month"];
    $tax_prev_month = mktime(0, 0, 0, date("n", $tax_month) - 1, 1, intval(date("Y", $tax_month)));
    $tax_next_month = mktime(0, 0, 0, date("n", $tax_month) + 1, 1, intval(date("Y", $tax_month)));

    $date_link = $posted;
    unset($date_link["tax_month"]);
    $date_link = "?" . ltrim(http_build_query($date_link) . "&tax_month=", "&");

    echo formatHTMLTitle("Invoice HK Tax Report", '<a href="' . hecho($date_link . $tax_prev_month, 1) . '">View ' . date("F Y", $tax_prev_month) . '</a> | <a href="' . hecho($date_link . $tax_next_month, 1) . '">View ' . date("F Y", $tax_next_month) . '</a>');
?>
<div style="text-align:left;">
	Report Period: <?=date("F Y", $posted["tax_month"])?><br />
	Total Sales: $<?=invoiceSumValue($invoice_list, "invoice_offerus")?> USD<br />
	Total Invoices: <?=count($invoice_list)?>
</div>
<br />
<?php
}

$page_url = $posted;
unset($page_url["page_id"]);
$page_url = "invoice_tax_report.php?" . ltrim(http_build_query($page_url) . "&page_id=", "&");

$items_per_page = count($invoice_list); //20;
$count_total    = count($invoice_list);
$invoice_list   = array_chunk($invoice_list, $items_per_page, true);
$invoice_list   = $invoice_list[$page_id];
$count_display  = count($invoice_list);
$page_links     = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

if (count($invoice_list)) {
?>
<form accept-charset="utf-8" action="index.php?action=update_tax_vals" method="post">
<table class="table_grid spacetable" style="margin:0;width:100%;">
	<tr>
		<th>HK Tax<br />Invoice ID</th>
		<th>HK Tax<br />Date</th>
		<th>Our Inv<br />ID</th>
		<th>Open<br />Date</th>
		<th>Customer<br />Name</th>
		<th>Invoice<br />Total (USD)</th>
		<th>Cost<br />Price</th>
		<th>Invoice<br />Profit</th>
		<th>Supplier<br />Name</th>
		<th>Payment<br />Method</th>
		<th>Status</th>
		<th>Edit</th>
	</tr>
<?php
$supplier_list = invoiceGetOptionList("supplier_id");

    $edit_url           = $get_vars;
    $edit_url['action'] = 'edit';
    unset($edit_url['invoice_id']);
    $tmp      = $edit_url;
    $edit_url = 'index.php?' . http_build_query($edit_url);
    $edit_url .= '&invoice_id=';
    $aspe_url = 'http://www.arcadespareparts.com/webadmin/invoice/index.php?' . http_build_query($tmp);
    $aspe_url .= '&invoice_id=';

    $external_url                = $posted;
    $external_url["action"]      = "tax_print";
    $internal_url["report_type"] = "e";
    unset($external_url["page_id"]);
    $external_url = "index.php?" . http_build_query($external_url);

    $internal_url                = $posted;
    $internal_url["action"]      = "tax_print";
    $internal_url["report_type"] = "i";
    unset($internal_url["page_id"]);
    $internal_url = "index.php?" . http_build_query($internal_url);

    $tax_view_url           = $get_vars;
    $tax_view_url['action'] = 'tax_view';
    unset($tax_view_url['invoice_id']);
    $tmp          = $tax_view_url;
    $tax_view_url = 'index.php?' . http_build_query($tax_view_url);
    $tax_view_url .= '&invoice_id=';

    $view_url           = $get_vars;
    $view_url['action'] = 'view';
    unset($view_url['invoice_id']);
    $tmp      = $view_url;
    $view_url = 'index.php?' . http_build_query($view_url);
    $view_url .= '&invoice_id=';

    $member_url           = $get_vars;
    $member_url['action'] = 'edit';
    unset($member_url['member_id']);
    $member_url = '/webadmin/new_members/?' . http_build_query($member_url) . '&member_id=';

    $supplier_url           = $get_vars;
    $supplier_url['action'] = 'list';
    unset($supplier_url['supplier_id']);
    $supplier_url = '/webadmin/invoice/?' . http_build_query($supplier_url) . '&supplier_id=';

    $unlock_url           = $get_vars;
    $unlock_url['action'] = 'unlock';
    unset($unlock_url['invoice_id']);
    $tmp        = $unlock_url;
    $unlock_url = 'index.php?' . http_build_query($unlock_url);
    $unlock_url .= '&invoice_id=';

    $alt = false;
    foreach ($invoice_list as $invoice_id) {
        $invoice          = invoiceFromDatabase($invoice_id);
        $invoice_lock     = invoiceGetLock($invoice_id);
        $invoice_jobs     = invoiceJobsFromDatabase($invoice_id);
        $invoice_products = invoiceProductsFromDatabase($invoice_id);
        $invoice_charges  = invoiceChargesFromDatabase($invoice_id);
        $invoice_payments = invoicePaymentsFromDatabase($invoice_id);
        if (0 != $invoice['member_id']) {
            $member_requests = memberRequestsFromDatabase($invoice['member_id']);
        } else {
            $member_requests = array(array('comments' => 'Invoice is not linked to a member'));
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

        $lock_tooltip = '';
        if (false !== $invoice_lock) {
            $class = ' class="locked"';
            if ($invoice_lock['staff_id'] != $_GLOBALS["staff"]["id"]) {
                $staff_list   = invoiceGetOptionList('staff_id');
                $lock_tooltip = formatHTMLSafe('This invoice/enquiry is currently locked to ' . $staff_list[$invoice_lock['staff_id']]);
            }
        }
        if ($invoice_lock['staff_id'] == $_GLOBALS["staff"]["id"]) {
            $class = ' class="mylock"';
        }

        $payment_due = $invoice['invoice_offer'];
        foreach ($invoice_payments as $payment) {
            $payment_due -= convertCurrency($payment['payment_amount'], $payment['payment_amount'], $invoice['invoice_currency']);
        }

        $payments_located = '<img src="images/status/' . (('y' == $invoice['payments_located']) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['payments_located']) ? 'yes' : 'no') . '" />';
        if (round($payment_due) > 0 && $payment_due < $invoice['invoice_offer']) {
            $payments_located = '<img src="images/status/deposit.gif" alt="Deposit" />';
        }

        if ($payment_due <= 0 && $invoice['invoice_offer'] > 0) {
            $payments_located = '<img src="images/status/yes.gif" alt="Yes" />';
        }

        $products_located = '<img src="images/status/' . (('y' == $invoice['products_located'] || $invoice['products_found'] == $invoice['products_needed']) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['products_located'] || $invoice['products_found'] == $invoice['products_needed']) ? 'Yes' : 'No') . '" />';
        $ship_located     = '<img src="images/status/' . (('y' == $invoice['ship_located']) ? 'yes' : 'no') . '.gif" alt="' . (('y' == $invoice['ship_located']) ? 'Yes' : 'No') . '" />';
        if ('y' != $invoice['products_located'] && $invoice['products_found'] < $invoice['products_needed']) {
            $products_located = '<span style="color:red;">' . $invoice['products_found'] . '/' . $invoice['products_needed'] . '</span>';
        }

        $invoice_type = array_search($invoice['invoice_status'], array('enquiry', 'closed', 'proforma', 'sales', 'processed', 'cancelled', 'return', 'completed'));
        $status_image = $invoice['invoice_status'];
        if ($invoice_type < 2) {
            $status_image = 'q_working';
            if ('feedback' == $invoice['invoice_action']) {
                $status_image = 'q_feedback';
            }

            if (1 == $invoice_type) {
                $status_image = 'q_cancelled';
            }

            if ('nointerest' == $invoice['invoice_action']) {
                $status_image = 'q_nointerest';
            }

            if ('nohelp' == $invoice['invoice_action']) {
                $status_image = 'q_nohelp';
            }

        } else {
            $status_image = $invoice['invoice_action'];
            if ("return" == $invoice["invoice_status"]) {
                $status_image = "returned";
            }

            if ("completed" == $invoice["invoice_status"]) {
                $status_image = "processed";
            }

        }
        $products_tooltip = array();
        $notfound         = array();
        $found            = array();
        $symbol           = getCurrencySymbol($invoice_currency);
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

        $display_name    = ('' != $invoice['bill_company']) ? $invoice['bill_company'] : $invoice['bill_name'];
        $payment_tooltip = '<table style="width:300px;text-align:center">' . '<tr><td>Amount :</td><td>$' . $invoice['invoice_offer'] . ' ' . $invoice['invoice_currency'] . '</td></tr>' . '<tr><td>Payment Method :</td><td>' . $invoice['paymenttype_id'] . '</td></tr>';
        if ($payment_due > 0) {
            $payment_tooltip .= '<tr><td>Payment Outstanding :</td><td>$' . sprintf('%.2f', $payment_due) . ' ' . $invoice['invoice_currency'] . '</td></tr>';
        }

        $payment_tooltip .= '</table>';
        $office_tooltip     = 'Office Invoice<br/>--------------------------<br/>' . $products_tooltip;
        $ship_tooltip       = 'Shipping Invoice';
        $dummy_tooltip      = 'Dummy Invoice';
        $customer_tooltip   = array($invoice['bill_address_full']);
        $customer_highlight = false;
        if (count($member_requests)) {
            $customer_tooltip[] = '';
            $customer_tooltip[] = "Member Warnings :";
            foreach ($member_requests as $request) {
                if ('' != $request['comments']) {
                    $customer_tooltip[] = '-' . $request['comments'];
                }
            }

            $customer_highlight = true;
        }
        $customer_tooltip = nl2br(implode("\n", $customer_tooltip));
        $lh               = ($invoice['invoice_profit'] < 0 || $invoice['invoice_profitau'] < 0);
        $imgsrc           = '/webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_rect' . ((2 == $invoice['site_id'] && 'dom' == $invoice['he_id']) ? '_dom' : '') . '.png';

        $product_suppliers = array();
        foreach ($invoice_products as $v) {
            if (0 != $v["supplier_id"]) {
                $product_suppliers[$v["supplier_id"]] = $v["supplier_name"];
            } elseif (trim($v["supplier_name"]) != "") {
                $product_suppliers[$v["supplier_name"]] = $v["supplier_name"];
            }
        }

        asort($product_suppliers);

        $invoice_key  = "invoices[" . $invoice_id . "]";
        $predict_time = $invoice["tax_time"];
        if (0 == $predict_time) {
            $predict_time = $invoice["payments_locate_time"];
        }

        if (0 == $predict_time) {
            $predict_time = $invoice["proforma_time"];
        }

        if (0 == $predict_time) {
            $predict_time = $invoice["open_time"];
        }

?>
	<tr<?=$class?>>
		<td>
			<input type="hidden" name="<?php hecho($invoice_key . "[invoice_id]")?>" value="<?php hecho($invoice["invoice_id"])?>" />
			<input type="text" name="<?php hecho($invoice_key . "[tax_id]")?>" size="6" maxlength="10" value="<?php hecho((0 != $invoice["tax_id"]) ? sprintf("%06d", $invoice["tax_id"]) : "")?>" />
		</td>
		<td style="white-space:nowrap;">
			<input type="text" size="10" maxlength="10" onfocus="<?php hecho('if (this.value == "") {this.value=' . jecho(date("Y-m-d", $predict_time), 1) . ';gfPop.fPopCalendar(this);this.value="";} else gfPop.fPopCalendar(this)')?>" name="<?php hecho($invoice_key . "[tax_date]")?>" value="<?php hecho((0 != $invoice["tax_time"]) ? date("Y-m-d", $invoice["tax_time"]) : "")?>"><a href="#" onclick="<?php hecho('$(' . jecho("[name='" . $invoice_key . "[tax_date]']", 1) . ').val(' . jecho(date("Y-m-d", $invoice["open_time"]), 1) . ');return false')?>"><img src="/webadmin/images/execute.gif" alt="&larr;" style="vertical-align:bottom;" /></a>
		</td>
		<td style="<?=$lh ? 'background-color:#CCFF66' : ''?>" onmouseover="<?php hecho("tt_in(" . jecho($office_tooltip, 1) . ");")?>" onmouseout="tt_out();">
			<a href="<?php hecho($view_url . $invoice['invoice_id'])?>" target="_blank"><?php hecho($invoice["invoice_number"])?></a>
		</td>
		<td style="text-align:center;"><?=date('d/m/Y', $invoice['open_time'])?></td>
		<td style="text-align:center;" onmouseover="<?php hecho('tt_in(' . jecho($customer_tooltip, 1) . ')')?>" onmouseout="tt_out()">
<?php
if ($customer_highlight) {
?>
			<div style="border:2px solid #FF1493;height:57px;"><br />
<?php
}
        if (0 != $invoice['member_id']) {
?>
				<a href="<?php hecho($member_url . $invoice['member_id'])?>" target="_blank">
<?php
}
?>
					<?php hecho($display_name)?>
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
		<td style="text-align:center;<?=$ph ? 'background-color:#c6f;' : ''?>">$<?=$invoice['invoice_offerus']?></td>
		<td style="text-align:center;<?=$ph ? 'background-color:#c6f;' : ''?>">$<?=$invoice['invoice_costus']?></td>
		<td style="text-align:center;<?=$ph ? 'background-color:#c6f;' : ''?>">$<?=$invoice['invoice_profitus']?></td>
		<td style="white-space:nowrap;">
<?php
foreach ($product_suppliers as $k => $v) {
            if ($k > 0) {
?>
			<a href="<?php hecho($supplier_url . $k)?>" target="_blank"<?=($supplier_list[$k] != $v) ? ' style="background:#ff0;"' : ''?>><?php hecho(("" != $v) ? $v : $supplier_list[$k])?></a><br />
<?php
} else {
                echo hecho($v, 1) . "<br />\n";
            }
        }

?>
		</td>
		<td style="text-align:center;"><?=ucwords(strtolower(preg_replace("/[^A-Za-z]/", " ", $invoice["paymenttype_id"])))?></td>
<?php
if ("true" == $posted["print"]) {
?>
		<td style="text-align:center;" onmouseover="<?php hecho("tt_in(" . jecho($invoice['handler_note'], 1) . ")")?>" onmouseout="tt_out()"><?php hecho(ucwords($status_image))?></td>
<?php
} else {
?>
		<td style="text-align:center;" onmouseover="<?php hecho("tt_in(" . jecho($invoice['handler_note'], 1) . ")")?>" onmouseout="tt_out()"><img src="images/status/<?=$status_image?>.gif" alt="<?php hecho($status_image)?>" /></td>
<?php
}
?>
		<td style="text-align:center;" onmouseover="<?php hecho("tt_in(" . jecho($lock_tooltip, 1) . ");")?>" onmouseout="tt_out();">
<?php
if (false === $invoice_lock || $invoice_lock['staff_id'] == $_GLOBALS["staff"]["id"]) {
            $href = $edit_url . $invoice['invoice_id'];
            if (in_array($invoice['site_id'], array(20, 26)) && !in_array($posted['site_id'], array(20, 26))) {
                $href = $aspe_url . $invoice['invoice_id'];
            }

?>
			<a href="<?php hecho($href)?>" target="_blank">Edit</a>
<?php
} else {
?>
			<a href="<?php hecho($view_url . $invoice['invoice_id'])?>" target="_blank">View</a>
<?php
}
        if (false !== $invoice_lock && $invoice_lock['staff_id'] == $_GLOBALS["staff"]["id"]) {
?>
			<a href="<?php hecho($unlock_url . $invoice['invoice_id'])?>">Unlock</a>
<?php
}
?>
			<a href="<?php hecho($tax_view_url . $invoice["invoice_id"])?>" target="_blank">Print</a>
		</td>
	</tr>
<?php
}
?>
</table>

<br />
<br />

<script type="text/javascript">
//<![CDATA[
function validateForm()
{
	var id_list = <?php jecho($invoice_list)?>;
	for (var i = 0;i < id_list.length;i++)
	{
		var $tax_id = $("input[name='invoices[" + id_list[i] + "][tax_id]']");
		var $tax_date = $("input[name='invoices[" + id_list[i] + "][tax_date]']");
		var valid_id = /^(0*[1-9][0-9]*)?$/.test($tax_id.val());
		var valid_date = /^(2[0-9]{3}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]))?$/.test($tax_date.val());
		if ($tax_id.val().length && $tax_date.val().length == 0 || !valid_date)
		{
			$tax_date.focus();
			alert("Please enter a valid date");
			return false;
		}
		if ($tax_id.val().length == 0 && $tax_date.val().length || !valid_id)
		{
			$tax_id.focus();
			alert("Please enter a valid id");
			return false;
		}
	}
	return true;
}
//]]>
</script>

<div style="text-align:center;"><?=$page_links?></div>
<br />
<br />

<div style="text-align:center;">
	<input type="button" onclick="if (validateForm()) this.form.submit()" value="Update" />
	<input type="button" onclick="<?php hecho("window.open(" . jecho($external_url, 1) . ")")?>" value="Print HK Tax Report" />
	<input type="button" onclick="<?php hecho("window.open(" . jecho($internal_url, 1) . ")")?>" value="Print Internal Report" />
</div>
<br />
<br />

</form>

<?php
} else {
}
?>

<div style="text-align:center;">
	<span style="font-style:italic;">Notes:</span><br />
	Rows marked in <span style="background-color:#90EEA0;">green</span> have new messages<br />
	Rows marked in <span style="background-color:#EE90A0;">red</span> are locked by another user<br />
	Rows marked in <span style="background-color:#90A0EE;">blue</span> are locked by you<br />
	Cells marked in <span style="background-color:yellow;">yellow</span> have been idle for <?=$_GLOBALS['site_options']['WARNING_IDLE_DAYS']?> or more days<br />
	Cells marked in <span style="background-color:#CC66FF;">purple</span> don't have a cost price.<br />
	Cells marked in <span style="border:2px solid orange;">orange</span> have special freight requests from the customer<br />
	Cells marked in <span style="border:2px solid #FF1493;">deep pink</span> have member warnings.<br />
	Cells marked in <span style="background-color:#CCFF66;">light green</span> indicate a loss for the invoice.<br />
</div>
<br />
<br />

<?php

include_once __DIR__ . "/templates/bare_footer.tpl.php";
