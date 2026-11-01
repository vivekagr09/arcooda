<?php
if (!defined('PRODUCTINVENTORY_TABLENAME')) {
    define('PRODUCTINVENTORY_TABLENAME', '`arcooda_www`.`products_inventory`');
}
if (!defined('PRODUCTINVENTORY_PRIMARYKEY')) {
    define('PRODUCTINVENTORY_PRIMARYKEY', 'id');
}

if (!defined('PRODUCT_AVAILABILITY_2_4_WEEKS')) {
    define('PRODUCT_AVAILABILITY_2_4_WEEKS', 11);
}

if (!function_exists('productReduceStock')) {
    /**
     * @param int|string $product_str
     * @param int        $reduce
     * @param bool       $used
     *
     * @return true
     */
    function productReduceStock($product_str, $reduce, $used = false)
    {
        global $DB;

        $stock = productPurchaseSummary($product_str, false, $used);
        $qty   = productGetValue($product_str, 'qty');
        if ($qty < $reduce) {
            foreach ($stock as $row) {
                $DB->query(sprintf(
                    "UPDATE %s SET `stock_left` = 0 WHERE `%s` = %d LIMIT 1",
                    PRODUCTINVENTORY_TABLENAME,
                    PRODUCTINVENTORY_PRIMARYKEY,
                    $row[PRODUCTINVENTORY_PRIMARYKEY]
                ));
            }
        } else {
            $stock_left = $reduce;
            foreach ($stock as $row) {
                if ($row['stock_left'] >= $stock_left) {
                    $DB->query(sprintf(
                        "UPDATE %s SET `stock_left` = %d WHERE `%s` = %d LIMIT 1",
                        PRODUCTINVENTORY_TABLENAME,
                        $row['stock_left'] - $stock_left,
                        PRODUCTINVENTORY_PRIMARYKEY,
                        $row[PRODUCTINVENTORY_PRIMARYKEY]
                    ));
                    break;
                } else {
                    $DB->query(sprintf(
                        "UPDATE %s SET `stock_left` = 0 WHERE `%s` = %d LIMIT 1",
                        PRODUCTINVENTORY_TABLENAME,
                        PRODUCTINVENTORY_PRIMARYKEY,
                        $row[PRODUCTINVENTORY_PRIMARYKEY]
                    ));
                    $stock_left -= $row['stock_left'];
                }
                if ($stock_left <= 0) {
                    break;
                }
            }
        }

        productSetValue($product_str, 'qty', $qty - $reduce);

        return true;
    }
}
