<?php
function pageChunk($page_list, $group_by = '', $split_sets = true)
{
    global $DB;
    if (!is_array($page_list) || count($page_list) < 1) {
        return $page_list;
    }

    if (!is_array($group_by)) {
        $group_by = array($group_by);
    }

    $fields      = pageGetOptionList('all_fields');
    $field_types = pageGetOptionList('all_fields_types');
    $field_list  = array('id');
    foreach ($group_by as $field) {
        if (array_key_exists($field, $fields)) {
            $field_list[] = $fields[$field] . " AS `" . $field . "`";
        }
    }

    $field_list = array_unique($field_list);
    $sql        = "SELECT " . implode(', ', $field_list) . " FROM `page` AS `p` WHERE `p`.`id` IN(" . implode(', ', $page_list) . ") ORDER BY `rank` ASC";
    $query      = $DB->query($sql);
    $results    = array();
    while ($row = $DB->fetch_assoc($query)) {
        $pointers = array(&$results);
        foreach ($group_by as $group) {
            $key = $row[$group];
            if ('' == $key) {
                $key = null;
            }

            $new_pointers = array();
            foreach ((substr($field_types[$group]['Type'], 0, 4) == 'set(' && $split_sets) ? explode(',', $key) : array($key) as $key2) {
                foreach ($pointers as $key1 => $pointer) {
                    if (!is_array($pointers[$key1][$key2])) {
                        $pointers[$key1][$key2] = array();
                    }

                    $new_pointers[] = &$pointers[$key1][$key2];
                }
            }
            $pointers = $new_pointers;
        }
        foreach ($pointers as $pointer_key => $pointer) {
            $pointers[$pointer_key][] = $row['id'];
        }

    }
    $DB->free_result($query);
    return $results;
}
function pageDelete($page_id)
{
    global $DB;
    $DB->query("DELETE FROM page p WHERE p.id=" . intval($page_id) . " LIMIT 1");
    $DB->query("DELETE FROM page_comments pc WHERE pc.page_id=" . intval($page_id));
    $DB->query("DELETE FROM page_images pi WHERE pi.page_id=" . intval($page_id));
    $DB->query("DELETE FROM page_chapters pc WHERE pc.page_id=" . intval($page_id));
    deleteMysqlCacheData('page', $page_id);
}
function pageExists($page_id)
{
    global $DB;
    $row = $DB->query_first("SELECT p.id FROM page p WHERE p.id=" . intval($page_id) . " LIMIT 1", MYSQLI_ASSOC);
    if ($row) {
        return true;
    }

    return false;
}
function pageFormatNumber($page_id)
{
    return sprintf('LM%04d', $page_id);
}
function pageFromDatabase($id)
{
    global $DB, $_GLOBALS;
    $fields = array('*' => '`p`.*');
    foreach (pageGetOptionList('ext_fields') as $key => $value) {
        $fields[$key] = $value . " AS `" . $key . "`";
    }
    $result = $DB->query_first("SELECT " . implode(", ", $fields) . " FROM `page` AS `p` WHERE `p`.`id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
    if (false == $result) {
        $result = array();
        foreach (pageGetOptionList('all_fields_types') as $key => $row) {
            $result[$key] = $row[4];
        }

    }
    if ("" == $result["slug"]) {
        $result["slug"] = trim(preg_replace("@[^0-9a-z/]+@", "-", strtolower($result["name"])), "-");
    }

    if ('url' == $result['content_type']) {
        $result['page_url'] = relative_url($result['content'], $_GLOBALS["site_settings"]["home_uri"]);
    } elseif ("" != $result["slug"]) {
        $result["page_url"] = "/" . $result['slug'];//formatURLSafe($result["slug"]);
    }

    return $result;
}
function pageFromPost($posted)
{
    global $DB;
    $page_id      = $posted['id'];
    $posted['contact_form']=(isset($posted['contact_form']) && $posted['contact_form']=='y')?'y':'n';
    $posted['have_question']=(isset($posted['have_question'] ) && $posted['have_question']=='y')?'y':'n';
    $result       = pageFromDatabase($page_id);
    $result['id'] = intval($page_id);
    foreach ($result as $key => $value) {
        if (array_key_exists($key, $posted) && !is_array($posted[$key])) {
            $result[$key] = trim($posted[$key]);
        }
    }

    if ("" == $result["slug"]) {
        $result["slug"] = trim(preg_replace("/[^0-9a-z]+/", "-", strtolower($result["name"])), "-");
    }

    if (0 == $page_id) {
        unset($result['id']);
        $result['create_time'] = time();
        $result['created']     = "NOW()";
        
    }
    return $result;
}
function pageGetLock($page_id)
{
    global $DB;
    $row = $DB->query_first("SELECT pl.*,UNIX_TIMESTAMP(pl.expiry_date) AS expiry_time FROM page_locks pl WHERE pl.page_id=" . intval($page_id) . " LIMIT 1", MYSQLI_ASSOC);
    if ($row['expiry_time'] < time()) {
        $row = false;
    }

    return $row;
}
function _recurse_tree($names, $branch, $tree, $include_group = true)
{
    $result = array();
    foreach ($branch as $k => $v) {
        if (is_array($tree[$v])) {
            $rebuild = array();
            if ($include_group) {
                $rebuild[$v] = '-' . $names[$v] . '-';
            }

            foreach (_recurse_tree($names, $tree[$v], $tree, $include_group) as $k2 => $v2) {
                $rebuild[$k2] = $v2;
            }

            $result[$names[$v]] = $rebuild;
        } else {
            $result[strval($v)] = $names[$v];
        }

    }
    return $result;
}
function pageGetOptionList($table_name)
{
    static $option_cache = array();
    if (isset($option_cache[$table_name])) {
        return $option_cache[$table_name];
    }

    global $DB;
    $table       = '';
    $key         = '';
    $value       = '';
    $order_by    = '';
    $where       = '';
    $option_list = array();
    switch ($table_name) {
        case 'staff_id':
        case 'modified_staff_id':
        case 'created_staff_id':
        case 'modified_staff_id':
            return getStaffList();
        case 'active':
            return array('y' => 'Yes', 'n' => 'No');
        case 'image_path':
        case 'page_path':
            return $_SERVER['DOCUMENT_ROOT'] . '/images/page';
        case 'content_type':
            return array('image' => 'Image', 'plain' => 'Plain Text', 'semihtml' => 'Semi-HTML', 'markdown' => 'Markdown', 'html' => 'HTML', 'ubb' => 'UBB Code', 'url' => "Direct URL", 'redirect' => "Redirect URL");
        case 'parent_id':
            $tree  = array(0 => array());
            $names = array();
            $query = $DB->query("SELECT `id`, `name`, `parent_id` FROM `page` ORDER BY `rank` ASC");
            while ($row = $DB->fetch_assoc($query)) {
                $tree[$row['parent_id']][$row['id']] = $row['id'];
                $names[$row['id']]                   = (trim($row['name']) != '') ? $row['name'] : '[Untitled]';
            }
            $DB->free_result($query);
            return $option_cache[$table_name] = _recurse_tree($names, $tree[0], $tree);
        case 'all_fields':
            return array_merge(pageGetOptionList('base_fields'), pageGetOptionList('ext_fields'));
        case 'all_fields_types':
            return array_merge(pageGetOptionList('base_fields_types'), pageGetOptionList('ext_fields_types'));
        case 'base_fields':
            $fields = array();
            foreach (getTableColumns('`page`') as $key) {
                $fields[$key] = '`p`.`' . $key . '`';
            }

            return $fields;
        case 'base_fields_types':
            return getTableFields('`page`');
        case 'venue_country':
            return getCountryList(false);
        case 'page_categories':
            return array();
        case 'ext_fields':
        case 'extra_fields':
            return array(
                'create_time'   => 'UNIX_TIMESTAMP(`created`)',
                'change_time'   => 'UNIX_TIMESTAMP(`modified`)',
                'modified_unix' => 'UNIX_TIMESTAMP(`modified`)',
            );
        case 'ext_fields_types':
            return array();
        default:
            trigger_error('pageGetOptionList(' . json_encode($table_name) . ') is undefined');
            break;
    }
    if ('' != $table) {
        $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table . (('' != $where) ? " WHERE " . $where : "") . (('' != $order_by) ? " ORDER BY " . $order_by . " ASC" : ""));
        while ($row = $DB->fetch_array($query)) {
            $option_list[$row[0]] = $row[1];
        }

        $DB->free_result($query);
    }
    $option_cache[$table_name] = $option_list;
    return $option_list;
}
function pageMoveRank($id, $rank_delta)
{
    global $DB;
    if (0 == $rank_delta) {
        return;
    }

    $cache_rank = array();

    $old_order = array();
    $query     = $DB->query(sprintf(
        "SELECT `p`.`id`, `p`.`rank` FROM `page` AS `p` WHERE `p`.`parent_id` = %d ORDER BY `p`.`rank` ASC",
        pageGetValue($id, 'parent_id')
    ));
    while ($row = $DB->fetch_assoc($query)) {
        $old_order[] = $row['id'];
        $cache_rank[$row['id']] = $row['rank'];
    }
    $DB->free_result($query);
    $old_pos = array_search($id, $old_order);

    $new_pos = min(max(0, $old_pos + $rank_delta), count($old_order) - 1);
    $new_order = $old_order;
    array_splice($new_order, $old_pos, 1);
    array_splice($new_order, $new_pos, 0, array($id));

    foreach ($new_order as $k => $v) {
        if ($cache_rank[$v] != $k) {
            $DB->query(sprintf(
                "UPDATE `page` SET `rank` = %d WHERE `id` = %d LIMIT 1",
                $k,
                $v
            ));
        }
    }
}
function pageSearch($criteria = '')
{
    global $DB;
    $sql         = "SELECT `p`.`id` FROM `page` AS `p`";
    $joins       = array();
    $field_types = pageGetOptionList('all_fields_types');
    $fields      = pageGetOptionList('all_fields');
    $whereAND    = array();
    if (is_array($criteria)) {
        foreach ($criteria as $field => $value) {
            if (isset($fields[$field])) {
                if (is_array($value)) {
                    if (count($value) > 0) {
                        $whereOR = array();
                        if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                            foreach ($value as $sub_value) {
                                $whereOR[] = "FIND_IN_SET(" . formatSQLString($sub_value) . ", " . $fields[$field] . ")";
                            }

                        } else {
                            foreach ($value as $sub_value) {
                                $whereOR[] = $fields[$field] . " = " . formatSQLString($sub_value);
                            }

                        }
                        $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                    } else {
                        return array();
                    }
                } else {
                    if (array_key_exists($field, $field_types) && substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                        $whereAND[] = "FIND_IN_SET(" . formatSQLString($value) . ", " . $fields[$field] . ")";
                    } else {
                        $whereAND[] = $fields[$field] . " = " . formatSQLString($value);
                    }
                }
                unset($fields[$field]);
            }
        }

    }
    $joins      = array();
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
        $searchBits = array('id', 'name');
        foreach ($search_for as $findme) {
            $whereOR = array();
            foreach ($searchBits as $key) {
                if (trim($findme) != '') {
                    $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                }
            }

            if (count($whereOR) > 0) {
                $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
            }

        }
    }
    if (count($joins) > 0) {
        $sql .= implode('', $joins);
    }

    if (count($whereAND) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereAND);
    }

    $sql .= " GROUP BY `p`.`id`";
    $sql .= " ORDER BY `p`.`rank` ASC";
    $results = array();
    $query   = $DB->query($sql);
    while ($row = $DB->fetch_assoc($query)) {
        $results[] = $row['id'];
    }

    $DB->free_result($query);
    return $results;
}
function pageSetLock($page_id, $staff_id = '', $timeout = 0)
{
    global $DB;
    $DB->query("DELETE FROM page_locks WHERE page_id=" . intval($page_id));
    if (0 == $timeout) {
        $timeout = 300;
    }

    if ('' != $staff_id) {
        $DB->query("INSERT INTO page_locks(page_id,staff_id,expiry_date) VALUES(" . intval($page_id) . "," . formatSQLString($staff_id) . "," . formatSQLDateTime(time() + $timeout) . ")");
    }

}
function pageSort($page_list, $order_by = 'rank')
{
    global $DB;
    if (!is_array($page_list) || count($page_list) < 2) {
        return $page_list;
    }

    $fields = pageGetOptionList('all_fields');
    if (!isset($fields[$order_by])) {
        return $page_list;
    }

    $sql   = " SELECT `p`.`id` FROM `page` AS `p`";
    $where = " WHERE `p`.`id` IN(" . implode(', ', $page_list) . ")";
    if (isset($fields[$order_by])) {
        $order = " ORDER BY " . $fields[$order_by] . " ASC";
    }

    $results = array();
    $query   = $DB->query($sql . $where . $order);
    while ($row = $DB->fetch_assoc($query)) {
        $results[] = $row['id'];
    }

    $DB->free_result($query);
    return $results;
}
function pageToDatabase($page)
{
    global $DB, $_GLOBALS;
    $page_id = intval($page['id']);
    $fields  = pageGetOptionList('base_fields_types');
    $changed = array();
    if (pageExists($page_id)) {
        $old_vals = pageFromDatabase($page_id);
        $set_vals = array();
        foreach ($page as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (is_array($page[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                    $set_vals[$key] = "`" . $key . "`=" . formatSQLString(implode(",", $page[$key]));
                } elseif (isset($page[$key])) {
                    $set_vals[$key] = "`" . $key . "`=" . formatSQLString($page[$key]);
                } else {
                    $set_vals[$key] = "`" . $key . "`=NULL";
                }

            }
        }

        $all_keys = array_keys($set_vals);
        if (count(array_intersect(array('title', 'description', 'keywords'), $all_keys))) {
            $set_vals['modified']          = "`modified` = NOW()";
            $set_vals['modified_staff_id'] = '`modified_staff_id` = ' . $DB->escape($_GLOBALS['staff']['user']);
        }
        unset($set_vals['rank']); //Do not allow ranking through this function
        if (count($set_vals)) {
            $set_vals['modified']          = "`modified` = NOW()";
            $set_vals['modified_staff_id'] = '`modified_staff_id`=' . formatSQLString($_GLOBALS['staff']['staff_id']);
            $sql                           = "UPDATE page SET " . implode(', ', $set_vals) . " WHERE id=" . $page_id . " LIMIT 1";
            $DB->query($sql);
        }
    } else {
        $insert_vals = array();
        foreach ($page as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (is_array($page[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                    $insert_vals[$key] = formatSQLString(implode(",", $page[$key]));
                } elseif (isset($page[$key])) {
                    $insert_vals[$key] = formatSQLString($value);
                }

                //elseif (array_key_exists($page, $key)) $insert_vals[$key] = 'NULL';

            }
        }
        if (count($insert_vals)) {
            $insert_vals['created_staff_id'] = formatSQLString($_GLOBALS['staff']['staff_id']);
            $sql                             = "INSERT INTO page(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")";
            $DB->query($sql);
            $page_id = $DB->insert_id();
            $DB->query("UPDATE `page` SET `rank` = " . intval($page_id) . " WHERE `id` = " . intval($page_id) . " LIMIT 1");
        }
    }
    deleteMysqlCacheData('page', $page_id);
    return $page_id;
}
function pageGetValue($page_id, $field)
{
    global $DB;
    $fields = getTableFields('`page`');
    if (array_key_exists($field, $fields)) {
        $row = $DB->query_first("SELECT `" . $field . "` FROM page p WHERE p.id=" . intval($page_id) . " LIMIT 1", MYSQLI_ASSOC);
        return $row[$field];
    }
    $special_fields = pageGetOptionList('ext_fields');
    if (array_key_exists($field, $special_fields)) {
        $row = $DB->query_first("SELECT " . $special_fields[$field] . " AS `" . $field . "` FROM `page` AS `p` WHERE `p`.`id` = " . intval($page_id) . " LIMIT 1", MYSQLI_ASSOC);
        return $row[$field];
    }
    return null;
}
function pageHasValue($page_id, $field, $value)
{
    return (pageGetValue($page_id, $field) == $value);
}
function pageSetValue($page_id, $field, $value)
{
    global $DB;
    $fields = getTableFields('`page`');
    if (array_key_exists($field, $fields)) {
        $DB->query("UPDATE page SET `" . $field . "`=" . formatSQLString($value) . " WHERE id=" . intval($page_id) . " LIMIT 1");
        return true;
    }
    return false;
}

function pageCommentFromDatabase($comment_id)
{
    global $DB;
    return $DB->query_first("SELECT pc.*,UNIX_TIMESTAMP(pc.created) AS comment_time FROM page_comments pc WHERE pc.id=" . intval($comment_id));
}
function pageCommentSearch($criteria, $min_time = 0, $max_time = 0)
{
    global $DB;
    $sql         = "SELECT pc.id FROM page_comments pc";
    $field_types = getTableFields('`page_comments`');
    $whereAND    = array();
    if (is_array($criteria)) {
        foreach ($criteria as $field => $value) {
            if (isset($field_types[$field])) {
                if (is_array($value)) {
                    if (count($value) > 0) {
                        $whereOR = array();
                        if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                            foreach ($value as $sub_value) {
                                $whereOR[] = "	FIND_IN_SET(" . formatSQLString($sub_value) . "," . $field . ")";
                            }

                        } else {
                            foreach ($value as $sub_value) {
                                $whereOR[] = "	" . $field . "=" . formatSQLString($sub_value);
                            }

                        }
                        $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                    } else {
                        return array();
                    }
                } else {
                    if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                        $whereAND[] = "	FIND_IN_SET(" . formatSQLString($value) . "," . $field . ")";
                    } else {
                        $whereAND[] = "	" . $field . "=" . formatSQLString($value);
                    }
                }
                unset($field);
            }
        }

    }
    if (0 != $min_time) {
        $whereAND[] = 'UNIX_TIMESTAMP(pc.created)>' . $min_time;
    }

    if (0 != $max_time) {
        $whereAND[] = 'UNIX_TIMESTAMP(pc.created)<' . $max_time;
    }

    if (count($whereAND) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereAND);
    }

    $sql .= " GROUP BY pc.id";
    $sql .= " ORDER BY pc.id ASC";
    $results = array();
    $query   = $DB->query($sql);
    while ($row = $DB->fetch_assoc($query)) {
        $results[] = $row['id'];
    }

    $DB->free_result($query);
    return $results;
}
function pageCommentToDatabase($page_id, $comment, $type = 'admin')
{
    global $DB;
    global $_GLOBALS;
    $DB->query("INSERT INTO page_comments (page_id, staff_id, comment_type, created, comment) VALUES (" . intval($page_id) . ", " . $_GLOBALS['staff']['staff_id'] . ", " . formatSQLString($type) . ", NOW(), " . formatSQLString($comment) . ")");
    return $DB->insert_id();
}

function pageImageDelete($image_id)
{
    global $DB;
    $DB->query("DELETE FROM page_images WHERE id=" . intval($image_id));
}
function pageImageExists($image_id)
{
    global $DB;
    $row = $DB->query_first("SELECT pi.id FROM page_images pi WHERE pi.id=" . intval($image_id) . " LIMIT 1", MYSQLI_ASSOC);
    if ($row) {
        return true;
    }

    return false;
}
function pageImageFromDatabase($image_id)
{
    global $DB;
      if (pageImageExists($image_id)) {
        $fields     = array('*' => 'pi.*');
        $item_image = $DB->query_first("SELECT " . implode(", ", $fields) . " FROM page_images pi WHERE pi.id=" . intval($image_id) . " LIMIT 1", MYSQLI_ASSOC);
        $page       = pageFromDatabase($item_image['page_id']);
        if ('' != $page['name']) {
            $slug                    = $page['slug'];
            $item_image['image_url'] = '/images/page/' . $item_image['image_file'] . '';
            $item_image['thumb_url'] = '/images/page/' . $item_image['image_file'] . '';
        } else {
            $item_image['image_url'] = '/images/page/' . $item_image['image_file'] . '';
            $item_image['thumb_url'] = '/images/page/' . $item_image['image_file'] . '';
        }
        return $item_image;
    } else {
        return array();
    }

}
function pageImageSearch($criteria = '')
{
    global $DB;
    $sql         = " SELECT pi.id FROM page_images pi";
    $joins       = array();
    $field_types = getTableFields('`page_images`');
    $fields      = array();
    foreach (getTableColumns('`page_images`') as $key) {
        $fields[$key] = 'pi.`' . $key . '`';
    }

    $whereAND = array();
    if (is_array($criteria)) {
        foreach ($criteria as $field => $value) {
            if (isset($fields[$field])) {
                if (is_array($value)) {
                    if (count($value) > 0) {
                        $whereOR = array();
                        if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                            foreach ($value as $sub_value) {
                                $whereOR[] = "	FIND_IN_SET(" . formatSQLString($sub_value) . "," . $fields[$field] . ")";
                            }

                        } else {
                            foreach ($value as $sub_value) {
                                $whereOR[] = "	" . $fields[$field] . "=" . formatSQLString($sub_value);
                            }

                        }
                        $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                    } else {
                        return array();
                    }
                } else {
                    if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                        $whereAND[] = "	FIND_IN_SET(" . formatSQLString($value) . "," . $fields[$field] . ")";
                    } else {
                        $whereAND[] = "	" . $fields[$field] . "=" . formatSQLString($value);
                    }
                }
                unset($fields[$field]);
            }
        }

    }
    $joins      = array();
    $search_for = array();
    if (is_array($criteria) && array_key_exists('any_text', $criteria)) {
        if (is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        } elseif (is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }
    }

    if (count($search_for) > 0) {
        $searchBits = array('id');
        foreach ($search_for as $findme) {
            $whereOR = array();
            foreach ($searchBits as $key) {
                if (trim($findme) != '') {
                    $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                }
            }

            if (count($whereOR) > 0) {
                $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
            }

        }
    }
    if (count($joins) > 0) {
        $sql .= implode('', $joins);
    }

    if (count($whereAND) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereAND);
    }

    $sql .= " GROUP BY pi.id";
    $sql .= " ORDER BY pi.id ASC";
    $results = array();
    $query   = $DB->query($sql);
    while ($row = $DB->fetch_assoc($query)) {
        $results[] = $row['id'];
    }

    $DB->free_result($query);
    return $results;
}
function pageImageToDatabase($page_image)
{
    global $DB;
    $page_image_id = intval($page_image['id']);
    $fields        = getTableFields('`page_images`');
    $changed       = array();
    if (pageImageExists($page_image_id)) {
        $old_vals = pageImageFromDatabase($page_image_id);
        $set_vals = array();
        foreach ($page_image as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (is_array($page_image[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                    $set_vals[$key] = "`" . $key . "`=" . formatSQLString(implode(",", $page_image[$key]));
                } elseif (isset($page_image[$key])) {
                    $set_vals[$key] = "`" . $key . "`=" . formatSQLString($page_image[$key]);
                } else {
                    $set_vals[$key] = "`" . $key . "`=NULL";
                }

            }
        }

        if (count($set_vals)) {
            $sql = "UPDATE page_images SET " . implode(', ', $set_vals) . " WHERE id=" . $page_image_id . " LIMIT 1";
            $DB->query($sql);
        }
    } else {
        $insert_vals = array();
        foreach ($page_image as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (is_array($page_image[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                    $insert_vals[$key] = formatSQLString(implode(",", $page_image[$key]));
                } elseif (isset($page_image[$key])) {
                    $insert_vals[$key] = formatSQLString($value);
                } else {
                    $insert_vals[$key] = 'NULL';
                }

            }
        }

        if (count($insert_vals)) {
            $sql = "INSERT INTO page_images(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES(" . implode(", ", $insert_vals) . ")";
            $DB->query($sql);
            return $DB->insert_id();
        }
    }
    return $page_image_id;
}

function pageChapterSearch($criteria = '')
{
    global $DB;
    $results  = array();
    $sql      = " SELECT pc.id FROM page_chapters pc";
    $whereAND = array();
    if ('' != $criteria['page_id']) {
        $whereAND[] = "pc.page_id=" . intval($criteria['page_id']);
    }

    if (count($whereAND) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereAND);
    }

    $sql .= " GROUP BY pc.id";
    $sql .= " ORDER BY pc.rank ASC";
    $query = $DB->query($sql);
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $results[] = $row['id'];
    }

    $DB->free_result($query);
    return $results;
}
function pageChapterFromDatabase($id)
{
    global $DB;
    if (pageChapterExists($id)) {
        $fields = array('*' => 'pc.*');
        foreach (pageChapterGetOptionList('ext_fields') as $key => $value) {
            $fields[$key] = $value . " AS `" . $key . "`";
        }

        $chapter = $DB->query_first("SELECT " . implode(", ", $fields) . " FROM `page_chapters` AS `pc` WHERE `pc`.`id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
        $url     = formatURLSafe($chapter['heading']);
        if ('' != $chapter['heading']) {
            $chapter['chapter_url'] = '/page/' . $url . '.html';
        }

        return $chapter;
    } else {
        $result = array();
        foreach (pageChapterGetOptionList('all_fields_types') as $key => $row) {
            $result[$key] = $row[4];
        }

        return $result;
    }
}
function pageChapterFromPost($posted)
{
    global $DB;
    $page_id      = $posted['page_id'];
    $id           = $posted['id'];
    $result       = pageChapterFromDatabase($id);
    $pChapter     = $posted['chapter'];
    $result['id'] = intval($id);
    foreach ($result as $key => $value) {
        if (array_key_exists($key, $pChapter) && !is_array($pChapter[$key])) {
            $result[$key] = trim($pChapter[$key]);
        }
    }

    if (0 == $id) {
        unset($result['id']);
        $result['create_time'] = time();
        $result['created']     = formatDateTime();
    }
    return $result;
}
function pageChapterToDatabase($chapter)
{
    global $DB, $_GLOBALS;
    $page_id = intval($chapter['page_id']);
    $id      = intval($chapter['id']);
    $fields  = pageChapterGetOptionList('base_fields_types');
    $changed = array();
    if (pageChapterExists($id)) {
        $old_vals = pageChapterFromDatabase($id);
        $set_vals = array();
        foreach ($chapter as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (is_array($chapter[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                    $set_vals[$key] = "`" . $key . "`=" . formatSQLString(implode(",", $chapter[$key]));
                } elseif (isset($chapter[$key])) {
                    $set_vals[$key] = "`" . $key . "`=" . formatSQLString($chapter[$key]);
                } else {
                    $set_vals[$key] = "`" . $key . "`=NULL";
                }

            }
        }

        $all_keys = array_keys($set_vals);
        if (count(array_intersect(array('title', 'description', 'keywords'), $all_keys))) {
            $set_vals['modified']          = "`modified` = NOW()";
            $set_vals['modified_staff_id'] = '`modified_staff_id`=' . formatSQLString($_GLOBALS['staff']['staff_id']);
        }
        unset($set_vals['rank']); //Do not allow ranking through this function
        if (count($set_vals)) {
            $set_vals['modified']          = "`modified` = NOW()";
            $set_vals['modified_staff_id'] = '`modified_staff_id`=' . formatSQLString($_GLOBALS['staff']['staff_id']);
            $sql                           = "UPDATE page_chapters SET " . implode(', ', $set_vals) . " WHERE id=" . $id . " LIMIT 1";
            $DB->query($sql);
        }
    } else {
        $insert_vals = array();
        foreach ($chapter as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (is_array($chapter[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                    $insert_vals[$key] = formatSQLString(implode(",", $chapter[$key]));
                } elseif (isset($chapter[$key])) {
                    $insert_vals[$key] = formatSQLString($value);
                } else {
                    $insert_vals[$key] = 'NULL';
                }

            }
        }

        if (count($insert_vals)) {
            $insert_vals['created_staff_id'] = formatSQLString($_GLOBALS['staff']['staff_id']);
            $sql                             = "INSERT INTO page_chapters(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES(" . implode(", ", $insert_vals) . ")";
            $DB->query($sql);
            $id = $DB->insert_id();
            $DB->query("UPDATE page_chapters SET rank=" . intval($id) . " WHERE id=" . intval($id) . " LIMIT 1");
        }
    }
    return $id;
}
function pageChapterDelete($id)
{
    global $DB;
    $DB->query("DELETE FROM page_chapters WHERE id=" . $id . " LIMIT 1");
}
function pageChapterExists($id)
{
    global $DB;
    $row = $DB->query_first("SELECT pc.id FROM page_chapters pc WHERE pc.id=" . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
    if ($row) {
        return true;
    }

    return false;
}
function pageChapterMoveRank($id, $rank_delta)
{
    global $DB;
    if (0 == $rank_delta) {
        return;
    }

    $rank      = array();
    $rank_list = array();
    $query     = $DB->query("SELECT pc.id,pc.rank FROM page_chapters pc ORDER BY pc.rank");
    while ($row = $DB->fetch_assoc($query)) {
        $rank_list[]      = $row['id'];
        $rank[$row['id']] = $row['rank'];
    }
    $current_rank = array_search($id, $rank_list);
    if (false === $current_rank) {
        return;
    }

    unset($rank_list[$current_rank]);
    $rank_list = array_values($rank_list);
    $new_rank  = max(0, min(count($rank_list) - 1, $current_rank + $rank_delta));
    //There appears to be a flaw here when $rank_delta<0 and $current_rank>0
    $new_list = array();
    while (count($new_list) < $new_rank && count($rank_list)) {
        $new_list[] = array_shift($rank_list);
    }

    $new_list[] = $id;
    while (count($rank_list)) {
        $new_list[] = array_shift($rank_list);
    }

    foreach ($rank as $id => $rank) {
        if ($new_list[$rank] == $id) {
            unset($new_list[$rank]);
        }
    }

    foreach ($new_list as $rank => $id) {
        $DB->query("UPDATE page_chapters SET rank=" . intval($rank) . " WHERE id=" . intval($id) . " LIMIT 1");
    }

}
function pageChapterGetOptionList($table_name)
{
    static $option_cache = array();
    if (isset($option_cache[$table_name])) {
        return $option_cache[$table_name];
    }

    global $DB;
    $table       = '';
    $key         = '';
    $value       = '';
    $option_list = array();
    switch ($table_name) {
        case 'staff_id':
            return getStaffList();
        case 'active':
            return array('y' => 'Yes', 'n' => 'No');
        case 'page_path':
            return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/page/chapter';
        case 'all_fields':
            return array_merge(pageChapterGetOptionList('base_fields'), pageChapterGetOptionList('ext_fields'));
        case 'all_fields_types':
            return array_merge(pageChapterGetOptionList('base_fields_types'), pageChapterGetOptionList('ext_fields_types'));
        case 'base_fields':
            $fields = array();
            foreach (getTableColumns('`page_chapters`') as $key) {
                $fields[$key] = '`pc`.`' . $key . '`';
            }

            return $fields;
        case 'base_fields_types':
            return getTableFields('`page_chapters`');
        case 'venue_country':
            return getCountryList(false);
        case 'page_categories':
            return array();
        case 'ext_fields':
        case 'extra_fields':
            return array(
                'create_time'   => 'UNIX_TIMESTAMP(`created`)',
                'change_time'   => 'UNIX_TIMESTAMP(`modified`)',
                'modified_unix' => 'UNIX_TIMESTAMP(`modified`)',
            );
        case 'ext_fields_types':
            return array();
        default:
            trigger_error('pageChapterGetOptionList(' . json_encode($table_name) . ') is undefined');
            break;
    }
    if ('' != $table) {
        $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
        while ($row = $DB->fetch_array($query)) {
            $option_list[$row[0]] = $row[1];
        }

        $DB->free_result($query);
    }
    $option_cache[$table_name] = $option_list;
    return $option_list;
}
function pageChapterGetValue($id, $field)
{
    global $DB;
    $fields = getTableFields('`page_chapters`');
    if (array_key_exists($field, $fields)) {
        $row = $DB->query_first("SELECT `" . $field . "` FROM page_chapters pc WHERE pc.id=" . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
        return $row[$field];
    }
    $special_fields = pageChapterGetOptionList('ext_fields');
    if (array_key_exists($field, $special_fields)) {
        $row = $DB->query_first("SELECT " . $special_fields[$field] . " AS `" . $field . "` FROM `page_chapter` AS `pc` WHERE `p`.`id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
        return $row[$field];
    }
    return null;
}
function pageChapterHasValue($id, $field, $value)
{
    return (pageChapterGetValue($id, $field) == $value);
}
function pageChapterSetValue($id, $field, $value)
{
    global $DB;
    $fields = getTableFields('`page_chapters`');
    if (array_key_exists($field, $fields)) {
        $DB->query("UPDATE page_chapters SET `" . $field . "`=" . formatSQLString($value) . " WHERE id=" . intval($id) . " LIMIT 1");
        return true;
    }
    return false;
}
function pageChapterGetLock($id)
{
    global $DB;
    $row = $DB->query_first("SELECT pl.*,UNIX_TIMESTAMP(pl.expiry_date) AS expiry_time FROM page_chapters_locks pl WHERE pl.id=" . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
    if ($row['expiry_time'] < time()) {
        $row = false;
    }

    return $row;
}
function pageChapterSetLock($id, $staff_id = '', $timeout = 0)
{
    global $DB;
    $DB->query("DELETE FROM page_chapters_locks WHERE id=" . intval($id));
    if (0 == $timeout) {
        $timeout = 300;
    }

    if ('' != $staff_id) {
        $DB->query("INSERT INTO page_chapters_locks(chapter_id,staff_id,expiry_date) VALUES(" . intval($id) . "," . formatSQLString($staff_id) . "," . formatSQLDateTime(time() + $timeout) . ")");
    }

}
