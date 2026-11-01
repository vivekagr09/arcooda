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
    'account_type' => 'lite',
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

if (array_key_exists('inquiry', $posted) && is_array($posted['inquiry'])) {
    $inquiry = $posted['inquiry'];
    if (array_key_exists('country', $inquiry)) {
        $member['flag']    = ($inquiry['country'] == $member['country']) ? 'green' : 'red';
        $member['country'] = $inquiry['country'];
    }
    if (array_key_exists('email', $inquiry)) {
        $member['email_address'] = $inquiry['email'];
    }
    if (array_key_exists('name', $inquiry)) {
        $split                = preg_split('/[ \t]+/', $inquiry['name']);
        $member['name']       = $member['first_name'] = array_shift($split);
        $member['surname']    = $member['last_name']  = implode(' ', $split);
        $member['full_name']  = $inquiry['name'];
    }
    if (array_key_exists('newsletter', $inquiry)) {
        $member['mailing_id'] = $inquiry['newsletter'];
        if (is_array($inquiry['newsletter'])) {
            $member['mailing_id'] = implode(',', $inquiry['newsletter']);
        }
    }
    if (array_key_exists('site_id', $inquiry)) {
        $member['site_id'] = $inquiry['site_id'];
    }
}

if (array_key_exists('warranty', $posted) && is_array($posted['warranty'])) {
    $warranty = $posted['warranty'];
    if (array_key_exists('country', $warranty)) {
        $member['flag']    = ($warranty['country'] == $member['country']) ? 'green' : 'red';
        $member['country'] = $warranty['country'];
    }
    if (array_key_exists('email', $warranty)) {
        $member['email_address'] = $warranty['email'];
    }
    if (array_key_exists('name', $warranty)) {
        $split                = preg_split('/[ \t]+/', $warranty['name']);
        $member['first_name'] = array_shift($split);
        $member['last_name']  = implode(' ', $split);
    }
    if (array_key_exists('city', $warranty)) {
        $member['city'] = $warranty['city'];
    }
    if (array_key_exists('post_code', $warranty)) {
        $member['post_code'] = $warranty['post_code'];
    }
}

if (array_key_exists('wanted', $posted) && is_array($posted['wanted'])) {
    $wanted = $posted['wanted'];
    if (array_key_exists('country', $wanted)) {
        $member['flag']    = ($wanted['country'] == $member['country']) ? 'green' : 'red';
        $member['country'] = $wanted['country'];
    }
    if (array_key_exists('email', $wanted)) {
        $member['email_address'] = $wanted['email'];
    }
    if (array_key_exists('name', $wanted)) {
        $split                = preg_split('/[ \t]+/', $wanted['name']);
        $member['first_name'] = array_shift($split);
        $member['last_name']  = implode(' ', $split);
    }
    if (array_key_exists('city', $wanted)) {
        $member['city'] = $wanted['city'];
    }
    if (array_key_exists('post_code', $wanted)) {
        $member['post_code'] = $wanted['post_code'];
    }
}

if (array_key_exists('triage', $posted) && is_array($posted['triage'])) {
    $triage = $posted['triage'];
    if (array_key_exists('country', $triage)) {
        $member['flag']    = ($triage['country'] == $member['country']) ? 'green' : 'red';
        $member['country'] = $triage['country'];
    }
    if (array_key_exists('email', $triage)) {
        $member['email_address'] = $triage['email'];
    }
    if (array_key_exists('name', $triage)) {
        $split                = preg_split('/[ \t]+/', $triage['name']);
        $member['first_name'] = array_shift($split);
        $member['last_name']  = implode(' ', $split);
    }
    if (array_key_exists('city', $triage)) {
        $member['city'] = $triage['city'];
    }
    if (array_key_exists('post_code', $triage)) {
        $member['post_code'] = $triage['post_code'];
    }
}

unset($browser);
