<?php
if (!function_exists('projectSort')) {
    function projectSort($project_list, $sort_by)
    {
        global $DB;
        if (!is_array($project_list) || count($project_list) < 2) {
            return $project_list;
        }

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.projects");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);

        $temp_list = array();
        if (isset($fields[$sort_by])) {
            $query = $DB->query("SELECT w.project_id FROM `highwaygroup`.projects w WHERE w.project_id IN(" . implode(',', $project_list) . ") ORDER BY `" . $sort_by . "` ASC");
            while ($row = $DB->fetch_assoc($query)) {
                $temp_list[] = $row['project_id'];
            }

            $DB->free_result($query);
        }

        if (count($temp_list) > 0) {
            $project_list = $temp_list;
        }

        return $project_list;
    }
}

//-----------------------------------------------------------------------------

if (!function_exists('projectSearch')) {
    function projectSearch($criteria = '')
    {
        global $DB;
        $results = array();

        $sql      = " SELECT w.project_id FROM `highwaygroup`.projects w";
        $whereAND = array();
        if ('' != $criteria['site_id']) {
            $whereAND[] = "FIND_IN_SET('" . $criteria['site_id'] . "', w.site_id)>0";
        }

        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " GROUP BY w.project_id";
        $sql .= " ORDER BY w.project_id ASC";

        $query = $DB->query($sql);
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $results[] = $row['project_id'];
        }

        $DB->free_result($query);

        return $results;
    }
}

if (!function_exists('projectChapterSearch')) {
    function projectChapterSearch($criteria = '')
    {
        global $DB;
        $results = array();

        $whereAND = array();
        if ('' != $criteria['project_id']) {
            $whereAND[] = "`wc`.`project_id` = " . intval($criteria['project_id']);
        }

        $query = $DB->query(sprintf(
            "SELECT `wc`.`chapter_id` FROM `highwaygroup`.`project_chapters` AS `wc`%s GROUP BY `wc`.`chapter_id` ORDER BY `wc`.`chapter_rank` ASC",
            (count($whereAND)) ? (" WHERE " . implode(" AND ", $whereAND)) : ''
        ));
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $results[] = $row['chapter_id'];
        }
        $DB->free_result($query);

        return $results;
    }
}

if (!function_exists('projectPageSearch')) {
    function projectPageSearch($criteria = '')
    {
        global $DB;
        $results = array();

        $sql      = " SELECT wp.page_id FROM `highwaygroup`.project_pages wp";
        $whereAND = array();
        if ('' != $criteria['chapter_id']) {
            $whereAND[] = "wp.chapter_id=" . intval($criteria['chapter_id']);
        }

        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " GROUP BY wp.page_id";
        $sql .= " ORDER BY wp.page_rank ASC";

        $query = $DB->query($sql);
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $results[] = $row['page_id'];
        }

        $DB->free_result($query);

        return $results;
    }
}

//-----------------------------------------------------------------------------

if (!function_exists('projectExists')) {
    function projectExists($project_id)
    {
        global $DB;
        $row = $DB->query_first("SELECT project_id FROM `highwaygroup`.projects WHERE project_id=" . intval($project_id));
        if ($row) {
            return true;
        }

        return false;
    }
}

if (!function_exists('projectChapterExists')) {
    function projectChapterExists($chapter_id)
    {
        global $DB;
        $row = $DB->query_first("SELECT chapter_id FROM `highwaygroup`.project_chapters WHERE chapter_id=" . intval($chapter_id));
        if ($row) {
            return true;
        }

        return false;
    }
}

if (!function_exists('projectPageExists')) {
    function projectPageExists($page_id)
    {
        global $DB;
        $row = $DB->query_first("SELECT page_id FROM `highwaygroup`.project_pages WHERE page_id=" . intval($page_id));
        if ($row) {
            return true;
        }

        return false;
    }
}

if (!function_exists('projectNoteExists')) {
    function projectNoteExists($chapter_id)
    {
        global $DB;
        $row = $DB->query_first("SELECT chapter_id FROM `highwaygroup`.project_notes WHERE chapter_id=" . intval($chapter_id));
        if ($row) {
            return true;
        }

        return false;
    }
}

//-----------------------------------------------------------------------------

if (!function_exists('projectFromDatabase')) {
    function projectFromDatabase($project_id)
    {
        global $DB;
        $summary = array();
        if (projectExists($project_id)) {
            $summary = $DB->query_first("SELECT w.*, UNIX_TIMESTAMP(w.create_date) AS create_time, UNIX_TIMESTAMP(w.change_date) AS change_time FROM `highwaygroup`.projects w WHERE w.project_id=" . intval($project_id) . " LIMIT 1", MYSQLI_ASSOC);
        } else {
            $query = $DB->query("SHOW COLUMNS FROM `highwaygroup`.projects");
            while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                $summary[$row[0]] = $row[4];
            }

            $DB->free_result($query);
        }
        return $summary;
    }
}

if (!function_exists('projectChapterFromDatabase')) {
    function projectChapterFromDatabase($chapter_id)
    {
        global $DB;
        $summary = array();
        if (projectChapterExists($chapter_id)) {
            $summary = $DB->query_first("SELECT wc.*, UNIX_TIMESTAMP(wc.create_date) AS create_time, UNIX_TIMESTAMP(wc.change_date) AS change_time FROM `highwaygroup`.project_chapters wc WHERE wc.chapter_id=" . intval($chapter_id) . " LIMIT 1", MYSQLI_ASSOC);
        } else {
            $query = $DB->query("SHOW COLUMNS FROM `highwaygroup`.project_chapters");
            while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                $summary[$row[0]] = $row[4];
            }

            $DB->free_result($query);
        }
        return $summary;
    }
}

if (!function_exists('projectPageFromDatabase')) {
    function projectPageFromDatabase($page_id)
    {
        global $DB;
        $summary = array();
        if (projectPageExists($page_id)) {
            $summary = $DB->query_first("SELECT wp.*, UNIX_TIMESTAMP(wp.create_date) AS create_time, UNIX_TIMESTAMP(wp.change_date) AS change_time FROM `highwaygroup`.project_pages wp WHERE wp.page_id=" . intval($page_id) . " LIMIT 1", MYSQLI_ASSOC);
            if ('' == $summary['page_type']) {
                $summary['page_type'] = 'semihtml';
                if ('' != $summary['image_type']) {
                    $summary['page_type'] = reset(explode('/', $summary['image_type']));
                }

            }
        } else {
            $query = $DB->query("SHOW COLUMNS FROM `highwaygroup`.project_pages");
            while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                $summary[$row[0]] = $row[4];
            }

            $DB->free_result($query);
        }
        return $summary;
    }
}

if (!function_exists('projectNoteFromDatabase')) {
    function projectNoteFromDatabase($chapter_id)
    {
        global $DB;
        $summary = array();
        if (projectNoteExists($chapter_id)) {
            $q = $DB->query("SELECT * FROM `highwaygroup`.project_notes wp WHERE wp.chapter_id=" . intval($chapter_id) . " ORDER BY wp.note_id DESC");
            while ($row = $DB->fetch_array($q)) {
                $summary[] = $row;
            }

        } else {
            $query = $DB->query("SHOW COLUMNS FROM `highwaygroup`.project_notes");
            while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                $summary[$row[0]] = $row[4];
            }

            $DB->free_result($query);
        }
        return $summary;
    }
}

//-----------------------------------------------------------------------------

if (!function_exists('projectDelete')) {
    function projectDelete($project_id)
    {
        global $DB;
        $chapter_list = array();
        $query        = $DB->query("SELECT chapter_id FROM `highwaygroup`.project_chapters WHERE project_id=" . intval($project_id));
        while ($row = $DB->fetch_assoc($query)) {
            $chapter_list[] = $row['chapter_id'];
        }

        if (count($chapter_list) > 0) {
            return false;
        }

        $DB->query("DELETE FROM `highwaygroup`.projects WHERE project_id=" . intval($project_id) . " LIMIT 1");
        return true;
    }
}

if (!function_exists('projectChapterDelete')) {
    function projectChapterDelete($chapter_id)
    {
        global $DB;
        $page_list = array();
        $query     = $DB->query("SELECT page_id FROM `highwaygroup`.project_pages WHERE chapter_id=" . intval($chapter_id));
        while ($row = $DB->fetch_assoc($query)) {
            $page_list[] = $row['page_id'];
        }

        if (count($page_list) > 1) {
            return false;
        }

        $DB->query("DELETE FROM `highwaygroup`.project_pages WHERE chapter_id=" . intval($chapter_id) . " LIMIT 1");
        $DB->query("DELETE FROM `highwaygroup`.project_chapters WHERE chapter_id=" . intval($chapter_id) . " LIMIT 1");
        return true;
    }
}

if (!function_exists('projectPageDelete')) {
    function projectPageDelete($page_id)
    {
        global $DB;
        $DB->query("DELETE FROM `highwaygroup`.project_pages WHERE page_id=" . intval($page_id) . " LIMIT 1");
        return true;
    }
}

if (!function_exists('projectNoteDelete')) {
    function projectNoteDelete($chapter_id)
    {
        global $DB;
        $DB->query("DELETE FROM `highwaygroup`.project_notes WHERE chapter_id=" . intval($chapter_id) . " LIMIT 1");
        return true;
    }
}

//-----------------------------------------------------------------------------

if (!function_exists('projectFromPost')) {
    function projectFromPost($posted)
    {
        global $DB, $_GLOBALS;
        $project_id           = $posted['project_id'];
        $result               = projectFromDatabase($project_id);
        $result['project_id'] = intval($project_id);
        foreach ($result as $key => $value) {
            if (array_key_exists($key, $posted) && !is_array($posted[$key])) {
                $result[$key] = trim($posted[$key]);
            }
        }

        if ($project_id <= 0) {
            unset($result['project_id']);
            $result['create_date']  = formatDateTime();
            $result['create_time']  = time();
            $result['create_staff'] = $_GLOBALS['staff']['id'];
        }
        if ('' != $posted['site_id']) {
            $result['site_id'] = $posted['site_id'];
        }

        $result['change_date']  = formatDateTime();
        $result['change_time']  = time();
        $result['change_staff'] = $_GLOBALS['staff']['id'];
        return $result;
    }
}

if (!function_exists('projectChapterFromPost')) {
    function projectChapterFromPost($posted)
    {
        global $DB, $_GLOBALS;
        $chapter_id           = $posted['chapter_id'];
        $result               = projectChapterFromDatabase($chapter_id);
        $result['chapter_id'] = intval($chapter_id);
        if (is_array($posted)) {
            foreach ($result as $key => $value) {
                if (array_key_exists($key, $posted) && !is_array($posted[$key])) {
                    $result[$key] = trim($posted[$key]);
                }
            }
        }

        if ($chapter_id <= 0) {
            unset($result['chapter_id']);
            $result['create_date']  = formatDateTime();
            $result['create_time']  = time();
            $result['create_staff'] = $_GLOBALS['staff']['id'];
        }
        $result['change_date']  = formatDateTime();
        $result['change_time']  = time();
        $result['change_staff'] = $_GLOBALS['staff']['id'];
        return $result;
    }
}

if (!function_exists('projectPageFromPost')) {
    function projectPageFromPost($posted)
    {
        global $DB, $_GLOBALS;
        $page_id           = $posted['page_id'];
        $result            = projectPageFromDatabase($page_id);
        $result['page_id'] = intval($page_id);
        foreach ($result as $key => $value) {
            if (array_key_exists($key, $posted) && !is_array($posted[$key])) {
                $result[$key] = trim($posted[$key]);
            }
        }

        if ($page_id <= 0) {
            unset($result['page_id']);
            $result['create_date']  = formatDateTime();
            $result['create_time']  = time();
            $result['create_staff'] = $_GLOBALS['staff']['id'];
        }
        $result['change_date']  = formatDateTime();
        $result['change_time']  = time();
        $result['change_staff'] = $_GLOBALS['staff']['id'];
        return $result;
    }
}

if (!function_exists('projectNoteFromPost')) {
    function projectNoteFromPost($posted)
    {
        global $DB;
        $result = array(
            "chapter_id" => 0,
            "type"       => "",
            "staff"      => 0,
            "notes"      => "",
            "added_date" => "",
        );
        foreach ($result as $key => $value) {
            if (array_key_exists($key, $posted) && !is_array($posted[$key])) {
                $result[$key] = trim($posted[$key]);
            }
        }

        $result['added_date'] = formatDateTime();
        return $result;
    }
}

//-----------------------------------------------------------------------------

if (!function_exists('projectToDatabase')) {
    function projectToDatabase($new_project)
    {
        global $DB;
        $project_id  = intval($new_project['project_id']);
        $old_project = projectFromDatabase($project_id);
        $changes     = $new_project;
        foreach ($old_project as $key => $value) {
            if ($changes[$key] == $value) {
                unset($changes[$key]);
            }
        }

        foreach ($changes as $key => $value) {
            if (!array_key_exists($key, $old_project)) {
                unset($changes[$key]);
            }
        }

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.projects");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);
        $update_vals = array();
        $insert_vals = array();
        foreach ($changes as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (isset($changes[$key])) {
                    $update_vals[$key] = "`" . $key . "`=" . $DB->escape($value);
                    $insert_vals[$key] = $DB->escape($value);
                } else {
                    $update_vals[$key] = "`" . $key . "`=NULL";
                    $insert_vals[$key] = "NULL";
                }
            }
        }

        if (projectExists($project_id)) {
            if (count($update_vals) > 0) {
                $DB->query("UPDATE `highwaygroup`.projects SET " . implode(', ', $update_vals) . " WHERE project_id=" . $project_id . " LIMIT 1");
            }

        } else {
            if (count($insert_vals) > 0) {
                $DB->query("INSERT INTO `highwaygroup`.projects (" . implode(', ', array_keys($insert_vals)) . ") VALUES (" . implode(', ', array_values($insert_vals)) . ")");
            }

            $project_id = $DB->insert_id();
        }
        return $project_id;
    }
}

if (!function_exists('projectChapterToDatabase')) {
    function projectChapterToDatabase($new_chapter)
    {
        global $DB;
        $chapter_id  = intval($new_chapter['chapter_id']);
        $old_chapter = projectChapterFromDatabase($chapter_id);
        $changes     = $new_chapter;
        foreach ($old_chapter as $key => $value) {
            if ($changes[$key] == $value) {
                unset($changes[$key]);
            }
        }

        foreach ($changes as $key => $value) {
            if (!array_key_exists($key, $old_chapter)) {
                unset($changes[$key]);
            }
        }

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.project_chapters");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);
        $update_vals = array();
        $insert_vals = array();
        foreach ($changes as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (isset($changes[$key])) {
                    $update_vals[$key] = "`" . $key . "`=" . $DB->escape($value);
                    $insert_vals[$key] = $DB->escape($value);
                } else {
                    $update_vals[$key] = "`" . $key . "`=NULL";
                    $insert_vals[$key] = "NULL";
                }
            }
        }

        if (projectChapterExists($chapter_id)) {
            if (count($update_vals) > 0) {
                $DB->query("UPDATE `highwaygroup`.project_chapters SET " . implode(', ', $update_vals) . " WHERE chapter_id=" . $chapter_id . " LIMIT 1");
            }

        } else {
            if (count($insert_vals) > 0) {
                $DB->query("INSERT INTO `highwaygroup`.project_chapters (" . implode(', ', array_keys($insert_vals)) . ") VALUES (" . implode(', ', array_values($insert_vals)) . ")");
            }

            $chapter_id = $DB->insert_id();
        }
        return $chapter_id;
    }
}

if (!function_exists('projectPageToDatabase')) {
    function projectPageToDatabase($new_page)
    {
        global $DB;
        $page_id  = intval($new_page['page_id']);
        $old_page = projectPageFromDatabase($page_id);
        $changes  = $new_page;
        foreach ($old_page as $key => $value) {
            if ($changes[$key] == $value) {
                unset($changes[$key]);
            }
        }

        foreach ($changes as $key => $value) {
            if (!array_key_exists($key, $old_page)) {
                unset($changes[$key]);
            }
        }

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.project_pages");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);
        $update_vals = array();
        $insert_vals = array();
        foreach ($changes as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (isset($changes[$key])) {
                    $update_vals[$key] = "`" . $key . "`=" . $DB->escape($value);
                    $insert_vals[$key] = $DB->escape($value);
                } else {
                    $update_vals[$key] = "`" . $key . "`=NULL";
                    $insert_vals[$key] = "NULL";
                }
            }
        }

        if (projectPageExists($page_id)) {
            if (count($update_vals) > 0) {
                $DB->query("UPDATE `highwaygroup`.project_pages SET " . implode(', ', $update_vals) . " WHERE page_id=" . $page_id . " LIMIT 1");
            }

        } else {
            if (count($insert_vals) > 0) {
                $DB->query("INSERT INTO `highwaygroup`.project_pages (" . implode(', ', array_keys($insert_vals)) . ") VALUES (" . implode(', ', array_values($insert_vals)) . ")");
            }

            $page_id = $DB->insert_id();
        }
        return $page_id;
    }
}

if (!function_exists('projectNoteToDatabase')) {
    function projectNoteToDatabase($new_note)
    {
        global $DB, $_GLOBALS;

        $DB->query(sprintf(
            "INSERT INTO `highwaygroup`.`project_notes` (`chapter_id`, `type`, `staff`, `notes`, `added_date`) VALUES (%d, %s, %d, %s, %s)",
            $new_note['chapter_id'],
            $DB->escape($new_note['type']),
            $_GLOBALS['staff']['id'],
            $DB->escape($new_note['notes']),
            $DB->escape($new_note['added_date'])
        ));

        /*$DB->query("INSERT INTO `highwaygroup`.project_notes (`chapter_id`, `type`, `staff`, `notes`, `added_date`) VALUES ('{$new_note['chapter_id']}', '{$new_note['type']}', '". $_GLOBALS['staff']['id'] ."', '{$new_note['notes']}', '{$new_note['added_date']}')");*/

        $page_id = $DB->insert_id();

        return $page_id;
    }
}

if (!function_exists('projectGetOptionList')) {
    function projectGetOptionList($option)
    {
        global $DB, $_GLOBALS;
        switch ($option) {
            case "staff_id":
            case "create_staff":
            case "change_staff":
            case "staff":
            case "assigned":
            case "reporter":
                //if ($_GLOBALS['site_settings']['site_id'] == 3)
                {
                    $result = array();
                    $query  = $DB->query("SELECT `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name` FROM `highwaygroup`.`users` ORDER BY `first_name` ASC");
                    while ($row = $DB->fetch_assoc($query)) {
                        $result[$row['id']] = $row['name'];
                    }

                    $DB->free_result($query);

                    $query = $DB->query("SELECT `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name` FROM `highwaygroup`.`users` ORDER BY `first_name` ASC");
                    while ($row = $DB->fetch_assoc($query)) {
                        $result[$row['id']] = $row['name'];
                    }

                    $DB->free_result($query);

                    return $result;
                }
                return getStaffList();
            case "site_id":
                return getWebsitesList();
        }
        return null;
    }
}

if (!function_exists('projectSetLock')) {
    function projectSetLock($id, $staff_id = 0, $timeout = 120)
    {
        global $DB;
        $section_name = $DB->escape("project");
        if (0 != $staff_id) {
            $DB->query(sprintf("UPDATE `highwaygroup`.`generic_locks` SET `expiry_date` = %s, `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `staff_id` = %d AND `expiry_date` > NOW() LIMIT 1", formatSQLDateTime(time() + $timeout), $section_name, $id, $staff_id));

            if ($DB->affected_rows() == 0) {
                $DB->query(sprintf("INSERT INTO `highwaygroup`.`generic_locks` (`section_name`, `generic_id`, `staff_id`, `expiry_date`, `created`, `modified`) VALUES (%s, %d, %d, %s, NOW(), NOW())", $section_name, $id, $staff_id, formatSQLDateTime(time() + $timeout)));
            }
        } else {
            $DB->query(sprintf("UPDATE `highwaygroup`.`generic_locks` SET `expiry_date` = NOW(), `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `expiry_date` > NOW()", $section_name, $id));
        }
    }
}

if (!function_exists('projectGetLock')) {
    function projectGetLock($id)
    {
        global $DB;
        $row = $DB->query_first("SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `highwaygroup`.`generic_locks` WHERE `section_name` = 'project' AND `generic_id` = " . intval($id) . " AND `expiry_date` > NOW() LIMIT 1", MYSQLI_ASSOC);
        if ($row['expiry_time'] < time()) {
            $row = false;
        }

        return $row;
    }
}

if (!function_exists("projectGetLocks")) {
    function projectGetLocks()
    {
        global $DB;
        $result = array();
        $query  = $DB->query("SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `highwaygroup`.`generic_locks` WHERE `section_name` = 'project' AND `expiry_date` > NOW()");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row["generic_id"]] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}
