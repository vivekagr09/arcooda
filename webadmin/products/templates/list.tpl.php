<!-- list.tpl -->
<?php if(isset($_GET['return_message'])){ ?>

<style type="text/css">
	.table_grid td{
		text-align: center!important;
	}
</style>
<?php
}
moduleLoad('invoice'); //invoiceProductSearch, invoiceProductFromDatabase, invoiceSearch, invoiceChunk, invoiceGetValue

global $_GLOBALS;

$sort_url = $get_vars;
unset($sort_url['order_by']);
unset($sort_url['order']);
if ($get_vars["order"] == "") $sort_url["order"] = "desc";
$sort_url = '?' . ltrim(http_build_query($sort_url) . "&order_by=", "&");

$select_url = $get_vars;
$select_url['action'] = 'select';
unset($select_url['id']);
$select_url = 'index.php?' . ltrim(http_build_query($select_url) . "&id=", "&");

$uselect_url = $get_vars;
$uselect_url['action'] = 'select_access';
$uselect_url['product_used'] = 'true';
unset($uselect_url['id']);
$uselect_url = 'index.php?' . http_build_query($uselect_url) . '&id=';

$edit_url = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['id']);
$edit_url = 'index.php?' . ltrim(http_build_query($edit_url) . "&id=", "&");

$toggle_url = $get_vars;
$toggle_url["action"] = "toggle";
unset($toggle_url['id']);
$toggle_url = 'index.php?' . ltrim(http_build_query($toggle_url) . "&id=", "&");

$unlock_url = $get_vars;
$unlock_url['action'] = 'unlock';
$unlock_url = 'index.php?' . http_build_query($unlock_url);
$unlock_url.= '&id=';

$yesno = array('y' => 'Yes', 'n' => 'No');

$active_status = array('n' => 'No', 'y' => 'Yes', 'cs' => 'Closed - Sold', 'cc' => 'Closed - Cancelled', 'cw' => 'Closed - Supplier Withdrawn');

$ord_img = '<img src="/webadmin/images/' . (($order == 'desc') ? 'desc' : 'asc') . '.png" alt="' . (($order == 'desc') ? '&darr;' : '&uarr;') . '" />';

$active_list = productGetOptionList("active");
$supplier_list = productGetOptionList('supplier');

$product_inquiry_qty = array();
if (count($product_list)) {
	moduleLoad('invoice');
	if (function_exists('invoiceProductSearch')) {
		foreach (invoiceProductSearch([
			'product_id' => $product_list,
		]) as $v) {
			$invoice_product = invoiceProductFromDatabase($v);
			$product_inquiry_qty[$invoice_product['product_id']][$invoice_product['invoice_id']] = $invoice_product['product_qty'];
		}
	}
}
?>
<div>
	<table class="table_grid" style="width:95%;margin:auto;">
		<tr>
<?php
if ($posted['multiple'] == 'y')
{
?>
			<th>&nbsp;</th>
<?php
}
?>
			<th><a href="<?php hecho($sort_url . "id") ?>">ID</a></th>
			<th><a href="<?php hecho($sort_url . "product_name") ?>">Product Name</a></th>
			<th><a href="<?php hecho($sort_url . "active") ?>">Active</a></th>
			<th><a href="<?php hecho($sort_url . "qty") ?>">Qty</a></th>
			<th>Cost Price<br />(<?php echo $list_currency ?>)</th>
			<th>Sales Price<br />(<?php echo $list_currency ?>)</th>
			<th>&nbsp;</th>
		</tr>
<?php
$alt = false;
foreach($product_list as $product_str)
{
	$product = productFromDatabase($product_str);
	$product_lock = productGetLock($product_str);
	$days_idle = formatDaysOpen($product['time_modified']);

	$product["normal_price"] = convertCurrency($product["primary_price"], $product["primary_currency"], "USD");
	$product["normal_currency"] = "USD";
	$product["effective_price"] = convertCurrency($product["secondary_price"], $product["secondary_currency"], "USD");
	$product["effective_currency"] = "USD";
	$product["on_special"] = (bccomp($product["effective_price"], $product["normal_price"], 2) < 0);

	$class = '';
	if ($alt = !$alt) $class = 'alt';
	if ($product_lock !== false) $class = 'locked';
	if ($days_idle == 0) $class = 'highlight';
	if ($product_lock['staff'] == $_GLOBALS['staff']['user']) $class = 'mylock';
	if ('consignment' == $product['stock_type']) {
		$cost_price =convertCurrency($product['consignment_cost_price'], $product['consignment_cost_currency'], 'USD', false, $product['precision']);
		$sales_price =$product['consignment_sales_price'];
	}else{
		$cost_price = convertCurrency($product["cost_price"], $product["cost_currency"], "USD");
		$sales_price = $product["effective_price"];
	}
	if('y' == $product['special_flag'] AND ($product['special_expiry'] >= date('Y-m-d 00:00:00') OR $product['special_expiry'] == '0000-00-00 00:00:00')){ 
		$sales_price = number_format((float)convertCurrency($product["special_priceus"], $product['secondary_currency'], 'USD', false, $product['precision']), 2, '.', ''); 
	}
	$details_tooltip = '<table><tr>';
	if ($product['thumb_url'] != '') $details_tooltip.= '<td style="vertical-align:top;"><img src="' . htmlspecialchars($product['thumb_url']) . '" alt="" /></td>';
	$details_tooltip.= '<td style="vertical-align:top;"><h2>' . htmlspecialchars($product['product_name']) . '</h2><ul><li>ID: ' . hecho($product["product_number"], 1) . '</li>';
	/*
	if ($product['man_code'] != "") $details_tooltip.= '<li>Manufacturer Code: ' . htmlspecialchars($product['man_code']) . '</li>';
	if ($product['man_name'] != "") $details_tooltip.= '<li>Manufacturer Name: ' . htmlspecialchars($product['man_name']) . '</li>';
	if ($product['supplier'] != 0) $details_tooltip.= '<li>Supplier: ' . htmlspecialchars($supplier_list[$product['supplier']]) . '</li>';
	elseif ($product['supplier_id'] != 0) $details_tooltip.= '<li>Supplier: ' . htmlspecialchars($supplier_list[$product['supplier_id']]) . '</li>';
	if ($product['sup_code'] != "") $details_tooltip.= '<li>Supplier Code: ' . htmlspecialchars($product['sup_code']) . '</li>';
	if ($product['sup_name'] != "") $details_tooltip.= '<li>Supplier Name: ' . htmlspecialchars($product['sup_name']) . '</li>';
	foreach(productSuppliersFromDatabase($product_str) as $row)
	{
		if ($row['supplier'] != 0) $details_tooltip.= '<li>Supplier: ' . htmlspecialchars($supplier_list[$row['supplier']]) . '</li>';
		elseif ($row['supplier_name'] != "") $details_tooltip.= '<li>Supplier: ' . htmlspecialchars($row['supplier_name']) . '</li>';
		if ($row['sup_code'] != "") $details_tooltip.= '<li>Supplier Code: ' . htmlspecialchars($row['sup_code']) . '</li>';
		if ($row['sup_name'] != "") $details_tooltip.= '<li>Supplier Name: ' . htmlspecialchars($row['sup_name']) . '</li>';
	}
	*/
	if ($product['time_added'] != 0) $details_tooltip.= '<li>Date Added: ' . date("d/m/Y", $product['time_added']) . '</li>';
	/*
	$time_last_sold = 0;
	$last_inquiry = 0;
	$sold_1_month = 0;
	$sold_12_months = 0;
	if (array_key_exists($product_str, $product_inquiry_qty))
	{
		$inquiry_list = invoiceSearch(array("invoice_id" => array_keys($product_inquiry_qty[$product_str]), "invoice_status" => "processed"));
		if (count($inquiry_list))
		{
			$inquiry_by_time = invoiceChunk($inquiry_list, "open_time");
			$time_last_sold = max(array_keys($inquiry_by_time));
			$last_inquiry = $inquiry_by_time[$time_last_sold];
			$time_1_month = mktime(0, 0, 0, intval(date("n")) - 1);
			$time_12_months = mktime(0, 0, 0, intval(date("n")), intval(date("j")), intval(date("Y")) - 1);
			foreach(inquiryChunk($inquiry_list, "open_day") as $open_day => $inquiry_list)
			{
				if ($open_day >= $time_1_month) foreach($inquiry_list as $inquiry_id) $sold_1_month+= $product_inquiry_qty[$product_str][$inquiry_id];
				if ($open_day >= $time_12_months) foreach($inquiry_list as $inquiry_id) $sold_12_months+= $product_inquiry_qty[$product_str][$inquiry_id];
			}
		}
	}
	if ($time_last_sold == 0)
	{
		$details_tooltip.= '<li>Last Sold: Never</li>';
	}
	else
	{
		$details_tooltip.= '<li>Last Sold: ' . date("j/n/Y", $time_last_sold) . '</li>';
		$details_tooltip.= '<li>Qty Sold Past 1 Month: ' . $sold_1_month . '</li>';
		$details_tooltip.= '<li>Qty Sold Past 12 Months: ' . $sold_12_months . '</li>';
		if (is_array($last_inquiry))
		{
			$details_tooltip.= '<li>Last inquiry:';
			foreach($last_inquiry as $v) $details_tooltip.= ' ' . invoiceGetValue($v, "invoice_number");
			$details_tooltip.= '</li>';
		}
	}
	*/
	$details_tooltip.= '</ul></td></tr></table>';
	 $view_url = sprintf(
            '/buy/%s-%d',
            /*
            '/p/%s-%d.html',
            */
            urlencode(preg_replace('/\s+/', '-', $product['product_name'])),
            $product['id']
        );
	$links = array();
	if ($popup_window) $links[] = '<a href="' . hecho($select_url . $product["id"], 1) . '">Select</a>';
	$links[] = '<a href="' . hecho($edit_url . $product["id"], 1) . '" target="_blank">Edit</a>';
	if (!$popup_window) $links[] = '<a href="' . hecho($toggle_url . $product["id"], 1) . '">' . (($product["active"] != "n") ? 'De-' : '') . 'Activate</a>';
	if ($product_lock !== false && $product_lock['staff'] == $_GLOBALS['staff']['user']) $links[] = '<a href="' . hecho($unlock_url . $product["id"], 1) . '">Unlock</a>';
?>
		<tr<?php echo ($class != "") ? (' class="' . $class . '"') : "" ?>>
<?php
	if ($posted['multiple'] == 'y')
	{
?>
			<td><input type="checkbox" name="product_list[]" value="<?php hecho($product_str) ?>" /></td>
<?php
	}
?>
			<td style="background-color:<?php echo $cell_colour ?>"><?php hecho($product["product_number"]) ?></td>
			<td onmouseover="<?php hecho("tt_in(" . jecho($details_tooltip, 1) . ");") ?>" onmouseout="tt_out();" style="text-align:left;white-space:wrap;"><a target="_blank" href="<?php hecho($view_url) ?>" rel="noopener noreferrer"><?php hecho($product["product_name"]) ?></a></td>
			<td><?php hecho($active_list[$product["active"]]) ?></td>
			<td><?php echo $product['qty'] ?></td>

			<td><?php echo $cost_price ?></td>
			<td><?php echo $sales_price ?></td>
			<td><?php echo implode(' | ', $links) ?></td>
		</tr>
<?php
}
?>
	</table>
<?php
if ($posted['multiple'] == 'y')
{
?>
	<p style="text-align:center;"><input type="submit" value="Select Products" /></p>
<?php
}

if (!$popup_window)
{
?>
	<center>
		<span style="font-style:italic;">Notes:</span><br />
		Rows highlighted in <span style="background-color:#90EEA0;;">&nbsp;green&nbsp;</span> have been modified today<br />
		Rows highlighted in <span style="background-color:#EE90A0;">&nbsp;red&nbsp;</span> are locked by another user<br />
		Rows highlighted in <span style="background-color:#90A0EE;">&nbsp;blue&nbsp;</span> are locked by you<br />
		Products coloured in <span style="color:red;">red</span> are on special<br />
		Cells highlighted in <span style="background-color:#BBFFBB;">light green</span> are products in stock.<br />
		Cells highlighted in <span style="background-color:#EEBBEE;">light purple</span> are consignment products.<br />
		Cells highlighted in <span style="background-color:#FEDC6C;">light orange</span> are sample products.<br />
		Cells highlighted in <span style="background-color:#edf;">light purple</span> are superseded<br />
	</center>
<?php
}
?>
	<br /><br />
</div>



<!-- /list.tpl -->
