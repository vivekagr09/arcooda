<?php
namespace app\controllers;

use app\helpers\Image;
use app\models\TwigBanner;
use app\models\TwigBrand;
use app\models\TwigCategory;
use app\models\TwigDistributor;
use app\models\TwigIcon;
use app\models\TwigManufacturer;
use app\models\TwigNews;
use app\models\TwigPage;
use app\models\TwigProduct;
use app\models\TwigSlider;

/**
 * Expects:
 * @var string $action
 * @var object $twig
 */

switch ($action) {
    case 'index':
        moduleLoad('product');

        global $_GLOBALS;
        global $DB;
        $front_page_layout = [];
        $query = $DB->query(sprintf(
            "SELECT id FROM %s WHERE `active` = 'y'",
            '`front_page_layout`',
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $front_page_layout[]   = $row;
        }
        $productColumns = [];
        // Get On sale products
          //echo '<pre>'; print_r(TwigProduct::search([],[],[],null,0,3)); die;
       /* foreach (TwigProduct::search(['special_flag' => 'y'],[],[],null,0,3) as $p) {
            $productColumns[] = $p;
        }*/
        // TODO: Get popular products
       /* foreach (TwigProduct::search(['feature' => 'y'],[],[],null,0,3) as $p) {
            $productColumns[] = $p;
        }*/
        // Get new arrival products
       /* foreach (TwigProduct::search(['discontinued'=>'y'],[],[],null,0,3) as $p) {
            $productColumns[] = $p;
        }*/
        $featured_product = [];
        if (in_array(6, array_column($front_page_layout, 'id'))){
            $featured_product = TwigProduct::search(['feature' => 'y'],[],[],null,0,10);
            if (count($featured_product) > 7) {
                $temp = [];
                foreach (array_rand($featured_product, 7) as $k) {
                    $temp[] = $featured_product[$k];
                }
                $featured_product = $temp;
                unset($temp);
            }
        }

        // Small two banners
        $banner_list2 = [];
        if (in_array(8, array_column($front_page_layout, 'id'))){
            $banner_list2 = TwigBanner::search(['banner_type' => [24, 25]]);
            shuffle($banner_list2);
            $banner_list2 = array_slice($banner_list2, 0, 2);
        }

        // Long banner
        $banner_list3 = [];
        if (in_array(10, array_column($front_page_layout, 'id'))){
            $banner_list3 = TwigBanner::search(['banner_type' => 26]);
            shuffle($banner_list3);
        }
        $carousel=[];
        $carousel_data =[];
            
        if (in_array(9, array_column($front_page_layout, 'id'))){ 
            $carousel_data = $DB->query_first("SELECT * FROM `product_page_carousel` WHERE   `active_home_page`='y' ORDER BY RAND() LIMIT 1");   
            if($carousel_data){
                if ($query = $DB->query(sprintf(
                    "SELECT `ppci`.*, `ppc`.`type` AS `carousel_type`, `ppc`.`topology`, `ppc`.`x_radious`, `ppc`.`x_rotation` FROM `product_page_carousel_images` AS `ppci` INNER JOIN `product_page_carousel` AS `ppc` ON `ppci`.`crousel_id` = `ppc`.`id` WHERE `ppc`.`id` = %d",
                    $carousel_data['id']
                ))) {
                    while ($row = $DB->fetch_assoc($query)) {
                        $carousel[] = $row;
                    }
                    $DB->free_result($query);
                }
            }
        }     
       
        if (in_array(7, array_column($front_page_layout, 'id'))){
            if (in_array(8, array_column($front_page_layout, 'id'))){
                 $front_page_youtube_video = [];
                $query = $DB->query("
                    SELECT * FROM `front_page_youtube_video`
                    WHERE `active`='y'
                    ORDER BY `rank` ASC
                ");
                while ($row = $DB->fetch_assoc($query)) {
                    $front_page_youtube_video[] = $row;
                }
                $DB->free_result($query);
            }else{
                 $front_page_youtube_video = '';
                if ($row = $DB->query_first(sprintf(
                    "SELECT * FROM `front_page_youtube_video` WHERE  `active`='y' ORDER BY RAND() LIMIT 1"
                ), MYSQLI_ASSOC)) {       
                    $front_page_youtube_video = $row;
                }
            }
        }

        ////echo '<pre>'; print_r($carousel); die;
        //TODO: Need a more elegant way to do this
        $manufacturer_limit = array_keys(productGroupCount(productSearch(['section' => 2] + $_GLOBALS['criteria_options']['product']), 'manufacturer'));

        $metaData = [];

        $query = $DB->query(sprintf(
            "SELECT * FROM %s %s %s",
            '`options`',
            'WHERE `flag`=0',
            'ORDER BY `id` ASC'
        ));
        while ($row = $DB->fetch_assoc($query)) {
            if (is_null($primary_key)) {
                $metaData[] = $row;
            } 
        }
        $DB->free_result($query);
        $latest_products = [];
        if (in_array(5, array_column($front_page_layout, 'id'))){
            $query = $DB->query(sprintf(
                "SELECT id FROM %s %s %s",
                '`products`',
                'WHERE `active`="y"',
                'ORDER BY `id` DESC  limit 5'
            ));
            while ($row = $DB->fetch_assoc($query)) {
               $latest_products[] = new TwigProduct($row['id']);
            }
        }
        $DB->free_result($query);
        $latest_survey = [];
        if (in_array(2, array_column($front_page_layout, 'id'))){
            $latest_survey = $DB->query_first(sprintf(
                "SELECT `survey`.*,`survey_link`.link FROM %s %s %s",
                '`highwaygroup`.`survey` as survey,`highwaygroup`.`survey_link`',
                'WHERE `status`=1 and survey_type="front" and site_id=39 and survey.id = survey_link.survey_id',
                'ORDER BY `id` DESC'
            ));
        }
        // Initialize variables for META_TITLE and META_DESCRIPTION
        $metaTitle = '';
        $metaDescription = '';

        // Iterate over the metaData array to extract the required values
        if (!empty($metaData)) {
            foreach ($metaData as $data) {
                if ($data['variable'] === 'META_TITLE') {
                    $metaTitle = $data['value'];
                } elseif ($data['variable'] === 'META_DESCRIPTION') {
                    $metaDescription = $data['value'];
                }
            }
        }
         $production_schedule=[];
        if (in_array(1, array_column($front_page_layout, 'id'))){ 
                $query = $DB->query(sprintf(
                    "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  
                        WHERE (`shipping_date` >= CURDATE() OR `status` = 1)
                        %s",
                    '`production_schedules`',
                    'ORDER BY `shipping_date` ASC'
                ));

             while ($row = $DB->fetch_assoc($query)) {
                     $row['product'] =  new TwigProduct($row['product_id']);
                    if($row['shipping_date'] > date('Y-m-d h:i:s')){
                        $row['status'] = 0;
                    } 
                    $production_schedule[] = $row;
            }
        }
        die($twig->load('site-index.twig')->render(['inputs' => [
            'page_title'       => $_GLOBALS['site_options']['META_TITLE'] ?? null,
            'page_description' => $_GLOBALS['site_options']['META_DESCRIPTION'] ?? null,
            'page_keywords'    => $_GLOBALS['site_options']['META_KEYWORDS'] ?? null,
            'page_canonical'   => '/',
            'page_breadcrumbs' => [
                ['title' => 'Home', 'url' => '/'],
            ],

            'result'           => [
                'y' => 'success',
                'n' => 'danger',
            ][$_GLOBALS['redirect']->value ?? ''] ?? '',
            'alert'            => [
                'y' => $_GLOBALS['redirect']->message ?? '',
                'n' => $_GLOBALS['redirect']->message ?? '',
            ][$_GLOBALS['redirect']->value ?? ''] ?? '',
            'banner_list1'       => TwigBanner::search(['banner_type' => 23]), //TODO: randomize order
            'latest_news'        => TwigNews::search(['archived' => 'y','date_schedule'=> date('Y-m-d')]),
           /* 'featured_products'  => array_slice(TwigProduct::search(['feature' => 'y']), 0, 10),*/
            'latest_products' => $latest_products,
            'banner_list2'       => $banner_list2,
            'banner_list3'       => (count($banner_list3)) ? $banner_list3[0] : null,
            'manufacturers'      => [], //TwigManufacturer::search(['show_front' => 'y', 'id' => $manufacturer_limit]),
            'icons'              => [] , //TwigIcon::getAllToShow(),
            'brands'             => TwigBrand::search(['show_front' => 'y']),
            'slider'             => ($_GLOBALS['site_options']['random_front_page_slider'] == 'no' && in_array(4, array_column($front_page_layout, 'id'))) ? TwigSlider::getDefaultSlider() : TwigSlider::getRandomSlider(),
            'distributors'       => TwigDistributor::search(['active' => 'y']),
            'product_columns'    => $productColumns,
            'products_on_sale'   => $featured_product,
            'carousel'           => $carousel,
            'carouseldata'       => $carousel_data,
            'front_page_layout'  => $front_page_layout,
            'front_page_youtube_video' => $front_page_youtube_video,
            'metaTitle'          => $metaTitle,
            'metaDescription'    => $metaDescription,
            'survey'    => $latest_survey,
            'production_schedule' => $production_schedule,
            'userreviews'             => getApprovedReviews()
        ]]));

    case 'page':
        if (!moduleLoad('page')) {
            break;
        }
/*        if (!empty($_COOKIE['REMOTE_USER'] ?? false)) {
            unset($_GLOBALS['criteria_options']['page']);
        }*/
        if ($pages = TwigPage::search(['slug' => $_GET['slug']])) {
            $page = reset($pages);
            die($twig->load('site-page.twig')->render(['inputs' => [
                'page_title'       => $page->title(),
                'page_description' => $page->description(),
                'page_keywords'    => $page->keywords(),
                'page_canonical'   => $page->url(),
                'page_breadcrumbs' => $page->breadcrumbs(),
                'page'             => $page,
                'member'           => array_key_exists('member', $_GLOBALS) ? $_GLOBALS['member'] : null,
            ]]));
        }

        $legacy_posts = [
            '15-spare-parts-arcooda-customers-limited-offer' => 98,
            '15khz-rgb-now-standard-32inch-lcd-hd-monitors' => 99,
            '1664-2' => 150,
            'arcade-monitors-sale' => 96,
            'arcooda-8-player-fish-machine-now-available' => 9,
            'arcooda-and-farsight-studios-launch-arcooda-pinball-arcade' => 23,
            'arcooda-and-pyramid-technologies-join-forces' => 50,
            'arcooda-announces-mini-crane-machine' => 71,
            'arcooda-arcade-not-toys' => 91,
            'arcooda-arcooda-experience' => 90,
            'arcooda-at-iaapa-attractions-expo-2016-showcasing-latest-machines' => 22,
            'arcooda-back-production' => 74,
            'arcooda-ceo-guest-speaker-at-newcastle-pinfest' => 59,
            'arcooda-competitors-come-get-ideas' => 93,
            'arcooda-do-what-you-want' => 83,
            'arcooda-farsight-developments' => 151,
            'arcooda-game-never-over' => 85,
            'arcooda-is-android' => 82,
            'arcooda-is-coinop' => 89,
            'arcooda-is-innovation' => 81,
            'arcooda-is-plug-and-play' => 87,
            'arcooda-is-touchscreen' => 86,
            'arcooda-is-unique-ip' => 88,
            'arcooda-is-you' => 92,
            'arcooda-launches-new-fish-hunting-game' => 40,
            'arcooda-manufacturing-special-production-of-29-inch-43-lcd-monitors' => 11,
            'arcooda-on-show-australasian-gaming-expo-sydney' => 10,
            'arcooda-partners-barnstorm-games-timeshock-arcade-edition' => 34,
            'arcooda-pinball-arcade-software-now-available' => 55,
            'arcooda-pinball-arcade-standalone-software-free-purchase-arcooda-cabinet' => 158,
            'arcooda-pinball-arcade-updates' => 24,
            'arcooda-pinball-massive-project' => 94,
            'arcooda-re-opened' => 66,
            'arcooda-video-pinball-artwork-suggestions' => 30,
            'arcooda-video-pinball-cabinets-new-website' => 57,
            'arcooda-video-pinball-machine-pinfest-2016-newcastle' => 14,
            'arcooda-video-pinball-take-2' => 64,
            'arcooda-video-pinball-updates' => 32,
            'arcooda-virtual-reality' => 84,
            'arcoodas-android-arcade-machine' => 38,
            'arcoodas-anti-cheater-alarm-system' => 42,
            'arcoodas-new-entry-level-video-redemption-machine' => 44,
            'backglass-support-arcooda-pinball-arcade-tables' => 43,
            'coin-operated-xbox-playstation-and-pc-now-available-on-game-wizard-xtreme' => 52,
            'completely-new-vision-pinball' => 95,
            'crtlcd-candy-cabinets-into-worldwide-markets' => 77,
            'doctor-who-master-of-time-on-arcooda-video-pinball' => 54,
            'emp-anti-cheater-discontinued-arcooda' => 63,
            'enjoy-favourite-slot-games-touch-wizard-machine' => 37,
            'fake-anti-cheater-devices' => 73,
            'features-like-see-touch-wizard-desktop-machine' => 27,
            'fish-machine-prices-good-true' => 36,
            'game-never-over' => 69,
            'game-wizard-2-0-coming-with-mercury-2-0' => 79,
            'game-wizard-android-trade-show-special' => 25,
            'game-wizard-for-android-coming-soon' => 8,
            'game-wizard-neptune-coming-soon' => 18,
            'game-wizard-xtreme-t-b-c-p' => 48,
            'get-rare-williams-bally-tables-today' => 68,
            'giveaway-man-cave-expo-tickets-giveaway' => 47,
            'highway-entertainment-hosting-arcooda-exclusive-preview-event' => 13,
            'highway-games-showing-arcooda-products-at-age-now-open' => 17,
            'highway-gaming-new-website-arcooda-customers' => 61,
            'history-current-pinball-manufacturersdevelopers' => 155,
            'insect-doctor-english-version-now-in-production' => 2,
            'introduce-dynasty-vertical-pinball-machine' => 65,
            'kinect' => 62,
            'king-of-treasures-plus-arcade-machines-production' => 7,
            'manufacturing-now-closed-for-ocean-monster-till-march' => 4,
            'maximise-gaming-potential-game-wizard-android' => 33,
            'mystic-dragon-2-perfect-for-family-entertainment-centres' => 51,
            'mystic-dragon-fish-hunter-game-coming-soon' => 21,
            'mystic-dragon-hunting-redemption-game-now-shipping' => 29,
            'new-arcade-cabinet-32-inch-game-wizard-xtreme-upright' => 49,
            'new-arcooda-video-series-keep-date' => 39,
            'new-arcooda-website-launching-mid-december' => 157,
            'new-feature-announced-arcooda-pinball-arcade-software' => 35,
            'newswhy-was-arcooda-born' => 76,
            'nintendo-switch-arcade-machine' => 56,
            'ocean-king-3-available-10-player-arcooda-deluxe-fish-machine' => 45,
            'pinball-arcade-customers-receive-discounts-arcooda-pinball-arcade' => 26,
            'pre-orders-for-arcooda-video-pinball-now-accepted' => 156,
            'premium-fish-cabinet-now-even-better' => 31,
            'reopening-1st-april-2021' => 72,
            'see-arcooda-man-cave-expo-sydney-july' => 46,
            'so-you-think-your-a-pinball-designer' => 19,
            'thank-you-news' => 75,
            'the-entertainer-arcade-music-drinks' => 97,
            'thunder-dragon-now-available-with-arcooda-8-player-fish-cabinet' => 20,
            'touch-wizard-from-arcooda-coming-soon' => 16,
            'touch-wizard-meets-pinball-arcade' => 28,
            'touch-wizard-packs-a-punch' => 70,
            'update-arcooda-video-pinball-machine' => 41,
            'virtual-reality-pinball' => 60,
            'want-game-wizard-mercury' => 80,
            'why-choose-arcooda' => 67,
            'why-we-built-the-game-wizard-xtreme-news' => 78,
            'wired-emp-anti-cheater-device' => 53,
            'xtreme-game-wizard-arcade-machine-at-inksmiths-tattoo-studio-nsw' => 12,
            'xtreme-game-wizard-arcade-machine-faq' => 15,
            'xtreme-game-wizard-arcade-machine-hong-kong-showroom' => 5,
            'xtreme-game-wizard-blue-arcade-machine-now-shipping' => 3,
            'xtreme-game-wizard-upgrade-kit-available-exclusively-highway-entertainment' => 6,
            'xtreme-game-wizard' => 1,
        ];
        if ($newses = TwigNews::search(['id' => ($legacy_posts[$_GET['slug'] ?? ''] ?? 0)])) {
            $news = reset($newses);
            redirect($news->url(), 308);
            die('Permanent redirect to ' . $news->url());
        }
        
        break;

    case 'graphicsimage':
        $filename = basename($_GET['name'] ?? '');
        if ($filename) {
            $image_file = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/season/image/' . $filename;
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            Image::HTTPSendFile($image_file);
            die();
        }
        break;

    case 'artimage' :
        $filename  = $_GET['name'];
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/art_work/';
        $file      = $dir . $filename;
        if ($_GET['crop'] ?? false) {
            $image_width=($_GET['width'])?$_GET['width']:128;
            $image_height=($_GET['height'])?$_GET['height']:96;
            list($width, $height) = getimagesize($file);
            if(!isset($_GET['exact'])){
                if ($width > $height) {
                  $image_height = floor(($height/$width)*$image_width);
                } else {
                  $image_width  = floor(($width/$height)*$image_height);
                }
            }

            $image=resize_and_crop($file,$image_width,$image_height,100,'other');
            if($image){
                $file=$image;
            }
        }
        $mime_type = mime_content_type($file);
        header('Date: ' . gmdate(DATE_RFC1123));
        header('Last-Modified: ' . date('Y-m-d'));
        header("Content-Type: " . $mime_type);
        //header('Content-Disposition: inline; filename="' . $name . '"');
        readfile($file);
        die();

    case 'brochureimage':
        $filename = basename($_GET['name'] ?? '');
        if ($filename) {
            $watermark_file = null;
            $profile_file   = $_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim';
            $width  = intval($_GET['size'] ?? $_GET['width'] ?? 0);
            $height = intval($_GET['size'] ?? $_GET['height'] ?? 0);
            /*if (!in_array([$width, $height], [
                [70, 70],    // widget-posts.twig
                [300, 300], 
                [80, 80],    // post-page.twig
                [664, 0],    // post-page.twig
                [730, 485],  // post-card.twig
                [1903, 500], // post-page.twig
            ])) {
                $width = $height = 0;
            }*/

            $image_file = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' . $filename;
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            $cache_file = sprintf(
                '%s/protected/cache/brochure/image/%s.%sx%s.png',
                $_SERVER['DOCUMENT_ROOT'],
                $filename,
                ($width) ? $width : '',
                ($height) ? $height : ''
            );
            if(!is_file($cache_file)){
                Image::copyToStatic(
                    $image_file,
                    $cache_file,
                    $profile_file,
                    $watermark_file,
                    $width,
                    $height
                );
            }
            if (!is_file($cache_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            Image::HTTPSendFile($cache_file);
            die();
        }
        break;

    case 'blogimage' :
        $filename  = $_GET['name'];
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/owner/blog/';
        $file      = $dir . $filename;
        if ($_GET['crop'] ?? false) {
            $new_width=($_GET['width'])?$_GET['width']:250;
            $new_height=($_GET['height'])?$_GET['height']:250;
            list($width, $height) = getimagesize($file);
            if ($width > $height) {
              $image_height = floor(($height/$width)*$new_width);
              $image_width  = $new_width;
            } else {
              $image_width  = floor(($width/$height)*$new_height);
              $image_height = $new_height;
            }

            $image=resize_and_crop($file,$image_width,$image_height,100,'other');
            if($image){
                $file=$image;
            }
        }
        $mime_type = mime_content_type($file);
        header('Date: ' . gmdate(DATE_RFC1123));
        header('Last-Modified: ' . date('Y-m-d'));
        header("Content-Type: " . $mime_type);
        //header('Content-Disposition: inline; filename="' . $name . '"');
        readfile($file);
        die();

    case '360viewimage' :
        $name  = $_GET['name'];
        $thumb = $_GET['thumb'];
        $dir = $_SERVER['DOCUMENT_ROOT'].'/webadmin/uploads/product/360view/';
        $thumb_dir = $dir.'smallimage/';
        $file = (($thumb) ? (((file_exists($thumb_dir.$name))  ? $thumb_dir : $dir)) : $dir);
        $file=$file.$name;
        $type=substr($name,strrpos($name,'.'));
        if(substr($type,0,1)=='.')
        $type=substr($type,1);
        if ($_GET['crop'] ?? false) {
            $new_width=($_GET['width'])?$_GET['width']:250;
                    $new_height=($_GET['height'])?$_GET['height']:250;
                    list($width, $height) = getimagesize($file);
                    if ($width > $height) {
                      $image_height = floor(($height/$width)*$new_width);
                      $image_width  = $new_width;
                    } else {
                      $image_width  = floor(($width/$height)*$new_height);
                      $image_height = $new_height;
                    }

                    $image=resize_and_crop($file,$image_width,$image_height,100,'product');
                    if($image){
                        $file=$image;
                    }
                }
        switch($type)
        {
            case "jpg":
                header("Content-type: image/jpeg");
                break;
            case "jpeg":
                header("Content-type: image/jpeg");
                break;
            case "gif":
                header("Content-type: image/gif");
                break;
            case "png":
                header("Content-type: image/png");
                break;
            default:
                echo "Image not recognized.";
                return 1;
        }
        readfile($file);
        die();

    case 'seriesimages' :
        global $DB;

        $attach = $DB->query_first(sprintf(
            "SELECT * FROM `product_series_images` AS `ma` WHERE `ma`.`id` = %d LIMIT 1",
            $_GET['id']
        ), MYSQLI_ASSOC);
        $filename  = basename($attach['file_orig']);
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/';
        $file      = $dir . $filename;
        if ($_GET['crop'] ?? false) {
            $new_width=($_GET['width'])?$_GET['width']:250;
            $new_height=($_GET['height'])?$_GET['height']:250;
            list($width, $height) = getimagesize($file);
            if ($width > $height) {
              $image_height = floor(($height/$width)*$new_width);
              $image_width  = $new_width;
            } else {
              $image_width  = floor(($width/$height)*$new_height);
              $image_height = $new_height;
            }

            $image=resize_and_crop($file,$image_width,$image_height,100,'other');
            if($image){
                $file=$image;
            }
        }
        $mime_type = mime_content_type($file);
        header('Date: ' . gmdate(DATE_RFC1123));
        header('Last-Modified: ' . date('Y-m-d'));
        header("Content-Type: " . $mime_type);
        //header('Content-Disposition: inline; filename="' . $name . '"');
        readfile($file);
        die();

    case 'supportimages' :
        global $DB;

        $attach = $DB->query_first(sprintf(
            "SELECT * FROM `product_supported_images` AS `ma` WHERE `ma`.`id` = %d LIMIT 1",
            $_GET['id']
        ), MYSQLI_ASSOC);
        $filename  = basename($attach['file_orig']);
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/';
        $file      = $dir . $filename;
        if ($_GET['crop'] ?? false) {
            $new_width=($_GET['width'])?$_GET['width']:250;
            $new_height=($_GET['height'])?$_GET['height']:250;
            list($width, $height) = getimagesize($file);
            if ($width > $height) {
              $image_height = floor(($height/$width)*$new_width);
              $image_width  = $new_width;
            } else {
              $image_width  = floor(($width/$height)*$new_height);
              $image_height = $new_height;
            }

            $image=resize_and_crop($file,$image_width,$image_height,100,'other');
            if($image){
                $file=$image;
            }
        }
        $mime_type = mime_content_type($file);
        header('Date: ' . gmdate(DATE_RFC1123));
        header('Last-Modified: ' . date('Y-m-d'));
        header("Content-Type: " . $mime_type);
        //header('Content-Disposition: inline; filename="' . $name . '"');
        readfile($file);
        die();

    case 'manual':
     if($_GET['id'] || $_GET['slug']){
            global $DB;
            $id = intval($_GET['id']);
            if($_REQUEST["type"]=='manual'){
                $res = $DB->query_first(sprintf(
                "SELECT `file_name`,`name` FROM `manuals` WHERE `id` = %d LIMIT 1",
                $id
                ), MYSQLI_ASSOC);
            }elseif($_REQUEST["type"]=='firm'){
                $res = $DB->query_first(sprintf(
                "SELECT `file_name`,`name` FROM `firmware` WHERE `id` = %d LIMIT 1",
                $id
                ), MYSQLI_ASSOC);
            }elseif($_GET["type"]=="assets"){
                $res = $DB->query_first(sprintf(
                "SELECT `file_name`,`status`,`created_by`,`name` FROM `file_assets` WHERE `slug` = %s LIMIT 1",
                "'".$_GET['slug']."'"
                ), MYSQLI_ASSOC);

                if ('private' == $res['status']) {
                    redirect('/');
                    die;
                }
            }else{
                $res = $DB->query_first(sprintf(
                "SELECT `attachment`,`name` FROM `product_links` WHERE `id` = %d LIMIT 1",
                $id
                ), MYSQLI_ASSOC);
            }
            if($_REQUEST["type"]=='manual' || $_REQUEST["type"]=='firm'){
                $_REQUEST["type"] = ($_REQUEST["type"] == 'firm')?'firmware': $_REQUEST["type"];
                $filename = ($res['file_name'])?$res['file_name']:$res['file_orig'];
                $dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/owner/'.$_REQUEST["type"].'/';
            }elseif($_GET["type"]=="assets"){
                $filename = ($res['file_name'])?$res['file_name']:$res['file_orig'];
                $dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/fileassets/';
            }else{
                $filename = ($res['attachment'])?$res['attachment']:$res['file_orig'];
                $dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/links/';
            }
            $file = $dir . $filename;
            $filedownloadname=trim(preg_replace('/[^A-Za-z0-9]+/', '-', $res['name']), '-');
            Image::HTTPSendFile($file,$filedownloadname);
            /*$mime_type = mime_content_type($filename);
            //$mime_type = mime_extension_type($filename);
            header('Date: ' . gmdate(DATE_RFC1123));
            header('Last-Modified: ' . gmdate(DATE_RFC1123, filemtime($file)));
            header("Content-Type: " . $mime_type);
            //header('Content-Disposition: inline; filename="' .$filename . '"');
            readfile($file);*/
            die();
        }
        break;

    case 'calculatecurrency':
        if($_REQUEST['amount'] && $_REQUEST['from_currency'] && $_REQUEST['to_currency']){
            $exchange_list=getExchangeRateList();
            $comrate = '0.000000';
            if (bccomp($exchange_list[$_REQUEST['to_currency']] ?? '0.000000', '0.000000', 6)) {
                $comrate = bcdiv($exchange_list[$_REQUEST['from_currency']], $exchange_list[$_REQUEST['to_currency']], 6);
            }
            $comprice=($comrate*$_REQUEST['amount']);
            echo number_format((float)$comprice, 2, '.', '');
        }
        die();

    case 'currencyCalculator':
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Currency Calculator', 'url' => '/currency-calculator'],
        ];
        die($twig->load('currency-calculator.twig')->render(['inputs' => [
            'title'       => 'currency calculator',
            'description' => 'currency calculator',
            'keywords'    => 'currency calculator',
            'page_canonical'   => 'currency calculator',
            'page_breadcrumbs' => $breadcrumbs,
        ]]));

    case 'freightCalculator':
        global $DB;

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Freight Calculator', 'url' => '/freight-calculator'],
        ];

        $func_countries_from_sea=[];
        $func_countries_from_air=[];
        $func_countries_to_air=[];
        $func_countries_to_sea=[];
        $countries=getCountryList();
        if ($query = $DB->query(sprintf(
            "SELECT DISTINCT `country_from`, `country_to`, `sea_air` FROM `coinop`.`freight_services_quotes` WHERE `country_from` <> '' AND `country_to` <> ''"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                if ('Sea' == $row['sea_air']) {
                    $func_countries_from_sea[$row['country_from']] = ['code' => $row['country_from'], 'name' => explode('-', $countries[$row['country_from']])[0]];
                    $func_countries_to_sea[$row['country_to']]   = ['code' => $row['country_to'], 'name' => explode('-', $countries[$row['country_to']])[0]];
                }
                if ('Air' == $row['sea_air']) {
                    $func_countries_from_air[$row['country_from']] = ['code' => $row['country_from'], 'name' => explode('-', $countries[$row['country_from']])[0]];
                    $func_countries_to_air[$row['country_to']]   = ['code' => $row['country_to'], 'name' => explode('-', $countries[$row['country_to']])[0]];
                }
            }
        }
        if ($query = $DB->query(sprintf(
            "SELECT DISTINCT `country_from`, `country_to`, `sea_air` FROM `highwaygroup`.`freight_services_quotes` WHERE `country_from` <> '' AND `country_to` <> ''"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                if ('Sea' == $row['sea_air']) {
                    $func_countries_from_sea[$row['country_from']] = ['code' => $row['country_from'], 'name' => explode('-', $countries[$row['country_from']])[0]];
                    $func_countries_to_sea[$row['country_to']]   = ['code' => $row['country_to'], 'name' => explode('-', $countries[$row['country_to']])[0]];
                }
                if ('Air' == $row['sea_air']) {
                    $func_countries_from_air[$row['country_from']] = ['code' => $row['country_from'], 'name' => explode('-', $countries[$row['country_from']])[0]];
                    $func_countries_to_air[$row['country_to']]   = ['code' => $row['country_to'], 'name' => explode('-', $countries[$row['country_to']])[0]];
                }
            }
        }
        die($twig->load('freight-calculator.twig')->render(['inputs' => [
            'title'            => 'freight calculator',
            'description'      => 'freight calculator',
            'keywords'         => 'freight calculator',
            'page_canonical'   => 'freight calculator',
            'page_breadcrumbs' => $breadcrumbs,

            'func_countries_from_sea' => array_values($func_countries_from_sea),
            'func_countries_from_air' => array_values($func_countries_from_air),
            'func_countries_to_air'   => array_values($func_countries_to_air),
            'func_countries_to_sea'   => array_values($func_countries_to_sea),
        ]]));

    case 'product-builder':
        global $DB;

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Product Builder', 'url' => '/product-builder.html'],
        ];
            $builder_category=[];
            $builder_product=[];
            $selected_category=[];
            $query=$DB->query("select mc.* from pb_machines_category mc JOIN pb_machines as ma on ma.cat=mc.id  JOIN pb_builders as bu on bu.id=ma.builder_id where bu.status=1 AND mc.deleted_at is NULL group by mc.id");
            while ($row = $DB->fetch_assoc($query)) {
                    $builder_category[] = $row;
            }
            if(isset($_GET['category'])){

                $query=$DB->query("Select ma.*,pw.link,bu.name as builder_name from pb_machines as ma JOIN pb_builders as bu on bu.id=ma.builder_id JOIN pb_websites as pw on pw.builder_id=bu.id where bu.status=1 AND ma.cat=".$_GET['category']);
                while ($row = $DB->fetch_assoc($query)) {
                    $row['product']=TwigProduct::search(['id' =>$row['product_id']])[0];
                    $row['description']=preg_replace("/\r\n|\r|\n/", '<br/>', $row['description']);
                    $builder_product[] = $row;
                }
                $selected_category=$DB->query_first("SELECT * from pb_machines_category where id=".$_GET['category']."", MYSQLI_ASSOC);
            }
            $isSecure = false;
            if (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) {
                $isSecure = true;
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && 'on' == $_SERVER['HTTP_X_FORWARDED_SSL']) {
                $isSecure = true;
            }
            $url = '/builder/';
            die($twig->load('main-product-builder.twig')->render(['inputs' => [
                'title'            => 'Product Builder',
                'description'      => 'Product Builder',
                'keywords'         => 'Product Builder',
                'page_canonical'   => 'Product Builder',
                'builder_product'  => $builder_product,
                'builder_category' => $builder_category,
                'page_breadcrumbs' => $breadcrumbs,
                'builderurl'       => $url,
                'selected_category'=> $selected_category
            ]]));

        case 'productSupportedIcon':
            global $DB;

            $slug = $_GET['slug'];
            if ($row = $DB->query_first(sprintf(
                "SELECT * FROM `product_supported` WHERE `active` = 'y' AND `slug` = %s LIMIT 1",
                $DB->escape($slug)
            ), MYSQLI_ASSOC)) {
                $test = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' . basename($row['img']);
                if (is_file($test)) {
                    Image::HTTPSendFile($test);
                    die();
                }
            }
            break;

        case 'productSupported':
            global $DB;

            $slug=$_GET['slug'];
            $related_product=[];
            $setup_images=[];
            $data = $DB->query_first(sprintf("SELECT * FROM `product_supported` WHERE `active`='y' AND `slug` = %s Limit 8",$DB->escape($slug)), MYSQLI_ASSOC);
            if($data){
                $products=$DB->query("SELECT id FROM `products` WHERE active='y' AND  FIND_IN_SET(".$data['id'].",product_supported)");
                while ($row = $DB->fetch_assoc($products)) {
                    $related_product[]=new TwigProduct($row['id']);
                }
                $query = $DB->query(sprintf(
                    "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d  %s",
                    '`product_supported_images`',
                    'supported_id',
                     $data['id'],
                    'ORDER BY `rank` ASC'
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    if (is_null($primary_key)) {  // @fixme What is $primary_key?
                        $row['image_url'] = sprintf(
                            '/supportimages/%s-%d.jpg',
                            trim(preg_replace('/[^A-Za-z0-9]+/', '-', $row['caption']), '-'),
                            $row['id']
                        );
                        $setup_images[]   = $row;
                    }
                }
                $breadcrumbs = [
                    ['title' => 'Home', 'url' => '/'],
                    ['title' => 'Arcade Play', 'url' => ''],
                    ['title' => $data['name'], 'url' => ''],
                ];
                if(!empty($data['description'])){
                    $data['description'] =  preg_replace("/\r\n|\r|\n/", '<br/>', $data['description']);
                }
                die($twig->load('product-detail-supported.twig')->render(['inputs' => [
                    'title'       => $data['meta_title'],
                    'description' => $data['meta_description'],
                    'keywords'    => $data['meta_keyword'],
                    'page_canonical'   => 'Product Supported',
                    'product_support'=> $data,
                    'related_product' => $related_product,
                    'page_breadcrumbs' => $breadcrumbs,
                    'setup_images'      => $setup_images
                ]]));
            }
        break;
        case 'series':
            global $DB;

            $slug=$_GET['slug'];
            $related_product=[];
            $setup_images=[];
            $data = $DB->query_first(sprintf("SELECT * FROM `product_series` WHERE `active`='y' AND `slug` = %s Limit 1",$DB->escape($slug)), MYSQLI_ASSOC);
            if($data){
                $products=$DB->query("SELECT id FROM `products` WHERE active='y' AND  product_series=".$data['id']."");
                while ($row = $DB->fetch_assoc($products)) {
                    $related_product[]=new TwigProduct($row['id']);
                }
                $query = $DB->query(sprintf(
                    "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d  %s",
                    '`product_series_images`',
                    'series_id',
                     $data['id'],
                    'ORDER BY `rank` ASC'
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $row['image_url'] = sprintf(
                        '/seriesimages/%s-%d.jpg',
                        trim(preg_replace('/[^A-Za-z0-9]+/', '-', $row['caption']), '-'),
                        $row['id']
                    );
                    $setup_images[]   = $row;
                }
                $breadcrumbs = [
                    ['title' => 'Home', 'url' => '/'],
                    ['title' => 'Series', 'url' => ''],
                    ['title' => $data['name'], 'url' => ''],
                ];

                die($twig->load('product-detail-series.twig')->render(['inputs' => [
                    'title'       => ucwords($data['name']). ' Series Family',
                    'description' => $data['description'],
                    'keywords'    => 'Product Series',
                    'page_canonical'   => 'Product Series',
                    'series'=> $data,
                    'related_product' => $related_product,
                    'page_breadcrumbs' => $breadcrumbs,
                    'setup_images'      => $setup_images
                ]]));
            }
        break;
    case 'page-image':
        global $DB;

        if (!moduleLoad('page')) {
            break;
        }

        $imageId = pageImageSearch(array('id' => $_GET['id']));
        $image_url = pageImageFromDatabase($imageId[0]);
        if (!empty($image_url) && isset($image_url['image_url'])) {
            // Output the image using HTML <img> tag
            echo '<img src="' . $image_url['image_url'] . '" alt="' . $image_url['alt_text'] . '" />';
        } else {
            // Handle the case when the image array is empty or doesn't contain the image_url key
            echo 'Image not found';
        }
        die;
    
    case 'brochure-image':
        global $DB;

        $row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d AND `deleted_at` IS NULL",
            '`product_brochure_image`',
            'id',
             $_GET['id']
        ), MYSQLI_ASSOC);

        $dir= $_SERVER['DOCUMENT_ROOT'] .'/webadmin/assets/icon/image/';
        
        if($row) {
            switch($row['filetype'])
            {
                case "jpg":
                    header("Content-type: image/jpeg");
                    break;
                case "jpeg":
                    header("Content-type: image/jpeg");
                    break;
                case "gif":
                    header("Content-type: image/gif");
                    break;
                case "png":
                    header("Content-type: image/png");
                    break;
                default:
                    echo "Image not recognized.";
                    return 1;
            }
            echo file_get_contents($dir . $row['name']);
        } else {
            echo 'Image not found';
        }

        die;
    case 'survey':
    global $DB;
    $survey = [];
    $query = $DB->query(sprintf(
        "SELECT `survey`.*,`survey_link`.link FROM %s %s %s",
        '`highwaygroup`.`survey` as survey,`highwaygroup`.`survey_link`',
        'WHERE  `status`=1 and survey_type="front" and site_id=39 and survey.id = survey_link.survey_id',
        'ORDER BY `id` DESC'
    ));
    while ($row = $DB->fetch_assoc($query)) {
        $survey[] = $row;
    }
    $breadcrumbs = [
        ['title' => 'Home', 'url' => '/'],
        ['title' => 'Survey', 'url' => '/arcooda-survey'],
    ];
    die($twig->load('site-survey.twig')->render(['inputs' => [
            'title'       => 'Current Open Survey',
            'description' =>'Current Open Survey',
            'keywords'    => 'Current Open Survey',
            'page_canonical'   => 'Current Open Survey',
            'page_breadcrumbs' => $breadcrumbs,
            'surveys'    => $survey
            ]
        ]));
    case 'surveyimage' :
        $filename  = $_GET['name'];
         if ($filename) {
            $watermark_file = null;
            $profile_file   = null;

            $width  = intval($_GET['size'] ?? $_GET['width'] ?? 0);
            $height = intval($_GET['size'] ?? $_GET['height'] ?? 0);

            $image_file = $_SERVER['DOCUMENT_ROOT'] . '/surveysimage/' . $filename;
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            $cache_file = sprintf(
                '%s/protected/cache/survey/%s.%sx%s.png',
                $_SERVER['DOCUMENT_ROOT'],
                $filename,
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
    case 'clearance' :
        $item = $DB->query_first("SELECT * FROM `clearance_items` WHERE `id` = 1  LIMIT 1", MYSQLI_ASSOC);
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Clearance Items', 'url' => '/clearance'],
        ];
        die($twig->load('clearance.twig')->render(['inputs' => [
            'page_title'       =>  $item['meta_title'],
            'page_description' => $item['meta_description'],
            'page_keywords'    => $item['meta_keywords'],
            'page_canonical'   => 'Clearance Items',
            'page_breadcrumbs' => $breadcrumbs,
            'description' =>$item['description'] 
        ]]));
    case 'clearance_image' :

        $row = $DB->query_first(sprintf(
            "SELECT * FROM %s  Where `%s` = %d AND `deleted_at` IS NULL",
            '`clearance_screenshots`',
            'id',
             $_GET['id']
        ), MYSQLI_ASSOC);
         if ($row) {
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
                $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/clearance',
                basename($row['filename'])
            );
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            $cache_file = sprintf(
                '%s/protected/cache/clearance/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $row['id'],
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
}
