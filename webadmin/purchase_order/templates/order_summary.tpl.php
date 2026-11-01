<!-- order_summary.tpl -->

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionOSummary\');">Order Summary</a>', '<small><i><a href="#" onclick="toggleSection(\'orderTable\',true);return false;" style="color:black">Expand</a> / <a href="#" onclick="toggleSection(\'orderTable\',false);return false;" style="color:black">Collapse</a></i></small>') ?>
<div id="sectionOSummary">
<table class="wide" id="orderTable" name="orderTable" style="display:none">
	<tr>
		<td style="text-align:center" colspan="2" class="left_title">Customer Needs</td>
		<td style="text-align:center" colspan="2" class="left_title">Current Supply</td>
		<td class="left_title">&nbsp;</td>
	</tr>
	<tr>
		<td style="width:25%" class="left_title">Warranty :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($warranty_terms, $owarranty) ?></select></td>
		<td style="width:25%" class="left_title">Warranty :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($warranty_terms, $invoice['def_warranty']) ?></select></td>
		<td class="left_title"><img src="images/<?= ($invoice['def_warranty'] == $owarranty && $owarranty > 0) ? 'tick' : 'cross' ?>.gif"></td>
	</tr>
	<tr>
		<td style="width:25%" class="left_title">Condition :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($condition_terms, $ocondition) ?></select></td>
		<td style="width:25%" class="left_title">Condition :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($condition_terms, $invoice['def_condition']) ?></select></td>
		<td class="left_title"><img src="images/<?= ($invoice['def_condition'] == $ocondition && $ocondition > 0) ? 'tick' : 'cross' ?>.gif"></td>
	</tr>
	<tr>
		<td style="width:25%" class="left_title">Packing :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($packing_terms, $opacking) ?></select></td>
		<td style="width:25%" class="left_title">Packing :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($packing_terms, $invoice['def_warranty']) ?></select></td>
		<td class="left_title"><img src="images/<?= ($invoice['def_packing'] == $opacking && $opacking > 0) ? 'tick' : 'cross' ?>.gif"></td>
	</tr>
	<tr>
		<td style="width:25%" class="left_title">Voltage :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($voltage_terms, $ovoltage) ?></select></td>
		<td style="width:25%" class="left_title">Voltage :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($voltage_terms, $invoice['def_voltage']) ?></select></td>
		<td class="left_title"><img src="images/<?= ($invoice['def_voltage'] == $ovoltage && $ovoltage > 0) ? 'tick' : 'cross' ?>.gif"></td>
	</tr>
	<tr>
		<td style="width:25%" class="left_title">Plug Type :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($plugs_list, $oplug) ?></select></td>
		<td style="width:25%" class="left_title">Plug Type :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($plugs_list, $invoice['def_plug']) ?></select></td>
		<td class="left_title"><img src="images/<?= ($invoice['def_plug'] == $oplug && $oplug > 0) ? 'tick' : 'cross' ?>.gif"></td>
	</tr>
	<tr>
		<td style="width:25%" class="left_title">Terms :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($shipping_terms, $oshipping) ?></select></td>
		<td style="width:25%" class="left_title">Terms :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($shipping_terms, $invoice['def_terms']) ?></select></td>
		<td class="left_title"><img src="images/<?= ($invoice['def_terms'] == $oshipping && $oshipping > 0) ? 'tick' : 'cross' ?>.gif"></td>
	</tr>
	<tr>
		<td class="left_title">General Supply Notes<br /><span style="font-size:9px;color:red;font-weight:normal;">Not Displayed To Customer</span></td>
		<td colspan="4"><textarea name="supply_notes" rows="<?= max(5, count(explode("\n", $invoice['supply_notes']))) ?>" onkeyup="textareaAutoSize(this,1,5);"><?php hecho($invoice['supply_notes'])?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">Authorize this supply as found :</td>
		<td colspan="4"><select name="supply_located"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['supply_located']) ?></select></td>
	</tr>
<?php
if ('y' == $invoice['supply_located']) {
    $staff = 'Customer';
    if ($invoice['supply_staff_id'] > 0) {
        $staff = $all_staff_list[$invoice['supply_staff_id']];
    }

?>
	<tr style="color: red;font-weight:bold">
		<td class="left_title">Authorised By :</td>
		<td colspan="4"><i><?= $staff ?> - <?= date('d/m/y h:i', $invoice['supply_locate_time']) ?></i></td>
	</tr>
<?php
}
?>
</table>
<table class="wide">
	<tr>
		<td style="width:25%" class="left_title"></td>
		<td>
			<input type="button" value="Preview Summary
Purchase Order" onclick="actionPurchasePreview(this.form);" />
<?php
if (count($invoice_porders) == 0) {
?>
			<input type="button" value="Make Purchase
Order(s)" onclick="actionPurchaseOrder(this.form);" />
<?php
}
?>
		</td>
	</tr>
</table>
<span name="orderTable_hideNote"></span>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionOSummary_hideNote">
	The Order Summary can be viewed by Clicking "Order Summary" above<br />
</div>

<!-- /order_summary.tpl -->
