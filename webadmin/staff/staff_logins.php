<?php
/**
 * @var string[] $all_staff_list
 * @var string[] $active_staff
 * @var string[] $inactive_staff
 */
include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/templates/staff_header.tpl.php';

$username_list = array();

foreach (file(
    $_SERVER['DOCUMENT_ROOT'] . '/webadmin/users/.htpasswd',
    FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
) ?: [] as $row) {
    $username_list[staffFromUser($row[0])][] = $row[0];
}

echo "Testing User Permissions...<br /><br />\n";

foreach ($active_staff as $staff_id => $username) {
    $staff_name = $all_staff_list[$staff_id];
    if (!isset($username_list[$staff_id])) {
        echo "User ID: " . $staff_id . " - " . $staff_name . " is unable to log in - No Password<br />\n";
    } elseif (count($username_list[$staff_id]) > 1) {
        echo "Warning: " . $staff_id . " - " . $staff_name . " has more than one password<br />\n";
    }
}

foreach ($inactive_staff as $staff_id => $username) {
    $staff_name = $all_staff_list[$staff_id];
    if (isset($username_list[$staff_id])) {
        echo "Warning: " . $staff_id . " - " . $staff_name . " is still able to log in<br />\n";
    }
}

if (count($username_list[0])) {
    foreach ($username_list[0] as $username) {
        echo "Warning: " . $username . " is able to log in<br />\n";
    }
}

if ($query = $DB->query(sprintf(
    "SELECT `au`.`user` FROM `highwaygroup`.`admin_users` AS `au` LEFT JOIN `highwaygroup`.`users` AS `u` ON `u`.`user` = `au`.`user` WHERE `u`.`active` <> 'y' GROUP BY `au`.`user`"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        echo "User Name: " . htmlspecialchars($row['user']) . " still has some webadmin permissions<br />\n";
    }
    $DB->free_result($query);
}

if ($query = $DB->query(sprintf(
    "SELECT `as`.`sect_key`, `as`.`section_description`, COUNT(`au`.`user`) AS `user_count` FROM `highwaygroup`.`admin_sections` AS `as` LEFT JOIN `highwaygroup`.`admin_users` AS `au` ON `as`.`section_id` = `au`.`section_id` GROUP BY `as`.`section_id` HAVING `user_count` = 0"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        echo "Permit: " . $row['sect_key'] . ' - ' . $row['section_description'] . " has no permitted users<br />\n";
    }
    $DB->free_result($query);
}

echo "<br />Finished Testing User Permissions<br />\n";

include_once __DIR__ . '/templates/staff_footer.tpl.php';
