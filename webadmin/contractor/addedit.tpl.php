<!-- addedit.tpl.php -->
<script type="text/javascript">
    function deletecontractor() {
        var ask = window.confirm("Are you sure you want to delete this contractor?");
        if (ask) {
            $('#deletecontractor').submit();

        }
    }
</script>
<?php
$form_action = 'list';
if ('edit' == $action) {
    $form_action = 'do_edit';
}

if ('add' == $action) {
    $form_action = 'do_add';
}

if ('view' == $action) {
    $form_action = 'view';
}
?>

<form accept-charset="utf-8" autocomplete="off" method="post" action=".?action=<?=$form_action?>">
	<input type="hidden" name="contractor[contractor_id]" value="<?=$contractor_id?>" />
<?php
if ('view' != $action) {
    // need to display the changes that were made in the last 5 mins or so.
    if ('add' != $action) {
        $query = $DB->query(sprintf(
            "SELECT UNIX_TIMESTAMP(`message_date`) AS `message_time`, `message_notes` FROM `contractor_messages` AS `mm` WHERE `mm`.`member_id` = %d AND `mm`.`message_date` > %s AND `mm`.`staff_id` = %d ORDER BY `mm`.`message_date` DESC",
            $contractor_id, formatSQLDateTime(time() - 255), $staff_id
        ));
        if ($DB->num_rows($query)) {
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<?php
            while ($message = $DB->fetch_assoc($query)) {
                $shade = substr('00' . dechex(min(255, (time() - $message['message_time']))), -2);
                $notes = explode(" ", $message['message_notes']);
                foreach ($notes as $key => $string) {
                    $notes[$key] = trim(chunk_split($string, 80));
                }

                $notes = implode(" ", $notes);
?>
<div style="color:#FF<?=$shade . $shade?>;text-align:left;width:95%;">
	<li><?=nl2br(hecho($notes, 1))?></li>
</div>
<br /><br />
<?php
            }
        }
    }
?>

<?php
$contractor_lock['staff_id']=$_GLOBALS["staff"]["id"];
    if ($contractor_lock['staff_id'] != $_GLOBALS["staff"]["id"] && 'add' != $action) {
?>
<div class="wide" style="border:1px solid black;font-weight:bold;background-color:pink;text-align:center;padding:8px;">
<?php
    if (false === $contractor_lock) {
        echo 'This contractor is not locked to you; You cannot submit any changes.';
    } else {
        $time_to_live = $contractor_lock['expiry_time'] - time();
        echo 'This contractor is currently locked to ' . $all_staff_list[$contractor_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
    }
?>
</div>
<br /><br />
<?php
    }
}

switch ($form_action) {
    case 'view':
    case 'do_edit':
        include __DIR__ . "/summary.tpl.php";
        include __DIR__ . "/details.tpl.php";
        include __DIR__ . "/contacts.tpl.php";
        include __DIR__ . "/comments.tpl.php";
        include __DIR__ . "/attachments.tpl.php";
        include __DIR__ . "/requests.tpl.php";
        include __DIR__ . "/direct_email.tpl.php";
        include __DIR__ . "/direct_email_history.tpl.php";
        include __DIR__ . "/contractor_detail.tpl.php";
        include __DIR__ . '/status.tpl.php';
        break;
    case 'do_add':
        include __DIR__ . "/details.tpl.php";
        include __DIR__ . "/contacts.tpl.php";
        include __DIR__ . "/comments.tpl.php";
        include __DIR__ . "/requests.tpl.php";
        include __DIR__ . "/direct_email.tpl.php";
        include __DIR__ . "/contractor_detail.tpl.php";
        include __DIR__ . '/status.tpl.php';
        break;
    default:
        break;
}

?>
<br />

<?php
if ('view' != $action) {
?>
<input type="button" value="Submit Changes"<?=($contractor_lock['staff_id'] == $staff_id || 'add' == $action) ? ' onclick="this.form.submit()"' : ' disabled="disabled"'?> />


<?php
}
if($form_action == 'do_edit'){
?>
<input type="button" value="Delete" style="float: right; margin-right: 60px;" onclick="deletecontractor()" />
<?php
}
?>

</form>
<?php
if($form_action == 'do_edit'){
?>
<form accept-charset="utf-8" autocomplete="off" method="post" action=".?action=deletecontractor" id="deletecontractor">
    <input type="hidden" name="contractor_id" value="<?=$contractor_id?>" />
</form>
<?php
}
?>
<!-- /addedit.tpl.php -->