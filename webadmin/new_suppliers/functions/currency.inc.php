<?php
/***********************
 * Currency Functions
 * Filename: currency.inc.php
 ***********************
 * Purpose:
 *    Central repository for all functions related to money stuff.
 ***********************/

include_once __DIR__ . '/../../currency.inc.php'; // For ShowPriceFrom &c

function makeCurrencyArray()
{
    global $DB;

    static $result = null;
    if (!is_null($result)) {
        return $result;
    }

    $result = array();
    $query  = $DB->query(sprintf(
        "SELECT `code`, `name` FROM `highwaygroup`.`currency` ORDER BY `name` ASC"
    ));
    while ($row = $DB->fetch_assoc($query)) {
        $result[$row['code']] = $row;
    }
    $DB->free_result($query);

    return $result;
}

function makePayMethodList()
{
    global $DB;

    static $result = null;
    if (!is_null($result)) {
        return $result;
    }

    $result = array();
    $query  = $DB->query(sprintf(
        "SELECT `paymethod_id` AS `id`, `text` FROM `payment_methods` ORDER BY `code`"
    ));
    while ($row = $DB->fetch_assoc($query)) {
        $result[$row['id']] = $row;
    }
    $DB->free_result($query);

    return $result;
}
