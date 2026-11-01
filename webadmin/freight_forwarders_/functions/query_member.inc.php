<?php
/***********************
 * Member Query Functions
 * Filename: members.inc.php
 ***********************
 * Purpose:
 *    Dump for member-related functions.
 ***********************/

require_once "{$_SERVER['DOCUMENT_ROOT']}/webadmin/new_suppliers/functions/user.inc.php";

function searchMembers($search_string, $name_email_only = false, $and = false)
{
    global $DB;
    $ids = array();

    if (!is_array($search_string)) {
        $search_string = explode(" ", $search_string);
    }

    if ($and) {
        $ids   = array();
        $count = 1;
        foreach ($search_string as $search) {
            if (!$name_email_only) {
                $DB->query("SELECT member_id FROM `highwaygroup`.`new_members` WHERE company LIKE '%" . $search . "%' OR member_id LIKE '" . $search . "'");
                while ($row = $DB->fetch_array()) {
                    $ids[$row['member_id']] |= $count;
                }

            }
            $DB->query("SELECT member_id FROM `highwaygroup`.`new_members_contacts` WHERE `name` LIKE '%" . $search . "%' OR `email` LIKE '%" . $search . "%'");
            while ($row = $DB->fetch_array()) {
                $ids[$row['member_id']] |= $count;
            }

            $DB->query("SELECT member_id FROM `highwaygroup`.`new_members_shipping` WHERE email LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%'");
            while ($row = $DB->fetch_array()) {
                $ids[$row['member_id']] |= $count;
            }

            $count *= 2;
        }
        $count -= 1;
        foreach ($ids as $key => $found) {
            if ($found != $count) {
                unset($ids[$key]);
            } else {
                $ids[$key] = $key;
            }

        }
        sort($ids);
        return $ids;
    }

    sort($ids);

    return $ids;
}
