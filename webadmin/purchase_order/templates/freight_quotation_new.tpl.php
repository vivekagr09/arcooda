<!-- freight_quotation_new.tpl -->

<?php
$tabs = array('FreightQuotationPickup' => 'Pickup', 'FreightQuotationCustomer' => 'Customer\'s Freight Company', 'FreightQuotationCompany' => 'Offered Freight Company');

//Set default tabs if shipping_method isn't set
$default_tab = 'FreightQuotationPickup';
if (0 != $invoice['ship_supplier_id']) {
    $default_tab = 'FreightQuotationCustomer';
}

if (0 != $invoice['ship_id']) {
    $default_tab = 'FreightQuotationCompany';
}

//Set default tabs if shipping_method is set
if ('' != $invoice['shipping_method']) {
    $default_tab = 'FreightQuotationCompany';
}

if ('customer_forwarder' == $invoice['shipping_method']) {
    $default_tab = 'FreightQuotationCustomer';
}

if ('pickup' == $invoice['shipping_method']) {
    $default_tab = 'FreightQuotationPickup';
}

?>

<?= formatHTMLTitle('<a href="javascript:void(toggleSection(\'sectionFreightQuotation\'))">Freight Quotation</a>') ?>
<br />
<input type="hidden" name="invoice[shipping_method]" value="<?php hecho($invoice['shipping_method'])?>" />
<div id="sectionFreightQuotation">
<script type="text/javascript">
//<![CDATA[
var freightQuotationTabArray = <?php jecho(array_keys($tabs))?>;
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
	<?= formatHTMLTabs($tabs, $default_tab, 'freightQuotationTabArray') ?>

<?php
if (array_key_exists('FreightQuotationPickup', $tabs)) {
?>
	<div id="sectionFreightQuotationPickup" class="tabTable" style="width:95%;<?= ('FreightQuotationPickup' == $default_tab) ? '' : 'display:none;' ?>">
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
	<div id="sectionFreightQuotationCustomer" class="tabTable" style="width:95%;<?= ('FreightQuotationCustomer' == $default_tab) ? '' : 'display:none;' ?>">
		<br />
<?php
include __DIR__ . '/tab_freight_quotation_customer.tpl.php';
?>
		<br />
	</div>
<?php
}

if (array_key_exists('FreightQuotationCompany', $tabs)) {
?>
	<div id="sectionFreightQuotationCompany" class="tabTable" style="width:95%;<?= ('FreightQuotationCompany' == $default_tab) ? '' : 'display:none;' ?>">
		<br />
<?php
include __DIR__ . '/tab_freight_quotation_company.tpl.php';
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
?>
	<br />
	<div>
		<table>
			<tr><td class="left_title">Tracking Number</td><td><input onchange="updateTrackingNum()" name="proforma_tracknumber" style="width:98%;" type="text" value="<?php hecho($invoice['proforma_tracknumber'])?>" /></td></tr>
			<tr>
				<td class="left_title" style="width:25%;">Freight Located</td>
<?php
$back_colour = ('y' == $invoice['ship_located']) ? '#00FF00' : '#FF0000';
?>
				<td colspan="2"><select name="ship_located" style="background-color:<?= $back_colour ?>;" onchange="updateSelect(this);return false;"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['ship_located']) ?></select></td>
			</tr>
<?php
if ('y' == $invoice['ship_located']) {
?>
			<tr style="color: red;font-weight:bold">
				<td class="left_title">Authorised</td>
<?php
$staff = 'Customer';
        if ($invoice['ship_staff_id'] > 0) {
            $staff = $all_staff_list[$invoice['ship_staff_id']];
        }

?>
				<td colspan="2"><i><?= $staff ?> - <?= date('d/m/y h:i', $invoice['ship_locate_time']) ?></i></td>
			</tr>
<?php
}
?>
		</table>
		<br />
		<center><input type="button" value="Update Invoice"<?= ($invoice_lock['staff_id'] == $staff_id || 'add' == $action) ? ' onclick="if(validateInvoice()) this.form.submit();"' : ' disabled="disabled"' ?> /></center>
	</div>
<?php
}
?>

	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionFreightQuotation_hideNote">Freight Quotation can be viewed by Clicking "Freight Quotation" above<br /></div>

<!-- /freight_quotation_new.tpl -->
