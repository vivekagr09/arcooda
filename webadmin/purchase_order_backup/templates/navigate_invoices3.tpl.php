<!-- navigate_invoices3.tpl -->

<?php
//Quick Nav Table
$totals_by_type   = array();
$totals_by_staff  = array();
$total_total      = 0;
$invoice_by_staff = array();

$producttypes = array(0 => 'Mixed', 1 => 'Parts', 2 => 'Machines', 3 => 'PCBs', 4 => 'Merchendise');

$query = $DB->query("SELECT `handler_id`, `producttype_id`, COUNT(`invoice_id`) AS `invoices` FROM `purchase_order` WHERE `invoice_id` IN(" . implode(', ', $invoice_list) . ") GROUP BY `handler_id`, `producttype_id`");
while ($row = $DB->fetch_assoc($query)) {
    $invoice_by_staff[$row['handler_id']][$row['producttype_id']] = $row['invoices'];
    $totals_by_staff[$row['handler_id']] += $row['invoices'];
    $totals_by_type[$row['producttype_id']] += $row['invoices'];
    $total_total += $row['invoices'];
}
$width = 100 / (count($totals_by_type) + 2);

ksort($totals_by_type);

$total_url           = $get_vars;
$total_url['action'] = 'list';
$total_url           = 'index.php?' . http_build_query($total_url);
?>
<table class="wide spacetable" align="center">
	<tr>
		<th style="height:32px;width:<?= $width ?>%;"></th>
<?php
foreach ($totals_by_type as $producttype_id => $site_total) {
    $site_url                   = $get_vars;
    $site_url['action']         = 'list';
    $site_url['producttype_id'] = $producttype_id;
    $site_url                   = 'index.php?' . http_build_query($site_url);
?>
		<th style="width:<?= $width ?>%;"><a href="<?= $site_url ?>"><?= (isset($producttypes[$producttype_id])) ? $producttypes[$producttype_id] : 'Other' ?></a></th>
<?php
}
?>
		<th style="width:<?= $width ?>%;">TOTAL</th>
	</tr>
<?php
$alt = false;
foreach ($invoice_by_staff as $handler_id => $by_site) {
    $staff_url            = $get_vars;
    $staff_url['action']  = 'list';
    $staff_url['handler'] = $handler_id;
    $staff_url            = 'index.php?' . http_build_query($staff_url);
?>
	<tr<?= ($alt = !$alt) ? '' : ' class="alt"' ?>>
		<th style="height:32px;"><a href="<?= $staff_url ?>"><?php hecho((0 == $handler_id) ? 'Nobody' : $all_staff_list[$handler_id])?></a></th>
<?php
foreach ($totals_by_type as $producttype_id => $site_total) {
        $site_url                   = $get_vars;
        $site_url['action']         = 'list';
        $site_url['producttype_id'] = $producttype_id;
        $site_url['handler']        = $handler_id;
        $site_url                   = 'index.php?' . http_build_query($site_url);
?>
		<td style="text-align:center;"><a href="<?= $site_url ?>"><?= intval($by_site[$producttype_id]) ?></a></td>
<?php
}
?>
		<td style="text-align:center;"><a href="<?= $staff_url ?>"><?= $totals_by_staff[$handler_id] ?></a></th>
	</tr>
<?php
}
?>
	<tr<?= ($alt = !$alt) ? '' : ' class="alt"' ?>>
		<th style="height:32px;">TOTAL</th>
<?php
foreach ($totals_by_type as $producttype_id => $site_total) {
    $site_url                   = $get_vars;
    $site_url['action']         = 'list';
    $site_url['producttype_id'] = $producttype_id;
    $site_url                   = 'index.php?' . http_build_query($site_url);
?>
		<td style="text-align:center;"><a href="<?= $site_url ?>"><?= $site_total ?></a></th>
<?php
}
?>
		<td style="text-align:center;"><a href="<?= $total_url ?>"><?= intval($total_total) ?></a></th>
	</tr>
</table>
<br />
<br />

<!-- /navigate_invoices3.tpl -->
