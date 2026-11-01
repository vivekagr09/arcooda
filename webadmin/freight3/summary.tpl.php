<!-- summary.tpl -->

<?php
moduleLoad('invoice'); //invoiceSearch, invoiceChunk, invoiceGetValue

$forwarder_list = $section_handler->getOptionList("forwarder_id");
$forwarder_url = "/webadmin/forwarders/index.php?action=edit&id=";

$available_stock = 0;

$invoice_data = array(
	'open_orders' => 'N/A',
	'last_date' => 'N/A',
	'total_shipments' => 'N/A',
);
if (moduleLoad('invoice')) {
	$invoice_list = invoiceSearch(array('ship_id' => $id,'site_id' =>$_GLOBALS['site_settings']['site_id']));
	$invoice_by_status = invoiceChunk($invoice_list, 'invoice_status');
	$invoice_data['open_orders'] = 0;
	foreach (array('enquiry', 'inquiry', 'proforma', 'sales', 'return','processed') as $k)
		if (array_key_exists($k, $invoice_by_status))
			$invoice_data['open_orders'] += count($invoice_by_status[$k]);
	if (array_key_exists('processed', $invoice_by_status)){
		$row = $DB->query_first(sprintf(
            "select ship_locate_date from  %s where invoice_status='processed' AND ship_locate_date IS NOT NULL order by invoice_id desc limit 1",
            '`highwaygroup`.`invoices`'
        ), MYSQLI_ASSOC);
		$invoice_data['last_date'] = date('d/m/Y', strtotime($row['ship_locate_date']));
	}
	$invoice_data['total_shipments'] = count($invoice_list);
}

$service_type_list = $section_handler->getOptionList('service_type');
$active_list = $section_handler->getOptionList('active');
?>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSummary\');return false">Summary</a>') ?>

<div id="sectionSummary">
	<table class="table_fields">
		<tr>
			<th style="width:25%;">Freight ID :</th>
			<td style="width:25%;"><?php hecho($section_handler->formatNumber($id)) ?></td>
			<th style="width:25%;">Date Added :</th>
			<td style="width:25%;"><?php hecho(date(_("d/m/Y"), $item['created_time'])) ?></td>
		</tr>
		<tr>
			<th>Service Name :</th>
			<td><?php hecho($item["name"]) ?></td>
			<th>Open Orders :</th>
			<td><a href="/webadmin/invoice/invoice_list.php?site_id=<?= $_GLOBALS['site_settings']['site_id'] ?>&ship_id=<?= $id ?>&status=inquiry|proforma|sales|return" target="_blank"><?php hecho($invoice_data["open_orders"]) ?></a></td>
		</tr>
		<tr>
			<th>Service Type :</th>
			<td><?php hecho($service_type_list[$item['service_type']]) ?></td>
			<th>Last Shipment :</th>
			<td><?php hecho($invoice_data["last_date"]) ?></td>
		</tr>
		<tr>
			<th>Provider :</th>
			<td><a href="<?php hecho($forwarder_url . $item["forwarder_id"]) ?>" target="_blank"><?php hecho($forwarder_list[$item["forwarder_id"]]) ?></a></td>
			<th>Total Shipments :</th>
			<td><?php hecho($invoice_data["total_shipments"]) ?></td>
		</tr>
		<tr>
			<th>Provider Code :</th>
			<td><?php hecho($item["forwarder_code"]) ?></td>
			<th>Active :</th>
			<td><?php 
				$active_site_array=explode(',', $item['active_id']);

			if(in_array($_GLOBALS['site_settings']['site_id'], $active_site_array)){
				echo '<span style="background-color:#0f0;width:100%">Active</span>';
			}else{
				echo '<span style="background-color:#f00;width:100%">Inactive</span>';
			} ?></td>
		</tr>
	</table>
	<br />
	<br />
</div>

<!-- /summary.tpl -->
