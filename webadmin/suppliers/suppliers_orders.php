<?php
return;

include_once "suppliers_common.php";
include_once $_SERVER['DOCUMENT_ROOT'].'/webadmin/orders/functions/query_orders.inc.php';
$supplier_name = $_GET['supplier_name'];
$print_me = (isset($_GET['print']) ? true : false);
$orders = supplierOrdersFromDatabase($supplier_name);

$status = array('approval'=>'Awaiting', 'payment'=>'Awaiting', 'goods'=>'Awaiting', 'checking'=>'Awaiting', 'cancelled'=>'', 'processed'=>'');
foreach($status as $key=>$value) {
	if($value == 'Awaiting')
		$status[$key] = $value.' '.ucfirst($key);
	else
		$status[$key] = ucfirst($key);
}
?>
<script language="javascript">
var windowObj = this;
function printpage() {
	if (window.print)
		window.print();
    else
		alert('To print this page, select Print Frame from the file menu.');
}

function doPrint()
{
	if (confirm("Click OK to print this document"))
		printpage();
}
</script>
<link rel="stylesheet" type="text/css" href="style.css" />
<div class="supplier_content" style="width:100%;">
<?php
if(!$print_me) { ?>
	<div align="center">
	<?php echo formatHTMLTitle('Supplier Purchase Orders') ?>
	</div>
	<?php if(is_array($orders)) { ?>
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
		$count=0;
		$yesno = array('y'=>'yes','n'=>'no');
		$pay = array('1'=>'no','2'=>'yes','3'=>'deposit');
		foreach($orders as $order_id){
			$order = orderFromDatabase($order_id);
?>
		<tr<?php echo ($alt=!$alt)?'':' class="alt"' ?>>
				<td style="text-align:center;width:72pt;"><img src="/webadmin/images/sites/site_<?php echo $order['buyer']?>/logo_64.png"></td>
				<td style="text-align:center;width:50pt;"><a href="/webadmin/orders/index.php?action=view&order_id=<?php echo $order_id?>" target="_blank">PO#000<?php echo $order_id ?></a></td>
				<td style="text-align:center;width:36pt;"><?php echo date("d/m/y",$order['open_date']) ?></td>
				<!--<td style="text-align:center;width:108pt;"><?php echo $order['supplier']?></td>-->
				<td style="text-align:center;width:72pt;">$<?php echo $order['value'].' '.$order['currency']?></td>
				<td style="text-align:center;width:72pt;">$<?php echo $order['value_aud']?></td>
				<td style="text-align:center;width:72pt;">$<?php echo $order['value_usd']?></td>
				<td style="text-align:center;width:36pt;"><img src="/webadmin/orders/images/<?php echo $yesno[$order['pay_authorised']]?>.jpg"></td>
				<td style="text-align:center;width:36pt;"><img src="/webadmin/orders/images/<?php echo $pay[$order['pay_made']]?>.jpg"></td>
				<td style="text-align:center;width:36pt;"><img src="/webadmin/orders/images/<?php echo $yesno[$order['goods_received']]?>.jpg"></td>
				<td style="text-align:center;width:72pt;"><?php echo $order['ordered_by']?></td>
				<td style="text-align:center;width:72pt;"><?php echo $order['last_staff']?></td>
				<td style="text-align:center;width:36pt;"><?php echo date("d/m/y",strtotime($order['last_modified']))?></td>
				<td style="text-align:center;width:36pt;">
<?php
if($order['status'] == 'processed' && $order['condition']=='bad' && $order['problem_solved']=='n') { ?>
					<img src="/webadmin/orders/images/problem.gif">
<?php
} else { ?>
					<img src="/webadmin/orders/images/<?php echo $order['status']?>.gif">
<?php
} ?>
				</td>
				<td style="text-align:center;width:36pt;"><?php echo $order['days_open']?></td>
				<td style="text-align:center;white-space:nowrap;width:36pt;">
					<a href="/webadmin/orders/index.php?action=edit&order_id=<?php echo $order_id?>" target="_blank">Edit</a>
				</td>
			</tr>
		<?php $count++;}?>
	</table>
<?php
}
}
else {
?>
<body style="width:100%;height:100%;margin:0px 0px 0px 0px;font-size: xx-small;font-family: Verdana, Arial, Helvetica, Sans-serif;" onLoad="doPrint()">
<div style="text-align:center;width:100%;">
<table style="width:100%;border: 1px solid #FFFFFF">
	<tr>
		<td style="text-align:left;vertical-align:top;width:auto;font-size:8pt;">
			<div style="font-size:20pt;text-align:left;">COINOP EXPRESS</div>
		</td>
		<td style="width:200px;font-size:8pt;vertical-align:top;">
			Purchase Orders Summary<br />
			Printed By <?= $_GLOBALS['staff']['name'] ?><br />
			Print Date: <?php echo date("d/m/y")?><br/>
			Supplier: <?php echo $supplier_name?>
		</td>
	</tr>
</table>
</div>
<?php
} ?>
</div>