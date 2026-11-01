<?php
if (!function_exists('cartGenericDelete')) {
    function cartGenericDelete($id, $table, $primary_key = 'id')
    {
        $primary_key='id';
  
        global $DB;
        $DB->query(sprintf(
            "UPDATE %s SET `deleted_at` = NOW() WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
            //"DELETE FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ));
        return $DB->affected_rows();
    }
}
if (!function_exists('cartGenericExists')) {
    function cartGenericExists($id, $table, $primary_key = 'id')
    {
        global $DB;
        return !!$DB->query_first(sprintf(
            "SELECT `%s` FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
            $primary_key,
            $table,
            $primary_key,
            $id
        ));
    }
}
if (!function_exists('cartGenericFromDatabase')) {
    function cartGenericFromDatabase($id, $table, $primary_key = 'id')
    {
        global $DB;
        $row = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
        if ($row) {
            $row['created_time'] = is_null($row['created_at']) ? null : strtotime($row['created_at']);
            $row['updated_time'] = is_null($row['updated_at']) ? null : strtotime($row['updated_at']);
        }
        return $row;
    }
}
if (!function_exists('cartGenericGetValue')) {
    function cartGenericGetValue($id, $field, $table, $primary_key = 'id')
    {
        global $DB;
        $fields = getTableFields($table);
        if (array_key_exists($field, $fields)) {
            $row = $DB->query_first(sprintf(
                "SELECT `%s` FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
                $field,
                $table,
                $primary_key,
                $id
            ));
            if ($row) {
                return $row[$field];
            }
        }
        return null;
    }
}
if (!function_exists('cartGenericSetValue')) {
    function cartGenericSetValue($id, $field, $value, $table, $primary_key = 'id')
    {
        global $DB;
        $fields = getTableFields($table);
        if (array_key_exists($field, $fields)) {
            $DB->query(sprintf(
                "UPDATE %s SET `%s` = %s WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
                $table,
                $field,
                secho($value, 1),
                $primary_key,
                $id
            ));
            return $DB->affected_rows();
        }
        return 0;
    }
}
if (!function_exists('cartGenericToDatabase')) {
    function cartGenericToDatabase(&$item, $table, $primary_key = 'id')
    {
        global $DB, $_GLOBALS;
        $id       = intval($item[$primary_key]);
        $fields   = getTableFields($table);
        $old_vals = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
        if ($old_vals) {
            $set_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item) && $item[$k] !== $old_vals[$k]) {
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
                //$item['updated_by'] = $_GLOBALS['staff']['id'];
                //$set_vals['updated_by'] = "`updated_by` = " . intval($item['updated_by']);
                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                ));

                echo sprintf(
                    "UPDATE %s SET %s WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                );
            }

         
        } else {
            //$item['created_by'] = $_GLOBALS['staff']['id'];
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
                $item[$primary_key] = $id = $DB->insert_id();
            }
        }
        return $id;
    }
}
if (!function_exists('cartGenericsDelete')) {
    function cartGenericsDelete($id, $table, $secondary_key = 'member_id')
    {
        global $DB;
        $DB->query(sprintf(
            "UPDATE %s SET `deleted_at` = NOW() WHERE `deleted_at` IS NULL AND `%s` = %d",
            //"DELETE FROM %s WHERE `%s` = %d",
            $table,
            $secondary_key,
            $id
        ));
        return $DB->affected_rows();
    }
}
if (!function_exists('cartGenericsFromDatabase')) {
    function cartGenericsFromDatabase($id, $table, $primary_key = 'id', $secondary_key = 'member_id',$type='shopping')
    {
        global $DB;
        $result = array();
        $query  = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d AND `cart_type`='%s'",
            $table,
            $secondary_key,
            $id,
            $type
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $row['created_time'] = is_null($row['created_at']) ? null : strtotime($row['created_at']);
            $row['updated_time'] = is_null($row['updated_at']) ? null : strtotime($row['updated_at']);
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
if (!function_exists('cartGenericsToDatabase')) {
    function cartGenericsToDatabase($id, &$items, $table, $primary_key = 'id', $secondary_key = 'member_id')
    {
        foreach ($items as &$v) {
            $v[$secondary_key] = $id;
            cartGenericToDatabase($v, $table, $primary_key);
        }
    }
}

if (!function_exists('cartProductDelete')) {
    function cartProductDelete($memid, $id)
    {
        global $DB;

        if ($memid != 0) {
            if ($DB->query(sprintf(
                "DELETE FROM %s WHERE `%s` = %d AND `%s` = %d LIMIT 1",
                "`cart_members`",
                "id",
                $id,
                "member_id",
                $memid
            ))) {
                return $DB->affected_rows;
            }
            return 0;
        }
        
        if ($DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d AND `%s` = %d LIMIT 1",
            "`cart_visitors`",
            "id",
            $id,
            "visitor_id",
            cartGetVisitorID()
        ))) {
            return $DB->affected_rows;
        }

        return 0;
    }
}
if (!function_exists('cartProductsDelete')) {
    function cartProductsDelete($id)
    {
        global $DB;

        $result = 0;

        if ($id != 0) {
            if ($DB->query(sprintf(
                "DELETE FROM %s WHERE `%s` = %d",
                "`cart_members`",
                "member_id",
                $id
            ))) {
                $result += $DB->affected_rows;
            }
        }
        
        if ($DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d",
            "`cart_visitors`",
            "visitor_id",
            cartGetVisitorID()
        ))) {
            $result += $DB->affected_rows;
        }

        return $result;
    }
}
if (!function_exists('cartProductsFromDatabase')) {
    function cartProductsFromDatabase($id, $type = 'shopping')
    {
        global $_GLOBALS;
        global $DB;

        $result = [];

        if ($id != 0) {
            if ($query = $DB->query(sprintf(
                "SELECT * FROM %s WHERE `%s` = %d AND `cart_type` = %s",
                "`cart_members`",
                "member_id",
                $id,
                $DB->escape($type)
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $result[$row["id"]] = $row;
                }
                $DB->free_result();
            }

            return $result;
        }
        
        if ($_GLOBALS['visitor']['visitor_id'] ?? 0) {
            if ($query = $DB->query(sprintf(
                "SELECT * FROM %s WHERE `%s` = %d AND `cart_type` = %s",
                "`cart_visitors`",
                "visitor_id",
                $_GLOBALS['visitor']['visitor_id'],
                $DB->escape($type)
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $result[$row["id"]] = $row;
                }
                $DB->free_result();
            }
        }

        return $result;
    }
}
if (!function_exists('cartProductsToDatabase')) {
    function cartProductsToDatabase($id, &$items)
    {
        global $DB;

        if ($id != 0) {
            return $DB->childrenSave(
                $id,
                $items,
                "`cart_members`",
                "id",
                "member_id"
            );
        }

        return $DB->childrenSave(
            cartGetVisitorID(),
            $items,
            "`cart_visitors`",
            "id",
            "visitor_id"
        );
    }
}


if (!function_exists('cartApplyCoupon')) {
    /**
     * @note Could memberSetValue/visitorSetValue(..., 'discount_code', ...) do the job here?
     */
    // function cartApplyCoupon($post)
    // {   
    //     $set_vals               = array();
    //     $set_vals['couponcode'] = $post['couponcode'];
        
    //     $primary_key = 'id';
    //     $id          = $post['id'];

    //     printf(
    //         "UPDATE %s SET `couponcode` = '%s' WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
    //         "`cart_members`",
    //         $post['couponcode'],
    //         $primary_key,
    //         $id
    //     );
    // }

    // Code Updated on 12th June 2023
    function cartApplyCoupon($post)
    {   
        //print_r($post);
        global $DB;
        
            $set_vals=array();
            $set_vals['couponcode']=$post['couponcode'];
         
            $primary_key='id';
            $id=$post['id'];

  
               $DB->query(sprintf(
                    "UPDATE %s SET couponcode='%s' WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
                     'cart_members',
                    $post['couponcode'],
                    $primary_key,
                    $id
                ));

    }
    //end
}

// code updated on 12th June 2023

if (!function_exists('cartParentProductCheck')) {
    function cartParentProductCheck($id)
    {
        global $DB;


 
            $result=[];

            if ($query = $DB->query(sprintf(
            "SELECT `%s` FROM %s WHERE `deleted_at` IS NULL AND `%s` = %s",
            'linking_id',
            'products_upgrade_linking',
            'product_id',
            $id
        ))) {

               
                while ($row = $DB->fetch_assoc($query)) {

                    $result[$row["linking_id"]] = $row;
                }
              
            }



     

        return $result;


    }
}

//end