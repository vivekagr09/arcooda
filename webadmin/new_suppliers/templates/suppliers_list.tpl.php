<!-- suppliers_list.tpl -->

<?php
$alt=false;
$ord_img='<img src="/webadmin/images/'.(($order=='desc')?'desc':'asc').'.png" />';

$sort_url=$get_vars;
$sort_url['action']='list';
$sort_url['order']=($order=='desc')?'asc':'desc';
unset($sort_url['order_by']);
$sort_url='index.php?'.http_build_query($sort_url);
$sort_url.='&order_by=';

$edit_url=$get_vars;
$edit_url['action']='edit';
unset($edit_url['id']);
$edit_url='index.php?'.http_build_query($edit_url);
$edit_url.='&id=';

$view_url=$get_vars;
$view_url['action']='view';
unset($view_url['id']);
$view_url='index.php?'.http_build_query($view_url);
$view_url.='&id=';

$unlock_url=$get_vars;
$unlock_url['action']='unlock';
unset($unlock_url['id']);
$unlock_url='index.php?'.http_build_query($unlock_url);
$unlock_url.='&id=';

$select_url=$get_vars;
$select_url['action']='select';
unset($select_url['id']);
$select_url='index.php?'.http_build_query($select_url);
$select_url.='&id=';

$country_list=supplierGetOptionList('country');
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:void(toggleSection(\'sectionSupplierList\'))">Suppliers</a>') ?>

<div id="sectionSupplierList">
	<table class="wide spacetable">
		<tr>
<?php
if($posted['multiple']=='y')
{
?>
			<th></th>
<?php
}
?>
			<th><a href="<?= $sort_url ?>id">Supplier #</a><?= ($order_by=='id')?$ord_img:'' ?></th>
			<th><a href="<?= $sort_url ?>supplier">Supplier Name</a><?= ($order_by=='supplier')?$ord_img:'' ?></th>
			<th><a href="<?= $sort_url ?>country">Country</a><?= ($order_by=='country')?$ord_img:'' ?></th>
			<th>Open Orders</th>
			<th>Total Orders</th>
			<th>Last Order</th>
			<th>Value (USD)</th>
			<!-- <th><a href="<?= $sort_url ?>site_id" >Websites</a></th> -->
			<th>&nbsp;</th>
		</tr>

<?php
foreach($supplier_list as $id)
{
	$supplier=supplierFromDatabase($id);
	$supplier_lock=supplierGetLock($id);

	$class='';
	if($alt=!$alt)		$class=' class="alt"';
	if($supplier_lock!==false)	$class=' class="locked"';
	if($supplier_lock['staff_id']==$_GLOBALS['staff']['id'])		$class=' class="mylock"';

	$purchase_list=array();
	$purchase_chunks=array();
	$purchase_total='N/A';
	//$purchase_list=purchaseorderSearch('id'=>$id);
	//$purchase_chunks=purchaseorderChunk($purchase_list,'status');
	//$purchase_total=convertCurrency(purchaseorderSumValue($purchase_list,'cost_totalus'),'USD');
?>
		<tr<?= $class ?>>
<?php
	if($posted['multiple']=='y')
	{
?>
			<td><input type="checkbox" name="supplier_list[]" value="<?= $id ?>" /></td>
<?php
	}
?>
			<td><?= supplierFormatNumber($id) ?></td>
			<td><a href="<?= $edit_url.$id ?>"><?= htmlspecialchars($supplier['supplier']) ?></a></td>
			<td><?= htmlspecialchars($country_list[$supplier['country']]) ?></td>
			<td><?= count($purchase_list['open']) ?></td>
			<td><?= count($purchase_list['open'])+count($purchase_list['processed']) ?></td>
			<td><?= (count($purchase_list))?date('d/m/Y',purchaseorderGetValue(end($purchase_list),'open_time')):'N/A' ?></td>
			<td>$<?= $purchase_total ?></td>
			<!-- <td>
<?php
		foreach(explode(',',$supplier['site_id']) as $site_id) if($site_id!='')
			echo '<img src="/webadmin/images/sites/site_'.$site_id.'/logo_16.png" />&nbsp;';
?>
			</td> -->
			<td style="text-align:center;">
<?php
	if($popup_window)
	{
?>
				<input type="button" value=" Select " onclick="window.location='<?= $select_url.$id ?>'" />
<?php
	}
	else
	{
		if($supplier_lock===false || $supplier_lock['staff_id']==$_GLOBALS['staff']['id'])
		{
?>
				<a href="<?= $edit_url.$id ?>" target="_blank">Edit</a>
<?php
		}
		else
		{
?>
				<a href="<?= $view_url.$id ?>" target="_blank">View</a>
<?php
		}
		if($supplier_lock!==false && $supplier_lock['staff_id']==$_GLOBALS['staff']['id'])
		{
?>
				<a href="<?= $unlock_url.$id ?>">Unlock</a>
<?php
		}
	}
?>
			</td>
		</tr>
<?php
}
?>
	</table>
	<br /><br />
</div>

<!-- /suppliers_list.tpl -->
