<!-- list.tpl -->

<?php
moduleLoad('invoice'); //invoiceSearch, invoiceChunk, invoiceSort, invoiceGetValue

$sort_url = $get_vars;
unset($sort_url['order_by']);
$sort_url["order"] = (("asc" == $sort_url["order"]) ? "desc" : "asc");
$sort_url          = '?' . http_build_query($sort_url) . "&order_by=";

$select_url           = $get_vars;
$select_url['action'] = 'select';
unset($select_url['id']);
$select_url = 'index.php?' . http_build_query($select_url) . "&id=";

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['id']);
$edit_url = 'index.php?' . http_build_query($edit_url) . "&id=";

$toggle_url = $get_vars;
unset($toggle_url['id']);
$toggle_url["action"] = "toggle";
$toggle_url           = 'index.php?' . http_build_query($toggle_url) . "&id=";

$unlock_url           = $get_vars;
$unlock_url['action'] = 'unlock';
$unlock_url           = 'index.php?' . http_build_query($unlock_url) . "&id=";

$view_url           = $get_vars;
$view_url['action'] = 'view';
$view_url           = 'index.php?' . http_build_query($view_url) . "&id=";

$member_url           = $get_vars;
$member_url['action'] = 'edit';
$member_url           = '/webadmin/member/?' . http_build_query($member_url) . "&id=";
?>

<table class="table_grid">
	<tr>
		<th><a href="<?php hecho($sort_url . "id")?>">ID</a></th>
		<th><a href="<?php hecho($sort_url . "forwarder_id")?>">Provider</a></th>
		<th><a href="<?php hecho($sort_url . "name")?>">Service Name</a></th>
		<th>Open Orders</th>
		<th>Total Orders</th>
		<th>Last Order</th>
		<th><a href="<?php hecho($sort_url . "service_from")?>">Service From</a></th>
		<th><a href="<?php hecho($sort_url . "service_type")?>">Destination</a></th>
		<th><a href="<?php hecho($sort_url . "active_id")?>">Active</a></th>
		<th></th>
	</tr>
<?php
$forwarder_list    = $section_handler->getOptionList("forwarder_id");
$service_from_list = $section_handler->getOptionList("service_from");
$service_type_list = $section_handler->getOptionList("service_type");

$alt = false;
foreach ($item_list as $id) {
    $item      = $section_handler->fromDatabase($id);
    $item_lock = $section_handler->getLock($id);
    //$item_orders = ....

    $class = "";
    if ($alt = !$alt) {
        $class = ' class="alt"';
    }

    if (false !== $item_lock) {
        $class = ' class="locked"';
    }

    if ($item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
        $class = ' class="mylock"';
    }
    if (in_array($_GLOBALS["site_settings"]["site_id"], explode(",", $item["active_id"]))) {
        $class = ' class="highlight"';
    }
    $invoice_data = array(
        'open_orders'  => 'N/A',
        'total_orders' => 'N/A',
        'last_date'    => 'N/A',
    );
    if (moduleLoad('invoice')) {
        $invoice_list                 = invoiceSearch(array('ship_id' => $id));
        $invoice_by_status            = invoiceChunk($invoice_list, 'invoice_status');
        $invoice_data['open_orders']  = 0;
        $invoice_data['total_orders'] = count($invoice_list);
        foreach (array('enquiry', 'inquiry', 'proforma', 'sales', 'return') as $k) {
            if (array_key_exists($k, $invoice_by_status)) {
                $invoice_data['open_orders'] += count($invoice_by_status[$k]);
            }
        }

        if (count($invoice_list)) {
			$invoice_list = invoiceSort($invoice_list, 'ship_locate_date');
            $invoice_data['last_date'] = date('Y-m-d', invoiceGetValue(end($invoice_list), 'ship_locate_time'));
        }

    }

    $links = array();
    if ($popup_window) {
        $links[] = '<a href="' . hecho($select_url . $id, 1) . '">Select</a>';
    }

    $links[] = '<a href="' . hecho($edit_url . $id, 1) . '" target="_blank">Edit</a>';
    if (false !== $item_lock && $item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
        $links[] = '<a href="' . hecho($unlock_url . $id, 1) . '">Unlock</a>';
    }

?>
	<tr<?php echo $class ?>>
		<td><?php hecho($section_handler->formatNumber($id))?></td>
		<td><?php hecho($forwarder_list[$item["forwarder_id"]])?></td>
		<td><?php hecho($item["name"])?></td>
		<td><?=$invoice_data['open_orders']?></td>
		<td><?=$invoice_data['total_orders']?></td>
		<td><?=$invoice_data['last_date']?></td>
		<td><?php hecho($service_from_list[$item["service_from"]])?></td>
		<td><?php hecho($service_type_list[$item["service_type"]])?></td>
		<td><a href="<?php hecho($toggle_url . $id)?>"><?php echo (in_array($_GLOBALS["site_settings"]["site_id"], explode(",", $item["active_id"]))) ? "Yes" : "No" ?></a></td>
		<td style="white-space:nowrap;"><?=implode(' | ', $links)?></td>
	</tr>
<?php
}
?>
</table>
<br />
<br />
<?php
if (!$popup_window) {
?>
<center>
	<span style="font-style:italic;">Notes:</span><br />
	Rows marked in <span style="background-color:#EE90A0;">red</span> are locked by another user<br />
	Rows marked in <span style="background-color:#90A0EE;">blue</span> are locked by you<br />
</center>
<?php
}
?>

<!-- /list.tpl -->
