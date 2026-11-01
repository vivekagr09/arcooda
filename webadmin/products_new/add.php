<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'add';

$id = '';
// defaults the condition to Brand New..
$item = array(
    'id'             => $id,
    'condition'      => 1,
    'warranty'       => 8,
    'available'      => 1,
);
$item_dimension        = array();

$item_admin_notes      = array();
$item_staff_messages   = array();
$item_changes          = array();
$item_bulk_pricing     = array();
$item_bulk_weights     = array();
$item_categories       = array();
$item_containers       = array();
$item_inventory        = array();
$item_invoices         = array();
$item_lock             = null;
$item_manuals          = array();
$manuals               = array();
$item_movies           = array();
$item_videos           = array();//$section_handler->videosFromDatabase($id);
$item_packings         = array();
$item_partof           = array();
$item_purchase_summary = array();
$item_related          = array();
$item_reserved         = array();
$item_screenshots      = array();
$item_suppliers        = array();
$item_default_suppliers= array();
$item_firmware         = array();
$item_links            =array();
$serail_number         = array();
$sub_product           = array();
$product_free_gift     = array();
$monitorOptions        = array();
if (array_key_exists('id', $_GET)) {
    die('TODO: Product copying');

    $id                = intval($_GET['id']);
    $item              = productFromDatabase($id);
    $item_dimension    = productDimensionFromDatabase($item['id']);

    $item_bulk_pricing = productBulkPricingFromDatabase($item['id']);
    $item_bulk_weights = productBulkWeightsFromDatabase($item['id']);
    $item_categories   = productCategoriesFromDatabase($item['id']);
    $item_containers   = productContainersFromDatabase($item['id']);
    $item_movies       = productMoviesFromDatabase($item['id']);
    $item_packings     = productPackingsFromDatabase($item['id']);
    $item_related = productRelatedFromDatabase($item['id']);
    $item_screenshots = productScreenshotsFromDatabase($item['id']);
    $item_suppliers   = productSuppliersFromDatabase($item['id']);

    $id          = '';
    $item['id']  = '';
    $item['qty'] = 0;
    foreach ($item as $k => $v) {
        if (substr($k, -4) == '_url') {
            unset($item[$k]);
        }
    }

    // now have to add the product getting copied to the list of related product...
    $item_related[] = $_GET['id'];
}

if (array_key_exists('section', $_GET)) {
    $item['section'] = intval($_GET['section']);
}

$doadd_url           = $_GET;
$doadd_url['action'] = 'do_add';
$doadd_url           = '.?' . http_build_query($doadd_url);
?>
<form action="<?php hecho($doadd_url)?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="item[id]" value="<?=$item['id']?>" />
<?php

include __DIR__ . '/addedit.tpl.php';

?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
