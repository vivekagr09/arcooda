<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$member_id = intval($posted['member_id']);
if (!$member_id) {
    die('Invalid member id passed.');
}

$member_logins = memberLoginHistoryFromDatabase($member_id, '', 0);
?>
<div style="text-align:center"><h2>Login History for Member ID <?=memberFormatNumber($member_id)?></h2></div>
<?php

include __DIR__ . '/templates/list_logins.tpl.php';

?>
<br />
<?php

include_once __DIR__ . '/footer.inc.php';
