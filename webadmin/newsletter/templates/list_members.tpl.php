<!-- list_members.tpl -->

<?php
$alt = false;
$ord_img = '<img src="/webadmin/images/' . (($order == 'desc') ? 'desc' : 'asc') . '.png" />';

$sort_url = $get_vars;
$sort_url['action'] = (($sales_list) ? 'sales_' : '') . 'list';
$sort_url['order'] = ($order == 'desc') ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = '?' . http_build_query($sort_url) . '&order_by=';

$edit_url = $get_vars;
$edit_url['action'] = 'edit_member';
unset($edit_url['member_id']);
unset($edit_url['site_id']);
$edit_url = 'index.php?' . http_build_query($edit_url) . '&member_id=';

$view_url = $get_vars;
$view_url['action'] = 'view_member';
unset($view_url['member_id']);
$view_url = 'index.php?' . http_build_query($view_url) . '&member_id=';

$unlock_url = $get_vars;
$unlock_url['action'] = 'unlock_member';
unset($unlock_url['member_id']);
$unlock_url = 'index.php?' . http_build_query($unlock_url) . '&member_id=';

$select_url = $get_vars;
$select_url['action'] = 'select_member';
unset($select_url['member_id']);
$select_url = 'index.php?' . http_build_query($select_url) . '&member_id=';
$member_locks = memberGetLocks();
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
if ($posted['multiple'] == 'y')
{
?>
		<th>Select</th>
<?php
}
?>
		<th><a href="<?php echo $sort_url ?>date_joined">Date Joined</a><?php echo ($order_by == 'date_joined') ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>company_name">Company</a><?php echo ($order_by == 'company_name') ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>last_name">Contact Name</a><?php echo ($order_by == 'last_name') ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>email_address">Email</a><?php echo ($order_by == 'email_address') ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>site_id">Member Type</a><?php echo ($order_by == 'site_id') ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>country">Country</a><?php echo ($order_by == 'country') ? $ord_img : '' ?></th>
		<th>IP Address</th>
		<th>Last Visit</th>
		<th><a href="<?php echo $sort_url ?>total_orders">Total Orders</a><?php echo ($order_by == 'total_orders') ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>total_logins">Total Visits</a><?php echo ($order_by == 'total_logins') ? $ord_img : '' ?></th>
		<th><a href="<?php echo $sort_url ?>active">Active</a><?php echo ($order_by == 'active') ? $ord_img : '' ?></th>
		<th>&nbsp;</th>
	</tr>
<?php
$site_list = memberGetOptionList('site_id');
$staff_id = $_GLOBALS['staff']['id'];
foreach($member_list as $member_id)
{
	$member = memberFromDatabase($member_id);
	$member_lock = false;
	if (array_key_exists($member_id, $member_locks)) {
        $member_lock = true;
    }
	// $member_lock = memberGetLock($member_id);
	$member_logins = memberLoginHistoryFromDatabase($member_id, '', 0);
	$invoice_list = invoiceSearch(array('member_id' => $member_id, 'invoice_status' => array('sales', 'proforma', 'processed')));

	$last_login = reset($member_logins);
	$member['order_total'] = invoiceSumValue($invoice_list, 'offer_price_au');

	$class = '';
	if ($alt = !$alt) $class = ' class="alt"';
	if ($member_lock !== false) $class = ' class="locked"';
	if ($member_lock['staff_id'] == $staff_id) $class = ' class="mylock"';

	$row = getCountryInfo($member['signup_IP']);
	if ($row['a2'] != '--') $member['signup_country'] = $row['a2'];

	$member['name'] = $member['first_name'] . ' ' . $member['last_name'];

	$bill_array = array(
		($member['company_name'] != '') ? (trim($member['company_name'])) : (trim($member['name'])),
		($member['company_name'] != '' && $member['name'] != '' && $member['name'] != $member['company_name']) ? ('Attn: ' . $member['name']) : '',
		trim($member['address']),
		ltrim(trim($member['city'] . (($member['state'] != '') ? ', ' . $member['state'] : '') . ' ' . $member['postcode']), ','),
		trim($country_list[$member['country']]),
		"\r",
		($member['phone_no'] != '') ? ('Ph: ' . $member['phone_no']) : '',
		($member['email_address'] != '') ? ('Email: ' . $member['email_address']) : '',
	);
	foreach($bill_array as $key => $value) if ($value == '') unset($bill_array[$key]);
	$tooltip = nl2br(implode("\n", $bill_array));
?>
	<tr<?php echo $class ?> style="text-align:center;">
<?php
	if ($posted['multiple'] == 'y')
	{
?>
		<td><input type="checkbox" name="member_list[]" value="<?php echo $member_id ?>" /></td>
<?php
	}
?>
		<!--<td><?php echo memberFormatNumber($member_id) ?></td>-->
		<td><?php echo date("d/m/y", $member['time_joined']) ?></td>
		<td><?php echo $member['company_name'] ?></td>
		<td onmouseover="tt_in(<?php echo formatJSString($tooltip) ?>)" onmouseout="tt_out()"><?php echo trim($member['first_name'] . ' ' . $member['last_name']) ?></td>
		<td><?php hecho($member['email_address']) ?></td>
		<td>
<?php
	foreach(explode(",", $member['site_id']) as $site_id) if ($site_id != '')
	{
?>
			<img src="/webadmin/images/sites/site_<?php echo $site_id ?>/logo_16.png" alt="<?php hecho($site_list[$site_id]) ?>" />
<?php
	}
?>
		</td>
		<td><i class="flagicon-<?php echo strtolower($member["country"]) ?>"></i></td>
		<td><i class="flagicon-<?php echo strtolower($member["signup_country"]) ?>"></i></td>
		<td><?php echo ($last_login['login_time']) ? date('d/m/Y', $last_login['login_time']) : 'Never' ?></td>
		<td><?php echo (count($invoice_list)) ? ('<a href="/webadmin/invoice/?status=sales|proforma|processed&amp;member_id=' . $member_id . '" target="_blank">' . count($invoice_list) . '</a>') : '0' ?></td>
		<td><?php echo (count($member_logins)) ? ('<a href="index.php?action=login&amp;member_id=' . $member_id . '" target="_blank">' . count($member_logins) . '</a>') : '0' ?></td>
		<td><?php echo ($member['status'] == 'active') ? 'Yes' : 'No' ?></td>
<!--

		<td style="text-align:center;"><?php echo convertCurrency($member['order_total'], 'AUD') ?></td>
		<td style="text-align:center;"><?php echo ($member['time_last_contacted'] == 0) ? 'J ' . date("d/m/y", $member['time_joined']) : date("d/m/y", $member['time_last_contacted']) ?></td>
-->
		<td>
<?php
	if ($popup_window)
	{
?>
			<input type="button" value=" Select " onclick="window.location='<?php echo $select_url . $member_id ?>'" />
			<input type="button" value=" Edit " onclick="window.location='<?php echo $edit_url . $member_id ?>'" />
<?php
	}
	else
	{
		if (true || $member_lock === false || $member_lock['staff_id'] == $staff_id)
		{
?>
			<a href="<?php echo $edit_url . $member_id ?>" target="_blank">Edit</a>
<?php
		}
		else
		{
?>
			<a href="<?php echo $view_url . $member_id ?>" target="_blank">View</a>
<?php
		}
		if ($member_lock !== false && $member_lock['staff_id'] == $staff_id)
		{
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
if ($posted['multiple'] == 'y')
{
?>
	<tr style="text-align:center;"<?php echo ($alt = !$alt) ? '' : ' class="alt"' ?>><td><input type="checkbox" name="select_all" onclick="selectAllInputs(this)" /><br />Select All</td><td colspan="11"><input type="submit" value="Select Products" /></td></tr>
<?php
}
?>
</table>

<?php
if (!$popup_window)
{
?>
<br />
<?php
}
?>

<!-- /list_members.tpl -->
