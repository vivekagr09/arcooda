<?php

namespace SEO\Services;

use Exception;
use SEO\Common\SEOException;
use Studio\Base\PlatformMeta;
use Studio\Util\Http\WebRequest;
use Studio\Util\Http\WebResponse;
use Studio\Util\Mail;

class KeywordsEverywhere
{

    /**
     * Checks the given key and returns the account ID if it's valid. Throws an exception if it's invalid or if any
     * other issue occurs.
     *
     * @param string $key
     * @return int
     */
    public static function getAccountId($key) {
        $response = static::get(static::uri("/ke/kepopup", array(
            'apiKey' => $key,
            'version' => '10.20',
            't' => time()
        )));

        if ($response->getStatusCode() !== 200) {
            throw new Exception("Received unexpected status code from {$response->getStatusCode()} from the API");
        }

        $body = $response->getBody();

        if (preg_match('/apikey=[\w]+&id=(\d+)/i', $body, $matches)) {
            return (int)$matches[1];
        }

        if (strpos($body, 'The API key is invalid') !== false) {
            throw new Exception('The key was reported as invalid');
        }

        return null;
    }

    /**
     * Retrieves the balance for the current key.
     *
     * @return int
     * @throws Exception
     */
    public static function getBalance() {
        $response = static::get('https://api.keywordseverywhere.com/v1/account/credits');
        $status = $response->getStatusCode();

        if ($status === 401) throw new Exception('API key is missing or is invalid');
        if ($status !== 200) throw new Exception("Received unexpected status code {$status} from the API");

        $data = $response->getJson();
        return $data[0];
    }

    /**
     * Fetches keyword data.
     *
     * @param string $key
     * @return array
     */
    public static function getKeywordData($keywords, $country = '', $currency = '') {
        $response = static::post(static::api('/v1/get_keyword_data'), array(
            'country' => $country,
            'currency' => $currency ?: '',
            'dataSource' => 'cli',
            'kw' => $keywords,
            't' => round(microtime(true) * 1000)
        ));

        if ($response->getStatusCode() !== 200) {
            throw new Exception("Failed to get keywords (status code {$response->getStatusCode()})");
        }

        $data = $response->getJson();

        if (isset($data['credits'])) {
            static::setBalance($data['credits']);
        }

        if (isset($data['data'])) {
            return $data['data'];
        }
    }

    /**
     * Returns `true` if KWE supports the given country code.
     *
     * @param string $code
     * @return bool
     */
    public static function supportsCountry($code) {
        $code = strtolower(trim($code));

        foreach (static::getCountries() as $cc => $cname) {
            if (strtolower($cc) === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an array of countries supported by the service.
     *
     * @return array
     */
    public static function getCountries() {
        return array(
            '' => 'Global',
            'au' => 'Australia',
            'ca' => 'Canada',
            'in' => 'India',
            'nz' => 'New Zealand',
            'za' => 'South Africa',
            'uk' => 'United Kingdom',
            'us' => 'United States'
        );
    }

    /**
     * Returns an array of currencies supported by the service.
     *
     * @return array
     */
    public static function getCurrencies() {
        return array(
            ''    => 'United States Dollar ($)',
            'aed' => 'UAE Dirham (AED)',
            'all' => 'Albanian Lek (ALL)',
            'ang' => 'Neth Antilles Guilder (NAƒ)',
            'ars' => 'Argentine Peso ($)',
            'aud' => 'Australian Dollar ($)',
            'awg' => 'Aruba Florin (ƒ)',
            'bbd' => 'Barbados Dollar (BBD)',
            'bdt' => 'Bangladesh Taka (Tk)',
            'bgn' => 'Bulgarian Lev (лв)',
            'bhd' => 'Bahraini Dinar (BHD)',
            'bif' => 'Burundi Franc (FBu)',
            'bmd' => 'Bermuda Dollar (BD$)',
            'bnd' => 'Brunei Dollar (B$)',
            'bob' => 'Bolivian Boliviano (Bs)',
            'brl' => 'Brazilian Real (R$)',
            'bsd' => 'Bahamian Dollar (B$)',
            'btn' => 'Bhutan Ngultrum (Nu)',
            'bwp' => 'Botswana Pula (P)',
            'byr' => 'Belarus Ruble (Br)',
            'bzd' => 'Belize Dollar (BZ$)',
            'cad' => 'Canadian Dollar (C$)',
            'chf' => 'Swiss Franc (CHF)',
            'clp' => 'Chilean Peso ($)',
            'cny' => 'Chinese Yuan (¥)',
            'cop' => 'Colombian Peso ($)',
            'crc' => 'Costa Rica Colon (₡)',
            'cup' => 'Cuban Peso ($MN)',
            'cve' => 'Cape Verde Escudo (Esc)',
            'czk' => 'Czech Koruna (Kč)',
            'djf' => 'Djibouti Franc (Fdj)',
            'dkk' => 'Danish Krone (kr)',
            'dop' => 'Dominican Peso (RD$)',
            'dzd' => 'Algerian Dinar (دج)',
            'eek' => 'Estonian Kroon (EEK)',
            'egp' => 'Egyptian Pound (EGP)',
            'etb' => 'Ethiopian Birr (Br)',
            'eur' => 'Euro (€)',
            'fjd' => 'Fiji Dollar (FJ$)',
            'fkp' => 'Falkland Islands Pound (£)',
            'gbp' => 'British Pound (£)',
            'ghs' => 'Ghanaian Cedi (GHS)',
            'gmd' => 'Gambian Dalasi (D)',
            'gnf' => 'Guinea Franc (FG)',
            'gtq' => 'Guatemala Quetzal (Q)',
            'gyd' => 'Guyana Dollar (GY$)',
            'hkd' => 'Hong Kong Dollar (HK$)',
            'hnl' => 'Honduras Lempira (L)',
            'hrk' => 'Croatian Kuna (kn)',
            'huf' => 'Hungarian Forint (Ft)',
            'idr' => 'Indonesian Rupiah (Rp)',
            'ils' => 'Israeli Shekel (₪)',
            'inr' => 'Indian Rupee (Rs)',
            'iqd' => 'Iraqi Dinar (IQD)',
            'isk' => 'Iceland Krona (kr)',
            'jod' => 'Jordanian Dinar (JOD)',
            'jpy' => 'Japanese Yen (¥)',
            'kes' => 'Kenyan Shilling (KSh)',
            'kgs' => 'Kyrgyzstan Som (KGS)',
            'khr' => 'Cambodia Riel (KHR)',
            'kmf' => 'Comoros Franc (KMF)',
            'kpw' => 'North Korean Won (₩)',
            'krw' => 'Korean Won (₩)',
            'kwd' => 'Kuwaiti Dinar (KWD)',
            'kyd' => 'Cayman Islands Dollar ($)',
            'kzt' => 'Kazakhstan Tenge (KZT)',
            'lkr' => 'Sri Lanka Rupee (ரூ)',
            'mad' => 'Moroccan Dirham (MAD)',
            'mdl' => 'Moldovan Leu (MDL)',
            'mkd' => 'Macedonian Denar (MKD)',
            'mmk' => 'Myanmar Kyat (K)',
            'mnt' => 'Mongolian Tugrik (₮)',
            'mop' => 'Macau Pataca ($)',
            'mro' => 'Mauritania Ougulya (UM)',
            'mur' => 'Mauritius Rupee (₨)',
            'mvr' => 'Maldives Rufiyaa (Rf)',
            'mwk' => 'Malawi Kwacha (MK)',
            'mxn' => 'Mexican Peso ($)',
            'myr' => 'Malaysian Ringgit (RM)',
            'nad' => 'Namibian Dollar (N$)',
            'ngn' => 'Nigerian Naira (₦)',
            'nio' => 'Nicaragua Cordoba (C$)',
            'nok' => 'Norwegian Krone (kr)',
            'npr' => 'Nepalese Rupee (₨)',
            'nzd' => 'New Zealand Dollar ($)',
            'omr' => 'Omani Rial (OMR)',
            'pab' => 'Panama Balboa (B)',
            'pen' => 'Peruvian Nuevo Sol (PEN)',
            'pgk' => 'Papua New Guinea Kina (K)',
            'php' => 'Philippine Peso (₱)',
            'pkr' => 'Pakistani Rupee (Rs)',
            'pln' => 'Polish Zloty (zł)',
            'qar' => 'Qatar Rial (QAR)',
            'ron' => 'Romanian New Leu (L)',
            'rub' => 'Russian Rouble (руб)',
            'rwf' => 'Rwanda Franc (RF)',
            'sar' => 'Saudi Arabian Riyal (SAR)',
            'sbd' => 'Solomon Islands Dollar (SI$)',
            'scr' => 'Seychelles Rupee (SR)',
            'sdg' => 'Sudanese Pound (SDG)',
            'sek' => 'Swedish Krona (kr)',
            'sgd' => 'Singapore Dollar (S$)',
            'shp' => 'St Helena Pound (£)',
            'skk' => 'Slovak Koruna (Sk)',
            'sll' => 'Sierra Leone Leone (Le)',
            'sos' => 'Somali Shilling (So)',
            'std' => 'Sao Tome Dobra (Db)',
            'svc' => 'El Salvador Colon (₡)',
            'syp' => 'Syrian Pound (SYP)',
            'szl' => 'Swaziland Lilageni (SZL)',
            'thb' => 'Thai Baht (฿)',
            'tnd' => 'Tunisian Dinar (TND)',
            'top' => 'Tonga Paang (T$)',
            'try' => 'Turkish Lira (YTL)',
            'ttd' => 'Trinidad Tobago Dollar (TTD)',
            'twd' => 'Taiwan Dollar (NT$)',
            'tzs' => 'Tanzanian Shilling (x)',
            'ugx' => 'Ugandan Shilling (USh)',
            'usd' => 'United States Dollar ($)',
            'uyu' => 'Uruguayan New Peso (UYU)',
            'uzs' => 'Uzbekistan Sum (UZS)',
            'vef' => 'Venezuelan Bolivar (VEF)',
            'vnd' => 'Vietnam Dong (₫)',
            'vuv' => 'Vanuatu Vatu (Vt)',
            'wst' => 'Samoa Tala (WS$)',
            'xaf' => 'CFA Franc (BEAC) (BEAC)',
            'xcd' => 'East Caribbean Dollar (EC$)',
            'xof' => 'CFA Franc (BCEAO) (BCEAO)',
            'xpf' => 'Pacific Franc (F)',
            'yer' => 'Yemen Riyal (YER)',
            'zar' => 'South African Rand (R)',
            'zmk' => 'Zambian Kwacha (ZMK)'
        );
    }

    /**
     * Generates an absolute URL for a request, given the request name and an optional array of arguments.
     *
     * @param string $request
     * @param (string|int)[] $args
     * @return string
     */
    protected static function uri($request, $args = array()) {
        $uri = 'https://keywordseverywhere.com/' . ltrim($request, '/') . '.php';
        if (!empty($args)) $uri .= '?' . http_build_query($args);
        return $uri;
    }

    /**
     * Generates an absolute URL for a request, given the request name and an optional array of arguments.
     *
     * @param string $request
     * @param (string|int)[] $args
     * @return string
     */
    protected static function api($request, $args = array()) {
        $uri = 'https://api.keywordseverywhere.com/' . ltrim($request, '/');
        if (!empty($args)) $uri .= '?' . http_build_query($args);
        return $uri;
    }

    /**
     * Performs a GET request with JSON encoding and returns the parsed response.
     *
     * @param string $uri
     * @return WebResponse
     */
    protected static function get($uri, $key = null) {
        global $studio;

        $web = new WebRequest($uri);
        $web->setUserAgent(sprintf('Mozilla/5.0 (compatible; SEO Studio %s)', PlatformMeta::VERSION_STR));
        $web->setHeader('Authorization', 'Bearer ' . (is_null($key) ? $studio->getopt('kwe-key') : $key));
        $web->setHeader('Accept', 'application/json');

        $response = $web->get();

        return $response;
    }

    /**
     * Performs a POST request with JSON encoding and returns the parsed response.
     *
     * @param string $uri
     * @param array $body
     * @return WebResponse
     */
    protected static function post($uri, $body = array()) {
        global $studio;

        $web = new WebRequest($uri);
        $web->setUserAgent(sprintf('Mozilla/5.0 (compatible; SEO Studio %s)', PlatformMeta::VERSION_STR));
        $web->setHeader('Authorization', 'Bearer ' . $studio->getopt('kwe-key'));
        $web->setHeader('Accept', 'application/json');

        $response = $web->post($body);

        return $response;
    }

    /**
     * Returns `true` if keywords everywhere is currently enabled.
     *
     * @return bool
     */
    public static function isEnabled() {
        global $studio;
        return $studio->getopt('kwe-enabled') === 'On';
    }

    /**
     * Returns `true` if the balance is currently low.
     *
     * @return bool
     */
    public static function isLowBalance() {
        global $studio;

        $threshold = $studio->getopt('kwe-notify-threshold', 10000);
        return static::isEnabled() && $studio->getopt('kwe-key') && $studio->getopt('kwe-balance', 0) < $threshold;
    }

    /**
     * Decreases the balance by the given amount.
     *
     * @param int $credits
     * @return void
     */
    public static function subtractBalance($credits) {
        global $studio;

        $credits = (int) $credits;
        $studio->sql->query("UPDATE `settings` SET `value` = `value` - $credits WHERE `name` = 'kwe-balance';");
    }

    /**
     * Increases the balance by the given amount.
     *
     * @param int $credits
     * @return void
     */
    public static function addBalance($credits) {
        global $studio;

        $credits = (int) $credits;
        $studio->sql->query("UPDATE `settings` SET `value` = `value` + $credits WHERE `name` = 'kwe-balance';");
    }

    /**
     * Updates the balance in the database with a known amount, and sends low balance emails if needed.
     *
     * @param int $credits
     * @return void
     */
    public static function setBalance($credits) {
        global $studio;

        $threshold = (int) $studio->getopt('kwe-notify-threshold', 10000);
        $notificationSent = $studio->getopt('kwe-notify-sent') === '1';

        $studio->setopt('kwe-balance', $credits);
        $studio->setopt('kwe-balance-timestamp', time());

        if ($credits <= $threshold) {
            if (!$notificationSent) {
                $studio->setopt('kwe-notify-sent', '1');

                if ($studio->getopt('kwe-notify-enabled') === 'On') {
                    if ($email = $studio->getopt('kwe-notify-email')) {
                        try {
                            $url = rtrim($studio->getopt('public-url'), '/') . '/admin/services/sources/keywords-everywhere.php';
                            $threshold = number_format($threshold);
                            $mailer = Mail::getClient();
                            $mailer->addAddress($email);
                            $mailer->Timeout = 10;
                            $mailer->Subject = '[SEO Studio] Low balance notification for Keywords Everywhere';
                            $mailer->Body = "Hi admin,

This is a quick notification to let you know that your Keywords Everywhere balance has dropped below your configured alert threshold ($threshold).

Please login via the link below to check it out:
{$url}

Thanks,
SEO Studio";
                            $mailer->send();
                        }
                        catch (Exception $ex) {}
                    }
                }
            }
        }
        else if ($notificationSent) {
            $studio->setopt('kwe-notify-sent', '0');
        }
    }

}
