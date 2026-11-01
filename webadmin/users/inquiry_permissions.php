<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/users/auth.php';

$user             = $_GLOBALS['staff']['user'];
$allowed_sections = get_allowed_sections($user);

if (has_permission('USERS', $allowed_sections)) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
    include __DIR__ . '/pwfile.php';

    if (isset($posted['action']) && 'do_edit' == $posted['action']) {
        $DB->query("TRUNCATE TABLE `highwaygroup`.`inquiry_permissions`");
        foreach ($posted['permission'] as $staff => $array) {
            foreach ($array as $id => $on) {
                $DB->query("INSERT INTO `highwaygroup`.`inquiry_permissions` SET staff=" . $DB->escape($staff) . ", section=" . $id);
            }
        }
    }

    $pw         = new htaccess($_SERVER['DOCUMENT_ROOT'] . "/webadmin/users/.htpasswd");
    $users_list = $pw->get_users();
    natcasesort($users_list);

    $sections    = array(1 => 'General Staff', 2 => 'Admin');
    $res         = $DB->query("SELECT * FROM `highwaygroup`.`inquiry_permissions`");
    $permissions = array();
    foreach ($users_list as $user) {
        $permissions[$user] = array();
    }

    while ($row = $DB->fetch_assoc($res)) {
        $permissions[$row['staff']][] = $row['section'];
    }

?>
<form method="post" action="?action=do_edit">
<table cellpadding="2" cellspacing="0" border="1" bordercolor="#CCCCCC" style="width:90%;border:1px solid #CCCCCC" align="center">
	<tr style="background-color:#000066;color:white;text-align:center">
		<td><b>Admin Users</b></td>
<?php
foreach ($sections as $id => $section) {
?>
		<td><b><?=$section?></b></td>
<?php
}
?>
	</tr>
<?php
$count = 0;
    foreach ($users_list as $user) {
?>
	<tr style="text-align:center;<?=($count % 2) ? 'background:#eee' : ''?>">
		<td><?=getStaffName($user)?></td>
<?php
foreach ($sections as $id => $section) {
?>
		<td><input type="checkbox" name="permission[<?=$user?>][<?=$id?>]"<?=(in_array($id, $permissions[$user])) ? ' checked="checked"' : ''?> /></td>
<?php
}
?>
	</tr>
<?php
$count++;
    }
?>
</table>
<br />
<div class="text-center"><input type="submit" value=" Update " /></div>
</form>
<?php
} else {
    die("You don't have persmission to access this section.");
}

