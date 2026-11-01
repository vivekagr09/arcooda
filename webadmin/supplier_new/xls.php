<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberFromDatabase, memberSort, memberSurveySearch, memberSearch

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

if ($count_total > 0) {
?>
<table>
    <tr>
        <th>ID</th>
        <th>Date Joined</th>
        <th>Company</th>
        <th>Contact Name</th>
        <th>Email</th>
        <th>Inquiries</th>
        <th>Invoices</th>
    </tr>
<?php
    foreach ($member_list as $member_id) {
        $member = memberFromDatabase($member_id, true);

        $member['signup_country'] = '';
        $row                      = getCountryInfo($member['signup_IP']);
        if ('--' != $row['a2']) {
            $member['signup_country'] = $row['a2'];
        }
?>
    <tr>
        <td><?=htmlspecialchars($member['member_number'])?></td>
        <td><?=htmlspecialchars(($member['created_time']) ? date("d/m/y", $member['created_time']) : '-')?></td>
        <td><?=htmlspecialchars($member['company'])?></td>
        <td><?=htmlspecialchars($member['full_name'])?></td>
        <td><?=htmlspecialchars($member['email_address'])?></td>
        <td><?=htmlspecialchars($member['total_inquiries'])?></td>
        <td><?=htmlspecialchars($member['total_invoices'])?></td>
    </tr>
<?php
    }
?>
</table>
<?php
}
