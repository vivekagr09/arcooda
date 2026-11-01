<?php
$all_staff_list = array();
if ($query = $DB->query(
    "SELECT `id`, `first_name`, `last_name` FROM `highwaygroup`.`users`"
)) {
    while ($row = $DB->fetch_assoc($query)) {
        $all_staff_list[$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
    }
    $DB->free_result($query);
}
/**
 * @var mixed[][] $product_screenshots
 */
$trackingIds = [] ;
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSS\');return false">Opened Shipments</a>', false)?>

<div id="sectionSS">


<table class="table_grid">
	<thead>
		<tr>
			<th style="width:10%"><a href="<?=htmlspecialchars($sort_url . 'open_date')?>">Date</a><?=('freight_received_date' == $order_by) ? $ord_img : ''?></th>
			<th style="width:10%"><a href="<?=htmlspecialchars($sort_url . 'invoice_id')?>">Invoice</a><?=('invoice_id' == $order_by) ? $ord_img : ''?></th>
			<th style="width:10%">Customer Name</th>
			<th style="width:10%">Invoice Value</th>
			<th style="width:10%">Courier</th>
			<th style="width:10%">Tracking Number</th>
			<th style="width:10%">Invoice Status</th>
            <th style="width:10%">Ship country</th>
			<th style="width:10%">Ship Date</th>
			<th style="width:10%">Received Date</th>
			<th style="width:10%"><a href="<?=htmlspecialchars($sort_url . 'freight_ship_date')?>">Transit Days</a><?=('freight_ship_date' == $order_by) ? $ord_img : ''?></th>
            <th style="width:5%"><i class="far fa-question-circle" style="margin-left: 10px;font-size:15px;" onmouseover="tt_in('Ticked Boxes will not be included in any reports')" onmouseout="tt_out();"></i></th>
            <th style="width:10%">Last Staff</th>
			<th style="width:10%">Checked</th>
		</tr>
	</thead>
	<tbody id="screenshots">
		
<?php
$icontracking = $DB->query_first(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE  deleted_at IS NULL AND freight_type = 'fix'",
                '`freight_tracking`',
            ));
$count = 0;
$member_url['action'] = 'edit';
$member_url = '/webadmin/member/?' . http_build_query($member_url) . '&member_id=';
foreach ($shipment_list as $shot) {
	$preview_time  = mktime(0, 0, 0);
	$edit_url = '/webadmin/invoice/?action=edit&invoice_id='.$shot['invoice_id'];
	$trackingIds[] = $shot['proforma_tracknumber'];
	$invoice = invoiceFromDatabase($shot['invoice_id']);
	$invoice_products = invoiceProductsFromDatabase($shot['invoice_id']);
    $invoice_charges  = invoiceChargesFromDatabase($shot['invoice_id']);
    $invoice_payments = invoicePaymentsFromDatabase($shot['invoice_id']);
    $products_tooltip = array();
    $notfound         = array();
    $found            = array();
    $symbol           = getCurrencySymbol($invoice_currency, 'HTML');
    $invoice_currency = $invoice['invoice_currency'];
    if (0 != $invoice['member_id']) {
        $member          = memberFromDatabase($invoice['member_id']);
        $member_requests = memberRequestsFromDatabase($invoice['member_id']);
        $member_warnings = memberWarningsFromDatabase($invoice['member_id']);
    } else {
        $member          = null;
        $member_requests = array();
        $member_warnings = array(array('comments' => 'Invoice is not linked to a member'));
    }
    foreach ($invoice_products as $product) {
        $color_code = '';
        if($product['colour_id'] > 0) {
            $color_code = productGenericFromDatabase($product['colour_id'], 'product_colour')['code3'];
        }
        $product_tr = sprintf(
            _('<tr><td style="text-align:right;">%s</td><td>x</td><td style="white-space:nowrap;">#%s: %s</td><td>@</td><td style="text-align:right;">%s</td><td>=%s</td><td style="text-align:right;">%s</td><td>%s</td></tr>'),
            $product['product_qty'],
            htmlspecialchars(invoiceProductFormatNumber($product['product_id'], $product['sub_id'])).$color_code,
            htmlspecialchars($product['product_name']),
            convertCurrency($product['product_offer'], $invoice_currency),
            $symbol,
            convertCurrency($product['product_offer'] * $product['product_qty'], $invoice_currency),
            htmlspecialchars($invoice_currency)
        );
        $products_tooltip[] = $product_tr;
        if ('n' == $product['supplier_found']) {
            $notfound[] = $product_tr;
        }

        if ('y' == $product['supplier_found'] || 's' == $product['supplier_found']) {
            $found[] = $product_tr;
        }
    }
    $invoice_gifts    = invoiceGiftsFromDatabasePrint($invoice_id);
    if(count($invoice_gifts['gift'])){
        foreach ($invoice_gifts['gift'] as $gift) {
            $gifts_tr = sprintf(
                _('<tr><td style="text-align:right;">%s</td><td>x</td><td style="white-space:nowrap;">#%d: %s</td><td>@</td><td style="text-align:right;">%s</td><td>=%s</td><td style="text-align:right;">%s</td><td>%s</td></tr>'),
                $gift['gift_qty'],
                 htmlspecialchars(invoiceProductFormatNumber($gift['product_id'], 0)),
                $gift['gift_name'].' (FREE - Gift)',
                "0.00",
                $symbol,
                "0.00",
                htmlspecialchars($invoice_currency)
            );
            $products_tooltip[] = $gifts_tr;
        }
    }
    foreach ($invoice_charges as $charge) {
        $charge_tr = sprintf(
            _('<tr><td style="text-align:right;">%s</td><td>x</td><td style="white-space:nowrap;">C%02d: %s</td><td>@</td><td style="text-align:right;">%s</td><td>=%s</td><td style="text-align:right;">%s</td><td>%s</td></tr>'),
            $charge['charge_qty'],
            $charge['charge_id'],
            htmlspecialchars($charge['charge_name']),
            convertCurrency($charge['charge_offer'], $invoice_currency),
            $symbol,
            convertCurrency($charge['charge_offer'] * $charge['charge_qty'], $invoice_currency),
            htmlspecialchars($invoice_currency)
        );
        $products_tooltip[] = $charge_tr;
    }
    $ship_offer = convertCurrency($invoice['ship_offer'], $invoice_currency);
    if ('' != $invoice['ship_product_name']) {
        $products_tooltip[] = sprintf(
            _('<tr><td style="text-align:right;">1</td><td>x</td><td style="white-space:nowrap;">F%02d%s %s</td><td>@</td> <td>%s</td><td>=%s</td><td style="text-align:right;">%s</td><td>%s</td></tr>'),
            $invoice['ship_id'],
            ('y' == $invoice['ship_insurance']) ? 'I' : 'N',
            htmlspecialchars($invoice['ship_product_name']),
            $ship_offer,
            $symbol,
            $ship_offer,
            htmlspecialchars($invoice_currency)
        );
    }
    $products_tooltip = '<table>' . implode('', $products_tooltip) . '</table>';
    $office_tooltip = 'Office Invoice<br />--------------------------<br />' . $products_tooltip;
    $customer_tooltip   = array(htmlspecialchars($invoice['bill_address_full']));
     $customer_highlight = false;

    if (!is_null($member)) {
        if ('y' === $member['spammer']) {
            $customer_tooltip[] = "\nFlagged: Spammer";
            $customer_highlight = true;
        }
        if ('y' === $member['timewaster']) {
            $customer_tooltip[] = "\nFlagged: Time Waster";
            $customer_highlight = true;
        }
        if ('y' === $member['tester']) {
            $customer_tooltip[] = "\nFlagged: Tester";
            $customer_highlight = true;
        }
    }

    if (count($member_requests)) {
        $customer_tooltip[] = '';
        $customer_tooltip[] = "Member Requests :";
        foreach ($member_requests as $request) {
            if ('' != $request['comments']) {
                $customer_tooltip[] = '-' . htmlspecialchars($request['comments']);
            }
        }

        $customer_highlight = true;
    }

    if (count($member_warnings)) {
        $customer_tooltip[] = '';
        $customer_tooltip[] = "Member Warnings :";
        foreach ($member_warnings as $warning) {
            if ('' != $warning['comments']) {
                $customer_tooltip[] = '-' . htmlspecialchars($warning['comments']);
            }
        }

        $customer_highlight = true;
    }

    $country_range = array();
    if ($invoice["member_id"] > 0) {
        $ip = getCountryInfo(memberGetValue($invoice["member_id"], "signup_IP"));
        if ("--" != $ip["a2"] && "" != $ip["a2"]) {
            $country_range[] = $ip["a2"];
        }

        $query = $DB->query("SELECT `login_ip` FROM `highwaygroup`.`new_members_login_track` WHERE `member_id` = " . intval($invoice["member_id"]));
        while ($row = $DB->fetch_assoc($query)) {
            $ip = getCountryInfo($row["login_ip"]);
            if ("--" != $ip["a2"] && "" != $ip["a2"]) {
                $country_range[] = $ip["a2"];
            }
        }
        $DB->free_result($query);
    }
    if ("" != $invoice["bill_country"]) {
        $country_range[] = $invoice["bill_country"];
    }

    if ("" != $invoice["ship_country"]) {
        $country_range[] = $invoice["ship_country"];
    }

    if ("" != $invoice["notify_country"]) {
        $country_range[] = $invoice["notify_country"];
    }

    if ("" != $invoice["forwarder_country"]) {
        $country_range[] = $invoice["forwarder_country"];
    }

    //if ($invoice["supply_country"] != "") $country_range[] = $invoice["supply_country"];
    if ("" != $invoice["def_country"]) {
        $country_range[] = $invoice["def_country"];
    }

    //if ($invoice["origin_country"] != "") $country_range[] = $invoice["origin_country"];
    $country_range = array_unique($country_range);

    if (count($country_range) > 1) {
        $customer_tooltip[] = '';
        $customer_tooltip[] = "Warning : Multiple countries detected - (" . implode(", ", $country_range) . ")";
        $customer_highlight = true;
    }

    $customer_tooltip = nl2br(implode("\n", $customer_tooltip));
?>
	<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> style="<?= ($shot['is_freight_checked'] == 'yes' && !empty($shot['freight_received_date']))?'background-color:#91e9774a':''?>">
	<td >
		<input type="hidden" name="freight_shipment[<?= $count ?>][invoice_id]" value="<?=$shot['invoice_id'] ?>" style="width:90%">
		<?= date('d/m/Y',strtotime($shot['open_date'])) ?>
		
	</td>
	<td >
		<a href="<?=htmlspecialchars($edit_url. "&invoice_time=" . $preview_time)?>" target="_blank"  onmouseover="<?=htmlspecialchars("tt_in(" . jecho($office_tooltip, 1) . ");")?>" onmouseout="tt_out();"><?=htmlspecialchars(invoiceFormatNumber($shot['invoice_id'], $shot['revision_id'], $shot['site_id']))?></a>
	</td>
	<td><a href="<?=htmlspecialchars($member_url . $invoice['member_id'])?>" target="_blank"  onmouseover="<?=htmlspecialchars('tt_in(' . jecho($customer_tooltip, 1) . ')')?>" onmouseout="tt_out()"><?= $shot['bill_name'] ?></a> </td>
	<td>$<?= $shot['invoice_offerus'] ?> USD </td>
	<td><?= $invoice_frieight_tracking_service[$shot['fright_tracking_service']] ?></td>
	<td>
        <div style="display: flex;justify-content: center;">
		<?php if($shot['frieght_traking_url']){ ?>
			<a href="<?= $shot['frieght_traking_url'] ?>" target="_blank"><?= $shot['proforma_tracknumber'] ?></a>

		<?php } else {  ?>
		<?= $shot['proforma_tracknumber'] ?>
	<?php } 
        if($shot['frieght_icon_traking_url']){
            echo '<a href="'.$shot['frieght_icon_traking_url'].'" target="_blank"><img src ="plane.png" style="width: 20px;padding-left: 5px;"></a>';
        }elseif ($icontracking['url']) {
            $trackingUrlIcon = str_replace("{tracking_id}", $shot['proforma_tracknumber'],$icontracking['url']);
             echo '<a href="'.$trackingUrlIcon.'" target="_blank"><img src ="plane.png" style="width: 20px;padding-left: 5px;"></a>';
        }else{
            
        }

    ?>
    </div>
	</td>
	<td><?= $invoice_status[$shot['invoice_action']] ?></td>
    <td><?= $shot['ship_country']?></td>
	<td><input type="date" size="10" maxlength="10" name="freight_shipment[<?= $count ?>][freight_ship_date]" value="<?= $shot['freight_ship_date'] ?>"></td>
	<td><input type="date" size="10" maxlength="10" name="freight_shipment[<?= $count ?>][freight_received_date]" value="<?= $shot['freight_received_date'] ?>"></td>
	<td>
		<?php
			$shipdate = new DateTime($shot['freight_ship_date']);
			$receiveddate = date('Y-m-d');
			if($shot['freight_received_date'] && $shot['freight_received_date'] != '0000-00-00'){
				$receiveddate = $shot['freight_received_date'];
			}
			$receiveddata = new DateTime($receiveddate);

			$diff = $shipdate->diff($receiveddata);
			if($shot['freight_ship_date']){
				echo $diff->days;
			}
			
		?>
	</td>
    <td><input type="checkbox" name="freight_shipment[<?= $count ?>][transit_time_report]" value="1" <?= $invoice['transit_time_report'] == 1 ? 'checked':''?>></td>
    <td><?= htmlspecialchars($all_staff_list[$invoice['handler_id']]) ?></td>
	<td>
		<select name="freight_shipment[<?= $count ?>][is_freight_checked]" >
			<option value="no" <?= ($shot['is_freight_checked'] == 'no' ? 'selected':'')?>>No</option>
			<option value="yes" <?= ($shot['is_freight_checked'] == 'yes' ? 'selected':'')?> <?=  ($shot['freight_received_date'] && $shot['freight_received_date'] != '0000-00-00') ?'false':'disabled'?>>Yes</option>
		</select>
	</td>
	</tr>
<?php
	$count++;
}
?>
	</tbody>
</table>
</div>

<br />
<script>
$(document).on('change','input[name*="[freight_received_date]"]', function () {

    var row = $(this).closest('tr');
    var receivedDate = $(this).val();

    if(receivedDate !== '' && receivedDate != '0000-00-00'){
        row.find('select[name*="[is_freight_checked]"] option[value="yes"]').prop('disabled', false);
    }else{
        row.find('select[name*="[is_freight_checked]"] option[value="yes"]').prop('disabled', true);
        row.find('select[name*="[is_freight_checked]"]').val('no');
    }

});
</script>
<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
