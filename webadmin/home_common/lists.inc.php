<?php
if (!function_exists('getAirPortsList')) {
    /**
     * @return string[]
     */
    function getAirPortsList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `country`, `name` FROM `world`.`airports` ORDER BY `name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['country']][$row['id']] = $row['name'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getAvailabilityList')) {
    /**
     * @return string[]
     */
    function getAvailabilityList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `availability` FROM `product_availability` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row['availability'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getAvailabilityName')) {
    /**
     * @param int $id
     *
     * @return string
     */
    function getAvailabilityName($id)
    {
        return getAvailabilityList()[$id] ?? "Not Selected";
    }
}
/*
if(!function_exists('getBaseProductTypes')) {
    function getBaseProductTypes()
    {
    global $DB;
    static $outarray = '';
    if ($outarray == '')
    {
    $outarray = [];
    $query = $DB->query("SELECT `id`, `name` FROM `product_types` WHERE `parent` = 0");
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) $outarray[$row['id']] = ucfirst($row['name']);
    $DB->free_result($query);
    }
    $sort_by = [1 => 3, 1, 2, 4];
    $order_list = [];
    foreach($outarray as $type_id => $value) if (isset($sort_by[$type_id])) $order_list[$type_id] = $sort_by[$type_id];
    else $order_list[$type_id] = 5;
    asort($order_list);
    $newoutarray = [];
    foreach($order_list as $type_id => $value) $newoutarray[$type_id] = $outarray[$type_id];
    return $newoutarray;
    }
    function getBaseProductTypes()
    {
    global $DB;
    static $outarray = '';
    if ($outarray == '')
    {
    $outarray = [];
    $query = $DB->query("SELECT `id`, `name` FROM `product_types` WHERE `parent` = 0");
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) $outarray[$row['id']] = ucfirst($row['name']);
    $DB->free_result($query);
    $temp = [];
    if ($outarray[2] == 'Machines') $temp[2] = '';
    if ($outarray[3] == 'Pcbs') $temp[3] = '';
    if ($outarray[1] == 'Parts') $temp[1] = '';
    if ($outarray[4] == 'Merchandises') $temp[4] = '';
    foreach($outarray as $key => $value) $temp[$key] = $value;
    $outarray = $temp;
    }
    return $outarray;
    }
    function getBaseProductTypes()
    {
    global $DB;
    static $outarray = '';
    if ($outarray == '')
    {
    $outarray = [];
    $query = $DB->query("SELECT `id`, `name` FROM `product_types` WHERE `parent` = 0");
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) $outarray[$row['id']] = ucfirst($row['name']);
    $DB->free_result($query);
    }
    $sort_by = [1 => 3, 1, 2, 4];
    $order_list = [];
    foreach($outarray as $type_id => $value) if (isset($sort_by[$type_id])) $order_list[$type_id] = $sort_by[$type_id];
    else $order_list[$type_id] = 5;
    asort($order_list);
    $newoutarray = [];
    foreach($order_list as $type_id => $value) $newoutarray[$type_id] = $outarray[$type_id];
    return $newoutarray;
    }
}
*/
if (!function_exists('getCategoryDetails')) {
    function getCategoryDetails($id, $field = '')
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`last_update`) AS `last_update_unix` FROM `hg_products`.`products_types` WHERE `type_id` = %d LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            if ('' != $field) {
                return $row[$field];
            }
            return $row;
        }

        return false;
    }
}
if (!function_exists('getClientIP')) {
    /**
     * @return string
     */
    function getClientIP()
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}
if (!function_exists('getCoinMechList')) {
    /**
     * @return string[]
     */
    function getCoinMechList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `coin_mech` FROM `highwaygroup`.`coin_mechs` ORDER BY `coin_mech` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row['coin_mech'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getConditionList')) {
    function getConditionList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `condition` FROM `hg_products`.`products_condition` ORDER BY `condition` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row['condition'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getConditionName')) {
    function getConditionName($id)
    {
        $list = getConditionList();
        if (array_key_exists($id, $list)) {
            return $list[$id];
        }

        return "";
    }
}
if (!function_exists('getContinentList')) {
    function getContinentList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = [];

        if ($query = $DB->query(sprintf(
            "SELECT `continent_id`, `continent_name` FROM `world`.`continent` ORDER BY `continent_name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['continent_id']] = $row['continent_name'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getCountryCurrency')) {
    function getCountryCurrency($country)
    {
        global $DB;

        if ('' == $country) {
            return 'USD';
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT `currency_code` FROM `world`.`country` WHERE `code` = %s LIMIT 1",
            $DB->escape($country)
        ), MYSQLI_ASSOC)) {
            return $row['currency_code'];
        }

        return 'USD';
    }
}
if (!function_exists('geoip_record_by_name')) {
    function geoip_record_by_name($hostname) {
        if (in_array($hostname, ['203.123.36.92', '203.123.36.88'])) return [
        'continent_code' => 'AS',
        'country_code' => 'IN',
        'country_code3' => 'IND',
        'country_name' => 'India',
        'region' => '07',
        'city' => 'Delhi',
        'postal_code' => '',
        'latitude' => 28.66670036315918,
        'longitude' => 77.2166976928711,
        'dma_code' => 0,
        'area_code' => 0,
        ];
        if (in_array($hostname, ['110.175.74.86'])) return [
        'continent_code' => 'OC',
        'country_code' => 'AU',
        'country_code3' => 'AUS',
        'country_name' => 'Australia',
        'region' => '02',
        'city' => 'Darlinghurst',
        'postal_code' => '2010',
        'latitude' => -33.879398345947266,
        'longitude' => 151.21929931640625,
        'dma_code' => 0,
        'area_code' => 0,
        ];
        return [
        'continent_code' => 'NA',
        'country_code' => 'US',
        'country_code3' => 'USA',
        'country_name' => 'United States',
        'region' => 'VA',
        'city' => 'Ashburn',
        'postal_code' => '20149',
        'latitude' => 39.048099517822266,
        'longitude' => -77.4728012084961,
        'dma_code' => 511,
        'area_code' => 703,
        ];
    }
}

if (!function_exists('getCountryInfo')) {
    /**
     * @param string $ip
     *
     * @return array{
     *   a2:string,
     *   a3:string,
     *   country:string,
     *   number:string,
     * }
     */
    function getCountryInfo($ip)
    {
        if ($record = geoip_record_by_name($ip)) {
            return [
                'a2'      => $record['country_code'],
                'a3'      => $record['country_code3'],
                'country' => $record['country_name'],
                'number'  => $record['country_code3'], //Supposed to be ISO3166-1 numeric code
            ];
        }
        return ['a2' => '--', 'a3' => '---', 'country' => 'Unknown', 'number' => '000'];
    }
}
if (!function_exists('getCountryList')) {
    /**
     * @param bool $code
     *
     * @return string[]
     */
    function getCountryList($code = true)
    {
        global $DB;

        $key = ($code) ? 1 : 0;

        static $result = array();
        if (array_key_exists($key, $result)) {
            return $result[$key];
        }

        $result = array(
            0 => array(),
            1 => array(),
        );
        if ($query = $DB->query(sprintf(
            "SELECT `code`, `name` FROM `world`.`country` ORDER BY `name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[0][$row['code']] = $row['name'];
                $result[1][$row['code']] = $row['name'] . ' - ' . $row['code'];
            }
            $DB->free_result($query);
        }

        return $result[$key];
    }
}
if (!function_exists('getCountryListNested')) {
    /**
     * @return mixed[]
     */
    function getCountryListNested()
    {
        $country_list = getCountryList(false);
        $result       = [
            "Americas" => [
                "Northern America" => "BM CA GL PM US",
                "Central America"  => "BZ CR GT HN MX NI PA SV",
                "Caribbean"        => "AG AI AW BB BL BQ BS CU CW DM DO GD GP HT JM KN KY LC MF MQ MS PR SX TC TT VC VG VI",
                "South America"    => "AR BO BR CL CO EC FK GF GY PE PY SR UY VE",
            ],
            "Africa"   => [
                "Northern Africa" => "DZ EG EH LY MA SD SS TN EA IC",
                "Western Africa"  => "BF BJ CI CV GH GM GN GW LR ML MR NE NG SH SL SN TG",
                "Middle Africa"   => "AO CD CF CG CM GA GQ ST TD",
                "Eastern Africa"  => "BI DJ ER ET KE KM MG MU MW MZ RE RW SC SO TZ UG YT ZM ZW",
                "Southern Africa" => "BW LS NA SZ ZA",
            ],
            "Europe"   => [
                "Northern Europe" => "GG IM JE AX DK EE FI FO GB IE IS LT LV NO SE SJ",
                "Western Europe"  => "AT BE CH DE FR LI LU MC NL",
                "Eastern Europe"  => "BG BY CZ HU MD PL RO RU SK UA",
                "Southern Europe" => "AD AL BA ES GI GR HR IT ME MK MT RS PT SI SM VA XK",
            ],
            "Asia"     => [
                "Western Asia"       => "AE AM AZ BH CY GE IL IQ JO KW LB OM PS QA SA SY TR YE",
                "Central Asia"       => "TM TJ KG KZ UZ",
                "Eastern Asia"       => "CN HK JP KP KR MN MO TW",
                "Southern Asia"      => "AF BD BT IN IR LK MV NP PK",
                "South-Eastern Asia" => "BN ID KH LA MM MY PH SG TH TL VN",
            ],
            "Oceania"  => [
                "Australia and New Zealand" => "AU NF NZ",
                "Melanesia"                 => "FJ NC PG SB VU",
                "Micronesia"                => "FM GU KI MH MP NR PW",
                "Polynesia"                 => "AS CK NU PF PN TK TO TV WF WS",
                "Outlying Oceania"          => "AQ BV CC CX GS HM IO TF UM AC CP DG TA",
            ],
        ];
        foreach ($result as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                if (is_string($v2)) {
                    $result[$k1][$k2] = [];
                    foreach (explode(" ", $v2) as $k) {
                        if (array_key_exists($k, $country_list)) {
                            $result[$k1][$k2][$k] = $country_list[$k];
                            unset($country_list[$k]);
                        }
                    }
                    asort($result[$k1][$k2]);
                }
            }
        }

        if (count($country_list)) {
            $result["Others"] = $country_list;
        }

        return $result;
    }
}
if (!function_exists('getCountryName')) {
    /**
     * @param string $id
     *
     * @return string
     */
    function getCountryName($id)
    {
        return getCountryList(false)[$id] ?? "";
    }
}
if (!function_exists('getCourierAttachments')) {
    function getCourierAttachments($id)
    {
        global $DB;

        $outarray = array();
        $sql      = "SELECT * FROM `coinop`.`inquiry_courier_quotes` WHERE `tracking_file` <> '' AND `id` = '" . $id . "'";
        $query    = $DB->query($sql);
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $outarray[] = $row;
        }

        return $outarray;
    }
}
if (!function_exists('getCourierList')) {
    function getCourierList($site_id = false)
    {
        $key           = (false !== $site_id) ? intval($site_id) : "false";
        static $result = array();
        if (is_array($result[$key])) {
            return $result[$key];
        }

        global $DB;
        $result[$key] = array();
        $query        = $DB->query("SELECT `name`, `prop_id` FROM `shipping_services` ORDER BY `name` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$key][$row['prop_id']] = $row['name'];
        }

        $DB->free_result($query);

        return $result[$key];
    }
}
if (!function_exists('getCourierName')) {
    function getCourierName($id)
    {
        $list = getCourierList(false);
        if (array_key_exists($id, $list)) {
            return $list[$id];
        }

        return "";
    }
}
if (!function_exists('getCurrencyCodes')) {
    function getCurrencyCodes()
    {
        return getCurrencyList(true);
    }
}
if (!function_exists('getCurrencyList')) {
    function getCurrencyList($code_only = false)
    {
        global $DB;

        $key = ($code_only) ? "y" : "n";

        static $result;
        if (!is_null($result)) {
            return $result[$key];
        }

        $result = ['y' => [], 'n' => []];

        if ($query = $DB->query(sprintf(
            "SELECT `code`, `name` FROM `highwaygroup`.`currency` ORDER BY `name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result['y'][$row['code']] = $row['code'];
                $result['n'][$row['code']] = sprintf('%s - %s', $row['name'], $row['code']);
            }
            $DB->free_result($query);
        }

        return $result[$key];
    }
}
if (!function_exists('getCurrencyName')) {
    /**
     * @param string $id
     *
     * @return string
     */
    function getCurrencyName($id = '')
    {
        $temp = getCurrencyList(false)[$id] ?? "";
        $temp = explode(' - ', $temp);
        return reset($temp);
    }
}
if (!function_exists('getCurrencySymbol')) {
    /**
     * @param string $code
     * @param string $encoding
     *
     * @return string
     */
    function getCurrencySymbol($code = "", $encoding = "ASCII")
    {
        if (in_array($code, array("ARS", "AUD", "BBD", "BMD", "BND", "BSD", "CAD", "CLP", "COP", "FJD", "GYD", "HKD", "KYD", "LRD", "MXN", "NAD", "NZD", "SBD", "SGD", "SRD", "SVC", "TVD", "USD", "XCD"))) {
            return '$';
        }

        if (in_array($code, array("ALL"))) {
            return "Lek";
        }

        if (in_array($code, array("BAM"))) {
            return "KM";
        }

        if (in_array($code, array("BOB"))) {
            return '$b';
        }

        if (in_array($code, array("BRL"))) {
            return 'R$';
        }

        if (in_array($code, array("BWP"))) {
            return "P";
        }

        if (in_array($code, array("BYR"))) {
            return "p.";
        }

        if (in_array($code, array("BZD"))) {
            return 'BZ$';
        }

        if (in_array($code, array("CHF"))) {
            return "CHF";
        }

        if (in_array($code, array("DKK", "EEK", "ISK", "NOK", "SEK"))) {
            return "kr";
        }

        if (in_array($code, array("DOP"))) {
            return 'RD$';
        }

        if (in_array($code, array("GTQ"))) {
            return "Q";
        }

        if (in_array($code, array("HNL"))) {
            return "L";
        }

        if (in_array($code, array("HRK"))) {
            return "kn";
        }

        if (in_array($code, array("HUF"))) {
            return "Ft";
        }

        if (in_array($code, array("IDR"))) {
            return "Rp";
        }

        if (in_array($code, array("JMD"))) {
            return 'J$';
        }

        if (in_array($code, array("LTL"))) {
            return "Lt";
        }

        if (in_array($code, array("LVL"))) {
            return "Ls";
        }

        if (in_array($code, array("MYR"))) {
            return "RM";
        }

        if (in_array($code, array("MZN"))) {
            return "MT";
        }

        if (in_array($code, array("NIO"))) {
            return 'C$';
        }

        if (in_array($code, array("PAB"))) {
            return "B/.";
        }

        if (in_array($code, array("PEN"))) {
            return "S/.";
        }

        if (in_array($code, array("PYG"))) {
            return "Gs";
        }

        if (in_array($code, array("RON"))) {
            return "lei";
        }

        if (in_array($code, array("SOS"))) {
            return "S";
        }

        if (in_array($code, array("TRY"))) {
            return "TL";
        }

        if (in_array($code, array("TTD"))) {
            return 'TT$';
        }

        if (in_array($code, array("TWD"))) {
            return 'NT$';
        }

        if (in_array($code, array("UYU"))) {
            return '$U';
        }

        if (in_array($code, array("VEF"))) {
            return "Bs";
        }

        if (in_array($code, array("ZAR"))) {
            return "R";
        }

        if (in_array($code, array("ZWD"))) {
            return 'Z$';
        }

        $encoding = strtoupper($encoding);
        switch ($encoding) {
            default:
            case "ANSI":
            case "ASCII":
                if (in_array($code, array("EGP", "FKP", "GBP", "GGP", "GIP", "IMP", "JEP", "LBP", "SHP", "SYP"))) {
                    return chr(163);
                }

                if (in_array($code, array("EUR"))) {
                    return chr(128);
                }

                if (in_array($code, array("RMB", "JPY", "CNY"))) {
                    return chr(165);
                }

                if (in_array($code, array("RUB"))) {
                    return "p.";
                }

                return chr(164);

            case "UTF-8":
            case "UTF8":
                if (in_array($code, array("ANG", "AWG"))) {
                    return "ƒ";
                }

                if (in_array($code, array("AZN"))) {
                    return "ман";
                }

                if (in_array($code, array("BGN", "KGS", "KZT", "UZS"))) {
                    return "лв";
                }

                if (in_array($code, array("CZK"))) {
                    return "Kč";
                }

                if (in_array($code, array("EGP", "FKP", "GBP", "GGP", "GIP", "IMP", "JEP", "LBP", "SHP", "SYP"))) {
                    return chr(0xc2) . chr(0xa3);
                }

                if (in_array($code, array("GHC"))) {
                    return "¢";
                }

                if (in_array($code, array("ILS"))) {
                    return "₪";
                }

                if (in_array($code, array("MKD"))) {
                    return "ден";
                }

                if (in_array($code, array("PLN"))) {
                    return "zł";
                }

                if (in_array($code, array("RSD"))) {
                    return "Дин.";
                }

                if (in_array($code, array("RUB"))) {
                    return "p.";
                }

                if (in_array($code, array("THB"))) {
                    return "฿";
                }

                if (in_array($code, array("TRL"))) {
                    return "₤";
                }

                if (in_array($code, array("VND"))) {
                    return "₫";
                }

                if (in_array($code, array("EUR"))) {
                    return chr(0xe2) . chr(0x82) . chr(0xac);
                }

                if (in_array($code, array("RMB", "JPY", "CNY"))) {
                    return chr(0xc2) . chr(0xa5);
                }

                return chr(0xc2) . chr(0xa4);

            case "HTML":
            case "HTML-ENTITIES":
                if (in_array($code, array("ANG", "AWG"))) {
                    return "&fnof;";
                }

                if (in_array($code, array("BGN", "KGS", "KZT", "UZS"))) {
                    return "&#1083;&#1074;";
                }

                if (in_array($code, array("CZK"))) {
                    return "K&#269;";
                }

                if (in_array($code, array("EGP", "FKP", "GBP", "GGP", "GIP", "IMP", "JEP", "LBP", "SHP", "SYP"))) {
                    return "&pound;";
                }

                if (in_array($code, array("ILS"))) {
                    return "&#8362;";
                }

                if (in_array($code, array("PLN"))) {
                    return "z&#322;";
                }

                if (in_array($code, array("RSD"))) {
                    return "&#1044;&#1080;&#1085;.";
                }

                if (in_array($code, array("RUB"))) {
                    return "p.";
                }

                if (in_array($code, array("THB"))) {
                    return "&#3647;";
                }

                if (in_array($code, array("VND"))) {
                    return "&#8363;";
                }

                if (in_array($code, array("EUR"))) {
                    return "&euro;";
                }

                if (in_array($code, array("RMB", "JPY", "CNY"))) {
                    return "&yen;";
                }

                return "&curren;";
        }
    }
}
if (!function_exists('getDefinedInventoryComments')) {
    function getDefinedInventoryComments()
    {
        $key           = 0;
        static $result = array();
        if (is_array($result[$key])) {
            return $result[$key];
        }

        global $DB;
        $result[$key] = array();
        $query        = $DB->query("SELECT `comments`, `id` FROM `highwaygroup`.`freight_services_defined_comments` ORDER BY `id` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$key][$row['id']] = $row["comments"];
        }

        $DB->free_result($query);

        return $result[$key];
    }
}
if (!function_exists('getDelTime')) {
    function getDelTime($id)
    {
        return getDeliveryTimeName($id);
    }
}
if (!function_exists('getDeliveryTimeList')) {
    function getDeliveryTimeList()
    {
        $key           = 0;
        static $result = array();
        if (is_array($result[$key])) {
            return $result[$key];
        }

        global $DB;
        $result[$key] = array();
        $query        = $DB->query("SELECT `del_time`, `id` FROM `highwaygroup`.`freight_services_delivery_time` ORDER BY `id` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$key][$row['id']] = $row["del_time"];
        }

        $DB->free_result($query);

        return $result[$key];
    }
}
if (!function_exists('getDeliveryTimeName')) {
    function getDeliveryTimeName($id)
    {
        $list = getDeliveryTimeList();
        if (array_key_exists($id, $list)) {
            return $list[$id];
        }

        return "";
    }
}
if (!function_exists('getDeliveryTimes')) {
    function getDeliveryTimes()
    {
        return getDeliveryTimeList();
    }
}
if (!function_exists('getDiscountBenefits')) {
    function getDiscountBenefits($discount, $implode = true)
    {
        global $DB;
        if (0 == $discount) {
            return 'None';
        }

        $str = array();
        $res = $DB->query(sprintf(
            "SELECT * FROM `highwaygroup`.`discount_benefits` WHERE `discount` = %d",
            $discount
        ));
        if ($implode) {
            while ($row = $DB->fetch_assoc($res)) {
                if ($row = $DB->query_first(sprintf(
                    "SELECT `value` FROM `highwaygroup`.`benefits` WHERE `id` = %d",
                    $row['benefit_id']
                ), MYSQLI_ASSOC)) {
                    $str[] = $row['value'];
                }
            }
            if (count($str)) {
                return implode(', ', $str);
            }

        } else {
            while ($row = $DB->fetch_assoc($res)) {
                $str[] = $row['benefit_id'];
            }

            return $str;
        }
    }
}
if (!function_exists('getDistanceList')) {
    /**
     * @return string[]
     */
    function getDistanceList()
    {
        return [
            'nanometre'  => "0.000001",
            'nanometer'  => "0.000001",
            'nm'         => "0.000001",
            'micrometre' => "0.001",
            'micrometer' => "0.001",
            'micron'     => "0.001",
            'pixel'      => "0.264583",
            'px'         => "0.264583",
            'point'      => "0.352777",
            'pt'         => "0.352777",
            'millimetre' => "1.0",
            'millimeter' => "1.0",
            'mm'         => "1.0",
            'centimetre' => "10.0",
            'centimeter' => "10.0",
            'cm'         => "10.0",
            'inch'       => "25.4",
            'in'         => "25.4",
            'decimetre'  => "100.0",
            'decimeter'  => "100.0",
            'dm'         => "100.0",
            'foot'       => "304.8",
            'ft'         => "304.8",
            'yard'       => "914.4",
            'yd'         => "914.4",
            'metre'      => "1000.0",
            'meter'      => "1000.0",
            'm'          => "1000.0",
            'perch'      => "5029.2",
            'rod'        => "5029.2",
            'rd'         => "5029.2",
            'kilometre'  => "1000000.0",
            'kilometer'  => "1000000.0",
            'km'         => "1000000.0",
            'mile'       => "1609344.0",
            'mi'         => "1609344.0",
            'au'         => "1.495e14",
            'ua'         => "1.495e14",
            'lightyear'  => "9.460e15",
            'ly'         => "9.460e15",
            'parsec'     => "3.085e19",
            'pc'         => "3.085e19",
            'angstrom'   => "1e-7",
            'twip'       => "0.017638",
            'mil'        => "0.0254",
            'line'       => "2.117",
            'pica'       => "4.23333",
            'mon'        => "24.0",
            'sun'        => "30.3",
            'hand'       => "101.6",
            'link'       => "201.2",
            'shyaku'     => "303.0",
            'ken'        => "1818.0",
            'jyo'        => "3030.0",
            'chain'      => "20120.0",
            'cho'        => "109100.0",
            'furlong'    => "201200.0",
            'kairi'      => "1852000.0",
            'ri'         => "3927000.0",
            'league'     => "4828000.0",
        ];
    }
}
if (!function_exists('getExchangeRateList')) {
    /**
     * @return string[]
     */
    function getExchangeRateList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `code`, `usfrom` FROM `highwaygroup`.`currency` ORDER BY `code` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['code']] = $row['usfrom'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getFeedbackTypes')) {
    function getFeedbackTypes()
    {
        $key           = 0;
        static $result = array();
        if (is_array($result[$key])) {
            return $result[$key];
        }

        global $DB;
        $result[$key] = array();
        $query        = $DB->query("SELECT `name`, `type_id` FROM `hwent_hwent`.`feedback_type` WHERE `active` = 'y' ORDER BY `type_id` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$key][$row['type_id']] = $row["name"];
        }

        $DB->free_result($query);

        return $result[$key];
    }
}
if (!function_exists('getFreeAccessoryList')) {
    function getFreeAccessoryList()
    {
        $key           = 0;
        static $result = array();
        if (is_array($result[$key])) {
            return $result[$key];
        }

        global $DB;
        $result[$key] = array();
        $query        = $DB->query("SELECT `id`, `name` FROM `convert_convert`.`products_accessories` ORDER BY `name` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$key][$row['id']] = $row["name"];
        }

        $DB->free_result($query);

        return $result[$key];
    }
}
if (!function_exists('getFreeAccessoryName')) {
    function getFreeAccessoryName($id)
    {
        $list = getFreeAccessoryList();
        if (array_key_exists($id, $list)) {
            return $list[$id];
        }

        return "";
    }
}
if (!function_exists('getFreightCourierQuotes')) {
    function getFreightCourierQuotes($inquiry_id)
    {
        global $DB;

        $outarray = array();
        $sql      = "SELECT * FROM `coinop`.`inquiry_courier_quotes` WHERE `inquiry_id` = '" . $inquiry_id . "'";
        $query    = $DB->query($sql);
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $outarray[] = $row;
        }

        return $outarray;
    }
}
if (!function_exists('getFreightForwarderList')) {
    /**
     * @return string[]
     */
    function getFreightForwarderList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `name` FROM `coinop`.`freight_forwarders` ORDER BY `name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row["name"];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getFreightForwarderName')) {
    /**
     * @param int $id
     *
     * @return string
     */
    function getFreightForwarderName($id)
    {
        return getFreightForwarderList()[$id] ?? "None Selected";
    }
}
if (!function_exists('getFreightQuotes')) {
    function getFreightQuotes($inquiry_id)
    {
        global $DB;
        $outarray = array();
        $sql      = "SELECT * FROM `coinop`.`freight_forwarders_quotes` WHERE qt_no='" . $inquiry_id . "'";
        $query    = $DB->query($sql);
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $outarray[] = $row;
        }

        return $outarray;
    }
}
if (!function_exists('getFreightServiceList')) {
    function getFreightServiceList()
    {
        $key           = 0;
        static $result = array();
        if (is_array($result[$key])) {
            return $result[$key];
        }

        global $DB;
        $result[$key] = array();
        $query        = $DB->query("SELECT `id`, `name` FROM `highwaygroup`.`freight_services` ORDER BY `name` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$key][$row['id']] = $row["name"];
        }

        $DB->free_result($query);

        return $result[$key];
    }
}
if (!function_exists('getcouriersServiceList')) {
    function getcouriersServiceList()
    {
        global $DB;

        $key           = 0;
        static $result = array();
        if (is_array($result[$key])) {
            return $result[$key];
        }

        $result[$key] = array();
        if ($query = $DB->query(sprintf(
            "SELECT `courier_id`, `courier_name` FROM `couriers` ORDER BY `courier_name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$key][$row['courier_id']] = $row["courier_name"];
            }
            $DB->free_result($query);
        }

        return $result[$key];
    }
}
if (!function_exists('getFreightServiceName')) {
    /**
     * @param int $id
     *
     * @return string
     */
    function getFreightServiceName($id)
    {
        return getFreightServiceList()[$id] ?? "";
    }
}
if (!function_exists('getCourierServiceName')) {
    function getCourierServiceName($id)
    {
        $list = getFreightServices('personal', '', true);
        if (array_key_exists($id, $list)) {
            return $list[$id];
        }

        return "";
    }
}
if (!function_exists('getFreightServiceProviderList')) {
    /**
     * @return string[]
     */
    function getFreightServiceProviderList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `name` FROM `convert_convert`.`freight_providers` ORDER BY `name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row["name"];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getFreightServiceProviderName')) {
    /**
     * @param int $id
     *
     * @return string
     */
    function getFreightServiceProviderName($id)
    {
        return getFreightServiceProviderList()[$id] ?? "";
    }
}
if (!function_exists('getFreightServiceProviders')) {
    function getFreightServiceProviders()
    {
        return getFreightServiceProviderList();
    }
}
if (!function_exists('getFreightServiceProvidersLink')) {
    function getFreightServiceProvidersLink($id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT `link_to` FROM `convert_convert`.`freight_providers` WHERE `id` = %d LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            return $row['link_to'];
        }

        return 0;
    }
}
if (!function_exists('getFreightServices')) {
    /**
     * @param string $type
     * @param string $service_type
     * @param bool   $active
     *
     * @return string[]
     */
    function getFreightServices($type = 'pp', $service_type = '', $active = false)
    {
        global $DB;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `name` FROM `highwaygroup`.`freight_services` WHERE `type` = '%s'%s%s",
            $DB->escape_string($type),
            ('' != $service_type) ? sprintf(" AND `service_type` = '%s'", $DB->escape_string($service_type)) : '',
            ($active) ? " AND `active` = 'y'" : ""
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row['name'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getFreightSupplierList')) {
    function getFreightSupplierList()
    {
        $key           = 0;
        static $result = array();
        if (is_array($result[$key])) {
            return $result[$key];
        }

        global $DB;
        $result[$key] = array();
        $query        = $DB->query("SELECT `fow_id`, `fow_company` FROM `fowarder` ORDER BY `fow_company` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$key][$row['fow_id']] = $row["fow_company"];
        }

        $DB->free_result($query);

        return $result[$key];
    }
}
if (!function_exists('getFullFieldName')) {
    function getFullFieldName($key, $type, $second = false)
    {
        $type   = strtolower($type);
        $weight = ('air' == $type) ? 'Air Freight' : (('sea' == $type) ? 'Ocean Freight' : 'Shipping Weight');
        $rate   = ucfirst($type) . ' Freight per ' . ('sea' == $type) ? 'cm<sup>3</sup>' : 'kg';
        $array  = array('wgt' => $weight, 'doc' => 'Document Charge', 'tel' => 'Tele-Release Document', 'clr' => 'Clearance Charge', 'exl' => 'Export License Fee', 'han' => 'Handling Charge', 'tru' => 'Handling Charge', 'cra' => 'Crating', 'fum' => 'Fumigation', 'com' => 'Commodity Inspection', 'cus' => 'Customs Inspection', 'ins' => 'Insurance Charge', 'ori' => 'Certificate Origin', 'oth' => 'Other', 'exw' => 'Ex Works', 'dtd' => 'Door To Door', 'dta' => 'Door To ' . ucfirst($type) . 'port', 'cube_weight' => 'Cubic Weight', 'wgt_rate' => $rate, 'ins_rate' => 'Insurance Rate', 'tot_total' => 'Total Cost', 'sal_total' => 'Sale Price', 'con_flag' => 'Confirmation', 'from' => 'Ship From ' . ucfirst($type) . 'port', 'to' => 'Ship To ' . ucfirst($type) . 'port');
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else if (!$second) {
            return strtoupper($key);
        } else {
            return ucfirst(str_replace("_", " ", $key));
        }

    }
}
if (!function_exists('getGoodsCondition')) {
    function getGoodsCondition($id = 0)
    {
        return getConditionName($id);
    }
}
if (!function_exists('getHowLocated')) {
    function getHowLocated()
    {
        global $DB;
        static $result = array();
        if (count($result)) {
            return $result;
        }

        $query = $DB->query("SELECT `refer_id`, `refer_name` FROM `converters`.`car_advert_referer`");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row['refer_id']] = $row['refer_name'];
        }

        $DB->free_result($query);

        return $result;
    }
}
if (!function_exists('getInternationalZones')) {
    function getInternationalZones($zone_id = 0)
    {
        global $DB;
        $zone_id = intval($zone_id);
        $sql     = "SELECT id, zone FROM `highwaygroup`.`freight_services_zones`" . ($zone_id > 0 ? " WHERE `id` = $zone_id LIMIT 1" : '');
        $res     = $DB->query($sql);
        $ret     = array();
        while ($row = $DB->fetch_assoc($res)) {
            if ($zone_id > 0) {
                return $row['zone'];
            } else {
                $ret[$row['id']] = $row['zone'];
            }

        }
        return $ret;
    }
}
if (!function_exists('getInvoicePlugList')) {
    function getInvoicePlugList()
    {
        return getPlugsList();
    }
}
if (!function_exists('getInvoiceVoltages')) {
    /**
     * @return string[]
     */
    function getInvoiceVoltages()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `voltage` FROM `highwaygroup`.`invoice_voltages` ORDER BY `id` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row['voltage'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getLanguages')) {
    function getLanguages($id = '')
    {
        global $DB;
        static $outarray = '';
        if ('' == $outarray) {
            $outarray = array();
            $sql      = "SELECT DISTINCT `id`, `name` FROM `world`.`language` ";
            if ('' != $id) {
                $sql .= "WHERE id = '$id' ";
            }

            $query = $DB->query($sql . "ORDER BY name ASC");
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $outarray[$row['id']] = $row['name'];
            }

        }
        if ('' != $id) {
            return $outarray[$id];
        }

        return $outarray;
    }
}
if (!function_exists('getLocationName')) {
    function getLocationName($location_id)
    {
        global $DB;
        $row = $DB->query_first("SELECT `location` FROM `hg_products`.`products_location` WHERE `id` = " . intval($location_id), MYSQLI_ASSOC);
        if (false !== $row) {
            return $row['location'];
        }

        return 'No Storage Location Selected';
    }
}
if (!function_exists('getMainInterests')) {
    function getMainInterests()
    {
        global $DB;
        $outarray  = array();
        $statement = "SELECT survey_type,long_desc FROM HE.HE_survey_types WHERE parent=2";
        $results   = $DB->query($statement);
        while ($row = $DB->fetch_array($results)) {
            $outarray[$row['survey_type']] = $row['long_desc'];
        }

        return $outarray;
    }
}
if (!function_exists('getManufacturerName')) {
    function getManufacturerName($supplier_id)
    {
        global $DB;
        $supplier_id = intval($supplier_id);
        if (0 == $supplier_id) {
            return 'None';
        }

        $qry = $DB->query_first("SELECT name FROM highway.manufacturers WHERE manufacturer_id=$supplier_id");
        return $qry['name'];
    }
}
if (!function_exists('getManufacturers')) {
    function getManufacturers($site_id = 0)
    {
        global $DB;
        $site_id = intval($site_id);
        $sql     = "SELECT `id`, `manufacturer` FROM `highwaygroup`.`suppliers` WHERE `type` IN ('man', 'both')" . (($site_id > 0) ? " AND FIND_IN_SET('" . $site_id . "', `site`) > 0" : '') . " ORDER BY `manufacturer` ASC";
        $res     = $DB->query($sql);
        $ret     = array();
        while ($row = $DB->fetch_assoc($res)) {
            $ret[$row['id']] = $row['manufacturer'];
        }

        return $ret;
    }
}
if (!function_exists('getMemberNameCountry')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function getMemberNameCountry($id)
    {
        global $DB;

        $id = intval($id);
        if (!$id) {
            return null;
        }

        if ($row = $DB->query_first(
            "SELECT CONCAT_WS(' ', `first_name`,`last_name`) AS `name`, `country`, `company_name` FROM `highwaygroup`.`new_members` WHERE `member_id` = " . $id,
            MYSQLI_ASSOC
        )) {
            $keep = array('name', 'country');
            foreach ($row as $key => $value) {
                if ('' == $row['name'] && '' != $row['company_name']) {
                    $row['name'] = $row['company_name'];
                }

                if (!in_array($key, $keep)) {
                    unset($row[$key]);
                }

            }
            return $row;
        }

        return null;
    }
}
if (!function_exists('getMessageTitle')) {
    /**
     * @param int $id
     *
     * @return string
     */
    function getMessageTitle($id)
    {
        global $DB;
        if ($row = $DB->query_first(sprintf(
            "SELECT `message_description` FROM `purchase_orders_message_types` WHERE `message_type_id` = %d LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            return $row['message_description'];
        }
        return '';
    }
}
if (!function_exists('getNewsTagName')) {
    /**
     * @param int $id
     *
     * @return string
     */
    function getNewsTagName($id)
    {
        return getNewsTags()[$id] ?? "";
    }
}
if (!function_exists('getNewsTags')) {
    /**
     * @return string[]
     */
    function getNewsTags()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `name` FROM `highwaygroup`.`tags` ORDER BY `name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["id"]] = $row["name"];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getOnlineUsers')) {
    function getOnlineUsers($dept_id = 0)
    {
        global $DB;
        $row = array();
        if (0 != $dept_id) {
            $row = $DB->query_first("SELECT COUNT(*) AS `total` FROM `phplive`.`chat_admin` AS `ca` LEFT JOIN `phplive`.`chatuserdeptlist` AS `cd` ON `ca`.`userID` = `cd`.`userID` WHERE `ca`.`available_status` = 1 AND `cd`.`deptID` = " . intval($dept_id), MYSQLI_ASSOC);
        } else {
            $row = $DB->query_first("SELECT COUNT(*) AS `total` FROM `phplive`.`chat_admin` AS `ca` WHERE `ca`.`available_status` = 1", MYSQLI_ASSOC);
        }

        return $row['total'];
    }
}
if (!function_exists('getOutletFrequencies')) {
    function getOutletFrequencies()
    {
        return array(1 => '50/60Hz', 2 => '50Hz', 3 => '60Hz');
    }
}
if (!function_exists('getOutletPlugs')) {
    function getOutletPlugs()
    {
        global $DB;
        static $outarray = '';
        if ('' == $outarray) {
            $outarray = array();
            $query    = $DB->query("SELECT `id`, `value` FROM `world`.`plug_types` ORDER BY `value` ASC");
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $outarray[$row['id']] = $row['value'];
            }

            $DB->free_result($query);
        }
        return $outarray;
    }
}
if (!function_exists('getOutletVoltages')) {
    function getOutletVoltages()
    {
        global $DB;
        static $outarray = '';
        if ('' == $outarray) {
            $outarray = array();
            $query    = $DB->query("SELECT `id`, `value` FROM `world`.`voltage` ORDER BY `value` ASC");
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $outarray[$row['id']] = $row['value'];
            }

            $DB->free_result($query);
        }
        return $outarray;
    }
}
if (!function_exists('getPCBTypes')) {
    function getPCBTypes()
    {
        global $DB;

        static $cache = null;
        if (!is_null($cache)) {
            return $cache;
        }

        $query = $DB->query(sprintf(
            "SELECT `id`, `pcb_type` FROM `products_pcb_types` WHERE `deleted_at` IS NULL ORDER BY `pcb_type` ASC"
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $cache[$row['id']] = $row['pcb_type'];
        }
        $DB->free_result($query);

        return $cache;
    }
}
if (!function_exists('getPackingRequirementList')) {
    function getPackingRequirementList()
    {
        $key           = 0;
        static $result = array();
        if (is_array($result[$key])) {
            return $result[$key];
        }

        global $DB;
        $result[$key] = array();
        $query        = $DB->query("SELECT `id`, `requirement` FROM `highwaygroup`.`purchase_orders_packing` ORDER BY `requirement` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$key][$row['id']] = $row["requirement"];
        }

        $DB->free_result($query);

        return $result[$key];
    }
}
if (!function_exists('getPackingRequirementName')) {
    function getPackingRequirementName($id)
    {
        $list = getPackingRequirementList();
        if (array_key_exists($id, $list)) {
            return $list[$id];
        }

        return "";
    }
}
if (!function_exists('getPackingRequirements')) {
    function getPackingRequirements()
    {
        return getPackingRequirementList();
    }
}
if (!function_exists('getParentCategories')) {
    function getParentCategories()
    {
        global $DB;
        $ret = array();
        $res = $DB->query("SELECT * FROM `hg_products`.`products_types` WHERE `is_parent` = 'y' ORDER BY `name` ASC");
        while ($row = $DB->fetch_assoc($res)) {
            $ret[$row['type_id']] = $row['name'];
        }

        return $ret;
    }
}
if (!function_exists('getPaymentMadeList')) {
    function getPaymentMadeList()
    {
        global $DB;
        static $result = array();
        if (count($result)) {
            return $result;
        }

        $query = $DB->query("SELECT `id`, `title` FROM `highwaygroup`.`purchase_orders_payment` ORDER BY `id` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row['id']] = $row['title'];
        }

        $DB->free_result($query);

        return $result;
    }
}
if (!function_exists('getPaymentMethodsList')) {
    function getPaymentMethodsList()
    {
        global $DB,$_GLOBALS;
        static $result = '';
        if ('' == $result) {
            $result = array();
            $query  = $DB->query("SELECT `payment_method_id`, `payment_method_name` FROM `highwaygroup`.`payment_methods` WHERE `payment_method_public` = 'y' AND site_id = 39 ORDER BY `payment_method_name` ASC");
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['payment_method_id']] = ucfirst($row['payment_method_name']);
            }

            $DB->free_result($query);
        }
        return $result;
    }
}
if (!function_exists('getPaymentName')) {
    function getPaymentName($pay_id)
    {
        global $DB;
        $outtext = '';
        if ('' == $outtext) {
            $query = $DB->query("SELECT text FROM new_payment_methods WHERE paymethod_id = '$pay_id'");
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $outtext = ucfirst($row['text']);
            }

            $DB->free_result($query);
        }
        return $outtext;
    }
}
if (!function_exists('getPersonalCourierName')) {
    function getPersonalCourierName($id)
    {
        $couriers = array(1 => 'EMS', 2 => 'UPS', 3 => 'TNT', 4 => 'Air Borne', 5 => 'DHL', 6 => 'Federal Express');
        if (array_key_exists($id, $couriers)) {
            return $couriers[$id];
        }

        return '';
    }
}
if (!function_exists('getPlugsList')) {
    function getPlugsList()
    {
        global $DB;
        $plug_list = array();
        $res       = $DB->query("SELECT `id`, `name` FROM `hg_products`.`products_plug_types` ORDER BY `name` ASC");
        while ($row = $DB->fetch_assoc($res)) {
            $plug_list[$row['id']] = $row['name'];
        }

        return $plug_list;
    }
}
if (!function_exists('getPlugsVoltages')) {
    function getPlugsVoltages($country)
    {
        global $DB;

        return $DB->query_first(sprintf(
            "SELECT `voltage`, `v_comments`, `plugs` FROM `world`.`country` WHERE `code` = %s LIMIT 1",
            $DB->escape($country)
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('getPortName')) {
    function getPortName($num, $country, $type)
    {
        $port_list = (strtolower($type) == 'air') ? getAirPortsList() : getSeaPortsList();
        return $port_list[$country][$num];
    }
}
if (!function_exists('getPostCode')) {
    function getPostCode($id)
    {
        global $DB;
        $id = intval($id);
        return $DB->query_first("SELECT * FROM `highwaygroup`.`post_codes` WHERE `id` = $id LIMIT 1");
    }
}
if (!function_exists('getProductCategory')) {
    function getProductCategory($parent = false)
    {
        global $DB;
        $outarray = array();
        if ($parent) {
            $res = $DB->query("SELECT * FROM `hg_products`.`products_types` WHERE `parent` = 0");
            while ($row = $DB->fetch_assoc($res)) {
                $outarray[$row['type_id']] = $row['name'];
            }

        } else {
            $res = $DB->query("SELECT * FROM `hg_products`.`products_types` WHERE `parent` = 0");
            while ($row = $DB->fetch_assoc($res)) {
                $res2 = $DB->query("SELECT * FROM `hg_products`.`products_types` WHERE `parent` = '" . $row['type_id'] . "'");
                while ($child = $DB->fetch_assoc($res2)) {
                    $outarray[$row['name']][$child['type_id']] = $child['name'];
                }

            }
        }
        return $outarray;
    }
}
if (!function_exists('getProductCondition')) {
    function getProductCondition()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `condition` FROM `hg_products`.`products_condition` ORDER BY `id` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row['condition'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getProductName')) {
    function getProductName($product_id)
    {
        global $DB;
        $product_id = intval($product_id);
        if (0 == $product_id) {
            return 'None';
        }

        $qry = $DB->query_first("SELECT product_name FROM products WHERE id=$product_id");
        return $qry['product_name'];
    }
}
if (!function_exists('getProductScreenshots')) {
    function getProductScreenshots($product_id)
    {
        global $DB;
        $product_id = intval($product_id);
        $res        = $DB->query_first("SELECT COUNT(`id`) FROM `hg_products`.`products_screenshots` WHERE `product_id` = $product_id");
        return $res[0];
    }
}
if (!function_exists('getProductSections')) {
    function getProductSections()
    {
        global $DB;

        static $result = null;
        if (!is_null($result)) {
            return $result;
        }

        $result = [];
        $query  = $DB->query(sprintf(
            "SELECT `id`, `name` FROM `product_types` WHERE `deleted_at` IS NULL AND `parent` = 0 ORDER BY `ordering` ASC"
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row['id']] = $row['name'];
        }
        $DB->free_result($query);

        return $result;
    }
}
if (!function_exists('getProductSignals')) {
    function getProductSignals($family = '')
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `signal_id`, `signal` FROM `hg_products`.`products_signals`%s ORDER BY `signal` ASC",
            (!!$family) ? sprintf(
                " WHERE `family` = %d",
                $family
            ) : ""
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['signal_id']] = $row['signal'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getProductStock')) {
    function getProductStock($id, $sub_id = 0)
    {
        global $DB;
        $id     = intval($id);
        $sub_id = intval($sub_id);
        $qry    = $DB->query_first("SELECT `qty` FROM `hg_products`.`products` WHERE `id` = $id AND `sub_id` = $sub_id LIMIT 1");
        return $qry['qty'];
    }
}
if (!function_exists('getProductStockStr')) {
    function getProductStockStr($id, $sub_id = 0)
    {
        global $DB;
        $id     = intval($id);
        $sub_id = intval($sub_id);
        $row    = $DB->query_first("SELECT `qty`, `floor_stock` FROM `hg_products`.`products` WHERE `id` = $id AND `sub_id` = $sub_id LIMIT 1", MYSQLI_ASSOC);
        $qty    = $row['qty'];
        $used   = $row['floor_stock'];
        $str    = intval($qty - $used) . " (New), " . intval($used) . " (Used)";
        return $str;
    }
}
if (!function_exists('getProductSuppliers')) {
    function getProductSuppliers()
    {
        global $DB;
        $outarray = array();
        $qry      = $DB->query("SELECT `id`, `manufacturer` FROM `highwaygroup`.`suppliers` ORDER BY `manufacturer` ASC");
        while ($row = $DB->fetch_assoc($qry)) {
            $outarray[$row['id']] = $row['manufacturer'];
        }

        return $outarray;
    }
}
if (!function_exists('getProductValue')) {
    function getProductValue($value, $key)
    {
        $ret = '';
        switch ($key) {
            case 'type':
                $ret = getTypeName($value);
                break;
            case 'country':
                $ret = getCountryName($value);
                break;
            case 'availability':
                $ret = getAvailabilityName($value);
                break;
            case 'condition':
                $ret = getConditionName($value);
                break;
            case 'warranty':
                $ret = getWarrantyName($value);
                break;
            case 'supplier':
                $ret = getSupplierName($value);
                break;
            default:
                moduleLoad('product');
                $list = productGetOptionList($key);
                $ret  = $list[$value];
                break;
        }
        return $ret;
    }
}
if (!function_exists('getProductsPerCategory')) {
    function getProductsPerCategory($type_id, $all = false)
    {
        global $DB;
        $type_id = intval($type_id);
        $total   = $DB->query_first("SELECT COUNT(*) FROM `hg_products`.`products` WHERE `category` = $type_id" . (($all) ? '' : " AND `active` = 'y'") . " LIMIT 1");
        return $total[0];
    }
}
if (!function_exists('getProductsPerLocation')) {
    function getProductsPerLocation($location_id)
    {
        global $DB;
        $location_id = intval($location_id);
        $sql         = "SELECT COUNT(*) FROM hg_produicts.products WHERE storage='$location_id' AND active='y' LIMIT 1";
        $total       = $DB->query_first($sql);
        return $total[0];
    }
}
if (!function_exists('getProductsPerSupplier')) {
    function getProductsPerSupplier($supplier_id)
    {
        global $DB;
        $supplier_id = intval($supplier_id);
        $sql         = "SELECT COUNT(*) FROM `hg_products`.`products` WHERE `supplier` = '$supplier_id' AND `active` = 'y' LIMIT 1";
        $total       = $DB->query_first($sql);
        return $total[0];
    }
}
if (!function_exists('getPurchaseOrderCharges')) {
    function getPurchaseOrderCharges($charge_id = 0)
    {
        global $DB;
        $sql = "SELECT * FROM `highwaygroup`.`purchase_orders_charges` " . (($charge_id > 0) ? "WHERE `id` = $charge_id" : '') . " ORDER BY `id`";
        $res = $DB->query($sql);
        $ret = array();
        while ($row = $DB->fetch_assoc($res)) {
            if ($charge_id > 0) {
                return $row;
            }

            $ret[] = $row;
        }
        return $ret;
    }
}
if (!function_exists('getQuoteAttachments')) {
    function getQuoteAttachments($id)
    {
        global $DB;
        $outarray = array();
        $sql      = "SELECT * FROM `coinop`.`freight_forwarders_quotes` WHERE ((`master_airway_file` != '' OR `lading_file` != '') AND `id` = '" . $id . "')";
        $query    = $DB->query($sql);
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $outarray[] = $row;
        }

        return $outarray;
    }
}
if (!function_exists('getSVID')) {
    function getSVID($courier_id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT `sv_id` FROM `converters`.`ship_via` WHERE `courier_id` = %d LIMIT 1",
            $courier_id
        ), MYSQLI_ASSOC)) {
            return $row['sv_id'];
        }
        
        return $courier_id;
    }
}
if (!function_exists('getSalesRep')) {
    function getSalesRep()
    {
        global $DB;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `staff_handle`, `staff_name` FROM `converters`.`car_sales_reps`"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['staff_handler']] = $row['staff_name'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getScreenshotLetter')) {
    function getScreenshotLetter($id)
    {
        global $DB;
        $id            = intval($id);
        $array         = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        $res           = $DB->query("SELECT `id` FROM `highwaygroup`.`purchase_orders_attachments` WHERE `order_id` = $id");
        $rank          = $DB->num_rows($res);
        $ret           = array();
        $ret['rank']   = $rank + 1;
        $ret['letter'] = $array[$rank];
        return $ret;
    }
}
if (!function_exists('getSeaPortsList')) {
    function getSeaPortsList()
    {
        global $DB;
        static $results = array();
        if (count($results)) {
            return $results;
        }

        $query = $DB->query("SELECT `id`, `country`, `name` FROM `world`.`seaports` ORDER BY `name` ASC");
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $results[$row['country']][$row['id']] = $row['name'];
        }

        $DB->free_result($query);

        return $results;
    }
}
if (!function_exists('getSectionName')) {
    function getSectionName($section_id = 0)
    {
        global $DB;
        $section_id = intval($section_id);
        $row        = $DB->query_first("SELECT name FROM `highwaygroup`.`job_sections` WHERE `id` = $section_id");
        if ($section_id > 0) {
            return $row['name'];
        }

        return 'None Selected';
    }
}
if (!function_exists('getSections')) {
    function getSections()
    {
        global $DB;

        $result = array();
        $query  = $DB->query("SELECT `id`, `name` FROM `highwaygroup`.`job_sections`");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row['id']] = $row['name'];
        }

        $DB->free_result($query);

        return $result;
    }
}
if (!function_exists('getShipToList')) {
    function getShipToList($address = false)
    {
        global $DB;
        $key            = ($address) ? 1 : 0;
        static $results = array();
        if (count($results)) {
            return $results[$key];
        }

        $query = $DB->query("SELECT `id`, `name`, `address`, `city`, `state`, `post_code`, `country`, `phone_no`, `fax`, `email` FROM `highwaygroup`.`purchase_orders_shipping_address` ORDER BY `name` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $results[0][$row["id"]] = $row["name"];
            $results[1][$row["id"]] = $row["name"] . "\n" . $row["address"] . ",\n" . $row["city"] . ", " . $row["state"] . ", " . trim(getCountryName($row["country"])) . ", " . $row["post_code"] . "\n\nPhone Number: " . $row["phone_no"] . ", Fax: " . $row["fax"] . "\nEmail: " . $row["email"];
        }
        $DB->free_result($query);

        return $results[$key];
    }
}
if (!function_exists('getShipVia')) {
    function getShipVia()
    {
        global $DB;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `sv_id`, `via` FROM `converters`.`ship_via` ORDER BY `via` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['sv_id']] = $row['via'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getShipViaList')) {
    function getShipViaList()
    {
        global $DB;
        static $outarray = '';
        if ('' == $outarray) {
            $outarray = array();
            $query    = $DB->query("SELECT DISTINCT name, prop_id FROM shipping_services ORDER BY name ASC");
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $outarray[$row['prop_id']] = $row['name'];
            }

        }
        return $outarray;
    }
}
if (!function_exists('getShipmentViaList')) {
    function getShipmentViaList()
    {
        return array(1 => 'Ocean Forwarder', 2 => 'Door To Port', 3 => 'Service');
    }
}
if (!function_exists('getShippingInvoices')) {
    function getShippingInvoices($id = 0)
    {
        global $DB;
        $res = $DB->query("SELECT * FROM `highwaygroup`.`purchase_orders_shipping_invoices`" . (($id > 0) ? " WHERE `id` = $id" : ''));
        $ret = array();
        while ($row = $DB->fetch_assoc($res)) {
            if ($id > 0) {
                return $row['label'];
            }

            $ret[$row['id']] = $row['label'];
        }
        return $ret;
    }
}
if (!function_exists('getShippingZoneName')) {
    function getShippingZoneName($zone_id)
    {
        global $DB;
        $zone_id = intval($zone_id);
        $row     = $DB->query_first("SELECT name FROM `highwaygroup`.`freight_services_shipping_zone` WHERE `id` = $zone_id", MYSQLI_ASSOC);
        return $row['name'];
    }
}
if (!function_exists('getShippingZones')) {
    function getShippingZones($product_id)
    {
        global $DB;
        $key            = intval($product_id);
        static $results = array();
        if (array_key_exists($key, $results)) {
            return $results[$key];
        }

        $results[$key] = array();
        $query         = $DB->query("SELECT `id`, `name` FROM `highwaygroup`.`freight_services_shipping_zone` WHERE `product_id` = " . intval($product_id));
        while ($row = $DB->fetch_assoc($query)) {
            $results[$key][$row['id']] = $row['name'];
        }

        $DB->free_result($query);

        return $results[$key];
    }
}
if (!function_exists('getShips')) {
    function getShips()
    {
        global $DB;
        $outarray = array();
        $sql      = "SELECT * FROM `world`.`shipping_notes` WHERE `country` = 'AU'";
        $query    = $DB->query($sql);
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $outarray[] = $row;
        }

        return $outarray;
    }
}
if (!function_exists('getSignalColour')) {
    function getSignalColour($id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT `colour` FROM `hg_products`.`products_signals_family` WHERE `id` = %d LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            return $row['colour'];
        }

        return null;
    }
}
if (!function_exists('getSignalDetails')) {
    function getSignalDetails($id, $field = '')
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`last_update`) AS `last_update_unix` FROM `hg_products`.`products_signals` WHERE `signal_id` = %d LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            if ('' != $field) {
                return $row[$field];
            }
            return $row;
        }

        return false;
    }
}
if (!function_exists('getSignalFamilyGroup')) {
    function getSignalFamilyGroup($orderby = '', $exclude = 0)
    {
        global $DB;

        $order_by = ('' != $orderby) ? sprintf(
            " ORDER BY %s ASC",
            $orderby
        ) : "";
        if ('rank' == $orderby) {
            $order_by .= ", `name` ASC";
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `name` FROM `hg_products`.`products_signals_family`%s",
            $order_by
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row['name'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getSignalGroupDetails')) {
    function getSignalGroupDetails($id, $field = '')
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`last_update`) AS `last_update_unix` FROM `hg_products`.`products_signals_family` WHERE `id` = %d LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            if ('' != $field) {
                return $row[$field];
            }
            return $row;
        }

        return false;
    }
}
if (!function_exists('getSignalGroupID')) {
    function getSignalGroupID($name, $search = false)
    {
        global $DB;
        $sql1 = "SELECT * FROM `hg_products`.`products_signals` WHERE `signal` = '" . $DB->escape_string($name) . "'";
        $sql2 = "SELECT * FROM `hg_products`.`products_signals_family` WHERE `name` = '" . $DB->escape_string($name) . "'";
        if ($search) {
            $sql1 = "SELECT * FROM `hg_products`.`products_signals` WHERE `signal` LIKE '" . $DB->escape_string('%' . $name . '%') . "'";
            $sql2 = "SELECT * FROM `hg_products`.`products_signals_family` WHERE `name` LIKE '" . $DB->escape_string('%' . $name . '%') . "'";
        }
        $row1 = $DB->query_first($sql1, MYSQLI_ASSOC);
        $row2 = $DB->query_first($sql2, MYSQLI_ASSOC);
        if ($row1) {
            $row1['id']        = $row1['signal_id'];
            $row1['name']      = $row1['signal'];
            $row1['is_family'] = false;
            return $row1;
        } elseif ($row2) {
            $row2['is_family'] = true;
            return $row2;
        }
        return false;
    }
}
if (!function_exists('getSignalGroupList')) {
    function getSignalGroupList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        foreach (getSignalFamilyGroup() as $k => $v) {
            if ($query = $DB->query(sprintf(
                "SELECT `signal_id`, `signal` FROM `hg_products`.`products_signals` WHERE `family` = %d",
                $k
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $result[$v][$row['signal_id']] = $row['signal'];
                }
                $DB->free_result($query);
            }
        }

        return $result;
    }
}
if (!function_exists('getSignalGroupName')) {
    function getSignalGroupName($id)
    {
        return getSignalGroupDetails($id, 'name');
    }
}
if (!function_exists('getSignalList')) {
    function getSignalList()
    {
        global $DB;
        $outarray = array();
        $res      = $DB->query("SELECT `id`, `value` FROM `world`.`video_signal` ORDER BY `value`");
        while ($row = $DB->fetch_assoc($res)) {
            $outarray[$row['id']] = $row['value'];
        }

        $DB->free_result($res);
        return $outarray;
    }
}
if (!function_exists('getSignalName')) {
    function getSignalName($id)
    {
        return getSignalDetails($id, 'signal');
    }
}
if (!function_exists('getSignalsPerFamily')) {
    function getSignalsPerFamily($id, $all = false)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `signal_id`, `signal`, `icon_image` FROM `hg_products`.`products_signals` WHERE `family` = %d",
            $id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['signal_id']] = ($all) ? $row : $row['signal'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getStaffEmail')) {
    function getStaffEmail($user)
    {
        global $DB;
        $outarray = array();
        $sql      = "SELECT `email` FROM `highwaygroup`.`users` ";
        if (is_numeric($user)) {
            $sql .= "WHERE `id` = '$user'";
        } else {
            $sql .= "WHERE `user` = '$user'";
        }

        $row = $query = $DB->query_first($sql, MYSQLI_ASSOC);
        return $row['email'];
    }
}
if (!function_exists('getStaffIDFromName')) {
    function getStaffIDFromName($full_name)
    {
        global $DB;
        $sql = "SELECT id FROM `highwaygroup`.`users` WHERE CONCAT_WS(' ', `first_name`, `last_name`) = '" . $DB->escape_string($full_name) . "'";
        $row = $DB->query_first($sql, MYSQLI_ASSOC);
        return $row['id'];
    }
}
if (!function_exists('getStaffList')) {
    function getStaffList()
    {
        global $DB;
        static $outarray = '';
        if ('' == $outarray) {
            $outarray = array();
            $sql      = "SELECT `id` AS `staff_id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `staff_name` FROM `highwaygroup`.`users` WHERE `active` = 'y'";
            $sql .= " ORDER BY `user`";
            $query = $DB->query($sql);
            while ($row = $DB->fetch_array($query)) {
                $outarray[$row['staff_id']] = $row['staff_name'];
            }

        }
        return $outarray;
    }
}
if (!function_exists('getStaffName')) {
    function getStaffName($user)
    {
        global $DB;
        $sql = "SELECT `id` AS `staff_id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `staff_name` FROM `highwaygroup`.`users` ";
        if (is_numeric($user)) {
            $sql .= "WHERE `id` = $user";
        } else {
            $sql .= "WHERE `user` = '$user'";
        }

        $row = $DB->query_first($sql, MYSQLI_ASSOC);
        return $row['staff_name'];
    }
}
if (!function_exists('getStorageLocations')) {
    function getStorageLocations()
    {
        global $DB;
        $outarray = array();
        $qry      = $DB->query("SELECT * FROM `hg_products`.`products_location` ORDER BY `location` ASC");
        while ($row = $DB->fetch_assoc($qry)) {
            $outarray[$row['id']] = $row['location'];
        }

        return $outarray;
    }
}
if (!function_exists('getStyleName')) {
    function getStyleName($id)
    {
        $id = intval($id);
        if (!$id) {
            return 'Not Selected';
        }

        return getCategoryDetails($id, 'name');
    }
}
if (!function_exists('getSubCategory')) {
    function getSubCategory($parent_id = 0)
    {
        global $DB;
        $parent_id = intval($parent_id);
        $outarray  = array();
        $res       = $DB->query("SELECT * FROM `hg_products`.`products_types` WHERE `parent` = $parent_id");
        while ($child = $DB->fetch_assoc($res)) {
            $outarray[$child['type_id']] = $child['name'];
        }

        return $outarray;
    }
}
if (!function_exists('getSupplierCountry')) {
    function getSupplierCountry($supplier_id)
    {
        global $DB;
        $supplier_id = intval($supplier_id);
        if (!$supplier_id) {
            return '';
        }

        $ret = $DB->query_first("SELECT `country` FROM `highwaygroup`.`suppliers` WHERE `id` = " . $supplier_id, MYSQLI_ASSOC);
        if ($ret) {
            return $ret['country'];
        }

        return '';
    }
}
if (!function_exists('getSupplierName')) {
    function getSupplierName($supplier_id)
    {
        global $DB;
        $supplier_id = intval($supplier_id);
        if (0 == $supplier_id) {
            return 'None';
        }

        $qry = $DB->query_first("SELECT `manufacturer` FROM `highwaygroup`.`suppliers` WHERE `id` = $supplier_id");
        return $qry['manufacturer'];
    }
}
if (!function_exists('getSupplierValues')) {
    function getSupplierValues($supplier_id)
    {
        global $DB;
        $supplier_id = intval($supplier_id);
        if (!$supplier_id) {
            return;
        }

        $row = $DB->query_first("SELECT `warranty`, `voltage`, `condition`, `packing`, `terms`, `plug` FROM `highwaygroup`.suppliers WHERE id=" . $supplier_id, MYSQLI_ASSOC);
        if ($row) {
            return $row;
        }

        return false;
    }
}
if (!function_exists('getTableColumns')) {
    /**
     * @param string $table_name
     *
     * @return string[]
     */
    function getTableColumns($table_name)
    {
        $result = array();
        foreach (getTableFields($table_name) as $k => $v) {
            // @todo Use the string index instead of the numeric index
            $result[$k] = $v[0];
        }

        return $result;
    }
}
if (!function_exists('getTableExists')) {
    function getTableExists($table_name)
    {
        global $DB;
        static $buffer = array();
        if (isset($buffer[$table_name])) {
            return $buffer[$table_name];
        }

        $query = $DB->query("SHOW TABLES LIKE '" . $DB->escape_string($table_name) . "'");

        $buffer[$table_name] = ($DB->num_rows($query) == 1);
        $DB->free_result($query);
        return $buffer[$table_name];
    }
}
if (!function_exists('getTableFields')) {
    function getTableFields($table_name)
    {
        global $DB;

        static $cache = array();
        if (array_key_exists($table_name, $cache)) {
            return $cache[$table_name];
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM %s",
            $table_name
        ))) {
            while ($row = $DB->fetch_array($query, MYSQLI_BOTH)) {
                // @todo Use the string index instead of the numeric index
                $result[$row[0]] = $row;
            }
            $DB->free_result($query);
        }

        $cache[$table_name] = $result;

        return $result;
    }
}
if (!function_exists('getTechnicianEmail')) {
    function getTechnicianEmail($tech_id)
    {
        global $DB;
        $tech_id = intval($tech_id);
        if (!$tech_id) {
            return '';
        }

        $row = $DB->query_first("SELECT `email` FROM `highwaygroup`.`technicians` WHERE `id` = $tech_id", MYSQLI_ASSOC);
        if ($row) {
            return $row['email'];
        }

        return '';
    }
}
if (!function_exists('getTechnicians')) {
    function getTechnicians($tech_id = 0)
    {
        global $DB;
        static $results = array();

        if (!count($results)) {
            $query = $DB->query("SELECT `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name` FROM `highwaygroup`.`technicians`");
            while ($row = $DB->fetch_assoc($query)) {
                $results[$row['id']] = $row['name'];
            }

            $DB->free_result($query);
        }

        if (0 != $tech_id) {
            return $results[$tech_id];
        }

        return $results;
    }
}
if (!function_exists('getTermsList')) {
    function getTermsList()
    {
        global $DB;
        static $outarray = '';
        if ('' == $outarray) {
            $outarray = array();
            $query    = $DB->query("SELECT id, term FROM `highwaygroup`.purchase_orders_terms ORDER BY term");
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $outarray[$row['id']] = ucfirst($row['term']);
            }

            $DB->free_result($query);
        }
        return $outarray;
    }
}
if (!function_exists('getTermsName')) {
    function getTermsName($terms_id)
    {
        global $DB;
        $outtext = '';
        if ('' == $outtext) {
            $query = $DB->query("SELECT term FROM `highwaygroup`.purchase_orders_terms WHERE id = '$terms_id'");
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $outtext = ucfirst($row['term']);
            }

            $DB->free_result($query);
        }
        return $outtext;
    }
}
if (!function_exists('getTimeZones')) {
    function getTimeZones()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `country`, `name`, `offset`, `day_light` FROM `world`.`time_zones` ORDER BY `name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['country']][] = array(
                    'id'        => $row['id'],
                    'zone'      => $row['name'],
                    'offset'    => $row['offset'],
                    'day_light' => $row['day_light'],
                );
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getTypeID')) {
    function getTypeID($type_name)
    {
        global $DB;
        $qry = $DB->query_first("SELECT type_id FROM hg_products.products_types WHERE name='$type_name'");
        return $qry['type_id'];
    }
}
if (!function_exists('getTypeName')) {
    function getTypeName($type_id)
    {
        global $DB;
        $type_id = intval($type_id);
        if (0 == $type_id) {
            return 'None';
        }

        $qry = $DB->query_first("SELECT name FROM hg_products.products_types WHERE type_id=$type_id");
        return $qry['name'];
    }
}
if (!function_exists('getVersionType')) {
    function getVersionType()
    {
        global $DB;
        static $result = array();
        if (count($result)) {
            return $result;
        }

        $query = $DB->query("SELECT `id`, `type` FROM `highwaygroup`.`version_type` ORDER BY `type` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row['id']] = $row['type'];
        }

        $DB->free_result($query);

        return $result;
    }
}
if (!function_exists('getVoltages')) {
    function getVoltages($id = '')
    {
        global $DB;
        static $outarray = '';
        if ('' == $outarray) {
            $outarray = array();
            $sql      = "SELECT DISTINCT value, id  FROM `world`.voltage ";
            if ('' != $id) {
                $sql .= "WHERE id = '$id' ";
            }

            $query = $DB->query($sql);
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $outarray[$row['id']] = $row['value'];
            }

        }
        if ('' != $id) {
            return $outarray[$id];
        }

        return $outarray;
    }
}
if (!function_exists('getWarantyTermsList')) {
    /**
     * @deprecated use getWarrantyTermsList
     *
     * @return string[]
     */
    function getWarantyTermsList()
    {
        return getWarrantyTermsList();
    }
}
if (!function_exists('getWarrantyName')) {
    /**
     * @param int $id
     *
     * @return string
     */
    function getWarrantyName($id)
    {
        $warranty_terms_list = getWarrantyTermsList();
        if (array_key_exists($id, $warranty_terms_list)) {
            return $warranty_terms_list[$id];
        }

        return '';
    }
}
if (!function_exists('getWarrantyTermsList')) {
    /**
     * @return string[]
     */
    function getWarrantyTermsList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `waranty` FROM `highwaygroup`.`waranty_terms` ORDER BY `waranty` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row['waranty'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getWarrantyTerms')) {
    /**
     * @deprecated use getWarrantyTermsList()
     *
     * @return string[]
     */
    function getWarrantyTerms()
    {
        return getWarrantyTermsList();
    }
}
if (!function_exists('getWebsitesList')) {
    function getWebsitesList()
    {
        global $DB;
        static $outarray = '';
        if ('' == $outarray) {
            $outarray = array();
            $query    = $DB->query("SELECT `id`, `website` FROM `highwaygroup`.`websites` ORDER BY `website` ASC");
            while ($row = $DB->fetch_array($query)) {
                $outarray[$row['id']] = $row['website'];
            }

        }
        $DB->free_result();
        return $outarray;
    }
}
if (!function_exists('getWeightList')) {
    function getWeightList()
    {
        return array(
            'yoctogram'  => "1e-24",
            'yg'         => "1e-24",
            'zeptogram'  => "1e-21",
            'zg'         => "1e-21",
            'attogram'   => "1e-18",
            'ag'         => "1e-18",
            'femtogram'  => "1e-15",
            'fg'         => "1e-15",
            'picogram'   => "1e-12",
            'pg'         => "1e-12",
            'nanogram'   => "1e-9",
            'ng'         => "1e-9",
            'microgram'  => "0.000001",
            'mcg'        => "0.000001",
            'milligram'  => "0.001",
            'mg'         => "0.001",
            'centigram'  => "0.01",
            'cg'         => "0.01",
            'decigram'   => "0.1",
            'dg'         => "0.1",
            'gram'       => "1.0",
            'g'          => "1.0",
            'decagram'   => "10.0",
            'dag'        => "10.0",
            'hectogram'  => "100.0",
            'hg'         => "100.0",
            'kilogram'   => "1000.0",
            'kg'         => "1000.0",
            'centner'    => "100000.0",
            'megagram'   => "1000000.0",
            'tonne'      => "1000000.0",
            'Mg'         => "1000000.0",
            'gigagram'   => "1e+9",
            'kilotonne'  => "1e+9",
            'Gg'         => "1e+9",
            'teragram'   => "1e+12",
            'Tg'         => "1e+12",
            'petagram'   => "1e+15",
            'Pg'         => "1e+15",
            'exagram'    => "1e+18",
            'Eg'         => "1e+18",
            'zettagram'  => "1e+21",
            'Zg'         => "1e+21",
            'yottagram'  => "1e+24",
            'Yg'         => "1e+24",
            'grain'      => "0.0648",
            'gr'         => "0.0648",
            'carat'      => "0.2",
            'CD'         => "0.2",
            'dram'       => "1.772",
            'dr'         => "1.772",
            'ounce'      => "28.35",
            'oz'         => "28.35",
            'newton'     => "102.0",
            'N'          => "102.0",
            'pound'      => "453.6",
            'lb'         => "453.6",
            'stone'      => "6350.0",
            'st'         => "6350.0",
            'kilonewton' => "102000.0",
            'kN'         => "102000.0",
        );
    }
}
if (!function_exists('getZoneCountries')) {
    function getZoneCountries($product_id)
    {
        global $DB;
        $product_id = intval($product_id);
        $zones      = getShippingZones($product_id);
        $countries  = array();
        foreach ($zones as $id => $zone) {
            $res = $DB->query("SELECT country FROM `highwaygroup`.`freight_services_int_destinations` WHERE product_id=$product_id AND zone_no=$id");
            while ($row = $DB->fetch_assoc($res)) {
                $countries[$id][] = getCountryName($row['country']);
            }
        }
        return $countries;
    }
}
if (!function_exists('getZonePricing')) {
    function getZonePricing($zone_id, $second = false)
    {
        global $DB;
        $zone_id = intval($zone_id);
        $ret     = array();
        $res     = $DB->query("SELECT id,price FROM `highwaygroup`.`freight_services_zone_prices` WHERE zone_no=$zone_id ORDER BY weight ASC");
        while ($row = $DB->fetch_assoc($res)) {
            $ret[] = array('id' => $row['id'], 'price' => $row['price']);
        }
        return $ret;
    }
}
if (!function_exists('getZoneWeights')) {
    /**
     * @param int  $zone_id
     * @param bool $second
     *
     * @return string[]
     */
    function getZoneWeights($zone_id, $second = false)
    {
        global $DB;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT DISTINCT `weight` FROM `highwaygroup`.`freight_services_zone_prices` WHERE `zone_no` = %d ORDER BY `weight` ASC",
            $zone_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['weight'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('isCategory')) {
    /**
     * @param int $type_id
     *
     * @return bool
     */
    function isCategory($type_id)
    {
        global $DB;
        return ($DB->query_first("SELECT type_id FROM hg_products.products_types WHERE parent=" . intval($type_id) . " LIMIT 1", MYSQLI_NUM) !== false);
    }
}
if (!function_exists('isMajorCategory')) {
    /**
     * @param int $signal_id
     *
     * @return bool
     */
    function isMajorCategory($signal_id)
    {
        global $DB;

        return !!$DB->query_first(sprintf(
            "SELECT 1 FROM `hg_products`.`products_signals_family` WHERE `id` = %d AND `major_category` = 'y' LIMIT 1",
            $signal_id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('is_bottom_array')) {
    /**
     * @param mixed[] $inArray
     *
     * @return bool
     */
    function is_bottom_array($inArray)
    {
        foreach ($inArray as $value) {
            if (is_array($value)) {
                return false;
            }
        }
        return true;
    }
}
if (!function_exists('load_class')) {
    /**
     * @param string $class
     * @param bool   $instantiate
     *
     * @return void
     */
    function load_class($class, $instantiate = true)
    {
        static $objects = array();
        if (isset($objects[$class])) {
            return $objects[$class];
        }

        $is_subclass = false;
        if (file_exists('class.' . $class . '.php')) {
            include_once realpath('class.' . $class . '.php');
            $is_subclass = true;
        } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . '/webadmin/' . $class . '/class.' . $class . '.php')) {
            include_once realpath($_SERVER['DOCUMENT_ROOT'] . '/webadmin/' . $class . '/class.' . $class . '.php');
            $is_subclass = true;
        }
        if (false == $instantiate) {
            $objects[$class] = true;
            return $objects[$class];
        }
        if (true == $is_subclass) {
            $objects[$class] = new $class();
            return $objects[$class];
        }
        $name            = ('Controller' != $class) ? 'CI_' . $class : $class;
        $objects[$class] = new $name();
        return $objects[$class];
    }
}
if (!function_exists("list_to_partitions")) {
    /**
     * @param int[] $list
     *
     * @return int[]
     */
    function list_to_partitions($list)
    {
        $partitions = array();
        foreach ($list as $v) {
            $prev_id = $v - 1;
            $next_id = $v + 1;
            if (array_key_exists($prev_id, $partitions)) {
                $test = array_search($next_id, $partitions);
                if (false !== $test) {
                    $partitions[$test] = $partitions[$prev_id];
                    unset($partitions[$prev_id]);
                } else {
                    $partitions[$v] = $partitions[$prev_id];
                    unset($partitions[$prev_id]);
                }
            } else {
                $test = array_search($next_id, $partitions);
                if (false !== $test) {
                    $partitions[$test] = $v;
                } else {
                    $partitions[$v] = $v;
                }
            }
        }
        ksort($partitions);
        return $partitions;
    }
}
if (!function_exists('mime_extension_type')) {
    /**
     * @param string $fname
     *
     * @return string
     */
    function mime_extension_type($fname)
    {
        $ext = explode('.', trim($fname));
        $ext = strtolower(end($ext));
        if ('' != $ext) {
            foreach (file('/etc/mime.types') as $row) {
                $row = trim($row);
                if ('' != $row && substr($row, 0, 1) != '#') {
                    $row       = preg_split('/[\\t ]+/', strtolower($row));
                    $mime_type = array_shift($row);
                    if (in_array($ext, $row)) {
                        return $mime_type;
                    }

                }
            }
        }
        return 'application/octet-stream';
    }
}
function mime2ext($mime) {
    $mime_map = [
        'video/3gpp2'                                                               => '3g2',
        'video/3gp'                                                                 => '3gp',
        'video/3gpp'                                                                => '3gp',
        'application/x-compressed'                                                  => '7zip',
        'audio/x-acc'                                                               => 'aac',
        'audio/ac3'                                                                 => 'ac3',
        'application/postscript'                                                    => 'ai',
        'audio/x-aiff'                                                              => 'aif',
        'audio/aiff'                                                                => 'aif',
        'audio/x-au'                                                                => 'au',
        'video/x-msvideo'                                                           => 'avi',
        'video/msvideo'                                                             => 'avi',
        'video/avi'                                                                 => 'avi',
        'application/x-troff-msvideo'                                               => 'avi',
        'application/macbinary'                                                     => 'bin',
        'application/mac-binary'                                                    => 'bin',
        'application/x-binary'                                                      => 'bin',
        'application/x-macbinary'                                                   => 'bin',
        'image/bmp'                                                                 => 'bmp',
        'image/x-bmp'                                                               => 'bmp',
        'image/x-bitmap'                                                            => 'bmp',
        'image/x-xbitmap'                                                           => 'bmp',
        'image/x-win-bitmap'                                                        => 'bmp',
        'image/x-windows-bmp'                                                       => 'bmp',
        'image/ms-bmp'                                                              => 'bmp',
        'image/x-ms-bmp'                                                            => 'bmp',
        'application/bmp'                                                           => 'bmp',
        'application/x-bmp'                                                         => 'bmp',
        'application/x-win-bitmap'                                                  => 'bmp',
        'application/cdr'                                                           => 'cdr',
        'application/coreldraw'                                                     => 'cdr',
        'application/x-cdr'                                                         => 'cdr',
        'application/x-coreldraw'                                                   => 'cdr',
        'image/cdr'                                                                 => 'cdr',
        'image/x-cdr'                                                               => 'cdr',
        'zz-application/zz-winassoc-cdr'                                            => 'cdr',
        'application/mac-compactpro'                                                => 'cpt',
        'application/pkix-crl'                                                      => 'crl',
        'application/pkcs-crl'                                                      => 'crl',
        'application/x-x509-ca-cert'                                                => 'crt',
        'application/pkix-cert'                                                     => 'crt',
        'text/css'                                                                  => 'css',
        'text/x-comma-separated-values'                                             => 'csv',
        'text/comma-separated-values'                                               => 'csv',
        'application/vnd.msexcel'                                                   => 'csv',
        'application/x-director'                                                    => 'dcr',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
        'application/x-dvi'                                                         => 'dvi',
        'message/rfc822'                                                            => 'eml',
        'application/x-msdownload'                                                  => 'exe',
        'video/x-f4v'                                                               => 'f4v',
        'audio/x-flac'                                                              => 'flac',
        'video/x-flv'                                                               => 'flv',
        'image/gif'                                                                 => 'gif',
        'application/gpg-keys'                                                      => 'gpg',
        'application/x-gtar'                                                        => 'gtar',
        'application/x-gzip'                                                        => 'gzip',
        'application/mac-binhex40'                                                  => 'hqx',
        'application/mac-binhex'                                                    => 'hqx',
        'application/x-binhex40'                                                    => 'hqx',
        'application/x-mac-binhex40'                                                => 'hqx',
        'text/html'                                                                 => 'html',
        'image/x-icon'                                                              => 'ico',
        'image/x-ico'                                                               => 'ico',
        'image/vnd.microsoft.icon'                                                  => 'ico',
        'text/calendar'                                                             => 'ics',
        'application/java-archive'                                                  => 'jar',
        'application/x-java-application'                                            => 'jar',
        'application/x-jar'                                                         => 'jar',
        'image/jp2'                                                                 => 'jp2',
        'video/mj2'                                                                 => 'jp2',
        'image/jpx'                                                                 => 'jp2',
        'image/jpm'                                                                 => 'jp2',
        'image/jpeg'                                                                => 'jpeg',
        'image/pjpeg'                                                               => 'jpeg',
        'application/x-javascript'                                                  => 'js',
        'application/json'                                                          => 'json',
        'text/json'                                                                 => 'json',
        'application/vnd.google-earth.kml+xml'                                      => 'kml',
        'application/vnd.google-earth.kmz'                                          => 'kmz',
        'text/x-log'                                                                => 'log',
        'audio/x-m4a'                                                               => 'm4a',
        'audio/mp4'                                                                 => 'm4a',
        'application/vnd.mpegurl'                                                   => 'm4u',
        'audio/midi'                                                                => 'mid',
        'application/vnd.mif'                                                       => 'mif',
        'video/quicktime'                                                           => 'mov',
        'video/x-sgi-movie'                                                         => 'movie',
        'audio/mpeg'                                                                => 'mp3',
        'audio/mpg'                                                                 => 'mp3',
        'audio/mpeg3'                                                               => 'mp3',
        'audio/mp3'                                                                 => 'mp3',
        'video/mp4'                                                                 => 'mp4',
        'video/mpeg'                                                                => 'mpeg',
        'application/oda'                                                           => 'oda',
        'audio/ogg'                                                                 => 'ogg',
        'video/ogg'                                                                 => 'ogg',
        'application/ogg'                                                           => 'ogg',
        'font/otf'                                                                  => 'otf',
        'application/x-pkcs10'                                                      => 'p10',
        'application/pkcs10'                                                        => 'p10',
        'application/x-pkcs12'                                                      => 'p12',
        'application/x-pkcs7-signature'                                             => 'p7a',
        'application/pkcs7-mime'                                                    => 'p7c',
        'application/x-pkcs7-mime'                                                  => 'p7c',
        'application/x-pkcs7-certreqresp'                                           => 'p7r',
        'application/pkcs7-signature'                                               => 'p7s',
        'application/pdf'                                                           => 'pdf',
        'application/octet-stream'                                                  => 'pdf',
        'application/x-x509-user-cert'                                              => 'pem',
        'application/x-pem-file'                                                    => 'pem',
        'application/pgp'                                                           => 'pgp',
        'application/x-httpd-php'                                                   => 'php',
        'application/php'                                                           => 'php',
        'application/x-php'                                                         => 'php',
        'text/php'                                                                  => 'php',
        'text/x-php'                                                                => 'php',
        'application/x-httpd-php-source'                                            => 'php',
        'image/png'                                                                 => 'png',
        'image/x-png'                                                               => 'png',
        'application/powerpoint'                                                    => 'ppt',
        'application/vnd.ms-powerpoint'                                             => 'ppt',
        'application/vnd.ms-office'                                                 => 'ppt',
        'application/msword'                                                        => 'doc',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/x-photoshop'                                                   => 'psd',
        'image/vnd.adobe.photoshop'                                                 => 'psd',
        'audio/x-realaudio'                                                         => 'ra',
        'audio/x-pn-realaudio'                                                      => 'ram',
        'application/x-rar'                                                         => 'rar',
        'application/rar'                                                           => 'rar',
        'application/x-rar-compressed'                                              => 'rar',
        'audio/x-pn-realaudio-plugin'                                               => 'rpm',
        'application/x-pkcs7'                                                       => 'rsa',
        'text/rtf'                                                                  => 'rtf',
        'text/richtext'                                                             => 'rtx',
        'video/vnd.rn-realvideo'                                                    => 'rv',
        'application/x-stuffit'                                                     => 'sit',
        'application/smil'                                                          => 'smil',
        'text/srt'                                                                  => 'srt',
        'image/svg+xml'                                                             => 'svg',
        'application/x-shockwave-flash'                                             => 'swf',
        'application/x-tar'                                                         => 'tar',
        'application/x-gzip-compressed'                                             => 'tgz',
        'image/tiff'                                                                => 'tiff',
        'font/ttf'                                                                  => 'ttf',
        'text/plain'                                                                => 'txt',
        'text/x-vcard'                                                              => 'vcf',
        'application/videolan'                                                      => 'vlc',
        'text/vtt'                                                                  => 'vtt',
        'audio/x-wav'                                                               => 'wav',
        'audio/wave'                                                                => 'wav',
        'audio/wav'                                                                 => 'wav',
        'application/wbxml'                                                         => 'wbxml',
        'video/webm'                                                                => 'webm',
        'image/webp'                                                                => 'webp',
        'audio/x-ms-wma'                                                            => 'wma',
        'application/wmlc'                                                          => 'wmlc',
        'video/x-ms-wmv'                                                            => 'wmv',
        'video/x-ms-asf'                                                            => 'wmv',
        'font/woff'                                                                 => 'woff',
        'font/woff2'                                                                => 'woff2',
        'application/xhtml+xml'                                                     => 'xhtml',
        'application/excel'                                                         => 'xl',
        'application/msexcel'                                                       => 'xls',
        'application/x-msexcel'                                                     => 'xls',
        'application/x-ms-excel'                                                    => 'xls',
        'application/x-excel'                                                       => 'xls',
        'application/x-dos_ms_excel'                                                => 'xls',
        'application/xls'                                                           => 'xls',
        'application/x-xls'                                                         => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
        'application/vnd.ms-excel'                                                  => 'xlsx',
        'application/xml'                                                           => 'xml',
        'text/xml'                                                                  => 'xml',
        'text/xsl'                                                                  => 'xsl',
        'application/xspf+xml'                                                      => 'xspf',
        'application/x-compress'                                                    => 'z',
        'application/x-zip'                                                         => 'zip',
        'application/zip'                                                           => 'zip',
        'application/x-zip-compressed'                                              => 'zip',
        'application/s-compressed'                                                  => 'zip',
        'multipart/x-zip'                                                           => 'zip',
        'text/x-scriptzsh'                                                          => 'zsh',
    ];

    return isset($mime_map[$mime]) ? $mime_map[$mime] : false;
}
function ext2mime($file) {
    $ext = explode('.', trim($file));
    $ext = strtolower(end($ext));
    $mime_map = [
        'video/3gpp2'                                                               => '3g2',
        'video/3gp'                                                                 => '3gp',
        'video/3gpp'                                                                => '3gp',
        'application/x-compressed'                                                  => '7zip',
        'audio/x-acc'                                                               => 'aac',
        'audio/ac3'                                                                 => 'ac3',
        'application/postscript'                                                    => 'ai',
        'audio/x-aiff'                                                              => 'aif',
        'audio/aiff'                                                                => 'aif',
        'audio/x-au'                                                                => 'au',
        'video/x-msvideo'                                                           => 'avi',
        'video/msvideo'                                                             => 'avi',
        'video/avi'                                                                 => 'avi',
        'application/x-troff-msvideo'                                               => 'avi',
        'application/macbinary'                                                     => 'bin',
        'application/mac-binary'                                                    => 'bin',
        'application/x-binary'                                                      => 'bin',
        'application/x-macbinary'                                                   => 'bin',
        'image/bmp'                                                                 => 'bmp',
        'image/x-bmp'                                                               => 'bmp',
        'image/x-bitmap'                                                            => 'bmp',
        'image/x-xbitmap'                                                           => 'bmp',
        'image/x-win-bitmap'                                                        => 'bmp',
        'image/x-windows-bmp'                                                       => 'bmp',
        'image/ms-bmp'                                                              => 'bmp',
        'image/x-ms-bmp'                                                            => 'bmp',
        'application/bmp'                                                           => 'bmp',
        'application/x-bmp'                                                         => 'bmp',
        'application/x-win-bitmap'                                                  => 'bmp',
        'application/cdr'                                                           => 'cdr',
        'application/coreldraw'                                                     => 'cdr',
        'application/x-cdr'                                                         => 'cdr',
        'application/x-coreldraw'                                                   => 'cdr',
        'image/cdr'                                                                 => 'cdr',
        'image/x-cdr'                                                               => 'cdr',
        'zz-application/zz-winassoc-cdr'                                            => 'cdr',
        'application/mac-compactpro'                                                => 'cpt',
        'application/pkix-crl'                                                      => 'crl',
        'application/pkcs-crl'                                                      => 'crl',
        'application/x-x509-ca-cert'                                                => 'crt',
        'application/pkix-cert'                                                     => 'crt',
        'text/css'                                                                  => 'css',
        'text/x-comma-separated-values'                                             => 'csv',
        'text/comma-separated-values'                                               => 'csv',
        'application/vnd.msexcel'                                                   => 'csv',
        'application/x-director'                                                    => 'dcr',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
        'application/x-dvi'                                                         => 'dvi',
        'message/rfc822'                                                            => 'eml',
        'application/x-msdownload'                                                  => 'exe',
        'video/x-f4v'                                                               => 'f4v',
        'audio/x-flac'                                                              => 'flac',
        'video/x-flv'                                                               => 'flv',
        'image/gif'                                                                 => 'gif',
        'application/gpg-keys'                                                      => 'gpg',
        'application/x-gtar'                                                        => 'gtar',
        'application/x-gzip'                                                        => 'gzip',
        'application/mac-binhex40'                                                  => 'hqx',
        'application/mac-binhex'                                                    => 'hqx',
        'application/x-binhex40'                                                    => 'hqx',
        'application/x-mac-binhex40'                                                => 'hqx',
        'text/html'                                                                 => 'html',
        'image/x-icon'                                                              => 'ico',
        'image/x-ico'                                                               => 'ico',
        'image/vnd.microsoft.icon'                                                  => 'ico',
        'text/calendar'                                                             => 'ics',
        'application/java-archive'                                                  => 'jar',
        'application/x-java-application'                                            => 'jar',
        'application/x-jar'                                                         => 'jar',
        'image/jp2'                                                                 => 'jp2',
        'video/mj2'                                                                 => 'jp2',
        'image/jpx'                                                                 => 'jp2',
        'image/jpm'                                                                 => 'jp2',
        'image/jpeg'                                                                => 'jpeg',
        'image/pjpeg'                                                               => 'jpeg',
        'application/x-javascript'                                                  => 'js',
        'application/json'                                                          => 'json',
        'text/json'                                                                 => 'json',
        'application/vnd.google-earth.kml+xml'                                      => 'kml',
        'application/vnd.google-earth.kmz'                                          => 'kmz',
        'text/x-log'                                                                => 'log',
        'audio/x-m4a'                                                               => 'm4a',
        'audio/mp4'                                                                 => 'm4a',
        'application/vnd.mpegurl'                                                   => 'm4u',
        'audio/midi'                                                                => 'mid',
        'application/vnd.mif'                                                       => 'mif',
        'video/quicktime'                                                           => 'mov',
        'video/x-sgi-movie'                                                         => 'movie',
        'audio/mpeg'                                                                => 'mp3',
        'audio/mpg'                                                                 => 'mp3',
        'audio/mpeg3'                                                               => 'mp3',
        'audio/mp3'                                                                 => 'mp3',
        'video/mp4'                                                                 => 'mp4',
        'video/mpeg'                                                                => 'mpeg',
        'application/oda'                                                           => 'oda',
        'audio/ogg'                                                                 => 'ogg',
        'video/ogg'                                                                 => 'ogg',
        'application/ogg'                                                           => 'ogg',
        'font/otf'                                                                  => 'otf',
        'application/x-pkcs10'                                                      => 'p10',
        'application/pkcs10'                                                        => 'p10',
        'application/x-pkcs12'                                                      => 'p12',
        'application/x-pkcs7-signature'                                             => 'p7a',
        'application/pkcs7-mime'                                                    => 'p7c',
        'application/x-pkcs7-mime'                                                  => 'p7c',
        'application/x-pkcs7-certreqresp'                                           => 'p7r',
        'application/pkcs7-signature'                                               => 'p7s',
        'application/pdf'                                                           => 'pdf',
        'application/octet-stream'                                                  => 'pdf',
        'application/x-x509-user-cert'                                              => 'pem',
        'application/x-pem-file'                                                    => 'pem',
        'application/pgp'                                                           => 'pgp',
        'application/x-httpd-php'                                                   => 'php',
        'application/php'                                                           => 'php',
        'application/x-php'                                                         => 'php',
        'text/php'                                                                  => 'php',
        'text/x-php'                                                                => 'php',
        'application/x-httpd-php-source'                                            => 'php',
        'image/png'                                                                 => 'png',
        'image/x-png'                                                               => 'png',
        'application/powerpoint'                                                    => 'ppt',
        'application/vnd.ms-powerpoint'                                             => 'ppt',
        'application/vnd.ms-office'                                                 => 'ppt',
        'application/msword'                                                        => 'doc',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/x-photoshop'                                                   => 'psd',
        'image/vnd.adobe.photoshop'                                                 => 'psd',
        'audio/x-realaudio'                                                         => 'ra',
        'audio/x-pn-realaudio'                                                      => 'ram',
        'application/x-rar'                                                         => 'rar',
        'application/rar'                                                           => 'rar',
        'application/x-rar-compressed'                                              => 'rar',
        'audio/x-pn-realaudio-plugin'                                               => 'rpm',
        'application/x-pkcs7'                                                       => 'rsa',
        'text/rtf'                                                                  => 'rtf',
        'text/richtext'                                                             => 'rtx',
        'video/vnd.rn-realvideo'                                                    => 'rv',
        'application/x-stuffit'                                                     => 'sit',
        'application/smil'                                                          => 'smil',
        'text/srt'                                                                  => 'srt',
        'image/svg+xml'                                                             => 'svg',
        'application/x-shockwave-flash'                                             => 'swf',
        'application/x-tar'                                                         => 'tar',
        'application/x-gzip-compressed'                                             => 'tgz',
        'image/tiff'                                                                => 'tiff',
        'font/ttf'                                                                  => 'ttf',
        'text/plain'                                                                => 'txt',
        'text/x-vcard'                                                              => 'vcf',
        'application/videolan'                                                      => 'vlc',
        'text/vtt'                                                                  => 'vtt',
        'audio/x-wav'                                                               => 'wav',
        'audio/wave'                                                                => 'wav',
        'audio/wav'                                                                 => 'wav',
        'application/wbxml'                                                         => 'wbxml',
        'video/webm'                                                                => 'webm',
        'image/webp'                                                                => 'webp',
        'audio/x-ms-wma'                                                            => 'wma',
        'application/wmlc'                                                          => 'wmlc',
        'video/x-ms-wmv'                                                            => 'wmv',
        'video/x-ms-asf'                                                            => 'wmv',
        'font/woff'                                                                 => 'woff',
        'font/woff2'                                                                => 'woff2',
        'application/xhtml+xml'                                                     => 'xhtml',
        'application/excel'                                                         => 'xl',
        'application/msexcel'                                                       => 'xls',
        'application/x-msexcel'                                                     => 'xls',
        'application/x-ms-excel'                                                    => 'xls',
        'application/x-excel'                                                       => 'xls',
        'application/x-dos_ms_excel'                                                => 'xls',
        'application/xls'                                                           => 'xls',
        'application/x-xls'                                                         => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
        'application/vnd.ms-excel'                                                  => 'xlsx',
        'application/xml'                                                           => 'xml',
        'text/xml'                                                                  => 'xml',
        'text/xsl'                                                                  => 'xsl',
        'application/xspf+xml'                                                      => 'xspf',
        'application/x-compress'                                                    => 'z',
        'application/x-zip'                                                         => 'zip',
        'application/zip'                                                           => 'zip',
        'application/x-zip-compressed'                                              => 'zip',
        'application/s-compressed'                                                  => 'zip',
        'multipart/x-zip'                                                           => 'zip',
        'text/x-scriptzsh'                                                          => 'zsh',
    ];
    $key = array_search ($ext, $mime_map);
   

    return isset($key) ? $key : 'text/plain';
}
function getvideoid($link){
    $video_id = explode("?v=", $link); 
    if (empty($video_id[1]))
    $video_id = explode("/v/", $link); 
    $video_id = explode("&", $video_id[1]); // Deleting any other params
    $video_id = $video_id[0];
    return $video_id;
}
function resize_and_crop($image_path,$thumb_w, $thumb_h, $quality=100,$folder)
{
    $CACHE_FILE_PATH=$_SERVER["DOCUMENT_ROOT"].'/cache_image/'.$folder.'/';
    if (file_exists($image_path)) {
        //$image_path =ltrim($image_path, '/');
        $dir_name =  dirname($image_path);
        $dir_name =  explode("/", $dir_name);
        $dir_name =  implode("_", $dir_name);
        $file_name = basename($image_path);
        $file_name = explode('.', $file_name);
        $file_name[1]=mime2ext(mime_content_type($image_path));
        if (!empty($file_name)) {
            if(isset($file_name[1])){
            $image_thumb = $CACHE_FILE_PATH  . $file_name[0] . '_' . $thumb_w . '_' . $thumb_h . '.' . $file_name[1];
            } else {
             $image_thumb = ""; 
            }
            if (!file_exists($image_thumb)) {
               if(!strcmp("jpg",$file_name[1]) || !strcmp("JPG",$file_name[1]) || !strcmp("jpeg",$file_name[1]) || !strcmp("JPEG",$file_name[1])){
                   $original = imagecreatefromjpeg($image_path);
                }

                if(!strcmp("png",$file_name[1]) || !strcmp("PNG",$file_name[1])){
                     $original = imagecreatefrompng($image_path);
                }
                if(!strcmp("webp",$file_name[1])){
                    return $image_path;
                     //imagecreatefromwebp($image_path);
                }
                if(!strcmp("gif",$file_name[1])){
                    return $image_path;
                     //imagecreatefromwebp($image_path);
                }
            if (!$original) return FALSE;

            // GET ORIGINAL IMAGE DIMENSIONS
            list($original_w, $original_h) = getimagesize($image_path);

            // RESIZE IMAGE AND PRESERVE PROPORTIONS
            $thumb_w_resize = $thumb_w;
            $thumb_h_resize = $thumb_h;
            if ($original_w > $original_h)
            {
                $thumb_h_ratio  = $thumb_h / $original_h;
                $thumb_w_resize = (int)round($original_w * $thumb_h_ratio);
            }
            else
            {
                $thumb_w_ratio  = $thumb_w / $original_w;
                $thumb_h_resize = (int)round($original_h * $thumb_w_ratio);
            }
            if ($thumb_w_resize < $thumb_w)
            {
                $thumb_h_ratio  = $thumb_w / $thumb_w_resize;
                $thumb_h_resize = (int)round($thumb_h * $thumb_h_ratio);
                $thumb_w_resize = $thumb_w;
            }

            // CREATE THE PROPORTIONAL IMAGE RESOURCE
            $thumb = imagecreatetruecolor($thumb_w_resize, $thumb_h_resize);
            $preto = imagecolorallocate($thumb, 0, 0, 0);
            if (!imagecopyresampled($thumb, $original, 0,0,0,0, $thumb_w_resize, $thumb_h_resize, $original_w, $original_h)) return FALSE;

            // ACTIVATE THIS TO STORE THE INTERMEDIATE IMAGE
            // imagejpeg($thumb, 'RAY_temp_' . $thumb_w_resize . 'x' . $thumb_h_resize . '.jpg', 100);

            // CREATE THE CENTERED CROPPED IMAGE TO THE SPECIFIED DIMENSIONS
            $final = imagecreatetruecolor($thumb_w, $thumb_h);
            $thumb_w_offset = 0;
            $thumb_h_offset = 0;
            if ($thumb_w < $thumb_w_resize)
            {
                $thumb_w_offset = (int)round(($thumb_w_resize - $thumb_w) / 2);
            }
            else
            {
                $thumb_h_offset = (int)round(($thumb_h_resize - $thumb_h) / 2);
            }
          if(!strcmp("png",$file_name[1]) || !strcmp("PNG",$file_name[1])){
            imagealphablending($final , false);
            imagesavealpha($final , true);
            imagecolortransparent ($final, $preto);
            imagecopymerge($original, $final, 0, 0, 0, 0, imagesx($final), imagesy($final), 100);
            imagecopyresized($final,$original,0,0,0,0,$thumb_w_resize,$thumb_h_resize, $original_w, $original_h );
          }else{
               if (!imagecopy($final, $thumb, 0,0, $thumb_w_offset, $thumb_h_offset, $thumb_w_resize, $thumb_h_resize)) return 1; 
          }
            
            // STORE THE FINAL IMAGE - WILL OVERWRITE $thumb_image_url
            
             if(!strcmp("jpg",$file_name[1]) || !strcmp("JPG",$file_name[1]) || !strcmp("jpeg",$file_name[1]) || !strcmp("JPEG",$file_name[1])){
                   if (!@imagejpeg($final, $image_thumb, $quality)) return false;
                        return $image_thumb;
                }
                 if(!strcmp("png",$file_name[1]) || !strcmp("PNG",$file_name[1])){
                    if (!@imagepng($final, $image_thumb, 9)) return false;
                     return $image_thumb;
                }
                if(!strcmp("webp",$file_name[1]) || !strcmp("WPEB",$file_name[1])){
                    if (!@imagewebp($final, $image_thumb, $quality)) return false;
                     return $image_thumb;
                }

            }else{
                 return $image_thumb;
            }
        }
    }
}
if (!function_exists('formatHTMLSafe')) {
    include_once '/home/common/utf8.inc.php';
    function formatHTMLSafe($intext)
    {

        /**
         * This method assumes the source is ISO-8859-1 if it's not valid UTF-8, and converts it
         * Then replaces ALL htmlentities, and undoes any double encodings found
         * Therefore this function is not suitable when double encoding is required, eg. html inside xml
         * But a simple htmlentities should suit for those occasions
         */
        if (utf8ToUnicode($intext) === false) {
            $intext = mb_convert_encoding($intext, 'UTF-8', 'ISO-8859-1');
        }

        return preg_replace('/&amp;([A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/', '&$1', htmlentities($intext, ENT_QUOTES, "UTF-8"));

        /**
         * Pre: 10-07-12
         */
        /*
        $translation_table = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        $translation_table[chr(38) ] = '&';
        return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/", "&amp;", strtr($intext, $translation_table));
         */

        /**
         * Pre: 10-06-30
         */
        /*
    return str_replace('<', '&lt;', str_replace('"', '&quot;', $intext));
     */
    }
}
if (!function_exists('insertLogDataInShoppingCart')) {
    function insertLogDataInShoppingCart()
    {
        $customCookes = 0;
        global $DB,$_GLOBALS;
        // add shoping cart data if user once come on this page.
        if (!array_key_exists('shopping_cart_id', $_COOKIE)) {
            $DB->query(sprintf(
                "INSERT INTO `highwaygroup`.`shopping_carts` (`visitor_id`, `member_id`, `site_id`, `date_added`) VALUES (%d, %d, %d, NOW())",
                $_GLOBALS['visitor']['id'],
                $_GLOBALS['member']['id'],
                $_GLOBALS['site_settings']['site_id']
            ));
            $customCookes = $DB->insert_id();
            custom_setcookie('shopping_cart_id', $_COOKIE['shopping_cart_id'] = $customCookes , strtotime('+1 year 00:00:00'), '/');
        } else {
            $DB->query(sprintf(
                "UPDATE `highwaygroup`.`shopping_carts` SET `last_updated` = NOW() WHERE `shopping_cart_id` = %d LIMIT 1",
                $_GLOBALS['shopping_cart_id']
            ));
        }
        if (0 != $_COOKIE["shopping_cart_id"] || $customCookes > 0) {
            $keys     = array(
                "shopping_cart_id" => (0 != $_COOKIE["shopping_cart_id"]) ? $_COOKIE["shopping_cart_id"] : $customCookes,
                "prod_count"       => 0,
                "product_id"       => 0,
                "sub_id"           => 0,
                "product_qty"      => 0,
                "power_id"         => '',
                "adapter_id"       => '',
                "product_offer"    => 0,
                "offer_currency"   => 0,
            );
            $products = array();
            $_GLOBALS['shopping_cart'] = cartLoadContents($_GLOBALS['member']['id'] ?? 0, $_GLOBALS['visitor']['id'] ?? 0);
            foreach ($_GLOBALS["shopping_cart"] as $k => $v) {
                $productdata = productFromDatabase($v["product_id"]);
                $v['product_offer'] = $productdata['sales_price'];
                 $v['offer_currency'] = $productdata['sales_currency'];
                $v               = array_merge($keys, $v);
                $v["prod_count"] = count($products) + 1;
                $products[]      = "'" . implode("', '", array_intersect_key($v, $keys)) . "'";
            }
            if (count($products)) {
                $DB->query(sprintf(
                    "DELETE FROM `highwaygroup`.`shopping_cart_products` WHERE `shopping_cart_id` = %d",
                   (0 != $_COOKIE["shopping_cart_id"]) ? $_COOKIE["shopping_cart_id"] : $customCookes
                ));
                $DB->query(sprintf(
                    "INSERT INTO `highwaygroup`.`shopping_cart_products` (`%s`) VALUES (%s)",
                    implode("`, `", array_keys($keys)),
                    implode("), (", array_values($products))
                ));
            }
        }
    }
}
if (!function_exists('traceUser')) {
    function traceUser()
    {
        global $DB,$_GLOBALS;
        $currentURL =  "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";;
        // Get last visited URL
        $lastVisitedURL = $_SERVER['HTTP_REFERER'];
        // Get request method
        if(($_GLOBALS['visitor']['id'] > 0 || $_GLOBALS['member']['id']) > 0 && ($currentURL !=  $lastVisitedURL)){
            // Get current URL
           
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            $DB->query(sprintf(
                "INSERT INTO `highwaygroup`.`visitor_trace` (`visitor_id`, `member_id`, `site_id`, `request_uri`, `referer`, `post`,`timestamp`) VALUES (%d, %d, %d, %s, %s, %s, NOW())",
                $_GLOBALS['visitor']['id'],
                $_GLOBALS['member']['id'],
                $_GLOBALS['site_settings']['site_id'],
                $DB->escape($currentURL),
                $DB->escape($lastVisitedURL),
                $DB->escape($requestMethod)
            ));
        }
    }
}
