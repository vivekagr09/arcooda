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

$site_id = intval($posted['site_id']);
$print   = $posted["print"];

$year                     = intval($posted['year']);
$current_year             = idate("Y");
$mon                      = idate('m');
if ($mon < 7) {
    $current_year--;
}

if (0 == $year) {
    $year = $current_year;
}

$quarters = profitGetQuarters($year);

$page_url = $get_vars;
unset($page_url['year']);
$page_url = '?' . http_build_query($page_url);
$list2    = array_chunk($quarters, 1);
$temp     = $list2[0];
$yp       = idate("Y", $temp[0]['start']);
$lyear    = $yp - 1;
$lyear2   = $yp - 2;
$links    = ('true' != $print) ? true : false;
if ('true' != $print) {
    include_once __DIR__ . '/templates/profits_header.tpl.php';
?>

<?=formatHTMLTitle('Sales Breakup - Quarterly (' . $year . '- ' . ($year + 1) . ')')?>

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
	<div style="font-size:20px;text-align:center"><b>Sales Breakup - Quarterly <?='(' . $year . '- ' . ($year + 1) . ')'?></b></div><br />
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
		<th colspan="2"><?=$lyear?> Profit</th>
		<th colspan="2"><?=$lyear2?> Profit</th>
		<th>Open Invoices</th>
	</tr>
<?php
$count = 0;
foreach ($quarters as $key => $array) {
    $breakup    = profitSalesBreakup($array['start'], $array['end'], $site_id, true, 'quarterly');
    $href       = '.?action=sales&type=monthly&start_time=' . $array['start'] . '&end_time=' . $array['end'];
    $last_year  = ('NA' != $breakup['last_year']) ? '$' . number_format($breakup['last_year'], 2, '.', ',') : 'NA';
    $last_year2 = ('NA' != $breakup['last_year2']) ? '$' . number_format($breakup['last_year2'], 2, '.', ',') : 'NA';
    $arrow1     = ('NA' != $last_year && 'NA' != $breakup['sales_profit']) ? ($breakup['sales_profit'] > $breakup['last_year']) ? 'up_arrow.gif' : 'down_arrow.gif' : '';
    $arrow2     = ('NA' != $last_year2 && 'NA' != $breakup['sales_profit']) ? ($breakup['sales_profit'] > $breakup['last_year2']) ? 'up_arrow.gif' : 'down_arrow.gif' : '';

    $invoice_url = array();
    if (0 != $site_id) {
        $invoice_url["site_id"] = $site_id;
    }

    $invoice_url["invoice_status"] = "processed";
    $invoice_url                   = "/webadmin/invoice/invoice_list.php?" . http_build_query($invoice_url) . "&open_quarter=";

    $open_url = array();
    if (0 != $site_id) {
        $open_url["site_id"] = $site_id;
    }

    $open_url["invoice_status"] = "open|proforma|sales";
    $open_url                   = "/webadmin/invoice/invoice_list.php?" . http_build_query($open_url) . "&open_quarter=";
?>
	<tr<?=($count % 2 == 1) ? ' class="alt"' : ''?> style="text-align:center">
		<td nowrap="nowrap">
<?php
if ($links) {
?>
			<a href="<?=$href?>" target="_blank">
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
		<td>$<?=is_numeric($breakup["sales_total_dom"]) ? number_format($breakup["sales_total_dom"], 2, ".", ",") : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_total_int"]) ? number_format($breakup["sales_total_int"], 2, ".", ",") : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_total"]) ? number_format($breakup['sales_total'], 2, '.', ',') : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_cost"]) ? number_format($breakup["sales_cost"], 2, ".", ",") : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_profit"]) ? number_format($breakup['sales_profit'], 2, '.', ',') : "NA"?></td>
		<td><?=$breakup["gp_ratio_dom"]?>%</td>
		<td><?=$breakup["gp_ratio_int"]?>%</td>
		<td><?=$breakup["gp_ratio"]?>%</td>
		<td><?=('' != $arrow1) ? '<img src="/webadmin/images/' . $arrow1 . '">' : "&nbsp;"?></td><td><?=$last_year?></td>
		<td><?=('' != $arrow2) ? '<img src="/webadmin/images/' . $arrow2 . '">' : "&nbsp;"?></td><td><?=$last_year2?></td>
		<td><a href="<?=htmlspecialchars($open_url . $array["start"])?>" target="_blank"><?=$breakup["pending"]?></a></td>
	</tr>
<?php
$count++;
}
?>
</table>
<br />
<?php
$end_year = 2003;
$max_year = $current_year;
$links    = array();
if ($year > $end_year) {
    $links[] = '<a href="' . $page_url . '&year=' . ($year - 1) . '"><< Previous Year</a>';
}

if ($year < $max_year) {
    $links[] = '<a href="' . $page_url . '&year=' . ($year + 1) . '">Next Year>></a>';
}

$links[] = '<a href="' . $page_url . '&print=true" target="_blank">Print</a>';
if ('true' != $print) {
?>
<div class="text-center"><?=implode("&nbsp;&nbsp;", $links)?></div>
<?php
}

include_once __DIR__ . '/templates/profits_footer.tpl.php';