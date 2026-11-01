<?php
/**
 * Checks whether metatag exists.
 * @param mixed $id
 * @return boolean
 */
function fileAssetsExists($id)
{
    global $DB;
    if ($DB->query_first("SELECT `id` FROM  `file_assets` WHERE `id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC)) {
        return true;
    }

    return false;
}

/**
 * Return array containing row with given id.
 * @param mixed $id
 * @return mixed
 */
function fileAssetsFromDatabase($id)
{
    global $DB;
    $id = intval($id);
    if (0 == $id) {
        return;
    }

    $sql = "SELECT *, UNIX_TIMESTAMP(updated_at) as last_modified_unix FROM `file_assets` WHERE id=$id";
    $row = $DB->query_first($sql, MYSQLI_ASSOC);
    if ($row) {
        return $row;
    }

    return false;
}

/**
 * Search for metatag based on criteria.
 * Returns array of ids matching the criteria.
 * @param mixed $criteria
 * @param string $extra
 * @param boolean $like_tag
 * @return array
 */
function fileAssetsSearch($criteria = '', $extra = '', $like_tag = false)
{
    global $DB;
    $results = array();
    $sql     = "SELECT `m`.`id` FROM `file_assets` AS `m`";
    $joins   = array();
    $fields  = $DB->get_table_fields('`file_assets`');
    foreach ($fields as $k => $v) {
        $fields[$k] = '`m`.`' . $v . '`';
    }
    //$fields = array_merge($fields, metaGetOptionList('ext_fields'));
    $whereAND = array();
    if (is_array($criteria)) {
        foreach ($criteria as $field => $value) {
            if (array_key_exists($field, $fields)) {
                if (is_array($value)) {
                    if (count($value) > 0) {
                        $whereOR = array();
                        foreach ($value as $sub_value) {
                            $whereOR[] = $fields[$field] . (($like_tag) ? " LIKE " : " = ") . $DB->escape($sub_value);
                        }

                        $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                    } else {
                        return array();
                    }

                } else {
                    $whereAND[] = $fields[$field] . (($like_tag) ? " LIKE " : " = ") . $DB->escape($value);
                }

                unset($fields[$field]);
            } elseif (is_string($criteria) && '' != $criteria) {
                $criteria = array('any_text' => $criteria);
            }
        }
    }

    $search_for = array();
    if (is_array($criteria) && array_key_exists("any_text", $criteria)) {
        if (is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

    }
    if (count($search_for) > 0) {
        $searchBits = array('`m`.`id`', '`m`.`title`');
        $whereOR    = array();
        foreach ($search_for as $findme) {
            foreach ($searchBits as $key) {
                if (trim($findme) != '') {
                    $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                }
            }

        }
        if (count($whereOR) > 0) {
            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
        }

    }
    if ('' != $extra) {
        $whereAND[] = $extra;
    }

    if (count($joins) > 0) {
        $sql .= " " . implode(" ", $joins);
    }

    if (count($whereAND) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereAND);
    }

    $sql .= " ORDER BY `m`.`id` ASC";
    if ($query = $DB->query($sql)) {
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['id'];
        }
        $DB->free_result($query);
    }

    return $results;
}

/**
 * Sort list of metatags by given field
 * Orders an array of metatags $meta_list in ascending order
 * by field $order_by; returns the same type that was entered
 * @param mixed $meta_list
 * @param string $order_by
 * @return mixed
 */
function fileAssetsSort($meta_list, $order_by = 'id')
{
    global $DB;

    if (!is_array($meta_list) || count($meta_list) < 2) {
        return $meta_list;
    }

    $sql     = " SELECT `id` FROM  `file_assets`";
    $choices = array('id' => '`id`', 'name' => '`name`');

    $where     = " WHERE `id` IN ('" . implode("', '", $meta_list) . "')";
    $meta_list = array();
    $order     = " ORDER BY " . (('' != $choices[$order_by]) ? $choices[$order_by] : '`id`') . " ASC";
    $query     = $DB->query($sql . $where . $order);
    while ($row = $DB->fetch_assoc($query)) {
        $meta_list[] = $row['id'];
    }

    return $meta_list;
}

/**
 * Add metatag to database.
 * Update existing row or add new.
 * @param array $posted
 * @return mixed
 */
function fileAssetsToDatabase($posted)
{
    global $_GLOBALS;
    global $DB;

    $add    = ('do_add' == $posted['action']) ? true : false;
    $id     = intval($posted['id']);
    $before = ($add) ? array() : fileAssetsFromDatabase($id);

    $sql = (($add) ? "INSERT INTO " : "UPDATE ") . "`file_assets`";

    $changes = array();
    $table   = $posted['assets'];
    $set     = array();

    if (is_array($table)) {
        foreach ($table as $key => $value) {
            if (!$add) {
                if (htmlspecialchars($DB->escape($before[$key])) != htmlspecialchars($DB->escape($value))) {
                    $changes[$key] = array('before' => $before[$key], 'after' => $value);
                }

            }
            $set[] = $key . '=' . $DB->escape($value);
        }
        if ($add || count($changes)) {
            $set[] = "`created_at` = NOW(), `created_by` = " . $DB->escape($_GLOBALS['staff']['user']);
        }else{
            $set[] = "`updated_at` = NOW(), `updated_by` = " . $DB->escape($_GLOBALS['staff']['user']);
        }

        if (is_array($set) && count($set)) {
            $sql .= " SET " . implode(', ', $set) . (($add) ? '' : " WHERE id=$id");
            $DB->query($sql);
            if ($add) {
                return  $DB->insert_id;
            }

        }
    }

}
function fileAssetsDeleteFromDatabase($id){
     global $DB;
        $DB->query(sprintf(
            "DELETE FROM `file_assets` WHERE `id` = %d LIMIT 1",
            $id
        ));
}

/************************************ End: Site meta Functions ***************************************************************/
