<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberFormatNumber, memberLoginHistoryFromDatabase

include_once __DIR__ . '/header.inc.php';

$member_id = intval($posted['member_id']);

$member_login_history = memberLoginHistoryFromDatabase($member_id);

?>
<h2>Member Logins <?=memberFormatNumber($member_id)?> - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/login.tpl.php';

include_once __DIR__ . '/footer.inc.php';
