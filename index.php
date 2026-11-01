<?php
namespace app;
ini_set('memory_limit', '-1');
use app\models\MyTwig;

include_once __DIR__ . '/common.inc.php';
/*
URL Structure:

/c(/[category-name]-[category-id])+ - Category Page
/lp/[banner-name]-[banner-id] - Banner redirect
/p/[product-name]-[product-id]\.html - Product Page
/n/[news-name]-[news-id]\.html - News Page
/s/[search-keywords] - Search Page
/[static-name] - General purpose pages
/ - Home Page
 */

$twig = new MyTwig();

unset($_GET['r']);
if ($route = $url_manager->parseRequest()) {
    $_GET['r'] = $route[0];
    foreach ($route[1] as $k => $v) {
        $_GET[$k] = $v;
    }
} else {
    $parsed_url = parse_url('https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
    $path_split = explode('/', $parsed_url['path']);
    array_shift($path_split); // Should always be blank
    /*
    if (preg_match('@^/p/.+-([0-9]+)\.html$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/view';
        $_GET['id'] = $m[1];
    */
    if (preg_match('@^/buy/.+-([0-9]+)/?$@', $parsed_url['path'], $m)) {
        $slugs        = explode('/', trim($m[0], '/'));
        $counts = array_count_values($slugs);
        if (isset($counts['buy']) && $counts['buy'] > 1) {
            die($twig->load('messages-404.twig')->render(['inputs' => [
            ]]));
        }
        traceUser();
        $_GET['r']  = 'product/view';
        $_GET['id'] = $m[1];
    }elseif (preg_match('@^/parts/.+-([0-9]+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'search/parts';
        $_GET['id'] = $m[1];
        traceUser();
    }elseif (preg_match('@^/p/compare_product$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/compare_product';
        $_GET['id'] = $m[1];
        traceUser();
    }elseif (preg_match('@^/review/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'product/review';
        traceUser();
    }elseif (preg_match('@^/getreviws/([0-9]+)/([0-9]+)?$@', $parsed_url['path'], $m)) {
        $_GET['product_id'] = $m[1];
        $_GET['page'] = $m[2];
        $_GET['r'] = 'product/getreviws';
        traceUser();
    }elseif (preg_match('@^/reviewimage/.+-([0-9]+)\.jpg?$@', $parsed_url['path'], $m)) {
            $_GET['id'] = $m[1];
            $_GET['r'] = 'product/reviewimage';
    } elseif (preg_match('@^/p/compare$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'product/compare';
        traceUser();
    } elseif (preg_match('@^/p/removecompare/([a-z0-9]+)$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/removecompare';
        $_GET['id'] = $m[1];
    } elseif (preg_match('@^/p/wishlist$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'product/wishlist';
        traceUser();
    } elseif (preg_match('@^/p/removewishlist/([a-z0-9]+)$@',$parsed_url['path'], $m)) {
        $_GET['r']  = 'product/removewishlist';
        $_GET['id'] = $m[1];
    } elseif (preg_match('@^/p/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/image';
        $_GET['id'] = $m[1];
    } elseif (preg_match('@^/images/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/image';
        $_GET['id'] = $m[1];
    }elseif (preg_match('@^/marker_images/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/marker_images';
        $_GET['id'] = $m[1];
    } elseif (preg_match('@^/colourimages/(.+)/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/colour_image';
        $_GET['id'] = $m[2];

    }elseif (preg_match('@^/graphicsimage/(.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/graphicsimage';
        $name        = explode('/', trim($m[1], '/'));
        $_GET['name'] = end($name);
    }elseif (preg_match('@^/artimage/(.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/artimage';
        $name        = explode('/', trim($m[1], '/'));
        $_GET['name'] = end($name);
    }elseif (preg_match('@^/brochureimage/(.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/brochureimage';
        $name        = explode('/', trim($m[1], '/'));
        $_GET['name'] = end($name);
    }elseif (preg_match('@^/blogimage/(.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/blogimage';
        $name        = explode('/', trim($m[1], '/'));
        $_GET['name'] = end($name);
    }elseif (preg_match('@^/360viewimage/(.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/360viewimage';
        $name        = explode('/', trim($m[1], '/'));
        $_GET['name'] = end($name);

    }elseif (preg_match('@^/arcade-play/(.+).png$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/productSupportedIcon';
        $slug        = explode('/', trim($m[1], '/'));
        $_GET['slug'] = end($slug);
    }elseif (preg_match('@^/arcooda-survey/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/survey';
        traceUser();
    }elseif (preg_match('@^/pre-order-product/?$@', $parsed_url['path'], $m)) {
        $_GET['type']    = 'preorder';
        $_GET['r']    = 'search/preorder';
        traceUser();
    }elseif (preg_match('@^/specials-product/?$@', $parsed_url['path'], $m)) {
        $_GET['type']    = 'special';
        $_GET['r']    = 'search/preorder';
        traceUser();
    }elseif (preg_match('@^/second-product/?$@', $parsed_url['path'], $m)) {
        $_GET['type']    = 'second';
        $_GET['r']    = 'search/preorder';
        traceUser();
    }elseif (preg_match('@^/discontinued-product/?$@', $parsed_url['path'], $m)) {
        $_GET['r']    = 'search/discontinued';
        traceUser();
    }elseif (preg_match('@^/arcooda-distributor/?$@', $parsed_url['path'], $m)) {
        $_GET['r']    = 'search/distributor';
        traceUser();
    }elseif (preg_match('@^/arcade-play/(.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/productSupported';
        $slug        = explode('/', trim($m[1], '/'));
        $_GET['slug'] = end($slug);
        traceUser();
    }elseif (preg_match('@^/series/(.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/series';
        $slug        = explode('/', trim($m[1], '/'));
        $_GET['slug'] = end($slug);
        traceUser();
    }elseif (preg_match('@^/seriesimages/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/seriesimages';
        $_GET['id'] = $m[1];

    }elseif (preg_match('@^/supportimages/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/supportimages';
        $_GET['id'] = $m[1];

    }elseif (preg_match('@^/page-image/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
       
        $_GET['r']  = 'site/page-image';
        $_GET['id'] = urldecode($m[1]);

    }elseif (preg_match('@^/brochure-image/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
       
        $_GET['r']  = 'site/brochure-image';
        $_GET['id'] = urldecode($m[1]);

    } elseif (preg_match('@^/icon/.+-([0-9]+)\.html$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'icon/view';
        $_GET['id'] = $m[1];
    } elseif (preg_match('@^/icon/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'icon/image';
        $_GET['id'] = $m[1];

    } elseif (preg_match('@^/manufacturer/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'manufacturer/image';
        $_GET['id'] = $m[1];

    // Brand related
    } elseif (preg_match('@^/our-brand/.*-(\\d+)\\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'brand/image';
        $_GET['id'] = $m[1];

    // Distributor related
    } elseif (preg_match('@^/distributed-by/.*-(\\d+)\\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'distributor/image';
        $_GET['id'] = $m[1];

    // News related
    } elseif (preg_match('@^/n/.*-(\\d+)\\.html$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'news/view';
        $_GET['id'] = $m[1];
        traceUser();
    } elseif (preg_match('@^/news_image/.+-(\d+_\d+\\.\w+)$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'news/extra';
        $_GET['id'] = $m[1];
    } elseif (preg_match('@^/clearance_image/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/clearance_image';
        $_GET['id'] = $m[1];
    }elseif (preg_match('@^/news_image/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'news/extra';
        $_GET['id'] = $m[1];
    }elseif (preg_match('@^/survey-image/(.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/surveyimage';
        $_GET['name'] = $m[1];
    }elseif (preg_match('@^/newsletters/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'news/newsletter';
    }elseif (preg_match('@^/newsletters/.*-(\\d+)\\.html$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'news/newsletter-detail';
        $_GET['id'] = $m[1];
    } elseif (preg_match('@^/n/.*-(\\d+)\\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'news/image';
        $_GET['id'] = $m[1];

    // Blog related
    } elseif (preg_match('@^/owner-news/.+-([0-9]+)\.html$@', $parsed_url['path'], $m)) {
        if (preg_match('/-(\d+)-\d+\.html$/', $m[0], $matches)) {
            $_GET['product_id'] = $matches[1];
        } else {
            $_GET['product_id'] = 0;
        }
        $_GET['r']  = 'news/ownernews';
        $_GET['id'] = $m[1];
        traceUser();
    } elseif (preg_match('@^/sliderimage/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'icon/sliderimage';
        $_GET['id'] = $m[1];

    } elseif (preg_match('@^/ss/.+-([0-9]+)\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/screenshot';
        $_GET['id'] = $m[1];

    } elseif (preg_match('@^/lp/.*-([0-9]+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'banner/click';
        $_GET['id'] = $m[1];
    } elseif (preg_match('@^/lp/.*-h([0-9]+)\\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'banner/blur_image';
        $_GET['id'] = $m[1];
        //$_GET['width']  = ([0-9]+|auto)
        //$_GET['height'] = ([0-9]+|auto)
        //$_GET['size']   = ([0-9]+|auto)
    } elseif (preg_match('@^/lp/.*-m([0-9]+)\\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'banner/mobile_image';
        $_GET['id'] = $m[1];
        //$_GET['width']  = ([0-9]+|auto)
        //$_GET['height'] = ([0-9]+|auto)
        //$_GET['size']   = ([0-9]+|auto)
    } elseif (preg_match('@^/lp/.*-([0-9]+)\\.jpg$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'banner/image';
        $_GET['id'] = $m[1];
        //$_GET['width']  = ([0-9]+|auto)
        //$_GET['height'] = ([0-9]+|auto)
        //$_GET['size']   = ([0-9]+|auto)
    
    /*
    } elseif (preg_match('@^/c/.+-([0-9]+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'search/category';
        $_GET['id'] = $m[1];
    */
    } elseif (preg_match('@^/buy/(.+)/?$@', $parsed_url['path'], $m)) {
        $slugs        = explode('/', trim($m[1], '/'));
        if (in_array("buy", $slugs)) {
            die($twig->load('messages-404.twig')->render(['inputs' => [
            ]]));
        }
        $_GET['r']    = 'search/category';
        $_GET['slug'] = end($slugs);
        traceUser();
    } elseif (preg_match('@^/specials/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/specials';
    /*
    } elseif (preg_match('@^/specials/.+-([0-9]+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']        = 'search/specials';
        $_GET['category'] = $m[1];
    */

    /*
    } elseif (preg_match('@^/n/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/news';
    */
        traceUser();
    } elseif (preg_match('@^/arcade-news/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/news';
        traceUser();
    } elseif (preg_match('@^/latest-news/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/news';
        $_GET['category'] = 'Latest News';
        traceUser();
    } elseif (preg_match('@^/arcade-games-news/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/news';
        $_GET['category'] = 'Arcade Games';
        traceUser();
    } elseif (preg_match('@^/game-software-news/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/news';
        $_GET['category'] = 'Game Software';
        traceUser();
    } elseif (preg_match('@^/arcade-parts-news/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/news';
        $_GET['category'] = 'Arcade Parts';
        traceUser();
    } elseif (preg_match('@^/product-builder.html/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/product-builder';
        traceUser();
    }elseif (preg_match('@^/arcooda-monitor-comparison-table.html/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/monitor-comparison';
        $_GET['is_admin']  = false;
        traceUser();
    }elseif (preg_match('@^/admin-arcooda-monitor-comparison-table.html/?$@', $parsed_url['path'], $m)) {
        $_GET['is_admin']  = true;
        $_GET['r']  = 'product/monitor-comparison';
    }elseif (preg_match('@^/download-arcooda-monitor-comparison-table.html/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/download-monitor-comparison';
    }elseif (preg_match('@^/merchandise-news/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/news';
        $_GET['category'] = 'Merchandise';
        traceUser();
    /*
    } elseif (preg_match('@^/n/([^/]+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']        = 'search/news';
        $_GET['category'] = urldecode($m[1]);
    */

    } elseif (preg_match('@^/s/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/suggest';
        traceUser();
    } elseif (preg_match('@^/s/(.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/suggest';
        $_GET['s'] = urldecode($m[1]);
        traceUser();
    } elseif (preg_match('@^/search/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/index';
        traceUser();
    } elseif (preg_match('@^/search/([^/]+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'search/index';
        $_GET['s'] = urldecode($m[1]);
        traceUser();

    } elseif (preg_match('@^/cart/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/index';
        $_GET['type']='shopping';
        traceUser();
    }elseif (preg_match('@^/quote-cart/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/index';
        $_GET['type']='quote';
        traceUser();
    } elseif (preg_match('@^/payment_success/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/payment_success';
        traceUser();
    }elseif (preg_match('@^/payment_fail/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/payment_fail';
        traceUser();
    }elseif (preg_match('@^/payment_manual_success/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/payment_manual_success';
        traceUser();
    }elseif (preg_match('@^/payment_manual_fail/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/payment_manual_fail';
        traceUser();
    } elseif (preg_match('@^/cart/(.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/' . urldecode($m[1]);
        traceUser();
    } elseif (preg_match('@^/checkout/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/checkout';
        $_GET['type']='shopping';
        traceUser();
    }elseif (preg_match('@^/fileassets/(.+)/(.+)?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'site/manual';
        $_GET['type']='assets';
        $_GET['slug']=urldecode($m[2]);
        traceUser();
    }elseif (preg_match('@^/quote-checkout/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/checkout';
        $_GET['type']='quote';
        traceUser();
    } elseif (preg_match('@^/payment/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'payment/manual-payment-checkout';
        traceUser();
    } elseif (preg_match('@^/freight/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/freight';
        traceUser();
    } elseif (preg_match('@^/cart/assisted-checkout/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/assisted-checkout';
        traceUser();
    } elseif (preg_match('@^/cart/express-checkout/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/express-checkout';
        traceUser();
    } elseif (preg_match('@^/cart/thank-you/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'cart/thank-you';
        traceUser();

    } elseif (preg_match('@^/(?P<controller>payment|airwallex)/(?P<action>.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = sprintf('%s/%s', $m['controller'], $m['action']);
        traceUser();

    }elseif (preg_match('@^/account/checkUsername/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'account/checkUsername';
        traceUser();
    } elseif (preg_match('@^/account/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'account/dashboard';
        traceUser();
    } elseif (preg_match('@^/account/([^/]+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'account/' . urldecode($m[1]);
        traceUser();
    } elseif (preg_match('@^/inquiry/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'account/inquiry';
        traceUser();
        
    }elseif (preg_match('@^/invoicepdf/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'account/invoicepdf';
        traceUser();
        
    }elseif (preg_match('@^/wanted/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'product/wanted';
        traceUser();
    }elseif (preg_match('@^/captchaimage/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'account/captchaimage';
    }elseif (preg_match('@^/testemailissue/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'account/testemailissue';
    }elseif (preg_match('@^/currency-calculator/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'site/currencyCalculator';
        traceUser();
    }elseif (preg_match('@^/calculate-currency/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'site/calculatecurrency';
        traceUser();
    }elseif (preg_match('@^/freight-calculator/?$@', $parsed_url['path'], $m)) {
        $_GET['r'] = 'site/freightCalculator';
        traceUser();
    }elseif (preg_match('@^/clearance/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'site/clearance';
    } elseif (preg_match('@^/?$@', $parsed_url['path'], $m)) {
        traceUser();
        $_GET['r'] = 'site/index';

    //TODO: Legacy URLs, make them canonical redirect
    } elseif (preg_match('@^/products/.+[-_]([0-9]+)\.html$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/view';
        $_GET['id'] = $m[1];
        traceUser();
    } elseif (preg_match('@^/checkout/addtocart_([0-9]+)\.html$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/view';
        $_GET['id'] = $m[1];
        traceUser();
    } elseif (preg_match('@^/(stocklist|products|print_stocklist)/(.+)/?$@', $parsed_url['path'], $m)) {
        //TODO: Manually map slug to id
        $_GET['r']    = 'search/category';
        $_GET['slug'] = urldecode($m[2]);
        traceUser();

    } elseif (preg_match('@^/(.+)/?$@', $parsed_url['path'], $m)) {
        $_GET['r']    = 'site/page';
        $_GET['slug'] = trim(urldecode($m[1]), '/');
        traceUser();
    }

    elseif (preg_match('@^/currency/getmembercurrencyconverter/?$@', $parsed_url['path'], $m)) {
        $_GET['r']  = 'product/currencyconverter';
        $_GET['id'] = $m[1];
        traceUser();
    }
}

$split      = explode('/', $_GET['r'] ?? 'error/404');
$controller = array_shift($split);
$action     = array_shift($split);  
include sprintf('%s/protected/controllers/%s.php', __DIR__, $controller);

//-----------------------------------------------------------------------------
// Not Found
//-----------------------------------------------------------------------------
if (!headers_sent()) {
    header("HTTP/1.1 404 Not Found", true, 404);
}
die($twig->load('messages-404.twig')->render(['inputs' => [
]]));
