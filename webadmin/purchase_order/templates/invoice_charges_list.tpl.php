<!-- invoice_charges_list.tpl -->

<?php
$charges_names = invoiceGetOptionList('charge_id');
$charge_list   = array();
foreach ($charges_list as $charge_id) {
    $charge_list[$charge_id] = $charges_names[$charge_id];
}

asort($charges_names);
?>
<script type="text/javascript">
function chargeChangeClass(new_class)
{
	toggleSection('charge_other_row',new_class==0);
}
</script>

<?= formatHTMLTitle('<a href="javascript:void(toggleSection(\'sectionDisplayed\'))">Displayed On Invoice</a>') ?>
<div id="sectionDisplayed">
	<table class="spacetable" style="width:auto;">
		<tr><td><input type="radio" name="class_id" value="1" /> American Express Processing Fee</td></tr>
		<tr class="alt"><td><input type="radio" name="class_id" value="2" /> BPAY Processing Fee</td></tr>
		<tr><td><input type="radio" name="class_id" value="3" /> PayPal Processing Fee</td></tr>
		<tr class="alt"><td><input type="radio" name="class_id" value="4" /> Visa Processing Fee</td></tr>
		<tr><td></td></tr>
		<tr class="alt"><td><input type="radio" name="class_id" value="5" /> GST</td></tr>
		<tr><td><input type="radio" name="class_id" value="6" /> Packing And Handling Fee</td></tr>
		<tr class="alt"><td><input type="radio" name="class_id" value="7" /> CIF Insurance</td></tr>
		<tr><td></td></tr>
		<tr class="alt"><td style="white-space:nowrap;">
			<input type="radio" name="class_id" value="0" />
			<select name="charge_id" style="width:auto;">
				<option value=""></option>
				<?= formatHTMLOptions($charge_list, $posted['class_id']) ?>
			</select>
		</td></tr>
	</table>
</div>

<?= formatHTMLTitle('<a href="javascript:void(toggleSection(\'sectionDisplayed\'))">Not Displayed On Invoice</a>') ?>
<div id="sectionDisplayed_hideNote" style="display:none;">
	<table class="spacetable" style="width:auto;">
		<tr><td></td></tr>
		<tr class="alt"><td style="white-space:nowrap;">
			<input type="radio" name="class_id" value="16" />
			<select name="charge_id" style="width:auto;">
				<option value=""></option>
				<?= formatHTMLOptions($charge_list, $posted['class_id']) ?>
			</select>
		</td></tr>
	</table>
</div>

<!-- /invoice_charges_list.tpl -->
