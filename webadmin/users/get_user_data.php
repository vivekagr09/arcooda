<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

header("Content-Type: text/plain");

echo json_encode($_GLOBALS["staff"]);
