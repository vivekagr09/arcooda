<?php
if (!defined('SUPPORTED_IMAGE_TABLENAME')) {
    define('SUPPORTED_IMAGE_TABLENAME', "`product_supported_images`");
}
if (!defined('SUPPORTED_IMAGE_PRIMARYKEY')) {
    define('SUPPORTED_IMAGE_PRIMARYKEY', "id");
}
if (!defined('SUPPORTED_IMAGE_SECONDARYKEY')) {
    define('SUPPORTED_IMAGE_SECONDARYKEY', "supported_id");
}

if (!function_exists('supportedImageDefault')) {
    /**
     * @return mixed[]
     */
    function supportedImageDefault()
    {
        global $DB;

        return supportedImageVirtual($DB->modelDefault(
            SUPPORTED_IMAGE_TABLENAME,
            SUPPORTED_IMAGE_PRIMARYKEY
        ));
    }
}
if (!function_exists('supportedImageDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function supportedImageDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SUPPORTED_IMAGE_TABLENAME,
            SUPPORTED_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('supportedImageExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function supportedImageExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SUPPORTED_IMAGE_TABLENAME,
            SUPPORTED_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('supportedImageFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function supportedImageFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SUPPORTED_IMAGE_TABLENAME,
            SUPPORTED_IMAGE_PRIMARYKEY
        )) {
            return supportedImageVirtual($row);
        }

        return null;
    }
}
if (!function_exists('supportedImageGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function supportedImageGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SUPPORTED_IMAGE_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SUPPORTED_IMAGE_TABLENAME,
                SUPPORTED_IMAGE_PRIMARYKEY
            );
        }

        if ($model = supportedImageFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('supportedImageHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function supportedImageHasValue($id, $field, $value)
    {
        return $value == supportedImageGetValue($id, $field);
    }
}
if (!function_exists('supportedImageQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function supportedImageQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(SUPPORTED_IMAGE_TABLENAME);

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
if (!function_exists('supportedImageSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function supportedImageSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = supportedImageQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            SUPPORTED_IMAGE_PRIMARYKEY,
            SUPPORTED_IMAGE_TABLENAME,
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
if (!function_exists('supportedImageSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function supportedImageSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SUPPORTED_IMAGE_TABLENAME,
            SUPPORTED_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('supportedImageSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function supportedImageSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = supportedImageQuery(array(
                SUPPORTED_IMAGE_PRIMARYKEY => $where,
            ));
        }

        return supportedImageSearch($where, $order_by);
    }
}
if (!function_exists('supportedImageToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function supportedImageToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SUPPORTED_IMAGE_TABLENAME,
            SUPPORTED_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('supportedImageVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function supportedImageVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('supportedImagesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function supportedImagesDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            SUPPORTED_IMAGE_TABLENAME,
            SUPPORTED_IMAGE_SECONDARYKEY
        );
    }
}
if (!function_exists('supportedImagesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function supportedImagesFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            SUPPORTED_IMAGE_TABLENAME,
            SUPPORTED_IMAGE_PRIMARYKEY,
            SUPPORTED_IMAGE_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = supportedImageVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('supportedImagesToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function supportedImagesToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            SUPPORTED_IMAGE_TABLENAME,
            SUPPORTED_IMAGE_PRIMARYKEY,
            SUPPORTED_IMAGE_SECONDARYKEY
        );
    }
}
