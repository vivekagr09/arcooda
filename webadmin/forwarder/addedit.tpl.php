<!-- addedit.tpl -->

<?php
$form_action = 'list';
if ('edit' == $action) {
    $form_action = 'do_edit';
} elseif ('add' == $action) {
    $form_action = 'do_add';
}
?>

<script type="text/javascript">
//<[![CDATA
function validateForwarder()
{
	return true;
}
//]]>
</script>

<form accept-charset="utf-8" autocomplete="off" method="post" action=".?action=<?=$form_action?>">
<?php
if ($item_lock['staff_id'] != $staff_id && 'add' != $action) {
?>
<table class="wide" align="center" style="border:1px solid black;">
	<tr><td style="font-weight:bold;background-color:pink;text-align:center;padding:8px 8px 8px 8px;">
<?php
if (false === $item_lock) {
        echo 'This forwarder is not locked to you; You cannot submit any changes.';
    } else {
        $time_to_live = $item_lock['expiry_time'] - time();
        echo 'This forwarder is currently locked to ' . $all_staff_list[$item_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
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
	<input type="hidden" name="forwarder[id]" value="<?=intval($id)?>" />
<?php
    case 'do_add':
        include __DIR__ . '/office.tpl.php';
        include __DIR__ . '/warehouse.tpl.php';
        include __DIR__ . '/contact.tpl.php';
        include __DIR__ . '/accounts.tpl.php';
        include __DIR__ . '/quotes.tpl.php';
        include __DIR__ . '/comments.tpl.php';
        break;
    default:
        break;
}

?>
<input type="button" value="Submit Changes"<?=($item_lock['staff_id'] == $staff_id || 'add' == $action) ? ' onclick="if(validateForwarder()) this.form.submit();"' : ' disabled="disabled"'?> />
<input type="button" value="Print" onclick="window.print();" />
</form>

<!-- /addedit.tpl -->
