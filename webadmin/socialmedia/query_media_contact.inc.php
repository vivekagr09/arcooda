<?php
if (!defined('SOCIALMEDIA_CONTACT_TABLENAME')) {
    define('SOCIALMEDIA_CONTACT_TABLENAME', '`social_media_partner_contacts`');
}
if (!defined('SOCIALMEDIA_CONTACT_PRIMARYKEY')) {
    define('SOCIALMEDIA_CONTACT_PRIMARYKEY', 'id');
}
if (!defined('SOCIALMEDIA_CONTACT_SECONDARYKEY')) {
    define('SOCIALMEDIA_CONTACT_SECONDARYKEY', 'social_media_id');
}

if (!function_exists('socialMediaContactDefault')) {
    /**
     * @return mixed[]
     */
    function socialMediaContactDefault()
    {
        global $DB;

        return socialMediaContactVirtual($DB->modelDefault(
            SOCIALMEDIA_CONTACT_TABLENAME,
            SOCIALMEDIA_CONTACT_PRIMARYKEY
        ));
    }
}
if (!function_exists('socialMediaContactDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function socialMediaContactDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SOCIALMEDIA_CONTACT_TABLENAME,
            SOCIALMEDIA_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('socialMediaContactExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function socialMediaContactExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SOCIALMEDIA_CONTACT_TABLENAME,
            SOCIALMEDIA_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('socialMediaContactFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function socialMediaContactFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SOCIALMEDIA_CONTACT_TABLENAME,
            SOCIALMEDIA_CONTACT_PRIMARYKEY
        )) {
            return socialMediaContactVirtual($row);
        }

        return null;
    }
}
if (!function_exists('socialMediaContactGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function socialMediaContactGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SOCIALMEDIA_CONTACT_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SOCIALMEDIA_CONTACT_TABLENAME,
                SOCIALMEDIA_CONTACT_PRIMARYKEY
            );
        }

        if ($model = socialMediaContactFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('socialMediaContactHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function socialMediaContactHasValue($id, $field, $value)
    {
        return $value == socialMediaContactGetValue($id, $field);
    }
}
if (!function_exists('socialMediaContactQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function socialMediaContactQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(SOCIALMEDIA_CONTACT_TABLENAME);

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
if (!function_exists('socialMediaContactSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function socialMediaContactSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = socialMediaContactQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            SOCIALMEDIA_CONTACT_PRIMARYKEY,
            SOCIALMEDIA_CONTACT_TABLENAME,
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
if (!function_exists('socialMediaContactSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function socialMediaContactSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SOCIALMEDIA_CONTACT_TABLENAME,
            SOCIALMEDIA_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('socialMediaContactSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function socialMediaContactSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = socialMediaContactQuery(array(
                SOCIALMEDIA_CONTACT_PRIMARYKEY => $where,
            ));
        }

        return socialMediaContactSearch($where, $order_by);
    }
}
if (!function_exists('socialMediaContactToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function socialMediaContactToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SOCIALMEDIA_CONTACT_TABLENAME,
            SOCIALMEDIA_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('socialMediaContactVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function socialMediaContactVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('socialMediaContactsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function socialMediaContactsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            SOCIALMEDIA_CONTACT_TABLENAME,
            SOCIALMEDIA_CONTACT_SECONDARYKEY
        );
    }
}
if (!function_exists('socialMediaContactsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function socialMediaContactsFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            SOCIALMEDIA_CONTACT_TABLENAME,
            SOCIALMEDIA_CONTACT_PRIMARYKEY,
            SOCIALMEDIA_CONTACT_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = socialMediaContactVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('socialMediaContactsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function socialMediaContactsToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            SOCIALMEDIA_CONTACT_TABLENAME,
            SOCIALMEDIA_CONTACT_PRIMARYKEY,
            SOCIALMEDIA_CONTACT_SECONDARYKEY
        );
    }
}
