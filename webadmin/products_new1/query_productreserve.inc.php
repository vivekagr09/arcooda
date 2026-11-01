<?php
include_once __DIR__ . '/query_product.inc.php';

if (!defined('PRODUCTRESERVE_TABLENAME')) {
    define('PRODUCTRESERVE_TABLENAME', '`arcooda_www`.`products_reserved`');
}
/*
if (!defined('PRODUCTRESERVE_PRIMARYKEY')) {
    //define('PRODUCTRESERVE_PRIMARYKEY', 'invoice_id');
}
if (!defined('PRODUCTRESERVE_SECONDARYKEY')) {
    //define('PRODUCTRESERVE_SECONDARYKEY', 'product_id');
}
if (!defined('PRODUCTRESERVE_TERTIARYKEY')) {
    //define('PRODUCTRESERVE_TERTIARYKEY', 'sub_id');
}
*/

if (!function_exists('productReserveStock')) {
    /**
     * @param int|string $product_str
     * @param int        $invoice_id
     * @param int        $qty
     *
     * @return void
     */
    function productReserveStock($product_str, $invoice_id, $qty = 1)
    {
        global $DB;

        $product_id    = 0;
        $sub_id        = 0;
        $product_split = productStrSplit($product_str, 2);
        extract($product_split);

        if ($qty) {
            $DB->query(sprintf(
                "INSERT INTO %s (`invoice_id`, `product_id`, `sub_id`, `qty`) VALUES (%d, %d, %d, %d) ON DUPLICATE KEY UPDATE `qty` = `qty` + %d",
                PRODUCTRESERVE_TABLENAME,
                $invoice_id,
                $product_id,
                $sub_id,
                $qty,
                $qty
            ));
        } else {
            // if qty is 0, then delete the row.
            $DB->query(sprintf(
                "DELETE FROM %s WHERE `invoice_id` = %d AND `product_id` = %d AND `sub_id` = %d LIMIT 1",
                PRODUCTRESERVE_TABLENAME,
                $invoice_id,
                $product_id,
                $sub_id
            ));
        }
    }
}
if (!function_exists('productReservedFromDatabase')) {
    /**
     * @param int|string $product_str
     *
     * @return int[]
     */
    function productReservedFromDatabase($product_str)
    {
        global $DB;

        $product_id    = 0;
        $sub_id        = 0;
        $product_split = productStrSplit($product_str, 2);
        extract($product_split);

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `invoice_id`, `qty` FROM %s WHERE `product_id` = %d AND `sub_id` = %d",
            PRODUCTRESERVE_TABLENAME,
            $product_id,
            $sub_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['invoice_id']] = $row['qty'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('productReservedStock')) {
    /**
     * @param int|string $product_str
     *
     * @return int
     */
    function productReservedStock($product_str)
    {
        global $DB;

        $product_id    = 0;
        $sub_id        = 0;
        $product_split = productStrSplit($product_str, 2);
        extract($product_split);

        if ($row = $DB->query_first(sprintf(
            "SELECT SUM(`qty`) AS `qty` FROM %s WHERE `product_id` = %d AND `sub_id` = %d",
            PRODUCTRESERVE_TABLENAME,
            $product_id,
            $sub_id
        ), MYSQLI_ASSOC)) {
            return $row['qty'];
        }

        return 0;
    }
}
