<?php
if (!defined('INVOICE_JOB_TABLENAME')) {
    define('INVOICE_JOB_TABLENAME', '`highwaygroup`.`invoice_jobs`');
}
if (!defined('INVOICE_JOB_PRIMARYKEY')) {
    define('INVOICE_JOB_PRIMARYKEY', 'job_id');
}
if (!defined('INVOICE_JOB_SECONDARYKEY')) {
    define('INVOICE_JOB_SECONDARYKEY', 'invoice_id');
}

if (!function_exists('invoiceJobDefault')) {
    /**
     * @return mixed[]
     */
    function invoiceJobDefault()
    {
        global $DB;

        return invoiceJobVirtual($DB->modelDefault(
            INVOICE_JOB_TABLENAME,
            INVOICE_JOB_PRIMARYKEY
        ));
    }
}
if (!function_exists('invoiceJobDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceJobDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            INVOICE_JOB_TABLENAME,
            INVOICE_JOB_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceJobExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function invoiceJobExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            INVOICE_JOB_TABLENAME,
            INVOICE_JOB_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceJobFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function invoiceJobFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            INVOICE_JOB_TABLENAME,
            INVOICE_JOB_PRIMARYKEY
        )) {
            return invoiceJobVirtual($row);
        }

        return null;
    }
}
if (!function_exists('invoiceJobGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function invoiceJobGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            INVOICE_JOB_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                INVOICE_JOB_TABLENAME,
                INVOICE_JOB_PRIMARYKEY
            );
        }

        if ($model = invoiceJobFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('invoiceJobHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function invoiceJobHasValue($id, $field, $value)
    {
        return invoiceJobGetValue($id, $field) == $value;
    }
}
if (!function_exists('invoiceJobQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function invoiceJobQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('invoiceJobSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function invoiceJobSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = invoiceJobQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            INVOICE_JOB_PRIMARYKEY,
            INVOICE_JOB_TABLENAME,
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
if (!function_exists('invoiceJobSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function invoiceJobSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            INVOICE_JOB_TABLENAME,
            INVOICE_JOB_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceJobSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function invoiceJobSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = invoiceJobQuery(array(
                INVOICE_JOB_PRIMARYKEY => $where,
            ));
        }

        return invoiceJobSearch($where, $order_by);
    }
}
if (!function_exists('invoiceJobToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    /*
function invoiceJobToDatabase(&$item)
{
global $DB;

return $DB->modelSave(
$item,
INVOICE_JOB_TABLENAME,
INVOICE_JOB_PRIMARYKEY
);
}
 */
}
if (!function_exists('invoiceJobVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function invoiceJobVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('invoiceJobsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceJobsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            INVOICE_JOB_TABLENAME,
            INVOICE_JOB_SECONDARYKEY
        );
    }
}
if (!function_exists('invoiceJobsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
    function invoiceJobsFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            INVOICE_JOB_TABLENAME,
            INVOICE_JOB_PRIMARYKEY,
            INVOICE_JOB_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = invoiceJobVirtual($v);
        }

        return $result;
    }
    */
}
if (!function_exists('invoiceJobsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function invoiceJobsToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            INVOICE_JOB_TABLENAME,
            INVOICE_JOB_PRIMARYKEY,
            INVOICE_JOB_SECONDARYKEY
        );
    }
}

if (!function_exists("invoiceJobToDatabase")) {
    /**
     * @param int     $id
     * @param mixed[] $job_info
     *
     * @return void
     */
    function invoiceJobToDatabase($id, $job_info)
    {
        global $DB;
        $DB->query(sprintf(
            "INSERT INTO %s SET `%s` = %d, `job_type` = %d, `job_content` = %s",
            INVOICE_JOB_TABLENAME,
            INVOICE_JOB_SECONDARYKEY,
            $id,
            $job_info['job_type'],
            $DB->escape($job_info['job_content'])
        ));
    }
}
if (!function_exists("invoiceJobsFromDatabase")) {
    /**
     * @param int $id
     *
     * @return int[]
     */
    function invoiceJobsFromDatabase($id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `%s` FROM %s WHERE `%s` = %d ORDER BY `%s` ASC",
            INVOICE_JOB_PRIMARYKEY,
            INVOICE_JOB_TABLENAME,
            INVOICE_JOB_SECONDARYKEY,
            $id,
            INVOICE_JOB_PRIMARYKEY
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row[INVOICE_JOB_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists("invoiceJobsByInvoice")) {
    /**
     * @return int[][]
     */
    function invoiceJobsByInvoice()
    {
        global $DB;

        static $result = null;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `%s`, `%s` FROM %s",
            INVOICE_JOB_PRIMARYKEY,
            INVOICE_JOB_SECONDARYKEY,
            INVOICE_JOB_TABLENAME
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row[INVOICE_JOB_SECONDARYKEY]][] = $row[INVOICE_JOB_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
