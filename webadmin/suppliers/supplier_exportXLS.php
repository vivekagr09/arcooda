<?php
/**
 * @fixme This script needs updating to use phpspreadsheet instead of writeexcel
 */
?><?php

return;

set_time_limit(300);
include_once 'suppliers_common.php';

moduleLoad('writeexcel');

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

$type        = $posted['type'];
$text        = $_REQUEST['show'];
$sub         = $_REQUEST['sub'];
$alpha_value = $_REQUEST['alpha_value'];

$extra = ('' != $sub) ? "s.$text " . (('site' == $text) ? "LIKE '%" . $sub . "%' " : '=' . $DB->escape($sub)) : '';
$extra .= ('' != $alpha_value) ? (('' != $extra) ? " AND " : "") . "s.manufacturer LIKE " . $DB->escape($alpha_value . '%') : '';

$search_for = (isset($posted['search_for'])) ? $posted['search_for'] : '';
$order_by   = (isset($posted['order_by'])) ? $posted['order_by'] : 'supplier_id';
$order      = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$supplier_list = supplierSearch($search_for, $extra, $site_id, $type);
$supplier_list = supplierSort($supplier_list, $order_by);
$seaports      = getSeaPortsList();
$airports      = getAirPortsList();
$company       = array(0 => 'None', 1 => 'HG', 2 => 'HE', 9 => 'CNV', 11 => 'HGR', 16 => 'CAR');
$sup_type      = array('supp' => 'Supplier', 'man' => 'Manufacturer', 'both' => 'Supplier & Manufacturer');

if (count($supplier_list)) {
    $fname     = tempnam("/tmp", "suppliers.xls");
    $workbook  = new writeexcel_workbook($fname);
    $worksheet = &$workbook->addworksheet('Suppliers');
    $worksheet->repeat_rows(0, 1);

    $header = &$workbook->addformat();
    $header->set_bold();
    $header->set_size(12);
    $header->set_fg_color("yellow");
    $header->set_border_color('black');
    $header->set_top(2);
    $header->set_bottom(2);
    $header->set_right(2);
    $header->set_align('center');
    $header->set_align('vcenter');
    //$header->set_merge();

    $worksheet->set_column('A:V', 25);
    $start_row = 1;
    $start_col = 0;
    $headings  = array('Name', 'Address', 'Phone', 'Fax', 'Mobile', 'Email', 'Website', 'Currency', 'Warehouses', 'Shipping' => array('Seaport', 'Airport'), 'Contacts' => array('Name', 'Position', 'Email'), 'Bank Details', 'Default Values', 'Supplier For', 'Type', 'Active');
    foreach ($headings as $key => $heading) {
        if (!is_array($heading)) {
            $worksheet->write($start_row, $start_col, $heading, $header);
            $worksheet->write_blank($start_row + 1, $start_col, $header);
            $worksheet->merge_cells($start_row, $start_col, $start_row + 1, $start_col);
            $start_col++;
        } else {
            $worksheet->write($start_row, $start_col, $key, $header);
            $temp = $start_col + 1;
            for ($i = 1; $i < count($heading); $i++) {
                $worksheet->write_blank($start_row, $temp++, $header);
            }

            // now merge those cells..
            $worksheet->merge_cells($start_row, $start_col, $start_row, $start_col + (count($heading) - 1));

            // now add the sub-headers.
            for ($i = 0; $i < count($heading); $i++) {
                $worksheet->write($start_row + 1, $start_col++, $heading[$i], $header);
            }

        }
    }
    $format = &$workbook->addformat();
    $format->set_size(12);
    $format->set_text_wrap();
    $format->set_border_color('black');
    $format->set_top(1);
    $format->set_bottom(1);
    $format->set_right(1);
    $format->set_align('center');
    $format->set_align('vcenter');
    $start_row = 3;
    $start_col = 0;
    // now have to print out the actual supplier data.
    foreach ($supplier_list as $supplier_id) {
        $supplier   = supplierFromDatabase($supplier_id);
        $warehouses = supplierWarehousesFromDatabase($supplier_id, true);
        $contacts   = supplierContactsFromDatabase($supplier_id);
        $banks      = supplierBanksFromDatabase($supplier_id, true);

        $rowspan = max(1, count($contacts));
        $sites   = explode(',', $supplier['site']);
        foreach ($sites as $key => $site) {
            $sites[$key] = $company[$site];
        }

        $address = array();
        if ('' != $supplier['address']) {
            $address[] = $supplier['address'];
            $temp      = array();
            if ('' != $supplier['city']) {
                $temp[] = $supplier['city'];
            }

            if ('' != $supplier['state']) {
                $temp[] = $supplier['state'];
            }

            if ('' != $supplier['post_code']) {
                $temp[] = $supplier['post_code'];
            }

            $address[] = implode(", ", $temp);
            if ('' != $supplier['country']) {
                $address[] = getCountryName($supplier['country']);
            }

        }
        $address = implode("\n", $address);
        if ('' != $supplier['country'] && '' != $supplier['seaport']) {
            $seaport = $seaports[$supplier['country']][$supplier['seaport']];
        }

        if ('' != $supplier['country'] && '' != $supplier['airport']) {
            $airport = $airports[$supplier['country']][$supplier['airport']];
        }

        $default = false;
        if ($supplier['waranty'] > 0) {
            $default = true;
        }

        if ($supplier['voltage'] > 0) {
            $default = true;
        }

        if ($supplier['condition'] > 0) {
            $default = true;
        }

        if ($supplier['plug'] > 0) {
            $default = true;
        }

        if ($supplier['packing'] > 0) {
            $default = true;
        }

        if (in_array('HE', $sites) || in_array('HG', $sites)) {
            if ($supplier['terms'] > 0) {
                $default = true;
            }

            if ($supplier['coin'] > 0) {
                $default = true;
            }

            if ($supplier['version'] > 0) {
                $default = true;
            }

        }
        $data = array($supplier['supplier_name'], $address, $supplier['phone'], $supplier['fax'], $supplier['mobile'], $supplier['email'], $supplier['website'], $supplier['currency'], ($warehouses > 0 ? 'Y' : 'N'), $seaport, $airport, $contacts, ($banks > 0 ? 'Y' : 'N'), ($default ? 'Y' : 'N'), implode(", ", $sites), $sup_type[$supplier['type']], ((('n' == $supplier['active']) ? 'Not ' : '') . 'Active'));

        foreach ($data as $temp) {
            if (!is_array($temp)) {
                $worksheet->write($start_row, $start_col, $temp, $format);
                if ($rowspan > 1) {
                    for ($i = 1; $i < $rowspan; $i++) {
                        $worksheet->write_blank($start_row + $i, $start_col, $format);
                    }

                    $worksheet->merge_cells($start_row, $start_col, ($start_row + ($rowspan - 1)), $start_col);
                }
                $start_col++;
            } else {
                $r_start = $start_row;
                $c_start = $start_col;
                if (count($temp)) {
                    foreach ($temp as $contact) {
                        $worksheet->write($r_start, $c_start++, $contact['name'], $format);
                        $worksheet->write($r_start, $c_start++, $contact['title'], $format);
                        $worksheet->write($r_start++, $c_start++, $contact['email'], $format);
                        $c_start = $start_col;
                    }
                } else {
                    for ($i = 0; $i < 3; $i++) {
                        $worksheet->write_blank($r_start, $c_start++, $format);
                    }
                }

                $start_col += 3;
            }
        }
        $start_row += $rowspan;
        $start_col = 0;
    }

    // now output the file...
    $workbook->close();
    //die('Under Construction');
    $filename = ((in_array('man', explode('|', $type))) ? 'manufacturers' : 'suppliers') . '.xls';

    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
    header("Content-Length: " . filesize($fname));
    readfile($fname);
    unlink($fname);
}
