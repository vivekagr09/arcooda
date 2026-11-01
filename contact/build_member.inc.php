<?php
/**
 * Returns:
 *  $member
 */

moduleLoad('member'); //memberDefault

$browser = array_merge(
    array('parent' => '', 'browser' => ''),
    get_browser($_SERVER['HTTP_USER_AGENT'], true)
);

$member = array_merge(memberDefault(), array(
    'account_type' => 'private',
    'b_version'    => $browser['parent'],
    'browser'      => $browser['browser'],
    'login_ip'     => $_SERVER['REMOTE_ADDR'],
    'currency'     => $_GLOBALS['site_options']['DEFAULT_CURRENCY'],
    'site_id'      => $_GLOBALS['site_settings']['site_id'],
    'user_agent'   => $_SERVER['HTTP_USER_AGENT'],
    'signup_IP'    => $_SERVER['REMOTE_ADDR'],
));

if (array_key_exists('visitor', $_GLOBALS) && is_array($_GLOBALS['visitor'])) {
    foreach (array(
        'address',
        'best_described',
        'business_interests',
        'city',
        'company_name',
        'country',
        'currency',
        'email_address',
        'fax_no',
        'first_name',
        'address',
        'flag',
        'forum_user_id',
        'freight_preference',
        'general_interests',
        'insurance_pref',
        'last_contacted',
        'last_name',
        'mailing_id',
        'mailing_list',
        'mailing_products',
        'mobile_no',
        'news_per_page',
        'nick_name',
        'packing_string',
        'partner',
        'phone_no',
        'phone_no2',
        'post_code',
        'products_order',
        'products_per_page',
        'reseller',
        'signup_IP',
        'state',
        'status',
        'timezone',
        'vat_number',
        'website',
        'xbox_qty',
        'xbox_status',
    ) as $k) {
        if (array_key_exists($k, $_GLOBALS['visitor']) && '' != $_GLOBALS['visitor'][$k]) {
            $member[$k] = $_GLOBALS['visitor'][$k];
        }
    }
}
$member['address']='';
$member['company_name']='';
$member['fax_no']='';
$member['mobile_no']='';
$member['website']='';
$member['browser']='';
if (array_key_exists('member_location', $_REQUEST)) {
    $member['flag']    = ($_REQUEST['member_location'] == $member['country']) ? 'green' : 'red';
    $member['country'] = $_REQUEST['member_location'];
}
if (array_key_exists('member_email', $_REQUEST)) {
    $member['email_address'] = $_REQUEST['member_email'];
}
if (array_key_exists('member_name', $_REQUEST)) {
    $split               = preg_split("/[ \t]+/", $_REQUEST['member_name']);
    $member['name']      = $member['first_name'] = array_shift($split);
    $member['surname']   = $member['last_name']  = implode(' ', $split);
    $member['full_name'] = $_REQUEST['member_name'];
    unset($split);
}
if (array_key_exists('given-name', $_REQUEST)) {
    $member['name']      = $member['first_name'] = $_REQUEST['given-name'];
}
if (array_key_exists('family-name', $_REQUEST)) {
    $member['surname']   = $member['last_name']  = $_REQUEST['family-name'];
}
if (array_key_exists('addresstype', $_REQUEST)) {
    $member['address_type'] = $_REQUEST['addresstype'];
}
if (array_key_exists('street-address', $_REQUEST)) {
    $member['address'] = $_REQUEST['street-address'];
}
if (array_key_exists('address-level2', $_REQUEST)) {
    $member['city']  =$_REQUEST['address-level2'];
}
if (array_key_exists('address-level1', $_REQUEST)) {
    $member['state'] = $_REQUEST['address-level1'];
}
if (array_key_exists('postal-code', $_REQUEST)) {
    $member['post_code'] = $_REQUEST['postal-code'];
}
if (array_key_exists('member_phone', $_REQUEST)) {
    $member['phone_number'] = $_REQUEST['member_phone'];
    $member['phone_no']=$_REQUEST['member_phone'];
}
unset($browser);
