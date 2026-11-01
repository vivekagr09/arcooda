<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$search_for = $posted['search_for'];

?>
<h2>Search Weekly Specials - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/search.tpl.php';

include_once __DIR__ . '/footer.inc.php';
