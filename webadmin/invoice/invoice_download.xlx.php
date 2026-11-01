<?php

include_once __DIR__ . '/common.inc.php';

include_once 'PEAR.php';
include_once 'Spreadsheet/Excel/Writer.php';

$fname    = tempnam("/tmp", "invoice_list.xls");
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$workbook = new Spreadsheet_Excel_Writer($fname);

$order    = (array_key_exists('order', $posted) && 'asc' == $posted['order']) ? 'asc' : 'desc';
$order_by = (array_key_exists('order_by', $posted)) ? strtolower($posted['order_by']) : 'invoice_id';
$page_id  = (array_key_exists('page_id', $posted)) ? max(0, intval($posted['page_id'])) : 0;

//Build a search criteria array
$criteria = array();
foreach (array('site_id', 'newsletter_id', 'visitor_id', 'invoice_action', 'invoice_status', 'paymenttype_id', 'payments_located', 'member_id', 'staff_id', "tax_month", "tax_year", 'ship_country','ship_id') as $key) {
    if (array_key_exists($key, $posted) && !empty($posted[$key])) {
        $criteria[$key] = (is_array($posted[$key])) ? $posted[$key] : preg_split("/[^a-zA-Z0-9]+/", $posted[$key]);
    }
}

if (9 == $_GLOBALS["site_settings"]["site_id"]) {
    $allowed = array(9, 16, 25);
    if (is_array($criteria["site_id"])) {
        foreach ($criteria["site_id"] as $k => $v) {
            if (!in_array($v, $allowed)) {
                unset($criteria["site_id"][$k]);
            }
        }

        if (count($criteria["site_id"]) == 0) {
            $criteria["site_id"] = 9;
        }
    } elseif (!in_array($criteria["site_id"], $allowed)) {
        $criteria["site_id"] = 9;
    }
} elseif (!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
    $criteria["site_id"] = $_GLOBALS["site_settings"]["site_id"];
}

if (array_key_exists('search_for', $posted) && !empty($posted['search_for'])) {
    $criteria['any_text'] = explode('|', $posted['search_for']);
}

if (array_key_exists('status', $posted) && !empty($posted['status'])) {
    $criteria['invoice_status'] = explode('|', $posted['status']);
}

if (array_key_exists('bill_country', $posted) && !empty($posted['bill_country'])) {
    $criteria['bill_country'] = explode('|', $posted['bill_country']);
}

if (array_key_exists('handler', $posted) && !empty($posted['handler'])) {
    $criteria['handler_id'] = $posted['handler'];
}

if (array_key_exists('open_year', $posted) && !empty($posted['open_year'])) {
    $criteria['open_year'] = mktime(0, 0, 0, 1, 1, date('Y', $posted['open_year']));
}

if (array_key_exists('open_quarter', $posted) && !empty($posted['open_quarter'])) {
    $criteria['open_quarter'] = mktime(0, 0, 0, date('m', $posted['open_quarter']), 1, date('Y', $posted['open_quarter']));
}

if (array_key_exists('open_month', $posted) && !empty($posted['open_month'])) {
    $criteria['open_month'] = mktime(0, 0, 0, date('m', $posted['open_month']), 1, date('Y', $posted['open_month']));
}

if (array_key_exists('open_week', $posted) && !empty($posted['open_week'])) {
    $criteria['open_week'] = mktime(0, 0, 0, date('m', $posted['open_week']), date('d', $posted['open_week']) + 1 - date('w', $posted['open_week']), date('Y', $posted['open_week']));
}

if (array_key_exists('invoice_list', $posted) && !empty($posted['invoice_list'])) {
    $criteria['invoice_id'] = preg_split('/[^0-9]+/', $posted['invoice_list']);
}
if (array_key_exists('invoice_action', $posted) && !empty($posted['invoice_action'])) {
    $criteria['invoice_action'] = $posted['invoice_action'];
}

//Find and pagenate the invoices
$invoice_list = invoiceSearch($criteria);

if (array_key_exists('prod_string', $posted)) {
    $temp = preg_split("/[^a-z0-9]+/i", $posted['prod_string']);
    foreach ($temp as $k => $v) {
        if ("" == $v) {
            unset($temp[$k]);
        }
    }

    if (count($temp) > 0) {
        $where = array();
        foreach ($temp as $findme) {
            foreach (array('`ip`.`product_id`', '`ip`.`product_name`', '`ip`.`product_notes`') as $v) {
                $where[] = $v . " LIKE " . formatSQLLikeString($findme);
            }
        }

        $temp  = array();
        $sql   = sprintf("SELECT `ip`.`invoice_id` FROM `highwaygroup`.`invoice_products` AS `ip` WHERE %s GROUP BY `ip`.`invoice_id` ASC", implode(" OR ", $where));
        $query = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $temp[] = $row['invoice_id'];
        }

        $DB->free_result($query);

        $invoice_list = array_intersect($invoice_list, $temp);
    }
}

if (array_key_exists('payments_made', $posted) && 'y' == $posted['payments_made']) {
    $temp  = array();
    if ($query = $DB->query(sprintf(
        "SELECT `ip`.`invoice_id` FROM `highwaygroup`.`invoice_payments` AS `ip` GROUP BY `ip`.`invoice_id`"
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $temp[] = $row['invoice_id'];
        }
        $DB->free_result($query);
    }

    $invoice_list = array_intersect($invoice_list, $temp);
}

$site_id   = intval($posted['site_id']);
$status_id = (array_key_exists('status', $posted)) ? $posted['status'] : '';
$search    = (array_key_exists('search_for', $posted)) ? $posted['search_for'] : '';
if (array_key_exists("status", $posted) && ((is_array($posted["status"]) && (in_array("sales", $posted["status"]) || in_array("proforma", $posted["status"]))) || in_array("sales", preg_split("/[^a-z0-9]+/", $posted["status"])) || in_array("proforma", preg_split("/[^a-z0-9]+/", $posted["status"]))) && (!isset($posted["referer"]))) {
    $sub_criteria                   = $criteria;
    $sub_criteria["invoice_status"] = "processed";
    $sub_criteria["invoice_action"] = array('working', 'feedback', 'nointerest', 'payment', 'approval', 'shipping', 'shipped', 'cancelled', 'nohelp', 'returned', 'sent_factory', 'prepare_factory', 'prepare_customer', 'sent_customer', 'return_completed', 'return_cancelled');
    $invoice_list                   = array_unique(array_merge($invoice_list, invoiceSearch($sub_criteria)));
    sort($invoice_list);
}

if (array_key_exists("supplier_id", $posted)) {
    $supplier_id = (is_array($posted["supplier_id"])) ? $posted["supplier_id"] : preg_split("/[^0-9]+/", $posted["supplier_id"]);
    foreach ($supplier_id as $k => $v) {
        $supplier_id[$k] = intval($v);
    }

    if (count($supplier_id) == 0 || count($invoice_list) == 0) {
        $invoice_list = array();
    } else {
        $query        = $DB->query("SELECT DISTINCT `invoice_id` FROM `highwaygroup`.`invoice_products` WHERE `supplier_id` IN(" . implode(", ", $supplier_id) . ") AND `invoice_id` IN(" . implode(", ", $invoice_list) . ")");
        $invoice_list = array();
        while ($row = $DB->fetch_assoc($query)) {
            $invoice_list[] = $row["invoice_id"];
        }

        $DB->free_result($query);
    }
}

$invoice_list = invoiceSort($invoice_list, $order_by);
if ('desc' == $order) {
    $invoice_list = array_reverse($invoice_list);
}

if (count($invoice_list)) {
    $credit_resultcode = array();
    if ($query = $DB->query(sprintf(
        "SELECT `result_code`, `result_message` FROM `highwaygroup`.`credit_resultcodes`"
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $credit_resultcode[$row["result_code"]] = $row["result_message"];
        }
        $DB->free_result($query);
    }
}

// header
$fmt_header = $workbook->addFormat([
    'FontFamily' => 'Calibri',
    'Size'       => 11,
    'Weight'     => 1,
    'HAlign'     => 'center',
    'VAlign'     => 'vcenter',
    'Border'     => 1,
]);

// center aligned (default for all except product name)
$fmt_center = $workbook->addFormat([
    'FontFamily' => 'Calibri',
    'Size'       => 11,
    'HAlign'     => 'center',
    'VAlign'     => 'vcenter',
    'Border'     => 1,
]);

// product name (left aligned + wrapped)
$fmt_customer = $workbook->addFormat([
    'FontFamily' => 'Calibri',
    'Size'       => 11,
    'HAlign'     => 'left',
    'VAlign'     => 'vcenter',
    'TextWrap'   => 1,
    'Border'     => 1,
]);

$worksheet = $workbook->addWorksheet($label);
/* ---------- COLUMN WIDTHS ---------- */
$worksheet->setColumn(0, 1, 12); // invoice Id
$worksheet->setColumn(2, 2, 25); // customer name
$worksheet->setColumn(3, 3, 16); // Other numeric columns
$worksheet->setColumn(4, 6, 18); 
$worksheet->setColumn(7, 7, 20); 
$worksheet->setColumn(8, 10, 18);
/* ---------- HEADER ---------- */
$row_id = 0;
$worksheet->writeRow($row_id++, 0, [
    'Invoice #',
    'Open Date',
    'Customer Name',
    'Invoice Value',
    'Products Located',
    'Freight Located',
    'Payment Made',
    'Status',
    'Last Staff',
    'Last Modified',
    'Days Open',
], $fmt_header);
foreach ($invoice_list as $key => $invoice_id) {
         $invoice = invoiceFromDatabase($invoice_id);
         $display_name = ('' != $invoice['bill_company']) ? $invoice['bill_company'] : $invoice['bill_name'];
         $invoicevalue = '$' . $invoice['invoice_offer'] . ' ' . $invoice['invoice_currency'];
        $products_located = (('y' == $invoice['products_located'] || $invoice['products_found'] == $invoice['products_needed']) ? 'Yes' : 'No');
         if ('y' != $invoice['products_located'] && $invoice['products_found'] < $invoice['products_needed']) {
            $products_located =  $invoice['products_found'] . '/' . $invoice['products_needed'];
        }
        $ship_located = (0 == $invoice['ship_id']) ? 'No' : 'Yes';

    
        $invoice_payments = invoicePaymentsFromDatabase($invoice_id);
        foreach ($invoice_payments as $payment) {
            $payment_due = bcsub($payment_due, convertCurrency($payment['payment_amount'], $payment["payment_currency"], $invoice["invoice_currency"]), 2);
        }
        $payments_located = ('y' == $invoice['payments_located']) ? 'Yes' : 'No';
             if (count($invoice_payments) && bccomp($payment_due, "0.05", 2) >= 0) {
            $payments_located = 'Deposit';
        }

        if (count($invoice_payments) && bccomp($payment_due, "0.05", 2) < 0 && $invoice['invoice_offer'] > 0.0) {
            $payments_located = 'Yes';
        }
        if (count($invoice_payments) && abs($payment_due) ==  abs($invoice['invoice_offer'])) {
            $payments_located = 'No';
        }
        
        $worksheet->write($row_id, 0, $invoice['invoice_id'], $fmt_center);
        $worksheet->write($row_id, 1, date('d/m/Y', $invoice['open_time']), $fmt_center);
        $worksheet->write($row_id, 2,  $display_name, $fmt_customer);
        $worksheet->write($row_id, 3, $invoicevalue, $fmt_center);
        $worksheet->write($row_id, 4, $products_located, $fmt_center);
        $worksheet->write($row_id, 5, $ship_located, $fmt_center);
        $worksheet->write($row_id, 6, $payments_located , $fmt_center);
        $worksheet->write($row_id, 7, $invoice['invoice_status'], $fmt_center);
        $worksheet->write($row_id, 8, htmlspecialchars($all_staff_list[$invoice['handler_id']]), $fmt_center);
        $worksheet->write($row_id, 9, date('d/m/Y', $invoice['write_time']), $fmt_center);
        $worksheet->write($row_id, 10, $invoice['days_open'], $fmt_center);
     $row_id++;
}
/* ---------- OUTPUT ---------- */
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"invoice_list.xls\"");

$workbook->close();
header("Content-Length: " . filesize($fname));
readfile($fname);
unlink($fname);