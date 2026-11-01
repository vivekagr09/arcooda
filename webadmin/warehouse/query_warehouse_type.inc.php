<?php
if (!defined('WAREHOUSE_ONLINE_TABLENAME')) {
    define('WAREHOUSE_ONLINE_TABLENAME', '`tbl_warehouse_online`');
}
if (!defined('WAREHOUSE_ONLINE_PRIMARYKEY')) {
    define('WAREHOUSE_ONLINE_PRIMARYKEY', 'id');
}

if (!function_exists('warehouseOnlineFromDatabase')) {
    /**
     * @param int $member_id
     *
     * @return mixed[][]
     */
    function warehouseOnlineFromDatabase($warehouse_id )
    {
        global $DB;

        $result = array();
         return  $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `warehouse_id` = %d ORDER BY id desc",
            WAREHOUSE_ONLINE_TABLENAME,
            $warehouse_id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('warehouseOnlineAdd')) {
    /**
     * @param int    $member_id
     * @param string $comment
     *
     * @return int
     */
    function warehouseOnlineAdd($warehouse_id, $data)
    {
        global $DB;
        if ($DB->query(sprintf(
            "INSERT INTO %s (`warehouse_id`,`country_location`, `warehouse`, `delivery_time`, `freight_costs`,`notes`,`show_warehouse`, `show_delivery_time`, `show_freight_costs`, `show_notes`) VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            WAREHOUSE_ONLINE_TABLENAME,
            $warehouse_id,
            formatSQLString($data['country_location']),
            formatSQLString($data['warehouse']),
            formatSQLString($data['delivery_time']),
            formatSQLString($data['freight_costs']),
            formatSQLString($data['notes']),
            formatSQLString($data['show_warehouse']),
            formatSQLString($data['show_delivery_time']),
            formatSQLString($data['show_freight_costs']),
            formatSQLString($data['show_notes'])
        ))) {
            return $DB->insert_id();
        }

        return 0;
    }
}
if (!function_exists('warehouseOnlineDelete')) {
    /**
     * @param int    $member_id
     * @param string $comment
     *
     * @return int
     */
    function warehouseOnlineDelete($warehouse_id)
    {
        global $DB;

        $DB->query(sprintf(
            "DELETE FROM %s where `warehouse_id` = %d",
            WAREHOUSE_ONLINE_TABLENAME,
            $warehouse_id,
        ));
    }
}

