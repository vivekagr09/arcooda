<?php
if (!defined('NEWS_IMAGE_TABLENAME')) {
    define('NEWS_IMAGE_TABLENAME', '`news_images`');
}
if (!defined('NEWS_IMAGE_PRIMARYKEY')) {
    define('NEWS_IMAGE_PRIMARYKEY', 'id');
}
if (!defined('NEWS_IMAGE_SECONDARYKEY')) {
    define('NEWS_IMAGE_SECONDARYKEY', 'news_id');
}

if (!function_exists('newsImageDefault')) {
    /**
     * @return mixed[]
     */
    function newsImageDefault()
    {
        global $DB;

        return newsImageVirtual($DB->modelDefault(
            NEWS_IMAGE_TABLENAME,
            NEWS_IMAGE_PRIMARYKEY
        ));
    }
}
if (!function_exists('newsImageDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function newsImageDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            NEWS_IMAGE_TABLENAME,
            NEWS_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('newsImageExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function newsImageExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            NEWS_IMAGE_TABLENAME,
            NEWS_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('newsImageFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function newsImageFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            NEWS_IMAGE_TABLENAME,
            NEWS_IMAGE_PRIMARYKEY
        )) {
            return newsImageVirtual($row);
        }

        return null;
    }
}
if (!function_exists('newsImageGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function newsImageGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            NEWS_IMAGE_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                NEWS_IMAGE_TABLENAME,
                NEWS_IMAGE_PRIMARYKEY
            );
        }

        if ($model = newsImageFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('newsImageHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function newsImageHasValue($id, $field, $value)
    {
        return $value == newsImageGetValue($id, $field);
    }
}
if (!function_exists('newsImageQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function newsImageQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(NEWS_IMAGE_TABLENAME);

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
if (!function_exists('newsImageSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function newsImageSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = newsImageQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            NEWS_IMAGE_PRIMARYKEY,
            NEWS_IMAGE_TABLENAME,
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
if (!function_exists('newsImageSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function newsImageSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            NEWS_IMAGE_TABLENAME,
            NEWS_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('newsImageSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function newsImageSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = newsImageQuery(array(
                NEWS_IMAGE_PRIMARYKEY => $where,
            ));
        }

        return newsImageSearch($where, $order_by);
    }
}
if (!function_exists('newsImageToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function newsImageToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            NEWS_IMAGE_TABLENAME,
            NEWS_IMAGE_PRIMARYKEY
        );
    }
}
if (!function_exists('newsImageVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function newsImageVirtual($row)
    {
        $row['src'] = sprintf(
            '/news_image/%s-%s.%s',
            preg_replace('/[^a-zA-Z0-9]+/', '-', $row['caption']),
            $row['id'],
            'jpg'
        );

        return $row;
    }
}

if (!function_exists('newsImagesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function newsImagesDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            NEWS_IMAGE_TABLENAME,
            NEWS_IMAGE_SECONDARYKEY
        );
    }
}
if (!function_exists('newsImagesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function newsImagesFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            NEWS_IMAGE_TABLENAME,
            NEWS_IMAGE_PRIMARYKEY,
            NEWS_IMAGE_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = newsImageVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('newsImagesToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function newsImagesToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            NEWS_IMAGE_TABLENAME,
            NEWS_IMAGE_PRIMARYKEY,
            NEWS_IMAGE_SECONDARYKEY
        );
    }
}
