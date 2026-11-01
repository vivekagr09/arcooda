<div align="center">
<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionPO\');">Supplier Summary</a>') ?>
</div>
<div id="sectionPO">
<?php
$yesno = array('n'=>'Not Active', 'y'=>'Active'); ?>
<?php
$company = array(''=>'Not Selected',1=>'Highway Games',2=>'Highway Entertainment',9=>'Converters.TV',16=>'Car Converters');?>
<?php
$status = array('approval'=>'Awaiting', 'payment'=>'Awaiting', 'goods'=>'Awaiting', 'checking'=>'Awaiting', 'cancelled'=>'', 'processed'=>'')?>
<?php
foreach($status as $key=>$value) {
	if($value == 'Awaiting')
		$status[$key] = $value.' '.ucfirst($key);
	else
		$status[$key] = ucfirst($key);
}
$href_code=''; $href_code_end='';
if($site_id==1)
{
	$href_code='<a href="'.$supplier['supplier_url'].'" target="_blank">';
	$href_code_end='</a>';
}
?>
<table class="wide" style="width:98%;text-align: center" align="center" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width:18%" class="left_title"><?=$href_code?>Supplier ID :<?=$href_code_end?></td>
		<td style="width:32%"><?=$supplier_id?></td>
		<td style="width:18%" class="left_title">Last Ordered :</td>
		<td style="width:32%"><?=$supplier['last_order']?></td>
	</tr>
	<tr>
		<td style="width:18%" class="left_title"><?=$href_code?>Supplier Name :<?=$href_code_end?></td>
		<td style="width:32%"><?=$supplier['supplier_name']?></td>
		<td style="width:18%" class="left_title">Total Orders :</td>
		<td style="width:32%"><?=$supplier['total_orders']?></td>
	</tr>
	<tr>
		<td style="width:18%" class="left_title">Contact Person :</td>
		<td style="width:32%"> <?=$supplier['contact']['name']?></td>
		<td style="width:18%" class="left_title">Purchase Total :</td>
		<td style="width:32%">$<?=$supplier['total_purchases'].' '.$supplier['currency']?></td>
	</tr>
	<tr>
		<td style="width:18%" class="left_title">Date Joined :</td>
		<td style="width:32%"><?=$supplier['date_added']?></td>
		<td style="width:18%" class="left_title">Outstanding Payment :</td>
		<td style="width:32%">$<?=$supplier['pay_outstanding'].' '.$supplier['currency']?></td>
	</tr>
	<tr>
		<td style="width:18%" class="left_title">Open P/O's :</td>
<?php
$open = supplierOrdersList($supplier['supplier_name'],'open'); ?>
		<td style="width:32%">
<?php
			$count = 0;
			foreach($open as $order_id) {
?>
				<a href="/webadmin/orders/index.php?action=edit&order_id=<?=$order_id?>" target="_blank">PO#000<?=$order_id?></a>
<?php
				if($count != count($open) -1)
					echo ", ";
				$count++;
			}
?>
		</td>
		<td style="width:18%" class="left_title">Status :</td>
		<td style="width:32%"><?=$yesno[$supplier['active']]?></td>
	</tr>
</table>
<br /><br />
</div>