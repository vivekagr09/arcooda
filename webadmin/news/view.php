<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'view';
$id     = $_GET['id'] ?? 0;

$item = newsFromDatabase($id) ?? newsDefault();

$item_comments = newsCommentsFromDatabase($id);
$item_images   = newsImagesFromDatabase($id);
$item_videos   = newsVideosFromDatabase($id);

include __DIR__ . '/_view.tpl.php';

include_once __DIR__ . '/footer.inc.php';
