<?php
/**
 * @var mixed[]  $posted
 * @var mixed[]  $get_vars
 * @var string[] $allowed_sections
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/templates/staff_header.tpl.php';

$summarize = false;
if ('y' == $posted['summarize']) {
    $summarize = true;
}

$show_status = (isset($posted['show_status'])) ? $posted['show_status'] : 1;
$user_name   = $_GLOBALS['staff']['user'];
$page        = intval($posted['page']);
$order_by    = (isset($posted['order_by'])) ? $posted['order_by'] : 'full_name';
$order       = $posted['order'];
if ('desc' != $order) {
    $order = 'asc';
}

$staff_list = staffSearch($show_status);
$staff_list = staffSort($staff_list, $order_by, ('desc' == $order) ? SORT_DESC : SORT_ASC);

$items_per_page = 30;
$count_total    = count($staff_list);
$staff_list     = array_chunk($staff_list, $items_per_page);
$count_pages    = count($staff_list);
$page           = max(0, min($count_pages - 1, $page));
$staff_list     = $staff_list[$page];
$count_display  = count($staff_list);

?>

<?=formatHTMLTitle('staffs')?>

<?php
if ($count_total > 0) {
    $sort_url           = $get_vars;
    $sort_url['action'] = 'list';
    unset($sort_url['order_by']);
    unset($sort_url['order']);
    $sort_url = '.?' . http_build_query($sort_url);

    $edit_url           = $get_vars;
    $edit_url['action'] = 'edit';
    unset($edit_url['staff_id']);
    $edit_url = '.?' . http_build_query($edit_url);

    $view_url           = $get_vars;
    $view_url['action'] = 'view';
    unset($view_url['staff_id']);
    $view_url = '.?' . http_build_query($view_url);

    $select_url           = $get_vars;
    $select_url['action'] = 'select';
    unset($select_url['staff_id']);
    $select_url = '.?' . http_build_query($select_url);

    $delete_url           = $get_vars;
    $delete_url['action'] = 'delete';
    unset($delete_url['staff_id']);
    $delete_url = '.?' . http_build_query($delete_url);

    $page_url           = $get_vars;
    $page_url['action'] = 'list';
    unset($page_url['page']);
    $page_url = '.?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $items_per_page);

    if ($summarize) {
?>
<table class="spacetable" align="center">
	<tr>
		<th><a href="<?=$sort_url?>&order_by=full_name&order=<?=('full_name' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Staff Name</a></th>
		<th><a href="<?=$sort_url?>&order_by=start_date&order=<?=('start_date' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Date Joined</a></th>
		<th><a href="<?=$sort_url?>&order_by=end_date&order=<?=('end_date' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Date Left</a></th>
		<th><a href="<?=$sort_url?>&order_by=time_here&order=<?=('time_here' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Time at Highway</a></th>
		<th><a href="<?=$sort_url?>&order_by=pay_rate&order=<?=('pay_rate' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Wage Rate</a></th>
		<th><a href="<?=$sort_url?>&order_by=pay_total&order=<?=('pay_total' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">YTD Total</a></th>
		<th><a href="<?=$sort_url?>&order_by=active&order=<?=('active' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Status</a></th>
		<th></th>
	</tr>
<?php
        $alt = false;
        foreach ($staff_list as $staff_id) {
            $staff = staffFromDatabase($staff_id);
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?> style="height:32px;">
		<td style="text-align:center;"><?=htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name'])?></td>
		<td style="text-align:center;"><?=($staff['start_time'] > 0) ? date('d/m/Y', $staff['start_time']) : '-'?></td>
		<td style="text-align:center;"><?=($staff['end_time'] > 0) ? date('d/m/Y', $staff['end_time']) : '-'?></td>
		<td style="text-align:center;">
			<?=($staff['years_here'] > 0) ? ($staff['years_here'] . ' yrs') : ''?>
			<?=($staff['months_here'] > 0) ? ($staff['months_here'] . ' mths') : ''?>
			<?=($staff['days_here'] > 0) ? ($staff['days_here'] . ' days') : ''?>
		</td>
		<td style="text-align:center;">$<?=$staff['pay_rate']?></td>
		<td class="unfinished" style="text-align:center;">$<?=$staff['pay_rate']?></td>
		<td style="text-align:center;"><?=('y' == $staff['active']) ? 'Active' : 'Inactive'?></td>
	</tr>
<?php
}
?>
</table>
<?php
} else {
?>
<table class="spacetable" align="center">
	<tr>
<!--		<th><a href="<?=$sort_url?>&order_by=staff_id&order=<?=('staff_id' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Staff ID</a></th> -->
		<th><a href="<?=$sort_url?>&order_by=full_name&order=<?=('full_name' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Name</a></th>
		<th><a href="<?=$sort_url?>&order_by=email&order=<?=('email' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Email</a></th>
		<th><a href="<?=$sort_url?>&order_by=home_phone&order=<?=('home_phone' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Home Phone</a></th>
		<th><a href="<?=$sort_url?>&order_by=mobile&order=<?=('mobile' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Mobile Phone</a></th>
		<th><a href="<?=$sort_url?>&order_by=icq_no&order=<?=('icq_no' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">ICQ Number</a></th>
		<th></th>
	</tr>
<?php
foreach ($staff_list as $staff_id) {
            $staff = staffFromDatabase($staff_id);
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?> style="height:32px;">
<!--		<td style="text-align:center;"><?=$staff['id']?></td> -->
		<td style="text-align:center;"><?=htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($staff['email'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($staff['home_phone'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($staff['mobile'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($staff['icq_no'])?></td>
		<td style="text-align:center;">
<?php
if (!isset($posted['return_message'])) {
                if (in_array('USERS', $allowed_sections) || $staff['user'] == $_GLOBALS['staff']['user']) {
?>
			<a href="<?=$edit_url?>&staff_id=<?=$staff_id?>">Edit</a>
<?php
}
                if (in_array('USERS', $allowed_sections)) {
?>
<!--
			<a href="<?=$delete_url?>&staff_id=<?=$staff_id?>" target="_blank">Delete</a>
			<a href="<?=$view_url?>&staff_id=<?=$staff_id?>" target="_blank">View</a>
-->
<?php
}
            } else {
?>
			<a href="<?=$select_url?>&staff_id=<?=$staff_id?>">Select</a>
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
}
?>
<p align="center">* Do not call to report software errors unless you can describe the problem and have submitted a descriptive <a href="/webadmin/softjob/">software job</a></p>
<p align="center"><?=$page_links?></p>
<?php
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/templates/staff_footer.tpl.php';
die();
