<!-- suppliers_list.tpl -->

<?php
$alt = false;
$ord_img = '<img src="/webadmin/images/' . (($order == 'desc') ? 'desc' : 'asc') . '.png" alt="' . (($order == 'desc') ? '&darr;' : '&uarr;') . '" />';

$activate_url = $get_vars;
$activate_url['action'] = 'activate';
unset($activate_url['id']);
$activate_url = 'index.php?' . http_build_query($activate_url) . '&id=';

$deactivate_url = $get_vars;
$deactivate_url['action'] = 'deactivate';
unset($deactivate_url['id']);
$deactivate_url = 'index.php?' . http_build_query($deactivate_url) . '&id=';

$sort_url = $get_vars;
$sort_url['action'] = 'list';
$sort_url['order'] = ($order == 'desc') ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = 'index.php?' . http_build_query($sort_url) . '&order_by=';

$edit_url = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['id']);
$edit_url = 'index.php?' . http_build_query($edit_url) . '&id=';

$copy_url = $get_vars;
$copy_url['action'] = 'copy';
unset($copy_url['id']);
$copy_url = 'index.php?' . http_build_query($copy_url) . '&id=';

$view_url = $get_vars;
$view_url['action'] = 'view';
unset($view_url['id']);
$view_url = 'index.php?' . http_build_query($view_url) . '&id=';

$unlock_url = $get_vars;
$unlock_url['action'] = 'unlock';
unset($unlock_url['id']);
$unlock_url = 'index.php?' . http_build_query($unlock_url) . '&id=';

$select_url = $get_vars;
$select_url['action'] = 'select';
unset($select_url['id']);
$select_url = 'index.php?' . http_build_query($select_url) . '&id=';

$delete_url = $get_vars;
$delete_url['action'] = 'delete';
unset($delete_url['id']);
$delete_url = 'index.php?' . http_build_query($delete_url) . '&id=';

$country_list=supplierGetOptionList('country');
$active_list=supplierGetOptionList('active');
?>

<script type="text/javascript">
//<![CDATA[
function selectAll(checked)
{
	var collection=getElements('supplier_list[]');
	for(i=0;i<collection.length;i++) collection[i].checked=checked;
}
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionList\');">Suppliers</a>') ?>

<div id="sectionList">
	<table class="wide spacetable">
		<tr>
			<th></th>
			<th style="width:36pt;"><a href="<?= $sort_url ?>id">ID</a><?= ($order_by=='id')?$ord_img:'' ?></th>
			<th style="width:172pt;"><a href="<?= $sort_url ?>name">Supplier Name</a><?= ($order_by=='name')?$ord_img:'' ?></th>
			<th style="width:36pt;"><a href="<?= $sort_url ?>country">Country</a><?= ($order_by=='country')?$ord_img:'' ?></th>
			<th style="width:36pt;"><a href="<?= $sort_url ?>open">Open Orders</a><?= ($order_by=='open')?$ord_img:'' ?></th>
			<th style="width:36pt;"><a href="<?= $sort_url ?>total">Total Orders</a><?= ($order_by=='total')?$ord_img:'' ?></th>
			<th style="width:72pt;"><a href="<?= $sort_url ?>date">Last Order</a><?= ($order_by=='date')?$ord_img:'' ?></th>
			<th style="width:36pt;"><a href="<?= $sort_url ?>value">Value (USD)</a><?= ($order_by=='value')?$ord_img:'' ?></th>
			<th style="width:72pt;"><a href="<?= $sort_url ?>site">Supplier For</a><?= ($order_by=='site')?$ord_img:'' ?></th>
			<th>Status</th>
		</tr>
<?php
$alt=false;
foreach($supplier_list as $supplier_id)
{
	$supplier = supplierFromDatabase($supplier_id);
	$supplier_lock = supplierGetLock($supplier_id);
	$set_sites=array();
	foreach(explode(',',$supplier['site']) as $site) $set_sites[]='<img src="images/'.$sites[$site].'.png" />';

	$class = '';
	if ($alt = !$alt) $class = ' class="alt"';
	if ($supplier_lock !== false) $class = ' class="locked"';
	if ($supplier_lock['staff_id'] == $_GLOBALS['staff']['staff_id']) $class = ' class="mylock"';
?>
		<tr<?= ($alt=!$alt)?'':' class="alt"' ?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';">
			<td><?= ($posted['multiple']=='y')?('<input type="checkbox" name="supplier_list[]" value="'.$supplier_id.'" />'):'' ?></td>
			<td><?= supplierFormatNumber($supplier_id) ?></td>
			<td><a target="_blank" href="<?= $edit_url.$supplier_id ?>"><?= htmlspecialchars($supplier['supplier_name']) ?></a></td>
			<td><?= htmlspecialchars($supplier['country_full']) ?></td>
			<td><?= htmlspecialchars($supplier['open_orders']) ?></td>
			<td><?= htmlspecialchars($supplier['total_orders']) ?></td>
			<td><?= htmlspecialchars($supplier['last_order']) ?></td>
			<td>$<?= htmlspecialchars($supplier['order_value']) ?></td>
			<td><?= implode(" ",$set_sites) ?></td>
<?php
	$links=array();
	if ($popup_window) $links[]='<input type="button" value=" Select " onclick="' . htmlspecialchars('window.location = ' . json_encode($select_url . $supplier_id) . ';return false') . '" />';
	else
	{
		if ($supplier_lock === false || $supplier_lock['staff_id'] == $_GLOBALS['staff']['staff_id'])
		{
			$links[]='<a style="'.$styles.'" href="'.$edit_url.$supplier_id.'" target="_blank">Edit</a>';
			//$links[]='<a href="'.$delete_url.$supplier_id.'" target="_blank">Delete</a>';
		}
		else $links[]='<a style="'.$styles.'" href="'.$view_url.$supplier_id.'" target="_blank">View</a>';
		if ($supplier['stock']!='ss')
		{
			if($supplier['active']=='y') $links[]='<a style="'.$styles.'" href="'.$deactivate_url.$supplier_id.'" style="color:'.$styles.'">De-Activate</a>';
			else $links[]='<a href="'.$activate_url.$supplier_id.'" style="color:'.$styles.'">Activate</a>';
		}
		if ($supplier_lock !== false && $supplier_lock['staff_id'] == $_GLOBALS['staff']['staff_id'])
		{
			$links[]='<a style="'.$styles.'" href="'.$unlock_url.$supplier_id.'">Unlock</a>';
		}
		//$links[]='<a href="'.$copy_url.$supplier_id.'" target="_blank">New</a>';
	}
?>
			<td style="text-align:center;<?= $styles ?>white-space:nowrap;"><?= implode(' | ',$links) ?></td>
		</tr>
<?php
}
if($posted['multiple']=='y')
{
?>
		<tr<?= !$alt?' class="alt"':'' ?>><td colspan="2"><input type="checkbox" name="select_all" onclick="selectAll(this)" /><br />Select All</td><td colspan="8">&nbsp;</td></tr>
<?php
}
?>
	</table>
	<br /><br />

<?php
if (!$popup_window && false)
{
?>
	<center>
		<span style="font-style:italic;">Notes:</span><br />
		Rows highlighted in <span style="background-color:#90EEA0;;">green</span> have been modified today<br />
		Rows highlighted in <span style="background-color:#EE90A0;">red</span> are locked by another user<br />
		Rows highlighted in <span style="background-color:#90A0EE;">blue</span> are locked by you<br />
		Suppliers coloured in <span style="color:red;">red</span> are on special<br />
		Cells highlighted in <span style="background-color:#BBFFBB;">light green</span> are suppliers in stock.<br />
		Cells highlighted in <span style="background-color:#EEBBEE;">light purple</span> are consignment suppliers.<br />
		Cells highlighted in <span style="background-color:#FEDC6C;">light orange</span> are sample suppliers.<br />
	</center>
	<br /><br />
<?php
}

if ($posted['multiple'] == 'y')
{
?>
	<p style="text-align:center;">
		<input type="submit" value="Select Suppliers" />
	</p>
	<br /><br />
<?php
}
?>
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionList_hideNote">
	Supplier list can be viewed by Clicking "Suppliers" above<br />
</div>

<!-- /suppliers_list.tpl -->
