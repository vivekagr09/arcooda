<?php
global $_STACK;
if (!isset($_STACK)) {
    $_STACK = [];
}

global $_GLOBALS;

 ini_set('display_errors', 'Off');
 error_reporting(0);

if (is_null($_GLOBALS['layout'] ?? null)) {
    /**
     * Detect popup requests
     *
     * By placing popup=# in the url, we can determine whether the request should be a popup
     * We can also detect whether this request is opening inside a thickbox by
     * detecting the removal of TB_iframe from the url, and keeping of popup=1
     * Also make sure to never hide the header unless the referer is from the same site
     */
    $_GLOBALS['layout'] = (parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_HOST) == $_SERVER['HTTP_HOST'] && (
        //If this is a thickbox iframe, we use popup=1, and thickbox removes the TB_iframe variable
        ('1' == ($posted['popup'] ?? '0') && !isset($posted['TB_iframe'])) ||
        //If this is an ajax popup or iframe, we use popup=2
        ('2' == ($posted['popup'] ?? '0')) ||
        //If this is any other popup, we use popup=3
        ('3' == ($posted['popup'] ?? '0')) ||
        //Legacy urls tend to use return_message as an indicator
        array_key_exists('return_message', $_GET)
    )) ? 'popup' : 'default';
}

if (!isset($_GLOBALS['site_settings'])) {
    $settings = [];

    $settings['site_id']        = 39;
    $settings['dom_int']        = 'int';
    $settings['site_name']      = 'Arcooda';
    $settings['home_uri']       = 'http://live.ethan.arcooda.com/';
    $settings['static_uri']     = 'https://static.arcooda.com/';
    $settings['static_path']    = __DIR__ . '/../static';
    $settings['supply_country'] = 'CN';

    if ('arcooda.com' !== $_SERVER['SERVER_NAME']) {
        $settings['home_uri'] = sprintf(
            '%s://%s/',
            ('on' == ($_SERVER['HTTPS'] ?? 'off')) ? 'https' : 'http',
            $_SERVER['SERVER_NAME']
        );
        unset($settings['static_uri']);
        unset($settings['static_path']);
    }

    $settings['email_sales']       = 'sales@arcooda.com';
    $settings['email_news']        = 'sales@arcooda.com';
    $settings['email_support']     = 'web@arcooda.com';
    $settings['email_error']       = 'errors@arcooda.com';
    $settings['error_email']       = 'errors@arcooda.com';
    $settings['admin_error_email'] = 'errors@arcooda.com';

    //-------------------------------------------------------------------------

    $settings['recaptcha_public']  = '6LdknhUTAAAAAKFmRXzT3xPpFVq5BeHFa6nJBJgv';
    $settings['recaptcha_private'] = '6LdknhUTAAAAAKlkwMwiFZRkvgZKTbBJLjJxhjIu';

    //-------------------------------------------------------------------------

    $settings['bpay_biller']     = '424606';
    $settings['bpay_minimum_au'] = 50;

    //-------------------------------------------------------------------------

    $settings['airwallex'] = [
        'clientId'   => 'ykHIr7I_RrquDo5sakLptg',
        'apiKey'     => 'f5ddea11913599e89eee10603356aec8ddfc9d0948ae088c23b8d09766bc6690f001e244f07cc187616729f9bbd6a5ff',
        'production' => true,
    ];
    //-------------------------------------------------------------------------

    //$settings['credit_user'] = 'ANZHWYGP';
    //$settings['credit_user']       = 'HIGROUCOM01';
    //$settings['credit_return_url'] = $settings['home_uri'] . 'checkout/return_credit.html?DR=${DR}';
    //$settings['credit_manual_url'] = $settings['home_uri'] . 'return_credit.html?DR=${DR}';
    //$settings['credit_server']     = '127.0.0.1';
    //$settings['credit_maximum_au'] = 500;
    //$settings['amex_maximum_au'] = 500;
    //$settings['visa_maximum_au'] = 500;
    //$settings['mastercard_maximum_au'] = 500;

    //-------------------------------------------------------------------------

    //$settings['paypal_server']     = 'www.paypal.com';
    //$settings['paypal_user']       = 'sales@arcooda.com';
    //$settings['paypal_password']   = '';
    //$settings['paypal_return_url'] = $settings['home_uri'] . 'checkout/return_paypal.html';
    //$settings['paypal_image_url']  = '';
    //$settings['paypal_maximum_au'] = 500;
    //$settings['paypal_maximum_usd'] = 500;

    //-------------------------------------------------------------------------

    /*
    $settings['phplive_baseurl'] = '/phplive';
    */

    //-------------------------------------------------------------------------

    $settings['handling_minimum_us'] = '10.00';

    //-------------------------------------------------------------------------

    /*
    $settings['cash_address']   = "Address:\n44 - 48 Maitland Road\nMayfield East NSW 2304\nAustralia\n\nPhone:\n+612-4968-9313 (5 lines)\n\nFax:\n+612-4968-9314";
    $settings['pickup_address'] = "44 - 48 Maitland Road\nMayfield East NSW 2304\nAustralia";
    */

    //-------------------------------------------------------------------------

    /*
    $settings['ebay_environment']  = 'production';
    $settings['ebay_paypal_email'] = '';
    */

    //-------------------------------------------------------------------------

    $settings['airwallex_client_id'] = 'ykHIr7I_RrquDo5sakLptg';
    $settings['airwallex_api_key']   = 'd3ba2a130be1fcf62c14286e6d8e240ec47f723c3237d54642a8a3dc7cf68c600e548967ef1524625733fb8ff5cd0b93';
    $settings['airwallex_base_url']  = 'https://pci-api-demo.airwallex.com/api/v1/';

    //-------------------------------------------------------------------------

    $_GLOBALS['site_settings'] = $settings;
    unset($settings);
}
