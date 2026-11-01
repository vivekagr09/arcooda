<?php
$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || trim($value) == '') {
            unset($get_vars[$key]);
        }
    }
}

$all_staff_list = array();
if ($query = $DB->query(sprintf(
    "SELECT `id`, `first_name`, `last_name` FROM `highwaygroup`.`users`"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $all_staff_list[$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
    }
}

$active_staff_list = array();
foreach (staffSearch(1) as $staff_id) {
    $active_staff_list[$staff_id] = $all_staff_list[$staff_id];
}

$inactive_staff_list = array();
foreach ($all_staff_list as $staff_id => $staff_name) {
    if (!isset($active_staff_list[$staff_id])) {
        $inactive_staff_list[$staff_id] = $staff_name;
    }
}

$display_inquiry = false;
if ('inquiry' == $posted['status'] || 'closed' == $posted['status'] || 'inquiry|closed' == $posted['status']) {
    $display_inquiry = true;
}

$invoice_companies = array();
if ($query = $DB->query(sprintf(
    "SELECT `company_id`, `company_name` FROM `highwaygroup`.`company_info`"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $invoice_companies[$row['company_id']] = $row['company_name'];
    }
    $DB->free_result($query);
}

$country_list = getCountryList();
