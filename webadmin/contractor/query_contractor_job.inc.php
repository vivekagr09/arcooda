<?php
if (!defined('CONTRACTOR_JOB_TABLENAME')) {
    define('CONTRACTOR_JOB_TABLENAME', '`contractors_jobs`');
}
if (!defined('CONTRACTOR_JOB_PRIMARYKEY')) {
    define('CONTRACTOR_JOB_PRIMARYKEY', 'id');
}
if (!defined('CONTRACTOR_JOB_SECONDARYKEY')) {
    define('CONTRACTOR_JOB_SECONDARYKEY', 'contractor_id');
}

if (!function_exists('contractorJobDefault')) {
    /**
     * @return mixed[]
     */
    function contractorJobDefault()
    {
        global $DB;

        return contractorJobVirtual($DB->modelDefault(
            CONTRACTOR_JOB_TABLENAME,
            CONTRACTOR_JOB_PRIMARYKEY
        ));
    }
}
if (!function_exists('contractorJobDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function contractorJobDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            CONTRACTOR_JOB_TABLENAME,
            CONTRACTOR_JOB_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorJobExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function contractorJobExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            CONTRACTOR_JOB_TABLENAME,
            CONTRACTOR_JOB_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorJobFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function contractorJobFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            CONTRACTOR_JOB_TABLENAME,
            CONTRACTOR_JOB_PRIMARYKEY
        )) {
            return contractorJobVirtual($row);
        }

        return null;
    }
}
if (!function_exists('contractorJobGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function contractorJobGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            CONTRACTOR_JOB_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                CONTRACTOR_JOB_TABLENAME,
                CONTRACTOR_JOB_PRIMARYKEY
            );
        }

        if ($model = contractorJobFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('contractorJobHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function contractorJobHasValue($id, $field, $value)
    {
        return $value == contractorJobGetValue($id, $field);
    }
}
if (!function_exists('contractorJobSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function contractorJobSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            CONTRACTOR_JOB_TABLENAME,
            CONTRACTOR_JOB_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorJobToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function contractorJobToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            CONTRACTOR_JOB_TABLENAME,
            CONTRACTOR_JOB_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorJobVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function contractorJobVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('contractorJobsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function contractorJobsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            CONTRACTOR_JOB_TABLENAME,
            CONTRACTOR_JOB_SECONDARYKEY
        );
    }
}
if (!function_exists('contractorJobsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function contractorJobsFromDatabase($id)
    {
        global $DB;

        $result = [];
        foreach ($DB->childrenLoad(
            $id,
            CONTRACTOR_JOB_TABLENAME,
            CONTRACTOR_JOB_PRIMARYKEY,
            CONTRACTOR_JOB_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = contractorJobVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('contractorJobsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function contractorJobsToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            CONTRACTOR_JOB_TABLENAME,
            CONTRACTOR_JOB_PRIMARYKEY,
            CONTRACTOR_JOB_SECONDARYKEY
        );
    }
}
