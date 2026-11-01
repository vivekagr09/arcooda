<?php
include_once __DIR__ . '/db_mysql.php';

class DB_SQL_Extended extends DB_SQL
{
    /**
     * @deprecated
     *
     * @param mixed $fields
     * @param mixed $i
     *
     * @return null
     */
    public function field_name($fields, $i)
    {
        $this->halt("Deprecated DB_SQL->field_name");
        return null;
    }

    /**
     * @deprecated
     *
     * @param string $table
     *
     * @return null
     */
    public function list_fields($table)
    {
        $this->halt("Deprecated DB_SQL->list_fields");
        return null;
    }

    /**
     * @deprecated
     *
     * @param mysqli_result $result
     *
     * @return null
     */
    public function num_fields($result = null)
    {
        $this->halt("Deprecated DB_SQL->num_fields");
        return null;
    }

    /**
     * @deprecated use query_table
     *
     * @param string $query_string
     * @param int    $result_type
     *
     * @return mixed[][]|bool
     */
    public function get_results($query_string, $result_type = MYSQLI_BOTH)
    {
        $result = [];
        if ($query = $this->query($query_string)) {
            //TODO: This would be an error case
            if (is_bool($query)) {
                return $query;
            }

            $result = $this->fetch_all($query, MYSQLI_ASSOC);
            $this->free($query);
        }

        return $result;
    }

    /**
     * @param string  $query_string
     * @param ?string $key
     *
     * @return mixed|null|bool
     */
    public function query_value($query_string, $key = null)
    {
        $result = null;

        // does a query and returns first row
        if ($query = $this->query($query_string)) {
            if (is_bool($query)) {
                return $query;
            }

            if ($row = $this->fetch_assoc($query)) {
                $result = $row[$key] ?? reset($row) ?? null;
            }
            $this->free($query);
        }

        return $result;
    }

    /**
     * @param string  $query_string
     * @param ?string $key
     *
     * @return mixed[][]|bool
     */
    public function query_table($query_string, $key = null)
    {
        $result = [];
        if ($query = $this->query($query_string)) {
            //TODO: This would be an error case
            if (is_bool($query)) {
                return $query;
            }

            foreach ($this->fetch_all($query, MYSQLI_ASSOC) as $row) {
                $result[$row[$key] ?? null] = $row;
            }
            $this->free($query);
        }

        return $result;
    }

    /**
     * @param string $query_string
     * @param ?string $key
     * @param ?string $value
     *
     * @return mixed[]|bool
     */
    public function query_list($query_string, $key = null, $value = null)
    {
        $result = [];
        if ($query = $this->query($query_string)) {
            //TODO: This would be an error case
            if (is_bool($query)) {
                return $query;
            }

            foreach ($this->fetch_all($query, MYSQLI_ASSOC) as $row) {
                $result[$row[$key] ?? reset($row)] = $row[$value] ?? end($row);
            }
            $this->free($query);
        }

        return $result;
    }

    /**
     * @deprecated Use table_columns
     *
     * @param string $table_name
     *
     * @return array{
     *     Field:string,
     *     Type:string,
     *     Null:string,
     *     Key:string,
     *     Default:mixed,
     *     Extra:string
     * }[]
     */
    public function get_table_fields($table_name)
    {
        return $this->table_columns($table_name);
    }

    /**
     * @deprecated Use table_fields
     *
     * @param string $table_name
     *
     * @return string[]
     */
    public function get_table_columns($table_name)
    {
        return $this->table_fields($table_name);
    }


    /**
     * @param string $table
     * @param string $primary_key
     *
     * @return mixed[]
     */
    public function modelDefault($table, $primary_key = 'id')
    {
        $result = array();

        $select_list = array();
        foreach ($this->table_defaults_ext($table) as $k => $v) {
            if ($k === $primary_key) {
                continue; // No default for primary key
            }
            if (is_null($v)) {
                continue; // No default available for this key
            }
            if (is_bool($v) || is_int($v) || is_float($v)) {
                $result[$k] = $v;
                continue;
            }

            if ('NULL' === strtoupper($v)) {
                $result[$k] = null; // Null expression
                continue;
            }
            if ('TRUE' === strtoupper($v)) {
                $result[$k] = true; // True expression
                continue;
            }
            if ('FALSE' === strtoupper($v)) {
                $result[$k] = false; // False expression
                continue;
            }

            if (preg_match('/^-?[_a-z0-9, \\.]+$/i', $v)) {
                $result[$k] = $v; // Unquoted constant
                continue;
            }
            if (preg_match('/^\'([^\'\\\\]*)\'$/', $v, $m)) {
                $result[$k] = $m[1]; // Quoted string, no escapes
                continue;
            }

            $select_list[] = sprintf(
                "DEFAULT(`%s`) AS `%s`",
                $k,
                $k
            );
        }
    
        if (count($select_list) && $row = $this->query_first(sprintf(
            "SELECT %s FROM (SELECT 1) AS `dummy` LEFT JOIN %s ON TRUE LIMIT 1",
            implode(", ", $select_list),
            $table
        ), MYSQLI_ASSOC)) {
            $result = array_merge($result, $row);
        }
    
        return $result;
    }

    /**
     * @param int    $id
     * @param string $table
     * @param string $primary_key
     *
     * @return int
     */
    public function modelDelete($id, $table, $primary_key = 'id')
    {
        global $_GLOBALS;

        $fields = $this->table_columns($table);

        if (array_key_exists('deleted_at', $fields)) {
            if ($this->query(sprintf(
                "UPDATE %s SET %s`deleted_at` = NOW() WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
                $table,
                (array_key_exists('deleted_by', $fields)) ? sprintf("`deleted_by` = %d, ", $_GLOBALS['staff']['id']) : '',
                $primary_key,
                $id
            ))) {
                return $this->affected_rows;
            }
        } elseif (array_key_exists('deleted_by', $fields)) {
            if ($this->query(sprintf(
                "UPDATE %s SET `deleted_by` = %d WHERE `deleted_by` IS NULL AND `%s` = %d LIMIT 1",
                $table,
                $_GLOBALS['staff']['id'],
                $primary_key,
                $id
            ))) {
                return $this->affected_rows;
            }
        } else {
            if ($this->query(sprintf(
                "DELETE FROM %s WHERE `%s` = %d LIMIT 1",
                $table,
                $primary_key,
                $id
            ))) {
                return $this->affected_rows;
            }
        }

        return 0;
    }

    /**
     * @param int    $id
     * @param string $table
     * @param string $primary_key
     *
     * @return bool
     */
    public function modelExists($id, $table, $primary_key = 'id')
    {
        return !!$this->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `%s` = %s LIMIT 1",
            $table,
            $primary_key,
            $this->escape($id)
        ), MYSQLI_ASSOC);
    }

    /**
     * @param int    $id
     * @param string $table
     * @param string $primary_key
     *
     * @return mixed[]|null
     */
    public function modelLoad($id, $table, $primary_key = 'id')
    {
        return $this->query_first(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
    }

    /**
     * @param int    $id
     * @param string $field
     * @param string $table
     * @param string $primary_key
     *
     * @return mixed
     */
    public function modelGet($id, $field, $table, $primary_key = 'id')
    {
        $fields = $this->table_columns($table);
        if (array_key_exists($field, $fields)) {
            if ($row = $this->query_first(sprintf(
                "SELECT `%s` FROM %s WHERE `%s` = %d LIMIT 1",
                $field,
                $table,
                $primary_key,
                $id
            ), MYSQLI_ASSOC)) {
                return $row[$field] ?? null;
            }
        }
        return null;
    }

    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     * @param string $table
     * @param string $primary_key
     *
     * @return int
     */
    public function modelSet($id, $field, $value, $table, $primary_key = 'id')
    {
        global $_GLOBALS;

        $fields = $this->table_columns($table);

        if (array_key_exists($field, $fields)) {
            if ($this->query(sprintf(
                "UPDATE %s SET %s`%s` = %s WHERE `%s` = %d LIMIT 1",
                $table,
                (array_key_exists('updated_by', $fields)) ? sprintf("`updated_by` = %d,", $_GLOBALS['staff']['id'] ?? 0) : '',
                $field,
                $this->escape($value),
                $primary_key,
                $id
            ))) {
                return $this->affected_rows;
            }
        }
        return 0;
    }

    /**
     * @todo Split this function into modelInsert and modelUpdate
     *
     * @param mixed[] $item
     * @param string  $table
     * @param string  $primary_key
     *
     * @return int|string
     */
    public function modelSave(&$item, $table, $primary_key = 'id')
    {
        global $_GLOBALS;

        $fields = $this->table_columns($table);

        $id     = null;
        $before = null;
        if (array_key_exists($primary_key, $item)) {
            $id     = $item[$primary_key];
            $before = $this->query_first(sprintf(
                "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
                $table,
                $primary_key,
                $id
            ));
        }

        if (!is_null($before)) {
            $set_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item) && $item[$k] !== $before[$k]) {
                    if (is_array($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $this->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $this->escape($item[$k]);
                    } elseif ('YES' === $fields[$k]['Null']) {
                        $set_vals[$k] = "`" . $k . "` = NULL";
                    }
                }
            }
            if (count($set_vals)) {
                if (array_key_exists('updated_by', $fields)) {
                    $item['updated_by'] = $_GLOBALS['staff']['id'] ?? 0;
                    $set_vals['updated_by'] = "`updated_by` = " . intval($item['updated_by']);
                }
                $this->query(sprintf(
                    "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                ));
            }
        } else {
            if (array_key_exists('created_by', $fields)) {
                $item['created_by'] = $_GLOBALS['staff']['id'];
            }
            $insert_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item)) {
                    if (is_array($item[$k])) {
                        $insert_vals[$k] = $this->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $insert_vals[$k] = $this->escape($item[$k]);
                    } elseif ('YES' === $fields[$k]['Null']) {
                        $insert_vals[$k] = "NULL";
                    }
                }
            }
            if (count($insert_vals)) {
                if ($this->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    $table,
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", $insert_vals)
                ))) {
                    $item[$primary_key] = $id = $this->insert_id;
                }
            }
        }

        return $id;
    }

    /**
     * @param int    $id
     * @param string $table
     * @param string $secondary_key
     *
     * @return int
     */
    public function childrenDelete($id, $table, $secondary_key)
    {
        global $_GLOBALS;

        $fields = $this->table_columns($table);

        if (array_key_exists('deleted_at', $fields)) {
            if ($this->query(sprintf(
                "UPDATE %s SET %s`deleted_at` = NOW() WHERE `deleted_at` IS NULL AND `%s` = %d",
                $table,
                (array_key_exists('deleted_by', $fields)) ? sprintf("`deleted_by` = %d, ", $_GLOBALS['staff']['id']) : '',
                $secondary_key,
                $id
            ))) {
                return $this->affected_rows;
            }
        } elseif (array_key_exists('deleted_by', $fields)) {
            if ($this->query(sprintf(
                "UPDATE %s SET `deleted_by` = %d WHERE `deleted_by` IS NULL AND `%s` = %d",
                $table,
                $_GLOBALS['staff']['id'],
                $secondary_key,
                $id
            ))) {
                return $this->affected_rows;
            }
        } else {
            if ($this->query(sprintf(
                "DELETE FROM %s WHERE `%s` = %d",
                $table,
                $secondary_key,
                $id
            ))) {
                return $this->affected_rows;
            }
        }

        return 0;
    }

    /**
     * @param int    $id
     * @param string $table
     * @param string $primary_key
     * @param string $secondary_key
     *
     * @return mixed[][]
     */
    public function childrenLoad($id, $table, $primary_key, $secondary_key)
    {
        $fields = $this->table_columns($table);

        $result = [];
        if ($query = $this->query(sprintf(
            "SELECT * FROM %s WHERE %s`%s` = %d",
            $table,
            (array_key_exists('deleted_at', $fields)) ? "`deleted_at` IS NULL AND " : '',
            $secondary_key,
            $id
        ))) {
            while ($row = $this->fetch_assoc($query)) {
                if (is_null($primary_key)) {
                    $result[] = $row;
                } else {
                    $result[$row[$primary_key]] = $row;
                }
            }
            $this->free_result($query);
        }

        return $result;
    }

    /**
     * @param int       $id
     * @param mixed[][] $items
     * @param string    $table
     * @param string    $primary_key
     * @param string    $secondary_key
     *
     * @return void
     */
    public function childrenSave($id, &$items, $table, $primary_key, $secondary_key)
    {
        foreach ($items as &$v) {
            $v[$secondary_key] = $id;
            $this->modelSave($v, $table, $primary_key);
        }
    }

    /**
     * @param string $table_reference
     *
     * @return array{
     *     quoted_db_name?:string,
     *     quoted_tbl_name?:string,
     *     db_name?:string,
     *     tbl_name?:string,
     * }
     */
    public function parseTableReference($table_reference)
    {
        $result = array();
    
        if (preg_match('/^(`?([^`]+)`?\\.)?(`?([^`]+)`?)?$/', $table_reference, $m)) {
            if (array_key_exists(2, $m) && '' != $m[2]) {
                $result['quoted_db_name']  = '`' . $m[2] . '`';
                $result['db_name']         = $m[2];
            }
            if (array_key_exists(4, $m) && '' != $m[4]) {
                $result['quoted_tbl_name'] = '`' . $m[4] . '`';
                $result['tbl_name']        = $m[4];
            }
        }
    
        if (!array_key_exists('db_name', $result)) {
            if ($row = $this->query_first(sprintf(
                "SELECT DATABASE() AS `db_name`"
            ), MYSQLI_ASSOC)) {
                $result['db_name'] = $row['db_name'];
            }
        }
    
        return $result;
    }

    /**
     * @param string $table
     *
     * @return mixed[]
     */
    function table_defaults_ext($table)
    {
        $table_reference = $this->parseTableReference($table);

        $result = array();
        if ($query = $this->query(sprintf(
            "SELECT `COLUMN_NAME`, `COLUMN_DEFAULT` FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = %s AND `TABLE_NAME` = %s",
            $this->escape($table_reference['db_name']),
            $this->escape($table_reference['tbl_name'])
        ))) {
            while ($row = $this->fetch_assoc($query)) {
                $result[$row['COLUMN_NAME']] = $row['COLUMN_DEFAULT'];
            }
            $this->free_result($query);
        }

        return $result;
    }
}
