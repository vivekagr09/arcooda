
<?php
include_once __DIR__ . '/common.inc.php';

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
foreach (invoiceGetOptionList('all_fields') as $key => $value) {
    if ('' != $posted[$key]) {
        $criteria[$key] = (is_array($posted[$key])) ? $posted[$key] : preg_split("/[^a-zA-Z0-9]+/", $posted[$key]);
    }
}

unset($criteria['invoice_id']);
if ('' != $posted['search_for']) {
    $criteria['any_text'] = explode('|', $posted['search_for']);
}

if ('' != $posted['subset_list']) {
    $criteria['invoice_id'] = explode('|', $posted['subset_list']);
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
    $criteria['invoice_id'] = explode('|', $posted['invoice_list']);
}

if ('' != $posted["prod_string"]) {
    $invoice_list = invoiceSearch($criteria);
    if (count($invoice_list)) {
        $query        = $DB->query("SELECT DISTINCT `invoice_id` FROM `invoice_products` WHERE `product_id` != 0 AND (`product_name` LIKE " . formatSQLString('%' . $posted["prod_string"] . '%') . " OR `product_id` = " . intval($posted["prod_string"]) . ") AND `invoice_id` IN(" . implode(", ", $invoice_list) . ")");
        $invoice_list = array();
        while ($row = $DB->fetch_assoc($query)) {
            $invoice_list[] = $row["invoice_id"];
        }

        $DB->free_result($query);
        $criteria["invoice_id"] = $invoice_list;
    }
}

if ('y' == $posted['payments_made']) {
    $invoice_list = array();
    $query        = $DB->query("SELECT `invoice_id` FROM `invoice_payments` GROUP BY `invoice_id`");
    while ($row = $DB->fetch_assoc($query)) {
        $invoice_list[] = $row['invoice_id'];
    }

    $DB->free_result($query);
    $criteria['invoice_id'] = $invoice_list;
}

$sub_criteria = $criteria;
unset($sub_criteria['handler_id']);
unset($sub_criteria['site_id']);
$invoice_list = invoiceSearch($sub_criteria);
echo '<pre>'; print_r($invoice_list);
?>
