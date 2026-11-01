<?php
namespace app;

use app\models\TwigCurrency;
use app\models\TwigUrlManager;
use app\models\TwigUrlRule;

use CommerceGuys\Addressing\Address;
use CommerceGuys\Addressing\Formatter\PostalLabelFormatter;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;

include_once __DIR__ . '/vendor/autoload.php';

function convertCurrency($amount, $from = null, $to = null, $round = false, $precision = 2) {
    return (new TwigCurrency($amount, $from, $precision))->getAmount();
}

function getCountryList() {
    $countryRepository = new CountryRepository();
    return $countryRepository->getList();
}

function memberFormatAddress($member) {
    $addressFormatRepository = new AddressFormatRepository();
    $countryRepository       = new CountryRepository();
    $subdivisionRepository   = new SubdivisionRepository();
    $formatter               = new PostalLabelFormatter($addressFormatRepository, $countryRepository, $subdivisionRepository);

    $split   = array_filter(explode("\n", $member['address_1'] . "\n" . $member['address_2']), function ($a) { return !empty($a); });
    $address = new Address(
        $member['country'],
        $member['state'],
        $member['city'],
        '',
        $member['zip'],
        '',
        $split[0] ?? '',
        $split[1] ?? '',
        $member['company'],
        $member['first_name'] ?? $member['name'],
        '',
        $member['last_name'] ?? $member['surname']
    );
    return $formatter->format($address, ['origin_country' => '__']);
}

include_once __DIR__ . '/../webadmin/common.inc.php';

moduleLoad('member'); //memberShippingAddressesFromDatabase, memberHasValue, memberFromDatabase
moduleLoad('freight');

global $_GLOBALS;
global $url_manager;
$_GLOBALS['url_manager'] = $url_manager = new TwigUrlManager([
    'rules' => [
        new TwigUrlRule(['pattern' => '/icon/<slug:\S+>-<id:\d+>', 'route' => 'icon/image', 'suffix' => '.jpg']),
        new TwigUrlRule(['pattern' => '/icon/<slug:\S+>-<id:\d+>', 'route' => 'icon/view', 'suffix' => '.html']),
        new TwigUrlRule(['pattern' => '/icon/<slug:\S+>-<id:\d+>', 'route' => 'icon/view', 'suffix' => '/', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/icon/<slug:\S+>-<id:\d+>', 'route' => 'icon/view', 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),

        new TwigUrlRule(['pattern' => '/<slug:\S+>-arcade-machines-<m:\d+>', 'route' => 'manufacturer/image', 'suffix' => '.jpg']),
      /*  new TwigUrlRule(['pattern' => '/<slug:\S+>-arcade-machines-<m:\d+>', 'route' => 'search/category', 'suffix' => '.html', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/<slug:\S+>-arcade-machines-<m:\d+>', 'route' => 'search/category', 'suffix' => '/']),
        new TwigUrlRule(['pattern' => '/<slug:\S+>-arcade-machines-<m:\d+>', 'route' => 'search/category', 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),*/

        new TwigUrlRule(['pattern' => '/buy/<slug:\S+>-<ss_id:\d+>-<id:\d+>-sml', 'route' => 'product/thumb', 'defaults' => ['ss_id' => 0], 'suffix' => '.jpg']),
        new TwigUrlRule(['pattern' => '/buy/<slug:\S+>-<id:\d+>-sml', 'route' => 'product/thumb', 'defaults' => ['ss_id' => 0], 'suffix' => '.jpg', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/buy/<slug:\S+>-<ss_id:\d+>-<id:\d+>', 'route' => 'product/image', 'defaults' => ['ss_id' => 0], 'suffix' => '.jpg']),
        new TwigUrlRule(['pattern' => '/buy/<slug:\S+>-<id:\d+>', 'route' => 'product/image', 'defaults' => ['ss_id' => 0], 'suffix' => '.jpg', 'mode' => TwigUrlRule::PARSING_ONLY]),
         new TwigUrlRule(['pattern' => '/manual/<slug:\S+>-<id:\d+>', 'route' => 'site/manual', 'suffix' => '.html']),
       /* new TwigUrlRule(['pattern' => '/buy/<slug:\S+>-<id:\d+>', 'route' => 'product/view', 'suffix' => '.html']),
        new TwigUrlRule(['pattern' => '/manual/<slug:\S+>-<id:\d+>', 'route' => 'site/manual', 'suffix' => '.html']),
        new TwigUrlRule(['pattern' => '/buy/<slug:\S+>-<id:\d+>', 'route' => 'product/view', 'suffix' => '/', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/buy/<slug:\S+>-<id:\d+>', 'route' => 'product/view', 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),*/

 /*       new TwigUrlRule(['pattern' => '/buy/<parent_slug:\S+>/<slug:\S+>', 'route' => 'search/category', 'suffix' => '.html', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/buy/<parent_slug:\S+>/<slug:\S+>', 'route' => 'search/category', 'suffix' => '/']),
        new TwigUrlRule(['pattern' => '/buy/<parent_slug:\S+>/<slug:\S+>', 'route' => 'search/category', 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/buy/<slug:\S+>', 'route' => 'search/category', 'suffix' => '.html', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/buy/<slug:\S+>', 'route' => 'search/category', 'suffix' => '/']),
        new TwigUrlRule(['pattern' => '/buy/<slug:\S+>', 'route' => 'search/category', 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),*/

        new TwigUrlRule(['pattern' => '/n/<slug:\S+>-<id:\d+>-sml', 'route' => 'news/thumb', 'suffix' => '.jpg']),
        new TwigUrlRule(['pattern' => '/n/<slug:\S+>-<id:\d+>', 'route' => 'news/image', 'suffix' => '.jpg']),
        new TwigUrlRule(['pattern' => '/n/<slug:\S+>-<id:\d+>', 'route' => 'news/view', 'suffix' => '.html']),
        new TwigUrlRule(['pattern' => '/n/<slug:\S+>-<id:\d+>', 'route' => 'news/view', 'suffix' => '/', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/n/<slug:\S+>-<id:\d+>', 'route' => 'news/view', 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),

        new TwigUrlRule(['pattern' => '/latest-news', 'route' => 'search/news', 'defaults' => ['category' => 'Latest News'], 'suffix' => '.html', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/latest-news', 'route' => 'search/news', 'defaults' => ['category' => 'Latest News'], 'suffix' => '/']),
        new TwigUrlRule(['pattern' => '/latest-news', 'route' => 'search/news', 'defaults' => ['category' => 'Latest News'], 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/arcade-games-news', 'route' => 'search/news', 'defaults' => ['category' => 'Arcade Games'], 'suffix' => '.html', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/arcade-games-news', 'route' => 'search/news', 'defaults' => ['category' => 'Arcade Games'], 'suffix' => '/']),
        new TwigUrlRule(['pattern' => '/arcade-games-news', 'route' => 'search/news', 'defaults' => ['category' => 'Arcade Games'], 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/game-boards-news', 'route' => 'search/news', 'defaults' => ['category' => 'Game Boards'], 'suffix' => '.html', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/game-boards-news', 'route' => 'search/news', 'defaults' => ['category' => 'Game Boards'], 'suffix' => '/']),
        new TwigUrlRule(['pattern' => '/game-boards-news', 'route' => 'search/news', 'defaults' => ['category' => 'Game Boards'], 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/arcade-parts-news', 'route' => 'search/news', 'defaults' => ['category' => 'Arcade Parts'], 'suffix' => '.html', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/arcade-parts-news', 'route' => 'search/news', 'defaults' => ['category' => 'Arcade Parts'], 'suffix' => '/']),
        new TwigUrlRule(['pattern' => '/arcade-parts-news', 'route' => 'search/news', 'defaults' => ['category' => 'Arcade Parts'], 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/merchandise-news', 'route' => 'search/news', 'defaults' => ['category' => 'Merchandise'], 'suffix' => '.html', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/merchandise-news', 'route' => 'search/news', 'defaults' => ['category' => 'Merchandise'], 'suffix' => '/']),
        new TwigUrlRule(['pattern' => '/merchandise-news', 'route' => 'search/news', 'defaults' => ['category' => 'Merchandise'], 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/arcade-news', 'route' => 'search/news', 'suffix' => '.html', 'mode' => TwigUrlRule::PARSING_ONLY]),
        new TwigUrlRule(['pattern' => '/arcade-news', 'route' => 'search/news', 'suffix' => '/']),
        new TwigUrlRule(['pattern' => '/arcade-news', 'route' => 'search/news', 'suffix' => '', 'mode' => TwigUrlRule::PARSING_ONLY]),
    ]
]);

if (preg_match('/^([0-9a-f]{1,8})([0-9a-f]{8})$/', $_COOKIE['visitor_id'] ?? '', $m)) {
    moduleLoad('visitor'); //visitorHasValue, visitorFromDatabase, visitorIncrementHits
    if (function_exists('visitorHasValue') && visitorHasValue($visitor_id = (int)hexdec($m[1]), 'rand_num', (int)hexdec($m[2]))) {
        $_GLOBALS['visitor'] = visitorFromDatabase($visitor_id);
        $_GLOBALS['site_options']['DEFAULT_CURRENCY'] = $_GLOBALS['visitor']['currency'] ?? $_GLOBALS['site_options']['DEFAULT_CURRENCY'];
        visitorIncrementHits($visitor_id);
    } else {
        custom_setcookie('visitor_id', $_COOKIE['visitor_id'] = '', time() - 3600, '/');
    }
}

if (preg_match('/^([0-9a-f]{1,8})([0-9a-f]{8})$/', $_COOKIE['member_id'] ?? '', $m)) {
    moduleLoad('member');
    if (function_exists('memberHasValue') && memberHasValue($member_id = (int)hexdec($m[1]), 'rand_num', (int)hexdec($m[2]))) {
        $_GLOBALS['member']                           = memberFromDatabase($member_id);
        $_GLOBALS['member']['ship_addr']              = memberShippingAddressesFromDatabase($member_id);
        $_GLOBALS['site_options']['DEFAULT_CURRENCY'] = $_GLOBALS['member']['currency'] ?? $_GLOBALS['site_options']['DEFAULT_CURRENCY'];
    } else {
        custom_setcookie('member_id', $_COOKIE['member_id'] = '', time() - 3600, '/');
    }
}

if (!isset($_GLOBALS['shopping_cart']) && function_exists('cartLoadContents')) {
    $_GLOBALS['shopping_cart'] = cartLoadContents($_GLOBALS['member']['id'] ?? 0, $_GLOBALS['visitor']['id'] ?? 0);
}
if (!isset($_GLOBALS['quote_cart']) && function_exists('cartLoadContents')) {
    $_GLOBALS['quote_cart'] = cartLoadContents($_GLOBALS['member']['id'] ?? 0, $_GLOBALS['visitor']['id'] ?? 0, 'quote');
}

if (array_key_exists('newsletter_id', $_COOKIE)) {
    $_GLOBALS['newsletter']['id']        = hexdec(substr($_COOKIE['newsletter_id'], -8));
    $_GLOBALS['newsletter']['member_id'] = hexdec(substr(substr($_COOKIE['newsletter_id'], 0, -8), -8));
}

if (is_object($redirect_data = json_decode($_COOKIE['redirect'] ?? 'null'))) {
    if ($redirect_data->value) {
        $_GLOBALS['redirect'] = $redirect_data;
        setcookie('redirect', json_encode([
            'value'   => false,
            'message' => '',
            'type'    => '',
        ]) ?: '', gmmktime(0, 0, 0, (int)gmdate('n'), (int)gmdate('j'), (int)gmdate('Y') + 1) ?: 0, '/');
    }
}
