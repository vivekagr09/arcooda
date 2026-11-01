<?php
if (!defined('MEMBER_WANTED_TABLENAME')) {
    define('MEMBER_WANTED_TABLENAME', "`highwaygroup`.`new_members_wanted`");
}
if (!defined('MEMBER_WANTED_PRIMARYKEY')) {
    define('MEMBER_WANTED_PRIMARYKEY', "id");
}
if (!defined('MEMBER_WANTED_SECONDARYKEY')) {
    define('MEMBER_WANTED_SECONDARYKEY', "member_id");
}

if (!function_exists('memberWantedFromDatabase')) {
    /**
     * @param int $wanted_id
     *
     * @return mixed[]|null
     */
    function memberWantedFromDatabase($wanted_id)
    {
        global $DB;

        $fields = array("`w`.*");
        foreach (memberWantedGetOptionList("ext_fields") as $k => $v) {
            $fields[] = sprintf("%s AS `%s`", $v, $k);
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT %s FROM %s AS `w` WHERE `w`.`%s` = %d LIMIT 1",
            implode(", ", $fields),
            MEMBER_WANTED_TABLENAME,
            MEMBER_WANTED_PRIMARYKEY,
            $wanted_id
        ), MYSQLI_ASSOC)) {
            return $row;
        }

        return null;
    }
}
if (!function_exists('memberWantedGetOptionList')) {
    /**
     * @param string $table_name
     *
     * @return mixed
     */
    function memberWantedGetOptionList($table_name)
    {
        global $DB;

        static $option_cache = array();
        if (isset($option_cache[$table_name])) {
            return $option_cache[$table_name];
        }

        $table       = '';
        $key         = '';
        $value       = '';
        $order_by    = '';
        $option_list = array();
        switch ($table_name) {
            case 'active':
                return array('n' => 'No', 'y' => 'Yes');
            case 'all_fields':
                return array_merge(memberWantedGetOptionList('base_fields'), memberWantedGetOptionList('ext_fields'));
            case 'all_fields_types':
                return array_merge(memberWantedGetOptionList('base_fields_types'), memberWantedGetOptionList('ext_fields_types'));
            case 'base_fields':
                $fields = array();
                foreach ($DB->get_table_columns('wanted') as $key) {
                    $fields[$key] = '`w`.`' . $key . '`';
                }

                return $fields;
            case 'base_fields_types':
                return $DB->get_table_fields('wanted');
            case 'joined_fields':
                return array();
            case 'ext_fields':
            case 'extra_fields':
                return array(
                    'expired'         => "IF(`w`.`expiry_date` > '1970-01-01 00:00:00' AND `w`.`expiry_date` < NOW(), 'y', 'n')",
                    'wanted_id'       => "`w`.`" . MEMBER_WANTED_PRIMARYKEY . "`",
                    'date_added_unix' => 'UNIX_TIMESTAMP(`w`.`date_added`)',
                    'expiry_unix'     => 'UNIX_TIMESTAMP(`w`.`expiry_date`)',
                    "text"            => "`w`.`item`",
                    "name"            => "`w`.`item`",
                    "title"           => "`w`.`item`",
                );
            case 'ext_fields_types':
                return array();
            default:
                echo 'memberWantedGetOptionList(' . json_encode($table_name) . ') is undefined<br />';
                break;
        }
        if ('' != $table) {
            if ($query = $DB->query(sprintf(
                "SELECT %s, %s FROM %s%s",
                $key,
                $value,
                $table,
                ('' != $order_by) ? sprintf(" ORDER BY %s ASC", $order_by) : ""
            ))) {
                while ($row = $DB->fetch_array($query)) {
                    $option_list[$row[0]] = $row[1];
                }
                $DB->free_result($query);
            }
        }
        $option_cache[$table_name] = $option_list;
        return $option_list;
    }
}
if (!function_exists('memberWantedSearch')) {
    /**
     * @param mixed $criteria
     *
     * @return int[]
     */
    function memberWantedSearch($criteria = '')
    {
        global $DB;

        $results = array();
        if (is_string($criteria) && '' != $criteria) {
            $criteria = array('any_text' => $criteria);
        }

        $sql         = " SELECT `w`.`" . MEMBER_WANTED_PRIMARYKEY . "` FROM " . MEMBER_WANTED_TABLENAME . " AS `w`";
        $joins       = array();
        $field_types = memberWantedGetOptionList('all_fields_types');
        $fields      = memberWantedGetOptionList('all_fields');
        $whereAND    = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "FIND_IN_SET(" . $DB->escape($sub_value) . "," . $fields[$field] . ")";
                                }
                            } else {
                                $temp = array();
                                foreach ($value as $sub_value) {
                                    $temp[] = $DB->escape($sub_value);
                                }

                                $whereOR[] = $fields[$field] . " IN(" . implode(", ", $temp) . ")";
                            }
                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }

                    } else {
                        if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                            $whereAND[] = "FIND_IN_SET(" . $DB->escape($value) . "," . $fields[$field] . ")";
                        } else {
                            $whereAND[] = $fields[$field] . "=" . $DB->escape($value);
                        }

                    }
                    unset($fields[$field]);
                }
            }
        }

        $joins      = array();
        $search_for = array();
        if (is_array($criteria) && is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_array($criteria) && is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array(
                sprintf("`w`.`%s`", MEMBER_WANTED_PRIMARYKEY),
            );
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
        if (count($joins) > 0) {
            $sql .= " " . implode(" ", $joins);
        }

        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " GROUP BY `w`.`" . MEMBER_WANTED_PRIMARYKEY . "`";
        $sql .= " ORDER BY `w`.`" . MEMBER_WANTED_PRIMARYKEY . "` ASC";
        if ($query = $DB->query(
            $sql
        )) {
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $results[] = $row['id'];
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
