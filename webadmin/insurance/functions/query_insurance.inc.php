<?php
if (!defined('INSURANCE_TABLENAME')) {
    define('INSURANCE_TABLENAME', '`highwaygroup`.`insurances`');
}
if (!defined('INSURANCE_PRIMARYKEY')) {
    define('INSURANCE_PRIMARYKEY', 'insurance_id');
}

if (!function_exists('insuranceDelete')) {
	/**
	 * @param int $id
	 *
	 * @return int
	 */
    function insuranceDelete($id)
    {
		/*
		if ($DB->query(sprintf(
			"DELETE FROM %s WHERE `%s` = %d LIMIT 1",
			INSURANCE_TABLENAME,
			INSURANCE_PRIMARYKEY,
			$id
		))) {
			$result = $DB->affected_rows();
			$result += insuranceRatesDelete($id);
			return $result;
		}
		*/

		return 0;
    }
}
if (!function_exists('insuranceGetOptionList')) {
	/**
	 * @param string $table_name
	 *
	 * @return mixed
	 */
    function insuranceGetOptionList($table_name)
    {
        global $DB;

        static $option_cache = array();
        if (isset($option_cache[$table_name])) {
            return $option_cache[$table_name];
        }

        $table       = '';
		$key         = '';
		$value       = '';
        $option_list = array();
        switch ($table_name) {
            case 'joined_fields':
                $joins = array('INNER JOIN highwaygroup.insurance_rates ir ON ir.insurance_id=i.insurance_id' => getTableColumns('highwaygroup.insurance_rates'));
                foreach (getTableColumns(INSURANCE_TABLENAME) as $field) {
                    foreach ($joins as $join => $rows) {
                        unset($joins[$join][$field]);
                    }
                }

                return $joins;
            case 'extra_fields':
            case 'ext_fields':
                return array();
        }
        if ('' != $table) {
            $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
            while ($row = $DB->fetch_array($query)) {
                $option_list[$row[0]] = $row[1];
            }

        }
        $option_cache[$table_name] = $option_list;
        return $option_list;
    }
}
if (!function_exists('insuranceGetValue')) {
	/**
	 * @param int    $id
	 * @param string $field
	 *
	 * @return mixed
	 */
    function insuranceGetValue($id, $field)
    {
        global $DB;

		$fields = GetTableFields(INSURANCE_TABLENAME);
        if (array_key_exists($field, $fields)) {
            if ($row = $DB->query_first(sprintf(
				"SELECT `%s` FROM %s WHERE `%s` = %d LIMIT 1",
				$field,
				INSURANCE_TABLENAME,
				INSURANCE_PRIMARYKEY,
				$id
			), MYSQLI_ASSOC)) {
				return $row[$field];
			}
        }

        return null;
    }
}
if (!function_exists('insuranceHasValue')) {
	/**
	 * @param int    $id
	 * @param string $field
	 * @param mixed  $value
	 *
	 * @return bool
	 */
    function insuranceHasValue($id, $field, $value)
    {
        return (insuranceGetValue($id, $field) == $value);
    }
}
if (!function_exists('insuranceSetValue')) {
	/**
	 * @param int    $id
	 * @param string $field
	 * @param mixed  $value
	 *
	 * @return int
	 */
    function insuranceSetValue($id, $field, $value)
    {
        global $DB;

		$fields = GetTableFields(INSURANCE_TABLENAME);
        if (array_key_exists($field, $fields)) {
            if ($DB->query(sprintf(
				"UPDATE %s SET `%s` = %s WHERE `%s` = %d LIMIT 1",
				INSURANCE_TABLENAME,
				$field,
				$DB->escape($value),
				INSURANCE_PRIMARYKEY,
				$id
			))) {
				return $DB->affected_rows();
			}
        }

        return 0;
    }
}
if (!function_exists('insuranceSumValue')) {
	/**
	 * @param int[]  $item_list
	 * @param string $field
	 *
	 * @return int|string|float|null
	 */
    function insuranceSumValue($item_list, $field)
    {
        global $DB;

		$fields = GetTableFields(INSURANCE_TABLENAME);
        if (array_key_exists($field, $fields)) {
            if ($row = $DB->query_first(sprintf(
				"SELECT SUM(`%s`) AS `sum_value` FROM %s WHERE `%s` IN(%s) LIMIT 1",
				$field,
				INSURANCE_TABLENAME,
				INSURANCE_PRIMARYKEY,
				implode(',', $item_list)
			), MYSQLI_ASSOC)) {
            	return $row['sum_value'];
			}
        }

		return null;
    }
}
if (!function_exists('insuranceExists')) {
	/**
	 * @param int $id
	 *
	 * @return bool
	 */
    function insuranceExists($id)
    {
        global $DB;

		return !!$DB->query_first(sprintf(
			"SELECT 1 FROM %s WHERE `%s` = %d LIMIT 1",
			INSURANCE_TABLENAME,
			INSURANCE_PRIMARYKEY,
			$id
		), MYSQLI_ASSOC);
    }
}
if (!function_exists('insuranceCalculateAmount')) {
	/**
	 * @param int   $id
	 * @param float $coverage_aud
	 *
	 * @return float
	 */
    function insuranceCalculateAmount($id, $coverage_aud)
    {
        $insurance  = insuranceFromDatabase($id);
        $result_aud = 0.0;
        switch ($insurance['pricing_type']) {
            case 'brackets':
                foreach (insuranceRatesFromDatabase($id) as $rate) {
                    if ($coverage_aud > $rate['rate_start']) {
                        $result_aud += (min($coverage_aud, $rate['rate_finish']) - $rate['rate_start']) * $rate['rate_rate'] / 100.0;
                    }
                }

                break;

            default:
            case 'fixed':
                $result_aud = $coverage_aud * $insurance['customer_charge'] / 100.0;
                break;
        }
        return $result_aud;
    }
}
if (!function_exists('insuranceChunk')) {
	/**
	 * @param int[]           $insurance_list
	 * @param string[]|string $group_by
	 *
	 * @return mixed[]
	 */
    function insuranceChunk($insurance_list, $group_by = '')
    {
        global $DB;
        if (!is_array($insurance_list) || count($insurance_list) < 1) {
            return $insurance_list;
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $fields = getTableColumns(INSURANCE_TABLENAME);
        foreach ($fields as $key => $value) {
            $fields[$key] = 'i.`' . $value . '`';
        }

        foreach (insuranceGetOptionList('ext_fields') as $key => $value) {
            $fields[$key] = $value . ' AS ' . $key;
        }

        $field_list = array('insurance_id');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[] = $fields[$field];
            }
        }

        $field_list = array_unique($field_list);
        $sql        = "SELECT " . implode(', ', $field_list) . " FROM " . INSURANCE_TABLENAME . " AS `i` WHERE `i`.`insurance_id` IN(" . implode(', ', $insurance_list) . ")";
        $query      = $DB->query($sql);
        $results    = array();
        while ($row = $DB->fetch_assoc($query)) {
            $pointer = &$results;
            foreach ($group_by as $group) {
                $key = $row[$group];
                if ('' != $key) {
                    if (!array_key_exists($key, $pointer)) {
                        $pointer[$key] = array();
                    }

                    $pointer = &$pointer[$key];
                }
            }
            $pointer[] = $row['insurance_id'];
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('insuranceSearch')) {
	/**
	 * @param mixed[]|string $criteria
	 *
	 * @return int[]
	 */
    function insuranceSearch($criteria = '')
    {
        global $DB;

		$results = array();
        $sql     = "SELECT `i`.`insurance_id` FROM " . INSURANCE_TABLENAME . " AS `i`";
        $fields  = getTableColumns(INSURANCE_TABLENAME);
        foreach ($fields as $key => $value) {
            $fields[$key] = 'i.`' . $value . '`';
        }

        $fields   = array_merge($fields, insuranceGetOptionList('ext_fields'));
        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = $fields[$field] . " = " . $DB->escape($sub_value);
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = $fields[$field] . " = " . $DB->escape($value);
                    }

                    unset($fields[$field]);
                }
            }
		} elseif (is_string($criteria) && '' != $criteria) {
			$whereAND[] = "`i`.`insurance_status` = " . $DB->escape($criteria);
        }

        $search_for = array();
        if (is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('i.insurance_id');
            $whereOR    = array();
            foreach ($search_for as $findme) {
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                    }
                }
            }
            if (count($whereOR) > 0) {
                $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
            }
        }

        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " ORDER BY `i`.`insurance_id` ASC";
        if ($query = $DB->query($sql)) {
			while ($row = $DB->fetch_assoc($query)) {
				$results[] = $row['insurance_id'];
			}
			$DB->free_result($query);
		}

        return $results;
    }
}
if (!function_exists('insuranceSort')) {
	/**
	 * @param int[]  $insurance_list
	 * @param string $order_by
	 *
	 * @return int[]
	 */
    function insuranceSort($insurance_list, $order_by = 'insurance_id')
    {
        global $DB;
        if (!is_array($insurance_list) || count($insurance_list) < 2) {
            return $insurance_list;
        }

        $fields = GetTableFields(INSURANCE_TABLENAME);
        if (!isset($fields[$order_by])) {
            return $insurance_list;
        }

        $sql   = "SELECT `i`.`insurance_id` FROM " . INSURANCE_TABLENAME . " AS `i`";
        $where = " WHERE i.insurance_id IN(" . implode(',', $insurance_list) . ")";
		$order = "";
        if (isset($fields[$order_by])) {
            $order = " ORDER BY " . $order_by . " ASC";
        }

        $insurance_list = array();
        if ($query = $DB->query($sql . $where . $order)) {
			while ($row = $DB->fetch_assoc($query)) {
				$insurance_list[] = $row['insurance_id'];
			}
			$DB->free_result($query);
		}

        return $insurance_list;
    }
}
if (!function_exists('insuranceFromPost')) {
	/**
	 * @param mixed[] $posted
	 *
	 * @return mixed[]
	 */
    function insuranceFromPost($posted)
    {
        global $DB;
        $insurance_id = $posted['insurance_id'];
        $old_vals     = insuranceFromDatabase($insurance_id);
        $new_vals     = $old_vals;
        foreach ($old_vals as $key => $value) {
            if (isset($posted['insurance'][$key])) {
                $new_vals[$key] = $posted['insurance'][$key];
            }
        }

        if ($insurance_id <= 0) {
            $new_vals['open_time']     = $_SERVER['REQUEST_TIME'];
            $new_vals['open_date']     = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $new_vals['proforma_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $new_vals['days_open']     = 0;
            $new_vals['time_open']     = 0;
            unset($new_vals['insurance_id']);
        }
        return $new_vals;
    }
}
if (!function_exists('insuranceTemplateVars')) {
	/**
	 * @param mixed[] $insurance
	 *
	 * @return string[]
	 */
    function insuranceTemplateVars($insurance = array())
    {
        global $DB;
        $insurance_id = intval($insurance['insurance_id']);
        $results      = array();
        foreach (getTableColumns(INSURANCE_TABLENAME) as $key) {
            $results['[' . strtoupper($key) . ']'] = '';
        }

        $results['[REQUEST_DATE]'] = date('jS F, Y', $_SERVER['REQUEST_TIME']);
        $results['[REQUEST_TIME]'] = date('g:i a', $_SERVER['REQUEST_TIME']);
        foreach ($insurance as $key => $value) {
            $results['[' . strtoupper($key) . ']'] = $value;
        }

        $results['[INVOICE_NUMBER]'] = insuranceFormatNumber($insurance_id);
        foreach ($insurance as $key => $value) {
            $option_list = insuranceGetOptionList($key);
            if (count($option_list) > 0) {
                $results['[' . strtoupper($key) . ']'] = $option_list[$value];
            }

        }
        foreach ($results as $key => $value) {
            $results[$key] = str_replace("\r", '', $value);
        }

        return $results;
    }
}
if (!function_exists('insuranceFromDatabase')) {
	/**
	 * @param int $insurance_id
	 *
	 * @return mixed[]
	 */
    function insuranceFromDatabase($insurance_id)
    {
        global $DB;
        $insurance_id = intval($insurance_id);
        $sql          = sprintf(
            "SELECT * FROM " . INSURANCE_TABLENAME . " WHERE `insurance_id` = %d LIMIT 1",
            $insurance_id
        );
        $row = $DB->query_first($sql, MYSQLI_ASSOC);
        if ($row) {
            $summary = $row;
        } else {
            $fields  = GetTableFields(INSURANCE_TABLENAME);
            $summary = array();
            foreach ($fields as $key => $row) {
                $summary[$key] = $row[4];
            }

        }
        return $summary;
    }
}
if (!function_exists('insuranceToDatabase')) {
	/**
	 * @param mixed[] $summary
	 *
	 * @return int
	 */
    function insuranceToDatabase($summary)
    {
        global $DB;
        $insurance_id  = intval($summary['insurance_id']);
        $old_insurance = insuranceFromDatabase($insurance_id);
        foreach ($old_insurance as $key => $value) {
            if ($summary[$key] == $value) {
                unset($summary[$key]);
            }
        }

        foreach ($summary as $key => $value) {
            if (!array_key_exists($key, $old_insurance)) {
                unset($summary[$key]);
            }
        }

        $fields = GetTableFields(INSURANCE_TABLENAME);
        if (count($summary) > 0) {
            if (insuranceExists($insurance_id)) {
                $set_vals = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = "`" . $key . "` = " . $DB->escape($value);
                        } else {
                            $set_vals[$key] = "`" . $key . "`=NULL";
                        }
                    }
                }

                $sql = "UPDATE " . INSURANCE_TABLENAME . " SET " . implode(', ', $set_vals) . " WHERE insurance_id=" . $insurance_id . " LIMIT 1";
                $DB->query($sql);
            } else {
                $set_vals = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = $DB->escape($value);
                        } else {
                            $set_vals[$key] = 'NULL';
                        }
                    }
                }

                $sql          = "INSERT INTO " . INSURANCE_TABLENAME . " (`" . implode('`, `', array_keys($set_vals)) . "`) VALUES (" . implode(", ", $set_vals) . ")";
                $query        = $DB->query($sql);
                $insurance_id = $DB->insert_id();
            }
        }
        return $insurance_id;
    }
}
if (!function_exists('insuranceRatesFromPost')) {
	/**
	 * @param mixed[] $posted
	 *
	 * @return mixed[][]
	 */
    function insuranceRatesFromPost($posted)
    {
        global $DB;
        $rates  = array();
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM highwaygroup.insurance_rates");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            } else {
                $auto = $row[0];
            }
        }

        $insurance_id = intval($posted['insurance_id']);
        if (insuranceExists($insurance_id)) {
            $rates = insuranceRatesFromDatabase($insurance_id);
        }

        if (isset($posted['rates_affected'])) {
            foreach (explode(',', $posted['rates_affected']) as $key1) {
                foreach ($rates as $key2 => $rate) {
                    if ($rate['rate_count'] == $key1) {
                        unset($rates[$key2]);
                    }
                }
            }
        }

        if (isset($posted['rate_count'])) {
            foreach ($posted['rate_count'] as $key1) {
                foreach ($rates as $key2 => $rate) {
                    if ($rate['rate_count'] == $key1) {
                        unset($rates[$key2]);
                    }
                }
            }
        }

        if (isset($posted['insurance_rate'])) {
            foreach ($posted['insurance_rate'] as $key1 => $value1) {
                $rate['insurance_id'] = $posted['insurance_id'];
                foreach ($fields as $key2 => $value) {
                    if (isset($value1[$key2])) {
                        $rate[$key2] = $value1[$key2];
                    }
                }

                $rates[] = $rate;
            }
        }
        return $rates;
    }
}
if (!function_exists('insuranceRatesFromDatabase')) {
	/**
	 * @param int $insurance_id
	 *
	 * @return mixed[][]
	 */
    function insuranceRatesFromDatabase($insurance_id)
    {
        global $DB;
        $insurance_id = intval($insurance_id);
        $sql          = "SELECT ir.* FROM highwaygroup.insurance_rates ir WHERE ir.insurance_id=" . $insurance_id . " ORDER BY rate_start ASC";
        $query        = $DB->query($sql);
        $result       = array();
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('insuranceRatesToDatabase')) {
	/**
	 * @param int       $insurance_id
	 * @param mixed[][] $rates
	 *
	 * @return void
	 */
    function insuranceRatesToDatabase($insurance_id, $rates)
    {
        global $DB;
        $insurance_id = intval($insurance_id);
        if (0 == $insurance_id) {
            return;
        }

        $sql = "DELETE FROM highwaygroup.insurance_rates WHERE insurance_id=" . $insurance_id;
        $DB->query($sql);
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM highwaygroup.insurance_rates");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        $re_sort = array();
        foreach ($rates as $key => $rate) {
            $re_sort[$key] = $rate['rate_rank'];
        }

        asort($re_sort);
        $counter = 1;
        foreach ($re_sort as $key => $rank) {
            $rates[$key]['rate_rank'] = $counter++;
        }

        foreach ($rates as $rate) {
            $insert_vals = array();
            if ($insurance_id > 0) {
                $rate['insurance_id'] = $insurance_id;
            }

            foreach ($rate as $key => $value) {
                if (isset($fields[$key])) {
                    $insert_vals[$key] = $DB->escape($value);
                }
            }

            $sql = "INSERT INTO highwaygroup.insurance_rates(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")";
            $DB->query($sql);
        }
    }
}
if (!function_exists('insuranceContractFromPost')) {
	/**
	 * @param mixed[] $posted
	 *
	 * @return mixed[]
	 */
    function insuranceContractFromPost($posted)
    {
        global $DB;
        $contract = array();
        $fields   = array();
        $query    = $DB->query("SHOW COLUMNS FROM highwaygroup.insurance_contracts");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            } else {
                $auto = $row[0];
            }
        }

        $contract['staff_id']     = staffFromUser($_SERVER['REMOTE_USER']);
        $contract['insurance_id'] = intval($posted['insurance_id']);
        foreach ($fields as $key => $value) {
            if (isset($posted['insurance_contract'][$key])) {
                $contract[$key] = $posted['insurance_contract'][$key];
            }
        }

        if ('' != $posted['contract_date_from']) {
            $time                               = explode('-', $posted['contract_date_from']);
            $time                               = mktime(0, 0, 0, (int)$time[1], (int)$time[2], (int)$time[0]);
            $contract['contract_commence_time'] = $time;
            $contract['contract_commence_date'] = substr(formatSQLDateTime($time), 1, -1);
        }
        if ('' != $posted['contract_date_till']) {
            $time                               = explode('-', $posted['contract_date_till']);
            $time                               = mktime(0, 0, 0, (int)$time[1], (int)$time[2], (int)$time[0]);
            $contract['contract_complete_time'] = $time;
            $contract['contract_complete_date'] = substr(formatSQLDateTime($time), 1, -1);
        }
        return $contract;
    }
}
if (!function_exists('insuranceContractsFromDatabase')) {
	/**
	 * @param int $insurance_id
	 *
	 * @return mixed[][]
	 */
    function insuranceContractsFromDatabase($insurance_id)
    {
        global $DB;
        $insurance_id = intval($insurance_id);
        $sql          = "SELECT ic.*, UNIX_TIMESTAMP(contract_commence_date) AS contract_commence_time, UNIX_TIMESTAMP(contract_complete_date) AS contract_complete_time FROM highwaygroup.insurance_contracts ic WHERE ic.insurance_id=" . $insurance_id . " ORDER BY ic.contract_commence_date ASC";
        $query        = $DB->query($sql);
        $result       = array();
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('insuranceContractFromDatabase')) {
	/**
	 * @param int $contract_id
	 *
	 * @return mixed[]|null
	 */
    function insuranceContractFromDatabase($contract_id)
    {
        global $DB;
        $contract_id = intval($contract_id);
        $sql         = "SELECT ic.*, UNIX_TIMESTAMP(contract_commence_date) AS contract_commence_time, UNIX_TIMESTAMP(contract_complete_date) AS contract_complete_time FROM highwaygroup.insurance_contracts ic WHERE ic.contract_id=" . $contract_id;
        $result      = $DB->query_first($sql, MYSQLI_ASSOC);
        return $result;
    }
}
if (!function_exists('insuranceContractSearch')) {
	/**
	 * @param mixed $criteria
	 * @param int   $time_from
	 * @param int   $time_till
	 *
	 * @return int[]
	 */
    function insuranceContractSearch($criteria = '', $time_from = 0, $time_till = 0)
    {
        global $DB;
        $results = array();
        $sql     = "SELECT `ic`.`contract_id` FROM `highwaygroup`.`insurance_contracts` AS `ic`";
        $fields  = getTableColumns('highwaygroup.insurance_contracts');
        foreach ($fields as $key => $value) {
            $fields[$key] = 'ic.`' . $value . '`';
        }

        //$fields=array_merge($fields,insuranceGetOptionList('ext_fields'));
        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = $fields[$field] . " = " . $DB->escape($sub_value);
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = $fields[$field] . " = " . $DB->escape($value);
                    }

                    unset($fields[$field]);
                }
            }
		} elseif (is_string($criteria) && '' != $criteria) {
			$whereAND[] = "`ic`.`insurance_status` = " . $DB->escape($criteria);
        }

        $search_for = array();
        if (is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('ic.contract_id');
            $whereOR    = array();
            foreach ($search_for as $findme) {
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                    }
                }

            }
            if (count($whereOR) > 0) {
                $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
            }

        }
        if (0 != $time_from) {
            $whereAND[] = 'ic.contract_complete_date>' . formatSQLDateTime($time_from);
        }

        if (0 != $time_till) {
            $whereAND[] = 'ic.contract_commence_date<' . formatSQLDateTime($time_till);
        }

        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " ORDER BY ic.contract_id ASC";
        $query = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['contract_id'];
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('insuranceContractToDatabase')) {
	/**
	 * @param int     $insurance_id
	 * @param mixed[] $contract
	 *
	 * @return int
	 */
    function insuranceContractToDatabase($insurance_id, $contract)
    {
        global $DB;
        $insurance_id = intval($insurance_id);
        if (0 == $insurance_id) {
            return;
        }

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM highwaygroup.insurance_contracts");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        $insert_vals = array();
        if ($insurance_id > 0) {
            $contract['insurance_id'] = $insurance_id;
        }

        foreach ($contract as $key => $value) {
            if (isset($fields[$key])) {
                $insert_vals[$key] = $DB->escape($value);
            }
        }

        $sql = "INSERT INTO highwaygroup.insurance_contracts(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")";
        $DB->query($sql);
        return $DB->insert_id();
    }
}
if (!function_exists('insuranceClaimCommentToDatabase')) {
	/**
	 * @param int            $claim_id
	 * @param mixed[]|string $comment
	 *
	 * @return int|false
	 */
    function insuranceClaimCommentToDatabase($claim_id, $comment)
    {
        global $DB;

		if (is_string($comment)) {
            $comment = array(
				'claim_id'     => $claim_id,
				'comment_text' => $comment,
				'comment_time' => $_SERVER['REQUEST_TIME'],
				'comment_date' => substr(formatSQLDateTime($_SERVER['REQUEST_TIME']), 1, -1),
				'staff_id'     => staffFromUser($_SERVER['REMOTE_USER']),
			);
        }

        if ($claim_id > 0) {
            $comment['claim_id'] = $claim_id;
        }

        if (0 == $comment['claim_id']) {
            return false;
        }

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM highwaygroup.insurance_claims_comments");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        $insert_vals = array();
        foreach ($comment as $key => $value) {
            if (isset($fields[$key])) {
                $insert_vals[$key] = $DB->escape($value);
            }
        }

        $sql = "INSERT INTO highwaygroup.insurance_claims_comments(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")";
        $DB->query($sql);
        return $DB->insert_id();
    }
}
if (!function_exists('insuranceClaimCommentsFromDatabase')) {
	/**
	 * @param int $claim_id
	 *
	 * @return mixed[][]
	 */
    function insuranceClaimCommentsFromDatabase($claim_id)
    {
        global $DB;
        $claim_id = intval($claim_id);
        $sql      = "SELECT icc.*, UNIX_TIMESTAMP(icc.comment_date) AS comment_time FROM highwaygroup.insurance_claims_comments icc WHERE icc.claim_id=" . $claim_id . " ORDER BY icc.comment_date ASC";
        $query    = $DB->query($sql);
        $result   = array();
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('insuranceClaimExists')) {
	/**
	 * @param int $claim_id
	 *
	 * @return bool
	 */
    function insuranceClaimExists($claim_id)
    {
        global $DB;
        $claim_id = intval($claim_id);
        $sql      = "SELECT ic.claim_id FROM highwaygroup.insurance_claims ic WHERE ic.claim_id=" . $claim_id . " LIMIT 1";
        $row      = $DB->query_first($sql, MYSQLI_ASSOC);
        if ($row) {
            return true;
        }

        return false;
    }
}
if (!function_exists('insuranceClaimFromDatabase')) {
	/**
	 * @param int $claim_id
	 *
	 * @return mixed[]
	 */
    function insuranceClaimFromDatabase($claim_id)
    {
        global $DB;
        $claim_id = intval($claim_id);
        $sql      = sprintf(
            "SELECT `ic`.*, UNIX_TIMESTAMP(`ic`.`claim_date`) AS `claim_time` FROM `highwaygroup`.`insurance_claims` AS `ic` WHERE `ic`.`claim_id` = %d LIMIT 1",
            $claim_id
        );
        $row = $DB->query_first($sql, MYSQLI_ASSOC);
        if ($row) {
            $summary = $row;
        } else {
            $fields  = GetTableFields("highwaygroup.insurance_claims");
            $summary = array();
            foreach ($fields as $key => $row) {
                $summary[$key] = $row[4];
            }

        }
        return $summary;
    }
}
if (!function_exists('insuranceClaimFromPost')) {
	/**
	 * @param mixed[] $posted
	 *
	 * @return mixed[]
	 */
    function insuranceClaimFromPost($posted)
    {
        global $DB;
        $claim_id = $posted['insurance_claim']['claim_id'];
        $old_vals = insuranceClaimFromDatabase($claim_id);
        $new_vals = $old_vals;
        foreach ($old_vals as $key => $value) {
            if (isset($posted['insurance_claim'][$key])) {
                $new_vals[$key] = $posted['insurance_claim'][$key];
            }
        }

        if ($claim_id <= 0) {
            $new_vals['claim_time'] = $_SERVER['REQUEST_TIME'];
            $new_vals['claim_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            unset($new_vals['claim_id']);
        }
        return $new_vals;
    }
}
if (!function_exists('insuranceClaimSearch')) {
	/**
	 * @param mixed $criteria
	 * @param int   $time_from
	 * @param int   $time_till
	 *
	 * @return int[]
	 */
    function insuranceClaimSearch($criteria = '', $time_from = 0, $time_till = 0)
    {
        global $DB;
        $results = array();
        $sql     = "SELECT `ic`.`claim_id` FROM `highwaygroup`.`insurance_claims` AS `ic`";
        $fields  = getTableColumns('highwaygroup.insurance_claims');
        foreach ($fields as $key => $value) {
            $fields[$key] = 'ic.`' . $value . '`';
        }

        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = $fields[$field] . " = " . $DB->escape($sub_value);
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = $fields[$field] . " = " . $DB->escape($value);
                    }

                    unset($fields[$field]);
                }
            }
		} elseif (is_string($criteria) && '' != $criteria) {
			$whereAND[] = "`ic`.`insurance_status` = " . $DB->escape($criteria);
		}

        $search_for = array();
        if (is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('ic.claim_id');
            $whereOR    = array();
            foreach ($search_for as $findme) {
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                    }
                }

            }
            if (count($whereOR) > 0) {
                $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
            }

        }
        if (0 != $time_from) {
            $whereAND[] = 'ic.claim_date>' . formatSQLDateTime($time_from);
        }

        if (0 != $time_till) {
            $whereAND[] = 'ic.claim_date<' . formatSQLDateTime($time_till);
        }

        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " ORDER BY ic.claim_id ASC";
        $query = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['claim_id'];
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('insuranceClaimSort')) {
	/**
	 * @param int[]  $claim_list
	 * @param string $order_by
	 *
	 * @return int[]
	 */
    function insuranceClaimSort($claim_list, $order_by = 'claim_id')
    {
        global $DB;
        if (!is_array($claim_list) || count($claim_list) < 2) {
            return $claim_list;
        }

        $fields = GetTableFields('highwaygroup.insurance_claims');
        if (!isset($fields[$order_by])) {
            return $claim_list;
        }

        $sql   = "SELECT `ic`.`claim_id` FROM `highwaygroup`.`insurance_claims` AS `ic`";
        $where = " WHERE ic.claim_id IN(" . implode(',', $claim_list) . ")";
		$order = "";
        if (isset($fields[$order_by])) {
            $order = " ORDER BY " . $order_by . " ASC";
        }

        $claim_list = array();
        if ($query = $DB->query($sql . $where . $order)) {
			while ($row = $DB->fetch_assoc($query)) {
				$claim_list[] = $row['claim_id'];
			}
			$DB->free_result($query);
		}

        return $claim_list;
    }
}
if (!function_exists('insuranceClaimToDatabase')) {
	/**
	 * @param mixed[] $summary
	 *
	 * @return int
	 */
    function insuranceClaimToDatabase($summary)
    {
        global $DB;
        $claim_id            = intval($summary['claim_id']);
        $old_insurance_claim = insuranceClaimFromDatabase($claim_id);
        foreach ($old_insurance_claim as $key => $value) {
            if ($summary[$key] == $value) {
                unset($summary[$key]);
            }
        }

        foreach ($summary as $key => $value) {
            if (!array_key_exists($key, $old_insurance_claim)) {
                unset($summary[$key]);
            }
        }

        $fields = GetTableFields("highwaygroup.insurance_claims");
        if (count($summary) > 0) {
            if (insuranceClaimExists($claim_id)) {
                $set_vals = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = "`" . $key . "` = " . $DB->escape($value);
                        } else {
                            $set_vals[$key] = "`" . $key . "`=NULL";
                        }
                    }
                }

                $sql = "UPDATE highwaygroup.insurance_claims SET " . implode(', ', $set_vals) . " WHERE claim_id=" . $claim_id . " LIMIT 1";
                $DB->query($sql);
            } else {
                $set_vals = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = $DB->escape($value);
                        } else {
                            $set_vals[$key] = 'NULL';
                        }
                    }
                }

                $sql      = "INSERT INTO highwaygroup.insurance_claims(`" . implode('`, `', array_keys($set_vals)) . "`) VALUES(" . implode(", ", $set_vals) . ")";
                $query    = $DB->query($sql);
                $claim_id = $DB->insert_id();
            }
        }
        return $claim_id;
    }
}
if (!function_exists('insuranceGetLock')) {
	/**
	 * @param int $insurance_id
	 *
	 * @return mixed[]|false
	 */
    function insuranceGetLock($insurance_id)
    {
        global $DB;
        $row = $DB->query_first("SELECT *, UNIX_TIMESTAMP(expiry_date) AS expiry_time FROM highwaygroup.insurance_locks WHERE insurance_id=" . intval($insurance_id) . " LIMIT 1", MYSQLI_ASSOC);
        if ($row['expiry_time'] < $_SERVER['REQUEST_TIME']) {
            $row = false;
        }

        return $row;
    }
}
if (!function_exists('insuranceSetLock')) {
	/**
	 * @param int        $insurance_id
	 * @param int|string $staff_id
	 * @param int        $timeout
	 *
	 * @return void
	 */
    function insuranceSetLock($insurance_id, $staff_id = '', $timeout = 0)
    {
        global $DB;
        $DB->query("DELETE FROM highwaygroup.insurance_locks WHERE insurance_id=" . intval($insurance_id));
        if (0 == $timeout) {
            $timeout = 300;
        }

        if ('' != $staff_id) {
            $DB->query("INSERT INTO `highwaygroup`.`insurance_locks` (`insurance_id`, `staff_id`, `expiry_date`) VALUES (" . intval($insurance_id) . ", " . $DB->escape($staff_id) . ", " . formatSQLDateTime($_SERVER['REQUEST_TIME'] + $timeout) . ")");
        }
    }
}
if (!function_exists('insuranceFormatNumber')) {
	/**
	 * @param int $insurance_id
	 * @param int $revision_id
	 * @param int $site_id
	 *
	 * @return string
	 */
    function insuranceFormatNumber($insurance_id, $revision_id = 0, $site_id = 0)
    {
        return "";
    }
}
if (!function_exists('insuranceEmailTemplatesList')) {
	/**
	 * @return mixed[][]
	 */
    function insuranceEmailTemplatesList()
    {
        global $DB;
        $results = array();
        $query   = $DB->query("SELECT template_id, template_name, template_subject, template_text FROM highwaygroup.email_templates WHERE FIND_IN_SET('insurance',template_group)");
        while ($row = $DB->fetch_assoc($query)) {
            $results[$row['template_id']] = $row;
        }

        return $results;
    }
}
