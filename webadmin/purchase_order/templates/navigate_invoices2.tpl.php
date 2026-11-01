<!-- navigate_invoices2.tpl -->

<?php
//Quick Nav Table
$site_list = invoiceGetOptionList('site_id');
$invoice_by_staff = array();
// $invoice_actions  = array('working' => 'Working On Enquiry', 'feedback' => 'Waiting Customer Feedback', 'nointerest' => 'Customer Not Interested', 'payment' => 'Awaiting Payment', 'approval' => 'Awaiting Payment Approval', 'shipping' => 'Awaiting Shipping', 'shipped' => 'Goods Shipped', 'processed' => 'Processed', 'cancelled' => 'Cancelled', 'nohelp' => 'Closed - Unable to help Customer');
$invoice_actions = array(
    'working'    => 'Working on Purchase Order',
    'supplier'   => 'Awaiting Supplier',
    'order'   => 'Order Placed',
    'cancelled'  => 'Cancelled',
    'order-working'    => 'Working on Purchase Order',
    'supplier-waiting'   => 'Waiting Supplier',
    'payment'   => 'Arranging Payment',
    'goods'  => 'Waiting Goods',
    'checking-goods'  => 'Checking Goods',
    'processed'  => 'Processed',
    'awaiting-order'   => 'Awaiting Order Approval',
    'shipping'   => 'Awaiting Shipping',
);

//$invoice_by_action = array();
$totals_by_action = array();
$totals_by_site   = array();
$total_total      = 0;

// $query = $DB->query("SELECT `invoice_action`, `site_id`, COUNT(`invoice_id`) AS `invoices` FROM `invoices` WHERE `invoice_id` IN(" . implode(', ', $invoice_list) . ") GROUP BY `invoice_action`, `site_id`");
$query = $DB->query("SELECT `order_action`, `site_id`, COUNT(`order_id`) AS `invoices` FROM `purchase_order` WHERE `order_id` IN(" . implode(', ', $invoice_list) . ") GROUP BY `order_action`, `site_id`");
while ($row = $DB->fetch_assoc($query)) {
    $invoice_by_action[$row['order_action']][$row['site_id']] = $row['invoices'];
    $totals_by_action[$row['order_action']] += $row['invoices'];
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
foreach ($invoice_by_action as $invoice_action => $by_site) {
    $staff_url                   = $get_vars;
    $staff_url['action']         = 'list';
    $staff_url['order_action'] = $invoice_action;
    $staff_url                   = 'index.php?' . http_build_query($staff_url);
?>
	<tr<?= ($alt = !$alt) ? '' : ' class="alt"' ?>>
		<th style="height:32px;"><a href="<?php hecho($staff_url)?>"><?php hecho(('' == $invoice_action) ? 'Not Selected' : $invoice_actions[$invoice_action])?></a></th>
<?php
foreach ($totals_by_site as $site_id => $site_total) {
        $site_url                   = $get_vars;
        $site_url['action']         = 'list';
        $site_url['site_id']        = $site_id;
        $site_url['order_action'] = $invoice_action;
        $site_url                   = 'index.php?' . http_build_query($site_url);
?>
		<td style="text-align:center;"><a href="<?php hecho($site_url)?>"><?= intval($by_site[$site_id]) ?></a></td>
<?php
}
?>
		<td style="text-align:center;"><a href="<?php hecho($staff_url)?>"><?= $totals_by_action[$invoice_action] ?></a></td>
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

<!-- /navigate_invoices2.tpl -->
