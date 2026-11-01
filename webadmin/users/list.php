<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$user_handler = new htaccess(__DIR__ . '/.htpasswd');
$item_list    = $user_handler->get_users();
include __DIR__ . '/list.tpl.php';

include_once __DIR__ . '/footer.inc.php';
