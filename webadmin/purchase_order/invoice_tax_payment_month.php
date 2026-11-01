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
$criteria = array("invoice_status" => "processed" /*"invoice_action" => "processed",*/);
foreach (invoiceGetOptionList("all_fields") as $v => $v2) {
    if (array_key_exists($v, $posted)) {
        $criteria[$v] = (is_array($posted[$v])) ? $posted[$v] : preg_split("/[^a-zA-Z0-9]+/", $posted[$v]);
    }
}

if (11 != $_GLOBALS["site_settings"]["site_id"]) {
    $criteria["site_id"] = $_GLOBALS["site_settings"]["site_id"];
}

$invoice_list = invoiceSearch($criteria);

$date_list = invoiceChunk($invoice_list, "tax_month");
krsort($date_list);

$page_url = $posted;
unset($page_url["page_id"]);
$page_url = "?" . ltrim(http_build_query($page_url) . "&page_id=", "&");

$items_per_page = 25;
$count_total    = count($date_list);
$date_list      = array_chunk($date_list, $items_per_page, true);
$date_list      = $date_list[$page_id];
$count_display  = count($date_list);
$page_links     = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

$pay_methods = array(
    "MASTERCARD" => "Mastercard",
    "VISA"       => "Visa Card",
    "AMEX"       => "Amex",
    "HKD_BANK"   => "HK Dollar",
    "USD_BANK"   => "US Dollar",
    "JPY_BANK"   => "JP Yen",
    "PAYPAL"     => "PayPal",
    "CASH"       => "Cash",
    "TRADE"      => "Trade",
);

$yearly_link = $posted;
unset($yearly_link["tax_month"]);
unset($yearly_link["tax_year"]);
$yearly_link["action"] = "tax_payment_year";
$yearly_link           = "index.php?" . http_build_query($yearly_link);

echo formatHTMLTitle("Payment Method - Monthly", '<a href="' . hecho($yearly_link, 1) . '">View Yearly Report</a>');

if (count($date_list)) {
?>
<table class="table_grid spacetable" style="margin:0;width:100%;">
	<tr>
		<th>Date Range</th>
		<th>Total<br />Invoices</th>
		<th>Total Sales<br />(USD)</th>
<?php
foreach ($pay_methods as $k2 => $v2) {
?>
		<th><?php hecho($v2)?><br />Payment</th>
<?php
}
?>
		<th>Other</th>
		<th>Total Funds</th>
		<th>Shortages</th>
		<th>Open<br />Invoices</th>
	</tr>
<?php
$alt = false;
    foreach ($date_list as $k => $v) {
        $total_link              = $criteria;
        $total_link["tax_month"] = $k;
        $total_link              = "invoice_tax_report.php?" . http_build_query($total_link);

        /**
         * Find open invoices
         */
        $open_count                   = $criteria;
        $open_count["tax_month"]      = $k;
        $open_count["invoice_status"] = array("proforma", "sales");
        unset($open_count["invoice_action"]);
        $open_count = count(invoiceSearch($open_count));

        $open_link                   = $criteria;
        $open_link["tax_month"]      = $k;
        $open_link["invoice_status"] = array("proforma", "sales");
        unset($open_link["invoice_action"]);
        $open_link = "invoice_list.php?" . http_build_query($open_link);

        $other_method      = array();
        $by_payment_method = array();
        $temp              = invoiceChunk($v, "paymenttype_id");
        foreach ($pay_methods as $k2 => $v2) {
            if (!array_key_exists($k2, $temp)) {
                $temp[$k2] = array();
            }

            $by_payment_method[$k2] = $temp[$k2];
            unset($temp[$k2]);
        }
        $by_payment_method["OTHER"] = array();
        foreach ($temp as $k2 => $v2) {
            if (is_array($v2)) {
                $other_method[$k2] = $k2;
            } else {
                $other_method["OTHER"] = "OTHER";
            }
        }

        foreach ($temp as $v2) {
            if (is_array($v2)) {
                $by_payment_method["OTHER"] = array_merge($by_payment_method["OTHER"], $v2);
            } else {
                $by_payment_method["OTHER"][] = $v2;
            }
        }

        unset($temp);
        foreach ($by_payment_method as $k2 => $v2) {
            $by_payment_method[$k2] = invoiceSumValue($v2, "invoice_offerus");
        }

        $paymethod_url = $criteria;
        unset($paymethod_url["paymenttype_id"]);
        $paymethod_url["tax_month"] = $k;
        $paymethod_url              = "invoice_list.php?" . ltrim(http_build_query($paymethod_url) . "&paymenttype_id=", "&");

?>
	<tr<?=($alt = !$alt) ? "" : ' class="alt"'?>>
		<td style="text-align:center;white-space:nowrap;"><a href="<?php hecho($total_link)?>" target="_blank"><?=date("F Y", $k)?></a></td>
		<td style="text-align:center;"><a href="<?php hecho($total_link)?>" target="_blank"><?=count($v)?></a></td>
		<td style="text-align:right;">$<?=invoiceSumValue($v, "invoice_offerus")?></td>

<?php
foreach ($pay_methods as $k2 => $v2) {
?>
		<td style="text-align:right;"><a href="<?php hecho($paymethod_url . $k2)?>" target="_blank">$<?=$by_payment_method[$k2]?></a></td>
<?php
}
?>
		<td style="text-align:right;" title="<?=implode(", ", $other_method)?>"><a href="<?php hecho($paymethod_url . implode("|", $other_method) . "|")?>" target="_blank">$<?=$by_payment_method["OTHER"]?></a></td>
		<td style="text-align:right;background:#ff0;">$<?=""?></td>
		<td style="text-align:right;background:#ff0;">$<?=""?></td>
		<td style="text-align:center;"><a href="<?php hecho($open_link)?>" target="_blank"><?=$open_count?></a></td>
	</tr>
<?php
}
?>
</table>

<br />
<br />
<?php
} else {
}
?>

<div class="print_hide">
	<div style="text-align:center;"><?=$page_links?></div>
	<br />
	<br />

	<div style="text-align:center;"><input type="button" value="Print" onclick="window.print();" /></div>
	<!--
<?php
$print_link          = $posted;
$print_link["print"] = "true";
$print_link          = "?" . http_build_query($print_link);
?>
	<form accept-charset="utf-8" action="<?php hecho($print_link)?>" method="post" target="_blank">
		<div style="text-align:center;">
			<input type="submit" value="Print" />
		</div>
	</form>
	-->
	<br />
	<br />
</div>
<?php
include_once __DIR__ . "/templates/bare_footer.tpl.php";
