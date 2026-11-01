<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

$page = $posted["page"];
if ("" == $page) {
    $page = 0;
}

$site_id = intval($site_id);
$print   = $posted["print"];

$list = profitSalesYears();

$items_per_page = 12;
$count_total    = count($list);
$list           = array_chunk($list, 12, true);
$count_pages    = count($list);
$page_id        = max(0, min($count_pages - 1, $page));
$list           = $list[$page];
$count_display  = count($list);

$page_url = $get_vars;
unset($page_url["page"]);
unset($page_url["print"]);
$page_url = "?" . http_build_query($page_url);

$page_links = ("true" != $print) ? formatHTMLPageLinks($page_url . "&page=", $page, $count_display, $count_total, $items_per_page) : "";

$list2  = array_chunk($list, 1);
$temp   = $list2[0];
$year   = idate("Y", $temp[0]["start"]);
$lyear  = $year - 1;
$lyear2 = $year - 2;

$links = ("true" != $print) ? true : false;
if ("true" != $print) {
    include_once __DIR__ . "/templates/profits_header.tpl.php";
?>

<?=formatHTMLTitle("Sales Breakup - Yearly")?>

<?php
    echo '<table class="table_grid" align="center">';
} else {
?>
<html>
	<head>
		<title>Sales Breakup - Yearly</title>
	</head>
	<script type="text/javascript">
//<![CDATA[
function printpage()
{
	if (window.print) window.print();
	else alert("To print this page, select Print Frame from the file menu.");
}
function doPrint()
{
	if (confirm("Click OK to print this document")) printpage();
}
//]]>
	</script>
	<body onload="doPrint()">
	<div style="font-size:20px;text-align:center"><b>Sales Breakup - Yearly</b></div><br />
	<table style="border-collapse:collapse;border:1px solid #CCCCCC" border="1" align="center">
<?php
}
?>
	<tr>
		<th>Date Range</th>
		<th>Qty Invoices</th>
		<th>Australian Sales (USD)</th>
		<th>Overseas Sales (USD)</th>
		<th>Total Sales (USD)</th>
		<th>Cost of Goods (USD)</th>
		<th>Gross Profit (USD)</th>
		<th>GP Ratio Australian</th>
		<th>GP Ratio Overseas</th>
		<th>GP Ratio</th>
		<th colspan="2"><?=$lyear?> Profit</th>
		<th colspan="2"><?=$lyear2?> Profit</th>
		<th>Open Invoices</th>
	</tr>
<?php
$count = 0;
foreach ($list as $key => $array) {
    $breakup    = profitSalesBreakup($array["start"], $array["end"], $site_id, true, "yearly");
    $last_year  = ('NA' != $breakup['last_year']) ? '$' . number_format(convertCurrency($breakup['last_year'],'AUD','USD'), 2, '.', ',') : 'NA';
    $last_year2 = ('NA' != $breakup['last_year2']) ? '$' . number_format(convertCurrency($breakup['last_year2'],'AUD','USD'), 2, '.', ',') : 'NA';
    $arrow1     = ("NA" != $last_year && "NA" != $breakup["sales_profit"]) ? ($breakup["sales_profit"] > $breakup["last_year"]) ? "up_arrow.gif" : "down_arrow.gif" : "";
    $arrow2     = ("NA" != $last_year2 && "NA" != $breakup["sales_profit"]) ? ($breakup["sales_profit"] > $breakup["last_year2"]) ? "up_arrow.gif" : "down_arrow.gif" : "";

    $invoice_url = array();
    if (0 != $site_id) {
        $invoice_url["site_id"] = $site_id;
    }

    $invoice_url["invoice_status"] = "processed";
    $invoice_url                   = "/webadmin/invoice/invoice_list.php?" . http_build_query($invoice_url) . "&open_year=";

    $open_url = array();
    if (0 != $site_id) {
        $open_url["site_id"] = $site_id;
    }

    $open_url["invoice_status"] = "open|proforma|sales";
    $open_url                   = "/webadmin/invoice/invoice_list.php?" . http_build_query($open_url) . "&open_year=";
?>
	<tr<?=($count % 2 == 1) ? ' class="alt"' : ""?> style="text-align:center">
		<td>
<?php
if ($links) {
?>
			<a target="_blank" href=".?site_id=<?=$site_id?>&action=sales&type=monthly&year=<?=$key?>">
<?php
}
?>
				<?=$key?>
<?php
if ($links) {
?>
			</a>
<?php
}
?>
		</td>
		<td><a href="<?=htmlspecialchars($invoice_url . mktime(0, 0, 0, 1, 1, $key))?>" target="_blank"><?=$breakup["total"]?></a></td>
		<td>$<?=is_numeric($breakup["sales_total_dom"]) ? number_format(convertCurrency($breakup["sales_total_dom"],'AUD','USD'), 2, ".", ",") : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_total_int"]) ? number_format(convertCurrency($breakup["sales_total_int"],'AUD','USD'), 2, ".", ",") : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_total"]) ? number_format(convertCurrency($breakup['sales_total'],'AUD','USD'), 2, '.', ',') : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_cost"]) ? number_format(convertCurrency($breakup["sales_cost"],'AUD','USD'), 2, ".", ",") : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_profit"]) ? number_format(convertCurrency($breakup['sales_profit'],'AUD','USD'), 2, '.', ',') : "NA"?></td>
		<td><?=$breakup["gp_ratio_dom"]?>%</td>
		<td><?=$breakup["gp_ratio_int"]?>%</td>
		<td><?=$breakup["gp_ratio"]?>%</td>
		<td><?=("" != $arrow1) ? '<img src="/webadmin/images/' . $arrow1 . '">' : "&nbsp;"?></td><td><?=$last_year?></td>
		<td><?=("" != $arrow2) ? '<img src="/webadmin/images/' . $arrow2 . '">' : "&nbsp;"?></td><td><?=$last_year2?></td>
		<td><a href="<?=htmlspecialchars($open_url . mktime(0, 0, 0, 1, 1, $key))?>" target="_blank"><?=$breakup["pending"]?></a></td>
	</tr>
<?php
$count++;
}
?>
</table>
<br />
<div class="text-center"><?=$page_links?></div>
<br />
<?php
if ("true" != $print) {
?>
<a href="<?=$page_url?>&print=true" target="_blank">Print</a>
<?php
}
?>

<?php
include_once __DIR__ . "/templates/profits_footer.tpl.php";
