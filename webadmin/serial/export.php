<?php
/**
 * @fixme This script needs updating to use phpspreadsheet instead of writeexcel
 */
?><?php

/**
 * @var Serial_Handler $section_handler
 * @var string         $section_name
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('writeexcel');

global $_GLOBALS;

$order    = $_REQUEST['order'] ?? 'asc';
$order_by = strtolower($_REQUEST['order_by'] ?? 'name');

if ('asc' != $order) {
    $order = 'desc';
}

//Build a search criteria array
$criteria = $_GLOBALS['criteria_options'][$section_name];
foreach ($section_handler->getOptionList('all_fields') as $k => $v) {
    if (array_key_exists($k, $_REQUEST)) {
        if (is_array($_REQUEST[$k])) {
            $criteria[$k] = $_REQUEST[$k];
        } elseif ('' !== $_REQUEST[$k]) {
            $criteria[$k] = preg_split('/[^0-9a-z]+/i', $_REQUEST[$k]);
        }
        foreach ($criteria[$k] as $k2 => $v2) {
            if (empty($v2)) {
                unset($criteria[$k][$k2]);
            }
        }
    }
}

unset($criteria['id']);

if (array_key_exists('search_for', $_REQUEST) && '' != $_REQUEST['search_for']) {
    $criteria['any_text'] = explode('|', $_REQUEST['search_for']);
}

if (array_key_exists('subset_list', $_REQUEST) && '' != $_REQUEST['subset_list']) {
    $criteria['id'] = explode('|', $_REQUEST['subset_list']);
}

//Find and pagenate the items
$item_list = $section_handler->search($criteria);
$item_list = $section_handler->sort($item_list, $order_by);
if ('desc' == $order) {
    $item_list = array_reverse($item_list);
}

$count_total = count($item_list);

if ($count_total > 0) {
    $fname    = tempnam('/tmp', 'serial_number_report.xls');
    if (false === $fname) {
        die('Couldn\'t create working file');
    }
    $workbook = new writeexcel_workbook($fname);

    $fmt_normal = &$workbook->addformat([
        'valign' => 'vcenter',
        'align'  => 'center',
    ]);
    $fmt_normal->set_text_wrap(1);

    $fmt_header = &$workbook->addformat([
        'bold'     => 1,
        'valign'   => 'vcenter',
        'align'    => 'center',
        'pattern'  => 1,
        'fg_color' => 42,
    ]);
    $fmt_header->set_text_wrap(1);

    $fmt_company_name = &$workbook->addformat([
        'bold'   => 1,
        'valign' => 'vcenter',
        'align'  => 'left',
        'size'   => 20,
    ]);

    $fmt_company = &$workbook->addformat([
        'valign' => 'vcenter',
        'align'  => 'left',
    ]);
    $fmt_company->set_text_wrap(1);

    $worksheet = &$workbook->addworksheet('Serial Numbers');

    //$worksheet->set_header('&LHighway Entertainment Pty Ltd&CReport ' . $report_id . ' - ' . $report['title'] . '&R' . date('d/m/Y',$report['timestamp_time']),0.25);
    $worksheet->set_header('&LHighway Entertainment Pty Ltd&CReport ', 0.25);
    $worksheet->set_footer('&CPage &p of &N', 0.25);
    //$worksheet->set_landscape();
    $worksheet->set_margins(0.5);
    $worksheet->freeze_panes('A5');
    $worksheet->repeat_rows(0, 0);
    $worksheet->print_area('A:F');
    $worksheet->fit_to_pages(1, 0);
    $worksheet->center_horizontally();

    //write company info
    $worksheet->write('A1', 'HIGHWAY GROUP', $fmt_company_name);
    $worksheet->merge_cells(0, 0, 0, 3);
    $worksheet->write('A2', '44-48 Maitland Road, Mayfield East, NSW, 2304', $fmt_company);
    $worksheet->merge_cells(1, 0, 1, 3);
    $worksheet->write('A3', 'Ph: (02) 4968 9313 Fax: (02) 4968 9314 Email: sales@arcooda.com', $fmt_company);
    $worksheet->merge_cells(2, 0, 2, 3);
    $report_generator  = $_GLOBALS['staff']['name'];
    $report_print_date = date('d/m/y');
    $worksheet->write('E1', 'Serial Number Summary   Printed By ' . $report_generator . '          Print Date: ' . $report_print_date, $fmt_normal);

    //start to generate content of the excel
    //key=>name of column
    $headings = [
        'serial_number'  => 'Serial #',
        'product_name'   => 'Machine Name',
        'product_number' => 'Machine #',
        'invoice_number' => 'Invoice #',
        'bill_name'      => 'Sold To',
        'time_to_use'    => 'Date of Invoice',
    ];
    $worksheet->write_row(3, 0, $headings, $fmt_header);

    //set column width
    $widths = [
        'serial_number'  => 10.00,
        'product_name'   => 59.00,
        'product_number' => 10.00,
        'invoice_number' => 11.00,
        'bill_name'      => 25.00,
        'time_to_use'    => 10.00,
    ];

    $count = 0;
    foreach ($headings as $k => $v) {
        $worksheet->set_column($count, $count, $widths[$k]);

        $count++;
    }

    $count = 3;
    foreach ($item_list as $id) {
        $item = $section_handler->fromDatabase($id);

        $products_count  = 0;
        $products_markup = 0;

        $count++;
        //set row height
        $worksheet->set_row($count, 12.75);
        //start to write data into excel file (row, column, value);
        $worksheet->write($count, 0, $item['serial_number'], $fmt_normal);
        $worksheet->write($count, 1, $item['product_name'], $fmt_normal);
        $worksheet->write($count, 2, $item['product_number'], $fmt_normal);
        $worksheet->write($count, 3, (0 != $item['invoice_id']) ? $item['invoice_number'] : 'Stock', $fmt_normal);
        $worksheet->write($count, 4, (0 != $item['invoice_id']) ? $item['bill_name'] : 'Stock', $fmt_normal);
        $worksheet->write($count, 5, (0 != $item['invoice_id']) ? date('d/m/Y', $item['time_to_use']) : 'Stock', $fmt_normal);
    }
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="serial_number_report.xls"');

    $workbook->close();

    header('Content-Length: ' . filesize($fname));
    readfile($fname);
    unlink($fname);
} else {
    echo htmlspecialchars(_('No results were found.'));
}
