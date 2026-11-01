<!-- navigate_invoices.tpl -->

<?php
moduleLoad('invoice');

//Quick Nav Table
$site_list = invoiceGetOptionList('site_id');
$invoice_by_staff = array();
$totals_by_staff  = array();
$totals_by_site   = array();
$total_total      = 0;

$query = $DB->query("SELECT `handler_id`, `site_id`, COUNT(`invoice_id`) AS `invoices` FROM `purchase_order` WHERE `invoice_id` IN(" . implode(', ', $invoice_list) . ") GROUP BY `handler_id`, `site_id`");
while ($row = $DB->fetch_assoc($query)) {
    $invoice_by_staff[$row['handler_id']][$row['site_id']] = $row['invoices'];
    $totals_by_staff[$row['handler_id']] += $row['invoices'];
    $totals_by_site[$row['site_id']] += $row['invoices'];
    $total_total += $row['invoices'];
}
$DB->free_result($query);

$width = 100 / (count($totals_by_site) + 2);
ksort($totals_by_site);
$total_url           = $get_vars;
$total_url['action'] = 'list';
$total_url           = 'index.php?' . http_build_query($total_url);
?>

<table class="wide spacetable" align="center">
	<tr>
		<th style="height:32px;width:<?= $width ?>%;"></th>
<?php
foreach ($totals_by_site as $site_id => $site_total) {
    $site_url            = $get_vars;
    $site_url['action']  = 'list';
    $site_url['site_id'] = $site_id;
    $site_url            = 'index.php?' . http_build_query($site_url);
?>
		<th style="width:<?= $width ?>%;"><a href="<?php hecho($site_url)?>"><?= (isset($site_list[$site_id])) ? $site_list[$site_id] : 'Other' ?></a></th>
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
		<th style="height:32px;"><a href="<?php hecho($staff_url)?>"><?php hecho((0 == $handler_id) ? 'Nobody' : $all_staff_list[$handler_id])?></a></th>
<?php
foreach ($totals_by_site as $site_id => $site_total) {
        $site_url            = $get_vars;
        $site_url['action']  = 'list';
        $site_url['site_id'] = $site_id;
        $site_url['handler'] = $handler_id;
        $site_url            = 'index.php?' . http_build_query($site_url);
?>
		<td style="text-align:center;"><a href="<?php hecho($site_url)?>"><?= intval($by_site[$site_id]) ?></a></td>
<?php
}
?>
		<td style="text-align:center;"><a href="<?php hecho($staff_url)?>"><?= $totals_by_staff[$handler_id] ?></a></td>
	</tr>
<?php
}
?>
	<tr<?= ($alt = !$alt) ? '' : ' class="alt"' ?>>
		<th style="height:32px;">TOTAL</th>
<?php
foreach ($totals_by_site as $site_id => $site_total) {
    $site_url            = $get_vars;
    $site_url['action']  = 'list';
    $site_url['site_id'] = $site_id;
    $site_url            = 'index.php?' . http_build_query($site_url);
?>
		<td style="text-align:center;"><a href="<?php hecho($site_url)?>"><?= $site_total ?></a></td>
<?php
}
?>
		<td style="text-align:center;"><a href="<?php hecho($total_url)?>"><?= intval($total_total) ?></a></td>
	</tr>
</table>
<br />
<br />

<!-- /navigate_invoices.tpl -->
