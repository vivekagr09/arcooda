<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice');

include_once __DIR__ . '/header.inc.php';

$invoice_list = invoiceSearch();

$invoice_months = array();
foreach (invoiceGroupCount($invoice_list, "tax_month") as $k => $v) {
    $invoice_months[$k] = date("F Y", $k);
}

krsort($invoice_months);

echo formatHTMLTitle("Print Report");

?>
<form accept-charset="utf-8" method="post" target="_blank" action="index.php?action=tax_report">
	<table class="table_fields" style="width:50%;margin:auto;">
		<tr>
			<th style="width:50%;">Choose Month</th>
			<td><select name="tax_month">
				<?= formatHTMLOptions($invoice_months, -1) ?>
			</select></td>
		</tr>
		<tr>
			<th>Type</th>
			<td><select name="report_type">
				<option value="i">Internal</option>
				<option value="e">HK Tax Report</option>
			</select></td>
		</tr>
		<tr>
			<th>Include Payment Method</th>
			<td><select name="payment_method">
				<option value="y">Yes</option>
				<option value="n">No</option>
			</select></td>
		</tr>
	</table>
	<br />
	<br />

	<div style="text-align:center;"><input type="submit" value="Print" /></div>
	<br />
	<br />
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
