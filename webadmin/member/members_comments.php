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

$member_comments = memberCommentsFromDatabase($member_id);

?>
<div style="text-align:center"><h2>Admin Comments for Member ID <?=memberFormatNumber($member_id)?></h2></div>
<?php

include __DIR__ . '/templates/list_comments.tpl.php';

include_once __DIR__ . '/footer.inc.php';
