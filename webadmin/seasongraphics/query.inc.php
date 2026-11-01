<?php

/**
 * Checks whether metatag exists.
 * @param mixed $id
 * @return boolean
 */
function graphicsExists($id)
{
    global $DB;
    if ($DB->query_first("SELECT `id` FROM  `season_graphics` WHERE `id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC)) {
        return true;
    }

    return false;
}

function graphicsGetValue($id, $field)
{
    global $DB;

    return $DB->modelGet($id, $field, "`season_graphics`", "id");
}

function graphicsHasValue($id, $field, $value)
{
    return ($value == graphicsGetValue($id, $field));
}

function graphicsSetValue($id, $field, $value)
{
    global $DB;

    return $DB->modelSet($id, $field, $value, "`season_graphics`", "id");
}

/**
 * Return array containing row with given id.
 * @param mixed $id
 * @return mixed
 */
function graphicsFromDatabase($id)
{
    global $DB;
    $id = intval($id);
    if (0 == $id) {
        return;
    }

    $sql = "SELECT * FROM `season_graphics` WHERE id=$id";
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
function graphicsSearch($criteria = '', $extra = '', $like_tag = false)
{
    global $DB;
    $results = array();
    $sql     = "SELECT `m`.`id` FROM `season_graphics` AS `m`";
    $joins   = array();
    $fields  = $DB->get_table_fields('`season_graphics`');
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
function graphicsSort($meta_list, $order_by = 'id')
{
    global $DB;

    if (!is_array($meta_list) || count($meta_list) < 2) {
        return $meta_list;
    }

    $sql     = " SELECT `id` FROM  `season_graphics`";
    $choices = array('id' => '`id`', 'desc' => '`title`', 'start_date' => '`start_date`', 'end_date' => '`end_date`');

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
function uploadimage(){

}
function graphicsToDatabase($posted)
{
    global $DB;
    $posted = array_merge($posted, $_FILES);
    $add    = ('do_add' == $posted['action']) ? true : false;
    $id     = intval($posted['id']);
    $before = ($add) ? array() : graphicsFromDatabase($id);

    $sql = (($add) ? "INSERT INTO " : "UPDATE ") . "`season_graphics`";

    $changes = array();
    $set     = array();
    if (is_array($posted)) {
        $set[] = 'title' . '=' . $DB->escape($posted['title']);
        $set[] = 'status' . '=' . $DB->escape($posted['status']);
        $set[] = 'start_date' . '=' .$DB->escape(date('Y').'-'.$posted['start_month'].'-'.$posted['start_day']);
        $endyear=($posted['start_month']>$posted['end_month'])?date('Y', strtotime('+1 year')):date('Y');
        $set[] = 'end_date' . '=' .$DB->escape($endyear.'-'.$posted['end_month'].'-'.$posted['end_day']);
        $set[] =  'header_graphic' . '=' . $DB->escape(json_encode($posted['header_image']));
        $set[] =  'menu_graphic' . '=' . $DB->escape(json_encode($posted['menu_image_data']));
        $set[] =  'footer_graphic' . '=' . $DB->escape(json_encode($posted['display_image'])); 
        $set[] =  'created_at = NOW()'; 
        if (is_array($set) && count($set)) {
            $sql .= " SET " . implode(', ', $set) . (($add) ? '' : " WHERE id=$id");
            $DB->query($sql);
            if ($add) {
                $id = $DB->insert_id;
            }

        }
    }

    /********************************************** Code below if to store any changes made to the Site meta *******************************************************/
    $array   = array();
    return $id;

}
function graphicsDeleteFromDatabase($id){
     global $DB;
        $DB->query(sprintf(
            "DELETE FROM `season_graphics` WHERE `id` = %d LIMIT 1",
            $id
        ));
}

/************************************ End: Site meta Functions ***************************************************************/
