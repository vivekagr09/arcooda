<?php
if (!function_exists('getWebsitesList')) {
    function getWebsitesList()
    {
        global $DB;
        static $outarray = '';
        if ('' == $outarray) {
            $outarray = array();
            $query    = $DB->query("SELECT `id`, `website` FROM `highwaygroup`.`websites` ORDER BY `website`");
            while ($row = $DB->fetch_array($query)) {
                $outarray[$row['id']] = htmlspecialchars($row['website']);
            }

        }
        //$DB->free_result();
        return $outarray;
    }
}

if (!function_exists('get_vars')) {
    function get_vars()
    {
        return '?' . http_build_query($_GET);
    }
}
