<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/templates/profits_header.tpl.php';

$page = $posted['page'];
if ('' == $page) {
    $page = 0;
}

$year    = intval($posted['year']);
$site_id = intval($posted['site_id']);
$view    = $posted['view'];
$list    = ($year) ? profitSalesMonthsForYear($year) : profitSalesMonths();
if ('all' != $view) {
    $items_per_page = 12;
    $count_total    = count($list);
    $list           = array_chunk($list, 12, true);
    $count_pages    = count($list);
    $page_id        = max(0, min($count_pages - 1, $page));
    $list           = $list[$page];
    $count_display  = count($list);
    $page_url       = $get_vars;
    unset($page_url['page']);
    $page_url   = '?' . http_build_query($page_url);
    $page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $items_per_page);
}

?>

<?=formatHTMLTitle('Profit & Loss Summary')?>

<table class="table_grid" align="center">
	<tr>
		<th>Date Range</th>
		<th>Qty Invoices</th>
		<th>Australian Sales (USD)</th>
		<th>Overseas Sales (USD)</th>
		<th>Total Sales (USD)</th>
		<th>Cost of Goods (USD)</th>
		<th>Gross Profit (USD)</th>
		<th>Break-Even</th>
		<th>Net Profit</th>
		<th colspan="2">Last Year Net Profit</th>
		<th>Open Invoices</th>
	</tr>
<?php
$count = 0;
foreach ($list as $key => $array) {
    $breakup            = profitSalesBreakup($array['start'], $array['end'], $site_id, true, 'monthly');
    list($month, $year) = explode('-', date("m-Y", $array['start']));
    $break_even         = profitBreakEvenMonth($month, $year);
    $break_even2        = profitBreakEvenMonth($month, $year - 1);
    $net_profit         = 'NA';
    $net_profit2        = 'NA';
    if (false !== $break_even) {
        $net_profit = $breakup['sales_profit'] - $break_even;
    }

    if (false !== $break_even2) {
        $net_profit2 = $breakup['last_year'] - $break_even2;
    }

    $arrow1 = ('NA' != $net_profit2 && 'NA' != $net_profit) ? (($net_profit > $net_profit2) ? 'up_arrow.gif' : 'down_arrow.gif') : '';
?>
	<tr<?=($count % 2 == 1) ? ' class="alt"' : ''?> style="text-align:center">
		<td><a target="_blank" href=".?site_id=<?=$site_id?>&action=sales&type=weekly&start_time=<?=$array['start']?>&end_time=<?=$array['end']?>"><?=$key?></a></td>
		<td><?=$breakup['total']?></td>
		<td>$<?=is_numeric($breakup["sales_total_dom"]) ? number_format(convertCurrency($breakup["sales_total_dom"],'AUD','USD'), 2, ".", ",") : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_total_int"]) ? number_format(convertCurrency($breakup["sales_total_int"],'AUD','USD'), 2, ".", ",") : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_total"]) ? number_format(convertCurrency($breakup['sales_total'],'AUD','USD'), 2, '.', ',') : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_cost"]) ? number_format(convertCurrency($breakup["sales_cost"],'AUD','USD'), 2, ".", ",") : "NA"?></td>
		<td>$<?=is_numeric($breakup["sales_profit"]) ? number_format(convertCurrency($breakup['sales_profit'],'AUD','USD'), 2, '.', ',') : "NA"?></td>
		<td><a href=".?action=break_even&year=<?=$year?>&month=<?=$month?>" target="_blank"><?=sprintf('%.2f', $break_even)?></a></td>
		<td><?= is_numeric($net_profit) ? convertCurrency($net_profit,'AUD','USD') : $net_profit ?></td>
		<td style="text-align:right;"><?=('' != $arrow1) ? '<img src="/webadmin/images/' . $arrow1 . '">' : "&nbsp;"?></td><td style="text-align:left;"><?=$net_profit2?></td>
		<td><?=$breakup["pending"]?></td>
	</tr>
<?php
$count++;
}
?>
</table>
<br />
<div class="text-center"><?=$page_links?></div>
<?php
include_once __DIR__ . '/templates/profits_footer.tpl.php';