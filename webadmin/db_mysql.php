<?php
/*. require_module 'mysqli'; .*/
/*. require_module 'mysqli_result'; .*/

class DB_SQL extends mysqli
{
    const ASSOC = MYSQLI_ASSOC;
    const NUM   = MYSQLI_NUM;
    const BOTH  = MYSQLI_BOTH;

    /** @var mysqli_result[] */
    public $open_results = [];

    /** @var string[] $all_queries */
    public $all_queries = [];

    /** @var string[] $time_queries */
    public $time_queries = [];

    /**
     * @var array{
     *     Field:string,
     *     Type:string,
     *     Null:string,
     *     Key:string,
     *     Default:mixed,
     *     Extra:string
     * }[][] $column_cache
     */
    public $column_cache = [];

    /**
     * @param string $dir
     *
     * @return string[]
     */
    private function parse_my_cnfs($dir)
    {
        $result = [];
        do {
            $path    = $dir . '/.my.cnf';
            $old_dir = $dir;
            $dir     = dirname($dir);
            if (!is_file($path)) {
                continue;
            }
            if (false === ($ini = parse_ini_file($path, true))) {
                continue;
            }
            $result += $ini['client'] ?? [];
            break;
        } while ($old_dir != $dir);
        return $result;
    }

    /**
     * @param ?string $host
     * @param ?string $username
     * @param ?string $passwd
     * @param ?string $dbname
     * @param ?int    $port
     * @param ?string $socket
     */
    public function __construct($host = null, $username = null, $passwd = null, $dbname = null, $port = null, $socket = null)
    {
        $cnf = $this->parse_my_cnfs(__DIR__);
        parent::__construct(
            $host ?? $cnf['host'] ?? null,
            $username ?? $cnf['user'] ?? null,
            $passwd ?? $cnf['password'] ?? null,
            $dbname ?? $cnf['database'] ?? null,
            $port ?? $cnf['port'] ?? null,
            $socket ?? $cnf['socket'] ?? null
        );

        if (mysqli_connect_errno()) {
            trigger_error(mysqli_connect_error() ?? '');
        }
    }

    /**
     * @param string $query
     * @param int    $result_mode
     *
     * @return mysqli_result|bool
     */
    public function query($query, $result_mode = MYSQLI_STORE_RESULT)
    {
        $key = count($this->all_queries);
        $this->all_queries[$key] = $query;

        $time_start = explode(' ', microtime(false));
        $result     = parent::query($query, $result_mode);
        $time_end   = explode(' ', microtime(false));

        if ($result instanceof mysqli_result) {
            $this->open_results[$key] = $result;
            $this->num_rows           = $result->num_rows;
        }

        if (false === $result) {
            $this->halt("Invalid SQL: " . $query);
        }

        $this->time_queries[$key] = bcadd(bcsub($time_end[0], $time_start[0], 0), bcsub($time_end[1], $time_start[1], 6), 6);

        return $result;
    }

    /**
     * @deprecated use $insert_id instead
     *
     * @return int|string
     */
    public function insert_id()
    {
        return $this->insert_id;
    }

    /**
     * @deprecated use $affected_rows instead
     *
     * @return int
     */
    public function affected_rows()
    {
        return $this->affected_rows;
    }

    /** @var int|string $num_rows */
    public $num_rows = 0;

    /**
     * Detect invalid result id
     *
     * @param mysqli_result|null $result
     *
     * @return mysqli_result|false
     */
    private function result($result = null)
    {
        $result = $result ?? end($this->open_results);

        if (!($result instanceof mysqli_result)) {
            $this->halt(sprintf(
                "Invalid result %s specified",
                var_export($result, true)
            ));
            return false;
        }

        return $result;
    }

    /**
     * @param mysqli_result $result
     *
     * @return int|null
     */
    public function current_field($result = null)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return null;
        }

        return $result->current_field;
    }

    /**
     * @param mysqli_result $result
     * @param int           $offset
     *
     * @return bool
     */
    public function data_seek($result = null, $offset = 0)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return false;
        }

        return $result->data_seek($offset);
    }

    /**
     * @param mysqli_result $result
     * @param int           $mode
     *
     * @return mixed[][]
     */
    public function fetch_all($result = null, $mode = MYSQLI_NUM)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return [];
        }

        return $result->fetch_all($mode);
    }

    /**
     * @param ?mysqli_result $result
     * @param int            $mode
     *
     * @return mixed[]|null
     */
    public function fetch_array($result = null, $mode = MYSQLI_BOTH)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return null;
        }

        return $result->fetch_array($mode);
    }

    /**
     * @param ?mysqli_result $result
     *
     * @return mixed[]|null
     */
    public function fetch_assoc($result = null)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return null;
        }

        return $result->fetch_assoc();
    }

    /**
     * @param ?mysqli_result $result
     * @param int           $index
     *
     * @return object|false
     */
    public function fetch_field_direct($result = null, $index = 0)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return false;
        }

        return $result->fetch_field_direct($index);
    }

    /**
     * @param ?mysqli_result $result
     *
     * @return object|false
     */
    public function fetch_field($result = null)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return false;
        }

        return $result->fetch_field();
    }

    /**
     * @param ?mysqli_result $result
     *
     * @return object[]
     */
    public function fetch_fields($result = null)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return [];
        }

        return $result->fetch_fields();
    }

    /**
     * @param ?mysqli_result $result
     *
     * @return object|null
     */
    public function fetch_object($result = null)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return null;
        }

        return $result->fetch_object();
    }

    /**
     * @param ?mysqli_result $result
     *
     * @return mixed[]|null
     */
    public function fetch_row($result = null)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return null;
        }

        return $result->fetch_row();
    }

    /**
     * @param ?mysqli_result $result
     *
     * @return int|null
     */
    public function field_count($result = null)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return null;
        }

        return $result->field_count;
    }

    /**
     * @param mysqli_result $result
     * @param int           $index
     *
     * @return bool
     */
    public function field_seek($result = null, $index = 0)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return false;
        }

        return $result->field_seek($index);
    }

    /**
     * @param mysqli_result $result
     *
     * @return void
     */
    public function free($result = null)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return;
        }

        if (false !== ($key = array_search($result, $this->open_results, true))) {
            unset($this->open_results[$key]);
        }
        $result->free();
    }

    /**
     * @param mysqli_result $result
     *
     * @return int|null
     */
    public function lengths($result = null)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return null;
        }

        return $result->lengths;
    }

    /**
     * @param ?mysqli_result $result
     *
     * @return int|null
     */
    public function num_rows($result = null)
    {
        $result = $this->result($result);

        if (!($result instanceof mysqli_result)) {
            return null;
        }

        return $result->num_rows;
    }

    /**
     * Alias for free()
     *
     * @param ?mysqli_result $result
     *
     * @return void
     */
    public function free_result($result = null)
    {
        $this->free($result);
    }

    /**
     * @param string $query_string
     * @param int    $result_type
     *
     * @return mixed[]|null
     */
    public function query_first($query_string, $result_type = MYSQLI_BOTH)
    {
        $result = $this->query($query_string);

        if (!is_object($result)) {
            $this->halt("Invalid query " . $query_string);
            return null;
        }

        $return = $this->fetch_array($result, $result_type);
        $this->free($result);
        return $return;
    }

    /**
     * @param string $msg
     *
     * @return bool
     */
    public function halt($msg)
    {
        // prints warning message when there is an error
        return trigger_error($this->error . '(' . $this->errno . ')');
    }

    /**
     * @param int $time
     *
     * @return string
     */
    public function escape_date($time = null)
    {
        // @note date(,) in php<8 will treat null as 0
        return date('Y-m-d', $time ?? time());
    }

    /**
     * @param int $time
     *
     * @return string
     */
    public function escape_time($time = null)
    {
        // @note date(,) in php<8 will treat null as 0
        return date('H:i:s', $time ?? time());
    }

    /**
     * @param int $time
     *
     * @return string
     */
    public function from_unixtime($time = null)
    {
        return sprintf("%s %s", $this->escape_date($time), $this->escape_time($time));
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public function escape_string($str)
    {
        return $this->real_escape_string($str);
    }

    /**
     * @param mixed $var
     *
     * @return string
     */
    public function escape($var)
    {
        if (is_null($var)) {
            return 'NULL';
        } elseif (true === $var) {
            return 'TRUE';
        } elseif (false === $var) {
            return 'FALSE';
        } elseif (is_int($var)) {
            return sprintf('%d', $var);
        } elseif (is_float($var)) {
            return sprintf("'%s'", $var);
        } elseif (is_array($var)) {
            return sprintf("'%s'", $this->escape_string(implode(',', $var)));
        }
        return sprintf("'%s'", $this->escape_string($var));
    }

    /**
     * @param string $table
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
    public function table_columns($table)
    {
        if (array_key_exists($table, $this->column_cache)) {
            return $this->column_cache[$table];
        }

        $this->column_cache[$table] = [];
        if ($query = $this->query(sprintf(
            "SHOW COLUMNS FROM %s",
            $table
        ))) {
            //@note For legacy support, use MYSQLI_BOTH
            while ($row = $this->fetch_array($query, MYSQLI_BOTH)) {
                $this->column_cache[$table][$row['Field']] = $row;
            }
            $this->free($query);
        }

        return $this->column_cache[$table];
    }

    /**
     * @param string $table
     *
     * @return string[]
     */
    public function table_fields($table)
    {
        $result = [];
        foreach ($this->table_columns($table) as $k => $v) {
            $result[$k] = $v['Field'];
        }
        return $result;
    }

    /**
     * @param string $table
     *
     * @return string[]
     */
    public function table_types($table)
    {
        $result = [];
        foreach ($this->table_columns($table) as $k => $v) {
            $result[$k] = $v['Type'];
        }
        return $result;
    }

    /**
     * @param string $table
     *
     * @return string[]
     */
    public function table_nulls($table)
    {
        $result = [];
        foreach ($this->table_columns($table) as $k => $v) {
            $result[$k] = $v['Null'];
        }
        return $result;
    }

    /**
     * @param string $table
     *
     * @return string[]
     */
    public function table_keys($table)
    {
        $result = [];
        foreach ($this->table_columns($table) as $k => $v) {
            $result[$k] = $v['Key'];
        }
        return $result;
    }

    /**
     * @param string $table
     *
     * @return mixed[]
     */
    public function table_defaults($table)
    {
        $result = [];
        foreach ($this->table_columns($table) as $k => $v) {
            $result[$k] = $v['Default'];
        }
        return $result;
    }

    /**
     * @param string $table
     *
     * @return string[]
     */
    public function table_extras($table)
    {
        $result = [];
        foreach ($this->table_columns($table) as $k => $v) {
            $result[$k] = $v['Extra'];
        }
        return $result;
    }
}
