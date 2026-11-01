<?php
if (!defined('CATEGORY_TABLENAME')) {
    define('CATEGORY_TABLENAME', "`product_types`");
}
if (!defined('CATEGORY_PRIMARYKEY')) {
    define('CATEGORY_PRIMARYKEY', "id");
}

if (!function_exists('categoryChunk')) {
    function categoryChunk($list, $group_by = '')
    {
        global $DB;

        if (is_array($list) && count($list) < 1) {
            return $list;
        }

        $select_list = array(
            sprintf("`%s`", CATEGORY_PRIMARYKEY),
        );

        $fields = categoryGetOptionList('base_fields');
        foreach ((array)$group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $select_list[] = sprintf("`%s`", $field);
            }
        }

        $select_list = array_unique($select_list);
        sort($select_list);

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT %s FROM %s WHERE %s",
            implode(', ', $select_list),
            CATEGORY_TABLENAME,
            (is_string($list)) ? $list : categorySQLList($list)
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $pointers = array(&$results);
                foreach ((array)$group_by as $group) {
                    if (array_key_exists($group, $row)) {
                        $key          = $row[$group];
                        $new_pointers = array();
                        foreach (explode(',', $key) as $k2) {
                            foreach ($pointers as $k1 => $pointer) {
                                if (!array_key_exists($k2, $pointer)) {
                                    $pointers[$k1][$k2] = array();
                                }

                                $new_pointers[] = &$pointers[$k1][$k2];
                            }
                        }

                        $pointers = $new_pointers;
                    } else {
                        continue (2);
                    }
                }
                foreach ($pointers as $k => $pointer) {
                    $pointers[$k][] = $row[CATEGORY_PRIMARYKEY];
                }
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists('categoryCount')) {
    /**
     * Count the products grouped by field
     *
     * @param string       $criteria The product search query
     * @param array|string $group_by The fields to group by
     */
    function categoryCount($criteria = "1", $group_by = '')
    {
        global $DB;

        //TODO: Deprecated calling convention
        if (is_array($criteria)) {
            $criteria = categoryQuery(array(CATEGORY_PRIMARYKEY => $criteria));
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $group_list = array();
        $field_list = array(
            'row_count' => 'COUNT(*) AS `row_count`',
        );
        foreach (categoryGetOptionList('base_fields') as $field => $expr) {
            if (in_array($field, $group_by)) {
                $group_list[] = sprintf("`%s`", $field);
                $field_list[] = sprintf("%s AS `%s`", $expr, $field);
            }
        }

        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT %s FROM %s AS `c` WHERE %s%s",
            implode(', ', $field_list),
            CATEGORY_TABLENAME,
            $criteria,
            (count($group_list)) ? sprintf(
                " GROUP BY %s",
                implode(', ', $group_list)
            ) : ''
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $pointers = array(&$results);
            foreach ($group_by as $group) {
                if (!array_key_exists($group, $row)) {
                    continue;
                }
                $k          = $row[$group];
                $new_pointers = array();
                foreach (explode(',', $k) as $k2) {
                    foreach ($pointers as $k1 => $pointer) {
                        if (!array_key_exists($k2, $pointers[$k1])) {
                            $pointers[$k1][$k2] = array();
                        }
                        $new_pointers[] = &$pointers[$k1][$k2];
                    }
                }
                $pointers = $new_pointers;
            }
            foreach ($pointers as $pointer_key => $pointer) {
                if (is_array($pointers[$pointer_key])) {
                    $pointers[$pointer_key] = 0;
                }
                $pointers[$pointer_key] += $row['row_count'];
            }
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('categoryDefault')) {
    /**
     * @return mixed[]
     */
    function categoryDefault()
    {
        global $DB;

        return categoryVirtual($DB->modelDefault(
            CATEGORY_TABLENAME,
            CATEGORY_PRIMARYKEY
        ));
    }
}
if (!function_exists('categoryDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function categoryDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            CATEGORY_TABLENAME,
            CATEGORY_PRIMARYKEY
        );
    }
}
if (!function_exists('categoryExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function categoryExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            CATEGORY_TABLENAME,
            CATEGORY_PRIMARYKEY
        );
    }
}
if (!function_exists('categoryFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function categoryFromDatabase($id)
    {
        global $DB;

        static $cache = array();
        $cache_key    = sprintf("%d", $id);
        if (array_key_exists($cache_key, $cache)) {
            return $cache[$cache_key];
        }

        if ($row = $DB->modelLoad(
            $id,
            CATEGORY_TABLENAME,
            CATEGORY_PRIMARYKEY
        )) {
            return $cache[$cache_key] = categoryVirtual($row);
        }

        return $cache[$cache_key] = null;
    }
}
if (!function_exists('categoryGetOptionList')) {
    function categoryGetOptionList($key)
    {
        $map = array(
        );
        if (array_key_exists($key, $map)) {
            $key = $map[$key];
        }

        static $cache = array();
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        global $DB;

        switch ($key) {
            case 'category_children':
                $result = array();
                if ($query = $DB->query(sprintf(
                    "SELECT `%s`, `parent` FROM %s ORDER BY `ordering` ASC",
                    CATEGORY_PRIMARYKEY,
                    CATEGORY_TABLENAME
                ))) {
                    while ($row = $DB->fetch_assoc($query)) {
                        $result[$row['parent']][] = $row[CATEGORY_PRIMARYKEY];
                    }
                    $DB->free_result($query);
                }
                return $cache[$key] = $result;

            case 'name':
                $result = array();
                if ($query = $DB->query(sprintf(
                    "SELECT `%s`, `name` FROM %s ORDER BY `%s` ASC",
                    CATEGORY_PRIMARYKEY,
                    CATEGORY_TABLENAME,
                    CATEGORY_PRIMARYKEY
                ))) {
                    while ($row = $DB->fetch_assoc($query)) {
                        $result[$row[CATEGORY_PRIMARYKEY]] = $row['name'];
                    }
                    $DB->free_result($query);
                }
                return $cache[$key] = $result;

            case 'section':
                $result = array();
                if ($query = $DB->query(sprintf(
                    "SELECT `%s`, `name` FROM %s WHERE `parent` = 0 ORDER BY `ordering` ASC",
                    CATEGORY_PRIMARYKEY,
                    CATEGORY_TABLENAME
                ))) {
                    while ($row = $DB->fetch_assoc($query)) {
                        $result[$row[CATEGORY_PRIMARYKEY]] = $row['name'];
                    }
                    $DB->free_result($query);
                }
                return $cache[$key] = $result;

            case 'icon_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/category/icon/';

            case 'header_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/category/header/';

            case 'image_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/images/sections/';

            case 'base_fields':
                $result = array();
                foreach (getTableColumns(CATEGORY_TABLENAME) as $field) {
                    $result[$field] = sprintf("`%s`", $field);
                }
                return $cache[$key] = $result;
        }

        //trigger_error('categoryGetOptionList(\'' . $key . '\') not defined');

        return $cache[$key] = null;
    }
}
if (!function_exists('categoryGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function categoryGetValue($id, $field)
    {
        global $DB;

        static $cache = array();
        $cache_key    = sprintf("%d_%s", $id, $field);
        if (array_key_exists($cache_key, $cache)) {
            return $cache[$cache_key];
        }

        if (array_key_exists($field, $DB->table_columns(
            CATEGORY_TABLENAME
        ))) {
            return $cache[$cache_key] = $DB->modelGet(
                $id,
                $field,
                CATEGORY_TABLENAME,
                CATEGORY_PRIMARYKEY
            );
        }

        if ($model = categoryFromDatabase($id)) {
            return $cache[$cache_key] = ($model[$field] ?? null);
        }

        return $cache[$cache_key] = null;
    }
}
if (!function_exists('categoryHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function categoryHasValue($id, $field, $value)
    {
        return $value == categoryGetValue($id, $field);
    }
}
if (!function_exists('categoryIsParent')) {
    function categoryIsParent($id)
    {
        global $DB;

        return !!$DB->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `parent` = %d LIMIT 1",
            CATEGORY_TABLENAME,
            $id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('categoryQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function categoryQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(CATEGORY_TABLENAME);

        $whereAND = array();
        foreach (array(
            '=' => (is_null($eq)) ? array() : $eq,
            '<' => (is_null($lt)) ? array() : $lt,
            '>' => (is_null($gt)) ? array() : $gt,
        ) as $op => $criteria) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $field_types)) {
                    $whereOR = array();
                    foreach ((array)$value as $sub_value) {
                        $whereOR[] = sprintf(
                            "`%s` %s %s",
                            $field,
                            $op,
                            $DB->escape($sub_value)
                        );
                    }
                    if (1 == count($whereOR)) {
                        $whereAND[] = implode("", $whereOR);
                    } elseif (count($whereOR)) {
                        $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                    }
                }
            }
        }

        if (array_key_exists('any_text', $eq)) {
            foreach ((array)$eq['any_text'] as $sub_value) {
                if (!empty($sub_value)) {
                    $whereOR = array();
                    foreach (array(
                        '`name`',
                        '`description`',
                        '`generic_notes`',
                    ) as $expr) {
                        $whereOR[] = sprintf(
                            "%s LIKE %s",
                            $expr,
                            $DB->escape("%" . $sub_value . "%")
                        );
                    }
                    $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                }
            }
        }

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('categorySearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function categorySearch($where = null, $order_by = "ordering")
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = categoryQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            CATEGORY_PRIMARYKEY,
            CATEGORY_TABLENAME,
            (!is_null($where)) ? sprintf(
                " WHERE %s",
                $where
            ) : "",
            (!is_null($order_by)) ? sprintf(
                " ORDER BY `%s`",
                $order_by
            ) : ""
        ));
    }
}
if (!function_exists('categorySetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function categorySetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            CATEGORY_TABLENAME,
            CATEGORY_PRIMARYKEY
        );
    }
}
if (!function_exists('categorySort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function categorySort($where = null, $order_by = "ordering")
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = categoryQuery(array(
                CATEGORY_PRIMARYKEY => $where,
            ));
        }

        return categorySearch($where, $order_by);
    }
}
if (!function_exists('categorySQLList')) {
    function categorySQLList($list)
    {
        if (count($list) == 0) {
            return "0";
        }

        return sprintf(
            "`%s` IN(%s)",
            CATEGORY_PRIMARYKEY,
            implode(", ", $list)
        );
    }
}
if (!function_exists('categoryToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function categoryToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            CATEGORY_TABLENAME,
            CATEGORY_PRIMARYKEY
        );
    }
}
if (!function_exists('categoryVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function categoryVirtual($row)
    {
        global $_GLOBALS;

        $row['icon_image']   = basename($row['icon_image']);
        $row['header_image'] = basename($row['header_image']);

        //@fixme this is wrong
        $path = categoryGetOptionList('image_path') . $row['img_location'] . '/';
        if (is_dir($path)) {
            if (is_file($path . $row['icon_image'])) {
                $row['icon_url'] = sprintf('/%s/%s', $row['img_location'], $row['icon_image']);
            }
            if (is_file($path . $row['header_image'])) {
                $row['header_url'] = sprintf('/%s/%s', $row['img_location'], $row['header_image']);
            }
        }

        //@fixme this is still wrong
        /*
        if ('' != $row['icon_image'] && is_file(categoryGetOptionList('icon_path') . $row['icon_image'])) {
            $row['icon_url'] = '/' . $row['icon_image'];
        }
        if ('' != $row['header_image'] && is_file(categoryGetOptionList('header_path') . $row['header_image'])) {
            $row['header_url'] = '/' . $row['header_image'];
        }
        */

        if (!empty($_GLOBALS["site_settings"]["static_path"] ?? '') && substr($_SERVER["REQUEST_URI"], 0, 10) != "/webadmin/") {
            if (array_key_exists('icon_url', $row)) {
                foreach (array(
                    '/images/categories/icon/' . $row[CATEGORY_PRIMARYKEY] . '.png',
                    '/images/categories/icon/' . $row[CATEGORY_PRIMARYKEY] . '.jpg',
                ) as $v) {
                    if (is_file($_GLOBALS["site_settings"]["static_path"] . $v)) {
                        $row['icon_url'] = $_GLOBALS["site_settings"]["static_uri"] . $v;
                        break;
                    }
                }
            }

            if (array_key_exists('header_url', $row)) {
                foreach (array(
                    '/images/categories/header/' . $row[CATEGORY_PRIMARYKEY] . '.png',
                    '/images/categories/header/' . $row[CATEGORY_PRIMARYKEY] . '.jpg',
                ) as $v) {
                    if (is_file($_GLOBALS["site_settings"]["static_path"] . $v)) {
                        $row['header_url'] = $_GLOBALS["site_settings"]["static_uri"] . $v;
                        break;
                    }
                }
            }
        }

        $row['view_url'] = sprintf(
            '%s/%s-%d',
            ($row['parent']) ? categoryGetValue($row['parent'], 'view_url') : '/c',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $row['name']), '-')),
            $row[CATEGORY_PRIMARYKEY]
        );

        return $row;
    }
}
