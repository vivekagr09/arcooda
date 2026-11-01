<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
<style>
	.wrap-url{
		 word-break: break-all;
	    overflow-wrap: break-word;
	    white-space: normal;
	    word-break: break-word;
	}
</style>
<?php
global $_GLOBALS;
$invoice_frieight_tracking_service = invoiceGetOptionList('freight_tracking');
$invoice_status =  [
    'working'   => 'Open',
    'feedback'    => 'Open',
    'nointerest'     => 'Closed',
    'payment'  => 'Awaiting Payment',
    'approval' => 'Awaiting Shipping',
    'shipping' => 'Awaiting Shipping',
    'shipped'    => 'Shipped',
    'shipped-loan'    => 'Shipped',
    'processed' => 'Processed',
    'cancelled'     => 'Closed',
    'nohelp'  => 'Closed',
    'returned' => 'Open',
    'sent_factory' => 'Open',
    'prepare_factory' => 'Open',
    'prepare_customer' => 'Open',
    'sent_customer' => 'Open',
    'return_completed' => 'Processed',
    'return_cancelled' => 'Closed',
];
$tabs = array('FreightQuotationPickup' => 'Pickup', 'FreightQuotationCustomer' => 'Customer\'s Freight Company', 'FreightQuotationHwgroup' => 'Highway Group Freight Company');

//Set default tabs if shipping_method isn't set
$default_tab = 'FreightQuotationPickup';
if (0 != $invoice['ship_supplier_id']) {
    $default_tab = 'FreightQuotationCustomer';
}

if (0 != $invoice['ship_id']) {
    $default_tab = 'FreightQuotationHwgroup';
}

//Set default tabs if shipping_method is set
if ('' != $invoice['shipping_method']) {
    $default_tab = 'FreightQuotationHwgroup';
}

if ('customer_forwarder' == $invoice['shipping_method']) {
    $default_tab = 'FreightQuotationCustomer';
}

if ('pickup' == $invoice['shipping_method']) {
    $default_tab = 'FreightQuotationPickup';
}

?>

<?=formatHTMLTitle('<a href="javascript:void(toggleSection(\'sectionFreightQuotation\'))">Freight Quotation</a>')?>
<br />
<input type="hidden" name="invoice[shipping_method]" value="<?=htmlspecialchars($invoice['shipping_method'])?>" />
<div id="sectionFreightQuotation">
<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;">Shipping Weight (kgs)</td>
			<td><input name="ship_weight" type="text" size="10" value="<?=round($invoice['ship_weight'], 2)?>" /></td>
		</tr>
		<tr name="missing_weights_row"<?=(count($missing_weights)) ? '' : ' style="display:none;"'?>>
			<td class="left_title" style="width:25%;"></td>
			<td ><div name="missing_weights_span" style="border:5px solid #9314FF;">
				The following products have undefined weights:<br />
				<?=implode("<br />\n", $missing_weights)?>
			</div></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Cubic Weight (cm<sup>3</sup>)</td>
			<td><input name="ship_cubic" type="text" size="10" value="<?=round($invoice['ship_cubic'], 2)?>" /></td>
		</tr>
	</table>
	<br/>
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;">Shipping To <span name="lblShipCountry"><?=htmlspecialchars($invoice['ship_country'])?></span></td>
			<td colspan="2">
				<input name="fumButton" type="button" value="Fumigation" onclick="javascript:shippingShowCountryNotes('fum');"<?=($row1) ? ' style="border:1px solid red;"' : ' disabled="disabled"'?> />
				<input name="cerButton" type="button" value="Certificates" onclick="javascript:shippingShowCountryNotes('cer');"<?=($row2) ? ' style="border:1px solid red;"' : ' disabled="disabled"'?> />
				<input name="shiButton" type="button" value="Special Needs" onclick="javascript:shippingShowCountryNotes('spn');"<?=($row3) ? ' style="border:1px solid red;"' : ' disabled="disabled"'?> />
				<a href="javascript:void(0);" onclick="javascript:shippingEditCountryNotes();">Add / Edit</a>
			</td>
		</tr>
	            
		<!-- <tr>
			<td class="left_title">Harmonised Code</td>
			<td><select name="harmonised_code" style="width:80%;">
				<option value="">Not Selected</option>
				<?=formatHTMLOptions($harmonised_code_nest, $invoice['harmonised_code'])?>
			</select>
			<a href="invoice_options.php?section=harmonised_code" target="_blank" class="addedit">Add / Edit</a>
			</td>
		</tr>
		<tr>
			<td class="left_title">Country Of Origin</td>
			<td><select name="origin_country" style="width:80%;">
				<option value="">Not Selected</option>
				<?=formatHTMLOptions(invoiceGetOptionList('origin_country'), $invoice['origin_country'])?>
			</select>
			<a href="invoice_options.php?section=origin_country" target="_blank" class="addedit">Add / Edit</a>
			</td>
		</tr> -->
	</table>
	<br/>
<script type="text/javascript">
//<![CDATA[
var freightQuotationTabArray = <?=json_encode(array_keys($tabs))?>;
function freightQuotationChangeShippingMethod(testValue)
{
	var keys=['pickup','customer_forwarder','other'];
	for(var i=0;i<keys.length;i++)
	{
		var elem=getElement('shipping_method_'+keys[i]);
		if(keys[i]!=testValue && elem!=null && elem.checked!=null) elem.checked=false;
		if(keys[i]==testValue && elem.checked != null) elem.checked = true;
	}
	setValue('invoice[shipping_method]',testValue);
	if(testValue=='pickup')
	{
		setValue('ship_product_name','');
		setValue('supplier_ship_product_name','');
		setValue('ship_supplier_name','Pickup');
		setValue('ship_product_notes','');
	}
}
//]]>
</script>
	<?=formatHTMLTabs($tabs, $default_tab, 'freightQuotationTabArray')?>

<?php
if (array_key_exists('FreightQuotationPickup', $tabs)) {
?>
	<div id="sectionFreightQuotationPickup" class="tabTable" style="width:95%;<?=('FreightQuotationPickup' == $default_tab) ? '' : 'display:none;'?>">
		<br />
<?php
include __DIR__ . '/tab_freight_quotation_pickup.tpl.php';
?>
		<br />
	</div>
<?php
}

if (array_key_exists('FreightQuotationCustomer', $tabs)) {
?>
	<div id="sectionFreightQuotationCustomer" class="tabTable" style="width:95%;<?=('FreightQuotationCustomer' == $default_tab) ? '' : 'display:none;'?>">
		<br />
<?php
include __DIR__ . '/tab_freight_quotation_customer.tpl.php';
?>
		<br />
	</div>
<?php
}

if (array_key_exists('FreightQuotationHwgroup', $tabs)) {
?>
	<div id="sectionFreightQuotationHwgroup" class="tabTable" style="width:95%;<?=('FreightQuotationHwgroup' == $default_tab) ? '' : 'display:none;'?>">
		<br />
<?php
include __DIR__ . '/tab_freight_quotation_hwgroup.tpl.php';
?>
		<br />
	</div>
<?php
}
?>
	<br />
	<br />
</div>
<br />

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionFreightQuotation_hideNote">Freight Quotation can be viewed by Clicking "Freight Quotation" above<br /></div>

<?php
if (!$flag_done_freight_located) {
	$fright_tracking_service = invoiceGetOptionList('freight_tracking');
	$fright_tracking_url = invoiceGetOptionList('freight_tracking_url');
	$icontracking = $DB->query_first(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE  deleted_at IS NULL AND freight_type = 'fix'",
                '`freight_tracking`',
            ));
?>
	<br />
	<div>
		<table>
			<tr>
				<td class="left_title">Freight Service</td>
				<td>
					<select name="fright_tracking_service" id="fright_tracking_service"  style="width:98%;">
						<option> Select Service</option>
						<?php
							foreach ($fright_tracking_service as $serviceId => $tracking_service) {
								$selected =$invoice['fright_tracking_service'] == $serviceId ?'selected' : '';
								if($tracking_service != 'Icon Link') 
								echo '<option value="'.$serviceId.'" data-service-url="'.$fright_tracking_url[$serviceId].'" '.$selected.'>'.$tracking_service.'</option>';
							}
						?>
				</td>
			</tr>
			<tr><td class="left_title">Tracking Number</td><td><input onchange="updateTrackingNum()" name="proforma_tracknumber" id="proforma_tracknumber" style="width:70%;" type="text" value="<?=htmlspecialchars($invoice['proforma_tracknumber'])?>" />
				<input type="button" name="generate link" value="Generate Link"  onclick="generateTrackingLink();"/>
				<input type="button" value="Reset" onclick="resetTrackingLink();" />
			</td></tr>
			<tr id="arcooda_frieght_traking_url" style="<?= $invoice['frieght_traking_url'] != '' ? '' : 'display:none;' ?>line-height: 40px;">
				<td class="left_title" ><a href="<?= $invoice['frieght_traking_url'] != '' ? $invoice['frieght_traking_url'] : '' ?>" id="view_traking_url" target="_blank">Tracking Url</a></td>
				<td>
					<span id="frieght_traking_url" class="wrap-url"><?= $invoice['frieght_traking_url'] != '' ? $invoice['frieght_traking_url'] : '' ?></span>
					<input type="hidden" name="frieght_traking_url" id="frieght_traking_url_id" value=<?= $invoice['frieght_traking_url'] ?>>
					<a href="#" onclick="copyFreightTrackingUrl('frieght_traking_url_id'); return false;"><i class="far fa-clone"></i></a>
				</td>
			</tr>
			<tr>
				<td class="left_title">Kingtrans Tracking Number</td><td><input  name="kingtrans_tracknumber" id="kingtrans_tracknumber" style="width:70%;" type="text" value="<?=htmlspecialchars($invoice['kingtrans_tracknumber'])?>" />
					<input type="button" name="generate link" value="Generate Link"  onclick="generateKingtransTrackingLink();"/>
					<input type="button" value="Reset" onclick="resetKingtransTrackingLink();" />
				</td>
			</tr>
			<tr id="arcooda_frieght_traking_url_kingiston" style="<?= $invoice['kingtrans_traking_url'] != '' ? '' : 'display:none;' ?>line-height: 40px;">
				<td class="left_title"><a href="<?= $invoice['kingtrans_traking_url'] != '' ? $invoice['kingtrans_traking_url'] : '' ?>" id="view_kingtrans_traking_url" target="_blank">Tracking Url</a></td>
				<td>
					<span id="frieght_traking_url_kingiston"><?= $invoice['kingtrans_traking_url'] != '' ? $invoice['kingtrans_traking_url'] : '' ?></span>
					<input type="hidden" name="kingtrans_traking_url" id="kingtrans_traking_url_id" value=<?= $invoice['kingtrans_traking_url_id'] ?>>
					<a href="#" onclick="copyFreightTrackingUrl('kingtrans_traking_url_id'); return false;"><i class="far fa-clone"></i></a>
				</td>
			</tr>
			<tr id="arcooda_frieght_icon_traking_url" style="<?= $invoice['frieght_icon_traking_url'] != '' ? '' : 'display:none;' ?>line-height: 40px;">
				<td class="left_title">Icon Tracking <img src="/webadmin/freight_tracking/plane.png"></td>
				<td>
					<span id="frieght_icon_traking_url"><?= $invoice['frieght_icon_traking_url'] != '' ? $invoice['frieght_icon_traking_url'] : '' ?></span>
					<input type="hidden" name="frieght_icon_traking_url" id="frieght_icon_traking_url_id" value=<?= $invoice['frieght_icon_traking_url'] ?>>
					<a href="#" onclick="copyFreightIconTrackingUrl(); return false;"><i class="far fa-clone"></i></a>
				</td>
			</tr>
			<tr>
				<td class="left_title" style="width:25%;">Freight Located</td>
<?php
$back_colour = ('y' == $invoice['ship_located']) ? '#00FF00' : '#FF0000';
?>
				<td colspan="2"><select name="ship_located" style="background-color:<?=$back_colour?>;" onchange="updateSelect(this);return false;"><?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['ship_located'])?></select></td>
			</tr>
<?php
if ('y' == $invoice['ship_located']) {
?>
			<tr style="color: red;font-weight:bold;line-height: 40px">
				<td class="left_title">Authorised</td>
<?php
$staff = 'Customer';
        if ($invoice['ship_staff_id'] > 0) {
            $staff = $all_staff_list[$invoice['ship_staff_id']];
        }

?>
				<td colspan="2"><i><?=$staff?> - <?=date('d/m/y h:i', $invoice['ship_locate_time'])?></i></td>
			</tr>
<?php
}
?>
			<tr>
				<td class="left_title" style="width:25%">Shipment Status :</td>
				<td colspan="4">
					<table class="table_grid" style="width: 100%;">
						<tr style=" <?= ($invoice['is_freight_checked'] == 'yes' && !empty($invoice['freight_received_date']))?'background-color:#91e9774a':''?>">
							<td>
								<input type="hidden"  name="frieght_icon_traking_url" value="<?= $invoice['frieght_icon_traking_url'] ?>">
								<input type="hidden"  name="invoice_action" value="<?= $invoice['invoice_action'] ?>">
								<input type="hidden"  name="is_delivery_order" value="1">
								<?= $invoice_frieight_tracking_service[$invoice['fright_tracking_service']] ?? 'No'?></td>
							<td>
						        <div style="display: flex;justify-content: center;">
								<?php if($invoice['frieght_traking_url']){ ?>
									<a href="<?= $invoice['frieght_traking_url'] ?>" target="_blank"><?= $invoice['proforma_tracknumber'] ?></a>

								<?php } else {  ?>
								<?= $invoice['proforma_tracknumber'] ?>
								<?php } 
							        if($invoice['frieght_icon_traking_url']){
							            echo '<a href="'.$invoice['frieght_icon_traking_url'].'" target="_blank"><img src ="/webadmin/freight_tracking/plane.png" style="width: 20px;padding-left: 5px;"></a>';
							        }elseif ($icontracking['url']) {
							            $trackingUrlIcon = str_replace("{tracking_id}", $invoice['proforma_tracknumber'],$icontracking['url']);
							             echo '<a href="'.$trackingUrlIcon.'" target="_blank"><img src ="/webadmin/freight_tracking/plane.png" style="width: 20px;padding-left: 5px;"></a>';
							        }else{
							            echo 'No';
							        }

							    ?>
							    </div>
							</td>
							<td><?= $invoice_status[$invoice['invoice_action']] ?></td>
							<td><?= $invoice['ship_country']?></td>
							<td><input type="date" size="10" maxlength="10" name="freight_ship_date" value="<?= $invoice['freight_ship_date'] ?>"></td>
							<td><input type="date" size="10" maxlength="10" name="freight_received_date" value="<?= $invoice['freight_received_date'] ?>"></td>
								<td>
								<?php
									$shipdate = new DateTime($invoice['freight_ship_date']);
									$receiveddate = date('Y-m-d');
									if($invoice['freight_received_date']){
										$receiveddate = $invoice['freight_received_date'];
									}
									$receiveddata = new DateTime($receiveddate);

									$diff = $shipdate->diff($receiveddata);
									if($invoice['freight_ship_date']){
										echo $diff->days;
									}
									
								?>
							</td>
							 <td><?= htmlspecialchars($all_staff_list[$invoice['handler_id']]) ?></td>
							 <td>
								<select name="is_freight_checked">
									<option value="no" <?= ($invoice['is_freight_checked'] == 'no' ? 'selected':'')?>>No</option>
									<option value="yes" <?= ($invoice['is_freight_checked'] == 'yes' ? 'selected':'')?>>Yes</option>
								</select>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br />
		<center><input type="button" value="Update Invoice"<?=('edit' == $action && !empty($invoice_lock) && $invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) ? ' onclick="javascript:if(validateInvoice()) this.form.submit();"' : ' disabled="disabled"'?> /></center>
	</div>
<?php
}
?>

	<br /><br />
</div>
<script type="text/javascript">
	function generateTrackingLink() {
	  const serviceSelect = document.getElementById('fright_tracking_service');
	  const trackingNumInput = document.getElementById('proforma_tracknumber');
	  const trackingUrlSpan = document.getElementById('frieght_traking_url');
	  const trackingUrlatag = document.getElementById('view_traking_url');
	  const trackingUrlInput = document.getElementById('frieght_traking_url_id');
	  const trackingRow = document.getElementById('arcooda_frieght_traking_url');
	  const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
	  const baseUrl = selectedOption.getAttribute('data-service-url');
	  const trackingId = trackingNumInput.value.trim();
	  const iconTrackingRow = document.getElementById('arcooda_frieght_icon_traking_url');
	  const iconTrackingUrlInput = document.getElementById('frieght_icon_traking_url_id');
	  const iconTrackingUrlSpan = document.getElementById('frieght_icon_traking_url');
	  const icontrackingurl = "<?php echo $icontracking['url']; ?>";
	  if (!baseUrl) {
	    alert('Please select a tracking service.');
	    return;
	  }

	  if (!trackingId) {
	    alert('Please enter a tracking number.');
	    return;
	  }

	  // Replace {tracking_id} placeholder in the URL
	  const finalUrl = baseUrl.replace('{tracking_id}', encodeURIComponent(trackingId));
	  const iconUrl = icontrackingurl.replace('{tracking_id}', encodeURIComponent(trackingId));
	  // Update span and hidden input
	  trackingUrlSpan.textContent = finalUrl;
	  trackingUrlatag.href = finalUrl;
	  trackingUrlInput.value = finalUrl;
	  iconTrackingUrlSpan.textContent = iconUrl;
	  iconTrackingUrlInput.value = iconUrl;
	  // Show the tracking row if hidden
	  trackingRow.style.display = 'table-row';
	  iconTrackingRow.style.display = 'table-row';
	}
	function generateKingtransTrackingLink() {
	  const trackingNumInput = document.getElementById('kingtrans_tracknumber');
	  const trackingUrlSpan = document.getElementById('frieght_traking_url_kingiston');
	  const trackingUrlatag = document.getElementById('view_kingtrans_traking_url');
	  const trackingUrlInput = document.getElementById('kingtrans_traking_url_id');
	  const trackingRow = document.getElementById('arcooda_frieght_traking_url_kingiston');
	  const baseUrl = "<?= $fright_tracking_url[8] ?>";
	  const trackingId = trackingNumInput.value.trim();
	  if (!baseUrl) {
	    alert('Please select a tracking service.');
	    return;
	  }

	  if (!trackingId) {
	    alert('Please enter a tracking number.');
	    return;
	  }

	  // Replace {tracking_id} placeholder in the URL
	  const finalUrl = baseUrl.replace('{tracking_id}', encodeURIComponent(trackingId));
	  // Update span and hidden input
	  trackingUrlSpan.textContent = finalUrl;
	  trackingUrlatag.href = finalUrl;
	  trackingUrlInput.value = finalUrl;

	  // Show the tracking row if hidden
	  trackingRow.style.display = 'table-row';
	}
	function copyFreightTrackingUrl(divid) {
	    // Prefer span text, fallback to hidden input
	    const hidden = document.getElementById(divid);
	    
	    const textToCopy = hidden?.value.trim() || '';

	    if (!textToCopy) {
	        alert('Tracking URL is empty.');
	        return;
	    }

	    navigator.clipboard.writeText(textToCopy)
	    .then(() => {
	        alert('Tracking URL copied to clipboard!');
	    })
	    .catch(err => {
	        console.error('Clipboard copy failed:', err);
	        alert('Failed to copy tracking URL.');
	    });
	}
	function copyFreightIconTrackingUrl() {
	    // Prefer span text, fallback to hidden input
	    const hidden = document.getElementById('frieght_icon_traking_url_id');
	    
	    const textToCopy = hidden?.value.trim() || '';

	    if (!textToCopy) {
	        alert('Tracking URL is empty.');
	        return;
	    }

	    navigator.clipboard.writeText(textToCopy)
	    .then(() => {
	        alert('Tracking URL copied to clipboard!');
	    })
	    .catch(err => {
	        console.error('Clipboard copy failed:', err);
	        alert('Failed to copy tracking URL.');
	    });
	}
	function resetTrackingLink() {
	    // inputs
	    const trackingNumInput = document.getElementById('proforma_tracknumber');
	    const serviceSelect = document.getElementById('fright_tracking_service');

	    // freight tracking
	    const trackingUrlSpan = document.getElementById('frieght_traking_url');
	    const trackingUrlInput = document.getElementById('frieght_traking_url_id');
	    const trackingRow = document.getElementById('arcooda_frieght_traking_url');

	    // icon tracking
	    const iconTrackingUrlSpan = document.getElementById('frieght_icon_traking_url');
	    const iconTrackingUrlInput = document.getElementById('frieght_icon_traking_url_id');
	    const iconTrackingRow = document.getElementById('arcooda_frieght_icon_traking_url');

	    // clear values
	    if (trackingNumInput) trackingNumInput.value = '';
	    if (trackingUrlSpan) trackingUrlSpan.textContent = '';
	    if (trackingUrlInput) trackingUrlInput.value = '';

	    if (iconTrackingUrlSpan) iconTrackingUrlSpan.textContent = '';
	    if (iconTrackingUrlInput) iconTrackingUrlInput.value = '';

	    // hide rows
	    if (trackingRow) trackingRow.style.display = 'none';
	    if (iconTrackingRow) iconTrackingRow.style.display = 'none';

	    // optional: reset service dropdown
	    if (serviceSelect) serviceSelect.selectedIndex = 0;
	}
	function resetKingtransTrackingLink() {
	    // inputs
	    const trackingNumInput = document.getElementById('kingtrans_tracknumber');
	    // freight tracking
	    const trackingUrlSpan = document.getElementById('frieght_traking_url_kingiston');
	    const trackingUrlInput = document.getElementById('kingtrans_traking_url_id');
	    const trackingRow = document.getElementById('arcooda_frieght_traking_url_kingiston');

	    // clear values
	    if (trackingNumInput) trackingNumInput.value = '';
	    if (trackingUrlSpan) trackingUrlSpan.textContent = '';
	    if (trackingUrlInput) trackingUrlInput.value = '';

	    // hide rows
	    if (trackingRow) trackingRow.style.display = 'none';
	}
</script>
<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
