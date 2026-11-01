<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

global $_GLOBALS;
global $DB;

moduleOverride('shopping_cart', __DIR__ . '/query.inc.php');
moduleLoad('shopping_cart');
moduleLoad('product');
moduleLoad("member");

$all_staff_list = array();
if ($query = $DB->query(sprintf(
    "SELECT `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name` FROM `highwaygroup`.`users` ORDER BY `first_name` ASC"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $all_staff_list[$row['id']] = $row['name'];
    }
    $DB->free_result($query);
}

$staff_id = $_GLOBALS['staff']['id'];

$posted = array_merge($_POST, $_GET);

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}
