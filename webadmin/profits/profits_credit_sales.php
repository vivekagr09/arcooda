<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

$site_id    = intval($posted['site_id']);
$start_time = intval($posted['start_time']);
$end_time   = intval($posted['end_time']);
$type       = ($posted['type']) ? $posted['type'] : 'best';
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
$profit_list = profitPaymentTypeSearch($start_time, $end_time, $site_id, $sale_type);
$ttotal      = 0;
if (count($profit_list)) {
    foreach ($profit_list as $payment_type => $array) {
        $ttotal += $array['total_qty'];
    }
}

if ('best' == $type) {
    $profit_list = array_reverse($profit_list, true);
}

$ctotal = "0.00";
if (count($profit_list)) {
    foreach ($profit_list as $payment_type => $array) {
        if (in_array($payment_type, array("VC", "MC", "AE"))) {
            $ctotal = bcadd($ctotal, $array['total_qty'], 2);
        }
    }
}

$page_url = $get_vars;
unset($page_url['top']);
unset($page_url['view']);
$page_url = '?' . http_build_query($page_url);
$links    = ('true' != $print) ? true : false;
if ('true' != $print) {
    include_once __DIR__ . '/templates/profits_header.tpl.php';

?>

<?=formatHTMLTitle(
    'Payment Type',
    $extra
)?>

<?php
} else {
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?=ucwords($type)?> Payment Type</title>
		<meta charset="UTF-8" />
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
	</head>
	<body onload="doPrint()">
	<div style="text-align:center;font-size:20px;"><?=ucwords($type)?> Payment Type</div>
<?php
}
$range = date(_('jS F, Y'), $start_time) . ' to ' . date(_('jS F, Y'), $end_time);
?>
<div style="text-align:center;">Date Range: <?=$range?></div>
<?php
if (count($profit_list)) {
?>
<h3 style="text-align:center;">Viewing All Payment Types</h3>
<br />
<?php
if ('true' != $print) {
?>
<table class="table_grid" style="margin:auto;">
<?php
} else {
?>
<table style="border-collapse:collapse;border:1px solid #ccc;margin:auto;" border="1">
<?php
}
?>
	<tr>
		<th>Ranking</th>
		<th>Payment Type</th>
		<th>% of Orders</th>
		<th>Qty Payments</th>
		<th>Total Payments (USD)</th>
<!--		<th>Total Sales (AUD)</th>
		<th>Total Profit (AUD)</th>
		<th>Profit per Order (AUD)</th> -->
		<th colspan="2">Last Period Ranking</th>
	</tr>
<?php
$count = 1;
    foreach ($profit_list as $payment_type => $array) {
        $arrow       = ($count != $array['last_ranking'] && '' != $array['last_ranking']) ? (($count < $array['last_ranking']) ? 'up_arrow.gif' : 'down_arrow.gif') : '';
        $percent     = ($array['total_qty'] / $ttotal) * 100;
        $unit_profit = ($array['total_qty'] > 0) ? $array['total_profit'] / $array['total_qty'] : 0;
?>
	<tr<?=($count % 2 == 0) ? ' class="alt"' : ''?> style="text-align:center">
		<td><?=$count?></td>
		<td><?=htmlspecialchars($array['payment_type_name'])?></td>
		<td><?=sprintf('%.2f', $percent)?>%</td>
		<td><?=$array['total_qty']?></td>
		<td><?=sprintf('%.2f', convertCurrency($array['total_sales'],'AUD','USD'))?></td>
		<!-- <td><?=sprintf('%.2f', $array['total_profit'])?></td>
		<td><?=sprintf('%.2f', $unit_profit)?></td> -->
		<td style="text-align:right;"><?=('' != $arrow) ? '<img src="/webadmin/images/' . $arrow . '" />' : " "?></td>
		<td style="text-align:left;"><?=$array['last_ranking']?></td>
	</tr>
<?php
$count++;
    }
?>
</table>
<br />
<div style="text-align:center;">Credit Card Transactions owing from Highway Group Pty Ltd: $<?=htmlspecialchars($ctotal)?></div>
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
        $page_links[] = '<a href="' . htmlspecialchars($url) . '&amp;start_time=' . $s_time1 . '&amp;end_time=' . $e_time1 . '"><< Previous Year</a>';
    }

    if ($yr1 + 2 <= idate("Y")) {
        $page_links[] = '<a href="' . htmlspecialchars($url) . '&amp;start_time=' . $s_time2 . '&amp;end_time=' . $e_time2 . '">Next Year >></a>';
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
        $page_links[] = '<a href="' . htmlspecialchars($url) . '&amp;start_time=' . $pst . '&amp;end_time=' . $pet . '"><< Previous Quarter</a>';
    }

    if (!($ny == $myear && $nm > $mmonth)) {
        $page_links[] = '<a href="' . htmlspecialchars($url) . '&amp;start_time=' . $nst . '&amp;end_time=' . $net . '">Next Quarter >></a>';
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
        $page_links[] = '<a href="' . htmlspecialchars($url) . '&amp;start_time=' . $pst . '&amp;end_time=' . $pet . '">&lt;&lt; Previous Month</a>';
    }

    if (!($ny == $myear && $nm > $mmonth)) {
        $page_links[] = '<a href="' . htmlspecialchars($url) . '&amp;start_time=' . $nst . '&amp;end_time=' . $net . '">Next Month &gt;&gt;</a>';
    }
} elseif ('weekly' == $sale_type) {
    list($nd, $nm, $ny, $nw) = explode('-', date('d-m-Y-w', strtotime('+1 week', $start_time)));
    list($pd, $pm, $py, $pw) = explode('-', date('d-m-Y-w', strtotime('-1 week', $start_time)));
    list($ed, $em, $ey)      = explode('-', date('d-m-Y'));
    $end_time                = mktime(0, 0, 0, 3, 1, 2004);
    $pst                     = mktime(0, 0, 0, $pm, $pd + 1 - $pw, $py);
    $pet                     = mktime(0, 0, 0, idate('m', $pst), idate('m', $pst) + 7 - idate('w', $pst), idate('Y', $pst));
    $nst                     = mktime(0, 0, 0, $nm, $nd + 1 - $nw, $ny);
    $net                     = mktime(0, 0, 0, idate('m', $nst), idate('m', $nst) + 7 - idate('w', $nst), idate('Y', $nst));
    if ($pst > $end_time) {
        $page_links[] = '<a href="' . htmlspecialchars($url) . '&amp;start_time=' . $pst . '&amp;end_time=' . $pet . '"><< Previous Week</a>';
    }

    if ($nst < time()) {
        $page_links[] = '<a href="' . htmlspecialchars($url) . '&amp;start_time=' . $nst . '&amp;end_time=' . $net . '">Next Week >></a>';
    }
}
$url          = $get_vars;
$url          = '?' . http_build_query($url);
$page_links[] = '<a href="' . htmlspecialchars($url) . '&amp;print=true" target="_blank">Print</a>';
?>
<div style="text-align:center;"><?=implode("&nbsp;|&nbsp;", $page_links)?></div>
<br />
<div style="text-align:center;">Note: In some circumstances it is not easily detectable where transactions originate. These transactions are listed as "Other" Payment Type.</div>
<?php
if (!$print) {
    include_once __DIR__ . '/templates/profits_footer.tpl.php';
} else {
?>
	</body>
</html>
<?php
}
