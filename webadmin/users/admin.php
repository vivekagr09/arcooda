<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

include_once __DIR__ . '/auth.php';

$action = $_REQUEST['action'] ?? '';

/**
 * @param mixed[][] $user_sections_ref
 * @param string    $user
 * @param int       $section
 *
 * @return bool
 */
function user_has_perm($user_sections_ref, $user, $section)
{
    if (!is_array($user_sections_ref)) {
        return false;
    }

    foreach ($user_sections_ref as $v) {
        if ($v['user'] == $user && $v['section_id'] == $section) {
            return true;
        }
    }

    return false;
}

/**
 * @param string $query
 *
 * @return void
 */
function list_users($query)
{
    global $_GLOBALS;
    global $DB, $pw, $script;

    $users_list = $pw->get_users();
    natcasesort($users_list);
    $users_list = array_values($users_list);

    $sections = array();
    if ($query = $DB->query(sprintf(
        "SELECT * FROM `highwaygroup`.`admin_sections` WHERE FIND_IN_SET('%d', `sites`) ORDER BY `section_description` ASC",
        $_GLOBALS['site_settings']['site_id']
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $sections[$row['section_id']] = $row;
        }
        $DB->free_result($query);
    }

    $user_sections_ref = array();
    if ($query = $DB->query(sprintf(
        "SELECT `user`, `section_id` FROM `highwaygroup`.`admin_users` WHERE `site_id` = %d ORDER BY `user` ASC",
        $_GLOBALS['site_settings']['site_id']
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $user_sections_ref[] = $row;
        }
        $DB->free_result($query);
    }

    // To group sections
    $groupSections = array(
        'all'     => array(),
        'sales'   => array(),
        'support' => array(),
        'man'     => array(),
        'boss'    => array(),
    );

    $groupSectionsColor = array(
        'all'     => '#e5e5e5',
        'sales'   => '#fffdbf',
        'support' => '#bfffc9',
        'man'     => '#bfc5ff',
        'boss'    => '#ffbfbf',
    );

    foreach ($sections as $k => $v) {
        $groupSections['other'][$k]  = $v;
        $groupSectionsColor['other'] = '#e5e5e5';
    }
?>
<script type="text/javascript">
//<![CDATA[
function toggleChecks(keyName, toggle)
{
    var startName = 'field_data[';
    var endName   = '][' + keyName + ']';
    var theElem;

<?php
foreach ($users_list as $user) {
?>
    theElem = document.getElementsByName(startName + '<?=$user?>' + endName);
    if (theElem.length > 0) {
        theElem[0].checked = toggle;
    }
<?php
}
?>
}
//]]>
</script>

<table cellpadding="2" cellspacing="1" style="width:90%;border:none;margin:auto">
    <tr>
        <td>&nbsp;</td>
        <td colspan="<?=count($sections)?>" style="background:#369" style="text-align:center" width="80%"><b style="color:#fff">Sections/Access</b></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td></td>
<?php
    foreach ($groupSections as $group => $sections) {
?>
    <td style="text-align:center;background:<?=$groupSectionsColor[$group]?>" colspan="<?=count($sections)?>"><?=ucwords($group)?></td>
<?php
    }
?>
        <td></td>
        <td></td>
    </tr>
    <tr style="background:#006">
        <td style="text-align:center" nowrap="nowrap"><b style="color:#fff">Admin Users</b></td>
<?php
    foreach ($groupSections as $group => $sections) {
        foreach ($sections as $k => $v) {
?>
        <td style="text-align:center"><b style="color:#fff"><?=$v['section_description']?></b></td>
<?php
        }
    }
?>
        <td style="text-align:center" nowrap="nowrap"><b style="color:#fff">Admin Users</b></td>
        <td style="background:#fff">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align:center" nowrap="nowrap"></td>
<?php
    foreach ($groupSections as $group => $sections) {
        foreach ($sections as $k => $v) {
?>
        <td style="text-align:center" style="background:#369"><input type="checkbox" onclick="toggleChecks(&amp;<?=$v['section_id']?>&amp;, this.checked)" /></td>
<?php
        }
    }
?>
        <td style="background:#fff">&nbsp;</td>
    </tr>
<?php
    $alt = false;
    foreach ($users_list as $v) {
        $name = $v;
        if ($row = $DB->query_first(sprintf(
            "SELECT `first_name`, `last_name` FROM `highwaygroup`.`users` WHERE `user` = %s LIMIT 1",
            $DB->escape($v)
        ), MYSQLI_ASSOC)) {
            $name = trim($row['first_name'] . ' ' . $row['last_name']);
        }
?>
    <tr style="background:<?=($alt = !$alt) ? '#FFF' : '#EEE'?>">
        <td style="text-align:center"><?=htmlspecialchars($name)?></td>
<?php
        foreach ($groupSections as $group => $sections) {
            foreach ($sections as $k2 => $v2) {
?>
        <td style="text-align:center" style="background-color: <?=$groupSectionsColor[$group]?>">
            <input name="field_data[<?=$v?>][<?=$k2?>]" type="checkbox"<?=(user_has_perm($user_sections_ref, $v, $k2)) ? ' checked="checked"' : ''?> />
        </td>
<?php
            }
        }
?>
        <td style="text-align:center"><?=htmlspecialchars($name)?></td>
        <td nowrap="nowrap"><a href="<?=$script?>&amp;action=edit&amp;username=<?=$v?>">Edit</a></td>
    </tr>
<?php
    }
?>
    <tr style="background:#006">
        <td style="text-align:center" nowrap="nowrap"><b style="color:#fff">Admin Users</b></td>
<?php
    foreach ($groupSections as $group => $sections) {
        foreach ($sections as $k => $v) {
?>
        <td style="text-align:center"><b style="color:#fff"><?=$v['section_description']?></b></td>
<?php
        }
    }
?>
        <td style="text-align:center" nowrap="nowrap"><b style="color:#fff">Admin Users</b></td>
        <td style="background:#fff">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align:center" nowrap="nowrap"></td>
<?php
    foreach ($groupSections as $group => $sections) {
        foreach ($sections as $k => $v) {
?>
        <td style="text-align:center" style="background:#369"><input type="checkbox" onclick="toggleChecks(&amp;<?=$v['section_id']?>&amp;, this.checked)" /></td>
<?php
        }
    }
?>
        <td style="background:#fff">&nbsp;</td>
    </tr>
    <tr><td colspan="20" style="text-align:center">
        <input type="hidden" name="action" value="do_quick_edit" />
        <input type="submit" name="do_action" value="Update" />
    </td></tr>
</table>
<?php
}

/**
 * @return void
 */
function do_add()
{
    global $_GLOBALS;
    global $DB;

    global $pw;

    $username = $_POST['username'];
    if (empty($username)) {
        echo 'Must enter a username<br /><br />';
        return;
    }

    $password = $_POST['password'];
    if (empty($username)) {
        echo 'Must enter a password<br /><br />';
        return;
    }

    if ($pw->add_user($username, $password)) {
        $DB->query(sprintf(
            "INSERT INTO %s (`first_name`, `last_name`, `user`, `email`, `date_added`, `home_phone`, `mobile`, `home_email`, `icq_no`) VALUES (%s, %s, %s, %s, NOW(), %s, %s, %s, %s)",
            "`highwaygroup`.`users`",
            $DB->escape($_POST['first_name']),
            $DB->escape($_POST['last_name']),
            $DB->escape($username),
            $DB->escape($_POST['email']),
            $DB->escape($_POST['home_phone']),
            $DB->escape($_POST['mobile']),
            $DB->escape($_POST['home_email']),
            $DB->escape($_POST['icq_no'])
        ));

        $permissions = $_POST['permissions'];
        if (empty($permissions)) {
            return;
        }

        $site_id = $_GLOBALS['site_settings']['site_id'];

        $insert_vals = array();
        foreach ($permissions as $section_id) {
            $insert_vals[] = sprintf(
                "(%s, %d, %d)",
                $DB->escape($username),
                $section_id,
                $site_id
            );
        }

        if (count($insert_vals)) {
            $DB->query(sprintf(
                "INSERT INTO `highwaygroup`.`admin_users` (`user`, `section_id`, `site_id`) VALUES %s",
                implode(", ", $insert_vals)
            ));
        }
        echo 'New User ' . $username . ' added<br /><br />';
    } else {
        echo 'User ' . $username . ' already exists<br /><br />';
    }
}

/**
 * @return void
 */
function do_delete()
{
    global $DB;

    global $pw;

    $username = $_POST['username'];
    if (empty($username)) {
        echo 'Must enter a username<br /><br />';
        return;
    }

    $pw->del_user($username);

    $DB->query(sprintf(
        "DELETE FROM `highwaygroup`.`admin_users` WHERE `user` = %s",
        $DB->escape($username)
    ));
    $DB->query(sprintf(
        "UPDATE `highwaygroup`.`users` SET `active` = 'n' WHERE `user` = %s LIMIT 1",
        $DB->escape($username)
    ));

    echo 'User ' . $username . ' deleted<br /><br />';
}

/**
 * @return void
 */
function do_edit()
{
    global $_GLOBALS;
    global $DB;

    global $pw;

    $username = $_POST['username'];
    if (empty($username)) {
        echo 'Must enter a username<br /><br />';
        return;
    }

    $password = $_POST['password'];
    if (!empty($password)) {
        $pw->set_passwd($username, $password);
    }

    $permissions = $_POST['permissions'];
    if (empty($permissions)) {
        return;
    }

    $site_id = $_GLOBALS['site_settings']['site_id'];

    $insert_vals = array();
    foreach ($permissions as $section_id) {
        $insert_vals[] = sprintf(
            "(%s, %d, %d)",
            $DB->escape($username),
            $section_id,
            $site_id
        );
    }

    $DB->query(sprintf(
        "DELETE FROM `highwaygroup`.`admin_users` WHERE `user` = %s AND `site_id` = %d",
        $DB->escape($username),
        $site_id
    ));

    if (count($insert_vals)) {
        $DB->query(sprintf(
            "INSERT INTO `highwaygroup`.`admin_users` (`user`, `section_id`, `site_id`) VALUES %s",
            implode(", ", $insert_vals)
        ));
    }

    echo 'User ' . $username . ' updated<br /><br />';
}

/**
 * @return void
 */
function do_quick_edit()
{
    global $_GLOBALS;
    global $DB;

    $site_id = $_GLOBALS['site_settings']['site_id'];

    $insert_vals = array();
    foreach ($_POST['field_data'] as $user => $section_list) {
        foreach ($section_list as $section_id => $on) {
            $insert_vals[] = sprintf(
                "(%s, %d, %d)",
                $DB->escape($user),
                $section_id,
                $site_id
            );
        }
    }

    if (count($insert_vals)) {
        $DB->query(sprintf(
            "DELETE FROM `highwaygroup`.`admin_users` WHERE `site_id` = %d",
            $site_id
        ));
        $DB->query(sprintf(
            "INSERT INTO `highwaygroup`.`admin_users` (`user`, `section_id`, `site_id`) VALUES %s",
            implode(", ", $insert_vals)
        ));
    }

    echo 'Update OK';
}

$user             = $_GLOBALS['staff']['user'];
$allowed_sections = get_allowed_sections($user);

if (has_permission('USERS', $allowed_sections)) {
    include_once __DIR__ . '/header.inc.php';
    include_once __DIR__ . '/pwfile.php';

    $script = '?';

    $pw         = new htaccess($_SERVER['DOCUMENT_ROOT'] . '/webadmin/users/.htpasswd');
    $users_list = $pw->get_users();
    $gets       = '&' . http_build_query($_GET);
?>
<form action="<?=$script . $gets?>&amp;query=" method="post" name="admin_form">
    <br />
    <table width="100%" border="0" cellspacing="2" cellpadding="2"><tr>
        <td style="background:#EEE" width="20%"><div style="text-align:center"><b>Add / Edit Permissions</b></div></td>
        <td style="background:#EEE" width="80%"><div style="text-align:right">
            <!--<a href="<?=$script?>&amp;action=add">Add New User</a> |-->
            <a href="<?=$script?>&query=">List Users Permissions (<?=count($users_list)?>)</a>
        </div></td>
    </tr></table>
    <br />
    <br />
<?php
    if (isset($action)) {
        switch ($action) {
            case 'add':
                include __DIR__ . '/add_form.inc.php';
                break;
            case 'edit':
                include __DIR__ . '/edit_form.inc.php';
                break;
            case 'delete':
                include __DIR__ . '/delete_form.inc.php';
                break;
            case 'do_add':
                do_add();
                break;
            case 'do_edit':
                do_edit();
                break;
            case 'do_delete':
                do_delete();
                break;
            case 'do_quick_edit':
                do_quick_edit();
                break;
        }
    }

    if (array_key_exists('query', $_REQUEST)) {
        list_users(trim($_REQUEST['query']));
    }

    include_once __DIR__ . '/footer.inc.php';
}
?>
</form>
