<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$status_list = array(
    '--1'                                => '',
    'inquiry|closed'                     => 'All Inquiries',
    'inquiry'                            => 'Open Inquiries',
    'closed'                             => 'Closed Inquiries',
    '--2'                                => '',
    'proforma|sales|processed|cancelled' => 'All Invoices',
    'proforma|sales'                     => 'Proforma+Sales Invoices',
    'proforma'                           => 'Proforma Invoices',
    'sales'                              => 'Sales Invoices',
    'processed'                          => 'Processed Invoices',
    'cancelled'                          => 'Cancelled Invoices',
    '--3'                                => '',
    'inquiry|proforma|sales'             => 'All Open/Proforma/Sales Invoices',
    'closed|cancelled'                   => 'All Closed/Cancelled Invoices',
);

$post_url = $get_vars;
unset($post_url['page_id']);
unset($post_url['site_id']);
unset($post_url['status']);
unset($post_url['search_for']);
unset($post_url['prod_string']);
$post_url['action'] = 'list';
$post_url           = '.?' . http_build_query($post_url);
?>
<form action="<?=htmlspecialchars($post_url)?>" method="post" target="_blank"><table align="center" style="border:1px solid #FF9900;width:95%;">
	<tr><td colspan="3" style="text-align:center;background-color:#FFFFCC;"><h2 style="color:#FF9900;">Search Invoices & Inquiries</h2></td></tr>
<?php
if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
?>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;width:37%;">Browse By Site:</td>
		<td style="text-align:left;background-color:#FFFFCC;width:25%;"><select name="site_id" style="width:256px;">
			<option value="">All</option>
			<?=formatHTMLOptions(invoiceGetOptionList('site_id'), $site_id)?>
		</select></td>
		<td style="text-align:right;background-color:#FFFFCC;width:37%;"></td>
	</tr>
<?php
}
?>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;width:37%;">Browse By Status:</td>
		<td style="text-align:left;background-color:#FFFFCC;width:25%;"><select name="status" style="width:256px;">
			<option value="">All</option>
			<?=formatHTMLOptions($status_list, $status_id)?>
		</select></td>
		<td style="text-align:left;background-color:#FFFFCC;width:37%;"></td>
	</tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;">Search For :</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			<input type="text" style="width:256px;" name="search_for" value="<?=htmlspecialchars($search_for)?>" />
		</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			(<b>Invoice ID</b>, <b>Customer Name</b>, <b>Tracking Number</b> or <b>Email</b>)
		</td>
	</tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;">Search For :</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			<input type="text" style="width:256px;" name="prod_string" value="<?=htmlspecialchars($prod_string)?>" />
		</td>
		<td style="text-align:left;background-color:#FFFFCC;">
			(<b>Product ID</b> or <b>Product Name</b>)
		</td>
	</tr>
	<tr><td colspan="3" style="text-align:center;background-color:#FFFFCC;"><input type="submit" value="Search" /></td></tr>
</table></form>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
