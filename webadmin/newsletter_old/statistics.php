<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action   = 'statistics';
$id       = intval($posted['id']);
$staff_id = $_GLOBALS['staff']['id'];
$site_id  = $_GLOBALS['site_settings']['site_id'];

$item          = $section_handler->fromDatabase($id);
$item_lock     = $section_handler->getLock($id);
$item_comments = $section_handler->commentSearch(array($section_handler->section_name . '_id' => $id, 'comment_type' => 'admin'));

$item_members_list = $section_handler->mailinglistFromDatabase($id);

$item_counts = array();
foreach ($item_members_list as $k => $v) {
    $item_counts[$v['email_status']]++;
}

$item_counts['total_visitors1'] = intval($item_counts['clicked'] + $item_counts['ordered']);
$item_counts['total_visitors2'] = intval($item_counts['clicked'] + $item_counts['ordered'] + $item_counts['unsubscribed']);
$item_counts['total_views']     = intval($item_counts['viewed'] + $item_counts['clicked'] + $item_counts['ordered'] + $item_counts['unsubscribed']);
$item_counts['total_sent']      = intval($item_counts['error'] + $item_counts['sent'] + $item_counts['bounced'] + $item_counts['viewed'] + $item_counts['clicked'] + $item_counts['ordered'] + $item_counts['unsubscribed']);

if (count($item_comments) > 5) {
    $item_comments = reset(array_chunk($item_comments, 5));
}

$cart_added = array();
if ($query = $DB->query(sprintf(
    "SELECT DISTINCT `member_id` FROM `highwaygroup`.`cart_tracking` WHERE `newsletter_id` = %d",
    $id
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $cart_added[] = $row['member_id'];
    }
    $DB->free_result($query);
}
?>
<h2>Newsletter Statistics <?=$section_handler->formatNumber($id)?> - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/statistics.tpl.php';
include __DIR__ . '/delivery.tpl.php';
include __DIR__ . '/locations.tpl.php';
include __DIR__ . '/sales_statistics.tpl.php';

?>
<form accept-charset="utf-8" action="index.php?action=do_comment&amp;id=<?=$id?>" method="post">
<?php

include __DIR__ . '/comments.tpl.php';

?>
	<div style="text-align:center;"><input type="submit" value="Add Comment" /></div>
</form>
<form accept-charset="utf-8" action="print_summary.php?id=<?=$id?>" method="post" target="_blank">
<?php

include __DIR__ . '/summary_information.tpl.php';

?>
	<div style="text-align:center;"><input type="submit" value="Print Graph" /></div>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
