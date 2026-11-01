<!-- search_invoices.tpl -->

<?php
$status_list = array(
    '--1'                                => '',
    'cancelled'                   => 'Cancelled',
    'closed'                      => 'Closed',
    'completed'                   => 'Completed',
    'enquiry'                     => 'Enquiry',
    'proforma'                    => 'Proforma',
    'processed'                   => 'Processed',
    'return'                      => 'Return',
    'sales'                       => 'Sales',
);

$post_url = $get_vars;
unset($post_url['page_id']);
unset($post_url['supplier_id']);
unset($post_url['status']);
unset($post_url['search_for']);
unset($post_url['prod_string']);
$post_url['action'] = 'list';
$post_url           = 'index.php?' . http_build_query($post_url);
?>
<form accept-charset="utf-8" action="<?php hecho($post_url)?>" method="post" onsubmit="searchOldInquiry();"><table align="center" style="border:1px solid #FF9900;width:95%;">
	<tr><td colspan="3" style="text-align:center;background-color:#FFFFCC;"><h2 style="color:#FF9900;">Search Purchase Orders</h2></td></tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;width:37%;">Browse By Supplier:</td>
		<td style="text-align:left;background-color:#FFFFCC;width:20%;"><select name="supplier_id" style="width:300px;">
			<option value="">Select Supplier</option>
			<?= formatHTMLOptions(purchaseOrderGetOptionList('supplier'), $supplier_id) ?>
		</select></td>
		<td style="text-align:right;background-color:#FFFFCC;width:37%;"></td>
	</tr>
	<!-- <tr>
		<td style="text-align:right;background-color:#FFFFCC;">Browse By Status:</td>
		<td style="text-align:left;background-color:#FFFFCC;"><select name="status" style="width:300px;">
			<option value="">All</option>
			<?= formatHTMLOptions($status_list, $status_id) ?>
		</select></td>
		<td style="text-align:left;background-color:#FFFFCC;"></td>
	</tr> -->
	<tr><td style="text-align:right;background-color:#FFFFCC;width:37%;">&nbsp;</td><td style="text-align:left;background-color:#FFFFCC;width:25%;">&nbsp;</td><td style="text-align:right;background-color:#FFFFCC;width:37%;">&nbsp;</td></tr>
	<tr><td style="text-align:right;background-color:#FFFFCC;width:37%;">&nbsp;</td><td style="text-align:left;background-color:#FFFFCC;width:25%;">&nbsp;</td><td style="text-align:right;background-color:#FFFFCC;width:37%;">&nbsp;</td></tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;">Search For :</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			<input type="text" style="width:300px;" name="search_for" id="search1" value="<?php hecho($search_for)?>" />
		</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			(<b>Purchase ID</b>)
		</td>
	</tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;">Search For :</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			<input type="text" style="width:300px;" name="prod_string" id="search2" value="<?php hecho($prod_string)?>" />
		</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			(<b>Product ID</b> or <b>supplier ID</b>  or <b>product name</b>  or <b>the supplier product name</b>)
		</td>
	</tr>
	<tr><td colspan="3" style="text-align:center;background-color:#FFFFCC;"><input type="submit" value="Search" /></td></tr>
</table></form>

<script>
	function searchOldInquiry() {
		var search1 = document.getElementById('search1').value;
		var search2 = document.getElementById('search2').value;
		// window.open('/webadmin/inquiry/inquiry_admin.php?search=true&list=all&search_string=' + search1 + '&prod_string=' + search2, '', 'width=900,height=500');
	}
</script>

<!-- /search_invoices.tpl -->
