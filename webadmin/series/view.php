<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'view';
$id     = $_GET['id'] ?? 0;

$item = seriesFromDatabase($id) ?? seriesDefault();

$item_images = seriesImagesFromDatabase($id);

include __DIR__ . '/_view.tpl.php';

include_once __DIR__ . '/footer.inc.php';
