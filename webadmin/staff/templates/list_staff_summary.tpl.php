<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 * @var string  $order
 * @var string  $order_by
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php

global $_GLOBALS;
global $DB;

if (!isset($cleanup_list)) {
    $cleanup_list = array();
}

$cleanup_list[] = array_keys($GLOBALS);

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/classes/class.temporarytable.php';
$alt        = false;
$ord_img    = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" />';
$shared_url = $get_vars;
unset($shared_url['staff_id']);
$sort_url           = $shared_url;
$sort_url['action'] = 'staff_summary';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url             = '.?' . http_build_query($sort_url) . '&order_by=';
$edit_url             = $shared_url;
$edit_url['action']   = 'edit';
$edit_url             = '.?' . http_build_query($edit_url) . '&staff_id=';
$delete_url           = $shared_url;
$delete_url['action'] = 'delete';
$delete_url           = '.?' . http_build_query($delete_url) . '&staff_id=';
$view_url             = $shared_url;
$view_url['action']   = 'view';
$view_url             = '.?' . http_build_query($view_url) . '&staff_id=';
$select_url           = $shared_url;
$select_url['action'] = 'select';
$select_url           = '.?' . http_build_query($select_url) . '&staff_id=';
$employee_types       = array('contract' => 'Contractor', 'fake' => 'Fake Staff', 'fixed' => 'Fixed Wage', 'paid' => 'Paid Staff', 'access' => 'Webadmin Access Only', 'work_exp' => 'Work Experience');

$the_table = new TemporaryTable();
foreach ($staff_list as $staff_id) {
    $staff                  = staffFromDatabase($staff_id);
    $statement              = timesheetStatementFromDatabase(end(timesheetStatementSearch($staff_id, 'bonus')));
    $new_row                = array();
    $new_row['staff_id']    = $staff_id;
    $new_row['staff_name']  = ucwords(trim($staff['first_name'] . ' ' . $staff['last_name']));
    $new_row['age']         = (0 != $staff['birth_time']) ? formatSecondsToText(time() - $staff['birth_time'], 2) : '-';
    $new_row['staff_type']  = htmlspecialchars($employee_types[$staff['user_type']]);
    $new_row['date_joined'] = (0 != $staff['start_time']) ? date('d/m/Y', $staff['start_time']) : '-';
    $new_row['date_left']   = (0 != $staff['end_time']) ? date('d/m/Y', $staff['end_time']) : '-';
    $new_row['time_here']   = (($staff['end_time'] - $staff['start_time']) > 0) ? formatSecondsToText($staff['end_time'] - $staff['start_time'], 2) : '-';
    $new_row['wage_rate']   = (bccomp('0.00', $staff['pay_rate'], 2) != 0) ? ('$' . convertCurrency($staff['pay_rate'], 'AUD')) : '-';
    $new_row['full_total']  = 0.0;
    $new_row['ytd_total']   = 0.0;
    $last_year              = mktime(idate('H'), idate('i'), idate('s'), idate('m'), idate('d'), idate('Y') - 1);

    if ($query = $DB->query(sprintf(
        "SELECT `amount`, UNIX_TIMESTAMP(`transaction_date`) AS `transaction_time` FROM %s WHERE `amount` > 0 AND `staff_id` = %d",
        "`highwaygroup`.`users_statements`",
        $staff_id
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            if ($row['transaction_time'] > $last_year) {
                $new_row['ytd_total'] += $row['amount'];
            }
            $new_row['full_total'] += $row['amount'];
        }
        $DB->free_result($query);
    }

    $new_row['full_total']        = (0.0 != $new_row['full_total']) ? ('$' . convertCurrency($new_row['full_total'], 'AUD')) : '-';
    $new_row['ytd_total']         = (0.0 != $new_row['ytd_total']) ? ('$' . convertCurrency($new_row['ytd_total'], 'AUD')) : '-';
    $new_row['pay_review_style']  = (0 != $staff['review_time'] && $staff['review_time'] < time()) ? 'background-color:#e9a;' : '';
    $new_row['pay_review_date']   = (0 != $staff['review_time']) ? date('d/m/Y', $staff['review_time']) : '-';
    $new_row['outstanding_style'] = (false !== $statement && $statement['type_balance'] > 0.0) ? 'background-color:#e9a;' : '';
    $new_row['outstanding_bonus'] = (false !== $statement) ? convertCurrency($statement['type_balance'], 'AUD') : '-';
    $new_row['status']            = ('y' == $staff['active']) ? 'Active' : 'Inactive';
    $the_table->Insert($new_row);
}
$super_mode = in_array('USERS', $allowed_sections);
?>
<table class="wide spacetable" align="center">
	<tr>
		<th><a href="<?=$sort_url?>full_name">Staff Name</a><?=('full_name' == $order_by) ? $ord_img : ''?></th>
<?php
if ($super_mode) {
?>
		<th><a href="<?=$sort_url?>birth_date">Age</a><?=('birth_date' == $order_by) ? $ord_img : ''?></th>
<?php
}
?>
		<th><a href="<?=$sort_url?>user_type">Staff Type</a><?=('user_type' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>start_date">Date Joined</a><?=('start_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>end_date">Date Left</a><?=('end_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>time_here">Time at Highway</a><?=('time_here' == $order_by) ? $ord_img : ''?></th>
<?php
if ($super_mode) {
?>
		<th><a href="<?=$sort_url?>pay_rate">Wage Rate</a><?=('pay_rate' == $order_by) ? $ord_img : ''?></th>
		<th>Total</th>
		<th>YTD Total</th>
		<th><a href="<?=$sort_url?>last_date">Pay Review Date</a><?=('last_date' == $order_by) ? $ord_img : ''?></th>
		<th>Outstanding Work Bonus</th>
<?php
}
?>
		<th><a href="<?=$sort_url?>active">Status</a><?=('active' == $order_by) ? $ord_img : ''?></th>
		<th>&nbsp;</th>
	</tr>
<?php
$alt         = false;
$prev_status = 'Active';
$query       = $the_table->Copy();
while ($row = $the_table->Fetch($query)) {
    //Spacer between active/inactive staff
    if ($row['status'] != $prev_status && ('' == $order_by || 'active' == $order_by)) {
?>
	<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>><td colspan="<?=($super_mode) ? '13' : '7'?>"><hr /></td></tr>
<?php
$prev_status = $row['status'];
    }
?>
	<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
		<td style="text-align:center;"><?=htmlspecialchars($row['staff_name'])?></td>
<?php
if ($super_mode) {
?>
		<td style="text-align:center;"><?=htmlspecialchars($row['age'])?></td>
<?php
}
?>
		<td style="text-align:center;"><?=htmlspecialchars($row['staff_type'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($row['date_joined'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($row['date_left'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($row['time_here'])?></td>
<?php
if ($super_mode) {
?>
		<td style="text-align:center;"><?=htmlspecialchars($row['wage_rate'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($row['full_total'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($row['ytd_total'])?></td>
		<td style="text-align:center;<?=$row['pay_review_style']?>"><?=htmlspecialchars($row['pay_review_date'])?></td>
		<td style="text-align:center;<?=$row['outstanding_style']?>"><?=htmlspecialchars($row['outstanding_bonus'])?></td>
<?php
}
?>
		<td style="text-align:center;"><?=htmlspecialchars($row['status'])?></td>
		<td style="text-align:center;">
<?php
if (!isset($posted['return_message'])) {
        if ($super_mode || $row['staff_id'] == $_GLOBALS['staff']['staff_id']) {
?>
			<a href="<?=$edit_url . $row['staff_id']?>" target="_blank">Edit</a>
<?php
}
    } else {
?>
			<a href="<?=$select_url . $row['staff_id']?>">Select</a>
<?php
}
?>
		</td>
	</tr>
<?php
}
?>
</table>
<?php
foreach (array_diff(array_keys($GLOBALS), array_pop($cleanup_list)) as $key) {
    unset($$key);
}

?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
