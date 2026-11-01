<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice');

include_once __DIR__ . '/header.inc.php';

$print_url           = $posted;
$print_url["action"] = "tax_print";
unset($print_url["page_id"]);
$print_url = "index.php?" . http_build_query($print_url);

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = "payments_locate_date";
if ("" != $posted["order_by"]) {
    $order_by = strtolower($posted['order_by']);
}

//Build a search criteria array
$criteria                   = array();
$criteria["invoice_status"] = array("proforma", "sales", "processed", "cancelled");
foreach (array('site_id', 'newsletter_id', 'invoice_action', 'invoice_status', 'paymenttype_id', 'payments_located', 'member_id', 'staff_id', 'visitor_id') as $key) {
    if ('' != $posted[$key]) {
        $criteria[$key] = (is_array($posted[$key])) ? $posted[$key] : preg_split("/[^a-zA-Z0-9]+/", $posted[$key]);
    }
}

if ('' != $posted['search_for']) {
    $criteria['any_text'] = explode('|', $posted['search_for']);
}

if ('' != $posted['status']) {
    $criteria['invoice_status'] = explode('|', $posted['status']);
}

if ('' != $posted['bill_country']) {
    $criteria['bill_country'] = explode('|', $posted['bill_country']);
}

if ('' != $posted['handler']) {
    $criteria['handler_id'] = $posted['handler'];
}

if ('' != $posted['open_year']) {
    $criteria['open_year'] = mktime(0, 0, 0, 1, 1, date('Y', $posted['open_year']));
}

if ('' != $posted['open_month']) {
    $criteria['open_month'] = mktime(0, 0, 0, date('m', $posted['open_month']), 1, date('Y', $posted['open_month']));
}

if ('' != $posted['open_week']) {
    $criteria['open_week'] = mktime(0, 0, 0, date('m', $posted['open_week']), date('d', $posted['open_week']) + 1 - date('w', $posted['open_week']), date('Y', $posted['open_week']));
}

if ('' != $posted['tax_year']) {
    $criteria['tax_year'] = mktime(0, 0, 0, 1, 1, date('Y', $posted['tax_year']));
}

if ('' != $posted['tax_fyear']) {
    $criteria['tax_fyear'] = mktime(0, 0, 0, 7, 1, date('Y', $posted['tax_fyear']) - ((date("m", $posted["tax_fyear"]) > 6) ? 0 : 1));
}

if ('' != $posted['tax_month']) {
    $criteria['tax_month'] = mktime(0, 0, 0, date('m', $posted['tax_month']), 1, date('Y', $posted['tax_month']));
}

if ('' != $posted['tax_week']) {
    $criteria['tax_week'] = mktime(0, 0, 0, date('m', $posted['tax_week']), date('d', $posted['tax_week']) + 1 - date('w', $posted['tax_week']), date('Y', $posted['tax_week']));
}

if ('' != $posted['invoice_list']) {
    $criteria['invoice_id'] = explode('|', $posted['invoice_list']);
}

//if ($posted['payments_made'] == 'y')
{
    $invoice_list = array();
    $query        = $DB->query("SELECT `invoice_id` FROM `invoice_payments` GROUP BY `invoice_id`");
    while ($row = $DB->fetch_assoc($query)) {
        $invoice_list[] = $row['invoice_id'];
    }

    $DB->free_result($query);
    $criteria['invoice_id'] = $invoice_list;
}
$site_id   = intval($posted['site_id']);
$status_id = $posted['status'];
$search    = $posted['search_for'];

//Build list from unallocated and duplicate allocations
$invoice_list = array();
$alloc_list   = array();
foreach (invoiceChunk(invoiceSearch($criteria), "tax_id") as $tax_id => $sub_list) {
    if (0 == $tax_id || count($sub_list) > 1) {
        $invoice_list = array_merge($invoice_list, $sub_list);
    } else {
        $alloc_list = array_merge($alloc_list, $sub_list);
    }
}

if (array_key_exists("tax_year", $criteria) || array_key_exists("tax_month", $criteria) || array_key_exists("tax_fyear", $criteria)) {
    $invoice_list = $alloc_list;
}

unset($alloc_list);

$invoice_list = invoiceSort($invoice_list, $order_by);
if ('desc' == $order) {
    $invoice_list = array_reverse($invoice_list);
}

$items_per_page = 100;
if ($_GLOBALS['staff']['invoices_per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['invoices_per_page'];
}

$count_total = count($invoice_list);
$display     = ($display_both) ? 'Invoices/Inquiries' : (($display_inquiry) ? 'Inquiries' : 'Invoices');

if ("" != $posted["p"] && count($pass = json_decode(base64_url_decode($posted["p"])))) {
?>
<div style="text-align:left;color:#070;width:95%;">
	Successfully updated:
	<ul>
<?php
foreach ($pass as $invoice_id) {
?>
		<li>I#<?= $invoice_id ?></li>
<?php
}
?>
	</ul>
</div>
<?php
}

if ("" != $posted["f"] && count($fail = json_decode(base64_url_decode($posted["f"])))) {
?>
<div style="text-align:left;color:#700;width:95%;">
	Failed to Update:
	<ul>
<?php
foreach ($fail as $row) {
?>
		<li>I#<?= $row[0] ?>: Tax Invoice #<?= $row[1] ?>, Tax Date <?= $row[2] ?></li>
<?php
}
?>
	</ul>
</div>
<?php
}

?>
<?= formatHTMLTitle("HK Tax Report") ?>
<?php
$invoice_list  = array_chunk($invoice_list, $items_per_page);
$count_pages   = count($invoice_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$invoice_list  = $invoice_list[$page_id];
$count_display = count($invoice_list);
if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url   = '?' . http_build_query($page_url);
    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);
?>
<form accept-charset="utf-8" action="index.php?action=update_tax_vals" method="post">
	<br />
<?php
include __DIR__ . '/templates/allocate_invoice.tpl.php';
?>
	<br />
	<?= $page_links ?><br />
	<br />
	<script type="text/javascript">
//<![CDATA[
function validateForm()
{
	var id_list = <?php jecho($invoice_list)?>;
	for (var i = 0;i < id_list.length;i++)
	{
		var $tax_id = $("input[name='invoices[" + id_list[i] + "][tax_id]']");
		var $tax_date = $("input[name='invoices[" + id_list[i] + "][tax_date]']");
		var valid_id = /^(0*[1-9][0-9]*)?$/.test($tax_id.val());
		var valid_date = /^(2[0-9]{3}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]))?$/.test($tax_date.val());
		if ($tax_id.val().length && $tax_date.val().length == 0 || !valid_date)
		{
			$tax_date.focus();
			alert("Please enter a valid date");
			return false;
		}
		if ($tax_id.val().length == 0 && $tax_date.val().length || !valid_id)
		{
			$tax_id.focus();
			alert("Please enter a valid id");
			return false;
		}
	}
	return true;
}
//]]>
	</script>
	<input type="button" onclick="if (validateForm()) this.form.submit()" value="Update" />
<?php
if (array_key_exists("tax_month", $posted) || array_key_exists("tax_year", $posted) || array_key_exists("tax_fyear", $posted)) {
?>
	<input type="button" onclick="<?php hecho("window.open(" . jecho($print_url, 1) . ")")?>" value="Print" /><br />
<?php
}
?>
</form>
<br />
<?php
} else {
?>
No results were found<br />
<br />
<?php
}
if (array_key_exists("tax_month", $posted)) {
    $tax_month_url           = $posted;
    $tax_month_url["action"] = "tax_list";
    unset($tax_month_url["page_id"]);
    unset($tax_month_url["tax_month"]);
    $tax_month_url = "index.php?" . http_build_query($tax_month_url) . "&tax_month=";

    $d = explode("-", date("Y-m", $posted["tax_month"]));
?>
<div style="text-align:center;">
	<a href="<?php hecho($tax_month_url . mktime(0, 0, 0, $d[1] - 1, 1, $d[0]))?>">&larr; Previous Month (<?php hecho(date("F Y", mktime(0, 0, 0, $d[1] - 1, 1, $d[0])))?>)</a>
	|
	<a href="<?php hecho($tax_month_url . mktime(0, 0, 0, $d[1] + 1, 1, $d[0]))?>">Next Month (<?php hecho(date("F Y", mktime(0, 0, 0, $d[1] + 1, 1, $d[0])))?>) &rarr;</a>
</div>
<?php
}
if (array_key_exists("tax_year", $posted)) {
    $tax_year_url           = $posted;
    $tax_year_url["action"] = "tax_list";
    unset($tax_year_url["tax_year"]);
    unset($tax_year_url["page_id"]);
    $tax_year_url = "index.php?" . http_build_query($tax_year_url) . "&tax_year=";

    $d = explode("-", date("Y", $posted["tax_year"]));
?>
<div style="text-align:center;">
	<a href="<?php hecho($tax_year_url . mktime(0, 0, 0, 1, 1, $d[0] - 1))?>">&larr; Previous Year (<?php hecho(date("Y", mktime(0, 0, 0, 1, 1, $d[0] - 1)))?>)</a>
	|
	<a href="<?php hecho($tax_year_url . mktime(0, 0, 0, 1, 1, $d[0] + 1))?>">Next Year (<?php hecho(date("Y", mktime(0, 0, 0, 1, 1, $d[0] + 1)))?>) &rarr;</a>
</div>
<?php
}
if (array_key_exists("tax_fyear", $posted)) {
    $tax_fyear_url           = $posted;
    $tax_fyear_url["action"] = "tax_list";
    unset($tax_fyear_url["tax_fyear"]);
    unset($tax_fyear_url["page_id"]);
    $tax_fyear_url = "index.php?" . http_build_query($tax_fyear_url) . "&tax_fyear=";

    $d = explode("-", date("Y", $posted["tax_fyear"]));
?>
<div style="text-align:center;">
	<a href="<?php hecho($tax_fyear_url . mktime(0, 0, 0, 1, 1, $d[0] - 1))?>">&larr; Previous Financial Year (<?php printf("%d - %d", $d[0] - 2, $d[0] - 1)?>)</a>
	|
	<a href="<?php hecho($tax_fyear_url . mktime(0, 0, 0, 1, 1, $d[0] + 1))?>">Next Financial Year (<?php printf("%d - %d", $d[0], $d[0] + 1)?>) &rarr;</a>
</div>
<?php
}

include_once __DIR__ . '/footer.inc.php';
