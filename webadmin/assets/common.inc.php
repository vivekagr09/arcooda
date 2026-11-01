<?php
include_once __DIR__ . '/query.inc.php';

$section_code = 'asset';
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

global $_GLOBALS;

moduleOverride('asset', __DIR__ . '/query.inc.php');
moduleLoad('asset');

$section_singular = "Asset";
$section_plural   = "Assets";

$action     = $_REQUEST['action'];
$asset_id   = intval($_REQUEST['asset_id']);
$asset_type = $_REQUEST['asset_type'];
$table_info = assetTableInfoFromType($asset_type);
switch ($asset_type) {
    case 'invoice':
        $section_singular = "Attachment";
        $section_plural   = "Attachments";
        break;
    case 'purchaseorder':
        $section_singular = "Attachment";
        $section_plural   = "Attachments";
        break;
    case 'convert_screenshots':
        $section_singular = "Screenshot";
        $section_plural   = "Screenshots";
        break;
}
