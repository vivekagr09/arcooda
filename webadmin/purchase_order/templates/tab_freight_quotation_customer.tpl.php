<!-- tab_freight_quotation_customer.tpl -->

<?php
if ('' == $invoice['own_freight_option']) {
    $invoice['own_freight_option'] = 'courier';
}

$forwarder_fields   = array('address', 'city', 'company_name', 'contact', 'country', 'email', 'fax_no', 'id', 'member_id', 'mobile_no', 'phone_no', 'post_code', 'state');
$ignore_fields      = array('id', 'member_id');
$forwarder_selected = 'new';
$forwarder_options  = array();
foreach ($member_forwarders as $value) {
    $found = true;
    foreach ($forwarder_fields as $field) {
        if (!in_array($field, $ignore_fields) && $invoice['forwarder_' . $field] != $value[$field]) {
            $found = false;
        }
    }

    if ($found) {
        $forwarder_selected = count($forwarder_options);
    }

    $temp = array();
    foreach (array('contact_name', 'company_name', 'city', 'state', 'country') as $key) {
        if (trim($value[$key]) != '') {
            $temp[] = trim($value[$key]);
        }
    }

    $forwarder_options[] = (count($temp)) ? implode(', ', $temp) : '[Untitled]';
}
if ('new' === $forwarder_selected) {
    $value = array();
    $found = false;
    foreach ($forwarder_fields as $field) {
        $value[$field] = $invoice['forwarder_' . $field];
        if ('' != $invoice['forwarder_' . $field]) {
            $found = true;
        }

    }
    if ($found) {
        $forwarder_selected = count($forwarder_options);
        $temp               = array();
        foreach (array('contact_name', 'company_name', 'city', 'state', 'country') as $key) {
            if (trim($value[$key]) != '') {
                $temp[] = trim($value[$key]);
            }
        }

        $forwarder_options[] = (count($temp)) ? implode(', ', $temp) : '[Untitled]';
        $member_forwarders[] = $value;
    }
}
?>
<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;"><input type="checkbox" name="shipping_method_customer_forwarder"<?= ('FreightQuotationCustomer' == $default_tab) ? ' checked="checked"' : '' ?> onchange="freightQuotationChangeShippingMethod('customer_forwarder')" /></td>
		<td>Use this shipping method</td>
	</tr>
</table>
<table class="wide"><tr><td class="left_title" style="width:25%;">Choose Freight Type</td><td>
	<input type="radio" name="own_freight_option" value="courier" onclick="toggleTables('courier')"<?= ('courier' == $invoice['own_freight_option']) ? ' checked="checked"' : '' ?> />Couriers<br />
	<input type="radio" name="own_freight_option" value="forwarder" onclick="toggleTables('forwarder')"<?= ('forwarder' == $invoice['own_freight_option']) ? ' checked="checked"' : '' ?> />Forwarders
</td></tr></table>
<br />
<script type="text/javascript">
<!--
var opts=Array('courier','forwarder');
function toggleTables(option)
{
	for(i=0;i<opts.length;i++) toggleSection(opts[i]+'Tab',(opts[i]==option));
}
function freightQuotationUpdateForwarder()
{
	var forwarder_fields = <?php jecho($forwarder_fields)?>;
	var member_forwarders = <?php jecho($member_forwarders)?>;
	var select_id=getValue('invoice[forwarder_select_id]');
	for(i=0;i<forwarder_fields.length;i++)
	{
		if(member_forwarders[select_id]==null || member_forwarders[select_id][forwarder_fields[i]]==null)
			setValue('invoice[forwarder_'+forwarder_fields[i]+']','');
		else
			setValue('invoice[forwarder_'+forwarder_fields[i]+']',member_forwarders[select_id][forwarder_fields[i]]);
	}
}
//-->
</script>
<table class="wide" id="courierTab"<?= ('courier' == $invoice['own_freight_option']) ? '' : ' style="display:none"' ?>>
	<tr><th></th><th>Service</th><th>Notes</th><th>Track</th><th>Insure</th><th>Tail Gate</th></tr>
	<tbody id="members_couriers">
<?php
$count = count($invoice_courier_quotes) + 1;
if (is_array($member_couriers)) {
    foreach ($member_couriers as $courier) {
        $courier_id = $courier['courier_id'];
        //$name = ($courier_id < 100) ? getFreightServiceName($courier_id) : getFreightServiceProviderName($courier_id);
        //$name.= " (" . $courier['account_num'] . ')';
        $notes    = 'Personal Account (' . $courier['account_num'] . ')';
        $selected = ($invoice['ship_id'] == $courier_id);
?>
	<tr style="text-align:center;">
		<td><input type="hidden" name="courier_id[<?= $count ?>]" value="<?= $courier_id ?>" /><input type="hidden" name="freightcourier_name[<?= $count ?>]" value="<?= $courier['courier_name'] ?>" /><input type="hidden" name="freightcourier_notes[<?= $count ?>]" value="<?= $notes ?>" /><input type="hidden" name="freightcourier_track[<?= $count ?>]" value="N/A" /><input type="hidden" name="freightcourier_insurance[<?= $count ?>]" value="N/A" /><input type="hidden" name="freightcourier_tail_gate[<?= $count ?>]" value="" /><input type="hidden" name="freightcourier_cost[<?= $count ?>]" value="0.00" /><input type="hidden" name="freightcourier_offer[<?= $count ?>]" value="0.00" /><input type="hidden" name="freightcourier_online[<?= $count ?>]" value="0.00" /><input type="hidden" name="freightcourier_costus[<?= $count ?>]" value="0.00" /><input type="hidden" name="freightcourier_costau[<?= $count ?>]" value="0.00" /><input type="hidden" name="freightcourier_offerus[<?= $count ?>]" value="0.00" /><input type="hidden" name="freightcourier_offerau[<?= $count ?>]" value="0.00" /><input type="hidden" name="freightcourier_supplier_id[<?= $count ?>]" value="<?= $courier_id ?>" /><input type="hidden" name="freightcourier_supplier_name[<?= $count ?>]" value="<?= $courier['account_num'] ?>" /><input type="checkbox" name="freightcourier_flag[<?= $count ?>]"<?= ($selected) ? ' checked="checked"' : '' ?> onclick="selectFreightCourier(<?= $count++ ?>, true);" /></td>
		<td><?= $courier['courier_name'] ?></td><td><?= $notes ?></td><td>N/A</td><td>N/A</td><td>N/A</td>
	</tr>
<?php
}
} else {
    echo '<tr><td colspan="6" class="left_title" style="text-align:center;">No Personal Member Couriers found</td></tr>';
}

?>
	</tbody>
</table>
<table class="wide" id="forwarderTab"<?= ('forwarder' == $invoice['own_freight_option']) ? '' : ' style="display:none"' ?>>
	<tr><td colspan="2" class="left_title" style="text-align:center;">
		Select A Forwarder :<br />
		<select name="invoice[forwarder_select_id]" onchange="freightQuotationUpdateForwarder()" style="text-align:center;">
			<option value="new">Create New Forwarder</option>
			<?= formatHTMLOptions($forwarder_options, $forwarder_selected) ?>
		</select>
		<input type="hidden" name="invoice[forwarder_id]" value="" />
		<input type="hidden" name="invoice[forwarder_member_id]" value="" />
	</td></tr>
	<tr>
		<td class="left_title" style="width:25%;">Contact Name</td>
		<td><input type="text" name="invoice[forwarder_contact]" value="<?php hecho($invoice['forwarder_contact'])?>" style="width:98%;" /></td>
	</tr>
	<tr>
		<td class="left_title">Company Name</td>
		<td><input type="text" name="invoice[forwarder_company_name]" value="<?php hecho($invoice['forwarder_company_name'])?>" style="width:98%;" /></td>
	</tr>
	<tr>
		<td class="left_title">Address</td>
		<td><textarea name="invoice[forwarder_address]" cols="80" rows="<?= max(2, count(explode("\n", trim($invoice['forwarder_address'])))) ?>" onkeyup="textareaAutoSize(this,1,2);"><?php hecho($invoice['forwarder_address'])?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">City</td>
		<td><input type="text" name="invoice[forwarder_city]" value="<?php hecho($invoice['forwarder_city'])?>" style="width:98%;" /></td>
	</tr>
	<tr>
		<td class="left_title">State</td>
		<td><input type="text" name="invoice[forwarder_state]" value="<?php hecho($invoice['forwarder_state'])?>" style="width:98%;" /></td>
	</tr>
	<tr>
		<td class="left_title">Post Code</td>
		<td><input type="text" name="invoice[forwarder_post_code]" value="<?php hecho($invoice['forwarder_post_code'])?>" style="width:98%;" /></td>
	</tr>
	<tr>
		<td class="left_title">Country</td>
		<td><select name="invoice[forwarder_country]"><option value=""></option><?= formatHTMLOptions(invoiceGetOptionList('forwarder_country'), $invoice['forwarder_country']) ?></select></td>
	</tr>
	<tr>
		<td class="left_title">Phone</td>
		<td><input type="text" name="invoice[forwarder_phone_no]" value="<?php hecho($invoice['forwarder_phone_no'])?>" style="width:98%;" /></td>
	</tr>
	<tr>
		<td class="left_title">Mobile</td>
		<td><input type="text" name="invoice[forwarder_mobile_no]" value="<?php hecho($invoice['forwarder_mobile_no'])?>" style="width:98%;" /></td>
	</tr>
	<tr>
		<td class="left_title">Fax</td>
		<td><input type="text" name="invoice[forwarder_fax_no]" value="<?php hecho($invoice['forwarder_fax_no'])?>" style="width:98%;" /></td>
	</tr>
	<tr>
		<td class="left_title">Email</td>
		<td><input type="text" name="invoice[forwarder_email]" value="<?php hecho($invoice['forwarder_email'])?>" style="width:98%;" /></td>
	</tr>
	<tr>
		<td class="left_title">Account Number</td>
		<td><input type="text" name="invoice[forwarder_account]" value="<?php hecho($invoice['forwarder_account'])?>" style="width:98%;" /></td>
	</tr>
</table>

<!-- /tab_freight_quotation_customer.tpl -->
