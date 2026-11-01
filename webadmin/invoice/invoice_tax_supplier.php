<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

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

if (!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
    $criteria["site_id"] = $_GLOBALS["site_settings"]["site_id"];
}

$invoice_list = invoiceSearch($criteria);

$invoice_by_supplier = array();
if (count($invoice_list)) {
    $query = $DB->query("SELECT DISTINCT `supplier_id`, `invoice_id` FROM `highwaygroup`.`invoice_products` WHERE `generic_code` <> 'GFR' AND `invoice_id` IN(" . implode(", ", $invoice_list) . ")");
    while ($row = $DB->fetch_assoc($query)) {
        $invoice_by_supplier[$row["supplier_id"]][] = $row["invoice_id"];
    }

    $DB->free_result($query);
}

$page_url = $posted;
unset($page_url["page_id"]);
$page_url = "invoice_tax_supplier.php?" . ltrim(http_build_query($page_url) . "&page_id=", "&");

$items_per_page      = count($invoice_by_supplier); //20;
$count_total         = count($invoice_by_supplier);
$invoice_by_supplier = array_chunk($invoice_by_supplier, $items_per_page, true);
$invoice_by_supplier = $invoice_by_supplier[$page_id];
$count_display       = count($invoice_by_supplier);
$page_links          = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

echo formatHTMLTitle("Supplier History");

if (count($invoice_by_supplier)) {
    $invoice_by_tax_year = invoiceGroupCount($invoice_list, "tax_year");
    krsort($invoice_by_tax_year);
    $supplier_list = invoiceGetOptionList("supplier_id");
    asort($supplier_list);
?>
<table class="table_grid spacetable">
	<tr>
		<th>Supplier Name</th>
<?php
    foreach ($invoice_by_tax_year as $k => $v) {
?>
		<th><?=date("Y", $k)?></th>
<?php
    }
?>
	</tr>
<?php
    $first_letter = '';
    $alt          = false;
    foreach ($supplier_list as $supplier_id => $supplier_name) {
        if (array_key_exists($supplier_id, $invoice_by_supplier)) {
            $invoice_link = $posted;
            unset($invoice_link["supplier_id"]);
            unset($invoice_link["tax_year"]);
            $invoice_link["action"]         = "list";
            $invoice_link["invoice_status"] = "processed";
            $invoice_link["invoice_action"] = "processed";
            $invoice_link                   = ".?" . ltrim(http_build_query($invoice_link) . "&supplier_id=" . $supplier_id . "&tax_year=", "&");

            $invoice_list  = $invoice_by_supplier[$supplier_id];
            $invoice_chunk = invoiceChunk($invoice_list, "tax_year");

            if (false && substr($supplier_name, 0, 1) != $first_letter) {
                $first_letter = substr($supplier_name, 0, 1);
?>
	<tr>
		<td style="padding:8px 0;text-align:center;"><b><?=htmlspecialchars(strtoupper($first_letter))?></b></td>
<?php
                foreach ($invoice_by_tax_year as $k => $v) {
?>
		<td></td>
<?php
                }
?>
	</tr>
<?php
                $alt = false;
            }
?>

	<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
		<td style="width:100%;"><?=htmlspecialchars($supplier_name)?></td>
<?php
            foreach ($invoice_by_tax_year as $k => $v) {
                if (array_key_exists($k, $invoice_chunk)) {
                    $sum   = "0.0000";
                    $flag  = false;
                    $query = $DB->query("SELECT `product_qty`, `product_costus` FROM `highwaygroup`.`invoice_products` WHERE `generic_code` <> 'GFR' AND `supplier_id` = " . $supplier_id . " AND `invoice_id` IN(" . implode(", ", $invoice_chunk[$k]) . ")");
                    while ($row = $DB->fetch_assoc($query)) {
                        $sum = bcadd($sum, bcmul($row["product_costus"], $row["product_qty"], 4), 4);
                        if (bccomp($row["product_costus"], "0.0000", 4) <= 0) {
                            $flag = true;
                        }
                    }
                    $DB->free_result($query);
?>
		<td style="text-align:right;<?=($flag) ? 'background:#faa;' : ''?>"><a href="<?=htmlspecialchars($invoice_link . $k)?>" target="_blank" title="<?=count($invoice_chunk[$k])?> Invoices">$<?=bcadd("0.00", $sum, 2)?></a></td>
<?php
                } else {
?>
		<td style="text-align:center;color:#ddd;">N/A</td>
<?php
                }
            }

?>
	</tr>
<?php
        }
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

include_once __DIR__ . '/footer.inc.php';
