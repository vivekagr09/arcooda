<?php
/**
 * @var string  $action
 * @var int     $member_id
 * @var mixed[] $posted
 */
?>
<!-- addedit_members.tpl.php -->

<?php
global $_GLOBALS;
global $DB;

if (array_key_exists('action', $posted) && 'error' == $posted['action']) {
?>
<div style="color:red;text-align:left;padding:5px;">Error : Either First Name wasn't entered or the Email Address entered already exists in the system.</div><br />
<?php
}
$query = $DB->query(sprintf(
    "SELECT UNIX_TIMESTAMP(`message_date`) AS `message_time`, `message_notes` FROM `highwaygroup`.`new_members_messages` WHERE `member_id` = %d AND `message_date` > %s ORDER BY `message_date` DESC",
    $member_id,
    formatSQLDateTime(time() - 255)
));
$count = 0;
while ($message = $DB->fetch_assoc($query)) {
    if (!$count) {
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<?php
}
    $shade = substr('00' . dechex(min(255, (time() - $message['message_time']))), -2);
?>
<div style="color:#FF<?=$shade . $shade?>;text-align:left;width:95%;">
	<?=nl2br(hecho($message['message_notes'], 1))?><br /><br />
</div>
<?php
$count++;
}
if ('edit' == $action && (empty($member_lock) || $member_lock['staff_id'] != $_GLOBALS['staff']['staff_id'])) {
?>
<div style="border:1px solid black;font-weight:bold;background-color:pink;text-align:center;padding:8px;">
<?php
    if (empty($member_lock)) {
        echo 'This Member is not locked to you; You cannot submit any changes.';
    } else {
        $time_to_live = $member_lock['expiry_time'] - time();
        echo 'This Member is currently locked to ' . $all_staff_list[$member_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
    }
?>
</div>
<br /><br />
<?php
}

$add_array  = array('details', 'contacts', 'shipping', 'notify', 'couriers', 'comments', 'special_freight', 'requests', 'warnings', 'preferences', 'status');
$edit_array = array('summary', 'details', 'contacts', 'shipping', 'notify', 'couriers', 'statement', 'orders', 'serial_numbers', 'wanted', 'survey', 'login', 'comments', 'attachments', 'special_freight', 'requests', 'warnings', 'preferences', 'mailing', 'status');

$use_array = (('add' == $action) ? $add_array : $edit_array);
if ('y' == $_GLOBALS['staff']['developer']) {
    $use_array[] = "authentication";
}

foreach ($use_array as $include) {
    if ('' != $include) {
        include __DIR__ . '/members_' . $include . '.tpl.php';
    }
}

if ("edit" == $action) {
?>
<table style="width:auto;margin:auto;">
	<tr>
		<td style="text-align:center;">
			<a href=".?action=bill_ship&amp;member_id=<?=$member_id?>" target="_blank" title="Open Bill to Ship to" onclick="window.open(this.href, '_blank', 'width=404,height=626,toolbar=0,resizable=0');return false"><img src="/webadmin/images/icon_shipto.png" alt="Open Bill to Ship to" /></a>
		</td>
		<td style="text-align:center;">
			<a href=".?action=ship_notify&amp;member_id=<?=$member_id?>" target="_blank" title="Open Ship to Notify" onclick="window.open(this.href, '_blank', 'width=404,height=626,toolbar=0,resizable=0');return false"><img src="/webadmin/images/icon_notify.png" alt="Open Ship to Notify" /></a>
		</td>
	</tr>
</table>
<br />
<br />
<?php
}
?>
<br />
<div style="text-align:center"><input style="font-size:16px;width:150px;" type="submit" value="<?=('add' == $action) ? 'Add' : 'Update'?>"<?=('add' == $action || (!empty($member_lock) && $member_lock['staff_id'] == $_GLOBALS['staff']['staff_id'])) ? ' onclick=""' : ' disabled="disabled"'?> /></div>
