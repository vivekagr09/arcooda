<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/db_sql_extended.php';

$DB = new DB_SQL_Extended();
$DB->set_charset('utf8mb4');
