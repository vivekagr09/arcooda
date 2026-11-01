<?php
/**
 * @var string $action
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $_GLOBALS;
global $DB;

moduleLoad('staff');

if (!isset($cleanup_list)) {
    $cleanup_list = array();
}

$cleanup_list[] = array_keys($GLOBALS);

$form_action = 'list';
if ('edit' == $action) {
    $form_action = 'do_edit';
}

if ('add' == $action) {
    $form_action = 'do_add';
}

$warn_password = true;
$pw_file       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/users/.htpasswd';
$passwords     = file($pw_file);
foreach ($passwords as $row) {
    $row = explode(':', trim($row));
    if ($row[0] == $staff['user'] && '' != $staff['user']) {
        $warn_password = false;
    }
}
?>
<script type="text/javascript">
//<![CDATA[
function validateStaff()
{
<?php
if ($warn_password) {
?>
	if ('y' == getValue('active') && '' == getValue('login_password')) {
		return confirm('You have not chosen a password.\nThe user will not be unable to login until you have set a password.\n\nDo you still wish to continue?');
    }
<?php
}
?>
	return true;
}
function staffTogglePassword(showMe)
{
	var theElem = getElement('staff_password');
	if (theElem == null) {
        return;
    }
	theElem.style.display = (showMe) ? '' : 'none';
}
//]]>
</script>

<form method="post" action=".?action=<?=$form_action?>" enctype="multipart/form-data">

<?php
foreach (staffHistorySearch([
    'staff_id'        => $staff_id,
    'message_type_id' => STAFF_HISTORY_TYPE_DETAIL_CHANGE,
]) as $message_id) {
    $message = staffHistoryFromDatabase($message_id);
    if ($message['message_time'] < time() - 255) {
        break;
    }

    $shade = substr('00' . dechex(min(255, (time() - $message['message_time']))), -2);
?>
<div style="color:#FF<?=$shade . $shade?>;text-align:left;width:95%;">
	<?=nl2br(htmlspecialchars($message['message_notes']))?><br /><br />
</div>
<?php
}

switch ($form_action) {
    case 'do_edit':
        include __DIR__ . '/staff_personal.tpl.php';
        include __DIR__ . '/staff_taxinfo.tpl.php';
        include __DIR__ . '/staff_bankinfo.tpl.php';
        include __DIR__ . '/staff_superinfo.tpl.php';
        include __DIR__ . '/staff_yearsummary.tpl.php';
        if (in_array('USERS', $allowed_sections)) {
            include __DIR__ . '/staff_administration.tpl.php';
            include __DIR__ . '/admin_history.tpl.php';
            include __DIR__ . '/staff_history.tpl.php';
        } else {
            include __DIR__ . '/staff_website_preferences.tpl.php';
        }
        break;
    case 'do_add':
        include __DIR__ . '/staff_personal.tpl.php';
        include __DIR__ . '/staff_taxinfo.tpl.php';
        include __DIR__ . '/staff_bankinfo.tpl.php';
        include __DIR__ . '/staff_superinfo.tpl.php';
        if (in_array('USERS', $allowed_sections)) {
            include __DIR__ . '/staff_administration.tpl.php';
        }

        break;
    default:
        break;
}
if (in_array('USERS', $allowed_sections)) {
?>
<p>Status: <select style="width:auto;" name="active" onchange="javascript:staffTogglePassword(this.value=='y');">
	<?=formatHTMLOptions(array('y' => 'Active', 'n' => 'Inactive'), $staff['active'])?>
</select></p>

<table id="staff_password" style="<?=('n' == $staff['active']) ? 'display:none;' : ''?>width:50%">
<?php
if ($staff_id > 0) {
?>
	<tr>
		<td class="left_title">Login Username</td>
		<td><?=htmlspecialchars($staff['user'])?></td>
	</tr>
<?php
}
?>
	<tr>
		<td class="left_title" style="width:25%;">Login Password<?=($warn_password) ? '*' : ''?></td>
		<td><input class="intext" name="login_password" value="" /></td>
	</tr>
</table>

<br /><br />

<input type="button" value="Submit Changes" onclick="javascript:if (validateStaff()) this.form.submit();" />
<?php
}
?>
</form>
<?php
foreach (array_diff(array_keys($GLOBALS), array_pop($cleanup_list)) as $key) {
    unset($$key);
}

?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
