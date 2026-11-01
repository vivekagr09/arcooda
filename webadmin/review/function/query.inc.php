<?php
if (!defined('REVIEW_TABLENAME')) {
    define('REVIEW_TABLENAME', "`products_reviews`");
}
if (!defined('REVIEW_PRIMARYKEY')) {
    define('REVIEW_PRIMARYKEY', "id");
}
if (!function_exists('reviewGenericToDatabase')) {
    function reviewGenericToDatabase(&$item, $table, $primary_key = 'id') {
        global $DB, $_GLOBALS;
        $id         = intval($item[$primary_key]);
        $fields     = $DB->table_columns($table);
        $old_vals   = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
        if ($old_vals) {
            $set_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item) && $item[$k] !=$old_vals[$k]) {
                    if (is_array($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape($item[$k]);
                    } else {
                        $set_vals[$k] = "`" . $k . "` = NULL";
                    }
                }
            }
            if (count($set_vals)) {
                if($table=='`supplier_product`'){
                   $set_vals['updated_at'] = "`updated_at` = NOW()";
                }

                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                ));
            }
        } else {                    
            $insert_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item)) {
                    if (is_array($item[$k])) {
                        $insert_vals[$k] = $DB->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $insert_vals[$k] = $DB->escape($item[$k]);
                    } else {
                        $insert_vals[$k] = "NULL";
                    }
                }
            }
            if (count($insert_vals)) {
                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    $table,
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", $insert_vals)
                ));

                $id = $DB->insert_id();


            }
        }
        return $id;
    }
}
if (!function_exists('reviewGenericFromPost')) {
    function reviewGenericsFromPost($posted, $table)
    {
        global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->table_columns($table);
        foreach ($posted as $k1 => $v1) {
            foreach ($fields as $k2 => $v2) {
                if ($k2 == $k1) {
                   $result[$k1] = $v1;
                }
            }
        }
        return $result;
    }
}
if (!function_exists('reviewGenericsFromDatabase')) {
    function reviewGenericsFromDatabase($id, $table, $primary_key = 'id', $secondary_key = 'product_id',$orderby='') {
        global $DB;
        $result = array();
        $query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE  `%s` = %d  %s",
            $table,
            $secondary_key,
            $id,
            $orderby,
        ));
        while ($row = $DB->fetch_assoc($query)) {
            if (is_null($primary_key)) {
                $result[] = $row;
            } else {
                $result[$row[$primary_key]] = $row;
            }
        }
        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('reviewGenericDelete')) {
    function reviewGenericDelete($id, $table, $primary_key = 'id') {
        global $DB;
        $DB->query(sprintf(
            "DELETE from %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ));
        return $DB->affected_rows();
    }
}
if (!function_exists('reviewDeleteFromDatabase')) {
    /**
     * Remove the review from the database
     * Updates the 'review' table in the database by deleting the
     * the row with id $id
     *
     * @param int $id
     *
     * @return void
     */
    function reviewDeleteFromDatabase($id)
    {
        global $DB;

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d LIMIT 1",
            REVIEW_TABLENAME,
            REVIEW_PRIMARYKEY,
            $id
        ));
    }
}
if (!function_exists('reviewExists')) {
    /**
     * Checks if a review is in the database
     * Searches the 'review' table in the database for row
     * with id $id; returns true if found, false if not
     *
     * @param int $id
     *
     * @return bool
     */
    function reviewExists($id)
    {
        global $DB;

        return !!$DB->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `%s` = %d LIMIT 1",
            REVIEW_TABLENAME,
            REVIEW_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC);
    }
}

if (!function_exists('reviewFromDatabase')) {
    /**
     * Returns review from database with provided id
     * Finds the first row in 'review' table which id matches parameter,
     * then adds that row with additional unix-timestamps for date_added and
     * expiry to static array; returns this new row if found, or false if not
     *
     * @param int $id
     *
     * @return mixed[]|null
     */
    function reviewFromDatabase($id)
    {
        global $DB;

        $fields = array('*');
        foreach (reviewGetOptionList('ext_fields') as $k => $v) {
            $fields[] = $v . " AS `" . $k . "`";
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT %s FROM %s WHERE `%s` = %d LIMIT 1",
            implode(", ", $fields),
            REVIEW_TABLENAME,
            REVIEW_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC)) {
            return reviewVirtual($row);
        }

        return null;
    }
}
if (!function_exists('reviewGetOptionList')) {
    /**
     * Returns array of options for table column/s
     *
     * @param string $table_name
     *
     * @return mixed
     */
    function reviewGetOptionList($table_name)
    {
        global $DB;

        static $option_cache = array();
        if (isset($option_cache[$table_name])) {
            return $option_cache[$table_name];
        }

        $table       = '';
        $key         = '';
        $value       = '';
        $order_by    = '';
        $option_list = array();
        switch ($table_name) {
            case 'image_path':
            case 'thumb_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/images/review/';
            case 'active':
                return array('n' => 'No', 'y' => 'Yes');
            case 'all_fields':
                return array_merge(reviewGetOptionList('base_fields'), reviewGetOptionList('ext_fields'));
            case 'all_fields_types':
                return array_merge(reviewGetOptionList('base_fields_types'), reviewGetOptionList('ext_fields_types'));
            case 'base_fields':
                $fields = array();
                foreach (getTableColumns(REVIEW_TABLENAME) as $key) {
                    $fields[$key] = '`' . $key . '`';
                }

                return $fields;
            case 'base_fields_types':
                return getTableFields(REVIEW_TABLENAME);
            case 'joined_fields':
                return array();
            case 'ext_fields':
            case 'extra_fields':
                return array();
            case 'ext_fields_types':
                return array();
            default:
                echo 'reviewGetOptionList(' . json_encode($table_name) . ') is undefined<br />';
                break;
        }
        if ('' != $table) {
            if ($query = $DB->query(
                "SELECT " . $key . "," . $value . " FROM " . $table . (('' != $order_by) ? " ORDER BY " . $order_by . " ASC" : "")
            )) {
                while ($row = $DB->fetch_array($query)) {
                    $option_list[$row[0]] = $row[1];
                }
                $DB->free_result($query);
            }
        }
        $option_cache[$table_name] = $option_list;
        return $option_list;
    }
}
if (!function_exists('reviewGetValue')) {
    /**
     * Get value stored in specified row/column of database
     * Searches the 'review' table in the database for row
     * with id $id, then gets value stored in column $field;
     * returns value if found, null if not
     *
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function reviewGetValue($id, $field)
    {
        global $DB;
        $fields = reviewGetOptionList("all_fields");
        if (array_key_exists($field, $fields)) {
            $row = $DB->query_first(sprintf(
                "SELECT %s AS `%s` FROM %s WHERE `%s` = %d LIMIT 1",
                $fields[$field],
                $field,
                REVIEW_TABLENAME,
                REVIEW_PRIMARYKEY,
                $id
            ), MYSQLI_ASSOC);
            return $row[$field];
        }
        return null;
    }
}
if (!function_exists('reviewQuery')) {
    /**
     * Generate a SQL WHERE expression to find matching review.
     *
     * @param array $eq (optional) Array of key => values that the
     *    review should match when searching. Values may be arrays.
     * @param array $lt (optional) Array of key => values that the
     *    review should be less than when searching. Values may be arrays.
     * @param array $gt (optional) Array of key => values that the
     *    review should be greater than when searching. Values may be arrays.
     */
    function reviewQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        // Simple case, all review
        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $whereAND    = [];
        $field_types = reviewGetOptionList('all_fields_types');
        foreach (reviewGetOptionList('all_fields') as $field => $expr) {
            if (array_key_exists($field, $eq)) {
                $whereOR = [];
                foreach ((array)($eq[$field]) as $v) {
                    if (array_key_exists($field, $field_types) && substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                        $whereOR[] = sprintf("FIND_IN_SET(%s, %s)", $DB->escape($v), $expr);
                    } else {
                        $whereOR[] = sprintf("%s = %s", $expr, $DB->escape($v));
                    }
                }
                if (count($whereOR)) {
                    $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                }
            }
            if (array_key_exists($field, $lt)) {
                foreach ((array)($lt[$field]) as $v) {
                    $whereAND[] = sprintf("%s < %s", $expr, $DB->escape($v));
                }
            }
            if (array_key_exists($field, $gt)) {
                foreach ((array)($gt[$field]) as $v) {
                    $whereAND[] = sprintf("%s > %s", $expr, $DB->escape($v));
                }
            }
        }

        foreach ((array)($eq['any_text'] ?? []) as $findme) {
            if (!empty($findme)) {
                $whereOR = array();
                foreach (array(
                    '`' . REVIEW_PRIMARYKEY . '`',
                    '`text`',
                ) as $expr) {
                    $whereOR[] = sprintf(
                        "%s LIKE %s",
                        $expr,
                        formatSQLLikeString($findme)
                    );
                }
                if (count($whereOR)) {
                    $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                }
            }
        }

        if (1 == count($whereAND)) {
            return implode(" AND ", $whereAND);
        } elseif (count($whereAND)) {
            return sprintf("(%s)", implode(" AND ", $whereAND));
        }
        return "1";
    }
}
if (!function_exists('reviewRandom')) {
    /**
     * Returns a random review
     * Collects review ids from database that are active and are
     * not expired, and have review_type $type (when $type != 0);
     * returns a random id from this collection, or false if none
     *
     * @param int $type
     *
     * @return int
     */
    function reviewRandom($type = 0)
    {
        global $DB;
        $now = date("'Y-m-d 00:00:00'");

        $results = array();
        if ($query = $DB->query(
            "SELECT `" . REVIEW_PRIMARYKEY . "` FROM " . REVIEW_TABLENAME . " WHERE `active` = 'y' AND (`expiry` >= " . $now . " OR `expiry` = '0000-00-00 00:00:00')" . (($type > 0) ? (" AND `review_type` = " . intval($type)) : "")
        )) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row[REVIEW_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        shuffle($results);
        return reset($results);
    }
}
if (!function_exists('reviewearch')) {
    /**
     * Search for review based on criteria
     *
     * @param mixed[]|string $where
     *
     * @return int[]
     */
    function reviewearch($where = '1')
    {
        global $DB;

        // Support legacy usage
        if (is_array($where)) {
            $where = reviewQuery($where);
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `%s` FROM %s WHERE %s ORDER BY `%s` DESC",
            REVIEW_PRIMARYKEY,
            REVIEW_TABLENAME,
            $where,
            REVIEW_PRIMARYKEY
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row[REVIEW_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('reviewFromPost')) {
    function reviewFromPost($posted)
    {
         global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->table_columns(REVIEW_TABLENAME);
        foreach ($posted as $k1 => $v1) {
            foreach ($fields as $k2 => $v2) {
                if ($k2 == $k1) {
                   $result[$k1] = $v1;
                }
            }
        }
        return $result;
    }
}
if (!function_exists('reviewToDatabase')) {
    /**
     * Add a review to the database
     * If 'action' == 'do_add', insert new review
     * into database, else update existing review
     *
     * @param mixed[] $posted
     *
     * @return int
     */
    function reviewToDatabase($posted)
    {
        global $_GLOBALS;
        global $DB;
        $postedAttchment = $posted['attachments'] ?? $posted['item']['attachments']??[];
        unset($posted['attachments']);
        $add    = ('do_add' == $posted['action']);
        $id     = intval($posted['id']);
        $sql    = (($add) ? "INSERT INTO" : "UPDATE") . " " . REVIEW_TABLENAME;
        $review = $posted['item'];
        $set = array();
        foreach ($review as $k => $v) {
            $set[$k] = "`" . $k . "` = " . $DB->escape($v);
        }
        $sql .= " SET " . implode(', ', $set) . (($add) ? "" : (" WHERE `" . REVIEW_PRIMARYKEY . "` = " . intval($id)));
        $DB->query($sql);

        if ($add) {
            $id = intval($DB->insert_id());
        }
        if(count($postedAttchment) > 0){
            foreach ($postedAttchment as $key => $value) {
                $item['review_id'] = $id;
                $item['id'] = $value;
                reviewGenericToDatabase($item,'`product_reviews_images`','id');
            }
        }
        return $id;
    }
}
if (!function_exists('reviewViewsReset')) {
    /**
     * Reset views of a review
     * Finds the review with id $id in database and sets it's
     * 'views' to 0
     *
     * @param int $id
     *
     * @return void
     */
    function reviewViewsReset($id)
    {
        global $DB;

        $DB->query(sprintf(
            "UPDATE %s SET `views` = 0 WHERE `%s` = %d LIMIT 1",
            REVIEW_TABLENAME,
            REVIEW_PRIMARYKEY,
            $id
        ));
    }
}
if (!function_exists('reviewVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function reviewVirtual($row)
    {
        global $_GLOBALS;

        return $row;
    }
}
if (!function_exists('reviewSetValue')) {
    function reviewSetValue($id, $field, $value)
    {
        global $DB;
        $fields = $DB->table_columns(REVIEW_TABLENAME);
        if (array_key_exists($field, $fields)) {
            $DB->query(sprintf(
                "UPDATE %s SET `%s` = %s WHERE `id` = %d LIMIT 1",
                REVIEW_TABLENAME,
                $field,
                $DB->escape($value),
                $id
            ));
            return true;
        }
        return false;
    }
}
if (!function_exists('memberProductReview')) {
    function memberProductReview($page = 0)
    {
        global $DB,$_GLOBALS;
        $limit = 10;
        $offset = $page * $limit;
        array('product'=>[] ,'page','page_url','pages');
        if ($query = $DB->query("SELECT products.product_id pro_id,reviews.* from highwaygroup.invoice_products AS products INNER JOIN highwaygroup.invoices AS invoice ON products.invoice_id = invoice.invoice_id LEFT JOIN arcooda_www.products_reviews as reviews ON products.product_id = reviews.product_id  AND reviews.user_id = invoice.member_id WHERE invoice.invoice_status IN ('processed', 'completed') AND invoice.member_id = ".$_GLOBALS['member']['id']." AND invoice.site_id = '39' AND products.product_id > 0 GROUP BY products.product_id ORDER BY invoice.invoice_id DESC LIMIT ".$limit." OFFSET ".$offset." ")) {
            while ($row = $DB->fetch_assoc($query)) {

                $result['product'][] = $row;
            }
            $DB->free_result($query);

            $resultData = $DB->query_first("SELECT count(DISTINCT products.product_id) AS total  from highwaygroup.invoice_products AS products INNER JOIN highwaygroup.invoices AS invoice ON products.invoice_id = invoice.invoice_id WHERE invoice.invoice_status IN ('processed', 'completed') AND invoice.site_id = '39' AND invoice.member_id = ".$_GLOBALS['member']['id']." ",MYSQLI_ASSOC);
            $totalPage = intval($resultData['total']);
            if($totalPage > 0){
                $result['pages'] = ( intval($totalPage/$limit) + ($totalPage % $limit > 0 ? 1 : 0));
            }else{
                 $result['pages'] = -1;
            }
            
            $result['page'] = $page;

            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('invoiceProductReview')) {
    function invoiceProductReview()
    {
        global $DB, $_GLOBALS;
        $result = array();
        
        $query = $DB->query("SELECT 
                                products.product_id AS pro_id, 
                                reviews.*, 
                                invoice.invoice_id
                            FROM 
                                highwaygroup.invoice_products AS products
                            INNER JOIN 
                                highwaygroup.invoices AS invoice 
                                ON products.invoice_id = invoice.invoice_id
                            INNER JOIN 
                                arcooda_www.products_reviews AS reviews 
                                ON products.product_id = reviews.product_id
                                AND invoice.member_id = reviews.user_id
                            WHERE 
                                invoice.invoice_status IN ('processed', 'completed')
                            GROUP BY 
                                reviews.id
                            ORDER BY 
                                invoice.invoice_id DESC;
                            ");

        // Fetch and store all the product reviews
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        // Free the result from memory
        $DB->free_result($query);

        return $result;
    }
}
if(!function_exists('getProductReviewDetails')) {
    function getProductReviewDetails($product_id)
    {
        global $DB;

        $result = array('review_count' => 0, 'average_rating' => 0, 'latest_review' => null);

        $query = $DB->query("SELECT 
                COUNT(reviews.id) AS review_count,
                AVG(reviews.rating) AS average_rating,
                (SELECT reviews FROM arcooda_www.products_reviews 
                    WHERE product_id = $product_id 
                    ORDER BY review_date DESC LIMIT 1) AS latest_review
            FROM arcooda_www.products_reviews AS reviews
            WHERE reviews.product_id = $product_id
        ");

        if ($row = $DB->fetch_assoc($query)) {
            // Populate the result array with data
            $result['review_count'] = $row['review_count'];
            $result['average_rating'] = number_format($row['average_rating'], 2); // Format average to 2 decimal places
            $result['latest_review'] = $row['latest_review'];
        }

        // Free the result set
        $DB->free_result($query);

        return $result;
    }

}
if(!function_exists('getProductsReview')) {
    function getProductsReview($criteria)
    {
        global $DB;
        $whereSearch = '';
        $whereAND    = array();
        $allowedFilter = ['any_text','pr.section','review.reviews'];
        foreach ($criteria as $k => $v) {
            if(in_array($k, $allowedFilter)){
                if(!empty($v)){
                    if ($k == 'any_text' ) {
                       foreach (array(
                            '`pr`.`product_name`',
                            '`pr`.`id`'
                        ) as $expr) {
                            $whereOR[] = sprintf(
                                "%s LIKE %s",
                                $expr,
                                formatSQLLikeString($v)
                            );
                        }
                        if (count($whereOR)) {
                            $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                        }
                    }else{
                         $whereAND[] = sprintf("%s = %s", $k, $DB->escape($v));
                    }
                }
            }
        }
        if (1 == count($whereAND)) {
            $whereSearch =  'WHERE ' . implode(" AND ", $whereAND);
        } elseif (count($whereAND)) {
            $whereSearch =  'WHERE ' . sprintf("(%s)", implode(" AND ", $whereAND));
        }
        $result = array();
        $orderBy = 'ORDER BY `review`.`id` DESC';
        if(array_key_exists('ranking', $criteria) && !empty($criteria['ranking'])){
            $orderBy = ($criteria['ranking'] == '1') ? ' ORDER BY average_rating DESC ' : ' ORDER BY average_rating ASC ';
        }
        $query =$DB->query(sprintf(
        "SELECT review.product_id, pr.product_name,COUNT(review.id) AS total_reviews, AVG(review.reviews) AS average_rating, latest_review.reviews AS latest_review FROM arcooda_www.products_reviews AS review
        JOIN (
                SELECT product_id, reviews, MAX(created_at) AS latest_created_at
                FROM arcooda_www.products_reviews
                GROUP BY product_id
            ) AS latest_review 
        ON review.product_id = latest_review.product_id 
        INNER JOIN products AS pr ON review.product_id = pr.id
        %s
        GROUP BY 
        review.product_id
        %s
        ",
        $whereSearch,
        $orderBy
        ));

        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }
        // Free the result set
        $DB->free_result($query);

        return $result;
    }

}





