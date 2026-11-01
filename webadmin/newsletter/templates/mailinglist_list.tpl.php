<!-- mailinglist_list.tpl.php -->

<?php
$alt=false;
$ord_img='<img src="/webadmin/images/'.(($order=='desc')?'desc':'asc').'.png" />';

$sort_url=$get_vars;
$sort_url['action']=$action;
$sort_url['order']=($order=='desc')?'asc':'desc';
unset($sort_url['order_by']);
$sort_url='index.php?'.http_build_query($sort_url).'&order_by=';

$toggle_url=$get_vars;
$toggle_url['action']='toggle';
unset($toggle_url['id']);
$toggle_url='index.php?'.http_build_query($toggle_url).'&id=';

$edit_url=$get_vars;
$edit_url['action']='edit';
unset($edit_url['id']);
$edit_url='index.php?'.http_build_query($edit_url).'&id=';

$view_url=$get_vars;
$view_url['action']='view';
unset($view_url['id']);
$view_url='index.php?'.http_build_query($view_url).'&id=';

$unlock_url=$get_vars;
$unlock_url['action']='unlock';
unset($unlock_url['id']);
$unlock_url='index.php?'.http_build_query($unlock_url).'&id=';

$select_url=$get_vars;
$select_url['action']='select';
unset($select_url['id']);
$select_url='index.php?'.http_build_query($select_url).'&id=';

$country_list = memberGetOptionList('country');
//$status_list = memberGetOptionList('status');
$site_list = memberGetOptionList('site_id');
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?php echo formatHTMLTitle('<a href="#" onclick="$(\'#sectionList\').add(\'#sectionList_hideNote\').toggle(\'slow\');return false">Newsletter List</a>') ?>

<div id="sectionList">
	<table class="wide spacetable">
		<tr>

			<th>Date Joined</th>
			<th><a href="<?php echo $sort_url ?>company_name">Company</a><?php echo ($order_by=='company_name')?$ord_img:'' ?></th>
			<th><a href="<?php echo $sort_url ?>first_name">Contact Name</a><?php echo ($order_by=='first_name')?$ord_img:'' ?></th>
			<th><a href="<?php echo $sort_url ?>email_address">Email</a><?php echo ($order_by=='email_address')?$ord_img:'' ?></th>
			<th><a href="<?php echo $sort_url ?>site_id">Member Type</a><?php echo ($order_by=='site_id')?$ord_img:'' ?></th>
			<th><a href="<?php echo $sort_url ?>country">Country</a><?php echo ($order_by=='country')?$ord_img:'' ?></th>


			<th>Total Orders</th>
			<th>Total Visits</th>
			<th><a href="<?php echo $sort_url ?>status">Active</a><?php echo ($order_by=='status')?$ord_img:'' ?></th>
			<th>Status</th>
			<th>&nbsp;</th>
		</tr>
<?php
foreach($item_list as $mailinglist_item)
{
	$member_id=$mailinglist_item['member_id'];
	$member = memberFromDatabase($member_id);
	$member_logins = memberLoginHistoryFromDatabase($member_id, '', 0);
	$invoice_list = invoiceSearch(array('member_id' => $member_id, 'invoice_status' => array('sales', 'proforma', 'processed')));

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

	$class=(($alt=!$alt)?' class="alt"':'');
?>
		<tr<?php echo $class ?>>
			<td style="text-align:center;"><?php echo date("d/m/y", $member['time_joined']) ?></td>
			<td style="text-align:center;"><a href="/webadmin/members/?action=edit&amp;member_id=<?php echo $member['member_id'] ?>" target="_blank"><?php hecho($member['company_name']) ?></a></td>
			<td style="text-align:center;" onmouseover="<?php hecho('tt_in(' . formatJSString($tooltip) . ')') ?>" onmouseout="tt_out()"><a href="/webadmin/members/?action=edit&amp;member_id=<?php echo $member['member_id'] ?>" target="_blank"><?php hecho(trim($member['first_name'] . ' ' . $member['last_name'])) ?></a></td>
			<td style="text-align:center;"><?php hecho($member['email_address']) ?></td>
			<td style="text-align:center;">
<?php
	foreach(explode(",",$member['site_id']) as $site_id) if($site_id != '')
	{
?>
				<img src="/webadmin/images/sites/site_<?php echo $site_id ?>/logo_16.png" alt="<?php hecho($site_list[$site_id]) ?>" />
<?php
	}
?>
			</td>
			<td style="text-align:center;"><?php echo ($member['country'] != '') ? ('<img src="/webadmin/images/flags/' . strtolower($member['country']) . '.gif" alt="' . formatHTMLSafe($member['country']) . '" title="' . $country_list[$member['country']] . '" />') : '' ?></td>

			<td style="text-align:center;"><?php echo (count($invoice_list)) ? ('<a href="/webadmin/invoice/?status=sales|proforma|processed&amp;member_id=' . $member_id . '" target="_blank">' . count($invoice_list) . '</a>') : '0' ?></td>
			<td style="text-align:center;"><?php echo (count($member_logins)) ? ('<a href="/webadmin/members/?action=login&amp;member_id=' . $member_id . '" target="_blank">' . count($member_logins) . '</a>') : '0' ?></td>
			<td style="text-align:center;"><?php echo ($member['status'] == "not_active") ? "No" : "Yes" ?></td>
			<td style="text-align:center;"><?php hecho(ucfirst($mailinglist_item['email_status'])) ?></td>
			<td style="text-align:center;">
<?php
	if($popup_window)
	{
		if($mailinglist_item['email_status']=='error')
		{
?>
				<input type="button" value=" Retry " class="unfinished" onclick="window.location='<?php echo $select_url.$id ?>'" />
<?php
		}
?>
				<input type="button" value=" Remove " class="unfinished" onclick="window.open('<?php echo $edit_url.$id ?>');" />
<?php
	}
	else
	{
		if($mailinglist_item['email_status'] == 'error')
		{
?>
				<a href="<?php echo $edit_url.$id ?>" class="unfinished" target="_blank">Retry</a>
<?php
		}
		if($mailinglist_item['email_status'] == 'listed')
		{
?>
				<a href="<?php echo $view_url.$id ?>" class="unfinished" target="_blank">Remove</a>
<?php
		}
?>
				<a href="/webadmin/members/?action=edit&amp;member_id=<?php echo $member['member_id'] ?>" target="_blank">Edit</a>
<?php
	}
?>
			</td>
		</tr>
<?php
}
?>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionList_hideNote">List of Newsletters can be viewed by Clicking "Newsletter List" above<br /></div>

<!-- /mailinglist_list.tpl.php -->