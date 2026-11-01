q<?php
include_once __DIR__ . '/common.inc.php';

use PHPMailer\PHPMailer\PHPMailer;

global $_GLOBALS;

moduleLoad('phpmailer');

$action = $_REQUEST['action'];
$id     = (array_key_exists('id', $_REQUEST)) ? intval($_REQUEST['id']) : 0;
$update_status = false;
//Update Actions
switch ($action) {
    case 'do_featured':
        if(array_key_exists("item_list",$_REQUEST)) {
            foreach($_REQUEST['item_list'] as $featured) {
                productSetValue($featured['id'],"featured_expiry",$featured['featured_expiry']);
            }
        }
        if (array_key_exists("products_affected", $_REQUEST)) {
            $changes       = array();
            $affected_list = preg_split("/[^0-9 ]+/", $_REQUEST["products_affected"]);
            $updated_list  = array();
            if (is_array($_REQUEST["product_list"])) {
                foreach ($_REQUEST["product_list"] as $after) {
                    $item_id                   = $after["product_id"];
                    $after["feature"] = "y";
                    $before                    = productFromDatabase($item_id);
                    foreach ($after as $k => $v) {
                        if ($v != $before[$k]) {
                            $changes[$item_id][] = $k . " from " . formatSQLString($before[$k]) . " to " . formatSQLString($v);
                            productSetValue($item_id, $k, $v);
                        }
                    }

                    $updated_list[] = $item_id;
                }
            }

            foreach (array_diff($affected_list, $updated_list) as $item_id) {
                $changes[$item_id][] = "feature to 'n'";
                $changes[$item_id][] = "featured_expiry to NULL";
                productSetValue($item_id, "feature", "n");
                productSetValue($item_id, "featured_expiry", null);
            }
            if (count($changes)) {
                foreach ($changes as $item_id => $change) {
                    $comment = array(
                        'product_id' => $item_id,
                        'comments' => "Changed " . implode(", ", $change) . ".",
                        'type' => "change",
                    );
                    productCommentToDatabase($comment);
                }
            }

        }
        $action = "featured";
        $update_status = true;
        break;

    case 'do_add_inventory':
        if (is_array($_REQUEST['stock_additions']) && count($_REQUEST['stock_additions'])) {
            $supplier_list  = productGetOptionList('supplier_id');
            $_REQUEST['id'] = array();

            /**
             * Build a new product inventory order
             */
            $stock             = $_REQUEST['stock'];
            $stock['order_id'] = productOrderToDatabase($stock);
            /**
             * Add inventory for each item in the order
             */
            foreach ($_REQUEST['stock_additions'] as $stock_addition) {
                $item_array=[];
                /**
                 * Find the product id
                 */
                $item_id = $stock_addition['product_id'];

                /**
                 * Get and set the before/after quantities
                 */
                $qty     = productGetValue($item_id, 'qty');
                $new_qty = $stock_addition['product_qty'] + $qty;
                productSetValue($item_id, 'qty', $new_qty);
                /**
                 * Add the stock to the database
                 */
                $item_array[]= array(
                    'product_id'       => $item_id,
                    'order_id'         => $stock['order_id'],
                    'movement'         => $stock_addition['product_qty'],
                    'color_id'         => $stock_addition['color_id'],
                    'comments'         => sprintf(
                        'Supplier: %s Ref: %s (Cost: %s%s %s + Freight: %s%s %s%s)%s',
                        $supplier_list[$stock['supplier_id']],
                        $stock['reference'],
                        getCurrencySymbol($stock['currency']),
                        convertCurrency($stock_addition['unit_cost'], $stock['currency']),
                        $stock['currency'],
                        getCurrencySymbol($stock['currency']),
                        convertCurrency($stock_addition['unit_freight'], $stock['currency']),
                        $stock['currency'],
                        ('USD' != $stock['currency']) ? sprintf(
                            ', Cost: $%s USD + Freight: $%s USD',
                            convertCurrency($stock_addition['unit_cost'], $stock['currency'], 'USD'),
                            convertCurrency($stock_addition['unit_freight'], $stock['currency'], 'USD')
                        ) : '',
                        ('' != $stock['comment']) ? "\n" . $stock['comment'] : ''
                    ),
                    'available'        => $new_qty,
                    'unit_cost'        => $stock_addition['unit_cost'],
                    'unit_freight'     => $stock_addition['unit_freight'],
                    'unit_cost_usd'    => convertCurrency($stock_addition['unit_cost'], $stock['currency'], 'USD'),
                    'unit_freight_usd' => convertCurrency($stock_addition['unit_freight'], $stock['currency'], 'USD'),
                    'unit_cost_aud'    => convertCurrency($stock_addition['unit_cost'], $stock['currency'], 'AUD'),
                    'unit_freight_aud' => convertCurrency($stock_addition['unit_freight'], $stock['currency'], 'AUD'),
                    'unit_currency'    => $stock['currency'],
                    'stock_left'       => max(0, $new_qty),
                    'stock_type'       => 'NEW',
                    'currency'         => $stock['currency'],
                    'supplier_id'      => $stock['supplier_id'],
                    'user'             => $_GLOBALS['staff']['user'],
                );
                $inventory_id = productInventoryToDatabase($item_id,$item_array);
                /**
                 * Update the cost pricing to include the new stock
                 */
                if ($new_qty > 0) {
                    productUpdateCostPrice($item_id);
                }

                $_REQUEST['id'][] = $item_id;
                /**
                 * Update Availability
                 */
                if ($new_qty > 0) {
                    productSetValue($item_id, 'available', 1);
                }

            }
            $_REQUEST['id'] = implode("|", array_unique($_REQUEST['id']));
        } else {
            die('Not enough information for request');
        }

        $action = 'list_inventory';
        break;

    case 'do_add':
        unset($_REQUEST['item']['id']);
        $new_added        = true;
    case 'do_edit':
        if (is_array($_REQUEST['item'])) {
            $id                = $_REQUEST['item']['id'];
            $is_new            = !productExists($id);
            if($is_new){
                $_REQUEST['item']['date_added']=formatDateTime();
            }
            $old_product       = productFromDatabase($id);
            $new_product       = productFromPost($_REQUEST['item']);
            if (array_key_exists('item_addon_default', $_POST) && $_POST['item_addon_default'] == 'discunt') {
                $new_product['bracket_discount_main'] = 'y';
            }else{
                $new_product['bracket_discount_main'] = 'n';
            }
            $preferred_supplier = null;
            if (array_key_exists('item_suppliers', $_POST)) {
                foreach ($_POST['item_suppliers'] as $supplier) {
                    if (($supplier['main'] ?? '') === 'y') {
                        $preferred_supplier = $supplier['supplier_id'] ?? null;
                        break;
                    }
                }
            }
            $new_product['preferred_supplier'] = $preferred_supplier;
            $id                = productToDatabase($new_product);
            if($id && $action=='do_add'){
               $_GET['id']=$id;
               $_GET['action']='edit';
            }
            $new_product['id'] = $_REQUEST['id'] = $id;
            if (array_key_exists('item_packings', $_POST)) {
                $item_packings = productPackingsFromPost($_POST['item_packings']);
                productPackingsToDatabase($id,$item_packings);
            }
            if (array_key_exists('item_packings_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_packings_deleted']) as $v) {
                    if ($v) {
                        productPackingDelete($v);
                    }
                }
            }
            if ($_POST['item']['is_price_flux']==1 && $_POST['item']['cost_currency']!=$_POST['item']['sales_currency']&& $_POST['item']['stock_type']=='consignment') {
                $new_product['id']=$id;
                updateProductConsignmentCost($new_product);
            }
            if (array_key_exists('item_dimension', $_POST)) {
                $item_dimension_data[]=$_POST['item_dimension'];
                $item_dimension = productDimensionFromPost($item_dimension_data);
                $item_dimension[0]['id'] = $id;
                productDimensionToDatabase($item_dimension[0]);
            }

            if (array_key_exists('item_bulk_pricing_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_bulk_pricing_deleted']) as $v) {
                    if ($v) {
                        productBulkPriceDelete($v);
                    }
                }
            }
            if (array_key_exists('item_bulk_pricing', $_POST)) {
                productBulkPricingToDatabase($id, productBulkPricingFromPost($_POST['item_bulk_pricing']));
            }

            if (array_key_exists('item_bulk_weights_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_bulk_weights_deleted']) as $v) {
                    if ($v) {
                        productBulkWeightDelete($v);
                    }
                }
            }
            if (array_key_exists('item_partof_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_partof_deleted']) as $v) {
                    if ($v) {
                        productPartsofDelete($v);
                    }
                }
            }
            if (array_key_exists('item_partof', $_POST)) {
                productPartofToDatabase(productPartsofFromPost($_POST['item_partof']));
            }
            if (array_key_exists('item_upgradelinking', $_POST)) {
                productUpgradeLinkingToDatabase(productUpgradeLinkingFromPost($_POST['item_upgradelinking']));
            }
            if (array_key_exists('item_upgrade_linking_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_upgrade_linking_deleted']) as $v) {
                    if ($v) {
                        productUpgradeLinkingDelete($v);
                    }
                }
            }
            if (array_key_exists('item_monitor', $_POST)) {
                foreach ($_POST['item_monitor'] as $key => $monitor) {
                    $monitor['product_id']=$id;
                    $data_monitor=productMonitorFromPost($monitor);
                    $monitor_id=productMonitorToDatabase($data_monitor);
                    if (array_key_exists('item_monitor_part', $monitor)) {
                        productMonitorPartLinkingToDatabase(productMonitorPartLinkingFromPost($monitor['item_monitor_part']),$monitor_id);
                    }
                }
            }
            if (array_key_exists('item_monitor_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_monitor_deleted']) as $v) {
                    if ($v) {
                        productMonitorDelete($v);
                    }
                }
            }
            if (array_key_exists('item_monitor_partof_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_monitor_partof_deleted']) as $v) {
                    if ($v) {
                        productMonitorPartLinkingDelete($v);
                    }
                }
            }
            if (array_key_exists('item_bulk_weights', $_POST)) {
                productBulkWeightsToDatabase($id, productBulkWeightsFromPost($_POST['item_bulk_weights']));
            }
            if (array_key_exists('item_categories_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_categories_deleted']) as $v) {
                    if ($v) {
                        productCategoryDelete($v);
                    }
                }
            }
            if (array_key_exists('item_categories', $_POST)) {
                $DB->query("UPDATE `products_linked_categories` SET `deleted_at` = NOW() WHERE `deleted_at` IS NULL AND `product_id` = $id");
                productCategoriesToDatabase($id, productCategoriesFromPost($_POST['item_categories']));
            
            }
            if (array_key_exists('add_on_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['add_on_deleted']) as $v) {
                    if ($v) {
                        productAddonDelete($v);
                    }
                }
            }
            if (array_key_exists('item_addon', $_POST)) {
                productAddonToDatabase($id, productAddonsFromPost($_POST['item_addon']));
            
            }

            if (array_key_exists('add_on_monitor_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['add_on_monitor_deleted']) as $v) {
                    if ($v) {
                        productAddonBracketDelete($v);
                    }
                }
            }
            if (array_key_exists('item_addon_monitor', $_POST)) {
                productAddonBracketToDatabase($id, productAddonsBracketFromPost($_POST['item_addon_monitor'],$_POST['item_addon_default']));
            
            }

            if (array_key_exists('monitor', $_POST) && $_POST['item']['type'] == 305) {
                $monitorData = $_POST['monitor'];
                $monitorData['category_id'] = $_POST['item']['type'];
                productMonitorOptionsDelete($id);
                productMonitorOptionsToDatabase($id, productMonitorOptionsFromPost($monitorData));
            
            }
            if (array_key_exists('item_containers', $_POST)) {
                productContainersToDatabase($id, productContainersFromPost($_POST['item_containers']));
            
            }
            if (array_key_exists('item_movies_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_movies_deleted']) as $v) {
                    if ($v) {
                        productMovieDelete($v);
                    }
                }
            }
            if (array_key_exists('item_movies', $_POST)) {
                productMoviesToDatabase($id, productMoviesFromPost($_POST['item_movies']));
            }
            //serial no code
            if (array_key_exists('item_sereal_id_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_sereal_id_deleted']) as $v) {
                    if ($v) {
                        productSerialNoDelete($v);
                    }
                }
            }
            if (array_key_exists('productserail', $_POST)) {
                foreach ($_POST['productserail'] as $key => $value) {
                    if($value['serial_id'] && $value['product_id']){
                        $value['product_str']=$new_product['product_name'];
                        if($value['id']){
                            $value['modified']=formatDateTime();
                        }else{
                            $value['created']=formatDateTime();
                            
                        }
                        productSerialNoToDatabase(productSerialNoFromPost($value));
                    }
                    
                }
                
                
            }
            //end serial no code
            if (array_key_exists('item_manuals_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_manuals_deleted']) as $v) {
                    if ($v) {
                        productSafetyDelete($v);
                    }
                }
            }
            
            if (array_key_exists('sub_product', $_POST)) { 
                foreach ($_POST['sub_product'] as $key => $sub_product) {
                    if($sub_product['name']){
                         $item_sub_product = productSubModuleFromPost($sub_product,$_FILES['sub_product'],$key);
                        productGenericToDatabase($item_sub_product,'`product_sub_module`');
                    }  
                }
            }
            if (array_key_exists('item_sub_product_id_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_sub_product_id_deleted']) as $v) {
                    if ($v) {
                        SubproductDelete($v);
                    }
                }
            }
           if (array_key_exists('subproductprice', $_POST)) { 
                foreach ($_POST['subproductprice'] as $key => $price) {
                        $price['special_flag']=isset($price['special_flag'])?$price['special_flag']:'n';
                         $sub_product_price = productSubModuleFromPost($price,[],0);
                        productGenericToDatabase($sub_product_price,'`product_sub_module`');
                }
            }
            if (array_key_exists('artwork', $_POST)) { 
                foreach ($_POST['artwork'] as $key => $artwork) {
                    if($artwork['name']){
                         $item_artwork = productArtWorkFromPost($artwork,$_FILES['artwork'],$key);
                        productGenericToDatabase($item_artwork,'`product_art_work`');
                    }  
                }
            }
             if (array_key_exists('item_art_work_id_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_art_work_id_deleted']) as $v) {
                    if ($v) {
                        productArtWorkDelete($v);
                    }
                }
            }
            if (array_key_exists('item_manual', $_POST)) {
                $item_manuals = productSafetyFromPost($_POST['item_manual']);
                productSafetyToDatabase($id, $item_manuals);
            }
            if (array_key_exists('admin_file', $_POST)) {
                $item_manuals = productAdminFilesFromPost($_POST['admin_file']);
                productAdminFilesToDatabase($id, $item_manuals);
            }
            if (array_key_exists('admin_files_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['admin_files_deleted']) as $v) {
                    if ($v) {
                        productAdminFilesDelete($v);
                    }
                }
            }
            
            if (array_key_exists("manuals", $_POST)) {
                $manuals_pos = $_POST["manuals"];
                $section_handler->updateManualsPosition($id, $manuals_pos);
            }
            if (array_key_exists("firmware_pos", $_POST)) {
                $firmware_pos = $_POST["firmware_pos"];
                $section_handler->updateFirmwaresPosition($id, $firmware_pos);
            }
            if (isset($_POST['artwork'])) {
                $_POST['productlink'] = $_POST['artwork'];
            }
            $new_item_links = $section_handler->linksFromPost($_POST['productlink'] ?? [], $id, $_FILES['link_image']);
            $section_handler->linksToDatabase($id, $new_item_links);
            $new_item_videos = $section_handler->videosFromPost($_POST['productlink'] ?? [], $id);
            $section_handler->videosToDatabase($id, $new_item_videos);
            if (array_key_exists('inventory_add', $_POST)) {
                foreach($_REQUEST['inventory_add']['color'] ?? [] as $key => $value){
                    if(in_array($value,$_REQUEST['item']['colour'])){
                        $data_stock=[];
                        $data_stock['product_id']=$id;
                        $data_stock['color_id']=intval($value);
                        $data_stock['when_sold_out'] = $_REQUEST['inventory_add']['when_sold_out'][$key];
                        $data_stock['available'] = $_REQUEST['inventory_add']['available'][$key];
                        $data_stock['user_id']=$_GLOBALS['staff']['id'];
                        inventoryStockadd_update_status($data_stock);
                    }
                } 
            }  
        if (count($posted['brochure_image'] ?? [])) {
            foreach ($posted['brochure_image'] as $key => $image) {
                $image['product_id']=$id;
                $image['file_orig']=$image['name'];
                $image['caption'] = trim($image['caption']);
                $images = productBrochureFromPost($image,'`product_brochure_image`');
                productGenericToDatabase($images,'`product_brochure_image`');
            }
        }
         if (array_key_exists('brochure_images_deleted_val', $_POST)) {
            foreach (preg_split('/[^0-9]+/', $_POST['brochure_images_deleted_val']) as $v) {
                if ($v) {
                          productGenericsDelete($v,'`product_brochure_image`','id');
                }
            }
        }
            if (array_key_exists('item_related_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_related_deleted']) as $v) {
                    if ($v) {
                        productRelationDelete($v);
                    }
                }
            }
            if (array_key_exists('item_related', $_POST)) {
                productRelatedToDatabase($id, productRelatedFromPost($_POST['item_related']));
            }
            if (array_key_exists('item_recommended_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_recommended_deleted']) as $v) {
                    if ($v) {
                        productRecommendedDelete($v);
                    }
                }
            }
            if (array_key_exists('item_recommended', $_POST)) {
                productRecommendedToDatabase($id, productRecommendedFromPost($_POST['item_recommended']));
            }
            // code to manage discontinued recomended product
            /*if (array_key_exists('item_discontinued_recommended_delete', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_discontinued_recommended_delete']) as $v) {
                    if ($v) {
                        productDiscontinuedRecommendedDelete($v);
                    }
                }
            }*/


            /*if (array_key_exists('recommended_product', $_POST)) {
                productDiscontinuedRecommendedToDatabase($id, productDiscontinuedRecommendedFromPost($_POST['recommended_product']));
            }*/

            if (array_key_exists('item_screenshots_deleted_val', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_screenshots_deleted_val']) as $v) {
                    if ($v) {
                        productScreenshotDelete($v);
                    }
                }
            }
            if (array_key_exists('item_screenshots_deleted_colour', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_screenshots_deleted_colour']) as $v) {
                    if ($v) {
                        productScreenshotColorDelete($v);
                    }
                }
            }
            if (array_key_exists('item_screenshots_colour', $_POST)) {
                productColourImagesToDatabase(productColourImages($_POST['item_screenshots_colour']));
            }
            if (array_key_exists('item_screenshots', $_POST)) {
            $item_screenshots = productScreenshotsFromPost($_POST['item_screenshots'],$_FILES['item_screenshots'],$id);
            //echo '<pre>'; print_r($item_screenshots); die;
                productScreenshotsToDatabase($id, $item_screenshots);
            }

            if (array_key_exists('item_suppliers_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['item_suppliers_deleted']) as $v) {
                    if ($v) {
                        productSupplierDelete($v);
                    }
                }
            }
            if (array_key_exists('item_suppliers', $_POST)) {
                $suppliersHistory = productSuppliersHistoryFromPost($_POST['item_suppliers']);
                $lastUpdatedSupplier = [];
                foreach ($suppliersHistory as $key => $supplier) {
                    if($supplier['last_update'] == 'y'){
                        $lastentry = $DB->query_first(sprintf(
                            "SELECT * FROM `arcooda_www`.`supplier_product_history` WHERE `supplier_id` = %d AND `product_id` = %d ORDER BY id desc LIMIT 1",
                            $supplier['supplier_id'],
                            $supplier['product_id']
                        ), MYSQLI_ASSOC);
                        if(count($lastentry)){
                            $lastUpdatedSupplier['supplier_id'] = $lastentry['supplier_id'];
                            $lastUpdatedSupplier['product_id'] = $lastentry['product_id'];
                            $lastUpdatedSupplier['cprice_usd'] = $lastentry['cprice_usd'];
                            $lastUpdatedSupplier['cost_price'] = $lastentry['cost_price'];
                            $lastUpdatedSupplier['sup_currency'] = $lastentry['sup_currency'];
                            $lastUpdatedSupplier['markup'] = $lastentry['markup'];
                            $lastUpdatedSupplier['sales_price'] = $lastentry['sales_price'];
                            $lastUpdatedSupplier['movement'] = $lastentry['movement'];
                        }

                    }
                    if ($supplier['cost_price'] == 0 && empty($supplier['markup'])) {
                        unset($suppliersHistory[$key]);
                    }
                }
                if(count($lastUpdatedSupplier)){
                    $suppliersHistory[] =$lastUpdatedSupplier;
                }
                productSuppliersHistoryToDatabase($id,$new_product, $suppliersHistory);
                $discontinued = isset($_POST['discontinued']) ? $_POST['discontinued'] : 'n';
                foreach ($_POST['item_suppliers'] as &$supplier) {
                    $supplier['discontinued'] = $discontinued;
                }
                productSuppliersToDatabase($id, productSuppliersFromPost($_POST['item_suppliers'],'product'));
                $supplier_lists = productSuppliersFromDatabase($id);
                if(count($supplier_lists) == 1) {
                    foreach($supplier_lists as $supplier_list) {
                        productSupplierSetValue($supplier_list['id'], 'main', 'y');
                    }
                }
            }
            if (array_key_exists('bonus', $posted['item']) && $posted['item']['bonus'] == 'y' && array_key_exists('free_gifts', $posted['item']) && $posted['item']['free_gifts'] == 'y') {
                $product_gifts = productGiftsFromPost($id,$posted);
                productGiftsDelete($id);
                productGiftsToDatabase($id, 0, $product_gifts);
            }
            if ( array_key_exists('product_free_gift', $posted)) {
                $product_gifts = productFreeGiftsFromPost($id,$posted);
                productFreeGiftsDelete($id);
                productFreeGiftsToDatabase($id, 0, $product_gifts);
            }
            if ($new_product['section']) {
                if (array_key_exists('item_image', $_FILES) && $_FILES['item_image']['error'] == UPLOAD_ERR_OK) {
                    $sub_path = $DB->query_first(sprintf(
                        "SELECT `img_location` FROM `arcooda_www`.`product_types` WHERE `id` = %d LIMIT 1",
                        $new_product['section']
                    ), MYSQLI_ASSOC);
                    $dst_image = false;
                    $dst_file = false;
                    for ($i = 1;;$i++) {
                        $dst_image = sprintf(
                            'img_%d_%d.jpg',
                            $i,
                            $new_product['id']
                        );
                        $dst_file = sprintf(
                            '%s%s',
                            productGetOptionList('image_path'),
                            $dst_image
                        );
                        if (!is_file($dst_file)) {
                            break;
                        }
                    }
                    if (move_uploaded_file($_FILES['item_image']['tmp_name'], $dst_file)) {

                        productSetValue($new_product['id'], 'image', $dst_image);
                        productSetValue($new_product['id'], 'image_orig', $_FILES['item_image']['name']);
                        deleteProductCachedImages($new_product['id'],'/protected/cache/product/image/');
                    }
                }
                if (array_key_exists('item_thumb', $_FILES) && $_FILES['item_thumb']['error'] == UPLOAD_ERR_OK) {
                    $sub_path = $DB->query_first(sprintf(
                        "SELECT `img_location` FROM `arcooda_www`.`product_types` WHERE `id` = %d LIMIT 1",
                        $new_product['section']
                    ), MYSQLI_ASSOC);
                    $dst_image = false;
                    $dst_file = false;
                    for ($i = 1;;$i++) {
                        $dst_image = sprintf(
                            'img_%d_%d_sml.jpg',
                            $i,
                            $new_product['id']
                        );
                        $dst_file = sprintf(
                            '%s%s',
                            productGetOptionList('image_path'),
                            $dst_image
                        );
                        if (!is_file($dst_file)) {
                            break;
                        }
                    }
                    if (move_uploaded_file($_FILES['item_thumb']['tmp_name'], $dst_file)) {
                        productSetValue($new_product['id'], 'thumb', $dst_image);
                        productSetValue($new_product['id'], 'thumb_orig', $_FILES['item_thumb']['name']);
                         deleteProductCachedImages($new_product['id'],'/protected/cache/product/image/');
                    }
                }
            }

            $delta = array();
            foreach ($new_product as $k => $v) {
                if ($old_product[$k] != $new_product[$k] && !is_array($new_product[$k]) && !is_array($old_product[$k])) {
                    $delta[] = $k . ' changed from: ' . hecho($old_product[$k], 1) . ' to: ' . hecho($new_product[$k], 1);
                }
            }

            if (array_key_exists('item_admin_note', $_REQUEST) && '' != trim($_REQUEST['item_admin_note'])) {
                $comment = array(
                    'product_id' => $id,
                    'comments' => $_REQUEST['item_admin_note'],
                    'type' => 'admin',
                );
                productCommentToDatabase($comment);
                $delta[] = 'Admin Note added: ' . formatSQLString($_REQUEST['item_admin_note']);
            }

            if (count($delta)) {
                $comment = array(
                    'product_id' => $id,
                    'comments' => "Changes Made: \n" . implode("\n", $delta),
                    'type' => 'change',
                );
                productCommentToDatabase($comment);
            }

            if (array_key_exists('item_message_to_staff', $_REQUEST) && '' != $_REQUEST['item_message_to_staff']['message']) {
                $new_email           = new PHPMailer();
                $new_email->Sender   = $_GLOBALS['staff']['email'];
                $new_email->From     = $_GLOBALS['staff']['email'];
                $new_email->FromName = trim($_GLOBALS['staff']['first_name'] . ' ' . $_GLOBALS['staff']['last_name']);
                $new_email->Mailer   = "sendmail";
                $new_email->CharSet  = "utf-8";
                $new_email->Subject  = "RE: " . $_GLOBALS['site_settings']['site_name'] . " Product #" . $id;
                $new_email->Body     = nl2br($_REQUEST['item_message_to_staff']['message']);
                $new_email->AltBody  = strip_tags($_REQUEST['item_message_to_staff']['message']);
                $new_email->isHTML(true);
                $addresses = array();
                foreach ($_REQUEST['item_message_to_staff']['staff_list'] as $staff_id) {
                    if ('' != $staff_id) {
                        $staff       = staffFromDatabase($staff_id);
                        $addresses[] = trim($staff['first_name'] . ' ' . $staff['last_name']) . ' <' . $staff['email'] . '>';
                        $new_email->AddAddress($staff['email'], trim($staff['first_name'] . ' ' . $staff['last_name']));
                    }
                }

                if ($new_email->Send()) {
                    $comment = array(
                        'product_id' => $id,
                        'comments' => 'Email Sent to Staff: ' . implode(', ', $addresses) . "\n" . hecho($_REQUEST['item_message_to_staff']['message'], 1),
                        'type' => 'email',
                    );
                    productCommentToDatabase($comment);
                }

            }
            $action = 'edit';
            if ('' != $_REQUEST['return_message']) {
                $action = 'select';
            }

        } else {
            $action = 'error';
        }
        if(is_array($_REQUEST['item_screenshots_colour'])){
            foreach ($_REQUEST['item_screenshots_colour'] as $key => $value){
                UpdateProductColourImagesRankStatus($value);
            }
        }
        break;

    case 'activate':
    case 'do_activate':
        productSetValue($_REQUEST['id'], 'active', 'y');
        $action = 'list';
        break;

    case 'deactivate':
    case 'do_deactivate':
        productSetValue($_REQUEST['id'], 'active', 'n');
        $action = 'list';
        break;

    case "toggle":
    case 'do_toggle':
        productSetValue($_REQUEST['id'], 'active', (productHasValue($_REQUEST['id'], 'active', 'y')) ? 'n' : 'y');
        $action = 'list';
        break;

    case 'lock':
    case "do_lock":
        productSetLock($id, $_GLOBALS['staff']['id']);
        $action = 'list';
        break;

    case 'unlock':
    case "do_unlock":
        productSetLock($id);
        $action = 'list';
        break;

    case 'copy':
    case 'do_copy':
        $item = productFromDatabase($_REQUEST['id']);
        unset($item['id']);
        $id             = productToDatabase($item);
        $item_packings = [];
        foreach (productGenericsFromDatabase($_REQUEST['id'], '`product_packing`','packing_id') as $k => $v) {
            $item_packings[]=['width'=>$v['width'],'height'=>$v['height'],'depth'=>$v['depth'],'weight'=>$v['weight'],'name'=>$v['name']];
        } 
        productPackingsToDatabase($id,$item_packings);
        $item_dimension[] = productDimensionFromDatabase($_REQUEST['id']);
        $item_dimension[0]['id'] = $id;
        productDimensionToDatabase($item_dimension[0]);
        $productLinks = $section_handler->linksFromDatabase($_REQUEST['id']);
        if(count($productLinks) > 0){
            $productLinks = array_map(function($item){
                unset($item['id']);
                unset($item['created_time']);
                $item['product_id'] = $id;
                return $item;
            }, $productLinks);
            $section_handler->linksToDatabase($id, $productLinks);
        }        
        $productVideos = $section_handler->videosFromDatabase($_REQUEST['id']);
        if(count($productVideos) > 0){
            $productVideos = array_map(function($item){
                unset($item['id']);
                unset($item['created_time']);
                $item['product_id'] = $id;
                return $item;
            }, $productVideos);
            $section_handler->videosToDatabase($id, $productVideos);
        }
        productScreenshotsCopy($_REQUEST['id'],$id);
        productSafetyCopy($_REQUEST['id'],$id);
        copyProductMonitorOption($_REQUEST['id'],$id);
        copyProductAddonToDatabase($_REQUEST['id'],$id);
        copyProductFreeGiftsToDatabase($_REQUEST['id'],$id);
        copyProductGiftsToDatabase($_REQUEST['id'],$id);
        copyProductSuppliersToDatabase($_REQUEST['id'],$id);
        copyProductAdminFilesToDatabase($_REQUEST['id'],$id);
        copyProductBulkPricingToDatabase($_REQUEST['id'],$id);
        copyProductPartofToDatabase($_REQUEST['id'],$id);
        copyProductUpgradeLinkingToDatabase($_REQUEST['id'],$id);
        copyProductBulkWeightsToDatabase($_REQUEST['id'],$id);
        copyProductCategoriesToDatabase($_REQUEST['id'],$id);
        copyProductContainersToDatabase($_REQUEST['id'],$id);
        copyProductSafetyToDatabase($_REQUEST['id'],$id);
        copyProductBrochureToDatabase($_REQUEST['id'],$id);
        copyProductRelatedToDatabase($_REQUEST['id'],$id);
        copyProductRecommendedToDatabase($_REQUEST['id'],$id);
        
        $action         = 'edit';
        redirect('edit.php?action=edit&id='.$id);
        die('die');
        break;

    case 'activate_ss':
    case 'deactivate_ss':
        $id      = intval($_REQUEST['id']);
        $shot_id = intval($_REQUEST['shot_id']);
        $active  = ('activate_ss' == $action) ? 'y' : 'n';
        $txt     = (('activate_ss' == $action) ? '' : 'de') . 'activated';
        productScreenshotSetValue($shot_id, 'active', $active);

        $comment = array(
            'product_id' => $id,
            'comments' => 'Screenshot (ID: ' . $shot_id . ') has been ' . $txt,
            'type' => 'change',
        );
        productCommentToDatabase($comment);

        $action = 'edit';
        break;
    case 'activate_mm':
    case 'deactivate_mm':
        $data_manual_update=[];
        if($_GET['manual_id']){
                $data_manual_update['id']=$_GET['manual_id'];
                $data_manual_update['active']=('activate_mm' == $action) ? 'y' : 'n';
                productGenericToDatabase($data_manual_update,'`product_manuals`', 'id');
                redirect($_GET['return_url']);
                die;
        }
        break;
       
    case 'do_delete':
        $id = intval($_REQUEST['id']);
        productSetValue($id, 'active', 'n');
        $action = 'list';
        if ('' != $_REQUEST['return_message']) {
            $action = 'select';
        }
        break;

    case 'add_stock':
        $action = 'edit';
        if(count($_REQUEST['item']['colour'])<=1){
            if (!$_REQUEST['inventory_add']['movement']) {
                break;
            }
        }
        $supplier_list = productGetOptionList('supplier_id');
        $inventory=[];
        $currencyArray = [
            'unit_cost' => 'unit_currency',
            'unit_freight' => 'unit_freight_currency',
            'other_cost' => 'other_cost_currency'

        ];

        foreach (array('unit_cost', 'unit_freight','other_cost') as $k1 => $v1) {
            foreach (array('_aud' => 'AUD', '_usd' => 'USD') as $k2 => $v2) {
                $inventory[$v1 . $k2] = convertCurrency($_REQUEST['inventory_add'][$v1], $_REQUEST['inventory_add'][$currencyArray[$v1]], $v2, false, 4);
            }
        }
        $inventory['comments'] = trim(sprintf(
            "%s%s(Cost: %s %s + Freight: %s%s)%s",
            ($_REQUEST['inventory_add']['supplier_id']) ? sprintf(
                "%s\n",
                $supplier_list[$_REQUEST['inventory_add']['supplier_id']]
            ) : '',
            trim(sprintf(
                "%s %s\n",
                $_REQUEST['inventory_add']['comment_1'],
                $_REQUEST['inventory_add']['comment_2']
            )),
            // getCurrencySymbol($_REQUEST['inventory_add']['unit_currency']),
            $_REQUEST['inventory_add']['unit_cost'],
            $_REQUEST['inventory_add']['unit_currency'],
            // getCurrencySymbol($_REQUEST['inventory_add']['unit_currency']),
            $_REQUEST['inventory_add']['unit_freight'],
            $_REQUEST['inventory_add']['unit_freight_currency'],
            sprintf(
                ", Total Cost: $%s USD",
                $_REQUEST['inventory_add']['total_cost'],
            )
        ));
        // $inventory['unit_cost_aud']=0.0000;
        // $inventory['unit_cost_usd']=0.0000;
        // $inventory['unit_freight_usd']=0.0000;
        // $inventory['unit_freight_aud']=0.0000;
        $inventory_value=0;
        if(count($_REQUEST['item']['colour'])<=1){
            $inventory['product_id'] =$id;
            $inventory['movement'] = intval($_REQUEST['inventory_add']['movement']);
            $inventory_value=productGetValue($id, 'qty') + intval($_REQUEST['inventory_add']['movement']);
            $inventory['available'] = $inventory_value;
            $inventory['unit_cost'] = $_REQUEST['inventory_add']['unit_cost'];
            $inventory['unit_currency'] = $_REQUEST['inventory_add']['unit_currency'];
            $inventory['unit_freight'] = $_REQUEST['inventory_add']['unit_freight'];
            $inventory['unit_freight_currency'] = $_REQUEST['inventory_add']['unit_freight_currency'];
            $inventory['other_cost'] = $_REQUEST['inventory_add']['other_cost'];
            $inventory['other_cost_currency'] = $_REQUEST['inventory_add']['other_cost_currency'];
            $inventory['warehouse_id'] = $_REQUEST['inventory_add']['warehouse'];
            $inventory['total_cost'] = $_REQUEST['inventory_add']['total_cost'];
            $inventory['currency'] = 'USD';
            $inventory['stock_left'] = intval($_REQUEST['inventory_add']['movement']);
            $inventory['currency']=$_REQUEST['inventory_add']['unit_currency'];
            $inventory['supplier_id']= intval($_REQUEST['inventory_add']['supplier_id']);
            productInventorySingleToDatabase($inventory);
            // add stok data as per warehouse 
            $data_stock=[];
            $data_stock['product_id']=$id;
            $data_stock['color_id']=0;
            $data_stock['warehouse_id'] = $_REQUEST['inventory_add']['warehouse'];
            $data_stock['qty']=inventoryStockGetValue($id,0,'qty',$_REQUEST['inventory_add']['warehouse']) +  intval($_REQUEST['inventory_add']['movement']);
            $data_stock['supplier_id']=intval($_REQUEST['inventory_add']['supplier_id']);
            $data_stock['factory_id']=$_REQUEST['inventory_add']['factory_id'][$key];
            $data_stock['when_sold_out'] = 'active';
            $data_stock['user_id']=$_GLOBALS['staff']['id'];
            inventoryStockadd_update($data_stock);
            // update supplier price and adda data in consignment listing.
            $supplierData = $lastentry = $DB->query_first(sprintf(
                "SELECT * FROM `arcooda_www`.`supplier_product` WHERE `supplier_id` = %d AND `product_id` = %d  LIMIT 1",
                $inventory['supplier_id'],
                 $inventory['product_id']
            ), MYSQLI_ASSOC);
            if($supplierData){
                $supplierData['unit_cost_price'] = $inventory['unit_cost'];
                $supplierData['unit_sup_currency'] = $inventory['unit_currency'];
                $supplierData['cost_price_usd'] =  convertCurrency( $inventory['unit_cost'], $inventory['unit_currency'], 'USD');
                $supplierData['cost_price_freight'] =  $inventory['unit_freight'];
                $supplierData['sup_currency_freight'] =  $inventory['unit_freight_currency'];
                $supplierData['cost_price_freight_usd'] = convertCurrency( $inventory['unit_freight'], $inventory['unit_freight_currency'], 'USD');
                $supplierData['cost_price'] =  $supplierData['cost_price_freight_usd']+$supplierData['cost_price_usd'];
                $suppliersHistory = productSuppliersHistoryFromPost([$supplierData]);
                unset($suppliersHistory['created_at']);
                productSuppliersHistoryToDatabase($id,productFromDatabase($id), $suppliersHistory);
                productSuppliersToDatabase($id, productSuppliersFromPost([$supplierData],'product'));
            }

        }else{
            foreach($_REQUEST['inventory_add']['color'] as $key => $value){
                if(in_array($value,$_REQUEST['item']['colour'])){
                   /* if($_REQUEST['inventory_add']['qty'][$key]){*/
                        $inventory['product_id'] =$id;
                        $inventory['movement'] = intval($_REQUEST['inventory_add']['qty'][$key]);
                        $inventory['available'] = inventoryStockGetValue($id,$value, 'qty',$_REQUEST['inventory_add']['warehouse']) + intval($_REQUEST['inventory_add']['qty'][$key]);
                        $inventory_value=$inventory_value+$inventory['available'];
                        $inventory['unit_cost'] = $_REQUEST['inventory_add']['unit_cost'];
                        $inventory['unit_freight'] = $_REQUEST['inventory_add']['unit_freight'];
                        $inventory['unit_currency'] = $_REQUEST['inventory_add']['unit_currency'];
                        $inventory['warehouse_id'] = $_REQUEST['inventory_add']['warehouse'];
                        $inventory['stock_left'] = intval($_REQUEST['inventory_add']['qty'][$key]);
                        $inventory['color_id'] = intval($value);
                        $inventory['currency']=$_REQUEST['inventory_add']['unit_currency'];
                        $inventory['supplier_id']= intval($_REQUEST['inventory_add']['supplier_id']);
                        if($_REQUEST['inventory_add']['qty'][$key]){
                            productInventorySingleToDatabase($inventory);
                        }
                        $data_stock=[];
                        $data_stock['product_id']=$id;
                        $data_stock['color_id']=intval($value);
                        $data_stock['qty']=$inventory['available'];
                        $data_stock['supplier_id']=intval($_REQUEST['inventory_add']['supplier_id']);
                        $data_stock['factory_id']=$_REQUEST['inventory_add']['factory_id'][$key];
                        $data_stock['when_sold_out'] = $_REQUEST['inventory_add']['when_sold_out'][$key];
                        $data_stock['user_id']=$_GLOBALS['staff']['id'];
                        inventoryStockadd_update($data_stock);

                }
                if (array_key_exists('inventory_add', $_FILES)) {
                    foreach ($_FILES['inventory_add']['tmp_name'][$key] as $imagekey => $imagevalue) {
                        if($_FILES['inventory_add']['error'][$key][$imagekey] == UPLOAD_ERR_OK){
                            $image_name='/webadmin/uploads/screenshot/image/'.time().'_'.$imagekey.$id.preg_replace("/[^a-zA-Z.]+/", "",$_FILES['inventory_add']['name'][$key][$imagekey]);
                            $dst_image=$_SERVER['DOCUMENT_ROOT'].$image_name;
                            if (move_uploaded_file($_FILES['inventory_add']['tmp_name'][$key][$imagekey], $dst_image)) {
                                $pro_image=[];
                                $pro_image['product_id']=$id;
                                $pro_image['color_id']=$value;
                                $pro_image['user_id']=$_GLOBALS['staff']['id'];
                                $pro_image['caption']=$_FILES['inventory_add']['name'][$key][$imagekey];
                                $pro_image['image']=$image_name;
                                addProductImages($pro_image);
                            }
                        }
                    }
                }
            }
        }
        productSetValue($id, 'qty', $inventory_value);

        foreach (productInventoryFromDatabase($id) as $v) {
            $unit_cost = bcadd($v['unit_cost'], $v['unit_freight'], 4);
            if ($v['movement'] > 0 && $v['stock_left'] > 0 && bccomp($unit_cost, '0.0000', 4) > 0) {
                productSetValue($id, 'cost_price', $unit_cost);
                productSetValue($id, 'cost_currency', $v['unit_currency']);
                break;
            }
        }
        break;

    case 'minus_stock':
        $action = 'edit';
        if(count($_REQUEST['item']['colour'])<=1){
            if (!$_REQUEST['inventory_minus']['movement']) {
                break;
            }
        }

        $inventory=[];
        foreach (array('unit_cost', 'unit_freight') as $k1 => $v1) {
            foreach (array('_aud' => 'AUD', '_usd' => 'USD') as $k2 => $v2) {
                $inventory[$v1 . $k2] = convertCurrency($inventory[$v1], $inventory['unit_currency'], $v2, false, 4);
            }
        }
        $inventory['comments'] = trim(sprintf(
            "%s %s\n(Sold for %s%s %s)",
            $_REQUEST['inventory_minus']['comment_1'],
            $_REQUEST['inventory_minus']['comment_2'],
            getCurrencySymbol($_REQUEST['inventory_minus']['unit_currency']),
            $_REQUEST['inventory_minus']['unit_cost'],
            $_REQUEST['inventory_minus']['unit_currency']
        ));
        $inventory['unit_cost_aud']=0.0000;
        $inventory['unit_cost_usd']=0.0000;
        $inventory['unit_freight_usd']=0.0000;
        $inventory['unit_freight_aud']=0.0000;
        $inventory_value=0;
        if(count($_REQUEST['item']['colour'])<=1){
            $inventory['product_id'] =$id;
            $inventory['movement'] = 0 - intval($_REQUEST['inventory_minus']['movement']);
            $inventory_value=productGetValue($id, 'qty') - intval($_REQUEST['inventory_minus']['movement']);
            $inventory['available'] = $inventory_value;
            $inventory['unit_cost'] = $_REQUEST['inventory_minus']['unit_cost'];
            $inventory['unit_freight'] = $_REQUEST['inventory_minus']['unit_freight'];
            $inventory['unit_currency'] = $_REQUEST['inventory_minus']['unit_currency'];
            $inventory['stock_left'] =0;
            $inventory['currency']=$_REQUEST['inventory_minus']['unit_currency'];
            productInventorySingleToDatabase($inventory);
            $count = $inventory['movement'];

            foreach (productInventoryFromDatabase($id) as $v) {
                if ($v['movement'] > 0 && $v['stock_left'] > 0) {
                    if ($v['stock_left'] > $count) {
                        $v['stock_left'] -= $count;
                        $count = 0;
                        productInventorySingleToDatabase($v);
                        break;
                    } else {
                        $count -= $v['stock_left'];
                        $v['stock_left'] = 0;
                        productInventorySingleToDatabase($v);
                    }
                }
            }

            foreach (productInventoryFromDatabase($id) as $v) {
                $unit_cost = bcadd($v['unit_cost'], $v['unit_freight'], 4);
                if ($v['movement'] > 0 && $v['stock_left'] > 0 && bccomp($unit_cost, '0.0000', 4) > 0) {
                    productSetValue($id, 'cost_price', $unit_cost);
                    productSetValue($id, 'cost_currency', $v['unit_currency']);
                    break;
                }
            }

        }else{
            foreach($_REQUEST['inventory_minus']['color'] as $key => $value){
                $count=0;
                if(in_array($value,$_REQUEST['item']['colour'])){
                    if($_REQUEST['inventory_minus']['qty'][$key]){
                        $inventory['product_id'] =$id;
                        $inventory['movement'] = 0-intval($_REQUEST['inventory_minus']['qty'][$key]);
                        $inventory['available'] = inventoryStockGetValue($id,$value, 'qty')- intval($_REQUEST['inventory_minus']['qty'][$key]);
                        $count=$_REQUEST['inventory_minus']['qty'][$key];
                        $inventory_value=$inventory_value+$inventory['available'];
                        $inventory['unit_cost'] = $_REQUEST['inventory_minus']['unit_cost'];
                        $inventory['unit_freight'] = $_REQUEST['inventory_minus']['unit_freight'];
                        $inventory['unit_currency'] = $_REQUEST['inventory_minus']['unit_currency'];
                        $inventory['stock_left'] = 0;
                        $inventory['color_id'] = intval($value);
                        $inventory['currency']=$_REQUEST['inventory_minus']['unit_currency'];
                        productInventorySingleToDatabase($inventory);
                        $data_stock=[];
                        $data_stock['product_id']=$id;
                        $data_stock['color_id']=intval($value);
                        $data_stock['qty']=$inventory['available'];
                        $data_stock['supplier_id']=intval($_REQUEST['inventory_add']['supplier_id']);
                        $data_stock['user_id']=$_GLOBALS['staff']['id'];
                        inventoryStockadd_update($data_stock);
                        foreach (productInventoryFromDatabasewithcolor($id,$value) as $v) {
                            if ($v['movement'] > 0 && $v['stock_left'] > 0) {
                                if ($v['stock_left'] > $count) {
                                    $v['stock_left'] -= $count;
                                    $count = 0;
                                    productInventorySingleToDatabase($v);
                                    break;
                                } else {
                                    $count -= $v['stock_left'];
                                    $v['stock_left'] = 0;
                                    productInventorySingleToDatabase($v);
                                }
                            }
                        }

                        foreach (productInventoryFromDatabasewithcolor($id,$value) as $v) {
                            $unit_cost = bcadd($v['unit_cost'], $v['unit_freight'], 4);
                            if ($v['movement'] > 0 && $v['stock_left'] > 0 && bccomp($unit_cost, '0.0000', 4) > 0) {
                                productSetValue($id, 'cost_price', $unit_cost);
                                productSetValue($id, 'cost_currency', $v['unit_currency']);
                                break;
                            }
                        }
                    }
                }
            }
        }

        productSetValue($id, 'qty', $inventory_value);

    break;
}

//Redirect Actions
switch ($action) {
    case 'json_lock':
        $item_lock = productGetLock($id);
        if (is_null($item_lock) || $item_lock['staff_id'] == $staff_id) {
            productSetLock($id, $_GLOBALS['staff']['id']);
            $item_lock = productGetLock($id);
        }
        header('Content-Type: text/javascript');
        jecho($item_lock);
        break;

    case 'add':
        //unset($_GET['id']);s
        redirect('add.php?' . http_build_query($_GET));
        break;

    case 'edit':
        productSetLock($id, $_GLOBALS['staff']['id']);
        redirect('edit.php?' . http_build_query($_GET));
        break;

    case 'categories':
        redirect('categories.php?' . http_build_query($_GET));
        break;

    case 'admin_notes':
        redirect('comments.php?' . http_build_query($_GET));
        break;

    case 'error':
        redirect('error.php?' . http_build_query($_GET));
        trigger_error('Product Section Error');
        break;

    case 'view':
        $item = productFromDatabase($_REQUEST['id']);
        redirect($item['view_url'] . '?preview=true');
        break;

    case 'search':
        redirect('search.php?' . http_build_query($_GET));
        break;

    case 'options':
        redirect('options.php?' . http_build_query($_GET));
        break;

    case 'select':
        redirect('select.php?' . http_build_query($_GET));
        break;

    case 'stock_values':
        redirect('stock_values.php?' . http_build_query($_GET));
        break;

    case 'delete':
        redirect('delete.php?' . http_build_query($_GET));
        break;

    case 'list_print':
    case 'print':
        redirect('print.php?' . http_build_query($_GET));
        break;

    case 'export':
        redirect('exportXLS.php?' . http_build_query($_GET));
        break;

    case 'image':
        redirect('images.php?' . http_build_query($_GET));
        break;

    case 'alert':
        redirect('alert.php?' . http_build_query($_GET));
        break;

    case 'add_inventory':
        redirect('add_inventory.php?' . http_build_query($_GET));
        break;

    case 'list_inventory':
        redirect('list_inventory.php?' . http_build_query($_GET));
        break;

    case 'inventory_history':
        redirect('inventory_history.php?' . http_build_query($_GET));
        break;

    case 'view_inventory':
        redirect('view_inventory.php?' . http_build_query($_GET));
        break;

    default:
    case 'list':
        redirect('list.php?' . http_build_query($_GET));
        break;

    case 'featured':
        $_GET['status'] = $update_status;
        redirect('featured.php?' . http_build_query($_GET));
        break;
}
