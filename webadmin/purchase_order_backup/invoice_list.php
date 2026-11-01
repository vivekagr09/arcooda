<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice');

include_once __DIR__ . '/header.inc.php';
// Report all PHP errors
error_reporting(E_ALL);

// Display errors on the screen
ini_set('display_errors', 1);
// Get the current user's details
$user_permits     = getInvoicePermissions($_GLOBALS['staff']['id']);
$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

//Build a search criteria array
$criteria = array();
foreach (purchaseOrderGetOptionList('all_fields') as $key => $value) {
    if ('' != $posted[$key]) {
        $criteria[$key] = (is_array($posted[$key])) ? $posted[$key] : preg_split("/[^a-zA-Z0-9]+/", $posted[$key]);
    }
}
unset($criteria['order_id']);
if ('' != $posted['search_for']) {
    $criteria['any_text'] = explode('|', $posted['search_for']);
}
if ('' != $posted['order_action']) {
    $criteria['order_action'] = explode('|', $posted['order_action']);
}
if ('' != $posted['subset_list']) {
    $criteria['order_id'] = explode('|', $posted['subset_list']);
}
if ('' != $posted['supplier_id']) {
    $criteria['supplier_id'] = explode('|', $posted['supplier_id']);
}

if ('' != $posted['status']) {
    $criteria['order_status'] = explode('|', $posted['status']);
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

if ('' != $posted['open_quarter']) {
    $criteria['open_quarter'] = mktime(0, 0, 0, date('m', $posted['open_quarter']), 1, date('Y', $posted['open_quarter']));
}

if ('' != $posted['open_month']) {
    $criteria['open_month'] = mktime(0, 0, 0, date('m', $posted['open_month']), 1, date('Y', $posted['open_month']));
}

if ('' != $posted['open_week']) {
    $criteria['open_week'] = mktime(0, 0, 0, date('m', $posted['open_week']), date('d', $posted['open_week']) + 1 - date('w', $posted['open_week']), date('Y', $posted['open_week']));
}

if ('' != $posted['invoice_list']) {
    $criteria['order_id'] = explode('|', $posted['invoice_list']);
}
if ('' != $posted["prod_string"]) {
    $invoice_list = purchaseOrderSearch($criteria);
    if (count($invoice_list)) {
        $query        = $DB->query("SELECT DISTINCT `order_id` FROM `purchase_order_products` WHERE `product_id` != 0 AND (`product_name` LIKE " . formatSQLString('%' . $posted["prod_string"] . '%') . " OR `product_id` = " . intval($posted["prod_string"]) . ") AND `order_id` IN(" . implode(", ", $invoice_list) . ")");
        $invoice_list = array();
        while ($row = $DB->fetch_assoc($query)) {
            $invoice_list[] = $row["order_id"];
        }

        $DB->free_result($query);
        $criteria["order_id"] = $invoice_list;
    }
}

if ('y' == $posted['payments_made']) {
    $invoice_list = array();
    $query        = $DB->query("SELECT `order_id` FROM `purchase_order_payments` GROUP BY `order_id`");
    while ($row = $DB->fetch_assoc($query)) {
        $invoice_list[] = $row['order_id'];
    }

    $DB->free_result($query);
    $criteria['order_id'] = $invoice_list;
}

$sub_criteria = $criteria;
unset($sub_criteria['handler_id']);
unset($sub_criteria['site_id']);
$invoice_list = purchaseOrderSearch($sub_criteria);
if (count($invoice_list) > 0) {
    $list_by1 = 'handler_id';
    $list_by2 = 'site_id';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewByHandlerWebsite'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Handler/Website</b></a>
	<br /><br />
	<div id="sectionViewByHandlerWebsite" class="child" style="display:none;">
<?php
include __DIR__ . '/templates/invoices_navigate.tpl.php';
?>
	</div>
</div>
<?php
}

$sub_criteria = $criteria;
unset($sub_criteria['order_action']);
unset($sub_criteria['site_id']);
$invoice_list = invoiceSearch($sub_criteria);
if (count($invoice_list) > 0) {
    $list_by1 = 'order_action';
    $list_by2 = 'site_id';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewByActionWebsite'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Status/Website</b></a>
	<br /><br />
	<div id="sectionViewByActionWebsite" class="child" style="display:none;">
<?php
include __DIR__ . '/templates/invoices_navigate.tpl.php';
?>
	</div>
</div>
<?php
}

$sub_criteria = $criteria;
unset($sub_criteria['handler_id']);
unset($sub_criteria['producttype_id']);
$invoice_list = purchaseOrderSearch($sub_criteria);
if (count($invoice_list) > 0) {
    $list_by1 = 'handler_id';
    $list_by2 = 'producttype_id';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewByHandlerType'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Handler/Product Type</b></a>
	<br /><br />
	<div id="sectionViewByHandlerType" class="child" style="display:none;">
<?php
include __DIR__ . '/templates/invoices_navigate.tpl.php';
?>
	</div>
</div>
<?php
}

$site_id   = intval($posted['site_id']);
$status_id = $posted['status'];
$search    = $posted['search_for'];

?>
<div style="text-align:left;">
	<a href="#" onclick="toggleSection('sectionSearchInvoices');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Search Invoices</b></a>
	<br /><br />
	<div id="sectionSearchInvoices" class="child"<?= ('' != $search) ? '' : ' style="display:none;"' ?>>
<?php
include __DIR__ . '/templates/search_invoices.tpl.php';
?>
	</div>
	<div id="sectionSearchInvoices_hideNote" class="child"></div>
</div>

<?php
//Find and pagenate the invoices
unset($criteria['site_id']);
$invoice_list = purchaseOrderSearch($criteria);
$invoice_list = purchaseordereSort($invoice_list, $order_by);
if ('desc' == $order) {
    $invoice_list = array_reverse($invoice_list);
}

$items_per_page = 50;
if ($_GLOBALS['staff']['invoices_per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['invoices_per_page'];
}

$count_total = count($invoice_list);

$invoice_list  = array_chunk($invoice_list, $items_per_page);
$count_pages   = count($invoice_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$invoice_list  = $invoice_list[$page_id] ?? [];
$count_display = count($invoice_list);

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url = '?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);

    $select_url = $get_vars;
    unset($select_url['action']);
    $select_url = 'invoice_select.php?' . http_build_query($select_url);
?>
<br />
<form accept-charset="utf-8" action="<?php hecho($select_url)?>" method="post">
<?php
include __DIR__ . '/templates/list_invoices.tpl.php';
?>
</form>
<br />
<?= $page_links ?><br />
<br />
<?php
if (!$popup_window) {
?>
<input type="button" onclick="window.open('invoice_list_printable.php?<?= http_build_query($get_vars) ?>');" value="Print <?= ($display_inquiry) ? 'Inquiry' : 'Invoice' ?> List" />
<input type="button" onclick="window.open('invoice_wanted_printable.php?<?= http_build_query($get_vars) ?>');" value="Print Wanted List" />
<?php
} else {
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/footer.inc.php';
