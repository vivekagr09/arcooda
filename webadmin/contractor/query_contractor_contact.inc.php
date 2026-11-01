<?php
if (!defined('CONTRACTOR_CONTACT_TABLENAME')) {
    define('CONTRACTOR_CONTACT_TABLENAME', '`contractor_contacts`');
}
if (!defined('CONTRACTOR_CONTACT_PRIMARYKEY')) {
    define('CONTRACTOR_CONTACT_PRIMARYKEY', 'id');
}
if (!defined('CONTRACTOR_CONTACT_SECONDARYKEY')) {
    define('CONTRACTOR_CONTACT_SECONDARYKEY', 'contractor_id');
}

if (!function_exists('contractorContactDefault')) {
    /**
     * @return mixed[]
     */
    function contractorContactDefault()
    {
        global $DB;

        return contractorContactVirtual($DB->modelDefault(
            CONTRACTOR_CONTACT_TABLENAME,
            CONTRACTOR_CONTACT_PRIMARYKEY
        ));
    }
}
if (!function_exists('contractorContactDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function contractorContactDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            CONTRACTOR_CONTACT_TABLENAME,
            CONTRACTOR_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorContactExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function contractorContactExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            CONTRACTOR_CONTACT_TABLENAME,
            CONTRACTOR_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorContactFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function contractorContactFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            CONTRACTOR_CONTACT_TABLENAME,
            CONTRACTOR_CONTACT_PRIMARYKEY
        )) {
            return contractorContactVirtual($row);
        }

        return null;
    }
}
if (!function_exists('contractorContactGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function contractorContactGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            CONTRACTOR_CONTACT_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                CONTRACTOR_CONTACT_TABLENAME,
                CONTRACTOR_CONTACT_PRIMARYKEY
            );
        }

        if ($model = contractorContactFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('contractorContactHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function contractorContactHasValue($id, $field, $value)
    {
        return $value == contractorContactGetValue($id, $field);
    }
}
if (!function_exists('contractorContactSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function contractorContactSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            CONTRACTOR_CONTACT_TABLENAME,
            CONTRACTOR_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorContactToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function contractorContactToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            CONTRACTOR_CONTACT_TABLENAME,
            CONTRACTOR_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorContactVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function contractorContactVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('contractorContactsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function contractorContactsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            CONTRACTOR_CONTACT_TABLENAME,
            CONTRACTOR_CONTACT_SECONDARYKEY
        );
    }
}
if (!function_exists('contractorContactsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function contractorContactsFromDatabase($id)
    {
        global $DB;

        $result = [];
        foreach ($DB->childrenLoad(
            $id,
            CONTRACTOR_CONTACT_TABLENAME,
            CONTRACTOR_CONTACT_PRIMARYKEY,
            CONTRACTOR_CONTACT_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = contractorContactVirtual($v);
        }

        uasort($result, function ($a, $b) {
            return strcmp($b['main'], $a['main']) ?: strcmp($b['send_email_cc'], $a['send_email_cc']) ?: ($b['id'] - $a['id']);
        });

        return $result;
    }
}
if (!function_exists('contractorContactsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function contractorContactsToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            CONTRACTOR_CONTACT_TABLENAME,
            CONTRACTOR_CONTACT_PRIMARYKEY,
            CONTRACTOR_CONTACT_SECONDARYKEY
        );
    }
}
