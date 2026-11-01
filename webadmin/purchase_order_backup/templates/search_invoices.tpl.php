<!-- search_invoices.tpl -->

<?php
moduleLoad('invoice');

$status_list = array(
    '--1'                                => '',
    'enquiry|inquiry|closed'             => 'All Inquiries',
    'enquiry|inquiry'                    => 'Open Inquiries',
    'closed'                             => 'Closed Inquiries',
    '--2'                                => '',
    'proforma|sales|processed|cancelled' => 'All Invoices',
    'proforma|sales'                     => 'Proforma+Sales Invoices',
    'proforma'                           => 'Proforma Invoices',
    'sales'                              => 'Sales Invoices',
    'processed'                          => 'Processed Invoices',
    'cancelled'                          => 'Cancelled Invoices',
    '--3'                                => '',
    'enquiry|inquiry|proforma|sales'     => 'All Open/Proforma/Sales Invoices',
    'closed|cancelled'                   => 'All Closed/Cancelled Invoices',
);
$site_list = array('' => 'All') + invoiceGetOptionList('site_id');

$post_url = $get_vars;
unset($post_url['page_id']);
unset($post_url['site_id']);
unset($post_url['status']);
unset($post_url['search_for']);
unset($post_url['prod_string']);
$post_url['action'] = 'list';
$post_url           = 'index.php?' . http_build_query($post_url);
?>
<form accept-charset="utf-8" action="<?php hecho($post_url)?>" method="post" onsubmit="searchOldInquiry();"><table align="center" style="border:1px solid #FF9900;width:95%;">
	<tr><td colspan="3" style="text-align:center;background-color:#FFFFCC;"><h2 style="color:#FF9900;">Search Invoices &amp; Inquiries</h2></td></tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;width:37%;">Browse By Site:</td>
		<td style="text-align:left;background-color:#FFFFCC;width:25%;"><select name="site_id" style="width:256px;">
			<?= formatHTMLOptions($site_list, $site_id) ?>
		</select></td>
		<td style="text-align:right;background-color:#FFFFCC;width:37%;"></td>
	</tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;">Browse By Status:</td>
		<td style="text-align:left;background-color:#FFFFCC;"><select name="status" style="width:256px;">
			<option value="">All</option>
			<?= formatHTMLOptions($status_list, $status_id) ?>
		</select></td>
		<td style="text-align:left;background-color:#FFFFCC;"></td>
	</tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;">Search For :</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			<input type="text" style="width:256px;" name="search_for" id="search1" value="<?php hecho($search_for)?>" />
		</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			(<b>Invoice ID</b>, <b>Customer Name</b>, <b>Tracking Number</b> or <b>Email</b>)
		</td>
	</tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;">Search For :</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			<input type="text" style="width:256px;" name="prod_string" id="search2" value="<?php hecho($prod_string)?>" />
		</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			(<b>Product ID</b> or <b>Product Name</b>)
		</td>
	</tr>
	<tr><td colspan="3" style="text-align:center;background-color:#FFFFCC;"><input type="submit" value="Search" /></td></tr>
</table></form>

<script>
	function searchOldInquiry() {
		var search1 = document.getElementById('search1').value;
		var search2 = document.getElementById('search2').value;
		window.open('/webadmin/invoice/?search=true&list=all&search_string=' + search1 + '&prod_string=' + search2, '', 'width=900,height=500');
	}
</script>

<!-- /search_invoices.tpl -->
