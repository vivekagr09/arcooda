<?php
$form_action = 'list';
if ('edit' == $action) {
    $form_action = 'do_edit';
}

if ('add' == $action) {
    $form_action = 'do_add';
}

?>
<!-- forwarders_addedit.tpl.php -->

<script type="text/javascript">
function validateForwarder()
{
	return true;
}
</script>

<form method="post" action="index.php?action=<?php echo $form_action ?>">
<?php
if ('add' != $action && false) {
    $query = $DB->query(
        "SELECT UNIX_TIMESTAMP(`message_date`) AS `message_time`, `message_type_id`, `message_title`, `message_notes` FROM `highwaygroup`.`freight_services_messages` WHERE	`product_id` = " . intval($forwarder_id) . " AND `message_date` > " . formatSQLDateTime(time() - 255) . " AND `message_sender` = " . intval($staff_id) . " ORDER BY `message_date` DESC"
    );
    if ($DB->num_rows($query)) {
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<?php
while ($message = $DB->fetch_assoc($query)) {
            $shade = substr('00' . dechex(min(255, (time() - $message['message_time']))), -2);
?>
<div style="color:#FF<?php echo $shade . $shade ?>;text-align:left;width:95%;">
	<li><?=htmlspecialchars($message['message_title'])?> : <?=nl2br(htmlspecialchars($message['message_notes']))?></li>
</div>
<br /><br />
<?php
}
    }
}

if ($forwarder_lock['staff_id'] != $staff_id && 'add' != $action) {
?>
<table class="wide" align="center" style="border:1px solid black;">
	<tr><td style="font-weight:bold;background-color:pink;text-align:center;padding:8px 8px 8px 8px;">
<?php
if (false === $forwarder_lock) {
        echo 'This forwarder is not locked to you; You cannot submit any changes.';
    } else {
        $time_to_live = $forwarder_lock['expiry_time'] - time();
        echo 'This forwarder is currently locked to ' . $all_staff_list[$forwarder_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
    }
?>
	</td></tr>
</table>
<br /><br />
<?php
}

switch ($form_action) {
    case 'do_edit':
?>
	<input type="hidden" name="forwarder[id]" value="<?php echo intval($forwarder_id) ?>" />
<?php
case 'do_add':
        include 'forwarders_office.tpl.php';
        include 'forwarders_warehouse.tpl.php';
        include 'forwarders_contact.tpl.php';
        include 'forwarders_accounts.tpl.php';
        include 'forwarders_quotes.tpl.php';
        include 'forwarders_comments.tpl.php';
        break;
    default:
        break;
}

?>
<input type="button" value="Submit Changes"<?php echo ($forwarder_lock['staff_id'] == $staff_id || 'add' == $action) ? ' onclick="javascript:if(validateForwarder()) this.form.submit();"' : ' disabled="disabled"' ?> />
<input type="button" value="Print" onclick="javascript:window.print();" />
</form>

<!-- /forwarders_addedit.tpl.php -->
