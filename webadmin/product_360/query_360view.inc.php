<?php
if (!function_exists('view360GenericsFromPost')) {
    function view360GenericsFromPost($posted, $table)
    {
         global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->get_table_columns($table);
        foreach ($posted as $k1 => $v1) {
                if (array_key_exists($k1,$fields)) {
                    if($v1 || $v1==0){
                        $result[$k1] = $v1;
                    }    
                }
        }
        return $result;
    }
}
if (!function_exists('view360FromPost')) {
    function view360FromPost($posted)
    {
        return view360GenericsFromPost(
            $posted,
            "`360_view`"
        );
    }
}
if (!function_exists('view360ImagesFromPost')) {
    function view360ImagesFromPost($posted)
    {
        return view360GenericsFromPost(
            $posted,
            "`360_images`"
        );
    }
}
if (!function_exists('view360MarkerFromPost')) {
    function view360MarkerFromPost($posted,$image,$key)
    {
        if(isset($image['marker_image'])){
            if(isset($image['marker_image']['name'][$key])){
                $file_orig = $image['marker_image']['name'][$key];
                $file_temp = $image['marker_image']['tmp_name'][$key];
                $error = $image['marker_image']['error'][$key];
                $size = intval($image['marker_image']['size'][$key]);
                $file_ext=substr($file_orig,strrpos($file_orig,'.'));
                if(substr($file_ext,0,1)=='.') 
                $file_ext=substr($file_ext,1);
                $file_new = "marker_".rand().'_'.rand().'.'.$file_ext; 
                $dir = $_SERVER['DOCUMENT_ROOT'].'/webadmin/uploads/product/360view/';
                if(move_uploaded_file($file_temp,$dir.$file_new)){
                    $posted['image']=$file_new;
                }
            }
        } 
        return view360GenericsFromPost(
            $posted,
            "`360_marker`"
        );
    }
}
if (!function_exists('view360GenericToDatabase')) {
    function view360GenericToDatabase(&$item, $table, $primary_key = 'id') {
        global $DB, $_GLOBALS;
        $id         = intval($item[$primary_key]);
        $fields     = $DB->get_table_columns($table);
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
                   $set_vals['updated_at'] = "`updated_at` = NOW()";
                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                ));
            }
        } else {
            $item['created_by'] = $_GLOBALS['staff']['id'];
            $item['created_at'] = formatDateTime();
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
        deleteMysqlCacheData($table,$id);
        return $id;
    }
}
if (!function_exists('view360ToDatabase')) {
    function view360ToDatabase(&$item)
    {
        return  view360GenericToDatabase(
            $item,
            "`360_view`"
        );

    }
}
if (!function_exists('view360ImagesToDatabase')) {
    function view360ImagesToDatabase(&$item)
    {
        return  view360GenericToDatabase(
            $item,
            "`360_images`"
        );

    }
}
if (!function_exists('view360MarkerToDatabase')) {
    function view360MarkerToDatabase(&$item)
    {
        return  view360GenericToDatabase(
            $item,
            "`360_marker`"
        );

    }
}
if (!function_exists('view360GenericsDelete')) {
    function view360GenericsDelete($id, $table, $secondary_key = 'view_id') {
        global $DB;
        $DB->query(sprintf(
            "DELETE FROM %s  WHERE  `%s` = %d",
            $table,
            $secondary_key,
            $id
        ));
        deleteMysqlCacheData($table,$id);
        return $DB->affected_rows();
    }
}
if (!function_exists('view360MarkerDelete')) {
    function view360MarkerDelete($id)
    {
        return  view360GenericsDelete(
            $id,
            "`360_marker`",
            "id"
        );

    }
}
if (!function_exists('view360ImagesDelete')) {
    function view360ImagesDelete($id)
    {
        return  view360GenericsDelete(
            $id,
            "`360_images`"
        );

    }
}
if (!function_exists('view360getActiveMarker')) {
    function view360getActiveMarker($id, $table, $primary_key = 'id', $secondary_key = 'product_id',$orderby='')
    {
        global $DB;
        $result = array();
        $query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d AND status=1 %s",
            $table,
            $secondary_key,
            $id,
            $orderby
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
if (!function_exists('view360GenericsFromDatabase')) {
    function view360GenericsFromDatabase($id, $table, $primary_key = 'id', $secondary_key = 'product_id',$orderby='') {
        global $DB;
        $result = array();
        $query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d  %s",
            $table,
            $secondary_key,
            $id,
            $orderby
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
if (!function_exists('view360productlist')) {
    function view360productlist() {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s ORDER BY `%s` DESC",
            "`360_view`",
            "id"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('view360SideImageList')) {
    function view360SideImageList() {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `%s`, `name` FROM %s ORDER BY `name` ASC",
            "id",
            "`360_side_images`"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["id"]] = $row['name'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('view360GenericFromDatabase')) {
    function view360GenericFromDatabase($id, $table, $primary_key = 'id') {
        global $DB;
        return $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('view360MarkerLocation')) {
    function view360MarkerLocation($marker_id, $image_id) {
        global $DB;
        
        if ($row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `%s` = %d AND `image_id` = %d LIMIT 1",
            "`360_view_marker_location`",
            "marker_id",
            $marker_id,
            $image_id
        ), MYSQLI_ASSOC)) {
            return $row;
        }

        return 0;
    }
}
if (!function_exists('view360MarkerLocationBymarker')) {
    function view360MarkerLocationBymarker($marker_id) {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `%s` = %d LIMIT 1",
            "`360_view_marker_location`",
            "marker_id",
            $marker_id
        ), MYSQLI_ASSOC)) {
            return $row;
        }

        return 0;
    }
}
?>