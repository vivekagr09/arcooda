<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('product');

$site_id    = intval($posted['site_id']);
$start_time = intval($posted['start_time']);
$end_time   = intval($posted['end_time']);
$top        = (0 != $posted['top']) ? intval($posted['top']) : 50;
$type       = ($posted['type']) ? $posted['type'] : 'most';
$view       = $posted['view'];
$sale_type  = ($posted['sale_type']) ? $posted['sale_type'] : 'yearly';
$print      = $posted['print'];

if (0 == $start_time) {
    if ('yearly' == $sale_type) {
        $start_time = mktime(0, 0, 0, idate("m"), 1, idate("Y") - 1);
        $end_time   = mktime(23, 59, 59, idate("m"), 2, idate("Y"));
    } elseif ('quarterly' == $sale_type) {
        $match      = array(1 => 1, 2 => 1, 3 => 1, 4 => 4, 5 => 4, 6 => 4, 7 => 7, 8 => 7, 9 => 7, 10 => 10, 11 => 10, 12 => 10);
        $mon        = $match[idate("m")];
        $e_mon      = $mon + 2;
        $start_time = mktime(0, 0, 0, $mon, 1, idate("Y"));
        $end_time   = mktime(23, 59, 59, $e_mon, cal_days_in_month(CAL_GREGORIAN, $e_mon, idate("Y")), idate("Y"));
    } elseif ('monthly' == $sale_type) {
        $start_time = mktime(0, 0, 0, idate("m"), 1, idate("Y"));
        $end_time   = mktime(23, 59, 59, idate("m"), cal_days_in_month(CAL_GREGORIAN, idate("m"), idate("Y")), idate("Y"));
    } elseif ('weekly' == $sale_type) {
        $start_time = mktime(0, 0, 0, idate('m'), idate('d') + 1 - idate('w'), idate('Y'));
        $end_time   = mktime(23, 59, 59, idate('m', $start_time), idate('d', $start_time) + 7 - idate('w', $start_time), idate('Y', $start_time));
    }
}
$profit_list = profitProductViewSearch($start_time, $end_time, $site_id, $sale_type);

if ("most" != $type) {
    $profit_list = array_reverse($profit_list, true);
}

/*
if ($type != "most")
{
$product_query = productQuery([
    'active'  => 'y',
    'site_id' => $site_id,
]);
$product_list = productSearch($product_query);
$product_list = productSort($product_list, 'product_name');
foreach ($product_list as $k => $v) if (isset($profit_list[$v])) unset($product_list[$k]);
$new_list = array();
foreach ($product_list as $v)
{
$new_list[$v]['total_qty'] = $new_list[$product_id]['total_sales'] = $new_list[$v]['total_profit'] = 0;
$new_list[$v]['last_ranking'] = '';
$new_list[$v]['product_name'] = productGetValue($v, 'product_name');
}
$profit_list = $new_list;
}
 */

if (array_key_exists("order_by", $posted)) {
    $temp = array();
    foreach ($profit_list as $k => $v) {
        $temp[$v[$posted["order_by"]]][$k] = $v;
    }

    if (array_key_exists("order", $posted) && "desc" == $posted["order"]) {
        krsort($temp);
    } else {
        ksort($temp);
    }

    $profit_list = array();
    foreach ($temp as $v1) {
        foreach ($v1 as $k2 => $v2) {
            $profit_list[$k2] = $v2;
        }
    }

    unset($temp);
}

$page_url = $get_vars;
unset($page_url['top']);
unset($page_url['view']);
$page_url = '?' . http_build_query($page_url);
if ('all' != $view) {
    $profit_list = array_chunk($profit_list, $top);
    $profit_list = $profit_list[0];
    $top2        = (50 == $top ? '20' : '50');
    $extra       = '<small><i>(<a href="' . $page_url . '&top=' . $top2 . '">View Top ' . $top2 . '</a>)</i></small>';
} else {
    $extra = '<small><i>(<a href="' . $page_url . '&top=50">View Top 50</a>)</i></small>';
}

$links = ('true' != $print) ? true : false;
if ('true' != $print) {
    include_once __DIR__ . "/templates/profits_header.tpl.php";
?>

<?=formatHTMLTitle(
    ucwords($type) . ' Viewed Products',
    $extra
)?>

<?php
} else {
?>
<html>
	<head>
		<title><?=htmlspecialchars(ucwords($type))?> Viewed Products</title>
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
	<div class="text-center" style="font-size:20px;"><?=htmlspecialchars(ucwords($type))?> Viewed Products</div>
<?php
}
$range = date(_('jS F, Y'), $start_time) . ' to ' . date(_('jS F, Y'), $end_time);
?>
<div class="text-center">Date Range: <?=htmlspecialchars($range)?></div>
<?php
if (count($profit_list)) {
?>
<h3 class="text-center">
<?php
if ('all' != $view) {
?>
	Currently Showing top <?=$top?> products
<?php
} else {
?>
	Viewing All Products
<?php
}
?>
</h3>
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
    $order_url = $posted;
    unset($order_url["order_by"]);
    if (array_key_exists("order", $order_url)) {
        unset($order_url["order"]);
    } else {
        $order_url["order"] = "desc";
    }

    $order_url = "?" . http_build_query($order_url) . "&order_by=";
?>
	<tr>
		<th><a href="<?=htmlspecialchars($order_url . "ranking")?>">Ranking</a></th>
		<th><a href="<?=htmlspecialchars($order_url . "product_name")?>">Product Name</a></th>
		<th><a href="<?=htmlspecialchars($order_url . "total_views_dom")?>">Australian Views</a></th>
		<th><a href="<?=htmlspecialchars($order_url . "total_views_int")?>">Overseas Views</a></th>
		<th><a href="<?=htmlspecialchars($order_url . "total_views")?>">Total Views</a></th>
		<th><a href="<?=htmlspecialchars($order_url . "total_qty")?>">Units Sold</a></th>
		<th>Sales:Views Ratio</th>
		<th colspan="2"><a href="<?=htmlspecialchars($order_url . "last_ranking")?>">Last Period Ranking</a></th>
	</tr>
<?php
$count = 1;
    foreach ($profit_list as $product_id => $array) {
        $arrow       = ($count != $array['last_ranking'] && '' != $array['last_ranking']) ? (($count < $array['last_ranking']) ? 'up_arrow.gif' : 'down_arrow.gif') : '';
        $dom_ratio   = ($array['total_views'] > 0) ? ($array['total_views_dom'] / $array['total_views']) * 100 : 0;
        $int_ratio   = ($array['total_views'] > 0) ? ($array['total_views_int'] / $array['total_views']) * 100 : 0;
        $hit_ratio   = ($array['total_views'] > 0) ? ($array['total_qty'] / $array['total_views']) * 100 : 0;
        $unit_profit = ($array['total_qty'] > 0) ? $array['total_profit'] / $array['total_qty'] : 0;

        $product = productFromDatabase($array["product_str"]);
?>
	<tr<?=($count % 2 == 0) ? ' class="alt"' : ''?> style="text-align:center">
		<td><?=htmlspecialchars($array["ranking"])?></td>
<?php
if (0 != $product['id']) {
?>
		<td><a href="<?=htmlspecialchars($product["product_url"])?>" target="_blank"><?=htmlspecialchars($product['product_name'])?></a></td>
<?php
} else {
?>
		<td><?=htmlspecialchars($array['product_name'])?></td>
<?php
}
?>
		<td><?=htmlspecialchars($array['total_views_dom'])?> (<?php printf("%.0f", $dom_ratio)?>%)</td>
		<td><?=htmlspecialchars($array['total_views_int'])?> (<?php printf("%.0f", $int_ratio)?>%)</td>
		<td><?=htmlspecialchars($array['total_views'])?></td>
		<td><?=htmlspecialchars($array['total_qty'])?></td>
		<td><?=(0 != $array["total_qty"] && 0 == $array["total_views"]) ? "<span style=\"color:#f00;\">inf</span>" : ((0 != $array["total_qty"]) ? sprintf('%0.2f%%', $hit_ratio) : "-")?></td>
		<td style="text-align:right;"><?=('' != $arrow) ? '<img src="/webadmin/images/' . $arrow . '">' : "&nbsp;"?></td>
		<td style="text-align:left;"><?=htmlspecialchars($array['last_ranking'])?></td>
	</tr>
<?php
$count++;
    }
?>
</table>
<br />
<?php
} else {
    echo 'No Results found for this date range.';
}

$page_links = array();
$url        = $get_vars;
unset($url['start_time']);
unset($url['end_time']);
$url              = '?' . http_build_query($url);
list($mon1, $yr1) = explode('-', date("n-Y", $start_time));
$emonth           = 3;
$eyear            = 2004;
$mmonth           = idate("m");
$myear            = idate("Y");
if ('yearly' == $sale_type) {
    $s_time1 = mktime(0, 0, 0, $mon1, 1, $yr1 - 1);
    $e_time1 = mktime(23, 59, 59, $mon1, 2, $yr1);
    $s_time2 = mktime(0, 0, 0, $mon1, 1, $yr1 + 1);
    $e_time2 = mktime(23, 59, 59, $mon1, 2, $yr1 + 2);
    if ($yr1 - 1 >= 2003) {
        $page_links[] = '<a href="' . $url . '&start_time=' . $s_time1 . '&end_time=' . $e_time1 . '"><< Previous Year (' . ($yr1 - 1) . ')</a>';
    }

    if ($yr1 + 2 <= idate("Y")) {
        $page_links[] = '<a href="' . $url . '&start_time=' . $s_time2 . '&end_time=' . $e_time2 . '">Next Year (' . ($yr1 + 1) . ')>></a>';
    }
} elseif ('quarterly' == $sale_type) {
    $p_quarter = array(1 => 10, 2 => 10, 3 => 10, 4 => 1, 5 => 1, 6 => 1, 7 => 4, 8 => 4, 9 => 4, 10 => 7, 11 => 7, 12 => 7);
    $n_quarter = array(1 => 4, 2 => 4, 3 => 4, 4 => 7, 5 => 7, 6 => 7, 7 => 10, 8 => 10, 9 => 10, 10 => 1, 11 => 1, 12 => 1);
    $pm        = $p_quarter[$mon1];
    $py        = $yr1;
    if (10 == $pm) {
        $py--;
    }

    $nm = $n_quarter[$mon1];
    $ny = $yr1;
    if (1 == $nm) {
        $ny++;
    }

    $pme = $pm + 2;
    $nme = $nm + 2;
    $pst = mktime(0, 0, 0, $pm, 1, $py);
    $pet = mktime(23, 59, 59, $pme, cal_days_in_month(CAL_GREGORIAN, $pme, $py), $py);
    $nst = mktime(0, 0, 0, $nm, 1, $ny);
    $net = mktime(23, 59, 59, $nme, cal_days_in_month(CAL_GREGORIAN, $nme, $ny), $ny);
    if (!($py == $eyear && $pm < $emonth)) {
        $page_links[] = '<a href="' . $url . '&start_time=' . $pst . '&end_time=' . $pet . '"><< Previous Quarter</a>';
    }

    if (!($ny == $myear && $nm > $mmonth)) {
        $page_links[] = '<a href="' . $url . '&start_time=' . $nst . '&end_time=' . $net . '">Next Quarter >></a>';
    }
} elseif ('monthly' == $sale_type) {
    $py = $ny = $yr1;
    $nm = $mon1 + 1;
    $pm = $mon1 - 1;
    if (0 == $pm) {
        $pm = 12;
        $py--;
    }
    if ($nm > 12) {
        $nm = 1;
        $ny++;
    }
    $pst = mktime(0, 0, 0, $pm, 1, $py);
    $pet = mktime(23, 59, 59, $pm, cal_days_in_month(CAL_GREGORIAN, $pm, $py), $py);
    $nst = mktime(0, 0, 0, $nm, 1, $ny);
    $net = mktime(23, 59, 59, $nm, cal_days_in_month(CAL_GREGORIAN, $nm, $ny), $ny);
    if (!($py == $eyear && $pm < $emonth)) {
        $page_links[] = '<a href="' . $url . '&start_time=' . $pst . '&end_time=' . $pet . '"><< Previous Month</a>';
    }

    if (!($ny == $myear && $nm > $mmonth)) {
        $page_links[] = '<a href="' . $url . '&start_time=' . $nst . '&end_time=' . $net . '">Next Month >></a>';
    }
} elseif ('weekly' == $sale_type) {
    list($nd, $nm, $ny, $nw) = explode('-', date('d-m-Y-w', strtotime('+1 week', $start_time)));
    list($pd, $pm, $py, $pw) = explode('-', date('d-m-Y-w', strtotime('-1 week', $start_time)));
    list($ed, $em, $ey)      = explode('-', date('d-m-Y'));
    $end_time                = mktime(0, 0, 0, 3, 1, 2004);
    $pst                     = mktime(0, 0, 0, $pm, $pd + 1 - $pw, $py);
    $pet                     = mktime(0, 0, 0, idate('m', $pst), idate('d', $pst) + 7 - idate('w', $pst), idate('Y', $pst));
    $nst                     = mktime(0, 0, 0, $nm, $nd + 1 - $nw, $ny);
    $net                     = mktime(0, 0, 0, idate('m', $nst), idate('d', $nst) + 7 - idate('w', $nst), idate('Y', $nst));
    if ($pst > $end_time) {
        $page_links[] = '<a href="' . $url . '&start_time=' . $pst . '&end_time=' . $pet . '"><< Previous Week</a>';
    }

    if ($nst < time()) {
        $page_links[] = '<a href="' . $url . '&start_time=' . $nst . '&end_time=' . $net . '">Next Week >></a>';
    }
}
if ('all' != $view) {
    $page_links[] = '<a href="' . $page_url . '&view=all">View All Products</a>';
}

$url          = $get_vars;
$url          = '?' . http_build_query($url);
$page_links[] = '<a href="' . $url . '&print=true" target="_blank">Print</a>';
?>
<?php
if ('true' != $print) {
?>
<div class="text-center"><?=implode("&nbsp;|&nbsp;", $page_links)?></div>
<?php
}

include_once __DIR__ . "/templates/profits_footer.tpl.php";
