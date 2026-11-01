<?php
if (!function_exists('memberJobsFromDatabase')) {
    function memberJobsFromDatabase($id)
    {
        global $DB;

        $result = array();

        /*
        foreach ($DB->childrenLoad(
            $id,
            MEMBER_JOB_TABLENAME,
            MEMBER_JOB_PRIMARYKEY,
            MEMBER_JOB_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = memberJobVirtual($v);
        }
        */

        return $result;
    }
}
