<?php
/**
 * @var string $asset_type
 * @var string $section_singular
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
$action = 'add';

$asset_id = 0;

$asset = [
    'owner_id' => intval($_GET['owner_id']),
    'asset_type' => $asset_type,
];

?>
<h2>Add New <?=$section_singular?></h2>
<?php

include __DIR__ . '/templates/addedit_asset.tpl.php';

include_once __DIR__ . '/footer.inc.php';
