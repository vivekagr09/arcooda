<?php
/***********************
 * Invoice Permissions Page Logic
 * Filename: invoice_permissions.php
 ***********************
 * Purpose:
 *    Do all the relevant stuff for managing permissions for invoice section
 ***********************/
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
?>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%">
			<div align="center">Invoice Permissions<?=($config['debug']) ? '<br />(Debug Mode)' : ''?></div>
		</td>
		<td class="pageMenu" width="80%"><div align="right"></div></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
</table>

<div id="inqcontent">

<div><h2 style="text-align:center;">Add/Edit Invoice Permissions</h2></div>
<?php
$messages = array();
$time_now = time();
$posted   = array_merge($_GET, $_POST);

// Handle action request
switch ($posted['action']) {
    case 'Update':
        resetPermissions($posted['data1']);
        $content = listInvoicePermissions();
        break;

    default:
        $content = listInvoicePermissions();
}
?><div><?php
if (count($messages) > 0) {
    foreach ($messages as $msg) {
        echo $msg . "<br />\n";
    }
}

?></div>
<?=$content?>
<!-- start common footer -->
<?php
include_once __DIR__ . '/footer.inc.php';

//Functions:
function resetPermissions($permit_list)
{
    global $DB, $messages;
    //$DB->Query("TRUNCATE TABLE invoice_permissions");
    $DB->query("DELETE FROM invoice_permissions WHERE 1");
    if (count($permit_list) > 0) {
        foreach ($permit_list as $user_id => $permits) {
            foreach ($permits as $section_id => $value) {
                if ('on' == $value) {
                    $DB->Query("INSERT INTO invoice_permissions(user,section_id) VALUES(" . intval($user_id) . "," . formatSQLString($section_id) . ")");
                }
            }
        }
    }

    $messages[] = "Updated permissions";
}
function listInvoicePermits()
{
    $permits      = array();
    $producttypes = getBaseProductTypes();
    foreach ($producttypes as $key => $name) {
        $permits[$key] = ucfirst($name);
    }

    //Swap the order of some of the permissions, just for Steve
    $neworder    = array(2, 3, 1, 4);
    $new_permits = array();
    foreach ($neworder as $key) {
        if (isset($permits[$key])) {
            $new_permits[$key] = $permits[$key];
            unset($permits[$key]);
        }
    }

    if (count($permits) > 0) {
        foreach ($permits as $key => $name) {
            $new_permits[$key] = $name;
        }
    }

    return $new_permits;
}
function listInvoicePermissions()
{
    global $_GLOBALS;
    global $DB;

    ob_start();
    $permits = listInvoicePermits();
    $admins  = getStaffList();
    asort($admins);
    foreach ($admins as $key => $name) {
        if (!$DB->query_first(sprintf(
            "SELECT 1 FROM `highwaygroup`.`admin_users` AS `au` INNER JOIN `highwaygroup`.`users` AS `u` ON `u`.`user` = `au`.`user` INNER JOIN `highwaygroup`.`admin_sections` AS `as` ON `as`.`section_id` = `au`.`section_id` WHERE `au`.`site_id` = %d AND `u`.`id` = %s AND `as`.`sect_key` = 'INQUIRY' LIMIT 1",
            $_GLOBALS['site_settings']['site_id'],
            $key
        ), MYSQLI_ASSOC)) {
            unset($admins[$key]);
        }
    }

    //Build a blank permissions table
    $admin_permit = array();
    foreach ($admins as $key1 => $name1) {
        $admin_permit[$key1] = array();
        foreach ($permits as $key2 => $name2) {
            $admin_permit[$key1][$key2] = false;
        }

    }
    //Find the saved permissions
    $query = $DB->query("SELECT * FROM invoice_permissions");
    while ($row = $DB->fetch_array($query)) {
        if (isset($admins[$row['user']])) {
            $admin_permit[$row['user']][$row['section_id']] = true;
        }
    }

?>
<div align="center">
<form accept-charset="utf-8" action="" method="post">
<table>
	<tr>
		<th height="32px">Admin Users</th>
<?php
    foreach ($permits as $name) {
        echo '		<th width="' . (100 / (count($permits) + 3)) . '%">' . str_replace('<', '&lt;', $name) . "</th>\n";
    }

?>
		<th width="<?=100 / (count($permits) + 3)?>%">Product Manager</th>
		<th width="<?=100 / (count($permits) + 3)?>%">Freight Manager</th>
		<th width="<?=100 / (count($permits) + 3)?>%">Adminstrator</th>
		<th></th>
	</tr>
<?php
    foreach ($admin_permit as $key1 => $permit) {
?>
<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
	<td style="white-space:nowrap;" height="32px"><?=str_replace('<', '&lt;', $admins[$key1])?></td>
<?php
        foreach ($permit as $key2 => $value) {
            if (5 != $key2 && 6 != $key2 && 7 != $key2) {
?>
	<td style="text-align:center;"><input name="data1[<?=$key1?>][<?=$key2?>]" type="checkbox"<?=($value) ? ' checked="checked"' : ''?> /></td>
<?php
            }
        }
?>
	<td style="text-align:center;"><input name="data1[<?=$key1?>][6]" type="checkbox" <?=(6 == $key2) ? ' checked="checked"' : ''?> /></td>
	<td style="text-align:center;"><input name="data1[<?=$key1?>][5]" type="checkbox" <?=(5 == $key2) ? ' checked="checked"' : ''?> /></td>
	<td style="text-align:center;"><input name="data1[<?=$key1?>][7]" type="checkbox" <?=(7 == $key2) ? ' checked="checked"' : ''?> /></td>
	<td class="unfinished">Edit</td>
</tr>
<?php
    }
?>
</table>
<div style="text-align:center;"><input type="submit" name="action" value="Update" /></div>
</form>
</div>
<?php
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}
