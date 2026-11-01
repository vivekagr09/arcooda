<?php
if (!defined('STAFF_REVIEW_TABLENAME')) {
    define('STAFF_REVIEW_TABLENAME', '`highwaygroup`.`users_reviews`');
}
if (!defined('STAFF_REVIEW_PRIMARYKEY')) {
    define('STAFF_REVIEW_PRIMARYKEY', 'review_id');
}
if (!defined('STAFF_REVIEW_SECONDARYKEY')) {
    define('STAFF_REVIEW_SECONDARYKEY', 'staff_id');
}

if (!function_exists('staffReviewToDatabase')) {
    /**
     * @param int     $staff_id
     * @param mixed[] $review_info
     *
     * @return int
     */
    function staffReviewToDatabase($staff_id, $review_info)
    {
        global $DB;
        global $_GLOBALS;

        if ($DB->query(sprintf(
            "INSERT INTO %s (`%s`, `reviewer_id`, `review_comments`, `review_date`) VALUES (%d, %d, %s, %s)",
            STAFF_REVIEW_TABLENAME,
            STAFF_REVIEW_SECONDARYKEY,
            $staff_id,
            $_GLOBALS['staff']['id'],
            $DB->escape($review_info['review_comments']),
            $DB->escape(formatDateTime($review_info['review_time']))
        ))) {
            return $DB->insert_id();
        }

        return 0;
    }
}
if (!function_exists('staffReviewsFromDatabase')) {
    /**
     * @param int $staff_id
     *
     * @return mixed[][]
     */
    function staffReviewsFromDatabase($staff_id)
    {
        global $DB;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d ORDER BY `review_date` ASC",
            STAFF_REVIEW_TABLENAME,
            STAFF_REVIEW_SECONDARYKEY,
            $staff_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = staffReviewVirtual($row);
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('staffReviewVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function staffReviewVirtual($row)
    {
        $row['review_time'] = (is_null($row['review_date'])) ? null : strtotime($row['review_date']);

        return $row;
    }
}
