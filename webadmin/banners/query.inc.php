<?php
if (!defined('BANNER_TABLENAME')) {
    define('BANNER_TABLENAME', "`banners`");
}
if (!defined('BANNER_PRIMARYKEY')) {
    define('BANNER_PRIMARYKEY', "id");
}

if (!function_exists('bannerAddClick')) {
    /**
     * Increments the number of clicks on a banner by 1
     * Updates the 'banners' table in the database by incrementing the
     * 'clicks' value by 1 for the row with id $id
     *
     * @param int $id
     *
     * @return void
     */
    function bannerAddClick($id)
    {
        global $DB;

        $DB->query(sprintf(
            "UPDATE %s SET `clicks` = `clicks` + 1 WHERE `%s` = %d LIMIT 1",
            BANNER_TABLENAME,
            BANNER_PRIMARYKEY,
            $id
        ));
    }
}
if (!function_exists('bannerAddView')) {
    /**
     * Increments the number of views of a banner by 1
     * Updates the 'banners' table in the database by incrementing the
     * 'views' value by 1 for the row with id $id
     *
     * @param int $id
     *
     * @return void
     */
    function bannerAddView($id)
    {
        global $DB;

        $DB->query(sprintf(
            "UPDATE %s SET `views` = `views` + 1 WHERE `%s` = %d LIMIT 1",
            BANNER_TABLENAME,
            BANNER_PRIMARYKEY,
            $id
        ));
    }
}
if (!function_exists('bannerChangeActiveStatus')) {
    /**
     * Changes the active status of a banner
     * sets the 'active' value in the 'banner' table for row with id $id
     * to the string provided in $status, 'y' for yes and 'n' for no
     *
     * @param int    $id
     * @param string $status
     *
     * @return void
     */
    function bannerChangeActiveStatus($id, $status)
    {
        global $DB;

        $DB->query(sprintf(
            "UPDATE %s SET `active` = %s WHERE `%s` = %d LIMIT 1",
            BANNER_TABLENAME,
            $DB->escape($status),
            BANNER_PRIMARYKEY,
            $id
        ));
    }
}
if (!function_exists('bannerChunk')) {
    /**
     * Groups list of items by fields entered
     * split_sets will split set fields by comma, such that
     * one item can be in multiple groups
     *
     * @param int[]           $item_list
     * @param string[]|string $group_by
     * @param bool            $split_sets
     *
     * @return mixed[]
     */
    function bannerChunk($item_list, $group_by = '', $split_sets = true)
    {
        global $DB;
        if (!is_array($item_list) || count($item_list) < 1) {
            return $item_list;
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $fields     = bannerGetOptionList('all_fields');
        $field_list = array(BANNER_PRIMARYKEY);
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[] = $fields[$field] . " AS `" . $field . "`";
            }
        }
        $field_list = array_unique($field_list);

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT %s FROM %s WHERE %s",
            implode(', ', $field_list),
            BANNER_TABLENAME,
            bannerSQLList($item_list)
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $pointers = array(&$results);
                foreach ($group_by as $group) {
                    $key = $row[$group];
                    if ('' == $key) {
                        $key = null;
                    }
                    $new_pointers = array();
                    foreach (($split_sets) ? explode(',', $key) : array($key) as $key2) {
                        foreach ($pointers as $key1 => $pointer) {
                            if (!array_key_exists($key1, $pointers)) {
                                $pointers[$key1] = array();
                            }
                            if (!array_key_exists($key2, $pointers[$key1])) {
                                $pointers[$key1][$key2] = array();
                            }
                            $new_pointers[] = &$pointers[$key1][$key2];
                        }
                    }
                    $pointers = $new_pointers;
                }
                foreach ($pointers as $pointer_key => $pointer) {
                    $pointers[$pointer_key][] = $row[BANNER_PRIMARYKEY];
                }
            }
            $DB->free_result($query);
        }
        return $results;
    }
}
if (!function_exists('bannerClicksReset')) {
    /**
     * Resets the number of clicks on a banner to 0
     * Updates the 'banners' table in the database by setting the
     * 'clicks' value to 0 for the row with id $id
     *
     * @param int $id
     *
     * @return void
     */
    function bannerClicksReset($id)
    {
        global $DB;

        $DB->query(sprintf(
            "UPDATE %s SET `clicks` = 0 WHERE `%s` = %d LIMIT 1",
            BANNER_TABLENAME,
            BANNER_PRIMARYKEY,
            $id
        ));
    }
}
if (!function_exists('bannerDeleteFromDatabase')) {
    /**
     * Remove the banner from the database
     * Updates the 'banners' table in the database by deleting the
     * the row with id $id
     *
     * @param int $id
     *
     * @return void
     */
    function bannerDeleteFromDatabase($id)
    {
        global $DB;

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d LIMIT 1",
            BANNER_TABLENAME,
            BANNER_PRIMARYKEY,
            $id
        ));
    }
}
if (!function_exists('bannerExists')) {
    /**
     * Checks if a banner is in the database
     * Searches the 'banners' table in the database for row
     * with id $id; returns true if found, false if not
     *
     * @param int $id
     *
     * @return bool
     */
    function bannerExists($id)
    {
        global $DB;

        return !!$DB->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `%s` = %d LIMIT 1",
            BANNER_TABLENAME,
            BANNER_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('bannerFormatNumber')) {
    /**
     * Formats a number to a decimal
     * Takes a number inputted as $id, and returns
     * a string containing $id as a decimal
     *
     * @param int $id
     *
     * @return string
     */
    function bannerFormatNumber($id)
    {
        return sprintf('%d', $id);
    }
}
if (!function_exists('bannerFromDatabase')) {
    /**
     * Returns banner from database with provided id
     * Finds the first row in 'banners' table which id matches parameter,
     * then adds that row with additional unix-timestamps for date_added and
     * expiry to static array; returns this new row if found, or false if not
     *
     * @param int $id
     *
     * @return mixed[]|null
     */
    function bannerFromDatabase($id)
    {
        global $DB;

        $fields = array('*');
        foreach (bannerGetOptionList('ext_fields') as $k => $v) {
            $fields[] = $v . " AS `" . $k . "`";
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT %s FROM %s WHERE `%s` = %d LIMIT 1",
            implode(", ", $fields),
            BANNER_TABLENAME,
            BANNER_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC)) {
            return bannerVirtual($row);
        }

        return null;
    }
}
if (!function_exists('bannerGetOptionList')) {
    /**
     * Returns array of options for table column/s
     *
     * @param string $table_name
     *
     * @return mixed
     */
    function bannerGetOptionList($table_name)
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
            case 'mobile_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/banner/mobile';
            case 'hover_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/banner/hover';
            case 'image_path':
            case 'thumb_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/banner/image';
            case 'active':
                return array('n' => 'No', 'y' => 'Yes');
            case 'banner_type':
                return array(
                    27 =>  'Newsletter   (912 x 160)("Wide")',
                    28 =>  'Newsletter   (424 x 320)("Thin")',
                    23 =>  'Front Banner - Large Slider(1350x500)',
                    24 =>  '1/2 Page Banner( 510x480)',
                    26 =>  'Front Banner - Bottom Long Banner(1350x500)',
                );
            case 'all_fields':
                return array_merge(bannerGetOptionList('base_fields'), bannerGetOptionList('ext_fields'));
            case 'all_fields_types':
                return array_merge(bannerGetOptionList('base_fields_types'), bannerGetOptionList('ext_fields_types'));
            case 'base_fields':
                $fields = array();
                foreach (getTableColumns(BANNER_TABLENAME) as $key) {
                    $fields[$key] = '`' . $key . '`';
                }

                return $fields;
            case 'base_fields_types':
                return getTableFields(BANNER_TABLENAME);
            case 'joined_fields':
                return array();
            case 'ext_fields':
            case 'extra_fields':
                return array(
                    'expired'         => "IF(`expiry` > '1970-01-01 00:00:00' AND `expiry` < NOW(), 'y', 'n')",
                    'banner_id'       => '`' . BANNER_PRIMARYKEY . '`',
                    'date_added_unix' => 'UNIX_TIMESTAMP(`date_added`)',
                    'expiry_unix'     => 'UNIX_TIMESTAMP(`expiry`)',
                    "name"            => "`text`",
                    "alt"             => "`text`",
                );
            case 'ext_fields_types':
                return array();
            default:
                echo 'bannerGetOptionList(' . json_encode($table_name) . ') is undefined<br />';
                break;
        }
        if ('' != $table) {
            if ($query = $DB->query(
                "SELECT " . $key . "," . $value . " FROM " . $table . (('' != $order_by) ? " ORDER BY " . $order_by . " ASC" : "")
            )) {
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
if (!function_exists('bannerGetValue')) {
    /**
     * Get value stored in specified row/column of database
     * Searches the 'banners' table in the database for row
     * with id $id, then gets value stored in column $field;
     * returns value if found, null if not
     *
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function bannerGetValue($id, $field)
    {
        global $DB;
        $fields = bannerGetOptionList("all_fields");
        if (array_key_exists($field, $fields)) {
            $row = $DB->query_first(sprintf(
                "SELECT %s AS `%s` FROM %s WHERE `%s` = %d LIMIT 1",
                $fields[$field],
                $field,
                BANNER_TABLENAME,
                BANNER_PRIMARYKEY,
                $id
            ), MYSQLI_ASSOC);
            return $row[$field];
        }
        return null;
    }
}
if (!function_exists('bannerQuery')) {
    /**
     * Generate a SQL WHERE expression to find matching banners.
     *
     * @param array $eq (optional) Array of key => values that the
     *    banner should match when searching. Values may be arrays.
     * @param array $lt (optional) Array of key => values that the
     *    banner should be less than when searching. Values may be arrays.
     * @param array $gt (optional) Array of key => values that the
     *    banner should be greater than when searching. Values may be arrays.
     */
    function bannerQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        // Simple case, all banners
        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $whereAND    = [];
        $field_types = bannerGetOptionList('all_fields_types');
        foreach (bannerGetOptionList('all_fields') as $field => $expr) {
            if (array_key_exists($field, $eq)) {
                $whereOR = [];
                foreach ((array)($eq[$field]) as $v) {
                    if (array_key_exists($field, $field_types) && substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                        $whereOR[] = sprintf("FIND_IN_SET(%s, %s)", $DB->escape($v), $expr);
                    } else {
                        $whereOR[] = sprintf("%s = %s", $expr, $DB->escape($v));
                    }
                }
                if (count($whereOR)) {
                    $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                }
            }
            if (array_key_exists($field, $lt)) {
                foreach ((array)($lt[$field]) as $v) {
                    $whereAND[] = sprintf("%s < %s", $expr, $DB->escape($v));
                }
            }
            if (array_key_exists($field, $gt)) {
                foreach ((array)($gt[$field]) as $v) {
                    $whereAND[] = sprintf("%s > %s", $expr, $DB->escape($v));
                }
            }
        }

        foreach ((array)($eq['any_text'] ?? []) as $findme) {
            if (!empty($findme)) {
                $whereOR = array();
                foreach (array(
                    '`' . BANNER_PRIMARYKEY . '`',
                    '`text`',
                ) as $expr) {
                    $whereOR[] = sprintf(
                        "%s LIKE %s",
                        $expr,
                        formatSQLLikeString($findme)
                    );
                }
                if (count($whereOR)) {
                    $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                }
            }
        }

        if (1 == count($whereAND)) {
            return implode(" AND ", $whereAND);
        } elseif (count($whereAND)) {
            return sprintf("(%s)", implode(" AND ", $whereAND));
        }
        return "1";
    }
}
if (!function_exists('bannerRandom')) {
    /**
     * Returns a random banner
     * Collects banner ids from database that are active and are
     * not expired, and have banner_type $type (when $type != 0);
     * returns a random id from this collection, or false if none
     *
     * @param int $type
     *
     * @return int
     */
    function bannerRandom($type = 0)
    {
        global $DB;
        $now = date("'Y-m-d 00:00:00'");

        $results = array();
        if ($query = $DB->query(
            "SELECT `" . BANNER_PRIMARYKEY . "` FROM " . BANNER_TABLENAME . " WHERE `active` = 'y' AND (`expiry` >= " . $now . " OR `expiry` = '0000-00-00 00:00:00')" . (($type > 0) ? (" AND `banner_type` = " . intval($type)) : "")
        )) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row[BANNER_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        shuffle($results);
        return reset($results);
    }
}
if (!function_exists('bannerSearch')) {
    /**
     * Search for banner based on criteria
     *
     * @param mixed[]|string $where
     *
     * @return int[]
     */
    function bannerSearch($where = '1')
    {
        global $DB;

        // Support legacy usage
        if (is_array($where)) {
            $where = bannerQuery($where);
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `%s` FROM %s WHERE %s ORDER BY `%s` DESC",
            BANNER_PRIMARYKEY,
            BANNER_TABLENAME,
            $where,
            BANNER_PRIMARYKEY
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row[BANNER_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('bannerSort')) {
    /**
     * Sort list of banners by given field
     * Orders an array of banners $banner_list in ascending order
     * by field $order_by; returns the same type that was entered
     *
     * @param int[]  $banner_list
     * @param string $order_by
     *
     * @return int[]
     */
    function bannerSort($banner_list, $order_by = 'id')
    {
        global $DB;
        if (!is_array($banner_list) || count($banner_list) < 2) {
            return $banner_list;
        }

        $sql         = "SELECT `" . BANNER_PRIMARYKEY . "` FROM " . BANNER_TABLENAME;
        $choices     = array('id' => 'id', 'active' => 'active', 'banner_type' => 'banner_type', 'clicks' => 'clicks', 'views' => 'views', 'rand_num' => 'RAND()', 'order' => '`order`');
        $where       = " WHERE `" . BANNER_PRIMARYKEY . "` IN ('" . implode("', '", $banner_list) . "')";
        $banner_list = array();
        $order       = " ORDER BY " . (('' != $choices[$order_by]) ? $choices[$order_by] : BANNER_PRIMARYKEY) . " ASC";
        if ($query = $DB->query($sql . $where . $order)) {
            while ($row = $DB->fetch_assoc($query)) {
                $banner_list[] = $row[BANNER_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        return $banner_list;
    }
}
if (!function_exists('bannerSQLList')) {
    /**
     * Converts a list of ids into an SQL query
     * returns a series of ranges for ids to be queried
     *
     * @param int[] $banner_list
     *
     * @return string
     */
    function bannerSQLList($banner_list, $key = 'id')
    {
        if (count($banner_list) == 0) {
            return "1";
        }

        $whereOR = array();
        $whereIN = array();
        foreach (list_to_partitions($banner_list) as $k => $v) {
            if ($k > $v + 2) {
                $whereOR[] = "(`" . $key . "` >= " . $v . " AND `" . $key . "` <= " . $k . ")";
            } else {
                foreach (range($v, $k) as $k2) {
                    $whereIN[] = $k2;
                }
            }

        }
        if (count($whereIN) > 1) {
            $whereOR[] = "`" . $key . "` IN(" . implode(", ", $whereIN) . ")";
        } elseif (count($whereIN) == 1) {
            $whereOR[] = "`" . $key . "` = " . implode(" OR `" . $key . "` = ", $whereIN);
        }

        return implode(" OR ", $whereOR);
    }
}
if (!function_exists('bannerToDatabase')) {
    /**
     * Add a banner to the database
     * If 'action' == 'do_add', insert new banner
     * into database, else update existing banner
     *
     * @param mixed[] $posted
     *
     * @return int
     */
    function bannerToDatabase($posted)
    {
        global $_GLOBALS;
        global $DB;

        $add    = ('do_add' == $posted['action']);
        $id     = intval($posted['id']);
        $sql    = (($add) ? "INSERT INTO" : "UPDATE") . " " . BANNER_TABLENAME;
        $banner = $posted['item'];
        if(!isset($banner['black_shading'])){
            $banner['black_shading'] = 'y';
        }
        if(!isset($banner['image_movement'])){
            $banner['image_movement'] = 'y';
        }
        if (array_key_exists('prevent_banners_affected', $posted)) {
            $banner['prevent_banners'] = array();
            if (is_array($posted['prevent_banners'])) {
                foreach ($posted['prevent_banners'] as $k => $v) {
                    $banner['prevent_banners'][] = $v['id'];
                }
            } elseif ('' != $posted['prevent_banners']) {
                $banner['prevent_banners'] = explode(',', $posted['prevent_banners']);
            }
            $banner['prevent_banners'] = implode(',', array_unique($banner['prevent_banners']));
        }

        $set = array();
        foreach ($banner as $k => $v) {
            $set[$k] = "`" . $k . "` = " . $DB->escape($v);
        }
        $set['active_on'] = "`active_on` = " . $DB->escape((is_array($banner['active_on'])) ? implode(',', $banner['active_on']) : '');
        if ($add) {
            $set['date_added'] = "`date_added` = NOW()";
            $set['staff']      = "`staff` = " . $DB->escape($_GLOBALS['staff']['user']);
        }
        $sql .= " SET " . implode(', ', $set) . (($add) ? "" : (" WHERE `" . BANNER_PRIMARYKEY . "` = " . intval($id)));

        $DB->query($sql);

        if ($add) {
            $id = intval($DB->insert_id());
        }

        if (UPLOAD_ERR_OK == $_FILES['image_file']['error']) {
            $ext   = explode('.', $_FILES['image_file']['name']);
            $ext   = end($ext);
            $image = sprintf('%d.%s', $id, $ext);
            $dir1  = bannerGetOptionList('image_path') . '/';
            if (is_file($dir1 . $image)) {
                unlink($dir1 . $image);
            }
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $dir1 . $image)) {
                $DB->query(sprintf(
                    "UPDATE %s SET `image` = %s WHERE `%s` = %d LIMIT 1",
                    BANNER_TABLENAME,
                    $DB->escape($image),
                    BANNER_PRIMARYKEY,
                    $id
                ));
            }
        }

        if (UPLOAD_ERR_OK == $_FILES['mobile_file']['error']) {
            $ext          = explode('.', $_FILES['mobile_file']['name']);
            $ext          = end($ext);
            $mobile_image = sprintf('%d.%s', $id, $ext);
            $dir1         = bannerGetOptionList('mobile_path') . '/';
            if (is_file($dir1 . $mobile_image)) {
                unlink($dir1 . $mobile_image);
            }
            if (move_uploaded_file($_FILES['mobile_file']['tmp_name'], $dir1 . $mobile_image)) {
                $DB->query(sprintf(
                    "UPDATE %s SET `mobile_image` = %s WHERE `%s` = %d LIMIT 1",
                    BANNER_TABLENAME,
                    $DB->escape($mobile_image),
                    BANNER_PRIMARYKEY,
                    $id
                ));
            }
        }

        if (UPLOAD_ERR_OK == $_FILES['hover_file']['error']) {
            $ext         = explode('.', $_FILES['hover_file']['name']);
            $ext         = end($ext);
            $hover_image = sprintf('%d.%s', $id, $ext);
            $dir1        = bannerGetOptionList('hover_path') . '/';
            if (is_file($dir1 . $hover_image)) {
                unlink($dir1 . $hover_image);
            }
            if (move_uploaded_file($_FILES['hover_file']['tmp_name'], $dir1 . $hover_image)) {
                $DB->query(sprintf(
                    "UPDATE %s SET `hover_image` = %s WHERE `%s` = %d LIMIT 1",
                    BANNER_TABLENAME,
                    $DB->escape($hover_image),
                    BANNER_PRIMARYKEY,
                    $id
                ));
            }
        }

        return $id;
    }
}
if (!function_exists('bannerViewsReset')) {
    /**
     * Reset views of a banner
     * Finds the banner with id $id in database and sets it's
     * 'views' to 0
     *
     * @param int $id
     *
     * @return void
     */
    function bannerViewsReset($id)
    {
        global $DB;

        $DB->query(sprintf(
            "UPDATE %s SET `views` = 0 WHERE `%s` = %d LIMIT 1",
            BANNER_TABLENAME,
            BANNER_PRIMARYKEY,
            $id
        ));
    }
}
if (!function_exists('bannerVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function bannerVirtual($row)
    {
        global $_GLOBALS;

        $row["click_url"] = $row["url"];

        if ('' != $row['image'] && is_file(bannerGetOptionList('image_path') . '/' . $row['image'])) {
            $ext   = 'jpg';
            $split = explode('.', $row['image']);
            if (count($split) > 1) {
                $ext = end($split);
            }
            if (in_array($ext, array('png', 'jpeg', 'bmp'))) {
                $ext = 'jpg';
            }

            $row['image_url'] = $row['thumb_url'] = '/banners/images/' . formatURLSafe($row['name']) . '/' . $row[BANNER_PRIMARYKEY] . '.' . $ext;
        }

        if (!empty($_GLOBALS["site_settings"]["static_path"] ?? '')) {
            if ('' != $row['image']) {
                foreach (array(
                    '/images/banners/image/' . $row[BANNER_PRIMARYKEY] . '.gif',
                    '/images/banners/image/' . $row[BANNER_PRIMARYKEY] . '.jpg',
                ) as $v) {
                    if (is_file($_GLOBALS["site_settings"]["static_path"] . $v)) {
                        $row['image_url'] = $_GLOBALS["site_settings"]["static_uri"] . $v;
                        break;
                    }
                }
            }
            if ('' != $row['mobile_image']) {
                foreach (array(
                    '/images/banners/mobile/' . $row[BANNER_PRIMARYKEY] . '.gif',
                    '/images/banners/mobile/' . $row[BANNER_PRIMARYKEY] . '.jpg',
                ) as $v) {
                    if (is_file($_GLOBALS["site_settings"]["static_path"] . $v)) {
                        $row['mobile_url'] = $_GLOBALS["site_settings"]["static_uri"] . $v;
                        break;
                    }
                }
            }
            if ('' != $row['hover_image']) {
                foreach (array(
                    '/images/banners/hover/' . $row[BANNER_PRIMARYKEY] . '.gif',
                    '/images/banners/hover/' . $row[BANNER_PRIMARYKEY] . '.jpg',
                ) as $v) {
                    if (is_file($_GLOBALS["site_settings"]["static_path"] . $v)) {
                        $row['hover_url'] = $_GLOBALS["site_settings"]["static_uri"] . $v;
                        break;
                    }
                }
            }
        }

        if ('/webadmin/' === substr($_SERVER["REQUEST_URI"], 0, 10)) {
            if ('' != $row['image']) {
                $row['image_url'] = substr(bannerGetOptionList('image_path') . '/' . $row['image'], strlen($_SERVER['DOCUMENT_ROOT']));
            }
            if ('' != $row['mobile_image']) {
                $row['mobile_url'] = substr(bannerGetOptionList('mobile_path') . '/' . $row['mobile_image'], strlen($_SERVER['DOCUMENT_ROOT']));
            }
            if ('' != $row['hover_image']) {
                $row['hover_url'] = substr(bannerGetOptionList('hover_path') . '/' . $row['hover_image'], strlen($_SERVER['DOCUMENT_ROOT']));
            }
        }

        return $row;
    }
}
