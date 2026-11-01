<?php
include_once ('common.inc.php');
include_once ('header.tpl.php');

$order = $posted['order'];
if ($order != 'asc') $order = 'desc';
$page_id = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

//Build a search criteria array
$criteria = $_GLOBALS['criteria_options']['freight'];
foreach(freightGetOptionList('all_fields') as $k => $v)
	if ($posted[$k] != '') $criteria[$k] = explode('|', $posted[$k]);
	elseif(array_key_exists($k, $posted)) $criteria[$k] = $posted[$k];
unset($criteria['id']);
if ($_GLOBALS['site_settings']['site_id'] != 3) $criteria['site_id'] = $_GLOBALS['site_settings']['site_id'];
if ($posted['search_for'] != '') $criteria['any_text'] = explode('|', $posted['search_for']);
if ($posted['subset_list'] != '') $criteria['id'] = explode('|', $posted['subset_list']);

$search_for = $posted['search_for'];
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionSearch'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Search</b></a>
	<br /><br />
	<div id="sectionSearch" class="child"<?= ($search_for=='')?' style="display:none;"':'' ?>>
<?php
include 'search.tpl.php';
?>
	</div>
</div>
<?php
//Find and pagenate the items
$item_list = freightSearch($criteria);
$item_list = freightSort($item_list,$order_by);
if($order == 'desc') $item_list = array_reverse($item_list);

$items_per_page = 50;
if ($_GLOBALS['staff']['per_page']>0) $items_per_page = $_GLOBALS['staff']['per_page'];
$count_total = count($item_list);

$item_list = array_chunk($item_list,$items_per_page);
$count_pages=count($item_list);
$page_id=max(0,min($count_pages-1,$page_id));
$item_list=$item_list[$page_id];
$count_display=count($item_list);




$search_url = $get_vars;
$search_url['action'] = 'list';
unset($search_url['search_for']);
$search_url = 'index.php?' . http_build_query($search_url);
?>
<table style="width:100%;border: 0px" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td style="width:25%;background-color:#F0F4F9">
			<img src="/webadmin/images/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All">
			<a href="/webadmin/express/index.php?action=list" target="_blank"><b>Express Couriers - Prepaid</b></a>
		</td>
	</tr>
</table>
<table style="width:100%;border: 0px" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td style="width:25%;background-color:#F0F4F9">
			<img src="/webadmin/images/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All">
			<a href="/webadmin/account/index.php?action=list" target="_blank"><b>Express Couriers - Account</b></a>
		</td>
	</tr>
</table>
<table style="width:100%;border: 0px" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td style="width:25%;background-color:#F0F4F9">
			<img src="/webadmin/images/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All">
			<a href="/webadmin/truck/index.php?action=list" target="_blank"><b>Truck Couriers</b></a>
		</td>
	</tr>
</table>
<table style="width:100%;border: 0px" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td style="width:25%;background-color:#F0F4F9">
			<img src="/webadmin/images/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All">
			<a href="/webadmin/freight_forwarders/index.php?action=list&type=sea|air|sea_air" target="_blank"><b>Sea/Air Freight Providers</b></a>
		</td>
	</tr>
</table>
<table style="width:100%;border: 0px" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td style="width:25%;background-color:#F0F4F9">
			<img src="/webadmin/images/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All">
			<a href="/webadmin/personal_courier/" target="_blank"><b>Personal Couriers</b></a>
		</td>
	</tr>
</table>
<center><?= formatHTMLTitle('All Freight Services') ?></center>

<?php
$product_list = freightSearch($search_for, $extra, $flag, $site_id);
$product_list = freightSort($product_list, $order_by, $site_id);
//print_array($product_list);
if ($order == 'desc') $product_list = array_reverse($product_list);
$items_per_page = 50;
$count_total = count($product_list);
$product_list = array_chunk($product_list, $items_per_page);
$count_pages = count($product_list);
$page = max(0, min($count_pages - 1, $page));
$product_list = $product_list[$page];
$count_display = count($product_list);
if ($count_total > 0)
{
	$sort_url = $get_vars;
	$sort_url['site_id'] = $site_id;
	unset($sort_url['order_by']);
	unset($sort_url['order']);
	$sort_url = '?' . http_build_query($sort_url);
	$select_url = $get_vars;
	$select_url['action'] = ($posted['return_message'] == 'test') ? 'select' : 'select_access';
	$select_url['site_id'] = $site_id;
	unset($select_url['truck_id']);
	$select_url = 'index.php?' . http_build_query($select_url);
	$edit_url = $get_vars;
	$edit_url['action'] = 'edit';
	unset($edit_url['truck_id']);
	$edit_url = 'index.php?' . http_build_query($edit_url);
	$page_url = $get_vars;
	$page_url['site_id'] = $site_id;
	unset($page_url['page']);
	$page_url = '?' . http_build_query($page_url);
	$page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $items_per_page, 'style="color:blue"');
	$ex = '';
	$status = array('y' => 'Active', 'n' => 'Inactive');
	$folders = array('pp' => 'express', 'truck' => 'truck', 'acc' => 'account', 'air' => 'freight_forwarders', 'sea' => 'freight_forwarders', 'sea_air' => 'freight_forwarders', 'personal' => 'personal_courier');
	$courier_type = array('pp' => 'Express - Prepaid', 'truck' => 'Truck Courier', 'acc' => 'Express - Account', 'air' => 'Air Freight Provider', 'sea' => 'Sea Freight Provider', 'sea_air' => 'Air/Sea Freight Provider', 'personal' => 'Personal Courier');
	$fields_pp = array('id', 'name', 'supplier', 'country', 'qty', 'cost_price', 'sell_price', 'last_sold', 'status', 'type');
	$fields_ac = $fields_tr = $fields_air = array('id', 'name', 'supplier', 'country', 'open', 'total', 'value', 'last_order', 'status', 'type');
	$fields_pr = array('id', 'name', 'status', 'type');
	$field_to_use = array('pp' => $fields_pp, 'truck' => $fields_tr, 'acc' => $fields_ac, 'air' => $fields_air, 'sea' => $fields_air, 'sea_air' => $fields_air, 'personal' => $fields_pr);
?>
	<br />
	<table class="wide" style="width: 98%;border: 1px solid #CCCCCC;<?= $ex
?>" align="center" cellspacing=0 cellpadding=2 border=1>
		<tr>
			<th><a href="<?= $sort_url
?>&order_by=product_id&order=<?= ($order_by == 'product_id' && $order == 'asc') ? 'desc' : 'asc' ?>">ID</a></th>
			<th><a href="<?= $sort_url ?>&order_by=name&order=<?= ($order_by == 'name' && $order == 'asc') ? 'desc' : 'asc' ?>">Product Name</a></th>
			<th><a href="<?= $sort_url ?>&order_by=supplier&order=<?= ($order_by == 'supplier' && $order == 'asc') ? 'desc' : 'asc' ?>">Supplier</a></th>
			<th><a href="<?= $sort_url ?>&order_by=country&order=<?= ($order_by == 'country' && $order == 'asc') ? 'desc' : 'asc' ?>">Country</a></th>
			<th><a href="<?= $sort_url ?>&order_by=qty&order=<?= ($order_by == 'qty' && $order == 'asc') ? 'desc' : 'asc' ?>">Qty</a></th>
			<th><a href="<?= $sort_url ?>&order_by=value&order=<?= ($order_by == 'value' && $order == 'asc') ? 'desc' : 'asc' ?>">Cost Price (AUD)</a></th>
			<th><a href="<?= $sort_url ?>&order_by=value&order=<?= ($order_by == 'value' && $order == 'asc') ? 'desc' : 'asc' ?>">Sell Price (AUD)</a></th>
			<th><a href="<?= $sort_url ?>&order_by=site&order=<?= ($order_by == 'site' && $order == 'asc') ? 'desc' : 'asc' ?>">Last Sold</a></th>
			<th><a href="<?= $sort_url ?>&order_by=open&order=<?= ($order_by == 'open' && $order == 'asc') ? 'desc' : 'asc' ?>">Open Orders</a></th>
			<th><a href="<?= $sort_url ?>&order_by=total&order=<?= ($order_by == 'total' && $order == 'asc') ? 'desc' : 'asc' ?>">Total Orders</a></th>
			<th><a href="<?= $sort_url ?>&order_by=date&order=<?= ($order_by == 'date' && $order == 'asc') ? 'desc' : 'asc' ?>">Last Order</a></th>
			<th><a href="<?= $sort_url ?>&order_by=value&order=<?= ($order_by == 'value' && $order == 'asc') ? 'desc' : 'asc' ?>">Value (USD)</a></th>
			<th><a href="<?= $sort_url ?>&order_by=active&order=<?= ($order_by == 'active' && $order == 'asc') ? 'desc' : 'asc' ?>">Status</th>
			<th><a href="<?= $sort_url ?>&order_by=type&order=<?= ($order_by == 'type' && $order == 'asc') ? 'desc' : 'asc' ?>">Courier Type</th>
			<th>&nbsp;</th>
		</tr>
<?php
	$alt = false;
	foreach($product_list as $product_id)
	{
		$product = freightServiceFromDatabase($product_id);
		$date = ($product['last_unix'] > 0) ? date('d/m/y', $product['last_unix']) : 'Never';
?>
		<tr<?= ($alt = !$alt) ? '' : ' class="alt"' ?> style="text-align:center">
			<td>F0<?= $product_id ?></td>
			<td><?= $product['name'] ?></td>
			<td><?= (in_array('supplier', $field_to_use[$product['type']])) ? getFreightForwarderName($product['supplier']) : 'N/A' ?></td>
			<td><?= 'Australia' ?></td>
			<td><?= (in_array('qty', $field_to_use[$product['type']])) ? $product['qty'] : 'N/A' ?></td>
			<td><?= (in_array('cost_price', $field_to_use[$product['type']])) ? '$' . $product['cost_price'] : 'N/A' ?></td>
			<td><?= (in_array('sell_price', $field_to_use[$product['type']])) ? '$' . $product['sell_price'] : 'N/A' ?></td>
			<td><?= (in_array('last_sold', $field_to_use[$product['type']])) ? $date : 'N/A' ?></td>
			<td><?= (in_array('open', $field_to_use[$product['type']])) ? '0' : 'N/A' ?></td>
			<td><?= (in_array('total', $field_to_use[$product['type']])) ? '0' : 'N/A' ?></td>
			<td><?= (in_array('last_order', $field_to_use[$product['type']])) ? 'Never' : 'N/A' ?></td>
			<td><?= (in_array('value', $field_to_use[$product['type']])) ? '$0.00' : 'N/A' ?></td>
			<td><?= $status[$product['active']] ?></td>
			<td><?= $courier_type[$product['type']] ?></td>
<?php
		$links = array();
		$str = ($product['type'] == 'truck') ? 'truck_' : 'product_';
		if ($product['type'] == 'sea' || $product['type'] == 'air' || $product['type'] == 'sea_air' || $product['type'] == 'personal') $str = '';
		$links[] = ($popup_window) ? '<a href="' . $select_url . '&' . $str . '_id=' . $product_id . '&' . $str . '_name=' . $product['name'] . '">Select</a>' : '<a href="/webadmin/' . $folders[$product['type']] . '/index.php?action=edit&' . $str . 'id=' . $product_id . '" target="_blank">Edit</a>';
		/*
		if(!$popup_window){
		$ac = 'activate';
		if($product['active']=='y')
		$ac = 'deactivate';
		$links[] = '<a href="/webadmin/'.$folders[$product['type']].'/index.php?action='.$ac.'&'.$str.'_id='.$product_id.'">'.ucfirst($ac).'</a>';
		}
		*/
?>
			<td><?= implode(' | ', $links) ?></td>
		</tr>
<?php
	}
?>
	</table><br />
<?php
	freightAlphaList($order_by, $order, $alpha_value, $page_url, $count_pages, $page); ?>
	<br /><br />
<?php
}
else
{
?>
	No results were found
<?php
}
?>
</div>
<?php
include_once ('footer.tpl.php');
?>