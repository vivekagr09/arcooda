<?php
/***********************
 * Country Functions
 * Filename: country_functions.inc.php
 ***********************
 * Purpose:
 *    Central repository for all functions related to country stuff.
 ***********************/

function makeCountryArray()
{
    global $DB;

    static $result;
    if (!is_null($result)) {
        return $result;
    }

    $result = [];
    if ($query  = $DB->query(sprintf(
        "SELECT `code`, `name` FROM `world`.`country` ORDER BY `name` ASC"
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row['code']] = $row;
        }
        $DB->free_result($query);
    }

    return $result;
}
