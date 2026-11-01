<?php
if (!class_exists('GenericSection_Handler')) {
    class GenericSection_Handler
    {
        //Please define in your extended class:
        //var $section_name;    //The name of the section in lowercase, singular
        //var $table_name;        //The name of the main table, ie. plural
        //var $primary_key;        //The name of the primary key
        //var $search_bits;        //An array of fields used as the default search fields
        //var $database_prefix;    //The prefix to table names, to override database selection

        protected $primary_key = "id";

        public function _list_to_sql($item_list)
        {
            global $DB;

            /**
             * Empty or non-list, return false clause
             */
            if (!is_array($item_list) || count($item_list) == 0) {
                return '0';
            }

            /**
             * Help the query cache by sorting
             */
            $item_list = array_unique($item_list);
            sort($item_list);

            /**
             * No optimisations available for non-numeric, detect and return
             */
            foreach ($item_list as $v1) {
                if (!is_numeric($v1)) {
                    $result = array();
                    foreach ($item_list as $v2) {
                        $result[] = $DB->escape($v2);
                    }

                    return '`a`.`' . $this->primary_key . '` IN(' . implode(', ', $result) . ')';
                }
            }

            /**
             * Handle single-item list
             */
            if (count($item_list) == 1) {
                return '`a`.`' . $this->primary_key . '` = ' . reset($item_list);
            }

            /**
             * Estimate the size of a normal "IN" list
             */
            $size_normal = 11;
            foreach ($item_list as $v) {
                $size_normal += strlen($v) + 2;
            }

            if ($size_normal < 34) {
                return '`a`.`' . $this->primary_key . '` IN(' . implode(", ", $item_list) . ')';
            }

            /**
             * Build a ranged list and return if smaller
             */
            $size_ranged = -4;
            $result      = array();
            foreach (convertListToRanges($item_list) as $v) {
                if ($v[1] > $v[0]) {
                    $addme = sprintf('(`a`.`' . $this->primary_key . '` >= %d AND `a`.`' . $this->primary_key . '` <= %d)', $v[0], $v[1]);
                } else {
                    $addme = sprintf('`a`.`' . $this->primary_key . '` = %d', $v[0]);
                }

                $result[] = $addme;
                $size_ranged += strlen($addme) + 4;
                if ($size_ranged > $size_normal) {
                    break;
                }

            }

            /**
             * Return the "RANGE" list
             */
            if ($size_ranged < $size_normal) {
                return implode(' OR ', $result);
            }

            /**
             * Return the "IN" list
             */
            return '`a`.`' . $this->primary_key . '` IN(' . implode(', ', $item_list) . ')';
        }
        public function chunk($item_list, $group_by = '', $split_sets = true)
        {
            global $DB;
            if (!is_array($item_list) || count($item_list) < 1) {
                return $item_list;
            }

            if (!is_array($group_by)) {
                $group_by = array($group_by);
            }

            $fields      = $this->getOptionList('all_fields');
            $field_types = $this->getOptionList('all_fields_types');
            $field_list  = array($this->primary_key);
            foreach ($group_by as $field) {
                if (array_key_exists($field, $fields)) {
                    $field_list[] = $fields[$field] . " AS `" . $field . "`";
                }
            }

            $field_list = array_unique($field_list);
            $sql        = "SELECT " . implode(', ', $field_list) . " FROM " . $this->database_prefix . "`" . $this->table_name . "` AS `a` WHERE " . $this->_list_to_sql($item_list);
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
                    $pointers[$pointer_key][] = $row[$this->primary_key];
                }

            }
            $DB->free_result($query);
            return $results;
        }
        public function commentFromDatabase($comment_id)
        {
            global $DB;
            return $DB->query_first("SELECT *, UNIX_TIMESTAMP(`comment_date`) AS `comment_time` FROM " . $this->database_prefix . "`" . $this->section_name . "_comments` WHERE `id` = " . intval($comment_id) . " LIMIT 1");
        }
        public function commentSearch($criteria, $min_time = 0, $max_time = 0)
        {
            global $DB;
            $sql         = "SELECT `ac`.`id` FROM " . $this->database_prefix . "`" . $this->section_name . "_comments` AS `ac`";
            $field_types = getTableFields($this->database_prefix . "`" . $this->section_name . "_comments`");
            $whereAND    = array();
            if (is_array($criteria)) {
                foreach ($criteria as $field => $value) {
                    if (isset($field_types[$field])) {
                        if (is_array($value)) {
                            if (count($value) > 0) {
                                $whereOR = array();
                                if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                                    foreach ($value as $sub_value) {
                                        $whereOR[] = "FIND_IN_SET(" . $DB->escape($sub_value) . ", " . $field . ")";
                                    }

                                } else {
                                    foreach ($value as $sub_value) {
                                        $whereOR[] = $field . " = " . $DB->escape($sub_value);
                                    }

                                }
                                $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                            } else {
                                return array();
                            }
                        } else {
                            if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                                $whereAND[] = "FIND_IN_SET(" . $DB->escape($value) . ", " . $field . ")";
                            } else {
                                $whereAND[] = $field . " = " . $DB->escape($value);
                            }
                        }
                        unset($field);
                    }
                }

            } elseif ('' != $criteria) {
                $whereAND[] = '`ac`.`id` = ' . intval($criteria);
            }

            if (0 != $min_time) {
                $whereAND[] = 'UNIX_TIMESTAMP(`ac`.`comment_date`) > ' . $min_time;
            }

            if (0 != $max_time) {
                $whereAND[] = 'UNIX_TIMESTAMP(`ac`.`comment_date`) < ' . $max_time;
            }

            if (count($whereAND) > 0) {
                $sql .= " WHERE " . implode(" AND ", $whereAND);
            }

            $sql .= " GROUP BY `ac`.`id`";
            $sql .= " ORDER BY `ac`.`id` ASC";
            $results = array();
            $query   = $DB->query($sql);
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row['id'];
            }
            $DB->free_result($query);
            return $results;
        }
        public function commentToDatabase($id, $comment, $type = 'admin')
        {
            global $DB;
            global $_GLOBALS;
            $title = $sentto = '';
            if (is_array($comment)) {
                $title   = $comment['comment_title'];
                $sentto  = $comment['comment_sentto'];
                $comment = $comment['comment_text'];
            }
            $DB->query("INSERT INTO " . $this->database_prefix . "`" . $this->section_name . "_comments` (`" . $this->section_name . "_id`, `staff_id`, `comment_type`, `comment_date`, `comment_text`, `comment_title`, `comment_sentto`) VALUES (" . intval($id) . ", " . $_GLOBALS['staff']['staff_id'] . ", " . $DB->escape($type) . ", NOW(), " . $DB->escape($comment) . ", " . $DB->escape($title) . ", " . $DB->escape($sentto) . ")");
            return $DB->insert_id();
        }
        public function commentsFromDatabase($id)
        {
            global $DB;
            $results = array();
            $query   = $DB->query("SELECT *, UNIX_TIMESTAMP(`comment_date`) AS `comment_time` FROM " . $this->database_prefix . "`" . $this->section_name . "_comments` WHERE `" . $this->section_name . "_id` = " . intval($id));
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row;
            }

            $DB->free_result($query);
            return $results;
        }
        public function delete($id)
        {
            global $DB;
            $DB->query("DELETE FROM " . $this->database_prefix . "`" . $this->table_name . "` WHERE `" . $this->primary_key . "` = " . intval($id) . " LIMIT 1");
        }
        public function exists($id)
        {
            static $results = array();
            if (array_key_exists($id, $results)) {
                return $results[$id];
            }

            global $DB;
            $row = $DB->query_first("SELECT `" . $this->primary_key . "` FROM " . $this->database_prefix . "`" . $this->table_name . "` WHERE `" . $this->primary_key . "` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
            if ($row) {
                return $results[$id] = true;
            }

            return false;
        }
        public function formatNumber($id)
        {
            return sprintf('%06d', $id);
        }
        public function fromDatabase($id)
        {
            global $DB;
            $fields = array('*' => '`a`.*');
            foreach ($this->getOptionList('ext_fields') as $key => $value) {
                $fields[$key] = $value . " AS `" . $key . "`";
            }

            $result = $DB->query_first("SELECT " . implode(", ", $fields) . " FROM " . $this->database_prefix . "`" . $this->table_name . "` AS `a` WHERE `a`.`" . $this->primary_key . "` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
            if (false === $result) {
                $result = array();
                foreach ($this->getOptionList('all_fields_types') as $key => $row) {
                    $result[$key] = $row[4];
                }

            }
            return $result;
        }
        public function fromPost($posted)
        {
            global $DB;
            $id                         = $posted[$this->primary_key];
            $result                     = $this->fromDatabase($id);
            $result[$this->primary_key] = intval($id);
            if (is_array($posted)) {
                foreach ($posted as $k => $v) {
                    if (is_array($v)) {
                        $result[$k] = implode(',', $v);
                    } else {
                        $result[$k] = $v;
                    }
                }

            }
            if ($id <= 0) {
                unset($result[$this->primary_key]);
            }

            return $result;
        }
        public function getLock($id)
        {
            global $DB;
            $row = $DB->query_first("SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM " . $this->database_prefix . "`generic_locks` WHERE `section_name` = " . $DB->escape($this->section_name) . " AND `generic_id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
            if ($row['expiry_time'] < time()) {
                $row = false;
            }

            return $row;
        }
        public function getLocks()
        {
            global $DB;
            $result = array();
            $now    = formatSQLDateTime(mktime(date("H"), 0, 0));
            $query  = $DB->query("SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM " . $this->database_prefix . "`generic_locks` WHERE `section_name` = " . $DB->escape($this->section_name) . " AND `expiry_date` > " . $now);
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["generic_id"]] = $row;
            }

            $DB->free_result($query);
            return $result;
        }
        public function getOptionList($table_name)
        {
            switch ($table_name) {
                case 'all_fields':
                    return array_merge($this->getOptionList('base_fields'), $this->getOptionList('ext_fields'));
                case 'all_fields_types':
                    return array_merge($this->getOptionList('base_fields_types'), $this->getOptionList('ext_fields_types'));
                case 'base_fields':
                    $fields = array();
                    foreach (getTableColumns($this->database_prefix . "`" . $this->table_name . "`") as $key) {
                        $fields[$key] = '`a`.`' . $key . '`';
                    }

                    return $fields;
                case 'base_fields_types':
                    return getTableFields($this->database_prefix . "`" . $this->table_name . "`");
                case 'ext_fields_types':
                    return array();
                case 'extra_fields':
                case 'ext_fields':
                    return array();
                    break;
            }
            return array();
        }
        public function getValue($id, $field)
        {
            global $DB;
            $fields = $this->getOptionList('all_fields');
            if (array_key_exists($field, $fields)) {
                $row = $DB->query_first("SELECT " . $fields[$field] . " AS " . $field . " FROM " . $this->database_prefix . "`" . $this->table_name . "` AS `a` WHERE `a`.`" . $this->primary_key . "` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
                return $row[$field];
            }
            return null;
        }
        public function groupCount($item_list, $group_by = '', $split_sets = true)
        {
            global $DB;
            if (!is_array($item_list) || count($item_list) < 1) {
                return $item_list;
            }

            if ('' === $group_by) {
                return count($item_list);
            }

            if (!is_array($group_by)) {
                $group_by = array($group_by);
            }

            if (count($group_by) == 0) {
                return count($item_list);
            }

            $fields      = $this->getOptionList('all_fields');
            $field_types = $this->getOptionList('all_fields_types');
            $field_list  = array('row_count' => 'COUNT(*) AS `row_count`');
            foreach ($group_by as $field) {
                if (array_key_exists($field, $fields)) {
                    $field_list[$field] = $fields[$field] . " AS `" . $field . "`";
                }
            }

            $field_list = array_unique($field_list);
            $group_list = $field_list;
            unset($group_list['row_count']);
            if (count($group_list) == 0) {
                return array(count($item_list));
            }

            $query   = $DB->query("SELECT " . implode(', ', $field_list) . " FROM " . $this->database_prefix . "`" . $this->table_name . "` AS `a` WHERE " . $this->_list_to_sql($item_list) . " GROUP BY " . implode(', ', array_keys($group_list)));
            $results = array();
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
                            if (!isset($pointers[$key1][$key2])) {
                                $pointers[$key1][$key2] = array();
                            }

                            $new_pointers[] = &$pointers[$key1][$key2];
                        }
                    }
                    $pointers = $new_pointers;
                }
                foreach ($pointers as $pointer_key => $pointer) {
                    if (is_array($pointers[$pointer_key])) {
                        $pointers[$pointer_key] = 0;
                    }

                    $pointers[$pointer_key] += $row['row_count'];
                }
            }
            $DB->free_result($query);
            return $results;
        }
        public function hasValue($id, $field, $value)
        {
            return ($this->getValue($id, $field) == $value);
        }
        public function search($criteria = '',$order_by=NULL)
        {
            global $DB;
            $results    = array();
            $sql        = "SELECT `a`.`" . $this->primary_key . "` FROM " . $this->database_prefix . "`" . $this->table_name . "` AS `a`";
            $joins      = array();
            $fields     = getTableFields($this->database_prefix . "`" . $this->table_name . "`");
            $ext_fields = $this->getOptionList('extra_fields');
            $whereAND   = array();
            if (is_array($criteria)) {
                foreach ($criteria as $field => $value) {
                    if (isset($fields[$field])) {
                        if (is_array($value)) {
                            if (count($value) > 0) {
                                $whereOR = array();
                                if (substr($fields[$field][1], 0, 4) == 'set(') {
                                    foreach ($value as $sub_value) {
                                        if ("" == $sub_value) {
                                            $whereOR[] = "`a`.`" . $field . "` = ''";
                                        } else {
                                            $whereOR[] = "FIND_IN_SET(" . $DB->escape($sub_value) . ", `a`.`" . $field . "`)";
                                        }
                                    }

                                } else {
                                    foreach ($value as $sub_value) {
                                        $whereOR[] = "`a`.`" . $field . "` = " . $DB->escape($sub_value);
                                    }

                                }
                                $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                            } else {
                                return array();
                            }
                        } else {
                            if($field == 'date_schedule'){
                                $whereAND[] = "( `a`.`date_schedule` = '0000-00-00' OR `a`.`date_schedule` <= ".$DB->escape($value) . ")";
                            }else{
                                if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                                $whereAND[] = "FIND_IN_SET(" . $DB->escape($value) . ", " . $field . ")";
                                } else {
                                    $whereAND[] = $field . " = " . $DB->escape($value);
                                }
                            }
                        }
                        unset($fields[$field]);
                    } elseif (isset($ext_fields[$field])) {
                        if (is_array($value)) {
                            if (count($value) > 0) {
                                $whereOR = array();
                                foreach ($value as $sub_value) {
                                    $whereOR[] = $ext_fields[$field] . " = " . $DB->escape($sub_value);
                                }

                                $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                            } else {
                                return array();
                            }
                        } else {
                            $whereAND[] = $ext_fields[$field] . " = " . $DB->escape($value);
                        }
                        unset($ext_fields[$field]);
                    }
                }
            } elseif (is_string($criteria) && '' != $criteria) {
                $criteria = array('any_text' => $criteria);
            } else {
                $criteria = array();
            }

            $search_for = array();
            if (array_key_exists("any_text", $criteria)) {
                if (is_array($criteria['any_text'])) {
                    $search_for = $criteria['any_text'];
                }

                if (is_string($criteria['any_text'])) {
                    $search_for = explode(' ', $criteria['any_text']);
                }

            }
            if (count($search_for) > 0) {
                $searchBits = array();
                if (is_array($this->search_bits)) {
                    $searchBits = $this->search_bits;
                }

                foreach ($search_for as $findme) {
                    $whereOR = array();
                    foreach ($searchBits as $key) {
                        if (trim($findme) != '') {
                            $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                        }
                    }

                    if (count($whereOR) > 0) {
                        $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                    }

                }
            }
            if (count($whereAND) > 0) {
                $sql .= " WHERE " . implode(" AND ", $whereAND);
            }
            if($order_by){
                if($order_by == 'date_added'){
                    $sql .= " ORDER BY 
                            CASE 
                                WHEN date_schedule = '0000-00-00' THEN date_added 
                                ELSE date_schedule 
                            END DESC, date_added DESC";
                }else{
                    $sql .= " ORDER BY `a`.`" . $order_by . "` DESC";
                }
                
            }else{
                
            }
            
            if(array_key_exists("limit", $criteria) && array_key_exists("offset", $criteria)){
                $sql .= sprintf(
                    " LIMIT %d, %d",
                    max(0, $criteria['offset']),
                    max(0, $criteria['limit'])
                );
            }
            $query = $DB->query($sql);
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row[$this->primary_key];
            }
            $DB->free_result($query);
            return $results;
        }
        public function totalnews($criteria = '')
        {
            global $DB;
            $results    = array();
            $sql        = "SELECT count(`a`.`" . $this->primary_key . "`) as total FROM " . $this->database_prefix . "`" . $this->table_name . "` AS `a`";
            $joins      = array();
            $whereAND   = array();
             $fields     = getTableFields($this->database_prefix . "`" . $this->table_name . "`");
            if (is_array($criteria)) {
                foreach ($criteria as $field => $value) {
                    if (isset($fields[$field])) {
                        if (is_array($value)) {
                            if (count($value) > 0) {
                                $whereOR = array();
                                if (substr($fields[$field][1], 0, 4) == 'set(') {
                                    foreach ($value as $sub_value) {
                                        if ("" == $sub_value) {
                                            $whereOR[] = "`a`.`" . $field . "` = ''";
                                        } else {
                                            $whereOR[] = "FIND_IN_SET(" . $DB->escape($sub_value) . ", `a`.`" . $field . "`)";
                                        }
                                    }

                                } else {
                                    foreach ($value as $sub_value) {
                                        $whereOR[] = "`a`.`" . $field . "` = " . $DB->escape($sub_value);
                                    }

                                }
                                $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                            } else {
                                return array();
                            }
                        } else {
                            if($field == 'date_schedule'){
                                $whereAND[] = "( `a`.`date_schedule` = '0000-00-00' OR `a`.`date_schedule` <= ".$DB->escape($value) . ")";
                            }else{
                                if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                                $whereAND[] = "FIND_IN_SET(" . $DB->escape($value) . ", " . $field . ")";
                                } else {
                                    $whereAND[] = $field . " = " . $DB->escape($value);
                                }
                            }
                        }
                        unset($fields[$field]);
                    } elseif (isset($ext_fields[$field])) {
                        if (is_array($value)) {
                            if (count($value) > 0) {
                                $whereOR = array();
                                foreach ($value as $sub_value) {
                                    $whereOR[] = $ext_fields[$field] . " = " . $DB->escape($sub_value);
                                }

                                $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                            } else {
                                return array();
                            }
                        } else {
                            $whereAND[] = $ext_fields[$field] . " = " . $DB->escape($value);
                        }
                        unset($ext_fields[$field]);
                    }
                }
            } elseif (is_string($criteria) && '' != $criteria) {
                $criteria = array('any_text' => $criteria);
            } else {
                $criteria = array();
            }

            $search_for = array();
            if (array_key_exists("any_text", $criteria)) {
                if (is_array($criteria['any_text'])) {
                    $search_for = $criteria['any_text'];
                }

                if (is_string($criteria['any_text'])) {
                    $search_for = explode(' ', $criteria['any_text']);
                }

            }
            if (count($search_for) > 0) {
                $searchBits = array();
                if (is_array($this->search_bits)) {
                    $searchBits = $this->search_bits;
                }

                foreach ($search_for as $findme) {
                    $whereOR = array();
                    foreach ($searchBits as $key) {
                        if (trim($findme) != '') {
                            $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                        }
                    }

                    if (count($whereOR) > 0) {
                        $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                    }

                }
            }
            //$whereAND[]='`a`.`active`="y"';
            if (count($whereAND) > 0) {
                $sql .= " WHERE " . implode(" AND ", $whereAND);
            }
            //$sql .= " ORDER BY `a`.`" . $this->primary_key . "` ASC";
            $query = $DB->query($sql);
            $results=$DB->fetch_assoc($query);
            $DB->free_result($query);
            return $results;
        }
        public function setLock($id, $staff_id = '', $timeout = 0)
        {
            global $DB;
            $DB->query("DELETE FROM " . $this->database_prefix . "`generic_locks` WHERE `section_name` = " . $DB->escape($this->section_name) . " AND `generic_id` = " . intval($id));
            if (0 == $timeout) {
                $timeout = 300;
            }

            if ('' != $staff_id) {
                $DB->query("INSERT INTO " . $this->database_prefix . "`generic_locks` (`section_name`, `generic_id`, `staff_id`, `expiry_date`, `created`) VALUES (" . $DB->escape($this->section_name) . ", " . intval($id) . ", " . $DB->escape($staff_id) . ", " . formatSQLDateTime(time() + $timeout) . ", NOW())");
            }

        }
        public function setValue($id, $field, $value)
        {
            global $DB;
            $fields = $this->getOptionList('base_fields');
            if (array_key_exists($field, $fields)) {
                $DB->query("UPDATE " . $this->database_prefix . "`" . $this->table_name . "` AS `a` SET " . $fields[$field] . " = " . $DB->escape($value) . " WHERE `a`.`" . $this->primary_key . "` = " . intval($id) . " LIMIT 1");
                return true;
            }
            return false;
        }
        public function sort($item_list, $order_by = 'id')
        {
            global $DB;
            if (!is_array($item_list) || count($item_list) < 2) {
                return $item_list;
            }

            $fields = $this->getOptionList('all_fields');
            if (!isset($fields[$order_by])) {
                return $item_list;
            }

            $sql   = "SELECT `a`.`" . $this->primary_key . "` FROM " . $this->database_prefix . "`" . $this->table_name . "` AS `a`";
            $where = " WHERE " . $this->_list_to_sql($item_list);
            if (isset($fields[$order_by])) {
                $order = " ORDER BY " . $fields[$order_by] . " ASC";
            }

            $results = array();
            $query   = $DB->query($sql . $where . $order);
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row[$this->primary_key];
            }

            $DB->free_result($query);
            return $results;
        }
        public function toDatabase($item)
        {
            global $DB;
            $id      = intval($item[$this->primary_key]);
            $fields  = $this->getOptionList('base_fields_types');
            $changed = array();

            /**
             * Testing new method of insert/update by avoiding a check for existance
             */
            if ($this->exists($id)) {
                $set_vals = array();
                foreach ($item as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (is_array($item[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                            $set_vals[$key] = "`" . $key . "` = " . $DB->escape(implode(",", $item[$key]));
                        } elseif (isset($item[$key])) {
                            $set_vals[$key] = "`" . $key . "` = " . $DB->escape($item[$key]);
                        } else {
                            $set_vals[$key] = "`" . $key . "` = ''";
                        }

                    }
                }

                unset($set_vals['timestamp']);
                if (count($set_vals)) {
                    if (array_key_exists("modified", $fields) && !array_key_exists("modified", $set_vals)) {
                        $set_vals["modified"] = "`modified` = NOW()";
                    }

                    $sql = "UPDATE " . $this->database_prefix . "`" . $this->table_name . "` SET " . implode(', ', $set_vals) . " WHERE `" . $this->primary_key . "` = " . $id . " LIMIT 1";
                    $DB->query($sql);
                }
                return $id;
            } else {
                $insert_vals = array();
                foreach ($item as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (is_array($item[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                            $insert_vals[$key] = $DB->escape(implode(",", $item[$key]));
                        } elseif (isset($item[$key])) {
                            $insert_vals[$key] = $DB->escape($value);
                        } else {
                            $insert_vals[$key] = "''";
                        }

                    }
                }

                unset($insert_vals['timestamp']);
                if (count($insert_vals)) {
                    if (array_key_exists("created", $fields) && !array_key_exists("created", $insert_vals)) {
                        $insert_vals["created"] = "NOW()";
                    }

                    $sql = "INSERT INTO " . $this->database_prefix . "`" . $this->table_name . "` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")";
                    $DB->query($sql);
                    return $DB->insert_id();
                }
            }
            return $id;
        }
    }
}
