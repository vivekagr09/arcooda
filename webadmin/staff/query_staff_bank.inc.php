<?php
if (!defined('STAFF_BANK_TABLENAME')) {
    define('STAFF_BANK_TABLENAME', '`highwaygroup`.`users_bankaccount`');
}
if (!defined('STAFF_BANK_PRIMARYKEY')) {
    define('STAFF_BANK_PRIMARYKEY', 'bank_id');
}
if (!defined('STAFF_BANK_SECONDARYKEY')) {
    define('STAFF_BANK_SECONDARYKEY', 'staff_id');
}

if (!function_exists('staffBankFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function staffBankFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            STAFF_BANK_TABLENAME,
            STAFF_BANK_PRIMARYKEY
        )) {
            return $row;
        }

        return null;
    }
}
if (!function_exists('staffBankSearch')) {
    /**
     * @param mixed $criteria
     *
     * @return int[]
     */
    function staffBankSearch($criteria)
    {
        global $DB;

        $fields   = $DB->table_columns(STAFF_BANK_TABLENAME);

        $whereAND = [];
        if (is_array($criteria)) {
            foreach ($criteria as $k => $v) {
                if (array_key_exists($k, $fields)) {
                    if (is_array($v)) {
                        if (count($v) > 0) {
                            $whereOR = [];
                            foreach ($v as $sv) {
                                $whereOR[] = "`" . $k . "` = " . $DB->escape($sv);
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return [];
                        }
                    } else {
                        $whereAND[] = "`" . $k . "` = " . $DB->escape($v);
                    }
                }
            }
        } elseif (0 != $criteria) {
            $whereAND[] = "`" . STAFF_BANK_SECONDARYKEY . "` = " . intval($criteria);
        }

        $result = [];
        if ($query  = $DB->query(sprintf(
            "SELECT `%s` FROM %s%s ORDER BY `bank_active` DESC",
            STAFF_BANK_PRIMARYKEY,
            STAFF_BANK_TABLENAME,
            (count($whereAND) > 0) ? (" WHERE " . implode(" AND ", $whereAND)) : ""
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row[STAFF_BANK_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('staffBankToDatabase')) {
    /**
     * @param int     $id
     * @param mixed[] $bank
     *
     * @return int
     */
    function staffBankToDatabase($id, $bank)
    {
        global $DB;

        if (!array_key_exists(STAFF_BANK_PRIMARYKEY, $bank) || 0 == $bank[STAFF_BANK_PRIMARYKEY]) {
            $DB->query(sprintf(
                "INSERT INTO %s (`%s`, `bank_name`, `bank_bank`, `bank_bsb`, `bank_account`) VALUES (%d, %s, %s, %s, %s)",
                STAFF_BANK_TABLENAME,
                STAFF_BANK_SECONDARYKEY,
                $id,
                $DB->escape($bank["bank_name"]),
                $DB->escape($bank["bank_bank"]),
                $DB->escape($bank["bank_bsb"]),
                $DB->escape($bank["bank_account"])
            ));
            $bank[STAFF_BANK_PRIMARYKEY] = $DB->insert_id();
        }
        if ('n' != $bank['bank_active']) {
            $DB->query(sprintf(
                "UPDATE %s SET `bank_active` = IF(`%s` = %d, 'y', 'n') WHERE `%s` = %d",
                STAFF_BANK_TABLENAME,
                STAFF_BANK_PRIMARYKEY,
                $bank[STAFF_BANK_PRIMARYKEY],
                STAFF_BANK_SECONDARYKEY,
                $id
            ));
        }

        return $bank[STAFF_BANK_PRIMARYKEY];
    }
}
