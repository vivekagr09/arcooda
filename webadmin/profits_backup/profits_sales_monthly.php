<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

$page = $posted['page'];
if ('' == $page) {
    $page = 0;
}

$site_id = intval($posted['site_id']);
$print   = $posted['print'];

$year       = intval($posted['year']);
$start_time = intval($posted['start_time']);
$end_time   = intval($posted['end_time']);
$view       = $posted['view'];
$list       = ($year) ? profitSalesMonthsForYear($year) : profitSalesMonths();
if (0 != $start_time && 0 != $end_time) {
    $list = profitSalesMonths($start_time, $end_time);
}

$page_url = $get_vars;
unset($page_url['page']);
$page_url   = '?' . http_build_query($page_url);
$page_links = array();
if ('all' != $view && 'true' != $print) {
    $items_per_page = 12;
    $count_total    = count($list);
    $list           = array_chunk($list, 12, true);
    $count_pages    = count($list);
    $page_id        = max(0, min($count_pages - 1, $page));
    $list           = $list[$page];
    $count_display  = count($list);
    $page_links     = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $items_per_page);
}
$list2  = array_chunk($list, 1);
$temp   = $list2[0];
$year   = idate("Y", $temp[0]['start']);
$lyear  = $year - 1;
$lyear2 = $year - 2;
$links  = ('true' != $print) ? true : false;
if ('true' != $print) {
    include_once __DIR__ . '/templates/profits_header.tpl.php';
?>

<?=formatHTMLTitle('Sales Breakup - Monthly')?>

<?php
    echo '<table class="wide" align="center">';
} else {
?>
<html>
	<head>
		<title>Sales Breakup - Monthly</title>
	</head>
	<script type="text/javascript">
//<![CDATA[
function printpage()
{
	if (window.print) window.print();
	else alert('To print this page, select Print Frame from the file menu.');
}
function doPrint()
{
	if (confirm("Click OK to print this document")) printpage();
}
//]]>
	</script>
	<body onload="doPrint()">
	<div style="font-size:20px;text-align:center"><b>Sales Breakup - Monthly</b></div><br />
	<table style="border-collapse:collapse;border:1px solid #CCCCCC" border="1" align="center">
<?php
}
?>
	<tr>
		<th>Date Range</th>
		<th>Qty Invoices</th>
		<th>Australian Sales (AUD)</th>
		<th>Overseas Sales (AUD)</th>
		<th>Total Sales (AUD)</th>
		<th>Cost of Goods (AUD)</th>
		<th>Gross Profit (AUD)</th>
		<th>GP Ratio Australian</th>
		<th>GP Ratio Overseas</th>
		<th>GP Ratio</th>
		<th colspan="2">Last Year Profit</th>
		<th colspan="2">Previous Year Profit</th>
		<th>Open Invoices</th>
	</tr>
<?php
$count = 0;
foreach ($list as $key => $array) {
    $breakup    = profitSalesBreakup($array['start'], $array['end'], $site_id, true, 'monthly');
    $href       = '.?site_id=' . $site_id . '&action=sales&type=weekly&start_time=' . $array['start'] . '&end_time=' . $array['end'];
    $last_year  = ('NA' != $breakup['last_year']) ? '$' . number_format($breakup['last_year'], 2, '.', ',') : 'NA';
    $last_year2 = ('NA' != $breakup['last_year2']) ? '$' . number_format($breakup['last_year2'], 2, '.', ',') : 'NA';
    $arrow1     = ('NA' != $last_year && 'NA' != $breakup['sales_profit']) ? ($breakup['sales_profit'] > $breakup['last_year']) ? 'up_arrow.gif' : 'down_arrow.gif' : '';
    $arrow2     = ('NA' != $last_year2 && 'NA' != $breakup['sales_profit']) ? ($breakup['sales_profit'] > $breakup['last_year2']) ? 'up_arrow.gif' : 'down_arrow.gif' : '';
    $last_link1 = (($links) ? '<a href=".?action=sales&type=weekly&start_time=' . $breakup['last_stamp']['start'] . '&end_time=' . $breakup['last_stamp']['end'] . '" target="_blank">' : '') . $last_year . (($links) ? '</a>' : '');
    $last_link2 = (($links) ? '<a href=".?action=sales&type=weekly&start_time=' . $breakup['last_stamp2']['start'] . '&end_time=' . $breakup['last_stamp2']['end'] . '" target="_blank">' : '') . $last_year2 . (($links) ? '</a>' : '');

    $invoice_url = array();
    if (0 != $site_id) {
        $invoice_url["site_id"] = $site_id;
    }

    $invoice_url["invoice_status"] = "processed";
    $invoice_url                   = "/webadmin/invoice/invoice_list.php?" . http_build_query($invoice_url) . "&open_month=";

    $open_url = array();
    if (0 != $site_id) {
        $open_url["site_id"] = $site_id;
    }

    $open_url["invoice_status"] = "open|proforma|sales";
    $open_url                   = "/webadmin/invoice/invoice_list.php?" . http_build_query($open_url) . "&open_month=";
?>
	<tr<?=($count % 2 == 1) ? ' class="alt"' : ''?> style="text-align:center">
		<td nowrap="nowrap">
<?php
if ($links) {
?>
			<a target="_blank" href="<?=$href?>">
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
		<td><a href="<?=htmlspecialchars($invoice_url . $array["start"])?>" target="_blank"><?=$breakup['total']?></a></td>
		<td>$<?=("NA" != $breakup["sales_total_dom"]) ? number_format($breakup["sales_total_dom"], 2, ".", ",") : $breakup["sales_total_dom"]?></td>
		<td>$<?=("NA" != $breakup["sales_total_int"]) ? number_format($breakup["sales_total_int"], 2, ".", ",") : $breakup["sales_total_int"]?></td>
		<td>$<?=("NA" != $breakup["sales_total"]) ? number_format($breakup['sales_total'], 2, '.', ',') : $breakup["sales_total"]?></td>
		<td>$<?=("NA" != $breakup["sales_cost"]) ? number_format($breakup["sales_cost"], 2, ".", ",") : $breakup["sales_cost"]?></td>
		<td>$<?=("NA" != $breakup["sales_profit"]) ? number_format($breakup['sales_profit'], 2, '.', ',') : $breakup["sales_profit"]?></td>
		<td><?=$breakup["gp_ratio_dom"]?>%</td>
		<td><?=$breakup["gp_ratio_int"]?>%</td>
		<td><?=$breakup["gp_ratio"]?>%</td>
		<td><?=('' != $arrow1) ? '<img src="/webadmin/images/' . $arrow1 . '">' : "&nbsp;"?></td><td><?=$last_link1?></td>
		<td><?=('' != $arrow2) ? '<img src="/webadmin/images/' . $arrow2 . '">' : "&nbsp;"?></td><td><?=$last_link2?></td>
		<td><a href="<?=htmlspecialchars($open_url . $array["start"])?>" target="_blank"><?=$breakup["pending"]?></a></td>
	</tr>
<?php
$count++;
}
?>
</table>
<br />
<div class="text-center"><?=(is_array($page_links)) ? implode(" ", $page_links) : $page_links?></div>
<br />
<?php
if ('true' != $print) {
?>
<div class="text-center"><a href="<?=$page_url?>&print=true" target="_blank">Print</a></div>
<?php
}

include_once __DIR__ . '/templates/profits_footer.tpl.php';