<?php
if (!defined('SOFTJOB_NOTE_TABLENAME')) {
    define('SOFTJOB_NOTE_TABLENAME', '`highwaygroup`.`bug_comments`');
}
if (!defined('SOFTJOB_NOTE_PRIMARYKEY')) {
    define('SOFTJOB_NOTE_PRIMARYKEY', 'id');
}
if (!defined('SOFTJOB_NOTE_SECONDARYKEY')) {
    define('SOFTJOB_NOTE_SECONDARYKEY', 'bug_id');
}

if (!function_exists('softjobNoteDefault')) {
    /**
     * @return mixed[]
     */
    function softjobNoteDefault()
    {
        global $DB;

        return softjobNoteVirtual($DB->modelDefault(
            SOFTJOB_NOTE_TABLENAME,
            SOFTJOB_NOTE_PRIMARYKEY
        ));
    }
}
if (!function_exists('softjobNoteDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function softjobNoteDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SOFTJOB_NOTE_TABLENAME,
            SOFTJOB_NOTE_PRIMARYKEY
        );
    }
}
if (!function_exists('softjobNoteExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function softjobNoteExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SOFTJOB_NOTE_TABLENAME,
            SOFTJOB_NOTE_PRIMARYKEY
        );
    }
}
if (!function_exists('softjobNoteFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function softjobNoteFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SOFTJOB_NOTE_TABLENAME,
            SOFTJOB_NOTE_PRIMARYKEY
        )) {
            return softjobNoteVirtual($row);
        }

        return null;
    }
}
if (!function_exists('softjobNoteGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function softjobNoteGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SOFTJOB_NOTE_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SOFTJOB_NOTE_TABLENAME,
                SOFTJOB_NOTE_PRIMARYKEY
            );
        }

        if ($model = softjobNoteFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('softjobNoteHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function softjobNoteHasValue($id, $field, $value)
    {
        return $value == softjobNoteGetValue($id, $field);
    }
}
if (!function_exists('softjobNoteQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    /*
    function softjobNoteQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
    */
}
if (!function_exists('softjobNoteSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function softjobNoteSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = softjobNoteQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            SOFTJOB_NOTE_PRIMARYKEY,
            SOFTJOB_NOTE_TABLENAME,
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
    */
}
if (!function_exists('softjobNoteSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function softjobNoteSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SOFTJOB_NOTE_TABLENAME,
            SOFTJOB_NOTE_PRIMARYKEY
        );
    }
}
if (!function_exists('softjobNoteSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function softjobNoteSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = softjobNoteQuery(array(
                SOFTJOB_NOTE_PRIMARYKEY => $where,
            ));
        }

        return softjobNoteSearch($where, $order_by);
    }
    */
}
if (!function_exists('softjobNoteToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    /*
    function softjobNoteToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SOFTJOB_NOTE_TABLENAME,
            SOFTJOB_NOTE_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('softjobNoteVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function softjobNoteVirtual($row)
    {
        $row['time_added'] = strtotime($row['date_added']);

        return $row;
    }
}

if (!function_exists('softjobNotesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function softjobNotesDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            SOFTJOB_NOTE_TABLENAME,
            SOFTJOB_NOTE_SECONDARYKEY
        );
    }
}
if (!function_exists('softjobNotesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
    function softjobNotesFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            SOFTJOB_NOTE_TABLENAME,
            SOFTJOB_NOTE_PRIMARYKEY,
            SOFTJOB_NOTE_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = softjobNoteVirtual($v);
        }

        return $result;
    }
    */
}
if (!function_exists('softjobNotesToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function softjobNotesToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            SOFTJOB_NOTE_TABLENAME,
            SOFTJOB_NOTE_PRIMARYKEY,
            SOFTJOB_NOTE_SECONDARYKEY
        );
    }
}

if (!function_exists('softjobNotesFromDatabase')) {
    function softjobNotesFromDatabase($softjob_id)
    {
        global $DB;
        $notes_list = array();
        $query      = $DB->query("SELECT id FROM `highwaygroup`.bug_comments WHERE bug_id=" . intval($softjob_id));
        while ($row = $DB->fetch_assoc($query)) {
            $notes_list[] = $row['id'];
        }

        return $notes_list;
    }
}
if (!function_exists('softjobNoteToDatabase')) {
    /**
     * @param int    $softjob_id
     * @param string $note
     *
     * @return int
     */
    function softjobNoteToDatabase($softjob_id, $note)
    {
        global $_GLOBALS;
        global $DB;

        $staff_id      = $_GLOBALS['staff']['id'];
        $staff_mapping = softjobGetOptionList('staff_mapping');
        if (0 != $staff_mapping[$staff_id]) {
            $staff_id = $staff_mapping[$staff_id];
        }

        $DB->query(sprintf(
            "INSERT INTO `highwaygroup`.`bug_comments` (`bug_id`, `date_added`, `user_id`, `comment`) VALUES (%d, NOW(), %d, %s)",
            $softjob_id,
            $staff_id,
            $DB->escape($note)
        ), MYSQLI_ASSOC);

        return $DB->insert_id();
    }
}
