<!-- list_wanted_printable.tpl -->

<?php
moduleLoad('invoice');

$invoice_cache = array();
$site_list = invoiceGetOptionList('site_id');
$ship_country_list = invoiceGetOptionList('ship_country');
?>

<style type="text/css">
/*<![CDATA[*/
td{white-space:nowrap}
/*]]>*/
</style>

<script type="text/javascript">
//<![CDATA[
$(function() {
	$("table").each(function() {
		var table=this;
		var headers=$("th",this);
		for(var i=0;i<headers.length;i++)
		{
			headers.eq(i).wrapInner('<a href="#" />').attr("column_id",i).click(function() {
				var column_id=$(this).attr("column_id");
				var rows=[];
				var vals=[];
				var temp=$("tr",table);
				for(var i=1;i<temp.length;i++)
				{
					rows.push(temp.eq(i));
					vals.push($("td",temp[i]).eq(column_id).text());
				}
				var reverse=true;
				var bubble=true;
				while(bubble)
				{
					bubble=false;
					for(var i=0;i<(rows.length-1);i++) for(var j=i+1;j<rows.length;j++) if(vals[i]>vals[j])
					{
						temp=rows[i]; rows[i]=rows[j]; rows[j]=temp;
						temp=vals[i]; vals[i]=vals[j]; vals[j]=temp;
						bubble=true;
						reverse=false;
					}
				}
				if(reverse) rows=rows.reverse();
				for(var i=0;i<rows.length;i++) $(table).append(rows[i]);
				return false;
			});
		}
	});
});
//]]>
</script>

<table class="wide spacetable" align="center">
	<tr>
<?php

if ('' == $site_list[$posted['site_id']]) {
?>
		<th>Site</th>
<?php
}
?>
		<th>PID</th>
		<th>Product Name</th>
		<th>Country</th>
		<th><?= ($display_inquiry) ? 'Inquiry' : 'Invoice' ?> #</th>
		<th>Customer</th>
		<th>Qty</th>
		<th>Unit Price</th>
		<th>Located</th>
		<th>Last Staff</th>
		<th>Days Open</th>
	</tr>

<?php
foreach ($product_list as $product) {
    if (!array_key_exists($product['invoice_id'], $invoice_cache)) {
        $invoice_cache[$product['invoice_id']] = invoiceFromDatabase($product['invoice_id']);
    }

    $invoice = $invoice_cache[$product['invoice_id']];
?>
	<tr<?= $class ?>>
<?php
if ('' == $site_list[$posted['site_id']]) {
?>
		<td style="text-align:center;"><?php hecho($site_list[$invoice['site_id']])?></td>
<?php
}
?>
		<td style="text-align:center;"><?php hecho($product['product_id'])?></td>
		<td style="text-align:center;"><?php hecho($product['product_name'])?></td>
		<td style="text-align:center;"><?php hecho($ship_country_list[$invoice['ship_country']])?></td>
		<td style="text-align:center;"><?= invoiceFormatNumber($invoice['invoice_id'], 0, $invoice['site_id']) ?></td>
		<td style="text-align:center;"><?php hecho($invoice['bill_name'])?></td>
		<td style="text-align:center;"><?= intval($product['product_qty']) ?></td>
		<td style="text-align:right;"><?= sprintf('%.2f', $product['product_offer']) ?> <?= $invoice['invoice_currency'] ?></td>
		<td style="text-align:center;"><?= ('y' == $product['product_found']) ? 'Yes' : 'No' ?></td>
		<td style="text-align:center;"><?php hecho($all_staff_list[$invoice['staff_id']])?></td>
		<td style="text-align:center;"><?= $invoice['days_open'] ?></td>
	</tr>
<?php
}
?>
</table>

<!-- /list_wanted_printable.tpl -->
