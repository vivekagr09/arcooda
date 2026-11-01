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

$date_list = invoiceChunk($invoice_list, "tax_year");
krsort($date_list);

$page_url = $posted;
unset($page_url["page_id"]);
$page_url = "?" . ltrim(http_build_query($page_url) . "&page_id=", "&");

$items_per_page = 20;
$count_total    = count($date_list);
$date_list      = array_chunk($date_list, 20, true);
$date_list      = $date_list[$page_id];
$count_display  = count($date_list);
$page_links     = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

echo formatHTMLTitle("Summary HK Tax Report - By Year");

if (count($date_list)) {
?>
<table class="table_grid spacetable" style="margin:0;width:100%;">
	<tr>
		<th>Date Range</th>
		<th>Total<br />Invoices</th>
		<th>Total Sales<br />(USD)</th>
		<th>Cost of<br />Goods (USD)</th>
		<th>Gross<br />Profit (USD)</th>
		<th>GP Ratio<br />%</th>
		<th>Last Year<br />Profits</th>
		<th>Open<br />Invoices</th>
		<th>Unallocated<br />Invoices</th>
	</tr>
<?php
$alt = false;
    foreach ($date_list as $k => $v) {
        $total_link             = $criteria;
        $total_link["tax_year"] = $k;
        $total_link             = "invoice_tax_summary_month.php?" . http_build_query($total_link);

        $total_sales = invoiceSumValue($v, "invoice_offerus");
        $total_cost  = invoiceSumValue($v, "invoice_costus");

        $cost_problems = array();
        if (count($v)) {
            $query = $DB->query("SELECT DISTINCT `invoice_id` FROM `invoice_products` WHERE `invoice_id` IN(" . implode(", ", $v) . ") AND `product_cost` <= '0.0000' AND `generic` = 'n'");
            while ($row = $DB->fetch_assoc($query)) {
                $cost_problems[] = $row["invoice_id"];
            }

            $DB->free_result($query);
        }

        $total_profit = invoiceSumValue($v, "invoice_profitus");
        $gp_ratio     = "-";
        if (bccomp($total_sales, "0.00", 2) != 0) {
            $gp_ratio = bcmul(bcdiv($total_profit, $total_sales, 2), "100", 0);
        }

        $last_year             = $criteria;
        $last_year["tax_year"] = mktime(0, 0, 0, date("n", $k), date("j", $k), date("Y", $k) - 1);
        $last_year             = invoiceSumValue(invoiceSearch($last_year), "invoice_profitus");

        /**
         * Find open invoices
         */
        $open_count                   = $criteria;
        $open_count["tax_year"]       = $k;
        $open_count["invoice_status"] = array("proforma", "sales");
        unset($open_count["invoice_action"]);
        $open_count = count(invoiceSearch($open_count));

        $open_link                   = $criteria;
        $open_link["tax_year"]       = $k;
        $open_link["invoice_status"] = array("proforma", "sales");
        unset($open_link["invoice_action"]);
        $open_link = "invoice_list.php?" . http_build_query($open_link);

        /**
         * Find unallocated invoices
         */
        $unalloc_count             = $criteria;
        $unalloc_count["tax_year"] = $k;
        $unalloc_count["tax_id"]   = 0;
        $unalloc_count             = count(invoiceSearch($unalloc_count));

        $unalloc_link             = $criteria;
        $unalloc_link["tax_year"] = $k;
        $unalloc_link["tax_id"]   = 0;
        $unalloc_link             = "invoice_tax_report.php?" . http_build_query($unalloc_link);
?>
	<tr<?=($alt = !$alt) ? "" : ' class="alt"'?>>
		<td style="text-align:center;"><a href="<?php hecho($total_link)?>" target="_blank"><?=date("Y", $k)?></a></td>
		<td style="text-align:center;"><a href="<?php hecho($total_link)?>" target="_blank"><?=count($v)?></a></td>
		<td style="text-align:right;">$<?=$total_sales?></td>
<?php
if (count($cost_problems)) {
?>
		<td style="text-align:right;background:#faa;"><a href="/webadmin/invoice/?invoice_list=<?=implode("|", $cost_problems)?>" target="_blank" title="<?=count($cost_problems)?> Invoice(s) With Non-Positive Cost">$<?=$total_cost?></a></td>
<?php
} else {
?>
		<td style="text-align:right;">$<?=$total_cost?></td>
<?php
}
?>
		<td style="text-align:right;">$<?=$total_profit?></td>
		<td style="text-align:center;"><?=$gp_ratio?>%</td>
		<td style="text-align:right;">$<?=$last_year?></td>
		<td style="text-align:center;"><a href="<?php hecho($open_link)?>" target="_blank"><?=$open_count?></a></td>
		<td style="text-align:center;"><a href="<?php hecho($unalloc_link)?>" target="_blank"><?=$unalloc_count?></a></td>
	</tr>
<?php
}
?>
</table>

<br />
<br />
<?php
}
?>

<div style="text-align:center;"><?=$page_links?></div>
<br />
<br />

<?php

include_once __DIR__ . "/templates/bare_footer.tpl.php";
