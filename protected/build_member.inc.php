<?php
namespace app;

use BrowscapPHP\Browscap;
use Doctrine\Common\Cache\FilesystemCache;
use Psr\Log\NullLogger;
use Roave\DoctrineSimpleCache\SimpleCacheAdapter;
session_start();
global $_GLOBALS;
/**
 * Returns:
 *  @var mixed[]   $member / member_preference
 *  @var mixed[][] $member_contacts
 *  @var mixed[][] $member_couriers
 *  @var mixed[][] $member_forwarders
 *  @var mixed[][] $member_logins
 *  @var mixed[][] $member_mailinglists
 *  @var mixed[][] $member_notifyparties
 *  @var mixed[][] $member_shippingaddresses
 *
 * Modifies:
 *  @var string[] $issues
 */
moduleLoad('member'); //memberDefault

$member = array_merge(memberDefault(), [
    'currency'          => 'USD',
    'products_per_page' => 32,
    'news_per_page'     => 10,
]);

$member_contacts          = [];
$member_couriers          = [];
$member_forwarders        = [];
$member_logins            = [];
$member_mailinglists      = [];
$member_notifyparties     = [];
$member_shippingaddresses = [];

/**
 * Set detected data
 */
$detected                 = [];
$detected['ip']           = getClientIP();
$detected['country_info'] = getCountryInfo($detected['ip']);
$detected['country']      = $detected['country_info']['a2'] ?? '';
$detected['user_agent']   = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (false || class_exists('Browscap')) {
    $bc = new Browscap(
        new SimpleCacheAdapter(
            new FilesystemCache(__DIR__ . '/vendor/browscap/browscap-php/resources')
        ),
        new NullLogger()
    );
    $detected['browser_info'] = $bc->getBrowser();
    $detected['browser']      = $detected['browser_info']->browser ?? '';
    $detected['version']      = $detected['browser_info']->version ?? '';
} else {
    $detected['browser_info'] = @get_browser($detected['user_agent'], true);
    $detected['browser']      = $detected['browser_info']['browser'] ?? '';
    $detected['version']      = $detected['browser_info']['version'] ?? '';
}
$detected['request_url']  = relative_url($_SERVER['REQUEST_URI']);
$detected['language']     = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '*';

$member['country']       = $detected['country'];
$member['signup_IP']     = $detected['ip'];
$member['browser']       = $detected['browser'] ?? '';
$member['b_version']     = $detected['version'] ?? '';
$member['user_agent']    = $detected['user_agent'];
$member['create_url']    = $detected['request_url'];
$member['date_joined']   = formatDateTime();
$member['site_id']       = $_GLOBALS['site_settings']['site_id'];
$member['lang']          = $detected['language'];

/**
 * Set visitor related data
 */
if (array_key_exists('visitor', $_GLOBALS) && is_array($_GLOBALS['visitor'])) {
    foreach ([
        'currency'           => 'currency',
    ] as $k => $v) {
        if (array_key_exists($v, $_GLOBALS['visitor']) && !is_null($_GLOBALS['visitor'][$v])) {
            $member[$k] = $_GLOBALS['visitor'][$v];
        }
    }
    $member['currency'] = $_GLOBALS['visitor']['currency'];
}

if ('register' === ($_POST['action'] ?? '')) {
    if (array_key_exists('username', $_POST)) {
        if (preg_match('/\\s/', $_POST['username'])) {
            $issues['username'] = 'Please use a single email address';
        }
    }
     if (empty($_POST['captcha']) || $_POST['captcha'] != $_SESSION['rand_code'] ) {
            $issues['captcha'] = 'Invalid Captcha';
    }
    $member_validate = [
        'first_name'       => 'b_givenName',
        'last_name'        => 'b_familyName',
        // 'address_1'        => 'b_streetAddress',
        'address'          => 'b_streetAddress',
        'city'             => 'b_addressLevel2',
        'state'            => 'b_addressLevel1',
        'post_code'        => 'b_postalCode',
        'country'          => 'b_country',
        'phone_no'         => 'b_homeTel',
        'email_address'    => 'username',
        'confirm_password' => 'newPassword_repeat',
        'password'         => 'newPassword',
        'account_type'     => 'account_type',
        "address_type"     => 'b_addressType'
    ];
    if($_POST['account_type']=='company'){
        $company_validate=[
            'company_name'=>'b_organization',
        ];
        $member_validate=array_merge($member_validate,$company_validate);
    }
    if($_POST['billing_as_shipping']=='n'){
        $shipping_validate=[
                'company_name'   => 's_organization',
                'address'        => 's_streetAddress',
                'city'           => 's_addressLevel2',
                'state'          => 's_addressLevel1',
                'post_code'      => 's_postalCode',
                'country'        => 's_country',
                'phone_no'       => 's_homeTel',
                'name'           => 's_givenName',
                'last_name'      => 's_familyName',
                'address_type'   => 's_addressType',
                "email"          => 's_email'
        ];
        $member_validate=array_merge($member_validate,$shipping_validate);
    }
    foreach ($member_validate as $k => $v) {
        if (array_key_exists($v, $_POST)) {
            if ('' == $_POST[$v]) {
                $issues[$v] = 'This field is required';
            }
        }
    }
     $member_post=[
            'company_name'       => 'b_organization',
            'organization_title' => 'b_organizationTitle',
            'first_name'         => 'b_givenName',
            'last_name'          => 'b_familyName',
            'address'            => 'b_streetAddress',
            'address_1'          => 'b_streetAddress',
            'address_type'       => 'b_addressType',
            'city'               => 'b_addressLevel2',
            'state'              => 'b_addressLevel1',
            'post_code'          => 'b_postalCode',
            'country'            => 'b_country',
            'company_number'     => 'b_company_number',
            'vat_number'         => 'b_vat_number',
            'abn_number'         => 'b_abn_number',
            'eori_number'        => 'b_eori_number',
            'phone_no'           => 'b_homeTel',
            'fax_no'             => 'b_faxTel',
            'mobile_no'          => 'b_mobileTel',
            'website'            => 'b_url',
            'phone_no2'          => 'b_workTel',
            'account_type'       => 'account_type',
            'email_address'      => 'username',
            'mailing_list'       => 'mailing_list',
            'best_described'     => 'described_as',
            'currency'           => 'b_transactionCurrency',
            'special_request'    => 's_instructions',
    ];
    if($_POST['b_url']!=''){
        // @todo Why are we doing this? Customer website must be prepended with https://www. ?
        $_POST['b_url'] = 'https://www.' . preg_replace('@^(https?://)?www\\.@', '', $_POST['b_url']);
    }
    foreach ($member_post as $k => $v) {
        if (array_key_exists($v, $_POST)) {
            $member[$k] = trim($_POST[$v]);
        }
    }
    if (array_key_exists('newPassword', $_POST) && strlen($_POST['newPassword']) >= 8) {
        if (array_key_exists('newPassword_repeat', $_POST) && $_POST['newPassword'] === $_POST['newPassword_repeat']) {
            $member_logins = ['password' => md5($_POST['newPassword'])];
        } else {
            $issues['newPassword'] = 'Passwords do not match';
        }
    } else {
        $issues['newPassword'] = 'Password must be 8 characters or more';
    }
    if (count($issues) == 0) {
        $member_contacts = [
            [
                'name'          => trim($member['first_name']),
                'last_name'     => trim($member['last_name']),
                'email'         => $member['email_address'],
                'phone'         => $member['phone_no'],
                'title'         => $member['organization_title'],
                'main'          => 'y',
                'send_email_cc' => 'y',
                'is_profile'    => 1
            ],
        ];
        $member_shippingaddresses[] = ('y' == $_POST['billing_as_shipping']) ? [
            'company_name'   => $_POST['b_organization'],
            'address'        => $_POST['b_streetAddress'],
            'city'           => $_POST['b_addressLevel2'],
            'state'          => $_POST['b_addressLevel1'],
            'post_code'      => $_POST['b_postalCode'],
            'country'        => $_POST['b_country'],
            'phone_no'       => $_POST['b_homeTel'],
            'phone_no2'      => $_POST['b_workTel'],
            'fax_no'         => $_POST['b_faxTel'],
            'mobile_no'      => $_POST['b_mobileTel'],
            'contact'        => $_POST['b_givenName'],
            'contact_last'   => $_POST['b_familyName'],
            'address_type'   => $_POST['b_addressType'],
            'email'          => $_POST['username'],
            'instructions'   => $_POST['s_instructions'],
            'default'        => 'y',
            'company_number' => $_POST['b_company_number'],
            'vat_number'     => $_POST['b_vat_number'],
            'eori_number'    => $_POST['b_eori_number'],
            'abn_number'     => $_POST['b_abn_number'],
        ] : [
            'company_name'   => $_POST['s_organization'],
            'address'        => $_POST['s_streetAddress'],
            'city'           => $_POST['s_addressLevel2'],
            'state'          => $_POST['s_addressLevel1'],
            'post_code'      => $_POST['s_postalCode'],
            'country'        => $_POST['s_country'],
            'phone_no'       => $_POST['s_homeTel'],
            'phone_no2'      => $_POST['s_workTel'],
            'fax_no'         => $_POST['s_faxTel'],
            'mobile_no'      => $_POST['s_mobileTel'],
            'contact'        => $_POST['s_givenName'],
            'contact_last'   => $_POST['s_familyName'],
            'address_type'   => $_POST['s_addressType'],
            'email'          => $_POST['s_email'],
            'instructions'   => $_POST['s_instructions'],
            'default'        => 'y',
            'company_number' => $_POST['s_company_number'],
            'vat_number'     => $_POST['s_vat_number'],
            'eori_number'    => $_POST['s_eori_number'],
            'abn_number'     => $_POST['s_abn_number'],
        ];
        if ('y' == $_POST['own_freight']) {
            $member_couriers[] = [
                'courier_id'   => $_POST['courier'],
                'courier_name' => (0 == $_POST['courier']) ? $_POST['courier_name'] : null,
                'account_num'  => $_POST['account_number'],
                'insurance'    => $_POST['ship_insurance'],
                'notes'        => $_POST['s_instructions'],
                'main'         => 'y',
            ];
        }
    }
}
foreach ([
    'first_name'    => 'givenName',
    'last_name'     => 'familyName',
    'country'       => 'country',
    'email_address' => 'email',
] as $k => $v) {
    if (array_key_exists($v, $_POST)) {
        $member[$k] = trim($_POST[$v]);
    }
}
// add new user for newslater  by non registered user
if ('newsletter' === ($_POST['action'] ?? '')) {
    if (array_key_exists('username', $_POST)) {
        if (preg_match('/\\s/', $_POST['username'])) {
            $issues['username'] = 'Please use a single email address';
        }
    }
    foreach ([
        'first_name'    => 'given-name',
        'last_name'     => 'family-name',
        'country'       => 'country',
        'email_address' => 'username',
    ] as $k => $v) {
        if (array_key_exists($v, $_POST)) {
            $member[$k] = trim($_POST[$v]);
            if ('' == $_POST[$v]) {
                $issues[$v] = 'This field is required';
            }
        }
    }
    if (count($issues) == 0) {
        $member_contacts = [[
            'name'          => trim($member['first_name'] . ' ' . $member['last_name']),
            'email'         => $member['email_address'],
            'main'          => 'y',
            'send_email_cc' => 'y',
            'is_profile'    => 1,
        ]];
        $member_notifyparties = [
            [
                'company_name' => '',
                'country'      => $member['country'],
                'contact'      => trim($member['first_name'] . ' ' . $member['last_name']),
                'email'        => $member['email_address'],
            ],
        ];
        $member_shippingaddresses = [
            [
                'name'    => $member['first_name'],
                'email'   => $member['email_address'],
                'surname' => $member['last_name'],
                'company' => '',
                'country' => $member['country'],
                'default' => 'y',
            ],
        ];
    }
}

/**
 * Override with Inquiry form
 */
if ('Inquiry' === ($_POST['action'] ?? '')) {

}


/**
 * Override with My Account form
 */
if ('Account' === ($_POST['action'] ?? '')) {

}


/**
 * Detect cross-country customer
 */
$detected['countries'] = [
    $detected['country'],
    $member['country'],
];
foreach ([$member_forwarders, $member_notifyparties, $member_shippingaddresses] as $k) {
    foreach ($k as $v) {
        if ($v['country'] !== '') {
            $detected['countries'][] = $v['country'];
        }
    }
}
$detected['countries'] = array_unique($detected['countries']);
$member['flag'] = (count($detected['countries']) > 1) ? 'red' : 'green';
