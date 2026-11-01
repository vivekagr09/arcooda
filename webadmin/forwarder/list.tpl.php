<!-- list.tpl -->

<?php
$alt     = false;
$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" />';

$temp_url         = $get_vars;
$temp_url['null'] = 'void';
unset($temp_url['id']);
unset($temp_url['action']);
$edit_url   = '.?action=edit&' . http_build_query($temp_url) . '&id=';
$view_url   = '.?action=view&' . http_build_query($temp_url) . '&id=';
$delete_url = '.?action=delete&' . http_build_query($temp_url) . '&id=';
$unlock_url = '.?action=unlock&' . http_build_query($temp_url) . '&id=';
$select_url = '.?action=select&' . http_build_query($temp_url) . '&id=';
unset($temp_url['order_by']);
$temp_url['order'] = ('desc' == $order) ? 'asc' : 'desc';
$sort_url          = '.?action=list&' . http_build_query($temp_url) . '&order_by=';

$country_list = forwarderGetOptionList('country');

?>
<table class="wide spacetable" align="center">
	<tr>
		<th><a href="<?=$sort_url?>name">Name of Forwarder</a><?=('name' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>country">Country</a><?=('country' == $order_by) ? $ord_img : ''?></th>
		<th>Last Contact</th>
		<th>Quotes</th>
		<th>Bookings</th>
		<th>&nbsp;</th>
	</tr>
<?php

foreach ($item_list as $id) {
    $item          = forwarderFromDatabase($id);
    $item_lock     = forwarderGetLock($id);
    $item_quotes   = forwarderQuotesFromDatabase($id);

    $last_contact = 0;
    $item_bookings = array();
    foreach ($item_quotes as $k => $v) {
        if ($v['time'] > $last_contact) $last_contact = $v['time'];
        if ($v['confirmed_flag'] == 'y') $item_bookings[] = $v;
    }

    $class = '';
    if ($alt = !$alt) {
        $class = ' class="alt"';
    }

    if (false !== $item_lock) {
        $class = ' class="locked"';
    }

    if ($item_lock['staff_id'] == $staff_id) {
        $class = ' class="mylock"';
    }

?>
	<tr<?=$class?>>
		<td style="text-align:center;"><?php hecho($item['name'])?></td>
		<td style="text-align:center;"><?php hecho($country_list[$item['country']])?></td>
		<td style="text-align:center;"><?=(0 != $last_contact) ? date('d/m/Y', $last_contact) : 'None'?></td>
		<td style="text-align:center;"><?=count($item_quotes)?></td>
		<td style="text-align:center;"><?=count($item_bookings)?></td>
		<td style="text-align:center;">
<?php
    if (array_key_exists('jsonp', $_REQUEST)) {
?>
			<a href="<?=$select_url . $item['id']?>">Select</a>
<?php
    }
    if (false === $item_lock || $item_lock['staff_id'] == $staff_id) {
?>
			<a href="<?=$edit_url . $item['id']?>" target="_blank">Edit</a>
			<a href="<?=$delete_url . $item['id']?>">Del</a>
<?php
    } else {
?>
			<a href="<?=$view_url . $item['id']?>" target="_blank">View</a>
<?php
    }
    if (false !== $item_lock && $item_lock['staff_id'] == $staff_id) {
?>
			<a href="<?=$unlock_url . $item['id']?>">Unlock</a>
<?php
    }
?>
		</td>
	</tr>
<?php
}
?>
</table>

<br />
<span style="font-style:italic;">Notes:</span><br />
Rows marked in <span style="color:red;">red</span> are locked by another user<br />
Rows marked in <span style="color:blue;">blue</span> are locked by you<br />

<!-- /list.tpl -->
