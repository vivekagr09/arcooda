<?php
use BrowscapPHP\Browscap;

//Reserved cookie: visitor_id
//Reserved tables: cart_members, cart_visitors
function cartAddToContents($cart_content, $cart_item, $product_qty = 1, $used = "n")
{
    $cart_item["product_qty"] = $product_qty;
    $cart_item["used"]        = $used;
    $cart_content[]           = $cart_item;
    return cartCleanContents($cart_content, 2);
}
function cartRemoveProduct($cart_content, $cart_key)
{
    unset($cart_content[$cart_key]);
    return cartCleanContents($cart_content);
}
function cartEmptyContents($cart_content = array())
{
    return array();
}
function cartAdjustContents($cart_content, $cart_key, $product_qty = 1)
{
    if (isset($cart_content[$cart_key])) {
        $cart_content[$cart_key]["product_qty"] = $product_qty;
    }

    return cartCleanContents($cart_content);
}
function cartCombineContents($cart_content1, $cart_content2)
{
    return cartCleanContents(array_merge($cart_content1, $cart_content2));
}
function cartCleanContents($cart_content, $cid = 1)
{
    //Trim all the product
    $new_cart = array();
    foreach ($cart_content as $key => $cart_item) {
        $new_cart[] = cartTrimProduct($cart_item);
    }
    $cart_content = $new_cart;
    //Add up all the duplicates
    $new_cart = array();
    foreach ($cart_content as $row1) {
        foreach ($new_cart as $key2 => $row2) {
            if (cartProductKey($row1) == cartProductKey($row2)) {
                $new_cart[$key2]["product_qty"] += $row1["product_qty"];
                continue (2);
            }
        }
        $new_cart[] = $row1;
    }
    $cart_content = $new_cart;
    //Re-Key
    $new_cart = array();
    foreach ($cart_content as $cart_item) {
        if ($cart_item["product_qty"] > 0) {
            $new_cart[cartProductKey($cart_item)] = cartTrimProduct($cart_item);
        }
    }

    $cart_content = $new_cart;
    return $cart_content;
}
function cartSummarize($cart_content)
{
    $result = array();
    if (is_array($cart_content)) {
        foreach ($cart_content as $key => $item) {
            $result[$key]        = cartTrimProduct($item);
            $result[$key]["qty"] = $result[$key]["product_qty"];
        }
    }

    return $result;
}

/**
 * @param int    $member_id
 * @param int    $visitor_id
 * @param string $type
 *
 * @return mixed[][] cart contents
 */
function cartLoadContents($member_id, $visitor_id = 0, $type='shopping')
{
    global $DB;

    $result = array();
    if ($member_id > 0) {
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d AND `cart_type` = %s",
            "`cart_members`",
            "member_id",
            $member_id,
            $DB->escape($type)
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[cartProductKey($row)] = cartTrimProduct($row);
            }
            $DB->free_result($query);
        }
    } else {
        if (0 == $visitor_id) {
            $visitor_id = cartGetVisitorID();
        }
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d AND `cart_type` = %s",
            "`cart_visitors`",
            "visitor_id",
            $visitor_id,
            $DB->escape($type)
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[cartProductKey($row)] = cartTrimProduct($row);
            }
            $DB->free_result($query);
        }
    }
    return cartCleanContents($result);
}

/**
 * @param int       $member_id
 * @param mixed[][] $cart_content
 *
 * @return bool true when successful, false if not
 */
function cartSaveContents($member_id, $cart_content)
{
    global $DB;
    $inserts     = array();
    if (0 != $member_id) {
        if (is_array($cart_content)) {
            foreach ($cart_content as $v) {
                $inserts[] = sprintf(
                    "(%d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %s, %d,%d,%s,%d,%s)",
                    $member_id,
                    $v["product_id"],
                    $v["sub_id"],
                    $v["product_qty"],
                    $v["back_qty"],
                    $v["power_id"],
                    $v["adapter_id"],
                    ($v["product_offer_us"])?$DB->escape($v["product_offer"]):0,
                    ($v["product_offer_us"])?$DB->escape($v["product_offer_us"]):0,
                    $DB->escape($v["offer_currency"]),
                    $DB->escape($v["used"]),
                    $v["colour_id"],
                    $v["sub_product_id"],
                    $DB->escape($v["is_free"]),
                    $v["free_with_product"],
                    $DB->escape($v["cart_type"]),
                );
            }
        }
        //TODO: Make use of `id` and `deleted_at`
        $DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d",
            "`cart_members`",
            "member_id",
            $member_id
        ));
        if (count($inserts)) {
            $DB->query(sprintf(
                "INSERT INTO %s (`%s`, `product_id`, `sub_id`, `product_qty`, `back_qty`, `power_id`, `adapter_id`, `product_offer`, `product_offer_us`, `offer_currency`, `used`,`colour_id`,`sub_product_id`,`is_free`,`free_with_product`,`cart_type`) VALUES %s",
                "`cart_members`",
                "member_id",
                implode(", ", $inserts)
            ));
        }
        $DB->query(sprintf(
            "UPDATE `highwaygroup`.`new_members` SET `packing_string` = '' WHERE `member_id` = %d LIMIT 1",
            $member_id
        ));
    } elseif (($visitor_id = cartGetVisitorID()) != 0) {
        if (is_array($cart_content)) {
            foreach ($cart_content as $v) {
                $inserts[] = sprintf(
                    "(%d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %s,%d,%d,%s,%d,%s)",
                    $visitor_id,
                    $v["product_id"],
                    $v["sub_id"],
                    $v["product_qty"],
                    $v["back_qty"],
                    $v["power_id"],
                    $v["adapter_id"],
                    ($v["product_offer_us"])?$DB->escape($v["product_offer"]):0,
                    ($v["product_offer_us"])?$DB->escape($v["product_offer_us"]):0,
                    $DB->escape($v["offer_currency"]),
                    $DB->escape($v["used"]),
                    $v["colour_id"],
                    $v["sub_product_id"],
                    $DB->escape($v["is_free"]),
                    $v["free_with_product"],
                    $DB->escape($v["cart_type"]),
                );
            }
        }

        //TODO: Make use of `id` and `deleted_at`
        $DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d",
            "`cart_visitors`",
            "visitor_id",
            $visitor_id
        ));
        if (count($inserts)) {
            $DB->query(sprintf(
                "INSERT INTO %s (`%s`, `product_id`, `sub_id`, `product_qty`, `back_qty`, `power_id`, `adapter_id`, `product_offer`, `product_offer_us`, `offer_currency`, `used`,`colour_id`,`sub_product_id`,`is_free`,`free_with_product`,`cart_type`) VALUES %s",
                "`cart_visitors`",
                "visitor_id",
                implode(", ", $inserts)
            ));
        }
        $DB->query(sprintf(
            "UPDATE `highwaygroup`.`visitors` SET `packing_string` = '' WHERE `visitor_id` = %d LIMIT 1",
            $visitor_id
        ));
    }

    return true;
}
function cartTrimProduct($cart_item)
{
    $valid_keys = array("adapter_id", "back_qty", "dependant_id", "dependant_sub_id", "generic", "generic_code", "is_gift", "offer_currency", "power_id", "product_id", "product_offer", "product_offer_au", "product_qty", "section", "site_type", "sub_id", "used", "colour_id","sub_product_id","is_free","free_with_product",'allow_invoice','cart_type','order_rule');
    foreach ($cart_item as $k => $v) {
        if (!in_array($k, $valid_keys)) {
            unset($cart_item[$k]);
        }
    }

    foreach ($valid_keys as $k) {
        if (isset($cart_item[$k])) {
            $cart_item[$k] = trim($cart_item[$k] . "");
        }
    }

    if (isset($cart_item["used"]) && "" == $cart_item["used"]) {
        $cart_item["used"] = "n";
    }
    if (isset($cart_item["generic"]) && "" == $cart_item["generic"]) {
        $cart_item["generic"] = "n";
    }

    if (isset($cart_item["site_type"]) && "" == $cart_item["site_type"]) {
        $cart_item["site_type"] = "int";
    }

    if (isset($cart_item["is_gift"]) && "" == $cart_item["is_gift"]) {
        $cart_item["is_gift"] = "n";
    }
    if (!isset($cart_item["colour_id"])) {
        $cart_item["colour_id"] = 0;
    }

    if (!isset($cart_item["sub_product_id"])) {
        $cart_item["sub_product_id"] = 0;
    }
     if (!isset($cart_item["is_free"])) {
        $cart_item["is_free"] = 'n';
    }
     if (!isset($cart_item["free_with_product"])) {
        $cart_item["free_with_product"] = 0;
    }
    //$cart_item["cart_type"]='shopping';
    if ((isset($cart_item["order_rule"]) && $cart_item["order_rule"]=='inquiry_for_everyone') || $cart_item["cart_type"]=='quote') {
        $cart_item["cart_type"]='quote';
    }else{
        $cart_item["cart_type"]='shopping';
    }
    ksort($cart_item);
    return $cart_item;
}
function cartProductKey($cart_item)
{
    
   $KeyArr=array();

   $KeyArr['cart_type']=$cart_item["cart_type"];
   $KeyArr['product_id']=$cart_item["product_id"];
   $KeyArr['colour_id']=$cart_item["colour_id"];
    $cart_item = cartTrimProduct($cart_item);
    unset($cart_item["back_qty"]);
    unset($cart_item["product_offer"]);
    unset($cart_item["product_offer_us"]);
    unset($cart_item["offer_currency"]);
    unset($cart_item["section"]);
    unset($cart_item["order_rule"]); 

  /*
   echo '<pre>';
   print_r($KeyArr);
   echo md5(implode(" ", $KeyArr));
   echo '</pre>';
   */

    return md5(implode(" ", $KeyArr));
}
function cartGetVisitorID($new_currency = "USD")
{
    global $_GLOBALS;

    if ($_GLOBALS['visitor']['visitor_id'] ?? 0) {
        return $_GLOBALS['visitor']['visitor_id'];
    }
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'){
            return 0;    
    }
    static $cache = null;
    if (!is_null($cache)) {
        return $cache;
    }

    global $DB;
    if (empty($new_currency)) {
        $new_currency = "USD";
    }

    $visitor_id = $_COOKIE["visitor_id"] ?? '';
    $rand_num   = hexdec(substr($visitor_id, -8));
    $visitor_id = hexdec(substr($visitor_id, 0, -8));

    $affected = 0;
    if (0 != $visitor_id) {
        $DB->query(sprintf(
            "UPDATE `highwaygroup`.`visitors` SET `hit_count` = `hit_count` + 1 WHERE `visitor_id` = %d AND `rand_num` = %d LIMIT 1",
            $visitor_id,
            $rand_num
        ));
        $affected = $DB->affected_rows();
        if (0 != $affected) {
            $cache = $visitor_id;
            return $visitor_id;
        }
    }

    foreach (array(
        "MnoGoSearch/", "bingbot/", "Googlebot", "Baiduspider", "UptimeRobot/", "MJ12bot/", "YandexBot/", "008/", "AhrefsBot", "Ezooms/", "FatBot 2.0", "YodaoBot", "msnbot", "Yahoo! Slurp", "WBSearchBot/", "ShopWiki/", "www.webwombat.com.au", "Sogou web spider/", "Sosospider+", "Seznam", "facebookexternalhit", "Sphider", "magpie-crawler", "discobot/", "Mail.RU/", "oBot/", "Aboundex/", "Acoon", "JikeSpider", "BPImageWalker", "Outlook-Express/", "ichiro/", "Hailoobot/", "SWEBot/", "KaloogaBot", "Comodo-Certificates-Spider", "FlightDeckReportsBot/", "MLBot", "discobot/", "Feedage/", "KomodiaBot/", "seexie.com_bot/", "WangIDSpider/", "SurveyBot/", "CukBot", "New-Sogou-Spider/", "SkimBot/", "Sogou+web+robot", "Sosoimagespider", "Twitterbot/", "SemrushBot/", "EdisterBot", "360Spider", "Sosospider/", "discoverybot/", "www.integromedb.org/Crawler", "BLEXBot", "BingPreview/", "Mail.RU_Bot/",
    ) as $v) {
        if (strpos($_SERVER["HTTP_USER_AGENT"], $v) !== false) {
            return 0;
        }
    }
    if (in_array($_SERVER["REMOTE_ADDR"], array("4.79.204.36", "190.42.105.248", "217.69.133.28", "95.211.131.54", "150.70.172.101", "183.178.85.181", "1.202.218.8", "64.124.98.10", "166.137.139.3", "91.140.153.68", "181.64.59.30"))) {
        return 0;
    }

    if ("HEAD" == $_SERVER["REQUEST_METHOD"]) {
        return 0;
    }

    if ("" != $_SERVER["HTTP_USER_AGENT"]) {
        $temp = @get_browser($_SERVER["HTTP_USER_AGENT"], true);
        if ($temp && 1 == ($temp['crawler'] ?? 0)) {
            return $cache = 0;
        }
        //if ($temp && $temp->cookies == 0) return $cache = 0;
    }

    /**
     * Simple cookie enable check - Don't waste processing time on visitors that don't track
     */
    if (!count($_COOKIE)) {
        custom_setcookie('has_cookies_enabled', '1', mktime(0, 0, 0, date("n"), date("j"), date("Y") + 1), "/");
        return $cache = 0;
    }
    if (array_key_exists('has_cookies_enabled', $_COOKIE)) {
        custom_setcookie('has_cookies_enabled', '', time() - 3600, '/');
    }
    $detected                 = [];
    $detected['ip']           = getClientIP();
    $detected['country_info'] = getCountryInfo($detected['ip']);
    $detected['country']      = $detected['country_info']['a2'] ?? '--';
    $detected['user_agent']   = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $browscap_resources = __DIR__ . '/../vendor/browscap/browscap-php/resources';
    if (false && class_exists('Browscap')) {
        $bc = new Browscap(
            new SimpleCacheAdapter(
                new FilesystemCache($browscap_resources)
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
    $rand_num = mt_rand(1, 2147483647);
    $DB->query(sprintf(
        "INSERT INTO `highwaygroup`.`visitors` (`rand_num`, `login_ip`, `currency`, `country`, `user_agent`,`site_id`) VALUES (%d, %s, %s, %s, %s,%s)",
        $rand_num,
        $DB->escape($detected['ip']),
        $DB->escape($new_currency),
        $DB->escape(('--' === $detected['country']) ? null : $detected['country']),
        $DB->escape($detected['user_agent']),
        $DB->escape($_GLOBALS['site_settings']['site_id'])
    ));
    $visitor_id = $DB->insert_id();

    $visitor_cookie = sprintf("%08x%08x", $visitor_id, $rand_num);
    custom_setcookie("visitor_id", $visitor_cookie, mktime(0, 0, 0, date("n"), date("j"), date("Y") + 1), "/");
    $_COOKIE["visitor_id"] = $visitor_cookie;

    $cache = $visitor_id;

    return $visitor_id;
}

//@todo These belong in a visitor module
if (!function_exists('visitorDefault')) {
    function visitorDefault()
    {
        global $DB;

        return visitorVirtual($DB->modelDefault(
            "`highwaygroup`.`visitors`"
        ));
    }
}
if (!function_exists('visitorVirtual')) {
    function visitorVirtual($row)
    {
        $row['id'] = $row['visitor_id'] = $row['visitor_id'];

        return $row;
    }
}
if (!function_exists('visitorFromDatabase')) {
    /**
     * @param int $visitor_id
     *
     * @return mixed[]
     */
    function visitorFromDatabase($visitor_id = 0)
    {
        global $DB;

        if (0 == $visitor_id) {
            $visitor_id = cartGetVisitorID();
        }

        if ($row = $DB->modelLoad(
            $visitor_id,
            "`highwaygroup`.`visitors`",
            "visitor_id"
        )) {
            return visitorVirtual($row);
        }

        return visitorDefault();
    }
}
if (!function_exists('visitorIncrementHits')) {
    /**
     * @param int $visitor_id
     *
     * @return int
     */
    function visitorIncrementHits($visitor_id = 0) {
        global $DB;

        if (0 == $visitor_id) {
            $visitor_id = cartGetVisitorID();
        }

        if ($DB->query(sprintf(
            "UPDATE %s SET `hit_count` = `hit_count` + 1, `last_visit_date` = NOW() WHERE `%s` = %d LIMIT 1",
            "`highwaygroup`.`visitors`",
            "visitor_id",
            $visitor_id
        ))) {
            return $DB->affected_rows;
        }

        return 0;
    }
}
if (!function_exists('visitorSetValue')) {
    /**
     * @param int    $visitor_id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function visitorSetValue($visitor_id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $visitor_id,
            $field,
            $value,
            "`highwaygroup`.`visitors`",
            "visitor_id"
        );
    }
}
if (!function_exists('visitorGetValue')) {
    function visitorGetValue($visitor_id, $field)
    {
        if ($row = visitorFromDatabase($visitor_id)) {
            return $row[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('visitorHasValue')) {
    function visitorHasValue($visitor_id, $field, $value)
    {
        return (visitorGetValue($visitor_id, $field) == $value);
    }
}

function cartAddToShopping($cart_contents, $member_id, $visitor_id, $shopping_cart_id = "")
{
    return 0;
    global $DB, $_GLOBALS;
    if (!$shopping_cart_id) {
        if (!$visitor_id) {
            return 0;
        }

        $DB->query(sprintf(
            "INSERT INTO `shopping_carts` (`visitor_id`, `member_id`, `date_added`) VALUES (%d, %d, NOW())",
            $visitor_id,
            $member_id
        ));
        $shopping_cart_id = $DB->insert_id();
    } else {
        $DB->query(sprintf(
            "UPDATE `shopping_carts` SET `member_id` = %d, `last_updated` = NOW() WHERE `shopping_cart_id` = %d LIMIT 1",
            $member_id,
            $shopping_cart_id
        ));
    }

    if (is_array($cart_contents)) {
        $inserts = array();
        foreach ($cart_contents as $v) {
            $inserts[] = sprintf("(%d, %d, %d, %d, %d, %d, %s, %s)", $shopping_cart_id, $v["product_id"], $v["sub_id"], $v["product_qty"], $v["power_id"], $v["adapter_id"], $DB->escape($v["product_offer"]), $DB->escape($v["offer_currency"]));
        }

        $DB->query(sprintf("DELETE FROM `shopping_cart_products` WHERE `shopping_cart_id` = %d", $shopping_cart_id));
        if (count($inserts)) {
            $DB->query("INSERT INTO `shopping_cart_products` (`shopping_cart_id`, `product_id`, `sub_id`, `product_qty`, `power_id`, `adapter_id`, `product_offer`, `offer_currency`) VALUES " . implode(", ", $inserts));
        }

    }
    return $shopping_cart_id;
}
function updateShoppingCartPayment($shopping_cart_id, $payment_made = true, $rejected = false)
{
    return;
    global $DB;
    $field1 = (($payment_made) ? "made" : (($rejected) ? "rejected" : ""));
    $field2 = (($payment_made) ? "payment" : (($rejected) ? "rejected" : ""));
    if (0 == $shopping_cart_id || "" == $field1 || "" == $field2) {
        return;
    }

    $DB->query(sprintf(
        "UPDATE `shopping_carts` SET `payment_%s` = 'y', `%s_date` = NOW() WHERE `shopping_cart_id` = %d LIMIT 1",
        $field1,
        $field2,
        $shopping_cart_id
    ));
}
function updateShoppingCartStage($shopping_cart_id, $stage)
{
    global $DB;
    if (0 == $shopping_cart_id) {
        return;
    }

    $DB->query(sprintf("UPDATE `highwaygroup`.`shopping_carts` SET `stage` = %d WHERE `shopping_cart_id` = %d LIMIT 1", $stage, $shopping_cart_id));
}
function updateShoppingCartInvoice($shopping_cart_id, $invoice_id)
{
    global $DB;
    if (0 == $shopping_cart_id) {
        return;
    }

    $DB->query(sprintf("UPDATE `highwaygroup`.`shopping_carts` SET `invoice_id` = %d WHERE shopping_cart_id = %d LIMIT 1", $invoice_id, $shopping_cart_id));
}
function updateShoppingCartStatus($shopping_cart_id, $status)
{
    global $DB,$_GLOBALS;
    $allowed_status = array("open", "ordered", "saved", "closed");
    if (0 == $shopping_cart_id || !in_array($status, $allowed_status)) {
        return;
    }
    $member_id = $_GLOBALS['member']['member_id'] ?? 0;
    $DB->query(sprintf("UPDATE `highwaygroup`.`shopping_carts` SET  `member_id` = $member_id, `status` = %s %s WHERE `shopping_cart_id` = %d LIMIT 1", $DB->escape($status), ("ordered" == $status) ? ", `date_ordered` = NOW()" : "", $shopping_cart_id));
}
function cartUpdateContents($cart_content, $member_id = 0)
{
    moduleLoad('product'); //productFromDatabase

    foreach ($cart_content as $key => $cart_item) {
        //if ($cart_item["generic"] == "n" || $cart_item["generic"] == "")
        {
            $product                                = productFromDatabase($cart_item["product_id"]);
            $cart_content[$key]["product_offer"]    = convertCurrency($product["sales_price"], $product["sales_currency"], "USD");
            $cart_content[$key]["product_offer_us"] = $cart_content[$key]["product_offer"];
        }
    }
    return $cart_content;
}

if (!function_exists('applyvisitorcoupon')) {
    /**
     * @param int $visitor_id
     *
     * @return int
     */
    function applyvisitorcoupon($visitor_id = 0,$couponcode) {
        global $DB;

        if (0 == $visitor_id) {
            $visitor_id = cartGetVisitorID();
        }

        if ($DB->query(sprintf(
            "UPDATE %s SET `%s` = '%s' , `last_visit_date` = NOW() WHERE `%s` = %d LIMIT 1",
            "`highwaygroup`.`visitors`",
            "discount_code",
            $couponcode,
            "visitor_id",
            $visitor_id
        ))) {
            return $DB->affected_rows;
        }

        return 0;
    }
}

if (!function_exists('removevisitorcoupon')) {
    /**
     * @param int $visitor_id
     *
     * @return int
     */
    function removevisitorcoupon($visitor_id = 0,$couponcode) {
        global $DB;

        if (0 == $visitor_id) {
            $visitor_id = cartGetVisitorID();
        }
        
        if ($DB->query(sprintf(
            "UPDATE %s SET `%s` = '%s' , `last_visit_date` = NOW() WHERE `%s` = %d LIMIT 1",
            "`highwaygroup`.`visitors`",
            "discount_code",
            $couponcode,
            "visitor_id",
            $visitor_id
        ))) {
            return $DB->affected_rows;
        }

        return 0;
    }
}
