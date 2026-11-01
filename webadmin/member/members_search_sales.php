<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$search_for = $posted['search_for'];
include __DIR__ . '/templates/search_sales_members.tpl.php';

include_once __DIR__ . '/footer.inc.php';
