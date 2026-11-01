<?php
include_once __DIR__ . '/query_member.inc.php';

if (!defined('MEMBER_MAILINGLIST_TABLENAME')) {
    define('MEMBER_MAILINGLIST_TABLENAME', '`highwaygroup`.`new_members_mailing_list`');
}
if (!defined('MEMBER_MAILINGLIST_PRIMARYKEY')) {
    define('MEMBER_MAILINGLIST_PRIMARYKEY', 'id');
}
if (!defined('MEMBER_MAILINGLIST_SECONDARYKEY')) {
    define('MEMBER_MAILINGLIST_SECONDARYKEY', 'member_id');
}

if (!function_exists('memberMailingListFromDatabase')) {
    /**
     * @param int $member_id
     *
     * @return mixed[][]
     */
    function memberMailingListFromDatabase($member_id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `member_id` = %d",
            MEMBER_MAILINGLIST_TABLENAME,
            $member_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['style_id']] = memberMailingListVirtual($row);
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('memberMailingListSearch')) {
    /**
     * @param mixed $criteria
     *
     * @return int[]
     */
    function memberMailingListSearch($criteria = '')
    {
        global $DB;

        if (is_string($criteria) && '' != $criteria) {
            $criteria = array('any_text' => $criteria);
        }

        $field_types = getTableFields(MEMBER_MAILINGLIST_TABLENAME);
        $fields      = array();
        foreach ($field_types as $row) {
            $fields[$row[0]] = 'm.`' . $row[0] . '`';
        }

        //$fields=array_merge($fields,memberGetOptionList('ext_fields'));
        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "FIND_IN_SET(" . formatSQLString($sub_value) . "," . $fields[$field] . ")";
                                }
                            } else {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = $fields[$field] . "=" . formatSQLString($sub_value);
                                }
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }

                    } else {
                        if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                            $whereAND[] = "FIND_IN_SET(" . formatSQLString($value) . "," . $fields[$field] . ")";
                        } else {
                            $whereAND[] = $fields[$field] . "=" . formatSQLString($value);
                        }

                    }
                    unset($fields[$field]);
                }
            }
        }

        $search_for = array();
        if (is_array($criteria) && is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_array($criteria) && is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('m.member_id', 'm.style_id');
            foreach ($search_for as $findme) {
                $whereOR = array();
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE " . formatSQLLikeString($findme);
                    }
                }

                if (count($whereOR) > 0) {
                    $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                }

            }
        }

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT `m`.`member_id` FROM %s AS `m`%s ORDER BY `m`.`member_id` ASC",
            MEMBER_MAILINGLIST_TABLENAME,
            (count($whereAND)) ? sprintf(
                " WHERE %s",
                implode(" AND ", $whereAND)
            ) : ''
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row['member_id'];
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists('memberMailingListSignup')) {
    /**
     * @param mixed[]   $posted
     * @param int       $site_id
     * @param int[]|int $style_id
     *
     * @return int
     */
    function memberMailingListSignup($posted, $site_id = 0, $style_id = 0)
    {
        global $DB;

        $member_id = 0;
        $eml       = trim($posted['email_address']);
        if ('' != $eml) {
            if ($row = $DB->query_first(sprintf(
                "SELECT `member_id` FROM `highwaygroup`.`new_members` WHERE `email_address` = %s LIMIT 1",
                $DB->escape($eml)
            ), MYSQLI_ASSOC)) {
                $member_id = intval($row['member_id']);
            }
        }

        $ip      = $_SERVER['REMOTE_ADDR'];
        $country = getCountryInfo($ip);
        $flag    = ('' != $country['a2'] && '' != $posted['country'] && $country['a2'] == $posted['country']) ? 'green' : 'red';
        if (0 == $member_id) {
            $accept_only = array('email_address', 'first_name', 'last_name', 'company_name', 'country');
            $insert_vals = array(
                'date_joined'  => "NOW()",
                'mailing_list' => "'y'",
                'site_id'      => formatSQLString($site_id),
                'signup_IP'    => formatSQLString($ip),
                'flag'         => formatSQLString($flag),
                'status'       => "'active'",
                'rand_num'     => rand(1, 65535),
            );
            foreach ($accept_only as $k) {
                if (isset($posted[$k])) {
                    $insert_vals[$k] = formatSQLString($posted[$k]);
                }
            }

            $DB->query("INSERT INTO `highwaygroup`.`new_members` (`" . implode("`, `", array_keys($insert_vals)) . "`) VALUES (" . implode(", ", array_values($insert_vals)) . ")");
            $member_id = $DB->insert_id();
        } else {
            $site_list   = explode(",", memberGetValue($member_id, 'site_id'));
            $site_list[] = $site_id;
            $site_list   = array_unique($site_list);
            sort($site_list, SORT_NUMERIC);
            $update_vals = array(
                'site_id'      => "`site_id` = '" . implode(",", $site_list) . "'",
                'mailing_list' => "`mailing_list` = 'y'",
                'status'       => "`status` = 'active'",
            );
            if ('red' == $flag) {
                $update_vals['flag'] = "`flag`=" . formatSQLString($flag);
            }

            $DB->query("UPDATE `highwaygroup`.`new_members` SET " . implode(", ", $update_vals) . " WHERE member_id=" . $member_id . " LIMIT 1");
        }
        $styles = array();
        if (is_array($style_id)) {
            foreach ($style_id as $v) {
                $styles[] = intval($v);
            }
        } elseif (0 == $style_id) {
            if ($query = $DB->query(sprintf(
                "SELECT `style_id` FROM `highwaygroup`.`newsletter_styles` WHERE `site_id` = %d",
                $site_id
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $styles[] = intval($row['style_id']);
                }
                $DB->free_result($query);
            }
        } else {
            $styles[] = intval($style_id);
        }

        foreach ($styles as $style_id) {
            if ($query = $DB->query(sprintf(
                "SELECT `%s` FROM %s WHERE `member_id` = %d AND `style_id` = %d AND `active` = 'y' LIMIT 1",
                MEMBER_MAILINGLIST_PRIMARYKEY,
                MEMBER_MAILINGLIST_TABLENAME,
                $member_id,
                $style_id
            ))) {
                if (!$DB->fetch_assoc($query)) {
                    $DB->query(sprintf(
                        "INSERT INTO %s (`member_id`, `style_id`, `active`, `date_joined`) VALUES (%d, %d, 'y', NOW())",
                        MEMBER_MAILINGLIST_TABLENAME,
                        $member_id,
                        $style_id
                    ));
                }
                $DB->free_result($query);
            }
        }

        /**
         * For compatibility with old HE mailing list
         */
        if (2 == $site_id) {
            $DB->query(sprintf(
                "INSERT IGNORE INTO `HE`.`HE_mailinglist` (`id`, `email`, `name`, `date_added`, `ip`) VALUES (%d, %s, %s, NOW(), %s)",
                $member_id,
                formatSQLString($eml),
                formatSQLString($posted['first_name'] . ' ' . $posted['last_name']),
                formatSQLString($ip)
            ));
        }

        return $member_id;
    }
}
if (!function_exists('memberMailingListToDatabase')) {
    /**
     * @param int       $member_id
     * @param mixed[][] $new_mailing
     *
     * @return void
     */
    function memberMailingListToDatabase($member_id, $new_mailing)
    {
        global $DB;

        $member_id = intval($member_id);
        if (!$member_id || !is_array($new_mailing)) {
            return;
        }

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `member_id` = %d",
            MEMBER_MAILINGLIST_TABLENAME,
            $member_id
        ));
        foreach ($new_mailing as $mailing) {
            $mailing['member_id'] = intval($member_id);
            $insert               = array();
            foreach ($mailing as $k => $v) {
                $insert["`" . $k . "`"] = formatSQLString($v);
            }

            $DB->query(sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                MEMBER_MAILINGLIST_TABLENAME,
                implode(", ", array_keys($insert)),
                implode(", ", array_values($insert))
            ));
        }
    }
}
if (!function_exists('memberMailingListVirtual')) {
    /**
     * @param mixed[] $item
     *
     * @return mixed[]
     */
    function memberMailingListVirtual($item)
    {
        $item['time_joined'] = (is_null($item['date_joined']) || $item['date_joined'] == '0000-00-00 00:00:00') ? null : strtotime($item['date_joined']);

        $item['time_cancelled'] = (is_null($item['date_cancelled']) || $item['date_joined'] == '0000-00-00 00:00:00' ) ? null : strtotime($item['date_cancelled']);

        return $item;
    }
}

if (!function_exists('memberMailingListSubscribe')) {
    function memberMailingListSubscribe($member_id, $style_id)
    {
        global $DB;
        if ($DB->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `%s` = %d AND `style_id` = %d AND `active` = 'y' LIMIT 1",
            MEMBER_MAILINGLIST_TABLENAME,
            MEMBER_MAILINGLIST_SECONDARYKEY,
            $member_id,
            $style_id
        ), MYSQLI_ASSOC)) {
            return true;
        }

         $DB->query(sprintf(
            "INSERT INTO %s (`member_id`, `style_id`, `active`, `comments`, `date_joined`, `date_cancelled`) VALUES (%d, %d, 'y', %s, NOW(), '0000-00-00 00:00:00')",
            MEMBER_MAILINGLIST_TABLENAME,
            $member_id,
            $style_id,
            $DB->escape('Subscribed via ' . relative_url())
        ));
    }
}
if (!function_exists('memberMailingListUnsubscribe')) {
    function memberMailingListUnsubscribe($member_id, $style_id)
    {
        global $DB;

        if (!$row = $DB->query_first(sprintf(
            "SELECT `%s` FROM %s WHERE `%s` = %d AND `style_id` = %d AND `active` = 'y' LIMIT 1",
            MEMBER_MAILINGLIST_PRIMARYKEY,
            MEMBER_MAILINGLIST_TABLENAME,
            MEMBER_MAILINGLIST_SECONDARYKEY,
            $member_id,
            $style_id
        ), MYSQLI_ASSOC)) {
            return true;
        }

        return $DB->query(sprintf(
            "UPDATE %s SET `active` = 'n', `comments` = CONCAT_WS('\\n', `comments`, %s), `date_cancelled` = NOW() WHERE `%s` = %d LIMIT 1",
            MEMBER_MAILINGLIST_TABLENAME,
            $DB->escape('Unsubscribed via ' . relative_url()),
            MEMBER_MAILINGLIST_PRIMARYKEY,
            $row[MEMBER_MAILINGLIST_PRIMARYKEY]
        ));
    }
}
