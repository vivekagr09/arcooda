<?php
global $_GLOBALS;

/**
 * Common include file for entire site
 *
 * Includes all required functions, handlers and settings for the entire website
 */
if (array_key_exists('REDIRECT_REMOTE_USER', $_SERVER) && !array_key_exists('REMOTE_USER', $_SERVER)) {
    $_SERVER['REMOTE_USER'] = $_SERVER['REDIRECT_REMOTE_USER'];
}

/**
 * Check to ensure http basic authentication for webadmin
 */
if (0 === strpos($_SERVER['REQUEST_URI'], '/webadmin/') && !array_key_exists('REMOTE_USER', $_SERVER)) {
    if (!array_key_exists('PHP_AUTH_USER', $_SERVER) || !in_array($_SERVER['PHP_AUTH_USER'], ['pbengine', 'Daniel'])) {
        header('HTTP/1.1 401 Unauthorized', true, 401);
        header('WWW-Authenticate: Basic realm="Web Admin"');
        die();
    } else {
        $_SERVER['REMOTE_USER'] = $_SERVER['PHP_AUTH_USER'];
    }
}

/**
 * Microtime performance tracking
 */
$_SERVER['REQUEST_MICROTIME'] = $_SERVER['REQUEST_MICROTIME'] ?? (function ($a) {
    return bcadd($a[0], $a[1], 6);
})(explode(' ', microtime(false)));

srand((int)hexdec(substr(md5(strval(time())), 0, 8)));
mt_srand((int)hexdec(substr(md5(strval(time())), 0, 8)));

include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/errors.inc.php';
include_once __DIR__ . '/settings.inc.php';
include_once __DIR__ . '/connection.inc.php';

/**
 * New module loading methods
 *
 * Include the new module loading functions, and define the locations for
 * possible modules
 * When a piece of code needs a module, it should specify it at the start with
 * moduleLoad('module_name');
 */
include_once __DIR__ . '/modules.inc.php';

//Find all the helper modules available
$temp = glob(__DIR__ . '/home_common/helpers/*_helper.php');
if (is_array($temp)) {
    foreach ($temp as $file) {
        $temp2 = explode('.', basename($file));
        moduleOverride(reset($temp2), $file);
    }
}

moduleOverride('360view', __DIR__ . '/product_360/query.inc.php');
//moduleOverride('aboutus', __DIR__ . '/aboutus/query.inc.php');
moduleOverride('asset', __DIR__ . '/assets/query.inc.php');
moduleOverride('banner', __DIR__ . '/banners/query.inc.php');
//moduleOverride('benefit', __DIR__ . '/benefits/functions/query_benefit.inc.php');
moduleOverride('blogcomment', __DIR__ . '/owner/blogs/query_blogcomment.inc.php');
moduleOverride('bpay', __DIR__ . '/home_common/bpay.inc.php');
moduleOverride('brand', __DIR__ . '/brand/query.inc.php');
//moduleOverride('bulk', __DIR__ . '/bulk_offers/functions/query_bulk.inc.php');
//moduleOverride('bulkoffer', __DIR__ . '/bulk_offers/functions/query_bulk.inc.php');
//moduleOverride('cache', __DIR__ . '/caching.inc.php');
moduleOverride('cart', __DIR__ . '/cart/query.inc.php');
moduleOverride('category', __DIR__ . '/category/query.inc.php');
//moduleOverride('competitor', __DIR__ . '/competitor/query.inc.php');
//moduleOverride('database', __DIR__ . '/connection2.inc.php');
moduleOverride('distributor', __DIR__ . '/distributor/query.inc.php');
//moduleOverride('ebay', __DIR__ . '/ebay/query.inc.php');
//moduleOverride('error', __DIR__ . '/errors.inc.php');
//moduleOverride('faq', __DIR__ . '/faq/functions/query_faq.inc.php');
//moduleOverride('features', __DIR__ . '/features/query.inc.php');
moduleOverride('forwarder', __DIR__ . '/freight_forwarders/query.inc.php');
moduleOverride('forwarder_new', __DIR__ . '/freight_forwarders_new/query.inc.php');
moduleOverride('fpdf', __DIR__ . '/vendor/fpdf/fpdf/src/Fpdf/Fpdf.php');
moduleOverride('freight', __DIR__ . '/freight3/query.inc.php');
//moduleOverride('front_page', __DIR__ . '/front_page/query.inc.php');
//moduleOverride('front_special', __DIR__ . '/front_specials/query.inc.php');
//moduleOverride('game', __DIR__ . '/games/functions/query_games.inc.php');
//moduleOverride('glossary', __DIR__ . '/glossary/functions/query_glossary.inc.php');
//moduleOverride('help', __DIR__ . '/helpsection/functions/query_help.inc.php');
moduleOverride('insurance', __DIR__ . '/insurance/functions/query_insurance.inc.php');
moduleOverride('invoice', __DIR__ . '/invoice/query.inc.php');
//moduleOverride('jobs', __DIR__ . '/jobs/query.inc.php');
//moduleOverride('label', __DIR__ . '/labels/functions/query_label.inc.php');
//moduleOverride('license', __DIR__ . '/license/functions/query_license.inc.php');
moduleOverride('member', __DIR__ . '/member/query.inc.php');
//moduleOverride('metatag', __DIR__ . '/metatags/functions/query_meta.inc.php');
//moduleOverride('movie', __DIR__ . '/movie/functions/query_movie.inc.php');
moduleOverride('news', __DIR__ . '/news/query.inc.php');
moduleOverride('newsletter', __DIR__ . '/newsletter/query.inc.php');
moduleOverride('notify', __DIR__ . '/notify/query.inc.php');
//moduleOverride('package', __DIR__ . '/package/functions/query_package.inc.php');
//moduleOverride('packing', __DIR__ . '/packing/functions/query_packing.inc.php');
moduleOverride('page', __DIR__ . '/page/query.inc.php');
moduleOverride('payment', __DIR__ . '/payment/query.inc.php');
//moduleOverride('paypal', __DIR__ . '/paypal/functions/query_paypal.inc.php');
//moduleOverride('permission', __DIR__ . '/permissions/functions/query_permissions.inc.php');
moduleOverride('phpmailer', __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php');
moduleOverride('product', __DIR__ . '/new_products/functions/query_products.inc.php');
//moduleOverride('profit', __DIR__ . '/profits/query.inc.php');
//moduleOverride('promotion', __DIR__ . '/promotion/query.inc.php');
moduleOverride('recaptcha', __DIR__ . '/vendor/recaptcha/src/autoload.php');
//moduleOverride('schedule', __DIR__ . '/scheduler.inc.php');
moduleOverride('series', __DIR__ . '/series/query.inc.php');
moduleOverride('shipinvoice', __DIR__ . '/shipinvoice/query.inc.php');
moduleOverride('shopping_cart', __DIR__ . '/shopping_carts/functions/query_shopping_cart.inc.php');
//moduleOverride('softjob', __DIR__ . '/softjobs/functions/query_softjob.inc.php');
//moduleOverride('special', __DIR__ . '/member_specials/functions/query_special.inc.php');
moduleOverride('staff', __DIR__ . '/staff/query.inc.php');
//moduleOverride('stocklist', __DIR__ . '/stocklist/query.inc.php');
moduleOverride('stripe', __DIR__ . '/stripe-php/init.php');
//moduleOverride('supplier', __DIR__ . '/suppliers/functions/query_suppliers.inc.php');
moduleOverride('supplier', __DIR__ . '/new_suppliers/functions/query_suppliers.inc.php');
moduleOverride('supplier_new', __DIR__ . '/supplier_new/query.inc.php');
moduleOverride('supported', __DIR__ . '/supported/query.inc.php');
//moduleOverride('tag', __DIR__ . '/tags/functions/query_tags.inc.php');
moduleOverride('tcpdf', __DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php');
moduleOverride('template', __DIR__ . '/home_common/template.inc.php');
//moduleOverride('testimonial', __DIR__ . '/testimonial/query.inc.php');
//moduleOverride('tool', __DIR__ . '/member_tools/functions/query_tools.inc.php');
//moduleOverride('tradeshow', __DIR__ . '/tradeshow/functions/query_tradeshow.inc.php');
//moduleOverride('visitor', __DIR__ . '/visitor/functions/query_visitor.inc.php');
moduleOverride('voucher', __DIR__ . '/voucher/query.inc.php');
moduleOverride('wanted', __DIR__ . '/wanted/functions/query_wanted.inc.php');
moduleOverride('writeexcel', __DIR__ . '/home_common/php_writeexcel/query.inc.php');
moduleOverride('review', __DIR__ . '/review/function/query.inc.php');
//-----------------------------------------------------------------------------

//Functions
include_once __DIR__ . '/functions/override.inc.php';
include_once __DIR__ . '/functions/bcmath.inc.php';
include_once __DIR__ . '/functions/shopping_cart.inc.php';
include_once __DIR__ . '/functions/formatting.inc.php';
include_once __DIR__ . '/functions/conversions.inc.php';
include_once __DIR__ . '/functions/lists.inc.php';
include_once __DIR__ . '/home_common/formatting.inc.php';
include_once __DIR__ . '/home_common/conversions.inc.php';
include_once __DIR__ . '/home_common/lists.inc.php';
include_once __DIR__ . '/home_common/http_status.inc.php';
 moduleLoad('invoice');
//-----------------------------------------------------------------------------

include_once __DIR__ . '/options.inc.php';
 ini_set('display_errors', 'Off');
 error_reporting(0);