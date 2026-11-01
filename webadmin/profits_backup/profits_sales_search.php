<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

$site_id   = intval($posted['site_id']);
$sale_type = ($posted['sale_type']) ? $posted['sale_type'] : 'yearly';
$print     = $posted['print'];

$periods = array('weekly' => '', 'monthly' => '', 'quarterly' => '', 'yearly' => '');
$period  = $posted['period'];
$site_id = $posted['site_id'];
$options = '';
foreach ($periods as $key => $option) {
    $options .= '<option value="' . $key . '"' . (($period == $key) ? 'selected' : '') . '>' . (('' != $option) ? $option : ucwords($key)) . '</option>';
}

if ('true' != $print) {
    include_once __DIR__ . '/templates/profits_header.tpl.php';
?>

<?=formatHTMLTitle('Sales Search')?>

<form action="" method="get">
<input type="hidden" name="action" value="search" />
<?php
if (0 != $site_id) {
?>
<input type="hidden" name="site_id" value="<?=$site_id?>" />
<?php
}
?>
<table style="border-color:#FF9900;margin:1px;width:95%;" border="0" align="center" cellpadding="2" cellspacing="0">
	<tr style="background-color:#FFFFCC;">
		<td style="width:30%;text-align:right;"><b>Product ID :</b></td>
		<td style="text-align:left"><input type="text" name="product_id" style="width:60%;" value="<?=$posted['product_id']?>"/></td>
	</tr>
	<tr style="background-color:#FFFFCC;">
		<td style="width:30%;text-align:right;"><b>Period :</b></td><td style="text-align:left"><select name="period" style="width:60%;"/><?=$options?></select></td>
	</tr>
	<tr style="background-color:#FFFFCC;text-align:center"><td colspan="2"><input type="submit" value="Search" /></td>
</table>
</form>
<br />
<?php
} else {
?>
<html>
	<head>
		<title>Sales Search Results</title>
	</head>
	<script>
	function printpage()
	{
		if (window.print) window.print();
		else alert('To print this page, select Print Frame from the file menu.');
	}

	function doPrint()
	{
		if (confirm("Click OK to print this document")) printpage();
	}
	</script>
	<body onload="doPrint()">
	<div class="text-center" style="font-size:20px;">Sales Search Results - Product ID: <?=$product_id?><br />Period: <?=ucwords($period)?></div>
	<br />
<?php
}
$list = profitSalesSearchList($posted['period']);
$list = array_reverse($list, true);
if ('search' == $posted['action'] && $posted['product_id'] > 0 && is_numeric($product_id)) {
    $product = profitProductDetails($posted['product_id'], $site_id);
    if ($product) {
        $av_profit = 0;
        if ($product['total_qty'] > 0) {
            $av_profit = $product['total_profit'] / $product['total_qty'];
        }

        $gp_ratio = 0;
        if ($product['total_sales'] > 0) {
            $gp_ratio = ($product['total_profit'] / $product['total_sales']) * 100;
        }

        list($am, $ay) = explode('-', date("n-Y", strtotime($product['date_added'])));
?>
<div style="padding-left:20px;">
<?php
if ('true' != $print) {
?>
<table class="wide" style="width:60%;">
<?php
} else {
?>
<table style="border-collapse:collapse;border:1px solid #CCCCCC" border="1" align="center">
<?php
}
?>
	<tr><td class="left_title" style="width:40%;"><b>Product Name :</b></td><td><?=$product['product_name']?></td></tr>
	<tr><td class="left_title" style="width:40%;"><b>Date Added :</b></td><td><?=date(_('jS F, Y'), strtotime($product['date_added']))?></td></tr>
	<tr><td class="left_title" style="width:40%;"><b>Total Units Sold :</b></td><td><?=$product['total_qty']?></td></tr>
	<tr><td class="left_title" style="width:40%;"><b>Total Profit (AUD) :</b></td><td>$<?=$product['total_profit']?></td></tr>
	<tr><td class="left_title" style="width:40%;"><b>Avg Profit (AUD) :</b></td><td>$<?=sprintf('%.2f', $av_profit)?></td></tr>
	<tr><td class="left_title" style="width:40%;"><b>GP Ratio :</b></td><td><?=sprintf('%.0f', $gp_ratio)?>%</td></tr>
</table>
</div>
<br />
<?php
if ('true' != $print) {
?>
<table class="wide" align="center">
<?php
} else {
?>
<table style="border-collapse:collapse;border:1px solid #CCCCCC" border="1" align="center">
<?php
}
?>
	<tr><th>Date Range</th><th>Units Sold</th><th>Total Sales (AUD)</th><th>Total Profit (AUD)</th><th>Average Unit Profit (AUD)</th><th>GP Ratio</th></tr>
<?php
$count = 0;
        foreach ($list as $date_range => $array) {
            list($sm, $sy) = explode('-', date("n-Y", $array['end']));
            if ($sy < $ay) {
                continue;
            }

            if ($sy == $ay && $sm < $am) {
                continue;
            }

            $breakup   = profitSalesDetails($posted['product_id'], $array['start'], $array['end'], $site_id, $posted['period']);
            $av_profit = ($breakup['total_qty'] > 0) ? ($breakup['total_profit'] / $breakup['total_qty']) : 0;
            $gp_ratio  = ($breakup['total_sales'] > 0) ? (($breakup['total_profit'] / $breakup['total_sales']) * 100) : 0;
?>
	<tr<?=($count % 2 == 1) ? ' class="alt"' : ''?> style="text-align:center">
		<td><?=$date_range?></td><td><?=$breakup['total_qty']?></td><td>$<?=sprintf('%.2f', $breakup['total_sales'])?></td>
		<td>$<?=sprintf('%.2f', $breakup['total_profit'])?></td><td>$<?=sprintf('%.2f', $av_profit)?></td><td><?=sprintf('%.0f', $gp_ratio)?>%</td>
	</tr>
<?php
$count++;
        }
?>
</table>
<br />
<?php
$url = $get_vars;
        $url = '?' . http_build_query($url);
        if ('true' != $print) {
?>
<div class="text-center"><a href="<?=$url?>&quot;print=true" target="_blank">Print Results</a></div>
<br />
<?php
		}
    }
}

include_once __DIR__ . '/templates/profits_footer.tpl.php';