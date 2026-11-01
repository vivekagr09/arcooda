<!-- menu.inc -->

<?php
global $_GLOBALS;

$temp = $_SERVER["DOCUMENT_ROOT"] . "/cache/webadmin/invoice_menu.inc.php_" . md5("");
if (is_file($temp) && (filemtime($temp) + 300) > time()) {
    readfile($temp);
    echo "<!-- #menu.inc -->\n";
    return;
}
ob_start();

if (moduleLoad("invoice")) {
    $criteria = array();
    if (array_key_exists('criteria_options', $_GLOBALS) && array_key_exists('invoice', $_GLOBALS['criteria_options'])) {
        $criteria = $_GLOBALS["criteria_options"]["invoice"];
    }

    $invoice_counts = array_merge(
        array(
            'inquiry'   => 0,
            'enquiry'   => 0,
            'closed'    => 0,
            'proforma'  => 0,
            'sales'     => 0,
			'processed' => 0,
            'cancelled' => 0,
            'return'    => 0,
            'completed' => 0,
        ),
        invoiceGroupCount(invoiceSearch($criteria), "invoice_status")
    );

    if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr bgcolor="#00CCFF"><td nowrap="nowrap" style="text-align:center"><b><?=htmlspecialchars(_("Customer Inquiries"))?></b></td></tr>
	<tr><td class="td_options" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
		<li><a class="td_options" href="/webadmin/invoice/?action=list&amp;status=inquiry|return|proforma|sales"><?=htmlspecialchars(_("List Open Inqs+Invoices"))?> (<?=intval($invoice_counts['inquiry'] + $invoice_counts['return'] + $invoice_counts['proforma'] + $invoice_counts['sales'])?>)</a></li>
		<br /><br />
		<li><a class="td_options" href="/webadmin/invoice/?action=add&amp;status=inquiry"><?=htmlspecialchars(_("Add New Inquiry"))?></a></li>
		<li><a class="td_options" href="/webadmin/invoice/?action=list&amp;status=inquiry|return"><?=htmlspecialchars(_("List Open Inquiries"))?> (<?=intval($invoice_counts['inquiry'] + $invoice_counts['return'])?>)</a></li>
		<li><a class="td_options" href="/webadmin/invoice/?action=list&amp;status=inquiry|closed|return|completed"><?=htmlspecialchars(_("List All Inquiries"))?> (<?=intval($invoice_counts['inquiry'] + $invoice_counts['closed'] + $invoice_counts['return'] + $invoice_counts['completed'])?>)</a></li>
		<!--<li><a class="td_options" href="/webadmin/invoice/?action=search&amp;status=inquiry|closed|return|completed">Search Inquiries</a></li>-->
		<br /><br />
		<li><a class="td_options" href="/webadmin/invoice/?action=add&amp;status=return"><?=htmlspecialchars(_("Add New Repair/Return"))?></a></li>
		<br /><br />
		<li><a class="td_options" href="/webadmin/invoice/?action=add&amp;status=sales"><?=htmlspecialchars(_("Add New Invoice"))?></a></li>
		<li><a class="td_options" href="/webadmin/invoice/?action=list&amp;status=proforma|sales|return"><?=htmlspecialchars(_("List Open Invoices"))?> (<?=intval($invoice_counts['proforma'] + $invoice_counts['sales'] + $invoice_counts['return'])?>)</a></li>
		<li><a class="td_options" href="/webadmin/invoice/?action=list&amp;status=processed|completed"><?=htmlspecialchars(_("List Processed Invoices"))?> (<?=intval($invoice_counts['processed'] + $invoice_counts['completed'])?>)</a></li>
		<li><a class="td_options" href="/webadmin/invoice/?action=list&amp;status=proforma|sales|processed|cancelled|return|completed"><?=htmlspecialchars(_("List All Invoices"))?> (<?=intval($invoice_counts['proforma'] + $invoice_counts['sales'] + $invoice_counts['processed'] + $invoice_counts['cancelled'] + $invoice_counts['return'] + $invoice_counts['completed'])?>)</a></li>
		<li><a class="td_options" href="/webadmin/invoice/invoice_options.php"><?=htmlspecialchars(_("Invoice Options"))?></a></li>
		<br /><br />
		<li><a class="td_options" href="/webadmin/invoice/?action=search"><?=htmlspecialchars(_("Invoice/Inquiry Search"))?></a></li>
		<li><a class="td_options" href="/webadmin/invoice/?action=list"><?=htmlspecialchars(_("Show All"))?> (<?=array_sum($invoice_counts)?>)</a></li>

	</ul></td></tr>
</table>
<br />
<?php
	} elseif (39 == $_GLOBALS['site_settings']['site_id']) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td><b><?=htmlspecialchars(_("Customer Inquiries"))?></b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/invoice/?action=list&amp;status=inquiry|return|proforma|sales"><?=htmlspecialchars(_("List Open Inqs+Invoices"))?> (<?=intval($invoice_counts["inquiry"] + $invoice_counts["return"] + $invoice_counts["proforma"] + $invoice_counts["sales"])?>)</a></li>
	</ul></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/invoice/?action=add&amp;status=inquiry"><?=htmlspecialchars(_("Add New Inquiry"))?></a></li>
		<li><a href="/webadmin/invoice/?action=list&amp;status=inquiry|return"><?=htmlspecialchars(_("List Open Inquiries"))?> (<?=intval($invoice_counts["inquiry"] + $invoice_counts["return"])?>)</a></li>
		<li><a href="/webadmin/invoice/?action=list&amp;status=inquiry|closed|return|completed"><?=htmlspecialchars(_("List All Inquiries"))?> (<?=intval($invoice_counts["inquiry"] + $invoice_counts["closed"] + $invoice_counts["return"] + $invoice_counts["completed"])?>)</a></li>
	</ul></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/invoice/?action=add&amp;status=return"><?=htmlspecialchars(_("Add New Repair/Return"))?></a></li>
	</ul></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/invoice/?action=add&amp;status=sales"><?=htmlspecialchars(_("Add New Invoice"))?></a></li>
		<li><a href="/webadmin/invoice/?action=list&amp;status=proforma|sales|return"><?=htmlspecialchars(_("List Open Invoices"))?> (<?=intval($invoice_counts["proforma"] + $invoice_counts["sales"] + $invoice_counts["return"])?>)</a></li>
		<li><a href="/webadmin/invoice/?action=list&amp;status=processed|completed"><?=htmlspecialchars(_("List Processed Invoices"))?> (<?=intval($invoice_counts["processed"] + $invoice_counts["completed"])?>)</a></li>
		<li><a href="/webadmin/invoice/?action=list&amp;status=proforma|sales|processed|cancelled|return|completed"><?=htmlspecialchars(_("List All Invoices"))?> (<?=intval($invoice_counts["proforma"] + $invoice_counts["sales"] + $invoice_counts["processed"] + $invoice_counts["cancelled"] + $invoice_counts["return"] + $invoice_counts["completed"])?>)</a></li>
		<li><a href="/webadmin/invoice/invoice_options.php"><?=htmlspecialchars(_("Invoice Options"))?></a></li>
	</ul></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/invoice/?action=search"><?=htmlspecialchars(_("Invoice/Inquiry Search"))?></a></li>
		<li><a href="/webadmin/invoice/?action=list"><?=htmlspecialchars(_("Show All"))?> (<?=array_sum($invoice_counts)?>)</a></li>
		<br /><br />
		<li><a class="td_options" href="/webadmin/shopping_carts"><?=htmlspecialchars(_("View Shopping Carts"))?></a></li>
		<br /><br />
	</ul></td></tr>
</table>
<?php
    } else {
?>
<h3><?=htmlspecialchars(_("Customer Inquiries"))?></h3>
<ul>
	<li><a href="/webadmin/invoice/?action=list&amp;status=inquiry|return|proforma|sales"><?=htmlspecialchars(_("List Open Inqs+Invoices"))?> (<?=intval($invoice_counts["inquiry"] + $invoice_counts["return"] + $invoice_counts["proforma"] + $invoice_counts["sales"])?>)</a></li>
</ul>
<ul>
	<li><a href="/webadmin/invoice/?action=add&amp;status=inquiry"><?=htmlspecialchars(_("Add New Inquiry"))?></a></li>
	<li><a href="/webadmin/invoice/?action=list&amp;status=inquiry|return"><?=htmlspecialchars(_("List Open Inquiries"))?> (<?=intval($invoice_counts["inquiry"] + $invoice_counts["return"])?>)</a></li>
	<li><a href="/webadmin/invoice/?action=list&amp;status=inquiry|closed|return|completed"><?=htmlspecialchars(_("List All Inquiries"))?> (<?=intval($invoice_counts["inquiry"] + $invoice_counts["closed"] + $invoice_counts["return"] + $invoice_counts["completed"])?>)</a></li>
</ul>
<ul>
	<li><a href="/webadmin/invoice/?action=add&amp;status=return"><?=htmlspecialchars(_("Add New Repair/Return"))?></a></li>
</ul>
<ul>
	<li><a href="/webadmin/invoice/?action=add&amp;status=sales"><?=htmlspecialchars(_("Add New Invoice"))?></a></li>
	<li><a href="/webadmin/invoice/?action=list&amp;status=proforma|sales|return"><?=htmlspecialchars(_("List Open Invoices"))?> (<?=intval($invoice_counts["proforma"] + $invoice_counts["sales"] + $invoice_counts["return"])?>)</a></li>
	<li><a href="/webadmin/invoice/?action=list&amp;status=processed|completed"><?=htmlspecialchars(_("List Processed Invoices"))?> (<?=intval($invoice_counts["processed"] + $invoice_counts["completed"])?>)</a></li>
	<li><a href="/webadmin/invoice/?action=list&amp;status=proforma|sales|processed|cancelled|return|completed"><?=htmlspecialchars(_("List All Invoices"))?> (<?=intval($invoice_counts["proforma"] + $invoice_counts["sales"] + $invoice_counts["processed"] + $invoice_counts["cancelled"] + $invoice_counts["return"] + $invoice_counts["completed"])?>)</a></li>
	<li><a href="/webadmin/invoice/invoice_options.php"><?=htmlspecialchars(_("Invoice Options"))?></a></li>
</ul>
<ul>
	<li><a href="/webadmin/invoice/?action=search"><?=htmlspecialchars(_("Invoice/Inquiry Search"))?></a></li>
	<li><a href="/webadmin/invoice/?action=list"><?=htmlspecialchars(_("Show All"))?> (<?=array_sum($invoice_counts)?>)</a></li>
</ul>
<?php
    }
}

file_put_contents($temp, ob_get_flush());
?>

<!-- /menu.inc -->
