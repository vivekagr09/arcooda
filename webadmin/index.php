<?php
include_once __DIR__ . '/common.inc.php';

if (($_COOKIE['REMOTE_USER'] ?? null) !== $_SERVER['REMOTE_USER']) {
    setcookie('REMOTE_USER', $_COOKIE['REMOTE_USER'] = $_SERVER['REMOTE_USER'], strtotime("+1 year"), '/');
}

include_once __DIR__ . '/header.inc.php';

include_once __DIR__ . '/footer.inc.php';
