<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'edit';
$add              = false;

$id               = intval($_GET['id']);
$item             = productFromDatabase($id);

$item_dimension   = productDimensionFromDatabase($id);

$item_admin_notes = array_reverse(productCommentSearch(productCommentQuery(array(
    'product_id'   => $id,
    'type' => 'admin',
))));
$item_staff_messages = array_reverse(productCommentSearch(productCommentQuery(array(
    'product_id'   => $id,
    'type' => 'email',
))));
$item_changes = array_reverse(productCommentSearch(productCommentQuery(array(
    'product_id'   => $id,
    'type' => 'change',
))));
$item_bulk_pricing     = productBulkPricingFromDatabase($id);
$item_bulk_weights     = productBulkWeightsFromDatabase($id);
$item_categories       = productCategoriesFromDatabase($id);
$item_containers       = productContainersFromDatabase($id);
$item_inventory        = productInventoryFromDatabase($id);
$item_invoices         = productInvoicesFromDatabase($id);
$item_lock             = productGetLock($id);
$manuals               = productSafetyFromDatabase($id);
//$item_movies           = productMoviesFromDatabase($id);
$item_packings         = productPackingsFromDatabase($id,null);
$item_partof           = productPartsofFromDatabase($id);
$item_purchase_summary = productPurchaseSummary($id);
$item_related          = productRelatedFromDatabase($id);
$item_recommended      = productRecommendedFromDatabase($id);
$item_reserved         = productReservedFromDatabase($id);
$item_screenshots      = productScreenshotsFromDatabase($id);
$item_suppliers        = productSuppliersFromDatabase($id);
$brochure_images       = productBrochureFromDatabase($id);
$item_default_suppliers = defaultProductSuppliersFromDatabase($id);
$doedit_url           = $_GET;
$doedit_url['action'] = 'do_edit';
$doedit_url           = '.?' . http_build_query($doedit_url);
$item_firmware        = $section_handler->firmwaresFromDatabase($id);
$item_links           = $section_handler->linksFromDatabase($id);
$item_manuals         = $section_handler->allManualsFromDatabase($id);
$item_videos          = $section_handler->videosFromDatabase($id);
$item_upgradelinking  = productUpgradeLinkingFromDatabase($id);
$admin_files          = productAdminFilesFromDatabase($id);
$item_monitor         = productMonitorFromDatabase($id);
$serail_number        = getserialNumberProduct($id);
$art_work             = productArtWorkFromDatabase($id);
$sub_product          = subProductFromDatabase($id);
$product_lock         = productGetLock($id);
//item_monitor_part    = productMonitorPartLinkingFromDatabase($id); 
?>
<form action="<?php hecho($doedit_url)?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="item[id]" value="<?=$id?>" />
<?php
include __DIR__ . '/addedit.tpl.php';

?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
