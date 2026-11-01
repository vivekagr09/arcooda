<?php
/**
 * Expects $workbook, $fmt_normal, $fmt_header, $member, $member_payments
 *
 * @var mixed[] $member
 *
 * Return $worksheet
 */
?><?php

$worksheet = $workbook->addworksheet('New Statement');

$worksheet->set_header('&LHighway Group Pty Ltd&CMember Statement ' . $member_id . ' - ' . $member['company_name'] . '&R' . date(_('d/m/Y')), 0.25);
$worksheet->set_footer('&CPage &p of &N', 0.25);
$worksheet->set_landscape();
$worksheet->set_margins(0.5);
$worksheet->set_zoom(80);
$worksheet->freeze_panes('A11');
$worksheet->repeat_rows(0, 10);
$worksheet->print_area('A:G');
$worksheet->fit_to_pages(1, 0);
$worksheet->center_horizontally();

$country_list = memberGetOptionList('country');
$company_name = $member['company_name'];
if ('' == $company_name) {
    $company_name = trim($member['first_name'] . ' ' . $member['last_name']);
}

$short_name = reset(explode(' ', $company_name));

$address1 = array();
if ('' != $company_name) {
    $address1[] = $company_name;
}

if ('' != $member['address']) {
    $address1[] = $member['address'];
}

if ('' != $member['city']) {
    $address1[] = $member['city'];
}

if ('' != $member['state']) {
    $address1[] = $member['state'];
}

if ('' != $member['post_code']) {
    $address1[] = $member['post_code'];
}

if ('' != $country_list[$member['country']]) {
    $address1[] = $country_list[$member['country']];
}

$address1 = str_replace(array("\r", "\n", "  ", "\t"), " ", implode(' ', $address1));

$address2 = array();
if ('' != $member['phone_no']) {
    $address2[] = 'Ph ' . $member['phone_no'];
}

if ('' != $member['phone_no2']) {
    $address2[] = 'Ph ' . $member['phone_no2'];
}

if ('' != $member['fax_no']) {
    $address2[] = 'Fax ' . $member['fax_no'];
}

$address2 = str_replace(array("\r", "\n", "  ", "\t"), " ", implode(', ', $address2));

$headings = array(
    'date'          => 'Date',
    'reference'     => 'Reference',
    'item'          => 'Item',
    'pay_by_hg'     => "Payment by\nHG",
    'pay_by_member' => "Payment by\n" . $short_name,
    'balance'       => "Balance to\nHG",
    'remarks'       => 'Remarks',
);

$widths = array(
    'date'          => 10.29,
    'reference'     => 10.29,
    'item'          => 58.14,
    'pay_by_hg'     => 13.00,
    'pay_by_member' => 13.00,
    'balance'       => 13.00,
    'remarks'       => 15.14,
);

/**
 * Set the column widths
 */
$count = 0;
foreach ($headings as $k => $v) {
    if (isset($widths[$k])) {
        $worksheet->set_column($count, $count, $widths[$k]);
    }

    $count++;
}

/**
 * Write the headers
 */
$worksheet->write_row(0, 0, array($address1), $fmt_title);
$worksheet->write_row(1, 0, array($address2), $fmt_title);
$worksheet->write_row(3, 0, array('NEW STATEMENT'), $fmt_subtitle);
$worksheet->write_row(5, 0, array('last updated ' . date(_('jS F, Y'), $last_update)), $fmt_subsubtitle);
$worksheet->write_row(5, 3, array('Sales by HG', 'Sales by ' . $short_name), $fmt_header);
$worksheet->write_row(3, 3, array($payby, $sale), $fmt_number);
$worksheet->write_row(3, 5, array($balance), ($balance > 0) ? $fmt_highlight_number : $fmt_number);

/**
 * Write in the column headers
 */
$rows = array();
foreach (array_values($headings) as $x => $v1) {
    foreach (explode("\n", $v1) as $y => $v2) {
        $rows[$y][$x] = $v2;
    }
}

foreach ($rows as $y => $v1) {
    foreach (array_values($headings) as $x => $v2) {
        if (!array_key_exists($x, $v1)) {
            $rows[$y][$x] = '';
        }
    }
}

for ($y = 0; $y < 2; $y++) {
    ksort($rows[$y]);
    $worksheet->write_row($y + 7, 0, $rows[$y], $fmt_header);
}

/**
 * Draw the border
 */
$row = array();
foreach (array_values($headings) as $x => $v) {
    $row[$x] = '';
}

$worksheet->write_row(9, 0, $row, $fmt_border);

$count = 10;
foreach ($display_rows as $row) {
    $count++;

    $output = array();
    foreach ($headings as $k => $v) {
        $output[$k] = $row[$k];
    }

    $temp = array();
    for ($i = 0; $i < 2; $i++) {
        $temp[] = array_shift($output);
    }

    $worksheet->write_row($count, 0, $temp, $fmt_normal);

    $temp = array();
    for ($i = 0; $i < 1; $i++) {
        $temp[] = array_shift($output);
    }

    $worksheet->write_row($count, 2, $temp, $fmt_text);

    $worksheet->write_row($count, 3, $output, $fmt_normal);
}
