<!-- proforma_table.tpl -->

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionProformaTable\');">Proforma Invoice</a>') ?>

<div id="sectionProformaTable">
	<table class="wide">
<!--
		<tr>
			<td class="left_title" style="width:25%;">Date</td>
			<td>
				<input name="proforma_date" id="proforma_date" type="text" size="24" readonly="readonly" value="<?= (0 != $invoice['proforma_time']) ? date('Y-m-d', $invoice['proforma_time']) : '' ?>" />
				<input type="button" value=" V " onclick="gfPop.fPopCalendar(getElement('proforma_date'));" />
				<input type="button" value="Clear" onclick="setValue('proforma_date','');" />
			</td>
		</tr>
-->
		<tr>
			<td class="left_title">Terms</td>
			<td><input name="proforma_terms" style="width:98%;" type="text" value="<?php hecho($invoice['proforma_terms'])?>" /></td>
		</tr>
		<tr>
			<td class="left_title">Ship Via</td>
			<td><input name="proforma_shipvia" style="width:98%;" type="text" value="<?php hecho($invoice['proforma_shipvia'])?>" /></td>
		</tr>
		<tr>
			<td class="left_title">Payment Terms</td>
			<td><input name="proforma_payterms" style="width:98%;" type="text" value="<?php hecho($invoice['proforma_payterms'])?>" /></td>
		</tr>
<!--
		<tr>
			<td class="left_title">Client Purchase Order</td>
			<td><input name="proforma_purchaseorder" style="width:98%;" type="text" value="<?php hecho($invoice['proforma_purchaseorder'])?>" /></td>
		</tr>
		<tr>
			<td class="left_title">Tracking Number</td>
			<td><input name="proforma_tracknumber" style="width:98%;" type="text" value="<?php hecho($invoice['proforma_tracknumber'])?>" /></td>
		</tr>
-->
		<tr><td class="left_title">&nbsp;</td><td>&nbsp;</td></tr>
		<tr>
			<td class="left_title">Remarks (1)</td>
			<td><textarea name="proforma_remark1" rows="1" cols="80" style="width:98%;height:64px;"><?php hecho($invoice['proforma_remark1'])?></textarea></td>
		</tr>
		<tr>
			<td class="left_title">(2)</td>
			<td><textarea name="proforma_remark2" rows="1" cols="80" style="width:98%;height:64px;"><?php hecho($invoice['proforma_remark2'])?></textarea></td>
		</tr>
		<tr>
			<td class="left_title">(3)</td>
			<td><textarea name="proforma_remark3" rows="1" cols="80" style="width:98%;height:64px;"><?php hecho($invoice['proforma_remark3'])?></textarea></td>
		</tr>
		<tr>
			<td class="left_title">(4)</td>
			<td><textarea name="proforma_remark4" rows="1" cols="80" style="width:98%;height:64px;"><?php hecho($invoice['proforma_remark4'])?></textarea></td>
		</tr>
		<tr>
			<td class="left_title">(5)</td>
			<td><textarea name="proforma_remark5" rows="1" cols="80" style="width:98%;height:64px;"><?php hecho($invoice['proforma_remark5'])?></textarea></td>
		</tr>
		<tr><td class="left_title">&nbsp;</td><td>&nbsp;</td></tr>
		<tr>
			<td class="left_title">Account Status (1)</td>
			<td>
				<input name="account_status_text1" type="text" style="width:256px;" value="<?php hecho($invoice['account_status_text1'])?>" />
				$<input name="account_status_value1" type="text" style="width:64px;" value="<?= sprintf('%.2f', $invoice['account_status_value1']) ?>" />
			</td>
		</tr>
		<tr>
			<td class="left_title">(2)</td>
			<td>
				<input name="account_status_text2" type="text" style="width:256px;" value="<?php hecho($invoice['account_status_text2'])?>" />
				$<input name="account_status_value2" type="text" style="width:64px;" value="<?= sprintf('%.2f', $invoice['account_status_value2']) ?>" />
			</td>
		</tr>
		<tr>
			<td class="left_title">(3)</td>
			<td>
				<input name="account_status_text3" type="text" style="width:256px;" value="<?php hecho($invoice['account_status_text3'])?>" />
				$<input name="account_status_value3" type="text" style="width:64px;" value="<?= sprintf('%.2f', $invoice['account_status_value3']) ?>" />
			</td>
		</tr>
		<tr>
			<td class="left_title">(4)</td>
			<td>
				<input name="account_status_text4" type="text" style="width:256px;" value="<?php hecho($invoice['account_status_text4'])?>" />
				$<input name="account_status_value4" type="text" style="width:64px;" value="<?= sprintf('%.2f', $invoice['account_status_value4']) ?>" />
			</td>
		</tr>
		<tr>
			<td class="left_title">(5)</td>
			<td>
				<input name="account_status_text5" type="text" style="width:256px;" value="<?php hecho($invoice['account_status_text5'])?>" />
				$<input name="account_status_value5" type="text" style="width:64px;" value="<?= sprintf('%.2f', $invoice['account_status_value5']) ?>" />
			</td>
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionProformaTable_hideNote">
	Proforma Invoice can be viewed by Clicking "Proforma Invoice" above
</div>

<!-- /proforma_table.tpl -->
