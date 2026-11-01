<?php
include_once __DIR__ . '/common.inc.php';

$statement = "DELETE FROM new_session_locks WHERE UNIX_TIMESTAMP(last_access) + timeout < UNIX_TIMESTAMP(NOW())";
$DB->query($statement);

if (!function_exists('tokenizePrimaryKeys')) {
    function tokenizePrimaryKeys($keyValArr)
    {
        $str = "";
        foreach ($keyValArr as $key => $value) {
            if ("" != $str) {
                $str .= ":";
            }

            $str .= "$key=$value";
        }
        return $str;
    }
}

// determines if the given table and primary keys are locked by the current user, returning true or false
if (!function_exists('my_lock')) {
    function my_lock($table, $primaryKeys)
    {
        global $DB, $session_key;

        $keyStr = tokenizePrimaryKeys($primaryKeys);

        $sql    = "SELECT * FROM new_session_locks WHERE mod_table='$table' AND mod_keys='$keyStr' AND session_key='$session_key'";
        $result = $DB->query($sql);

        if ($DB->num_rows($result) > 0) {
            return true;
        }

        return false;
    }
}

// determines if the given table and primary keys are locked by anyone but current user, returning true or false
if (!function_exists('locked')) {
    function locked($table, $primaryKeys, $msg = false)
    {
        global $DB, $session_key;

        $keyStr = tokenizePrimaryKeys($primaryKeys);

        $sql    = "SELECT * FROM new_session_locks WHERE mod_table='$table' AND mod_keys='$keyStr' AND session_key!='$session_key'";
        $result = $DB->query($sql);

        if ($DB->num_rows($result) > 0) {
            if ($msg) {
                locked_msg($table, $primaryKeys);
            }

            return true;
        }
        return false;
    }
}

// locks the given table and primary keys with the current users session key
if (!function_exists('lock')) {
    function lock($table, $primaryKeys)
    {
        global $DB, $session_key;

        if (locked($table, $primaryKeys)) {
            return;
        }

        if ('' != $table && count($primaryKeys) > 0) {
            unlock($table, $primaryKeys);
            $keyStr    = tokenizePrimaryKeys($primaryKeys);
            $user      = get_user();
            $statement = "INSERT INTO new_session_locks (session_key, user, mod_table, mod_keys, last_access)
						VALUES ('$session_key', '$user', '$table', '$keyStr', NOW())";
            //echo $statement;
            $DB->query($statement);
            //echo "$table - LOCKED.<br>";
        }
    }
}

// unlocks the given table with the current users session key
if (!function_exists('unlock')) {
    function unlock($table, $primaryKeys)
    {
        global $DB, $session_key;

        if ('' != $table && count($primaryKeys) > 0) {
            $keyStr    = tokenizePrimaryKeys($primaryKeys);
            $statement = "DELETE FROM new_session_locks WHERE session_key = '$session_key' AND mod_table = '$table' AND mod_keys = '$keyStr'";
            //echo $statement;
            $DB->query($statement);
            //echo "$table - UNLOCKED.<br>";
        }
    }
}

// returns a string containing the time left until the lock is removed
if (!function_exists('time_till_free')) {
    function time_till_free($table, $primaryKeys)
    {
        global $DB, $session_key;

        $keyStr    = tokenizePrimaryKeys($primaryKeys);
        $statement = "SELECT UNIX_TIMESTAMP(last_access)+timeout - UNIX_TIMESTAMP(NOW()) as time_left FROM new_session_locks
				WHERE mod_table = '$table' AND mod_keys = '$keyStr' AND session_key != '$session_key' ORDER BY last_access DESC";
        $time = $DB->query_first($statement);

        $time_left = $time['time_left'];
        $time_str  = '';

        if ($time_left > 3600) {
            $time_str .= intval($time_left / 3600) . " Hours ";
        }

        if ($time_left > 60) {
            $time_str .= intval(($time_left % 3600) / 60) . " Minutes ";
        }

        $time_str .= ($time_left % 60) . " Seconds";

        return $time_str;
    }
}

// return the user who has the lock on the given table and row
if (!function_exists('get_locking_user')) {
    function get_locking_user($table, $primaryKeys)
    {
        global $DB, $session_key;

        $keyStr    = tokenizePrimaryKeys($primaryKeys);
        $statement = "SELECT user FROM new_session_locks WHERE mod_table = '$table' AND mod_keys = '$keyStr'
					 AND session_key != '$session_key' ORDER BY last_access DESC";
        $result = $DB->query_first($statement);
        return ucwords($result['user']);
    }
}

if (!function_exists('locked_msg')) {
    function locked_msg($table, $primaryKeys)
    {
?>
	<table width="90%" border="0" cellspacing="0" cellpadding="1" align="center">
	  <tr>
		<td bgcolor="#000000"><table width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
			  <td bgcolor="pink"><div align="center"><strong>This record is currently
				  accessed by <?=get_locking_user($table, $primaryKeys)?>, please wait for <?=time_till_free($table, $primaryKeys)?> and try again.</strong></div></td>
			</tr>
		  </table></td>
	  </tr>
	</table><br><br>
<?php
    }
}

if (!function_exists('show_locked_msg')) {
    function show_locked_msg($table, $primaryKeys)
    {
        if (locked($table, $primaryKeys)) {
            locked_msg($table, $primaryKeys);
        }

    }
}