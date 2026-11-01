<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceChunk, invoiceSearch, invoiceSumValue, invoiceSort, invoiceGetValue

global $DB;

$time1 = explode(' ', microtime());

include_once 'PEAR.php';
include_once 'Spreadsheet/Excel/Writer.php';

$fname    = tempnam("/tmp", "new_members.xls");
$workbook = new Spreadsheet_Excel_Writer($fname);

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

$worksheet = $workbook->addWorksheet($label);
$row_id    = 0;

$criteria = array();
foreach (array('member_id', 'country', 'status', 'flag', 'account_type', 'mailing_list', 'reseller', 'partner', 'best_described', 'currency', 'products_per_page', 'news_per_page', 'mailing_products', 'last_initial', 'city', 'site_id') as $k) {
    if (array_key_exists($k, $posted)) {
        if (is_array($posted[$k])) {
            $criteria[$k] = $posted[$k];
        } else {
            $criteria[$k] = preg_split("/[^a-zA-Z0-9@_]+/", $posted[$k]);
        }

    }
}

if ('' != $posted['search_for']) {
    $criteria['any_text'] = explode(' ', $posted['search_for']);
}

$member_list = memberSearch($criteria);
if (count($criteria["any_text"])) {
    $whereAND = array();
    foreach ($criteria["any_text"] as $word) {
        if (trim($word) != "") {
            $whereOR = array();
            foreach (array("`email`") as $field) {
                $whereOR[] = $field . " LIKE " . formatSQLLikeString($word);
            }

            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
            unset($whereOR);
        }
    }

    if (count($whereAND)) {
        if ($query = $DB->query(sprintf(
            "SELECT `member_id` FROM `highwaygroup`.`new_members_contacts` WHERE %s",
            implode(" AND ", $whereAND)
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $member_list[] = $row["member_id"];
            }
            $DB->free_result($query);
        }
        $member_list = array_unique($member_list);
    }
    unset($whereAND);
}

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$order_by = strtolower($posted['order_by']);
if ('' != $posted['last_initial']) {
    $order_by = 'last_name';
    $order    = 'asc';
}

$member_list = memberSort($member_list, $order_by);
if ('desc' == $order) {
    $member_list = array_reverse($member_list);
}

moduleLoad("invoice");
$site_list           = memberGetOptionList('site_id');
$invoices_by_member  = array();
$inquiries_by_member = array();
if (count($member_list)) {
    $invoices_by_member = invoiceChunk(invoiceSearch(array(
        "member_id"      => $member_list,
        "invoice_status" => "processed",
    )), "member_id");
    $inquiries_by_member = invoiceChunk(invoiceSearch(array(
        "member_id"      => $member_list,
        "invoice_status" => array("inquiry", "closed", "cancelled"),
    )), "member_id");
}

$worksheet->writeRow($row_id++, 0, array(
    'Date Joined',
    'Company',
    'Contact Name',
    'Email',
    'Member Type',
    'Country',
    'IP Address',
    'Total Inquiries',
    'Total Sales',
    'Sales Value (USD)',
    'Last Order',
    'Total Logins',
    'Last Login',
    'Active',
    'Notes',
), $fmt_header);

$member_cache = array();
if ($query = $DB->query(sprintf(
    "SELECT `member_id`, `company_name`, `country`, `date_joined`, `email_address`, `first_name`, `last_name`, `signup_IP`, `site_id`, `spammer`, `tester`, `timewaster`, TRIM(CONCAT_WS(' ', `first_name`, `last_name`)) AS `name` FROM `highwaygroup`.`new_members` WHERE `member_id` IN(%s)",
    implode(", ", $member_list)
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $member_cache[$row['member_id']] = $row;
    }
    $DB->free_result($query);
}

$country_range_cache = array();
if ($query = $DB->query(sprintf(
    "SELECT `member_id`, GROUP_CONCAT(`country`) AS `country` FROM (SELECT `member_id`, `country` FROM `highwaygroup`.`new_members_shipping` WHERE `member_id` IN(%1\$s) UNION SELECT `member_id`, `country` FROM `highwaygroup`.`new_members_notify` WHERE `member_id` IN(%1\$s) UNION SELECT `member_id`, `country` FROM `highwaygroup`.`new_members_forwarders` WHERE `member_id` IN(%1\$s)) AS `sub1` GROUP BY `member_id`",
    implode(', ', $member_list)
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $country_range_cache[$row['member_id']] = ('' !== $row['country']) ? array_unique(explode(',', $row['country'])) : array();
    }
    $DB->free_result($query);
}

foreach ($member_list as $id) {
    if (array_key_exists($id, $member_cache)) {
        $member = $member_cache[$id];
    } else {
        $member = memberFromDatabase($id);
    }

    if (array_key_exists($id, $country_range_cache)) {
        $country_range = $country_range_cache[$id];
    } else {
        $country_range = array();
        foreach (memberShippingAddressesFromDatabase($id) as $v) {
            $country_range[] = $v["country"];
        }

        foreach (memberNotifyPartiesFromDatabase($id) as $v) {
            $country_range[] = $v["country"];
        }

        foreach (memberForwardersFromDatabase($id) as $v) {
            $country_range[] = $v["country"];
        }

    }

    $member_logins = memberLoginHistoryFromDatabase($id, '', 0);
    $last_login    = reset($member_logins);

    $member["processed_list"] = array();
    $member['inquiry_list']   = array();
    if (array_key_exists($id, $invoices_by_member)) {
        $member["processed_list"] = $invoices_by_member[$id];
    }

    if (array_key_exists($id, $inquiries_by_member)) {
        $member["inquiry_list"] = $inquiries_by_member[$id];
    }

    $member["processed_total"] = invoiceSumValue($member["processed_list"], "invoice_offerau");

    $row = getCountryInfo($member['signup_IP']);
    if ('--' != $row['a2']) {
        $member['signup_country'] = $row['a2'];
    }

    $member['name'] = trim($member['first_name'] . ' ' . $member['last_name']);

    $email_tooltip = array();
    foreach (memberRequestsFromDatabase($id) as $v) {
        if ('' != $v['comments']) {
            $email_tooltip[] = $v['comments'];
        }
    }
    foreach (memberWarningsFromDatabase($id) as $v) {
        if ('' != $v['comments']) {
            $email_tooltip[] = $v['comments'];
        }
    }

    $country_range[] = $member['country'];
    $country_range[] = $member["signup_country"];
    if ($query = $DB->query(sprintf(
        "SELECT `login_ip` FROM `highwaygroup`.`new_members_login_track` WHERE `member_id` = %d",
        $id
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $ip              = getCountryInfo($row["login_ip"]);
            $country_range[] = $ip["a2"];
        }
        $DB->free_result($query);
    }

    $country_range = array_unique($country_range);
    foreach ($country_range as $k => $v) {
        if ("--" == $v || "" == $v) {
            unset($country_range[$k]);
        }
    }

    if (count($country_range) > 1) {
        $email_tooltip[] = "Multiple countries (" . implode(", ", $country_range) . ")";
    }

    if ('y' === $member['spammer']) {
        $email_tooltip[] = 'Spammer';
    }

    if ('y' === $member['timewaster']) {
        $email_tooltip[] = 'Time Waster';
    }

    if ('y' === $member['tester']) {
        $email_tooltip[] = 'Tester';
    }

    $site_col = array();
    foreach (explode(",", $member['site_id']) as $site_id) {
        if ('' != $site_id) {
            $site_col[] = $site_list[$site_id];
        }
    }

    $processed_col = '';
    if (count($member['processed_list'])) {
        $last_order    = end(invoiceSort($member['processed_list'], 'proforma_date'));
        $processed_col = date(_('d/m/Y'), invoiceGetValue($last_order, 'proforma_time'));
    }

    $worksheet->writeRow($row_id++, 0, array(
        date(_('d/m/Y'), strtotime($member['date_joined'])),
        $member['company_name'],
        $member['name'],
        $member['email_address'],
        implode("\n", $site_col),
        $country_list[$member['country']],
        $country_list[$member['signup_country']],
        count($member["inquiry_list"]),
        count($member["processed_list"]),
        '$' . convertCurrency($member['processed_total'], 'USD'),
        $processed_col,
        count($member_logins),
        ($last_login['login_time']) ? date(_('d/m/Y'), $last_login['login_time']) : 'Never',
        ('active' == $member['status']) ? 'Yes' : 'No',
        implode("\n", $email_tooltip),
    ), $fmt_normal);
}

$time2 = explode(' ', microtime());
//echo bcsub(bcadd($time2[0], $time2[1], 6), bcadd($time1[0], $time1[1], 6), 6);
//die();

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"new_members.xls\"");
$workbook->close();
header("Content-Length: " . filesize($fname));
readfile($fname);
unlink($fname);
