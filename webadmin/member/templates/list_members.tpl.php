<?php
/**
 * @var mixed[] $get_vars
 * @var int[]   $member_list
 * @var mixed[] $posted
 */
?><?php

moduleLoad('invoice'); //invoiceChunk, invoiceSearch, invoiceSumValue

?>
<style type="text/css">
/*<![CDATA[*/
.membricon-checked{
    display: inline-block;
    width: 16px;
    height: 16px;
    *margin-right: .3em;
    line-height: 16px;
    vertical-align: text-top;
    background-image: url(data:image/png;base64,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);
    background-position: 16px 16px;
    background-repeat: no-repeat;
    margin-top: 1px;
}
/*]]>*/
</style>
<style type="text/css">
    .locked-spammer{
        background-color: #d4c9c9!important;
    }
</style>
<?php
moduleLoad("invoice");

global $DB;

$alt     = false;
$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" />';

$sort_url           = $get_vars;
$sort_url['action'] = (($sales_list) ? 'sales_' : '') . 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = '.?' . http_build_query($sort_url) . '&order_by=';

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['member_id']);
$edit_url = '.?' . http_build_query($edit_url) . '&member_id=';

$view_url           = $get_vars;
$view_url['action'] = 'view';
unset($view_url['member_id']);
$view_url = '.?' . http_build_query($view_url) . '&member_id=';

$unlock_url           = $get_vars;
$unlock_url['action'] = 'unlock' . (($sales_list) ? '_sales' : '');
unset($unlock_url['member_id']);
$unlock_url = '.?' . http_build_query($unlock_url) . '&member_id=';

$select_url           = $get_vars;
$select_url['action'] = 'select';
unset($select_url['member_id']);
$select_url = '.?' . http_build_query($select_url) . '&member_id=';

$check_staff_list = getStaffList();
$country_list     = memberGetOptionList('country');

?>
<!-- members_list.tpl.php -->
<script type="text/javascript">
//<![CDATA[
function selectAllInputs(elem)
{
	var elems = getElements('member_list[]');
	for(var i = 0;i < elems.length;i++) elems[i].checked = elem.checked;
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
		<th><a href="<?=$sort_url?>date_joined">Date Joined</a><?=('date_joined' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>company_name">Company</a><?=('company_name' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>last_name">Contact Name</a><?=('last_name' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>email_address">Email</a><?=('email_address' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>site_id">Member Type</a><?=('site_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>country">Country</a><?=('country' == $order_by) ? $ord_img : ''?></th>
		<th>IP Address</th>
		<th><a href="<?=$sort_url?>total_logins">Total Visits</a><?=('total_logins' == $order_by) ? $ord_img : ''?></th>
		<th>Last Visit</th>
		<th>Inquries</th>
		<th>Invoices</th>
		<th><a href="<?=$sort_url?>processed_count">Total Sales</a><?=('processed_count' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>processed_total">Sales Value (AUD)</a><?=('processed_total' == $order_by) ? $ord_img : ''?></th>
		<th>Last Contact</th>
		<th><a href="<?=$sort_url?>active">Active</a><?=('active' == $order_by) ? $ord_img : ''?></th>
		<th>&nbsp;</th>
	</tr>
<?php
$site_list    = memberGetOptionList('site_id');
$member_locks = memberGetLocks();

$members_invoices_chunked = array();
if (count($member_list)) {
    $members_invoices_chunked = invoiceChunk(
        invoiceSearch(array("member_id" => $member_list)),
        array('member_id', 'invoice_status')
    );
}

foreach ($member_list as $id) {
    $member = memberFromDatabase($id);
    //$member_lock = memberGetLock($id);
    $member_lock = false;
    if (array_key_exists($id, $member_locks)) {
        $member_lock = $member_locks[$id];
    }

    $member_logins            = memberLoginHistoryFromDatabase($id, '', 0);
    $last_login               = reset($member_logins);
    $member_requests          = memberRequestsFromDatabase($id);
    $member_shippingaddresses = memberShippingAddressesFromDatabase($id);
    $member_notifyparties     = memberNotifyPartiesFromDatabase($id);
    $member_forwarders        = memberForwardersFromDatabase($id);
    $member_warnings          = memberWarningsFromDatabase($id);

    $invoices_chunked = ((array_key_exists($id, $members_invoices_chunked)) ? $members_invoices_chunked[$id] : array()) + array(
        'inquiry'   => array(),
        'enquiry'   => array(),
        'closed'    => array(),
        'sales'     => array(),
        'proforma'  => array(),
        'processed' => array(),
        'cancelled' => array(),
    );

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

    if ("not_active" == $member["status"]) {
        $class = ' class="locked"';
    }
    if ('y' === $member['spammer']) {
        $class = ' class="locked-spammer"';
    }
    $signup_ip = getCountryInfo($member['signup_IP']);
    if ('--' != $signup_ip['a2']) {
        $member['signup_country'] = $signup_ip['a2'];
    }
    $member['name'] = trim($member['first_name'] . ' ' . $member['last_name']);

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

    $email_tooltip = array();

    if ('y' === $member['spammer']) {
        $email_tooltip[] = 'Flagged: Spammer';
    }
    if ('y' === $member['timewaster']) {
        $email_tooltip[] = 'Flagged: Time Waster';
    }
    if ('y' === $member['tester']) {
        $email_tooltip[] = 'Flagged: Tester';
    }

    if (count($member_requests)) {
        $email_tooltip[] = 'Member Requests:';
        foreach ($member_requests as $v) {
            if ('' != $v['comments']) {
                $email_tooltip[] = '-' . $v['comments'];
            }
        }
    }

    if (count($member_warnings)) {
        $email_tooltip[] = 'Member Warnings:';
        foreach ($member_warnings as $v) {
            if ('' != $v['comments']) {
                $email_tooltip[] = '-' . $v['comments'];
            }
        }
    }

    $country_range = array($member['country'], $member["signup_country"]);
    if ($query = $DB->query(sprintf(
        "SELECT `login_ip` FROM `highwaygroup`.`new_members_login_track` WHERE `member_id` = %d",
        $id
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $login_ip = $signup_ip;
            if ($row['login_ip'] !== $member['signup_IP']) {
                $login_ip = getCountryInfo($row["login_ip"]);
            }
            $country_range[] = $login_ip["a2"];
        }
        $DB->free_result($query);
    }

    foreach ($member_shippingaddresses as $member_shippingaddress) {
        $country_range[] = $member_shippingaddress["country"];
    }

    foreach ($member_notifyparties as $member_notifyparty) {
        $country_range[] = $member_notifyparty["country"];
    }

    foreach ($member_forwarders as $v) {
        $country_range[] = $v["country"];
    }

    $country_range = array_unique($country_range);
    foreach ($country_range as $k => $v) {
        if ("--" == $v || "" == $v) {
            unset($country_range[$k]);
        }
    }

    if (count($country_range) > 1) {
        $email_tooltip[] = "Warning : Multiple countries detected - (" . implode(", ", $country_range) . ")";
    }
    $writedate         = $DB->query_first("SELECT `write_date` FROM `highwaygroup`.`invoices` WHERE `member_id` = ".$id." ORDER BY write_date DESC limit 1" );

?>
	<tr style="text-align:center;"<?=$class?>>
<?php
if ('y' == $posted['multiple']) {
?>
		<td><input type="checkbox" name="member_list[]" value="<?=$id?>" /></td>
<?php
}
?>
		<td><?=date(_("d/m/Y"), strtotime($member['created_at']))?></td>
		<td><?=$member['company_name']?></td>
		<td onmouseover="<?=htmlspecialchars('tt_in(' . json_encode($tooltip) . ')')?>" onmouseout="tt_out()"><?=htmlspecialchars($member['name'])?></td>
<?php
if (count($email_tooltip)) {
        $email_tooltip = nl2br(implode("\n", $email_tooltip));
?>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode($email_tooltip) . ')')?>" onmouseout="tt_out()"><div style="border:2px solid #FF1493;"><?=htmlspecialchars($member['email_address'])?></div></td>
<?php
} else {
?>
		<td><?=htmlspecialchars($member['email_address'])?></td>
<?php
}
?>
		<td style="text-align:center;white-space:nowrap;">
<?php
foreach (explode(",", $member['site_id']) as $site_id) {
        if ('' != $site_id) {
?>
			<i class="favicon-<?=$site_id?>" title="<?=htmlspecialchars($site_list[$site_id])?>"></i>
<?php
}
    }
?>
            <?=($member['check_staff_id']) ? sprintf(
        '<i class="membericon-checked" style="background-position:-16px -16px" title="Member Check %s (by %s)"></i>',
        date(_('d/m/Y'), strtotime($member['check_date'])),
        htmlspecialchars($check_staff_list[$member['check_staff_id']])
    ) : ''?>
		</td>
		<td<?=(count($country_range) > 1) ? ' style="background:#f70;"' : ''?>><?=('' != $member['country']) ? ('<i class="flagicon-' . strtolower($member["country"]) . '" title="' . hecho($country_list[$member['country']], 1) . '"></i>') : ''?></td>
		<td<?=(count($country_range) > 1) ? ' style="background:#f70;"' : ''?>><?=('' != $member['signup_country']) ? ('<i class="flagicon-' . strtolower($member["signup_country"]) . '" title="' . hecho($country_list[$member['signup_country']], 1) . '"></i>') : ''?></td>
		<td><?=(count($member_logins)) ? ('<a href=".?action=login&amp;member_id=' . $id . '" target="_blank">' . count($member_logins) . '</a>') : '0'?></td>
		<td><?=($last_login['login_time']) ? date(_('d/m/Y'), $last_login['login_time']) : 'Never'?></td>
		<td><a href="/webadmin/invoice/?status=inquiry|enquiry|closed|cancelled&amp;member_id=<?=$id?>" target="_blank"><?=count($invoices_chunked['inquiry']) + count($invoices_chunked['enquiry']) + count($invoices_chunked['closed']) + count($invoices_chunked['cancelled'])?></a></td>
		<td><a href="/webadmin/invoice/?status=sales|proforma|processed&amp;member_id=<?=$id?>" target="_blank"><?=count($invoices_chunked['sales']) + count($invoices_chunked['proforma']) + count($invoices_chunked['processed'])?></a></td>
		<td><a href="/webadmin/invoice/?status=processed&amp;member_id=<?=$id?>" target="_blank"><?=count($invoices_chunked['processed'])?></a></td>
		<td style="text-align:center;">$<?=convertCurrency(invoiceSumValue($invoices_chunked['processed'], "invoice_offerau"), 'AUD')?></td>
		<td><?= $writedate ? date('d/m/Y',strtotime($writedate['write_date'])):'N/A'?></td>
		<td><?=('active' == $member['status']) ? 'Yes' : 'No'?></td>
		<td>
<?php
if ($popup_window) {
?>
			<input type="button" value=" Select " onclick="window.location='<?=htmlspecialchars($select_url . $id)?>'" />
			<input type="button" value=" Edit " onclick="window.location='<?=htmlspecialchars($edit_url . $id)?>'" />
<?php
} else {
        if (empty($member_lock) || $member_lock['staff_id'] == $staff_id) {
?>
			<a href="<?=htmlspecialchars($edit_url . $id)?>" target="_blank">Edit</a>
<?php
} else {
?>
			<a href="<?=htmlspecialchars($view_url . $id)?>" target="_blank">View</a>
<?php
}
        if (!empty($member_lock) && $member_lock['staff_id'] == $staff_id) {
?>
			<a href="<?=htmlspecialchars($unlock_url . $id)?>">Unlock</a>
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
	<tr style="text-align:center;"<?=($alt = !$alt) ? '' : ' class="alt"'?>>
		<td><input type="checkbox" name="select_all" onclick="selectAllInputs(this)" /><br />Select All</td>
		<td colspan="11"><input type="submit" value="Select Products" /></td>
	</tr>
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
<!-- /list_invoices.tpl.php -->
