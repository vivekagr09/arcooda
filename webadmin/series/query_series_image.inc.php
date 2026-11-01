<?php
if (!defined('SERIES_IMAGE_TABLENAME')) {
    define('SERIES_IMAGE_TABLENAME', "`product_series_images`");
}
if (!defined('SERIES_IMAGE_PRIMARYKEY')) {
    define('SERIES_IMAGE_PRIMARYKEY', "id");
}
if (!defined('SERIES_IMAGE_SECONDARYKEY')) {
    define('SERIES_IMAGE_SECONDARYKEY', "series_id");
}

if (!function_exists('seriesImageDefault')) {
    /**
     * @return mixed[]
     */
    function seriesImageDefault()
    {
        global $DB;

        return seriesImageVirtual($DB->modelDefault(
            SERIES_IMAGE_TABLENAME,
            SERIES_IMAGE_PRIMARYKEY
        ));
    }
}
if (!function_exists('seriesImageDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function seriesImageDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SERIES_IMAGE_TABLENAME,
            SERIES_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('seriesImageExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function seriesImageExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SERIES_IMAGE_TABLENAME,
            SERIES_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('seriesImageFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function seriesImageFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SERIES_IMAGE_TABLENAME,
            SERIES_IMAGE_PRIMARYKEY
        )) {
            return seriesImageVirtual($row);
        }

        return null;
    }
}
if (!function_exists('seriesImageGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function seriesImageGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SERIES_IMAGE_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SERIES_IMAGE_TABLENAME,
                SERIES_IMAGE_PRIMARYKEY
            );
        }

        if ($model = seriesImageFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('seriesImageHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function seriesImageHasValue($id, $field, $value)
    {
        return $value == seriesImageGetValue($id, $field);
    }
}
if (!function_exists('seriesImageQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function seriesImageQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(SERIES_IMAGE_TABLENAME);

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

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('seriesImageSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function seriesImageSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = seriesImageQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            SERIES_IMAGE_PRIMARYKEY,
            SERIES_IMAGE_TABLENAME,
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
if (!function_exists('seriesImageSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function seriesImageSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SERIES_IMAGE_TABLENAME,
            SERIES_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('seriesImageSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function seriesImageSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = seriesImageQuery(array(
                SERIES_IMAGE_PRIMARYKEY => $where,
            ));
        }

        return seriesImageSearch($where, $order_by);
    }
}
if (!function_exists('seriesImageToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function seriesImageToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SERIES_IMAGE_TABLENAME,
            SERIES_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('seriesImageVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function seriesImageVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('seriesImagesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function seriesImagesDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            SERIES_IMAGE_TABLENAME,
            SERIES_IMAGE_SECONDARYKEY
        );
    }
}
if (!function_exists('seriesImagesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function seriesImagesFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            SERIES_IMAGE_TABLENAME,
            SERIES_IMAGE_PRIMARYKEY,
            SERIES_IMAGE_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = seriesImageVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('seriesImagesToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function seriesImagesToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            SERIES_IMAGE_TABLENAME,
            SERIES_IMAGE_PRIMARYKEY,
            SERIES_IMAGE_SECONDARYKEY
        );
    }
}
