<?php
include_once __DIR__ . '/common.inc.php';

$list = array();
$list = invoiceGetOptionList($posted['list']);

header('Content-Type: text/javascript');
jecho($list);
