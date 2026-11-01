<?php
if ('Daniel' == ($_COOKIE['REMOTE_USER'] ?? '')) {
    restore_error_handler();
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');
}

include_once __DIR__ . '/protected/common.inc.php';

if ('Daniel' == ($_COOKIE['REMOTE_USER'] ?? '')) {
    restore_error_handler();
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');
}
