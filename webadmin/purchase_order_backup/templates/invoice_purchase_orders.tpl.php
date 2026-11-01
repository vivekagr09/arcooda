<!-- invoice_purchase_orders.tpl -->

<?php
$alt     = false;
$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" />';

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['order_id']);
$edit_url = '/webadmin/orders/index.php?' . http_build_query($edit_url);
$edit_url .= '&order_id=';

$view_url           = $get_vars;
$view_url['action'] = 'view';
unset($view_url['order_id']);
$view_url = '/webadmin/orders/index.php?' . http_build_query($view_url);
$view_url .= '&order_id=';
?>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionPurchaseOrders\');">Purchase Orders</a>') ?>
<div id="sectionPurchaseOrders">

<table class="wide" align="center">
	<tr>
		<th style="width:50pt;">ID</th>
		<th style="width:36pt;">Date</th>
		<th style="width:36pt;">Value</th>
		<th style="width:36pt;">Value (USD)</th>
		<th style="width:36pt;">Order Approved</th>
		<th style="width:36pt;">Payment Made</th>
		<th style="width:36pt;">Goods Received</th>
		<th style="width:72pt;">Ordered By</th>
		<th style="width:72pt;">Last Staff</th>
		<th style="width:36pt;">Last Modified</th>
		<th style="width:36pt;">Order Status</th>
		<th style="width:36pt;">Days Open</th>
	</tr>
<?php
$yesno = array('y' => 'yes', 'n' => 'no');
$pay   = array('1' => 'no', '2' => 'yes', '3' => 'deposit');
foreach ($invoice_porders as $porder_id) {
    $porder = orderFromDatabase($porder_id);

    $class = '';
    if ($alt = !$alt) {
        $class = ' class="alt"';
    }

    $status_image = $porder['status'] . '.gif';
    if ('processed' == $porder['status'] && 'bad' == $porder['condition'] && 'n' == $porder['problem_solved']) {
        $status_image = 'problem.gif';
    }

?>
	<tr<?= $class ?>>
		<td style="text-align:center;"><a href="<?= $view_url . $porder_id ?>" target="_blank"><?= sprintf('PO#%05d', $porder_id) ?></a></td>
		<td style="text-align:center;"><?= date("d/m/y", $porder['open_date']) ?></td>
		<td style="text-align:center;"><?= getCurrencySymbol($porder['currency']) ?><?= convertCurrency($porder['value'], $porder['currency']) ?> <?= $porder['currency'] ?></td>
		<td style="text-align:center;">$<?= convertCurrency($porder['value_usd'], 'USD') ?></td>
		<td style="text-align:center;"><img src="/webadmin/orders/images/<?= $yesno[$porder['pay_authorised']] ?>.jpg" /></td>
		<td style="text-align:center;"><img src="/webadmin/orders/images/<?= $pay[$porder['pay_made']] ?>.jpg" /></td>
		<td style="text-align:center;"><img src="/webadmin/orders/images/<?= $yesno[$porder['goods_received']] ?>.jpg" /></td>
		<td style="text-align:center;"><?= $porder['ordered_by'] ?></td>
		<td style="text-align:center;"><?= $porder['last_staff'] ?></td>
		<td style="text-align:center;"><?= date("d/m/y", strtotime($porder['last_modified'])) ?></td>
		<td style="text-align:center;"><img src="/webadmin/orders/images/<?= $status_image ?>" /></td>
		<td style="text-align:center;"><?= $porder['days_open'] ?></td>
	</tr>
<?php
}
?>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionPurchaseOrders_hideNote">
Purchase Orders can be viewed by clicking "Purchase Orders" above
<br />
</div>

<!-- /invoice_purchase_orders.tpl -->
