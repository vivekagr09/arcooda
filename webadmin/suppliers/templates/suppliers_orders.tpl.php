<?php
/**
 * @var mixed[] $supplier
 */

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/orders/functions/query_orders.inc.php';

$o_orders = supplierOrdersFromDatabase($supplier['supplier_name']);

$orders = $o_orders;
if (count($o_orders)) {
    $orders = array_chunk($o_orders, 5);
    $orders = $orders[0];
}

$status = array('approval' => 'Awaiting', 'payment' => 'Awaiting', 'goods' => 'Awaiting', 'checking' => 'Awaiting', 'cancelled' => '', 'processed' => '');
foreach ($status as $key => $value) {
    if ('Awaiting' == $value) {
        $status[$key] = $value . ' ' . ucfirst($key);
    } else {
        $status[$key] = ucfirst($key);
    }

}
?>
<div align="center">
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionOrders\');">Supplier Purchase Orders</a>')?>
</div>
<?php
if (is_array($orders)) {
?>
<div id="sectionOrders">
<table class="wide" align="center" style="width:98%">
	<tr>
			<th style="width:72pt;">HGR Company</a></th>
			<th style="width:50pt;">ID</a></th>
			<th style="width:36pt;">Date</a></th>
			<!--<th style="width:72pt;">Supplier</a></th>-->
			<th style="width:36pt;">Value</a></th>
			<th style="width:36pt;">Value (AUD)</a></th>
			<th style="width:36pt;">Value (USD)</a></th>
			<th style="width:36pt;">Order Approved</a></th>
			<th style="width:36pt;">Payment Made</a></th>
			<th style="width:36pt;">Goods Received</a></th>
			<th style="width:72pt;">Ordered By</a></th>
			<th style="width:72pt;">Last Staff</a></th>
			<th style="width:36pt;">Last Modified</a></th>
			<th style="width:36pt;">Order Status</a></th>
			<th style="width:36pt;">Days Open</a></th>
			<th style="width:36pt;">&nbsp;</th>
		</tr>
<?php
	$alt   = false;
    $count = 0;
    $yesno = array('y' => 'yes', 'n' => 'no');
    $pay   = array('1' => 'no', '2' => 'yes', '3' => 'deposit');
    foreach ($orders as $order_id) {
        $order = orderFromDatabase($order_id);
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
			<td style="text-align:center;width:72pt;"><img src="/webadmin/images/sites/site_<?=$order['buyer']?>/logo_64.png"></td>
			<td style="text-align:center;width:50pt;"><a href="/webadmin/orders/index.php?action=view&order_id=<?=$order_id?>" target="_blank">PO#000<?=$order_id?></a></td>
			<td style="text-align:center;width:36pt;"><?=date("d/m/y", $order['open_date'])?></td>
			<!--<td style="text-align:center;width:108pt;"><?=$order['supplier']?></td>-->
			<td style="text-align:center;width:72pt;">$<?=$order['value'] . ' ' . $order['currency']?></td>
			<td style="text-align:center;width:72pt;">$<?=$order['value_aud']?></td>
			<td style="text-align:center;width:72pt;">$<?=$order['value_usd']?></td>
			<td style="text-align:center;width:36pt;"><img src="/webadmin/orders/images/<?=$yesno[$order['pay_authorised']]?>.jpg"></td>
			<td style="text-align:center;width:36pt;"><img src="/webadmin/orders/images/<?=$pay[$order['pay_made']]?>.jpg"></td>
			<td style="text-align:center;width:36pt;"><img src="/webadmin/orders/images/<?=$yesno[$order['goods_received']]?>.jpg"></td>
			<td style="text-align:center;width:72pt;"><?=$order['ordered_by']?></td>
			<td style="text-align:center;width:72pt;"><?=$order['last_staff']?></td>
			<td style="text-align:center;width:36pt;"><?=date("d/m/y", strtotime($order['last_modified']))?></td>
			<td style="text-align:center;width:36pt;">
<?php
if ('processed' == $order['status'] && 'bad' == $order['condition'] && 'n' == $order['problem_solved']) {?>
				<img src="/webadmin/orders/images/problem.gif">
<?php
} else {?>
				<img src="/webadmin/orders/images/<?=$order['status']?>.gif">
<?php
}?>
			</td>
			<td style="text-align:center;width:36pt;"><?=$order['days_open']?></td>
			<td style="text-align:center;white-space:nowrap;width:36pt;">
				<a href="/webadmin/orders/index.php?action=edit&order_id=<?=$order_id?>" target="_blank">Edit</a>
			</td>
		</tr>
	<?php $count++;}?>
</table>
<?php
if (count($o_orders) > 5) {?>
<br />
<table align="center" style="width:98%">
	<tr class="alt">
		<td align="center">
			<input type="button" value="View All (<?=count($o_orders)?>)" onclick="window.open('index.php?action=view_orders&supplier_name=<?=htmlspecialchars($supplier['supplier_name'])?>')">
			<input type="button" value="Print Summary" onclick="window.open('index.php?action=view_orders&print=true&supplier_name=<?=htmlspecialchars($supplier['supplier_name'])?>')">
		</td>
	</tr>
</table>
<?php
}?>
</div>
<?php
}?>