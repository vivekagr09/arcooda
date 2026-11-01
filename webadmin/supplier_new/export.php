<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceSearch
moduleLoad('member'); //memberGetOptionList, memberSort, memberSurveySearch, memberSearch

global $_GLOBALS;
global $DB;

include_once 'PEAR.php';
include_once 'Spreadsheet/Excel/Writer.php';

ini_set('memory_limit', '2G');

$order = array_key_exists('order', $_REQUEST) ? $_REQUEST['order'] : 'desc';
if ('asc' != $order) {
    $order = 'desc';
}

$order_by = array_key_exists('order_by', $_REQUEST) ? $_REQUEST['order_by'] : 'member_id';

//Build a search criteria array
$criteria = array();
foreach (supplierGetOptionList('all_fields') as $k => $v) {
    if (array_key_exists($k, $_REQUEST)) {
        $criteria[$k] = explode('|', $_REQUEST[$k]);
    }
}

unset($criteria['member_id']);
if (array_key_exists('subset_list', $_REQUEST)) {
    $criteria['member_id'] = explode('|', $_REQUEST['subset_list']);
}

if (array_key_exists('option', $_REQUEST)) {
    $criteria['option'] = explode('|', $_REQUEST['option']);
}

if (array_key_exists('search_for', $_REQUEST)) {
    $chunk = explode('|', $_REQUEST['search_for']);
    foreach ($chunk as $v) {
        $spaceChunk = explode(' ', $v);
        foreach ($spaceChunk as $_v) {
            $criteria['any_text'][] = $_v;
        }
    }
}

$member_list = memberSearch($criteria);
if (array_key_exists('option', $criteria)) {
    $member_list = memberSurveySearch(array('member_id' => $member_list, 'option' => $criteria['option']));
}

$member_list = memberSort($member_list, $order_by);
if ('desc' == $order) {
    $member_list = array_reverse($member_list);
}

$count_total   = count($member_list);
$count_display = count($member_list);

$member_cache = array();

$query = $DB->query("SELECT `m`.`member_id`, `m`.`check_staff_id`, `m`.`company`, `m`.`country`, `m`.`customer_type`, `m`.`email_address`, `m`.`member_type`, `m`.`signup_IP`, `m`.`site_id`, `m`.`wholesale`, UNIX_TIMESTAMP(`m`.`check_date`) AS `check_time`, UNIX_TIMESTAMP(`m`.`created`) AS `created_time`, UNIX_TIMESTAMP(`m`.`modified`) AS `modified_time`, CONCAT_WS(' ', `m`.`name`, `m`.`surname`) AS `full_name`, 0 AS `total_invoices`, 0 AS `total_inquiries`, 0 AS `last_login`, 0 AS `total_logins`, '0.00' AS `order_total` FROM `highwaygroup`.`new_members` AS `m` GROUP BY `m`.`member_id`");
while ($row = $DB->fetch_assoc($query)) {
    $row['member_number']            = sprintf('CU#%06d', $row['member_id']);
    $member_cache[$row['member_id']] = $row;
}
$DB->free_result($query);

foreach (invoiceGroupCount(invoiceSearch([
    'invoice_status' => ['proforma', 'sales', 'processed', 'return', 'completed'],
]), 'member_id') as $k => $v) {
    if (array_key_exists($row['member_id'], $member_cache)) {
        $member_cache[$k]['total_inquiries'] += $v;
    }
}

foreach (invoiceGroupCount(invoiceSearch([
    'invoice_status' => ['enquiry', 'inquiry', 'closed', 'cancelled'],
]), 'member_id') as $k => $v) {
    if (array_key_exists($row['member_id'], $member_cache)) {
        $member_cache[$k]['total_inquiries'] += $v;
    }
}


//$country_list = getCountryList(false);
$site_list = getWebsitesList();
//$check_staff_list = memberGetOptionList('check_staff_id');

$fname      = tempnam("/tmp", "new_members.xls");
$workbook   = new Spreadsheet_Excel_Writer($fname);
$fmt_normal = $workbook->addFormat(array(
    'FontFamily' => 'Calibri',
    'Size'       => 11,
));
$fmt_header = $workbook->addFormat(array(
    'FontFamily' => 'Calibri',
    'Size'       => 11,
    'Weight'     => 1,
    'HAlign'     => 'center', 'VAlign' => 'vcenter',
));

$worksheet = $workbook->addWorksheet($_GLOBALS['site_settings']['site_name'] . ' Members List');

$worksheet->setHeader('&L' . $_GLOBALS['site_settings']['site_name'] . '&CMember List&R' . date('d/m/Y'), 0.25);
$worksheet->setFooter('&CPage &p of &N', 0.25);
//$worksheet->setLandscape();
$worksheet->setMargins(0.5);
//$worksheet->freezePanes(array(3, 0));
$worksheet->repeatRows(0, 0);
$worksheet->printArea(0, 0, 0, 8);
$worksheet->fitToPages(1, 0);
$worksheet->centerHorizontally();
$worksheet->write(0, 0, $_GLOBALS['site_settings']['site_name'], $fmt_header);
$worksheet->mergeCells(0, 0, 0, 4);
$worksheet->write(1, 0, "", $fmt_header);
$worksheet->mergeCells(1, 0, 1, 4);
$worksheet->write(2, 0, "", $fmt_header);
$worksheet->mergeCells(2, 0, 2, 4);
$worksheet->write(0, 7, $_GLOBALS['site_settings']['site_name'] . " Member List\nPrinted By " . $_GLOBALS['staff']['name'] . "\nPrint Date: " . date("d/m/y"), $fmt_header);
$worksheet->mergeCells(0, 7, 2, 8);

$count = 3;

$count++;
$output   = array();
$output[] = 'ID';
$output[] = 'Date Joined';
$output[] = 'Company';
$output[] = 'Contact Name';
$output[] = 'Email';

$output[] = 'Inquiries';
$output[] = 'Invoices';

$worksheet->writeRow($count, 0, $output, $fmt_header);

foreach ($member_list as $member_id) {
    $count++;
    $member = $member_cache[$member_id];

    $member['signup_country'] = '';
    $row                      = getCountryInfo($member['signup_IP']);
    if ('--' != $row['a2']) {
        $member['signup_country'] = $row['a2'];
    }

    $output   = array();
    $output[] = $member['member_number'];
    $output[] = ($member['created_time']) ? date("d/m/y", $member['created_time']) : '-';
    $output[] = $member['company'];
    $output[] = $member['full_name'];
    $output[] = $member['email_address'];

    $output[] = $member['total_inquiries'];
    $output[] = $member['total_invoices'];

    $worksheet->writeRow($count, 0, $output, $fmt_normal);
}

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"member_list_" . date('Ymd') . ".xls\"");
$workbook->close();
header("Content-Length: " . filesize($fname));
readfile($fname);
unlink($fname);
