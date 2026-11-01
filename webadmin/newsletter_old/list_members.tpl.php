<!-- list_members.tpl -->

<?php
moduleLoad('invoice'); //invoiceSearch, invoiceSumValue
moduleLoad('member'); //memberGetOptionList, memberFromDatabase, memberGetLock, memberLoginHistoryFromDatabase, memberFormatNumber

$alt     = false;
$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" />';

$sort_url           = $get_vars;
$sort_url['action'] = (($sales_list) ? 'sales_' : '') . 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = '?' . http_build_query($sort_url) . '&order_by=';

$edit_url           = $get_vars;
$edit_url['action'] = 'edit_member';
unset($edit_url['member_id']);
$edit_url = 'index.php?' . http_build_query($edit_url) . '&member_id=';

$view_url           = $get_vars;
$view_url['action'] = 'view_member';
unset($view_url['member_id']);
$view_url = 'index.php?' . http_build_query($view_url) . '&member_id=';

$unlock_url           = $get_vars;
$unlock_url['action'] = 'unlock_member';
unset($unlock_url['member_id']);
$unlock_url = 'index.php?' . http_build_query($unlock_url) . '&member_id=';

$select_url           = $get_vars;
$select_url['action'] = 'select_member';
unset($select_url['member_id']);
$select_url = 'index.php?' . http_build_query($select_url) . '&member_id=';
?>
<script type="text/javascript">
//<![CDATA[
function selectAllInputs(elem)
{
	var elems=getElements('member_list[]');
	for(var i=0;i<elems.length;i++) elems[i].checked=elem.checked;
}
//]]>
</script>

<table class="wide spacetable">
	<tr>
<?php
if ('y' == $posted['multiple']) {
?>
		<th>Select</th>
<?php
}
?>
		<th><a href="<?php echo $sort_url ?>created">Date Joined</a><?php echo ('created' == $order_by) ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>company_name">Company</a><?php echo ('company_name' == $order_by) ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>last_name">Contact Name</a><?php echo ('last_name' == $order_by) ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>email_address">Email</a><?php echo ('email_address' == $order_by) ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>member_type">Member Type</a><?php echo ('member_type' == $order_by) ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>country">Country</a><?php echo ('country' == $order_by) ? $ord_img : '' ?></th>
		<th>IP Address</th>
		<th>Last Visit</th>
		<th><a href="<?php echo $sort_url ?>total_orders">Total Orders</a><?php echo ('total_orders' == $order_by) ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>total_logins">Total Visits</a><?php echo ('total_logins' == $order_by) ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>active">Active</a><?php echo ('active' == $order_by) ? $ord_img : '' ?></th>
		<th>&nbsp;</th>
	</tr>
<?php
$type_list = memberGetOptionList('member_type');
$site_list = memberGetOptionList('site_id');
$staff_id  = $_GLOBALS['staff']['id'];
foreach ($member_list as $member_id) {
    $member        = memberFromDatabase($member_id);
    $member_lock   = memberGetLock($member_id);
    $member_logins = memberLoginHistoryFromDatabase($member_id, '', 0);
    $invoice_list  = invoiceSearch(array('member_id' => $member_id, 'invoice_status' => array('sales', 'proforma', 'processed')));

    $last_login            = reset($member_logins);
    $member['order_total'] = invoiceSumValue($invoice_list, 'offer_price_au');

    $class = '';
    if ($alt = !$alt) {
        $class = ' class="alt"';
    }

    if (!empty($member_lock)) {
        $class = ' class="locked"';
    }

    if (!empty($member_lock) && $member_lock['staff_id'] == $staff_id) {
        $class = ' class="mylock"';
    }

    $row = getCountryInfo($member['signup_IP']);
    if ('--' != $row['a2']) {
        $member['signup_country'] = $row['a2'];
    }

    $member['name'] = $member['first_name'] . ' ' . $member['last_name'];

    $bill_array = array(
        ('' != $member['company_name']) ? (trim($member['company_name'])) : (trim($member['name'])),
        ('' != $member['company_name'] && '' != $member['name'] && $member['name'] != $member['company_name']) ? ('Attn: ' . $member['name']) : '',
        trim($member['address']),
        ltrim(trim($member['city'] . (('' != $member['state']) ? ', ' . $member['state'] : '') . ' ' . $member['postcode']), ','),
        trim($country_list[$member['country']]),
        "\r",
        ('' != $member['phone_no']) ? ('Ph: ' . $member['phone_no']) : '',
        ('' != $member['email_address']) ? ('Email: ' . $member['email_address']) : '',
    );
    foreach ($bill_array as $key => $value) {
        if ('' == $value) {
            unset($bill_array[$key]);
        }
    }

    $tooltip = nl2br(implode("\n", $bill_array));
?>
	<tr<?php echo $class ?> style="text-align:center;">
<?php
if ('y' == $posted['multiple']) {
?>
		<td><input type="checkbox" name="member_list[]" value="<?php echo $member_id ?>" /></td>
<?php
}
?>
		<!--<td><?php echo memberFormatNumber($member_id) ?></td>-->
		<td><?php echo date("d/m/y", $member['created_time']) ?></td>
		<td><?php echo $member['company_name'] ?></td>
		<td onmouseover="<?php hecho("tt_in(" . jecho($tooltip, 1) . ")")?>" onmouseout="tt_out()"><?php hecho(trim($member['first_name'] . ' ' . $member['last_name']))?></td>
		<td><?php hecho($member['email_address'])?></td>
		<td>
<?php
foreach (explode(",", $member['member_type']) as $site_id) {
        if ('' != $site_id) {
            $site_name = $type_list[$site_id];
            if (0 == $site_id) {
                $site_id = array_search($site_name, $site_list);
            }

            if (0 != $site_id) {
?>
			<img src="/webadmin/images/sites/site_<?php echo $site_id ?>/logo_16.png" alt="<?php hecho($site_name)?>" />
<?php
} else {
?>
			<img src="images/<?php echo $site_id ?>.png" alt="<?php hecho($site_name)?>" />
<?php
}
        }
    }

?>
		</td>
		<td><?php echo ('' != $member['country']) ? ('<img src="/webadmin/images/flags/' . strtolower($member['country']) . '.gif" alt="' . hecho($member['country'], 1) . '" title="' . $country_list[$member['country']] . '" />') : '' ?></td>
		<td><?php echo ('' != $member['signup_country']) ? ('<img src="/webadmin/images/flags/' . strtolower($member['signup_country']) . '.gif" alt="' . hecho($member['signup_country'], 1) . '" title="' . $country_list[$member['signup_country']] . '" />') : '' ?></td>
		<td><?php echo ($last_login['login_time']) ? date('d/m/Y', $last_login['login_time']) : 'Never' ?></td>
		<td><?php echo (count($invoice_list)) ? ('<a href="/webadmin/invoice/?status=sales|proforma|processed&amp;member_id=' . $member_id . '" target="_blank">' . count($invoice_list) . '</a>') : '0' ?></td>
		<td><?php echo (count($member_logins)) ? ('<a href="index.php?action=login&amp;member_id=' . $member_id . '" target="_blank">' . count($member_logins) . '</a>') : '0' ?></td>
		<td><?php echo ('active' == $member['status']) ? 'Yes' : 'No' ?></td>
<!--

		<td style="text-align:center;"><?php echo convertCurrency($member['order_total'], 'AUD') ?></td>
		<td style="text-align:center;"><?php echo (0 == $member['time_last_contacted']) ? 'J ' . date("d/m/y", $member['created_time']) : date("d/m/y", $member['time_last_contacted']) ?></td>
-->
		<td>
<?php
if ($popup_window) {
?>
			<input type="button" value=" Select " onclick="window.location='<?php echo $select_url . $member_id ?>'" />
			<input type="button" value=" Edit " onclick="window.location='<?php echo $edit_url . $member_id ?>'" />
<?php
} else {
        if (empty($member_lock) || $member_lock['staff_id'] == $staff_id) {
?>
			<a href="<?php echo $edit_url . $member_id ?>" target="_blank">Edit</a>
<?php
} else {
?>
			<a href="<?php echo $view_url . $member_id ?>" target="_blank">View</a>
<?php
}
        if (!empty($member_lock) && $member_lock['staff_id'] == $staff_id) {
?>
			<a href="<?php echo $unlock_url . $member_id ?>">Unlock</a>
<?php
}
    }
?>
		</td>
	</tr>
<?php
}
if ('y' == $posted['multiple']) {
?>
	<tr style="text-align:center;"<?php echo ($alt = !$alt) ? '' : ' class="alt"' ?>><td><input type="checkbox" name="select_all" onclick="selectAllInputs(this)" /><br />Select All</td><td colspan="11"><input type="submit" value="Select Products" /></td></tr>
<?php
}
?>
</table>

<?php
if (!$popup_window) {
?>
<br />
<?php
}
?>

<!-- /list_members.tpl -->
