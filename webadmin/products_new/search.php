<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'search';

$search_for = array_key_exists('search_for', $_REQUEST) ? $_REQUEST['search_for'] : '';

include __DIR__ . '/_search.tpl.php';

include_once __DIR__ . '/footer.inc.php';
