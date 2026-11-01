<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'add';
$id     = 0;

$item = seriesDefault();

$item_images = seriesImagesFromDatabase($id);

include __DIR__ . '/_add.tpl.php';

include_once __DIR__ . '/footer.inc.php';
