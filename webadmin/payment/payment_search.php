<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$country_list = getCountryList();

include 'templates/search_payment.tpl.php';

include_once __DIR__ . '/footer.inc.php';
