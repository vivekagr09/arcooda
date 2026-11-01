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

$site_id = intval($site_id);

$end_time     = $posted['end_time'];
$start_time   = $posted['start_time'];
$view         = $posted['view'];
$posted_month = $posted['month'];
$print        = $posted['print'];
if ('' == $start_time) {
    $start_time = mktime(0, 0, 0, 3, 1, 2004);
}
//$start_time=mktime(0,0,0,idate("m"),1,idate("Y"));
if ('' == $end_time) {
    $end_time = mktime(23, 59, 59, idate("m"), cal_days_in_month(CAL_GREGORIAN, idate("m"), idate("Y")), idate("Y"));
}

$week_start_time                                                               = mktime(0, 0, 0, idate('m', $start_time), idate('d', $start_time) + 1 - idate('w', $start_time), idate('Y', $start_time));
$week_end_time                                                                 = mktime(23, 59, 59, idate('m', $week_start_time), idate('d', $week_start_time) + 7 - idate('w', $week_start_time), idate('Y', $week_start_time));
$list                                                                          = array();
$list[date("d/m/Y", $week_start_time) . ' - ' . date("d/m/Y", $week_end_time)] = array('start' => $week_start_time, 'end' => $week_end_time);
while ($week_end_time < $end_time) {
    $week_start_time                                                               = mktime(0, 0, 0, idate('m', $week_end_time), idate('d', $week_end_time) + 1 - idate('w', $week_end_time), idate('Y', $week_end_time));
    $week_end_time                                                                 = mktime(23, 59, 59, idate('m', $week_start_time), idate('d', $week_start_time) + 7 - idate('w', $week_start_time), idate('Y', $week_start_time));
    $list[date("d/m/Y", $week_start_time) . ' - ' . date("d/m/Y", $week_end_time)] = array('start' => $week_start_time, 'end' => $week_end_time);
}
$page_url = $get_vars;
unset($page_url['start_time']);
unset($page_url['end_time']);
unset($page_url['month']);
unset($page_url['page']);
$page_url   = '?' . http_build_query($page_url);
$page_links = array();

if (0 != $posted_month) {
    //idate("m", $start_time) == idate("m", $end_time) ||
    $yr           = idate("Y", $start_time);
    $mst          = mktime(0, 0, 0, $posted_month, 1, $yr);
    $est          = mktime(23, 59, 59, $posted_month, cal_days_in_month(CAL_GREGORIAN, $posted_month, $yr), $yr);
    $page_links[] = '<a href="' . $page_url . '&start_time=' . $mst . '&end_time=' . $est . '">Show Full Month</a>';
}
$list = array_reverse($list, true);
if ('all' != $view && 'true' != $print) {
    $items_per_page = 25;
    $count_total    = count($list);
    $list           = array_chunk($list, $items_per_page, true);
    $count_pages    = count($list);
    $page_id        = max(0, min($count_pages - 1, $page));
    $list           = $list[$page];
    $count_display  = count($list);
    $page_links1    = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $items_per_page);
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

<?=formatHTMLTitle('Sales Breakup - Weekly')?>

<?php
    echo '<table class="wide" align="center">';
} else {
?>
<html>
	<head>
		<title>Sales Breakup - Weekly</title>
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
	<div style="font-size:20px;text-align:center"><b>Sales Breakup - Weekly</b></div><br />
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
foreach ($list as $key => $array) {
    $breakup    = profitSalesBreakup($array['start'], $array['end'], $site_id, true, 'weekly');
    $site_str   = ($site_id) ? '&site_id=' . $site_id : '';
    $class      = ($count % 2 == 1) ? ' class="alt"' : '';
    $href       = '/webadmin/invoice/invoice_list.php?open_week=' . $array['start'] . '&status=processed' . $site_str;
    $last_year  = ('NA' != $breakup['last_year']) ? '$' . number_format($breakup['last_year'], 2, '.', ',') : 'NA';
    $last_year2 = ('NA' != $breakup['last_year2']) ? '$' . number_format($breakup['last_year2'], 2, '.', ',') : 'NA';
    $arrow1     = ('NA' != $last_year && 'NA' != $breakup['sales_profit']) ? ($breakup['sales_profit'] > $breakup['last_year']) ? 'up_arrow.gif' : 'down_arrow.gif' : '';
    $arrow2     = ('NA' != $last_year2 && 'NA' != $breakup['sales_profit']) ? ($breakup['sales_profit'] > $breakup['last_year2']) ? 'up_arrow.gif' : 'down_arrow.gif' : '';
    $last_link1 = (($links) ? '<a href=".?month=' . date("n", $start_time) . '&action=sales&type=weekly&start_time=' . $breakup['last_stamp']['start'] . '&end_time=' . $breakup['last_stamp']['end'] . '" target="_blank">' : '') . $last_year . (($links) ? '</a>' : '');
    $last_link2 = (($links) ? '<a href=".?month=' . date("n", $start_time) . '&action=sales&type=weekly&start_time=' . $breakup['last_stamp2']['start'] . '&end_time=' . $breakup['last_stamp2']['end'] . '" target="_blank">' : '') . $last_year2 . (($links) ? '</a>' : '');

    $invoice_url = array();
    if (0 != $site_id) {
        $invoice_url["site_id"] = $site_id;
    }

    $invoice_url["invoice_status"] = "processed";
    $invoice_url                   = "/webadmin/invoice/invoice_list.php?" . http_build_query($invoice_url) . "&open_week=";

    $open_url = array();
    if (0 != $site_id) {
        $open_url["site_id"] = $site_id;
    }

    $open_url["invoice_status"] = "open|proforma|sales";
    $open_url                   = "/webadmin/invoice/invoice_list.php?" . http_build_query($open_url) . "&open_week=";
?>
	<tr<?=$class?> style="text-align:center">
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
<?php
if ('all' != $view) {
    $page_links[] = '<a href=".?action=sales&type=weekly' . (('' != $posted['start_time'] && '' != $posted['end_time']) ? '' : '&view=all') . '">View All</a>';
}

$print_url = $get_vars;
unset($print_url['action']);
unset($print_url['type']);
$print_url    = '.?action=sales&type=weekly&print=true&' . http_build_query($print_url);
$page_links[] = '<a href="' . $print_url . '" target="_blank">Print</a>';
/*
$end_month = 3;
$end_year  = 2004;
$max_month = idate("m");
$max_year  = idate("Y");
$mon       = idate('n', $start_time);
$yr        = idate('Y', $start_time);

$prevy=$nexty=$yr;
$prev=$mon-1;
if ($prev<1) { $prev=12; $prevy--; }
$next=$mon+1;
if ($next>12){ $next=1;$nexty++; }

$pst=mktime(0,0,0,$prev,1,$prevy);
$pet=mktime(23,59,59,$prev,cal_days_in_month(CAL_GREGORIAN,$prev,$prevy),$prevy);
$nst=mktime(0,0,0,$next,1,$nexty);
$net=mktime(23,59,59,$next,cal_days_in_month(CAL_GREGORIAN,$next,$nexty),$nexty);

if (!($yr==$end_year && $prev<$end_month)) $page_links[]='<a href="'.$page_url.'&start_time='.$pst.'&end_time='.$pet.'"><< Previous Month</a>';
if (!($yr==$max_year && $next>$max_month)) $page_links[]='<a href="'.$page_url.'&start_time='.$nst.'&end_time='.$net.'">Next Month >></a>';
 */
if ('true' != $print) {
?>
<div class="text-center"><?=implode("&nbsp;&nbsp;", $page_links)?></div>
<br />
<div class="text-center"><?=$page_links1?></div>
<?php
}

include_once __DIR__ . '/templates/profits_footer.tpl.php';
