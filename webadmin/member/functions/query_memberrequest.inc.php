<?php
if (!defined('MEMBER_REQUEST_TABLENAME')) {
    define('MEMBER_REQUEST_TABLENAME', "`highwaygroup`.`new_members_requests`");
}
/*
if (!defined('MEMBER_REQUEST_PRIMARYKEY')) {
    define('MEMBER_REQUEST_PRIMARYKEY', "id");
}
*/
if (!defined('MEMBER_REQUEST_SECONDARYKEY')) {
    define('MEMBER_REQUEST_SECONDARYKEY', "member_id");
}

if (!function_exists('memberRequestsDelete')) {
    function memberRequestsDelete($id)
    {
        global $DB;

        $DB->childrenDelete($id, MEMBER_REQUEST_TABLENAME, MEMBER_REQUEST_SECONDARYKEY);
    }
}
