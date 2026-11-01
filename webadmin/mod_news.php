<?php
/******************************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 *****************************************************************************/

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$result = $DB->query("SELECT * FROM `news`");
while ($item = $DB->fetch_array($result)) {
    $news_short = addslashes(nl2br(htmlspecialchars($item['short_description'])));
    $news_long  = addslashes(nl2br(htmlspecialchars($item['long_description'])));
    $DB->query(sprintf(
        "UPDATE `news` SET `short_description` = '%s', `long_description` = '%s' WHERE `news_id` = %d LIMIT 1",
        $news_short,
        $news_long,
        $item['news_id']
    ));
}
