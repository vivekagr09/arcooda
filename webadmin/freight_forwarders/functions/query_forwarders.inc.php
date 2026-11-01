<?php
/*
moduleLoad('freight');
*/

include_once __DIR__ . '/query_forwarderswarehouses.inc.php';
include_once __DIR__ . '/query_forwarderscontacts.inc.php';
include_once __DIR__ . '/query_forwardersaccounts.inc.php';
include_once __DIR__ . '/query_forwardersquotes.inc.php';

if (!function_exists('forwardersChunk')) {
    /**
     * @param  int[] $forwarder_list
     * @param  mixed $group_by
     * @return mixed[]
     */
    function forwardersChunk($forwarder_list, $group_by = '')
    {
        global $DB;
        if (!is_array($forwarder_list) || count($forwarder_list) < 1) {
            return $forwarder_list;
        }

        if (!is_array($group_by)) {
            $group_by = [$group_by];
        }

        $fields = getTableColumns('freight_services');
        foreach ($fields as $key => $value) {
            $fields[$key] = 'f.`' . $value . '`';
        }

        foreach (forwardersGetOptionList('ext_fields') as $key => $value) {
            $fields[$key] = $value . ' AS ' . $key;
        }

        $field_list = ['id'];
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[] = $fields[$field];
            }
        }

        $field_list = array_unique($field_list);
        $sql        = "SELECT " . implode(', ', $field_list) . " FROM freight_services f WHERE f.id IN(" . implode(',', $forwarder_list) . ")";
        $query      = $DB->query($sql);
        $results    = [];
        while ($row = $DB->fetch_assoc($query)) {
            $pointer = &$results;
            foreach ($group_by as $group) {
                $key = $row[$group];
                if ('' == $key) {
                    $key = ' ';
                }

                //            {
                if (!array_key_exists($key, $pointer)) {
                    $pointer[$key] = [];
                }

                $pointer = &$pointer[$key];
                //            }

            }
            $pointer[] = $row['id'];
        }
        $DB->free_result($query);
        return $results;
    }
}

if (!function_exists('forwardersDelete')) {
    /**
     * @param int $forwarder_id
     * @return void
     */
    function forwardersDelete($forwarder_id)
    {
        global $DB;
        $forwarder_id = intval($forwarder_id);
        $DB->query("DELETE FROM freight_services_warehouse WHERE product_id=" . $forwarder_id);
        $DB->query("DELETE FROM freight_services_contacts WHERE product_id=" . $forwarder_id);
        $DB->query("DELETE FROM freight_services_comments WHERE product_id=" . $forwarder_id);
        $DB->query("DELETE FROM freight_services_quotes WHERE product_id=" . $forwarder_id);
        $DB->query("DELETE FROM freight_services WHERE id=" . $forwarder_id . " LIMIT 1");
    }
}

if (!function_exists('forwardersExists')) {
    /**
     * @param int $forwarder_id
     * @return bool
     */
    function forwardersExists($forwarder_id)
    {
        global $DB;
        $sql = "SELECT id FROM freight_services WHERE id=" . intval($forwarder_id) . " LIMIT 1";
        $row = $DB->query_first($sql, MYSQLI_ASSOC);
        if ($row) {
            return true;
        }
        return false;
    }
}

if (!function_exists('forwardersFromDatabase')) {
    /**
     * @param int $forwarder_id
     * @return mixed[]
     */
    function forwardersFromDatabase($forwarder_id)
    {
        global $DB;
        $result = $DB->query_first(sprintf(
            "SELECT * FROM `freight_services` WHERE `id` = %d LIMIT 1",
            $forwarder_id
        ), MYSQLI_ASSOC);
        if ($result) {
            return $result;
        }

        $fields = getTableFields('freight_services');
        $result = [];
        foreach ($fields as $k => $v) {
            $result[$k] = $v[4];
        }
        return $result;
    }
}

if (!function_exists('forwardersFromPost')) {
    /**
     * @param  mixed[] $posted
     * @return mixed[]
     */
    function forwardersFromPost($posted)
    {
        global $DB;
        $forwarder_id = $posted['id'];
        $old_vals     = forwardersFromDatabase($forwarder_id);
        $new_vals     = $old_vals;
        foreach ($old_vals as $key => $value) {
            if (isset($posted[$key])) {
                $new_vals[$key] = $posted[$key];
            }
        }
        if ($forwarder_id <= 0) {
            unset($new_vals['id']);
        }
        return $new_vals;
    }
}

if (!function_exists('forwardersGetOptionList')) {
    /**
     * @param string $table_name
     * @return mixed
     */
    function forwardersGetOptionList($table_name)
    {
        global $DB;
        static $option_cache = [];
        if (isset($option_cache[$table_name])) {
            return $option_cache[$table_name];
        }

        $table       = null;
        $key         = null;
        $value       = null;
        $option_list = [];
        switch ($table_name) {
            case 'country':
                return getCountryList();
            case 'active':
                return ['n' => 'Inactive', 'y' => 'Active'];
            case 'type':
                return ['pp' => 'Pre-Paid', 'truck' => 'Truck', 'acc' => 'Account', 'sea' => 'Sea', 'air' => 'Air'];

            case 'all_fields':
                return array_merge(forwardersGetOptionList('base_fields'), forwardersGetOptionList('ext_fields'));
            case 'all_fields_types':
                return array_merge(forwardersGetOptionList('base_fields_types'), forwardersGetOptionList('ext_fields_types'));
            case 'base_fields':
                $fields = [];
                foreach (getTableColumns('freight_services') as $key) {
                    $fields[$key] = '`f`.`' . $key . '`';
                }
                return $fields;
            case 'base_fields_types':
                return getTableFields('freight_services');
            case 'ext_fields_types':
                return [];

            case 'joined_fields':
                $joins = [
                    ' LEFT JOIN freight_services_attachments ffa ON ffa.product_id=f.id' => getTableColumns('freight_services_attachments'),
                    ' LEFT JOIN freight_services_bank ffb ON ffb.product_id=f.id'        => getTableColumns('freight_services_bank'),
                    ' LEFT JOIN freight_services_comments ffc ON ffc.product_id=f.id'    => getTableColumns('freight_services_comments'),
                    ' LEFT JOIN freight_services_contacts ffe ON ffe.product_id=f.id'    => getTableColumns('freight_services_contacts'),
                    ' LEFT JOIN freight_services_quotes ffq ON ffq.product_id=f.id'      => getTableColumns('freight_services_quotes'),
                    ' LEFT JOIN freight_services_warehouse ffw ON ffw.product_id=f.id'   => getTableColumns('freight_services_warehouse'),
                ];
                foreach (getTableColumns('freight_services') as $field) {
                    foreach ($joins as $join => $rows) {
                        unset($joins[$join][$field]);
                    }
                }

                return $joins;
            case 'extra_fields':
            case 'ext_fields':
                return [];
        }
        if (!is_null($table)) {
            $query = $DB->query(sprintf(
                "SELECT %s, %s FROM %s",
                $key,
                $value,
                $table
            ));
            while ($row = $DB->fetch_array($query)) {
                $option_list[$row[0]] = $row[1];
            }
            $DB->free_result($query);
        }
        $option_cache[$table_name] = $option_list;
        return $option_list;
    }
}

if (!function_exists("forwardersGroupCount")) {
    /**
     * @param  int[] $item_list
     * @param  mixed $group_by
     * @return mixed[]
     */
    function forwardersGroupCount($item_list, $group_by = '')
    {
        return freightGroupCount($item_list, $group_by);
    }
}

if (!function_exists("forwardersSearch")) {
    /**
     * @param  ?mixed $criteria
     * @return int[]
     */
    function forwardersSearch($criteria = [])
    {
        moduleLoad('freight');

        $temp = [];
        if (is_array($criteria)) {
            $temp = $criteria;
        } elseif ("" != $criteria) {
            $temp["any_text"] = $criteria;
        }

        $criteria["type"] = "personal";

        return freightSearch($temp, "`s`.`type` IN('sea', 'air', 'sea_air')");
    }
}

if (!function_exists('forwardersGetLock')) {
    /**
     * @param  int $id
     * @return ?mixed[]
     */
    function forwardersGetLock($id)
    {
        global $DB;
        return $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `generic_locks` WHERE `section_name` = 'forwarder' AND `generic_id` = %d AND `expiry_date` > NOW() LIMIT 1",
            $id
        ), MYSQLI_ASSOC);
    }
}

if (!function_exists("forwardersGetLocks")) {
    /**
     * @return mixed[][]
     */
    function forwardersGetLocks()
    {
        global $DB;
        $result = [];
        $query  = $DB->query("SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `generic_locks` WHERE `section_name` = 'forwarder' AND `expiry_date` > NOW()");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row["generic_id"]] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}

if (!function_exists('forwardersSetLock')) {
    /**
     * @param int $id
     * @param int $staff_id
     * @param int $timeout
     * @return void
     */
    function forwardersSetLock($id, $staff_id = 0, $timeout = 300)
    {
        global $DB;
        if (0 != $staff_id) {
            $DB->query(sprintf(
                "UPDATE `generic_locks` SET `expiry_date` = %s, `modified` = NOW() WHERE `section_name` = 'forwarder' AND `generic_id` = %d AND `staff_id` = %d AND `expiry_date` > NOW() LIMIT 1",
                formatSQLDateTime(time() + $timeout),
                $id,
                $staff_id
            ));
            if ($DB->affected_rows() == 0) {
                $DB->query(sprintf(
                    "INSERT INTO `generic_locks` (`section_name`, `generic_id`, `staff_id`, `expiry_date`, `created`, `modified`) VALUES ('forwarder', %d, %d, %s, NOW(), NOW())",
                    $id,
                    $staff_id,
                    formatSQLDateTime(time() + $timeout)
                ));
            }
        } else {
            $DB->query(sprintf(
                "UPDATE `generic_locks` SET `expiry_date` = NOW(), `modified` = NOW() WHERE `section_name` = 'forwarder' AND `generic_id` = %d AND `expiry_date` > NOW()",
                $id
            ));
        }
    }
}

if (!function_exists('forwardersSort')) {
    /**
     * @param  int[]  $forwarder_list
     * @param  string $order_by
     * @return int[]
     */
    function forwardersSort($forwarder_list, $order_by = 'id')
    {
        global $DB;
        if (!is_array($forwarder_list) || count($forwarder_list) < 2) {
            return $forwarder_list;
        }

        $fields = getTableFields('freight_services');
        if (!array_key_exists($order_by, $fields)) {
            return $forwarder_list;
        }

        $result = [];
        $query  = $DB->query(sprintf(
            "SELECT `f`.`id` FROM `freight_services` AS `f` WHERE `f`.`id` IN(%s) ORDER BY %s ASC",
            implode(', ', $forwarder_list),
            $order_by
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row['id'];
        }
        $DB->free_result($query);

        return $result;
    }
}

if (!function_exists('forwardersToDatabase')) {
    /**
     * @param  mixed[] $summary
     * @return int
     */
    function forwardersToDatabase($summary)
    {
        global $DB;
        $forwarder_id  = intval($summary['id']);
        $old_forwarder = forwardersFromDatabase($forwarder_id);
        foreach ($old_forwarder as $key => $value) {
            if ($summary[$key] == $value) {
                unset($summary[$key]);
            }
        }

        foreach ($summary as $key => $value) {
            if (!array_key_exists($key, $old_forwarder)) {
                unset($summary[$key]);
            }
        }

        $fields = getTableFields('freight_services');
        if (count($summary) > 0) {
            if (forwardersExists($forwarder_id)) {
                $set_vals = [];
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = "`" . $key . "`=" . $DB->escape($value);
                        } else {
                            $set_vals[$key] = "`" . $key . "`=NULL";
                        }
                    }
                }

                $sql = "UPDATE freight_services SET " . implode(', ', $set_vals) . " WHERE id=" . $forwarder_id . " LIMIT 1";
                $DB->query($sql);
            } else {
                $set_vals = [];
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = "'" . $DB->escape_string($value) . "'";
                        } else {
                            $set_vals[$key] = 'NULL';
                        }
                    }
                }

                $sql          = "INSERT INTO freight_services(`" . implode('`, `', array_keys($set_vals)) . "`) VALUES(" . implode(", ", $set_vals) . ")";
                $query        = $DB->query($sql);
                $forwarder_id = $DB->insert_id();
            }
        }
        return $forwarder_id;
    }
}

if (!function_exists('forwardersAlphaList')) {
    /**
     * @param  string $alpha_value
     * @param  string $page_url
     * @return string
     */
    function forwardersAlphaList($alpha_value = '', $page_url = '?')
    {
        global $DB;
        $str = '<table class="wide" style="width:98%;"><td bgcolor=#FAFAFA style="text-align: center; width: 83%"><b>By Forwarder Name :</b> ';
        for ($i = 'A';; $i++) {
            $row = $DB->query_first(sprintf(
                "SELECT * FROM `freight_services` WHERE `name` LIKE '%s%%' AND (`type` = 'sea' OR `type` = 'air' OR `type` = 'sea_air') LIMIT 1",
                $i
            ), MYSQLI_ASSOC);
            if ($row) {
                if ($i == $alpha_value) {
                    $str .= '<span style="background-color:#7FFFD4">' . $i . '</span>&nbsp;';
                } else {
                    $str .= '<a href="' . $page_url . '&by=alpha&alpha_value=' . $i . '">' . $i . '</a>&nbsp;';
                }

            } else {
                $str .= '<font color="#999999">' . $i . '</font>&nbsp;';
            }

            if ("Z" == $i) {
                break;
            }

        }
        $str .= '</td></table>';
        return $str;
    }
}

//-----------------------------------------------------------------------------
//For Compatibility:
include_once __DIR__ . '/query_forwarder.inc.php';
