<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$sort_url = $get_vars;
unset($sort_url['order_by']);
unset($sort_url['order']);
$sort_url = '?' . http_build_query($sort_url);

$select_url           = $get_vars;
$select_url['action'] = 'select';
unset($select_url['voucher_id']);
$select_url = 'index.php?' . http_build_query($select_url);

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['voucher_id']);
$edit_url = 'index.php?' . http_build_query($edit_url);

$unlock_url           = $get_vars;
$unlock_url['action'] = 'unlock';
$unlock_url           = 'index.php?' . http_build_query($unlock_url);
$unlock_url .= '&voucher_id=';

$staff_id = $_GLOBALS['staff']['staff_id'];
?>
<table class="wide" style="width:95%" align="center">
	<tr>
		<th><a href="<?=$sort_url?>&order_by=id&order=<?=('id' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Voucher ID</a></th>
		<th><a href="<?=$sort_url?>&order_by=date_added&order=<?=('date_added' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Date Added</a></th>
		<?=(!$site_id) ? '<th>Site</th>' : ''?>
		<th><a href="<?=$sort_url?>&order_by=member_name&order=<?=('member_name' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Offered To</a></th>
		<th>Value</th><th style="width:36pt;">Terms (Purchase Value)</th>
		<th><a href="<?=$sort_url?>&order_by=date_added&order=<?=('date_added' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Status</a></th>
		<th>Expiry Date</th>
		<th style="width:36pt;">&nbsp;</th>
	</tr>
<?php
$alt = true;
foreach ($voucher_list as $voucher_id) {
    $voucher      = voucherFromDatabase($voucher_id);
    $voucher_lock = voucherGetLock($voucher_id);

    $class = '';
    if ($alt = !$alt) {
        $class = 'alt';
    }

    if (false !== $voucher_lock) {
        $class = 'locked';
    }

    if ($voucher_lock['staff_id'] == $staff_id) {
        $class = 'mylock';
    }

    $links = array();
    if ($popup_window) {
        $links[] = '<a href="' . $select_url . '&voucher_id=' . $voucher_id . '">Select</a>';
    }

    $links[] = '<a href="' . $edit_url . '&voucher_id=' . $voucher_id . '" target="_blank">Edit</a>';
    if (false !== $product_lock && $voucher_lock['staff_id'] == $staff_id) {
        $links[] = '<a href="' . $unlock_url . $voucher_id . '">Unlock</a>';
    }

?>
<tr style="text-align:center;" class="<?=$class?>">
	<td><?=voucherFormatNumber($voucher_id)?></td>
	<td><?=($voucher['time_added']) ? date('d/m/Y', $voucher['time_added']) : ''?>
	<?=(!$site_id) ? '<td>' . $site_list[$voucher['site_id']] . '</td>' : ''?>
	<td><?=$voucher['member_name']?></td>
	<td><?=$voucher['amount'] . ' ' . $voucher['currency']?></td>
	<td><?=$terms[$voucher['terms']]?></td>
	<td><?=strtoupper($voucher['status'])?></td>
	<td><?=($voucher['expiry_time']) ? date('d/m/Y', $voucher['expiry_time']) : ''?></td>
	<td><?=implode(' | ', $links)?></td>
</tr>
<?php
}
?>
</table>

<br />
<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
