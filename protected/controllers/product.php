<?php
/**
 * @var string            $action
 * @var app\models\MyTwig $twig
 */
?><?php

namespace app\controllers;
use app\helpers\Image;
use app\models\TwigCurrency;
use app\models\TwigProduct;
use app\models\TwigUser;
use app\models\TwigWantedProduct;
use app\models\TwigCategory;
use app\models\Twig360View;
use app\models\PHPMailerEmail;
use ParsedownExtra;
use app\models\CustomPDF;
use app\cache\CacheService;

$cacheService = new CacheService();

global $_GLOBALS;
global $DB;

// @todo We need a better staff preview detection method
if (!empty($_COOKIE['REMOTE_USER'] ?? false) || strpos($_SERVER['SCRIPT_URL'], 'webadmin') !== false) {
    unset($_GLOBALS['criteria_options']['product']);
}

switch ($action) {
    case 'image':
        if ($product = new TwigProduct($_GET['id'])) {

            $watermark_file = $_SERVER['DOCUMENT_ROOT'] . '/protected/watermark.png';
            $profile_file   = $_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim';
            
            $width  = intval($_GET['size'] ?? $_GET['width'] ?? 0);
            $height = intval($_GET['size'] ?? $_GET['height'] ?? 0);
            if (!in_array([$width, $height], [
                [40, 40],   // search.twig
                [96, 96],
                [50, 50],   // wishlist.twig
                [64, 64],   // widget-products.twig
                [70, 70],   // dropcart.twig, product-gallery.twig
                [150, 150], // compare.twig
                [160, 160], // wishlist.twig, cart.twig
                [245, 245], // product-card.twig
                [500, 500], // product-gallery.twig
                [700, 700], // product-gallery.twig
            ])) {
                $width = $height = 250; // order-success.twig
            }

            $image_file = $product->image_file();
            if(isset($_GET['original'])){
                Image::HTTPSendFile($image_file);
            }
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }
            
            $cache_file = sprintf(
                '%s/protected/cache/product/image/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $product->id,
                ($width) ? $width : '',
                ($height) ? $height : ''
            );
            if (is_file($cache_file)) {
                Image::HTTPSendFile($cache_file);
                die();
            }
            Image::copyToStatic(
                $image_file,
                $cache_file,
                $profile_file,
                $watermark_file,
                $width,
                $height
            );
            if (!is_file($cache_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            Image::HTTPSendFile($cache_file);
            die();
        }
        break;

    case 'colour_image':
        global $DB;

        $attach = $cacheService->getData('products_images', $_GET['id']);
        if($attach == false){
            $attach = $DB->query_first(sprintf(
                "SELECT * FROM `products_images` AS `ma` WHERE `ma`.`id` = %d AND `active` = 'y' LIMIT 1",
                $_GET['id']
            ), MYSQLI_ASSOC);
             $cacheService->updateData('products_images', $_GET['id'], $attach);
        }

        if ($products = TwigProduct::search(['id' => $attach['product_id']])) {
            $product = reset($products);

            $watermark_file = $_SERVER['DOCUMENT_ROOT'] . '/protected/watermark.png';
            $profile_file   = $_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim';

            $width  = intval($_GET['size'] ?? $_GET['width'] ?? 0);
            $height = intval($_GET['size'] ?? $_GET['height'] ?? 0);
            if (!in_array([$width, $height], [
                [40, 40],   // search.twig
                [50, 50],   // wishlist.twig
                [64, 64],   // widget-products.twig
                [70, 70],   // dropcart.twig, product-gallery.twig
                [150, 150], // compare.twig
                [160, 160], // wishlist.twig, cart.twig
                [245, 245], // product-card.twig
                [500, 500], // product-gallery.twig
                [700, 700], // product-gallery.twig
            ])) {
                $width = $height = 250; // order-success.twig
            }

            $image_file = sprintf(
                "%s/%s",
                $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/screenshot/image',
                basename($attach['image'])
            );
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            $cache_file = sprintf(
                '%s/protected/cache/product/colour/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $attach['id'],
                ($width) ? $width : '',
                ($height) ? $height : ''
            );
            Image::copyToStatic(
                $image_file,
                $cache_file,
                $profile_file,
                $watermark_file,
                $width,
                $height
            );
            if (!is_file($cache_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            Image::HTTPSendFile($cache_file);
            die();
        }
        break;

    case 'view':
        moduleLoad('product'); //productFromDatabase
        global $_GLOBALS;

        if (!empty($_POST)) {
            $product_qty = 0;
            $product_id  = $_POST['product_id'];
            if (isset($_POST['product_qty']) && empty($_POST['product_qty']) === false) {
                $product_qty = intval($_POST['product_qty']);
            }
            if (productExists($product_id)) {
                $product               = productFromDatabase($product_id);
                $product['product_id'] = $product['id'];
                // Hack:
                // sub_id, back_qty, power_id, adapter_id not exist in product
                // But, used in cart. Throwing errors.
                $product['sub_id']     = 0;
                $product['back_qty']   = 0;
                $product['colour_id']  = (isset($_POST['color_cart']) && $_POST['color_cart'] > 0) ? $_POST['color_cart'] : 0;
                 $product['sub_product_id']  = (isset($_POST['sub_pro']) && $_POST['sub_pro'] > 0) ? $_POST['sub_pro'] : 0;
                $product['power_id']   = 0;
                $product['adapter_id'] = 0;

                $curentprice           = $product['sales_price'];
                if (array_key_exists('member', $_GLOBALS)) {
                    $user = new TwigUser($_GLOBALS['member']['member_id']);
                    if ($user->wholesale() == 'y') {
                        $bulkprice = productBulkPricingFromDatabase($product['id']);

                        if (count($bulkprice)) {
                            foreach ($bulkprice as $bulkdata) {
                                if ($product_qty >= $bulkdata['qty'] && $curentprice > $bulkdata['unit_sale_price']) {
                                    $curentprice = number_format((float) $bulkdata['unit_sale_price'], 2, '.', '');
                                }
                            }
                        }
                    }
                }
                if(isset($_POST['sub_pro']) && $_POST['sub_pro'] > 0){
                      $row = $DB->query_first(sprintf("SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d AND `deleted_at` IS NULL LIMIT 1",
                            '`product_sub_module`',
                            'id',
                            $_POST['sub_pro'],
                        ));
                      $productsobj = reset(TwigProduct::search(['id' => $product['id']]));
                      //print_r($productsobj); die;
                     $product['sales_price'] = $productsobj->SubProductSalesPrice($row);
                }else{
                    $product['sales_price'] = $curentprice;
                }
                if ('n' != $product['active']) {
                    $cart = $_GLOBALS['shopping_cart'];
                    $cart=array_merge($cart,$_GLOBALS['quote_cart']);
                    //echo '<pre>'; print_r($_GLOBALS['quote_cart']); die;
                    if ($product_qty < 0) {
                        $cart = cartAddToContents($cart, $product, 0 - $product_qty);
                    }
                    if ($product_qty > 0) {
                        $cart = cartAddToContents($cart, $product, $product_qty);
                    }
                    if (0 == $product_qty) {
                        $cart = cartAddToContents($cart, $product, 1);
                    }
                    foreach ($cart as $cart_key => $cart_item) {
                        $min_order = productGetValue($cart_item['product_id'], 'min_order');
                        if ($cart_item['product_qty'] < $min_order) {
                            $cart_item['product_qty']       = $min_order;
                            $cart[$cart_key]['product_qty'] = $min_order;
                        }
                        $cart[$cart_key]['product_offer']    = $product['sales_price'];
                        $cart[$cart_key]['product_offer_us'] = $cart[$cart_key]['product_offer'];
                        $cart[$cart_key]['offer_currency']   = 'USD';
                    }
                    //$_GLOBALS['shopping_cart'] = $cart;
                    cartSaveContents($_GLOBALS['member']['id'] ?? 0, $cart);
                    $rdirect['value']   = 'y';
                    $rdirect['message'] = 'Product added successfully in cart';
                    $data = json_encode($rdirect) ?: '';
                    setcookie('redirect', $data, strtotime('+1 year') ?: 0, '/');
                    insertLogDataInShoppingCart('shopping');
                }
                if(isset($_POST['linkpro']) && count($_POST['linkpro'])){
                    foreach ($_POST['linkpro'] as $key => $pro) {
                        addProductToCart($pro);
                    }
                }
                if(isset($_POST['options_ids']) && count($addonpro = explode(',',$_POST['options_ids']))){
                    foreach ($addonpro as $key => $pro) {
                        addProductToCart($pro);
                    }
                }

                if(isset($_POST['bracketoption_id']) && $_POST['bracketoption_id'] != 'discount'){
                    $bracket =  productAddonBracketFromDatabase($_POST['bracketoption_id']);
                    if (productExists($bracket['related_id'])) {
                        if( $bracket['is_free'] == 'y'){

                            addProductToCart($bracket['related_id'],$bracket['is_free'],$product_id); 
                        }else{
                            addProductToCart($bracket['related_id']);
                        }
                    }
                }
                 if(isset($_POST['bracketoption_id']) && $_POST['bracketoption_id'] == 'discount'){
                      session_start();
                      $_SESSION['bracket-discount'][$product_id] = 'y';
                 }
                // addon free product need to add in cart
                foreach (productAddonsFromDatabase($product_id) as $addonPro){
                    if (productExists($addonPro['related_id']) && $addonPro['is_free'] == 'y') {
                        addProductToCart($addonPro['related_id'],$addonPro['is_free'],$product_id);
                    }
                }
            }
            redirect($_SERVER['REQUEST_URI']);
            die();
        }
        if ($products = TwigProduct::search(['id' => $_GET['id']])) {

            $product  = reset($products);
            $view360=($product->get360viewID())?new Twig360View($product->get360viewID()):null;
            $template = 'product-view.twig';
            if (array_key_exists('source', $_GET) && 'quickview' === $_GET['source'] &&
                array_key_exists('format', $_GET) && 'ajax' === $_GET['format'] &&
                array_key_exists('HTTP_REFERER', $_SERVER) &&
                strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) !== false
            ) {
                $template = 'product-quickview.twig';
            }

            $value   = '';
            $message = '';
            if (is_object($_GLOBALS['redirect'] ?? null)) {
                $value   = $_GLOBALS['redirect']->value;
                $message = $_GLOBALS['redirect']->message;
            }
            die($twig->load($template)->render(['inputs' => [
                'bracket' => $_GET['bracket'] ?? 'n',
                'result'  => [
                    'y' => 'success',
                    'n' => 'danger',
                ][$value] ?? '',
                'alert'   => [
                    'y' => $message,
                    'n' => $message,
                ][$value] ?? '',
                'product' => $product,
                'view360' => $view360
            ]]));
        }
        break;

    case 'screenshot':
        moduleLoad('product');

        //@fixme Just give me one product section please
        include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products_new/query.inc.php';

        //@todo if ($screenshots = TwigProductScreenshot::search(['ss_id' => $_GET['id']])) {
        if ($screenshots = [productScreenshotFromDatabase($_GET['id'])]) {
            $screenshot = reset($screenshots);
        
            if (!TwigProduct::search(['id' => $screenshot['id']])) {
                break;
            }

            $watermark_file = $_SERVER['DOCUMENT_ROOT'] . '/protected/watermark.png';
            $profile_file   = $_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim';

            $width  = intval($_GET['size'] ?? $_GET['width'] ?? 0);
            $height = intval($_GET['size'] ?? $_GET['height'] ?? 0);
            if (!in_array([$width, $height], [
                [70, 70],   // product-gallery.twig
                [245, 245], // product-card.twig
                [500, 500], // product-gallery.twig
                [700, 700], // product-gallery.twig
            ])) {
                $width = $height = 250; // order-success.twig
            }

            //@todo $image_file = $screenshot->image_file();
            $image_file = productGetOptionList('screenshot_path') . '/' . basename($screenshot['filename']);
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            $cache_file = sprintf(
                '%s/protected/cache/product/screenshot/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $screenshot['ss_id'], //@todo $screenshot->id
                ($width) ? $width : '',
                ($height) ? $height : ''
            );
            Image::copyToStatic(
                $image_file,
                $cache_file,
                $profile_file,
                $watermark_file,
                $width,
                $height
            );
            if (!is_file($cache_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            Image::HTTPSendFile($cache_file);
            die();
        }
        break;

    case 'compare_product':
        moduleLoad('product');

        global $DB;

        $_SESSION['compare_message'] = '';
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            if (isset($_POST['compare']) && $_POST['product_id'] > 0) {
                $visitor_id = cartGetVisitorID();
                $product_id = intval($_POST['product_id']);
                if (productExists($product_id)) {
                    $row = $DB->query_first("SELECT `id` FROM compare_product` WHERE `visitor_id` = " . $visitor_id . " AND `product_id` = " . $product_id . " LIMIT 1");
                    if (!isset($row['id'])) {
                        $DB->query(sprintf(
                            "INSERT INTO `compare_product` (`product_id`, `visitor_id`, `status`, `created_at`) VALUES (%d, %s, %d,NOW())",
                            $product_id,
                            $visitor_id,
                            1
                        ));
                        $_SESSION['compare_message'] = 'Compare product added successfully';
                    }
                }
            } elseif (isset($_POST['wishlist']) && $_POST['product_id'] > 0) {

                $visitor_id = cartGetVisitorID();

                //FIXME: visitor/member can't use the same table => id conflicts
                //$visitor_id = (isset($_GLOBALS['member']['member_id'])) ? $_GLOBALS['member']['member_id'] : cartGetVisitorID();

                $product_id = intval($_POST['product_id']);
                if (productExists($product_id)) {

                    //FIXME: Just updated the table with unique key and use INSERT IGNORE
                    if (!$DB->query_first(sprintf(
                        "SELECT `id` FROM  `wishlist_product` WHERE `visitor_id` = %d AND `product_id` = %d LIMIT 1",
                        $visitor_id,
                        $product_id
                    ), MYSQLI_ASSOC)) {
                        $DB->query(sprintf(
                            "INSERT INTO `wishlist_product` (`product_id`, `visitor_id`, `status`, `created_at`) VALUES (%d, %s, %d, NOW())",
                            $product_id,
                            $visitor_id,
                            1
                        ));
                        $_SESSION['compare_message'] = 'Wishlist product added successfully';
                    }
                }

            }
        }
        redirect($_SERVER['HTTP_REFERER']);
        die();

    case 'compare':
        global $DB;
        $_SESSION['compare_message'] = null;
        $visitor_id                  = cartGetVisitorID();
        $product                     = TwigProduct::getcompareproduct($visitor_id);
        $breadcrumbs                 = [['title' => 'Home', 'url' => '/'], ['title' => 'Compare', 'url' => '/p/compare']];

        die($twig->load('compare.twig')->render(['inputs' => [
            'page_title'       => 'Compare Products',
            'page_description' => 'Compare Products',
            'page_keywords'    => 'Compare Products',
            'page_canonical'   => '/p/compare',
            'page_breadcrumbs' => $breadcrumbs,
            'products'         => $product,
        ]]));

    case 'removecompare':
        global $DB;
        $visitor_id = cartGetVisitorID();
        $query      = $DB->query("SELECT `id`,product_id FROM `compare_product` WHERE `visitor_id` = " . $visitor_id);
        while ($row = $DB->fetch_assoc($query)) {
            if (md5($row['product_id']) == trim($_GET['id'])) {
                $DB->query("DELETE FROM  compare_product WHERE id=" . intval($row['id']));
                $_SESSION['compare_message'] = 'product successfully removed form compare list';
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
        die();

    case 'wishlist':
        global $DB;

        $_SESSION['compare_message'] = null;

        $visitor_id = cartGetVisitorID();
        //FIXME: visitor/member can't use the same table => id conflicts
        //$visitor_id = (isset($_GLOBALS['member']['member_id'])) ? $_GLOBALS['member']['member_id'] : cartGetVisitorID();

        $product     = TwigProduct::getwishlistproduct($visitor_id);
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Wishlist', 'url' => '/p/wishlist'],
        ];
        die($twig->load('wishlist.twig')->render(['inputs' => [
            'page_title'       => 'Wishlist Products',
            'page_description' => 'Wishlist Products',
            'page_keywords'    => 'Wishlist Products',
            'page_canonical'   => '/p/wishlist',
            'page_breadcrumbs' => $breadcrumbs,
            'products'         => $product,
        ]]));

    case 'removewishlist':
        global $DB;
        $visitor_id = cartGetVisitorID();
        $query      = $DB->query("SELECT `id`,product_id FROM `wishlist_product` WHERE `visitor_id` = " . $visitor_id);
        while ($row = $DB->fetch_assoc($query)) {
            if (md5($row['product_id']) == trim($_GET['id'])) {
                $DB->query("DELETE FROM  wishlist_product WHERE id=" . intval($row['id']));
                $_SESSION['compare_message'] = 'product successfully removed form wish list';
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
        die();
    break;
    case 'wanted':
        $criteria=[];
        $criteria['approved']='y';
        $criteria['active']='y';
        if (array_key_exists('ct', $_REQUEST)) { 
                foreach ($_REQUEST['ct'] as $v) {
                    $criteria['section_id'][] = $v;
                }
        }
        foreach ([
            's'  => 'any_text',
            'cl' => 'online_pref_location',
            'm'  => 'manufacturer',
            'c'  => 'condition',
        ] as $k1 => $v1) {
            if (array_key_exists($k1, $_REQUEST)) {
                if (is_array($_REQUEST[$k1])) {
                    foreach ($_REQUEST[$k1] as $v2) {
                        $criteria[$v1][] = $v2;
                    }
                } else {
                    $criteria[$v1][] = $_REQUEST[$k1];
                }
            }
        }
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Wanted', 'url' => '/wanted'],
        ];
        $products = TwigWantedProduct::search($criteria);
        $sort                 = 'Latest Items';
            //$show    = 15;
            $show    = (isset($_GET['show']) && !empty($_GET['show'])) ? intval($_GET['show']) : 32;
            $page    = 0;
            $sort_by = 'id';
            $order   = 'desc';
            if (isset($_GET['sort'])) {
                $sort = $_GET['sort'];

                switch ($sort) {
                    case 'Product Name':
                        $sort_by = 'item';
                        $order   = 'asc';
                        break;

                    default:
                        $sort_by = 'id';
                        $order   = 'desc';
                        break;
                }
            }
            $total = count($products);
            if ('id' !== $sort_by || 'desc' !== $order) {
                foreach ($products as $k => $v) {
                    $products[$k] = $v->id;
                }
                $products = TwigWantedProduct::list(wantedSort($products, $sort_by));
                if ('asc' !== $order) {
                    $products = array_reverse($products);
                }
            }
            if (array_key_exists('show', $_REQUEST)) {
                $show = min(112, max(1, intval($_REQUEST['show'])));
            }

            $products = array_chunk($products, $show);
            $pages    = count($products);
            if ($pages) {
                if (array_key_exists('page', $_REQUEST)) {
                    $page = min(count($products) - 1, max(0, intval($_REQUEST['page'])));
                }
                $products = $products[$page];
            }
            $page_url = $_REQUEST;
            unset($page_url['page']);
            asort($page_url);
            $page_url['page'] = '';
            $page_url         = '/wanted/' . '?' . http_build_query($page_url);
            $filters          = [];
            $items            = [];
            $filter_url       = $_REQUEST;
            unset($filter_url['page']);
            $filter_url   = '?' . http_build_query($filter_url);
            $category    = TwigCategory::search(['parent'=>0]);
            $items        = [];
            $sub_criteria = $criteria;
            unset($sub_criteria['section_id']);
            $temp = wantedChunk(wantedSearch($sub_criteria), 'section_id');
            foreach ($category  as $k => $v) {
                if (array_key_exists($v->id, $temp)) {
                    $items[] = [
                        'title'    => $v->name(),
                        'count'    => count($temp[$v->id]),
                        'checked'  => (array_key_exists('section_id', $criteria) && in_array($v->id, $_REQUEST['ct'])),
                        'disabled' => false,
                        'name'     => 'ct[]',
                        'value'    => $v->id,
                    ];
                }
            }

            if (count($items)) {
                $filters[] = [
                    "title"   => "Category",
                    "type"    => "checkbox",
                    "options" => ['items' => $items],
                ];
            }
            $items        = [];
            $sub_criteria = $criteria;
            unset($sub_criteria['online_pref_location']);
            $temp = wantedChunk(wantedSearch($sub_criteria), 'online_pref_location');
            foreach (getCountryList() as $k => $v) {
                if (array_key_exists($k, $temp)) {
                    $items[] = [
                        'title'    => explode('-',$v)[0],
                        'count'    => count($temp[$k]),
                        'checked'  => (array_key_exists('online_pref_location', $criteria) && in_array($k, $criteria['online_pref_location'])),
                        'disabled' => false,
                        'name'     => 'cl[]',
                        'value'    => $k,
                    ];
                }
            }
            if (count($items)) {
                $filters[] = [
                    "title"   => "Country",
                    "type"    => "checkbox",
                    "options" => ['items' => $items],
                ];
            }
          die($twig->load('wanted.twig')->render(['inputs' => [
            'page_title'       => 'Wanted Products',
            'page_description' => 'Wanted Products',
            'page_keywords'    => 'Wanted Products',
            'page_canonical'   => '/wanted',
            'sort'             => $sort,
            'show'             => $show,
            'page'             => $page,
            'pages'            => $pages,
            'total'            => $total,
            'page_url'         => $page_url,
            'filters'          => $filters,
            'page_breadcrumbs' => $breadcrumbs,
            'products'         => $products,
        ]]));
    break;
    case 'review':
        global $_GLOBALS;
        moduleLoad('review');
        moduleLoad('product');
        $data = '';
        $issues = array();
        foreach ([
            'product_id',
            'user_name',
            'reviews',
            'comment',
        ] as $k) {
            if ('' === trim($posted ?? '')) {
                $issues[$k] = '' . str_replace('_',' ', $k) . ' field is required';
            }
        }

        if(count($issues)==0 && $_GLOBALS['member']['id'] > 0){
            $posted = reviewFromPost($posted);
            $posted['item'] = $posted;
            $posted['action'] = 'do_add';
            $reviewId = reviewToDatabase($posted);
                if (!$reviewId) {
                    $issues['db_error'] = 'Database insertion failed: ' . $DB->error;
                }
        }

        if($reviewId) {
            $reviews = getProductReviews($posted['product_id'],0);
            $product  = new TwigProduct($posted['product_id']);
            $responseHtml = $twig->load('components/review-template.twig')->render(['inputs' => [
                'reviews'       => $reviews['reviews'],
                'newreview' => true,
                'productname' => $product->name
            ]]);
            $paginationHtml = $twig->load('/components/pagination-new.twig')->render(['inputs' => [
                'page'       => $reviews['page'],
                'page_url'   => $reviews['page_url'],
                'pages'      => $reviews['pages'],
            ]]);
            ob_start();
            ?>
            <p>New review posted by <?= $_GLOBALS['member']['full_name'] ?> for the product <?= $product->name ?></p>
            <p>Rating : <?= $posted['reviews']?></p>
            <p>Review : <?= $posted['comment']?></p>
            <?php
            $message = ob_get_clean();
            $email  = new PHPMailerEmail();
            $email->Subject  = "New review posted by ".$_GLOBALS['member']['full_name']."";
            $email->From     = $_GLOBALS['site_settings']['email_sales'];
            $email->FromName = 'Alert - Review';
            //$email->Body     = sprintf(ParsedownExtra::instance()->setBreaksEnabled(true)->text($message), htmlspecialchars($history['message_raw']));
            $email->Body     = $message;
            $email->AltBody  = $message;// sprintf($message, $history['message_raw']);
            $email->AddAddress($_GLOBALS['site_settings']['email_sales'], $_GLOBALS['site_settings']['site_name']);
            //$email->AddAddress('vivek@yopmail.com', 'Arcooda');
            $email->Send();
            // Render the review template
            die(json_encode([
                'html' => $responseHtml,
                'paginationHtml' => $paginationHtml,
                'success'   => true,
            ]));
        }

        die(json_encode([
            'html' => '',
            'paginationHtml' => '',
            'success'   => false,
        ]));
    break;
    case 'getreviws':
        if (isset($_GET['product_id']) && isset($_GET['page'])) {
                moduleLoad('product');
                $product_id = intval($_GET['product_id']);
                $offest = intval($_GET['page']);
                $reviews = getProductReviews($product_id,$offest);
                $responseHtml = $twig->load('components/review-template.twig')->render(['inputs' => [
                    'reviews'       => $reviews['reviews'],
                ]]);
                $paginationHtml = $twig->load('/components/pagination-new.twig')->render(['inputs' => [
                    'page'       => $reviews['page'],
                    'page_url'   => $reviews['page_url'],
                    'pages'      => $reviews['pages'],
                ]]);
                // Render the review template
                die(json_encode([
                    'html' => $responseHtml,
                    'paginationHtml' => $paginationHtml,
                    'success'   => true,
                ]));
            
            } else {
                die(json_encode([
                    'html' => '',
                    'paginationHtml' => '',
                    'success'   => false,
                ]));
            }
        break;
     case 'reviewimage':

        $screenshot = $cacheService->getData('product_reviews_images', $_GET['id']);
        if($screenshot == false){
            $screenshot = $DB->query_first(sprintf("SELECT *  FROM %s  Where `%s` = %d  LIMIT 1",
                '`product_reviews_images`',
                'id',
                $_GET['id'],
            ));
            $cacheService->updateData('product_reviews_images', $_GET['id'], $screenshot);
        }
        if ($screenshot) {
            $watermark_file = $_SERVER['DOCUMENT_ROOT'] . '/protected/watermark.png';
            $profile_file   = $_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim';

            $width  = intval($_GET['size'] ?? $_GET['width'] ?? 0);
            $height = intval($_GET['size'] ?? $_GET['height'] ?? 0);
            if (!in_array([$width, $height], [
                [70, 70],   // product-gallery.twig
                [245, 245], // product-card.twig
                [500, 500], // product-gallery.twig
                [700, 700], // product-gallery.twig
            ])) {
                $width = $height = 250; // order-success.twig
            }

            //@todo $image_file = $screenshot->image_file();
            $image_file = $_SERVER['DOCUMENT_ROOT'] . '/images/review/' . $screenshot['name'];
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }
            $cache_file = sprintf(
                '%s/protected/cache/review/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $screenshot['id'], //@todo $screenshot->id
                ($width) ? $width : '',
                ($height) ? $height : ''
            );
            Image::copyToStatic(
                $image_file,
                $cache_file,
                $profile_file,
                $watermark_file,
                $width,
                $height
            );
            if (!is_file($cache_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            Image::HTTPSendFile($cache_file);
            die();
        }
     break;
     case 'monitor-comparison':
            global $DB;
            $compareProduct = [];
            $helpText=[];
            $orderby = '';
            $includePrice = ['size','ratio'];
           $today = date("'Y-m-d 00:00:00'"); 
           $sortBy = $_GET['sortby'] ?? 'size';
            $choices = array(
            'name' => 'ORDER BY p.product_name',
            'size'   => 'ORDER BY msize.data_value',
            'ratio' => 'ORDER BY mratio.name',
            'price'  => "ORDER BY IF(`p`.`special_flag` = 'y' AND `p`.`special_priceus` > 0.0 AND (`p`.`special_expiry` >= " . $today . " OR `p`.`special_expiry` = '0000-00-00 00:00:00'), `p`.`special_priceus`, IF(`p`.`price_flux` = 'y', IF(`p`.`stock_type` = 'consignment', `p`.`consignment_cost_price`, `p`.`cost_price`) * (IF(`p`.`stock_type` = 'consignment', `p`.`consignment_markup`, `p`.`markup`) + 100.0) / 100.0, IF(`p`.`stock_type` = 'consignment', `p`.`consignment_sales_price`, `p`.`sales_price`)))",
                'id'   => 'p.id',
            );
            if(isset($sortBy) && isset($choices[$sortBy])){
                $orderby = $choices[$sortBy];
            }
            if(in_array($sortBy, $includePrice)){
                $orderby =$orderby.','."IF(`p`.`special_flag` = 'y' AND `p`.`special_priceus` > 0.0 AND (`p`.`special_expiry` >= " . $today . " OR `p`.`special_expiry` = '0000-00-00 00:00:00'), `p`.`special_priceus`, IF(`p`.`price_flux` = 'y', IF(`p`.`stock_type` = 'consignment', `p`.`consignment_cost_price`, `p`.`cost_price`) * (IF(`p`.`stock_type` = 'consignment', `p`.`consignment_markup`, `p`.`markup`) + 100.0) / 100.0, IF(`p`.`stock_type` = 'consignment', `p`.`consignment_sales_price`, `p`.`sales_price`)))";
            }
            $query    = $DB->query("SELECT mov.product_id FROM monitor_options_value AS mov JOIN products AS p ON mov.product_id = p.id LEFT JOIN product_monitor_size  AS msize ON  mov.monitor_size = msize.id LEFT JOIN monitor_ratio AS mratio ON  p.monitor_ratio = mratio.id WHERE mov.is_comparable = 'yes' AND p.active = 'y'  ".$orderby);
            while ($row = $DB->fetch_assoc($query)) {
               $compareProduct[]= new TwigProduct($row['product_id']);
            }
             $query    = $DB->query("SELECT * FROM `monitor_help_text`");
            while ($row = $DB->fetch_assoc($query)) {
               $helpText[$row['option_key']]=$row['support_text'];
            }
            $breadcrumbs = [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Monitor Comparison', 'url' => '/arcooda-monitor-comparison-table.html'],
            ];
            $template = 'compaire-monitors.twig';
            if($_GET['is_admin']){
                $template = 'compaire-monitors-admin.twig';
            }
            die($twig->load($template)->render(['inputs' => [
                'page_title'       => 'Arcooda Monitor Comparison Table',
                'page_description' => 'Arcooda Monitor Comparison Table',
                'page_keywords'    => 'Arcooda Monitor Comparison Table',
                'page_canonical'   => '/arcooda-monitor-comparison-table.html',
                'page_breadcrumbs' => $breadcrumbs,
                'products'         => $compareProduct,
                'helpText'         => $helpText,
                'sortby'           => $sortBy
            ]]));
     break;
     case 'download-monitor-comparison':
            global $DB;
            $compareProduct = [];
            $productName = [];
            $helpText=[];
            $orderby = '';
            $includePrice = ['size','ratio'];
           $today = date("'Y-m-d 00:00:00'"); 
           $sortBy = $_GET['sortby'] ?? 'size';
            $choices = array(
            'name' => 'ORDER BY p.product_name',
            'size'   => 'ORDER BY msize.data_value',
            'ratio' => 'ORDER BY mratio.name',
            'price'  => "ORDER BY IF(`p`.`special_flag` = 'y' AND `p`.`special_priceus` > 0.0 AND (`p`.`special_expiry` >= " . $today . " OR `p`.`special_expiry` = '0000-00-00 00:00:00'), `p`.`special_priceus`, IF(`p`.`price_flux` = 'y', IF(`p`.`stock_type` = 'consignment', `p`.`consignment_cost_price`, `p`.`cost_price`) * (IF(`p`.`stock_type` = 'consignment', `p`.`consignment_markup`, `p`.`markup`) + 100.0) / 100.0, IF(`p`.`stock_type` = 'consignment', `p`.`consignment_sales_price`, `p`.`sales_price`)))",
                'id'   => 'p.id',
            );
            if(isset($sortBy) && isset($choices[$sortBy])){
                $orderby = $choices[$sortBy];
            }
            if(in_array($sortBy, $includePrice)){
                $orderby =$orderby.','."IF(`p`.`special_flag` = 'y' AND `p`.`special_priceus` > 0.0 AND (`p`.`special_expiry` >= " . $today . " OR `p`.`special_expiry` = '0000-00-00 00:00:00'), `p`.`special_priceus`, IF(`p`.`price_flux` = 'y', IF(`p`.`stock_type` = 'consignment', `p`.`consignment_cost_price`, `p`.`cost_price`) * (IF(`p`.`stock_type` = 'consignment', `p`.`consignment_markup`, `p`.`markup`) + 100.0) / 100.0, IF(`p`.`stock_type` = 'consignment', `p`.`consignment_sales_price`, `p`.`sales_price`)))";
            }
            $query    = $DB->query("SELECT mov.product_id,p.product_name FROM monitor_options_value AS mov JOIN products AS p ON mov.product_id = p.id LEFT JOIN product_monitor_size  AS msize ON  mov.monitor_size = msize.id LEFT JOIN monitor_ratio AS mratio ON  p.monitor_ratio = mratio.id WHERE mov.is_comparable = 'yes' AND p.active = 'y'  ".$orderby);
            while ($row = $DB->fetch_assoc($query)) {
               $product = new TwigProduct($row['product_id']);
               $compareProduct[] = $product;
               $productName[] = [$row['product_name'],'https://www.arcooda.com'.$product->images()[0]->src];
            }
             $query    = $DB->query("SELECT * FROM `monitor_help_text`");
            while ($row = $DB->fetch_assoc($query)) {
               $helpText[$row['option_key']]=$row['support_text'];
            }
            $usableWidth = 297 - 15 - 15; // page width minus margins
            $prodCount = count($productName);
            $prodWidth = ($usableWidth - 40) / $prodCount; 
            $tablehtml = $twig->load('compare-monitor-download.twig')->render(['inputs' => [
                'page_title'       => 'Arcooda Monitor Comparison Table',
                'page_description' => 'Arcooda Monitor Comparison Table',
                'page_keywords'    => 'Arcooda Monitor Comparison Table',
                'page_canonical'   => '/arcooda-monitor-comparison-table.html',
                'page_breadcrumbs' => $breadcrumbs,
                'products'         => $compareProduct,
                'helpText'         => $helpText,
                'sortby'           => $sortBy,
                'prodWidth'        => $prodWidth
            ]]);   
            $pdf = new CustomPDF('L', 'mm', 'A4', true, 'UTF-8', false);

            $pdf->setPrintHeader(true);
            $pdf->setPrintFooter(true);

            $pdf->productNames = $productName;

            $pdf->SetMargins(15, 75, 15);  // more space and clean left border
            $pdf->SetHeaderMargin(10);
            $pdf->SetFooterMargin(15);

            $pdf->SetAutoPageBreak(TRUE, 25);

            $pdf->AddPage();

            $pdf->SetFont('helvetica', '', 9);
            $pdf->writeHTML($tablehtml, true, false, false, false);

            $pdf->Output('compare-products.pdf', 'I');
            die;
        break;
}
function addProductToCart($product_id , $isFree = 'n' ,$freeWithProduct = 0){
    moduleLoad('product');

    global $_GLOBALS;
    if (productExists($product_id)) {
        $product               = productFromDatabase($product_id);
        $product_qty = productGetValue($product_id, 'min_order');
        $product['product_id'] = $product['id'];
        // Hack:
        // sub_id, back_qty, power_id, adapter_id not exist in product
        // But, used in cart. Throwing errors.
        $product['sub_id']     = 0;
        $product['back_qty']   = 0;
        $product['colour_id']  = 0;
        $product['sub_product_id']=0;
        $product['power_id']   = 0;
        $product['adapter_id'] = 0;
        $product['is_free'] = $isFree;
        if($isFree){
            $product['order_rule'] = 'invoice_for_everyone';
        }
        $product['free_with_product'] = $freeWithProduct;
        $curentprice = $product['sales_price'];
        if (array_key_exists('member', $_GLOBALS)) {
            $user = new TwigUser($_GLOBALS['member']['member_id']);
            if ($user->wholesale() == 'y') {
                $bulkprice = productBulkPricingFromDatabase($product['id']);

                if (count($bulkprice)) {
                    foreach ($bulkprice as $bulkdata) {
                        if ($product_qty >= $bulkdata['qty'] && $curentprice > $bulkdata['unit_sale_price']) {
                            $curentprice = number_format((float) $bulkdata['unit_sale_price'], 2, '.', '');
                        }
                    }
                }
            }
        }
        
        $product['sales_price'] = $curentprice;
        
        if ('n' != $product['active']) {
            $member_id=$_GLOBALS['member']['id'] ?? 0;
            $cart = cartLoadContents($member_id,0,'shopping');
            $cart=array_merge($cart,cartLoadContents($member_id,0,'quote'));
            if ($product_qty < 0) {
                $cart = cartAddToContents($cart, $product, 0 - $product_qty);
            }
            if ($product_qty > 0) {
                $cart = cartAddToContents($cart, $product, $product_qty);
            }
            if (0 == $product_qty) {
                $cart = cartAddToContents($cart, $product, 1);
            }
            foreach ($cart as $cart_key => $cart_item) {
                $min_order = productGetValue($cart_item['product_id'], 'min_order');
                if ($cart_item['product_qty'] < $min_order) {
                    $cart_item['product_qty']       = $min_order;
                    $cart[$cart_key]['product_qty'] = $min_order;
                }
                $cart[$cart_key]['product_offer']    = (string) (new TwigCurrency($product['sales_price'], $product['sales_currency'], $product['precision']));
                $cart[$cart_key]['product_offer_us'] = $cart[$cart_key]['product_offer'];
                $cart[$cart_key]['offer_currency']   = 'USD';
            }
            //$_GLOBALS['shopping_cart'] = $cart;
            cartSaveContents($_GLOBALS['member']['id'] ?? 0, $cart);
            insertLogDataInShoppingCart();
        }
    }
}
