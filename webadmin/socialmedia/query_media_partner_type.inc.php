<?php
if (!defined('PARTNER_LINKS_TABLENAME')) {
    define('PARTNER_LINKS_TABLENAME', '`media_partner_links`');
}
if (!defined('PARTNER_LINKS_PRIMARYKEY')) {
    define('PARTNER_LINKS_PRIMARYKEY', 'id');
}

if (!function_exists('partnerlinksFromDatabase')) {
    /**
     * @param int $member_id
     *
     * @return mixed[][]
     */
    function partnerlinksFromDatabase($partner_id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `partner_id` = %d ORDER BY id desc",
            PARTNER_LINKS_TABLENAME,
            $partner_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row[PARTNER_LINKS_PRIMARYKEY]] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('partnerLinksAdd')) {
    /**
     * @param int    $member_id
     * @param string $comment
     *
     * @return int
     */
    function partnerLinksAdd($partner_id, $data)
    {
        global $DB;

        if ($DB->query(sprintf(
            "INSERT INTO %s (`partner_id`, `media_type`, `member_count`, `media_links`) VALUES (%d, %s, %d, %s)",
            PARTNER_LINKS_TABLENAME,
            $partner_id,
            formatSQLString($data['media_type']),
            $data['member_count'],
            formatSQLString($data['media_links'])
        ))) {
            return $DB->insert_id();
        }

        return 0;
    }
}
if (!function_exists('partnerLinksDelete')) {
    /**
     * @param int    $member_id
     * @param string $comment
     *
     * @return int
     */
    function partnerLinksDelete($partner_id)
    {
        global $DB;

        $DB->query(sprintf(
            "DELETE FROM %s where `partner_id` = %d",
            PARTNER_LINKS_TABLENAME,
            $partner_id,
        ));
    }
}

