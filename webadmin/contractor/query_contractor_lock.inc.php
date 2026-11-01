<?php
if (!defined('CONTRACTOR_LOCK_TABLENAME')) {
    define('CONTRACTOR_LOCK_TABLENAME', '`generic_locks`');
}
if (!defined('CONTRACTOR_LOCK_PRIMARYKEY')) {
    define('CONTRACTOR_LOCK_PRIMARYKEY', 'id');
}
if (!defined('CONTRACTOR_LOCK_SECONDARYKEY')) {
    define('CONTRACTOR_LOCK_SECONDARYKEY', 'generic_id');
}

if (!function_exists('contractorLockDefault')) {
    /**
     * @return mixed[]
     */
    function contractorLockDefault()
    {
        global $DB;

        return contractorLockVirtual($DB->modelDefault(
            CONTRACTOR_LOCK_TABLENAME,
            CONTRACTOR_LOCK_PRIMARYKEY
        ));
    }
}
if (!function_exists('contractorLockDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function contractorLockDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            CONTRACTOR_LOCK_TABLENAME,
            CONTRACTOR_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorLockExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function contractorLockExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            CONTRACTOR_LOCK_TABLENAME,
            CONTRACTOR_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorLockFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function contractorLockFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            CONTRACTOR_LOCK_TABLENAME,
            CONTRACTOR_LOCK_PRIMARYKEY
        )) {
            return contractorLockVirtual($row);
        }

        return null;
    }
}
if (!function_exists('contractorLockGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function contractorLockGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            CONTRACTOR_LOCK_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                CONTRACTOR_LOCK_TABLENAME,
                CONTRACTOR_LOCK_PRIMARYKEY
            );
        }

        if ($model = contractorLockFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('contractorLockHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function contractorLockHasValue($id, $field, $value)
    {
        return $value == contractorLockGetValue($id, $field);
    }
}
if (!function_exists('contractorLockSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function contractorLockSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            CONTRACTOR_LOCK_TABLENAME,
            CONTRACTOR_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorLockToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function contractorLockToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            CONTRACTOR_LOCK_TABLENAME,
            CONTRACTOR_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorLockVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function contractorLockVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('contractorLocksDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function contractorLocksDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            CONTRACTOR_LOCK_TABLENAME,
            CONTRACTOR_LOCK_SECONDARYKEY
        );
    }
}
if (!function_exists('contractorLocksFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function contractorLocksFromDatabase($id)
    {
        global $DB;

        $result = [];
        foreach ($DB->childrenLoad(
            $id,
            CONTRACTOR_LOCK_TABLENAME,
            CONTRACTOR_LOCK_PRIMARYKEY,
            CONTRACTOR_LOCK_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = contractorLockVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('contractorLocksToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function contractorLocksToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            CONTRACTOR_LOCK_TABLENAME,
            CONTRACTOR_LOCK_PRIMARYKEY,
            CONTRACTOR_LOCK_SECONDARYKEY
        );
    }
}

if (!function_exists('contractorGetLock')) {
    function contractorGetLock($id)
    {
        global $DB;
        $row = $DB->query_first("SELECT *, UNIX_TIMESTAMP(`gl`.`expiry_date`) AS `expiry_time` FROM `generic_locks` AS `gl` WHERE `gl`.`section_name` = 'contractor'  LIMIT 1", MYSQLI_ASSOC);

        return $row;
    }
}
if (!function_exists('contractorSetLock')) {
    function contractorSetLock($id, $staff_id = 0, $timeout = 120)
    {
        global $DB;
        $section_name = formatSQLString("contractor");
        if (0 != $staff_id) {
            $DB->query(sprintf(
                "UPDATE `generic_locks` SET `expiry_date` = %s, `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `staff_id` = %d AND `expiry_date` > NOW() LIMIT 1",
                formatSQLDateTime(time() + $timeout),
                $section_name,
                $id,
                $staff_id
            ));
            if ($DB->affected_rows() == 0) {
                $DB->query(sprintf(
                    "INSERT INTO `generic_locks` (`section_name`, `generic_id`, `staff_id`, `expiry_date`, `created`, `modified`) VALUES (%s, %d, %d, %s, NOW(), NOW())",
                    $section_name,
                    $id,
                    $staff_id,
                    formatSQLDateTime(time() + $timeout)
                ));
            }

        } else {
            $DB->query(sprintf("UPDATE `generic_locks` SET `expiry_date` = NOW(), `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `expiry_date` > NOW()", $section_name, $id));
        }

    }
}
