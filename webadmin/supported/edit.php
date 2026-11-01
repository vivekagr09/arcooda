<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'edit';
$id     = $_GET['id'] ?? 0;

$item = supportedFromDatabase($id) ?? supportedDefault();

$item_images = supportedImagesFromDatabase($id);

include __DIR__ . '/_edit.tpl.php';

include_once __DIR__ . '/footer.inc.php';
