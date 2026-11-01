<?php
/**
 * Highway Entertainment Newsletter Template
 *
 * @var mixed[] $item
 * @var mixed[][] $item_settings
 */
?>
<style type="text/css">
    iframe{
        width: 100%!important;
    }
</style>
<?php
global $_GLOBALS;
global $DB;

/**
 * Re-organise the settings for the template
 */
$style_id = 5;
include "common_" . $style_id . ".inc.php";
$domain_name = "www.arcooda.com";
$base_url = "https://www.arcooda.com/";
$track_url = "https://www.arcooda.com[TRACKER_KEY]";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" lang="en" xmlns="https://www.w3.org/1999/xhtml">
<head>
<title><?= htmlspecialchars($item["subject"]) ?></title>
<meta name="viewport" content="width=750px" />
<style>
    table tr td p {
        font-size: 12px !important;
    }
    #negotiation_menu {
        display:none !important;
    }
    /* .tableListing tr td {
        display: inline-block;
        width: 50%;
        box-sizing: border-box;
    } */
</style>
</head>
<body style="font:12px arial, helvetica, clean, sans-serif;width:750px;margin:auto" alink="#006699" link="#006699" text="#000000" vlink="#006699">

<br />

<?php
if (
    "" != $item_settings["paragraph"]["value"] &&
    formatCleanString($item_settings["paragraph"]["value"]) !=
        formatCleanString($sections["paragraph"]["defaults"]["value"])
) { ?>
<div style="position:relative;margin:4px 4px 0">
    <h1 style="margin:0"><a href="<?= htmlspecialchars(
     relative_url(
         "/newsletters/" .
             formatURLSafe($item["subject"]) .
             "_" .
             $item["id"] .
             ".html",
         $track_url
     )
 ) ?>"><img src="<?= htmlspecialchars(
    relative_url("/brand.png", $track_url)
) ?>" alt="Arcooda Newsletter" border="0" style="max-width: 230px;"/></a></h1>
    <i style="font-size:10px;position:absolute;top:12px;right:18px;color:#999"><?= date(
     "jS - F - Y",
     $item["time_added"]
 ) ?></i>
</div>
<p style="padding:16px;margin:0 4px 4px;font-size:12px">
    <?= formatHTMLDescription($item_settings["paragraph"]["value"]) ?>
</p>
<?php } else { ?>
<div style="position:relative;margin:4px">
    <h1 style="margin:0"><a href="<?= htmlspecialchars(
     relative_url(
         "/newsletters/" .
             formatURLSafe($item["subject"]) .
             "_" .
             $item["id"] .
             ".html",
         $track_url
     )
 ) ?>"><img src="<?= htmlspecialchars(
    relative_url("/brand.png", $track_url)
) ?>" alt="Arcooda Newsletter" border="0" style="max-width: 230px;"/></a></h1>
    <i style="font-size:11px;position:absolute;top:12px;right:18px;color:#999"><?= date(
     "jS - F - Y",
     $item["time_added"]
 ) ?></i>
</div>
<?php }

$packets = [];
foreach ($item_settings as $k => $settings) {
    if ("y" == $settings["enable"]) {
        switch ($k) {
            case "disclaimer":
                if (trim($settings["value"]) != "") {
                    ob_start(); ?>
<div style="border:2px solid #8ac">
    <p style="text-align:center;font-size:11px;font-weight:bold;color:#069"><?= formatHTMLDescription(
     $settings["value"]
 ) ?></p>
</div>
<?php $packets[] = ["size" => "wide", "content" => ob_get_clean()];
                }
                break;

            case "top_banner":
            case "large_banner":
            case "bottom_banner":
                if (is_array($settings["items"]) && moduleLoad("banner")) {
                    foreach ($settings["items"] as $v) {
                        if ("" != $v["str"]) {

                            $id = $v["str"];
                            ob_start();
                            $static_path =
                                $_GLOBALS["site_settings"]["static_path"];
                            $_GLOBALS["site_settings"]["static_path"] = "";
                            $temp = bannerFromDatabase($id);
                            $_GLOBALS["site_settings"][
                                "static_path"
                            ] = $static_path;
                            if ("" == $v["name"]) {
                                $v["name"] = $temp["alt"];
                            }

                            $v["view_url"] = relative_url(
                                $temp["click_url"],
                                $track_url
                            );
                            $v["image_url"] = relative_url(
                                str_replace(
                                    ".jpg",
                                    ".jpg",
                                    $temp["image_url"]
                                ),
                                $base_url
                            );
                            ?>
<div style="padding:4px 0;margin:0;text-align:center">
    <a href="<?= htmlspecialchars(
     $v["view_url"]
 ) ?>"><img title="<?= htmlspecialchars(
    $v["name"]
) ?>" src="<?= htmlspecialchars($v["image_url"]) ?>" alt="<?= htmlspecialchars(
    $v["name"]
) ?>" border="0" style="width:100%;"/></a>
</div>
<?php $packets[] = [
    "size" => "wide" == $settings["option"] ? "wide" : "thin",
    "content" => ob_get_clean(),
];
                        }
                    }
                }

                break;

            case "news":
                ob_start(); ?>
    <h2 style="color:#fff;background:#158 url(<?= htmlspecialchars(
     relative_url("/images/newsletter_" . $style_id . "/htiles.png", $base_url)
 ) ?>) repeat-x 0px 0px;font-size:21px;margin:0;padding:11px 15px 12px;font-weight:normal;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px"><?= htmlspecialchars(
    $sections[$k]["title"]
) ?></h2>
<?php
$first = true;
moduleLoad("news"); //newsFromDatabase
if (is_array($settings["items"])) {
    foreach ($settings["items"] as $v) {
        if ("" != $v["str"]) {
            $id = $v["str"];
            $temp = newsFromDatabase($id);
            if ("" == $v["name"]) {
                $v["name"] = $temp["news_title"];
            }

            if ("brief" == $settings["option"]) {

                if ("" == $v["text"]) {
                    $v["text"] = trim($temp["short_description"]);
                }

                $v["view_url"] = sprintf(
                    "/n/%s-%d.html",
                    urlencode(
                        trim(
                            preg_replace("/[^A-Za-z0-9]+/", "-", $v["name"]),
                            "-"
                        )
                    ),
                    $id
                );

                if (function_exists("newsImagesFromDatabase")) {
                    foreach (newsImagesFromDatabase($id) as $img) {
                        if ("y" == $img["default_image"]) {
                            $newsimage = [
                                "src" => $img["src"],
                                "alt" => $img["caption"],
                            ];
                        }
                    }
                }
                $v["image_url"] = relative_url(
                                str_replace(
                                    ".jpg",
                                    ".jpg",
                                   $newsimage["src"]
                                ),
                                $base_url
                            ).'?size=250';
                ?>
    <table style="border-collapse:collapse<?= !$first
     ? ";border-top:1px solid #ccc"
     : "" ?>" width="100%" cellpadding="8"><tr>
        <td valign="top"><a href="<?= htmlspecialchars(
      $v["view_url"]
  ) ?>"><img src="<?= htmlspecialchars(
    $v["image_url"]
) ?>" alt="<?= htmlspecialchars(
    $newsimage["alt"]
) ?>" title="<?= htmlspecialchars(
    $v["name"]
) ?>" border="0" width="250px;"/></a></td>
        <td>
            <h3 style="margin:0.5em 0;font-size:15px"><a href="<?= htmlspecialchars(
       $v["view_url"]
   ) ?>" style="text-decoration:none;"><?= htmlspecialchars(
    $v["name"]
) ?></a></h3>
            <p style="font-size:12px"><?= formatHTMLDescription($v["text"]) ?></p>
            <a style="float:right;font-weight:normal" href="<?= htmlspecialchars(
       $v["view_url"]
   ) ?>">More &raquo;</a>
        </td>
    </tr></table>
<?php
            } else {

                if ("" == $v["text"]) {
                    $v["text"] = trim($temp["long_description"]);
                }

                if (function_exists("newsImagesFromDatabase")) {
                    foreach (newsImagesFromDatabase($id) as $img) {
                        if ("y" == $img["default_image"]) {
                            $newsimage = [
                                "src" => $img["src"],
                                "alt" => $img["caption"],
                            ];
                        }
                    }
                }
                $v["image_url"] = $newsimage["src"];
                ?>
    <table style="border-collapse:collapse<?= !$first
     ? ";border-top:1px solid #ccc"
     : "" ?>" width="100%" cellpadding="8"><tr><td valign="top">
        <a href="<?= htmlspecialchars(
      $v["view_url"]
  ) ?>"><img src="<?= htmlspecialchars(
    $v["image_url"]
) ?>" alt="<?= htmlspecialchars($v["name"]) ?>" title="<?= htmlspecialchars(
    $v["name"]
) ?>" border="0" align="left" style="margin:0 22px 22px 0" width="300px;"/></a>
        <h3 style="margin:0.5em 0;font-size:15px"><a href="<?= htmlspecialchars(
      $v["view_url"]
  ) ?>" style="text-decoration:none"><?= htmlspecialchars(
    $v["name"]
) ?></a></h3>
        <p style="font-size:12px"><?= formatHTMLDescription($v["text"]) ?></p>
        <hr clear="left" color="#cccccc" />
        <a style="float:right;text-decoration:none;font-size:11px;font-weight:normal" href="<?= htmlspecialchars(
      relative_url("/news.html", $track_url)
  ) ?>"><i style="color:#999">More News Stories &raquo;</i></a>
    </td></tr></table>
<?php
            }
            $first = false;
        }
    }
}

$packets[] = [
    "size" => "brief" == $settings["option"] ? "thin" : "wide",
    "content" => ob_get_clean(),
];
break;


            case "promotion":
                if (!moduleLoad("promotion")) {
                    break;
                }

                $promotion_handler = new Promotion_Handler();
                if (is_array($temp = $settings["items"])) {
                    foreach ($temp as $k2 => $v) {
                        if ("" == $v["str"]) {
                            unset($temp[$k2]);
                        }
                    }
                }

                if (is_array($temp) && count($temp)) {
                    foreach ($temp as $master_v) {

                        ob_start();
                        $temp = $promotion_handler->fromDatabase(
                            $master_v["str"]
                        );
                        if ("" == $master_v["name"]) {
                            $master_v["name"] = $temp["name"];
                        }

                        if ("" == $master_v["text"]) {
                            $master_v["text"] = trim($temp["description"]);
                        }

                        $master_v["view_url"] = relative_url(
                            $temp["promotion_url"],
                            $track_url
                        );
                        $master_v["image_url"] = relative_url(
                            $temp["thumb_url"],
                            $base_url
                        );

                        $promotion_products = [];
                        foreach (
                            $promotion_handler->productsFromDatabase(
                                $master_v["str"]
                            )
                            as $v
                        ) {
                            $promotion_products[] = [
                                "str" => $v["product_str"],
                            ];
                        }
                        ?>
    <h2 style="color:#fff;background:#158 url(<?= htmlspecialchars(
     relative_url("/images/newsletter_" . $style_id . "/htiles.png", $base_url)
 ) ?>) repeat-x 0px 0px;font-size:21px;margin:0;padding:11px 15px 12px;font-weight:normal;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px"><?= htmlspecialchars(
    $master_v["name"]
) ?></h2>
    <div style="border-bottom:1px solid #ccc;padding:8px;">
        <p style="padding:16px;margin:0 4px 4px;font-size:12px;"><?= formatHTMLDescription(
      $master_v["text"]
  ) ?></p>
        <div style="text-align:right;font-size:9px;clear:both;">sale ends <?= date(
      "d/m/y",
      $temp["time_end"]
  ) ?></div>
    </div>
<?php
switch ($settings["option"]) {
    case "summary":
        if (is_array($temp = $promotion_products)) {
            foreach ($temp as $k2 => $v) {
                if ("" == $v["str"]) {
                    unset($temp[$k2]);
                }
            }
        }

        if (is_array($temp)) {
            foreach (array_chunk($temp, 3) as $first1 => $row) {
                if ($first1) {
                    echo dividing_bar();
                } ?>
<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php
$cell_width = 740 / count($row) - 17;
$name_height = 0;
$text_height = 0;
foreach ($row as $v) {
    if ("bulk_offers" == $k) {
        $temp = fetchBulk($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["title"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }
    } else {
        $temp = fetchProduct($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["product_name"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }
    }
    $name_height = max(
        $name_height,
        estimate_p_height(15, $v["name"], $cell_width, true)
    );
    $text_height = max(
        $text_height,
        estimate_p_height(12, $v["text"], $cell_width)
    );
}

foreach ($row as $first => $v) {
    if ("bulk_offers" == $k) {
        $temp = fetchBulk($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["title"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }

        $price_au = fetchBulkPricing($v["str"], "AUD");
        $price_aup = fetchBulkPricing($v["str"], "AUD+");
        /*
$price_us = fetchBulkPricing($v['str'], 'USD');
 */

        $v["view_url"] = relative_url($temp["bulk_url"], $track_url);
        $v["image_url"] = relative_url($temp["image_180_url"], $base_url);
        $v["purchase_url"] = relative_url(
            "/bulk_offers/addtocart_" . $temp["id"] . ".html",
            $track_url
        );
    } else {
        $temp = fetchProduct($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["product_name"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }

        $price_au = fetchProductPricing($v["str"], "AUD");
        $price_aup = fetchProductPricing($v["str"], "AUD+");
        /*
$price_us = fetchProductPricing($v['str'], 'USD');
 */

        $v["view_url"] = relative_url($temp["product_url"], $track_url);
        $v["image_url"] = relative_url($temp["image_180_url"], $base_url);
        $v["purchase_url"] = relative_url(
            "/checkout/addtocart_" .
                $temp["id"] .
                "_" .
                $temp["sub_id"] .
                ".html",
            $track_url
        );
    } ?>
    <td<?= $first
     ? ' style="border-left:1px solid #ccc"'
     : "" ?> width="33%" valign="top"><table style="border-collapse:collapse;font-size:12px" width="100%" cellpadding="8">
        <tr><td style="text-align:center">
            <h3 style="margin:0.5em 0;font-size:15px"><a href="<?= htmlspecialchars(
       $v["view_url"]
   ) ?>" style="text-decoration:none;"><?= htmlspecialchars(
    $v["name"]
) ?></a></h3>
<?php
$est_height = estimate_p_height(15, $v["name"], $cell_width, true);
if ($est_height < $name_height) { ?>
            <div style="height:<?= intval(
       $name_height - $est_height
   ) ?>px;padding:0;margin:0"></div>
<?php }
?>
            <a href="<?= htmlspecialchars(
       $v["view_url"]
   ) ?>"><img title="<?= htmlspecialchars(
    $v["name"]
) ?>" src="<?= htmlspecialchars($v["image_url"]) ?>" alt="<?= htmlspecialchars(
    $v["name"]
) ?>" border="0" style="margin:12px 0" /></a>
            <p style="font-size:12px;text-align:left"><?= formatHTMLDescription(
       $v["text"]
   ) ?></p>
<?php
$est_height = estimate_p_height(12, $v["text"], $cell_width);
if ($est_height < $text_height) { ?>
            <div style="height:<?= intval(
       $text_height - $est_height
   ) ?>px;padding:0;margin:0"></div>
<?php }
if ("y" == $temp["price_online"] && $temp["saving"] >= 1.0) { ?>
<negotiate_list field="country">
<negotiate_item default="default">
                <?= dont_pay_text("$" . convertCurrency($price_au["normal_price"],"AUD","USD") . " USD") ?>
</negotiate_item>
<negotiate_item value="AU">
                <?= dont_pay_text("$" . $price_aup["normal_price"] . " AUD inc. GST") ?>
</negotiate_item>
</negotiate_list>
                <b><i style="color:#c00">Save <?= $temp["saving"] ?>%</i></b><br /><br />
<?php }
if ("y" == $temp["price_online"]) { ?>
<negotiate_list field="country">
<negotiate_item default="default">
                <?= small_buy_button($v["purchase_url"], convertCurrency($price_au["current_price"],"AUD","USD"), "USD") ?>
</negotiate_item>
<negotiate_item value="AU">
                <?= small_buy_button($v["purchase_url"], $price_aup["current_price"], "AUD+") ?>
</negotiate_item>
</negotiate_list>
<?php } else {echo small_mail_button($v["purchase_url"]);}
?>
            </td>
        </tr>
    </table></td>
<?php
}
?>
</tr></table>
<?php
            }
        }

        break;

    case "detailed":
        $first = true;
        if (is_array($promotion_products)) {
            foreach ($promotion_products as $v) {
                if ("" != $v["str"]) {
                    if ("bulk_offers" == $k) {
                        $temp = fetchBulk($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["title"];
                        }

                        if ("" == $v["text"]) {
                            $v["text"] = trim($temp["long_desc"]);
                        }

                        $price_au = fetchBulkPricing($v["str"], "AUD");
                        $price_aup = fetchBulkPricing($v["str"], "AUD+");
                        /*
$price_us = fetchBulkPricing($v['str'], 'USD');
 */
                        $v["view_url"] = relative_url(
                            $temp["bulk_url"],
                            $track_url
                        );
                        $v["image_url"] = relative_url(
                            $temp["image_url"],
                            $base_url
                        );
                        $v["purchase_url"] = relative_url(
                            "/bulk_offers/addtocart_" . $temp["id"] . ".html",
                            $track_url
                        );
                        $shots = [];
                    } else {
                        $temp = fetchProduct($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["product_name"];
                        }

                        if ("" == $v["text"]) {
                            $v["text"] = trim($temp["long_desc"]);
                        }

                        $price_au = fetchProductPricing($v["str"], "AUD");
                        $price_aup = fetchProductPricing($v["str"], "AUD+");
                        /*
$price_us = fetchProductPricing($v['str'], 'USD');
 */
                        $v["view_url"] = relative_url(
                            $temp["product_url"],
                            $track_url
                        );
                        $v["image_url"] = relative_url(
                            $temp["image_url"],
                            $base_url
                        );
                        $v["purchase_url"] = relative_url(
                            "/checkout/addtocart_" .
                                $temp["id"] .
                                "_" .
                                $temp["sub_id"] .
                                ".html",
                            $track_url
                        );

                        $actual = [];
                        $reference = [];
                        foreach (
                            productScreenshotsFromDatabase($v["str"], "y")
                            as $a => $b
                        ) {
                            if (2 == $b["ss_type"]) {
                                $reference[$a] = $b;
                            } else {
                                $actual[$a] = $b;
                            }
                        }

                        $shots = array_merge($actual, $reference);
                        if (count($shots)) {
                            $shots = reset(array_chunk($shots, 10));
                        }
                    } ?>
<table style="border-collapse:collapse<?= !$first
    ? ";border-top:1px solid #ccc"
    : "" ?>" width="100%" cellpadding="8"><tr>
    <td>
        <a href="<?= htmlspecialchars(
      $v["view_url"]
  ) ?>"><img src="<?= htmlspecialchars(
    $v["image_url"]
) ?>" alt="<?= htmlspecialchars($v["name"]) ?>" title="<?= htmlspecialchars(
    $v["name"]
) ?>" border="0" /></a>
<?php
ob_start();
if ("y" == $temp["price_online"] && $temp["saving"] >= 1.0) { ?>
<div style="text-align:center;">
<negotiate_list field="country">
<negotiate_item default="default">
        <?= dont_pay_text("$" .  convertCurrency($price_au["normal_price"],"AUD","USD") . " USD") ?>
</negotiate_item>
<negotiate_item value="AU">
        <?= dont_pay_text("$" . $price_aup["normal_price"] . " AUD inc. GST") ?>
</negotiate_item>
</negotiate_list>
        <b><i style="color:#c00">Save <?= $temp["saving"] ?>%</i></b><br /><br />
</div>
<?php }
if ("y" == $temp["price_online"]) { ?>
<negotiate_list field="country">
<negotiate_item default="default">
        <?= buy_button($v["purchase_url"], convertCurrency($price_au["current_price"],"AUD","USD"), "USD") ?>
</negotiate_item>
<negotiate_item value="AU">
        <?= buy_button($v["purchase_url"], $price_aup["current_price"], "AUD+") ?>
</negotiate_item>
</negotiate_list>
<?php } else {echo mail_button($v["purchase_url"]);}

$price_html = ob_get_clean();
if ("right" != $v["price_position"]) {
    echo "<br /><br /><br />" . $price_html;
}
?>
    </td>
    <td valign="top">
        <h3 style="margin:0.5em 0;font-size:15px"><a href="<?= htmlspecialchars(
      $v["view_url"]
  ) ?>" style="text-decoration:none;"><?= htmlspecialchars(
    $v["name"]
) ?></a></h3>
        <p style="font-size:12px"><?= formatHTMLDescription($v["text"]) ?></p>
<?php
if (count($shots)) { ?>
        <ul style="list-style:none;margin:0;padding:0">
<?php foreach ($shots as $shot) {
    if (0 != $shot["sub_id"]) { ?>
            <li style="float:left;padding:12px;margin:0"><a href="<?= htmlspecialchars(
       relative_url(
           str_replace(
               "/screenshots/",
               "/screenshots2/",
               $shot["screenshot_url"]
           ),
           $track_url
       )
   ) ?>" target="_blank"><img src="<?= htmlspecialchars(
    relative_url(str_replace("-sml.", "-0x40.", $shot["thumb_url"]), $base_url)
) ?>" alt="<?= htmlspecialchars(
    $shot["caption"]
) ?>" style="border:1px solid #ccc" /></a></li>
<?php }
} ?>
        </ul>
        <br style="clear:both" />
<?php }
if ("right" == $v["price_position"]) {
    echo $price_html;
}
?>
    </td>
</tr></table>
<?php $first = false;
                }
            }
        }

        break;

    case "full":
        $first = true;
        if (is_array($promotion_products)) {
            foreach ($promotion_products as $v) {
                if ("" != $v["str"]) {
                    if ("bulk_offers" == $k) {
                        $temp = fetchBulk($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["title"];
                        }

                        if ("" == $v["text"]) {
                            $v["text"] = trim($temp["short_desc"]);
                        }

                        $price_au = fetchBulkPricing($v["str"], "AUD");
                        $price_aup = fetchBulkPricing($v["str"], "AUD+");
                        /*
$price_us = fetchBulkPricing($v['str'], 'USD');
 */
                        $v["view_url"] = relative_url(
                            $temp["bulk_url"],
                            $track_url
                        );
                        $v["image_url"] = relative_url(
                            $temp["image_180_url"],
                            $base_url
                        );
                        $v["purchase_url"] = relative_url(
                            "/bulk_offers/addtocart_" . $temp["id"] . ".html",
                            $track_url
                        );
                    } else {
                        $temp = fetchProduct($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["product_name"];
                        }

                        if ("" == $v["text"]) {
                            $v["text"] = trim($temp["short_desc"]);
                        }

                        $price_au = fetchProductPricing($v["str"], "AUD");
                        $price_aup = fetchProductPricing($v["str"], "AUD+");
                        /*
$price_us = fetchProductPricing($v['str'], 'USD');
 */
                        $v["view_url"] = relative_url(
                            $temp["product_url"],
                            $track_url
                        );
                        $v["image_url"] = relative_url(
                            $temp["image_180_url"],
                            $base_url
                        );
                        $v["purchase_url"] = relative_url(
                            "/checkout/addtocart_" .
                                $temp["id"] .
                                "_" .
                                $temp["sub_id"] .
                                ".html",
                            $track_url
                        );
                    } ?>
<table style="border-collapse:collapse<?= !$first
    ? ";border-top:1px solid #ccc"
    : "" ?>" width="100%" cellpadding="8"><tr>
    <td><a href="<?= htmlspecialchars(
     $v["view_url"]
 ) ?>"><img src="<?= htmlspecialchars(
    $v["image_url"]
) ?>" alt="<?= htmlspecialchars($v["name"]) ?>" title="<?= htmlspecialchars(
    $v["name"]
) ?>" border="0" /></a></td>
    <td valign="top" width="100%">
        <h3 style="margin:0.5em 0;font-size:15px"><a href="<?= htmlspecialchars(
      $v["view_url"]
  ) ?>" style="text-decoration:none;"><?= htmlspecialchars(
    $v["name"]
) ?></a></h3>
        <p style="font-size:12px"><?= formatHTMLDescription($v["text"]) ?></p>
    </td>
    <td style="text-align:center">
        <br />
<?php
if ("y" == $temp["price_online"] && $temp["saving"] >= 1.0) { ?>
        <br />
<negotiate_list field="country">
<negotiate_item default="default">
        <?= dont_pay_text("$" .  convertCurrency($price_au["normal_price"],"AUD","USD") . " USD") ?>
</negotiate_item>
<negotiate_item value="AU">
        <?= dont_pay_text("$" . $price_aup["normal_price"] . " AUD inc. GST") ?>
</negotiate_item>
</negotiate_list>
        <b><i style="color:#c00">Save <?= $temp["saving"] ?>%</i></b><br /><br />
<?php } elseif ("n" == $temp["price_online"]) {
    echo please_ask_text();
} else {
     ?>
        <br />
        <br />
        <br />
        <br />
<?php
}
if ("y" == $temp["price_online"]) { ?>
<negotiate_list field="country">
<negotiate_item default="default">
        <?= buy_button($v["purchase_url"], convertCurrency($price_au["current_price"],"AUD","USD"), "USD") ?>
</negotiate_item>
<negotiate_item value="AU">
        <?= buy_button($v["purchase_url"], $price_aup["current_price"], "AUD+") ?>
</negotiate_item>
</negotiate_list>
<?php } else {echo mail_button($v["purchase_url"]);}
?>
    </td>
</tr></table>
<?php $first = false;
                }
            }
        }

        break;

    case "brief":
        if (is_array($temp = $promotion_products)) {
            foreach ($temp as $k2 => $v) {
                if ("" == $v["str"]) {
                    unset($temp[$k2]);
                }
            }
        }

        if (is_array($temp)) {
            foreach (array_chunk($temp, 5) as $first1 => $row) {

                $cell_width = 740 / count($row) - 8;
                $name_height = 0;
                foreach ($row as $v) {
                    if ("bulk_offers" == $k) {
                        $temp = fetchBulk($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["title"];
                        }
                    } else {
                        $temp = fetchProduct($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["product_name"];
                        }
                    }
                    $name_height = max(
                        $name_height,
                        estimate_p_height(12, $v["name"], $cell_width, true)
                    );
                }

                $style1 = "";
                if ($first1) {
                    echo dividing_bar();
                }
                ?>
<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php foreach ($row as $first2 => $v) {

    if ("bulk_offers" == $k) {
        $temp = fetchBulk($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["title"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }

        $price_au = fetchBulkPricing($v["str"], "AUD");
        $price_aup = fetchBulkPricing($v["str"], "AUD+");
        /*
$price_us = fetchBulkPricing($v['str'], 'USD');
 */
        $v["view_url"] = relative_url($temp["bulk_url"], $track_url);
        $v["image_url"] = relative_url($temp["thumb_url"], $base_url);
        $v["purchase_url"] = relative_url(
            "/bulk_offers/addtocart_" . $temp["id"] . ".html",
            $track_url
        );
    } else {
        $temp = fetchProduct($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["product_name"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }

        $price_au = fetchProductPricing($v["str"], "AUD");
        $price_aup = fetchProductPricing($v["str"], "AUD+");
        /*
$price_us = fetchProductPricing($v['str'], 'USD');
 */
        $v["view_url"] = relative_url($temp["product_url"], $track_url);
        $v["image_url"] = relative_url($temp["thumb_url"], $base_url);
        $v["purchase_url"] = relative_url(
            "/checkout/addtocart_" .
                $temp["id"] .
                "_" .
                $temp["sub_id"] .
                ".html",
            $track_url
        );
    }
    $style2 = "";
    if ($first2) {
        $style2 = "border-left:1px solid #ccc;";
    }

    $style = rtrim($style1 . $style2, ";");
    ?>
    <td<?= "" != $style
     ? ' style="' . $style . '"'
     : "" ?> width="20%" valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="4">
        <tr><th height="<?= ceil(
      $name_height + 8
  ) ?>"><h3 style="margin:0;font-size:12px"><a href="<?= htmlspecialchars(
    $v["view_url"]
) ?>" style="font-weight:bold;text-decoration:none"><?= htmlspecialchars(
    $v["name"]
) ?></a></h3></th></tr>
        <tr><td valign="top" align="center"><a href="<?= htmlspecialchars(
      $v["view_url"]
  ) ?>"><img title="<?= htmlspecialchars(
    $v["name"]
) ?>" src="<?= htmlspecialchars($v["image_url"]) ?>" alt="<?= htmlspecialchars(
    $v["name"]
) ?>" border="0" /></a></td></tr>
        <tr><td valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="3">
            <tr>
                <td style="color:#f00;font-size:18px" colspan="3" align="center">
<?php
if ("y" == $temp["price_online"] && 0.0 != $price_au["current_price"]) { ?>
<negotiate_list field="country">
<negotiate_item default="default">
                    <b><i>$<?= convertCurrency($price_au[
         "current_price"
                    ],"AUD","USD") ?></i></b> 
                    <!-- <span style="font-size:10px;white-space:nowrap;">exc GST</span> -->
</negotiate_item>
<negotiate_item value="AU">
                    <b><i>$<?= $price_aup[
         "current_price"
     ] ?></i></b> <span style="font-size:10px;white-space:nowrap;">inc GST</span>
</negotiate_item>
</negotiate_list>
<?php } else { ?>
                    <b><i>Please Ask</i></b>
<?php }
if ($temp["saving"] >= 1.0) { ?>
                    <br />
                    <i style="color:#f00;font-size:10px;">(Save <?= $temp["saving"] ?>%)</i>
<?php }
?>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="3"><a href="<?= htmlspecialchars(
        $v["purchase_url"]
    ) ?>"><img src="<?= htmlspecialchars(
    relative_url(
        "/images/newsletter_" . $style_id . "/unit_button.png",
        $base_url
    )
) ?>" alt="Buy Now!" border="0" /></a></td>
            </tr>
<?php if (count($temp["bulk_pricing"])) {
    foreach ($temp["bulk_pricing"] as $v) {

        if (0.0 == $v["unit_sale_price_us"]) {
            $v["unit_sale_price_us"] = convertCurrency(
                $v["unit_sale_price"],
                "AUD",
                "USD",
                false,
                $temp["precision"]
            );
        }

        if (0.0 == $v["unit_sale_price"]) {
            $v["unit_sale_price"] = convertCurrency(
                $v["unit_sale_price_us"],
                "USD",
                "AUD",
                false,
                $temp["precision"]
            );
        }
        ?>
            <tr style="border:1px solid #ccc">
                <td align="center"><i><?= intval($v["qty"]) ?> x</i></td>
                <td align="center">
<negotiate_list field="country">
<negotiate_item default="default">
                <i>$<?= convertCurrency(
        $v["unit_sale_price"] / 1.1,
        "AUD",
        "AUD",
        false,
        $temp["precision"]
    ) ?></i>
</negotiate_item>
<negotiate_item value="AU">
                <i>$<?= convertCurrency(
        $v["unit_sale_price"],
        "AUD",
        "AUD",
        false,
        $temp["precision"]
    ) ?></i>
</negotiate_item>
</negotiate_list>
                </td>
                <td align="center"><a href="<?= htmlspecialchars(
        relative_url(
            "/checkout/addtocart_" .
                $temp["id"] .
                "_" .
                $temp["sub_id"] .
                "_" .
                $v["qty"] .
                ".html",
            $track_url
        )
    ) ?>"><img src="<?= htmlspecialchars(
    relative_url(
        "/images/newsletter_" . $style_id . "/bulk_button.png",
        $base_url
    )
) ?>" alt="Buy" border="0" /></a></td>
            </tr>
<?php
    }
} ?>
        </table></td></tr>
    </table></td>
<?php
} ?>
</tr></table>
<?php
            }
        }

        break;
}
$packets[] = ["size" => "wide", "content" => ob_get_clean()];

                    }
                }

                break;

            case "bulk_offers":
            case "latest_offers":
            case "arcade_machines":
            case "arcade_boards":
            case "arcade_parts":
            case "arcade_merchandise":
            case "featured_products":
            case "specials":
                ob_start(); ?>
    <h2 style="color:#fff;background:#158 url(<?= htmlspecialchars(
     relative_url("/images/newsletter_" . $style_id . "/htiles.png", $base_url)
 ) ?>) repeat-x 0px 0px;font-size:21px;margin:0;padding:11px 15px 12px;font-weight:normal;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px"><?= htmlspecialchars(
    $sections[$k]["title"]
) ?></h2>
<?php
switch ($settings["option"]) {
    case "summary":
        if (is_array($temp = $settings["items"])) {
            foreach ($temp as $k2 => $v) {
                if ("" == $v["str"]) {
                    unset($temp[$k2]);
                }
            }
        }

        if (is_array($temp)) {
            foreach (array_chunk($temp, 3) as $first1 => $row) {
                if ($first1) {
                    echo dividing_bar();
                } ?>
<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php
$cell_width = 740 / count($row) - 17;
$name_height = 0;
$text_height = 0;
foreach ($row as $v) {
    if ("bulk_offers" == $k) {
        $temp = fetchBulk($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["title"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }
    } else {
        $temp = fetchProduct($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["product_name"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }
    }
    $name_height = max(
        $name_height,
        estimate_p_height(15, $v["name"], $cell_width, true)
    );
    $text_height = max(
        $text_height,
        estimate_p_height(12, $v["text"], $cell_width)
    );
}

foreach ($row as $first => $v) {
    if ("bulk_offers" == $k) {
        $temp = fetchBulk($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["title"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }

        $price_au = fetchBulkPricing($v["str"], "AUD");
        $price_aup = fetchBulkPricing($v["str"], "AUD+");
        /*
$price_us = fetchBulkPricing($v['str'], 'USD');
 */
        $v["view_url"] = relative_url($temp["bulk_url"], $track_url);
        $v["image_url"] = relative_url($temp["image_url"], $base_url);
        $v["purchase_url"] = relative_url(
            "/bulk_offers/addtocart_" . $temp["id"] . ".html",
            $track_url
        );
    } else {
        $temp = fetchProduct($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["product_name"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }

        $price_au = fetchProductPricing($v["str"], "AUD");
        $price_aup = fetchProductPricing($v["str"], "AUD+");
        /*
$price_us = fetchProductPricing($v['str'], 'USD');
 */
        $v["view_url"] = relative_url($temp["product_url"], $track_url);
        $v["image_url"] = relative_url($temp["image_url"], $base_url);
        $v["purchase_url"] = relative_url(
            "/checkout/addtocart_" .
                $temp["id"] .
                "_" .
                $temp["sub_id"] .
                ".html",
            $track_url
        );
    } ?>
    <td<?= $first
     ? ' style="border-left:1px solid #ccc"'
     : "" ?> width="33%" valign="top"><table style="border-collapse:collapse;font-size:12px" width="100%" cellpadding="8">
        <tr><td style="text-align:center">
            <h3 style="margin:0.5em 0;font-size:15px"><a href="<?= htmlspecialchars(
       $v["view_url"]
   ) ?>" style="text-decoration:none;"><?= htmlspecialchars(
    $v["name"]
) ?></a></h3>
<?php
$est_height = estimate_p_height(15, $v["name"], $cell_width, true);
if ($est_height < $name_height) { ?>
            <div style="height:<?= intval(
       $name_height - $est_height
   ) ?>px;padding:0;margin:0"></div>
<?php }
?>
            <a href="<?= htmlspecialchars(
       $v["view_url"]
   ) ?>"><img title="<?= htmlspecialchars(
    $v["name"]
) ?>" src="<?= htmlspecialchars($v["image_url"]) ?>" alt="<?= htmlspecialchars(
    $v["name"]
) ?>" border="0" style="margin:12px 0;width:100%;" /></a>
            <p style="font-size:12px;text-align:left"><?= formatHTMLDescription(
       $v["text"]
   ) ?></p>
<?php
$est_height = estimate_p_height(12, $v["text"], $cell_width);
if ($est_height < $text_height) { ?>
            <div style="height:<?= intval(
       $text_height - $est_height
   ) ?>px;padding:0;margin:0"></div>
<?php }
if ("y" == $temp["price_online"] && $temp["saving"] >= 1.0) { ?>
<negotiate_list field="country">
<negotiate_item default="default">
                <?= dont_pay_text("$" .  convertCurrency($price_au["normal_price"],"AUD","USD") . " USD") ?>
</negotiate_item>
<negotiate_item value="AU">
                <?= dont_pay_text("$" . $price_aup["normal_price"] . " AUD inc. GST") ?>
</negotiate_item>
</negotiate_list>
                <b><i style="color:#c00">Save <?= $temp["saving"] ?>%</i></b><br /><br />
<?php }
if ("y" == $temp["price_online"]) { ?>
<negotiate_list field="country">
<negotiate_item default="default">
                <?= small_buy_button($v["purchase_url"], convertCurrency($price_au["current_price"],"AUD","USD"), "USD") ?>
</negotiate_item>
<negotiate_item value="AU">
                <?= small_buy_button($v["purchase_url"], $price_aup["current_price"], "AUD+") ?>
</negotiate_item>
</negotiate_list>
<?php if ("bulk_offers" == $k && $temp["bulk_qty"] > 1) { ?>
                <br />
<negotiate_list field="country">
<negotiate_item default="default">
                <?= average_price_text(
        "$" .
            convertCurrency(
                $price_au["current_price"] / $temp["bulk_qty"],
                "AUD","USD"
            ) .
            " USD"
    ) ?>
</negotiate_item>
<negotiate_item value="AU">
                <?= average_price_text(
        "$" .
            convertCurrency(
                $price_aup["current_price"] / $temp["bulk_qty"],
                "AUD"
            ) .
            " AUD"
    ) ?>
</negotiate_item>
</negotiate_list>
<?php }} else {echo small_mail_button($v["purchase_url"]);}
?>
            </td>
        </tr>
    </table></td>
<?php
}
?>
</tr></table>
<?php
            }
        }

        break;

    case "detailed":
        $first = true;
        if (is_array($settings["items"])) {
            foreach ($settings["items"] as $v) {
                if ("" != $v["str"]) {
                    if ("bulk_offers" == $k) {
                        $temp = fetchBulk($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["title"];
                        }

                        if ("" == $v["text"]) {
                            $v["text"] = trim($temp["long_desc"]);
                        }

                        $price_au = fetchBulkPricing($v["str"], "AUD");
                        $price_aup = fetchBulkPricing($v["str"], "AUD+");
                        /*
$price_us = fetchBulkPricing($v['str'], 'USD');
 */
                        $v["view_url"] = relative_url(
                            $temp["bulk_url"],
                            $track_url
                        );
                        $v["image_url"] = relative_url(
                            $temp["image_url"],
                            $base_url
                        );
                        $v["purchase_url"] = relative_url(
                            "/bulk_offers/addtocart_" . $temp["id"] . ".html",
                            $track_url
                        );
                        $shots = [];
                    } else {
                        $temp = fetchProduct($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["product_name"];
                        }

                        if ("" == $v["text"]) {
                            $v["text"] = trim($temp["long_desc"]);
                        }

                        $price_au = fetchProductPricing($v["str"], "AUD");
                        $price_aup = fetchProductPricing($v["str"], "AUD+");
                        /*
$price_us = fetchProductPricing($v['str'], 'USD');
 */ 
                        $v["view_url"] = relative_url(
                            $temp["product_url"],
                            $track_url
                        );
                        $v["image_url"] = relative_url(
                            $temp["image_url"],
                            $base_url
                        );
                        $v["purchase_url"] = relative_url(
                            "/checkout/addtocart_" .
                                $temp["id"] .
                                "_" .
                                $temp["sub_id"] .
                                ".html",
                            $track_url
                        );

                        $actual = [];
                        $reference = [];
                        foreach (
                            productScreenshotsFromDatabase($v["str"], "y")
                            as $a => $b
                        ) {
                            if (2 == $b["ss_type"]) {
                                $reference[$a] = $b;
                            } else {
                                $actual[$a] = $b;
                            }
                        }

                        $shots = array_merge($actual, $reference);
                        if (count($shots)) {
                            $shots = reset(array_chunk($shots, 10));
                        }
                    } ?>
<table style="border-collapse:collapse<?= !$first
    ? ";border-top:1px solid #ccc"
    : "" ?>" width="100%" cellpadding="8"><tr>
    <td>
        <a href="<?= htmlspecialchars(
      $v["view_url"]
  ) ?>"><img src="<?= htmlspecialchars(
    $v["image_url"]
) ?>" alt="<?= htmlspecialchars($v["name"]) ?>" title="<?= htmlspecialchars(
    $v["name"]
) ?>" border="0" /></a>
<?php
ob_start();
if ("y" == $temp["price_online"] && $temp["saving"] >= 1.0) { ?>
<div style="text-align:center;">
<negotiate_list field="country">
<negotiate_item default="default">
        <?= dont_pay_text("$" .  convertCurrency($price_au["normal_price"],"AUD","USD") . " USD") ?>
</negotiate_item>
<negotiate_item value="AU">
        <?= dont_pay_text("$" . $price_aup["normal_price"] . " AUD inc. GST") ?>
</negotiate_item>
</negotiate_list>
        <b><i style="color:#c00">Save <?= $temp["saving"] ?>%</i></b><br /><br />
</div>
<?php }
if ("y" == $temp["price_online"]) { ?>
<negotiate_list field="country">
<negotiate_item default="default">
        <?= buy_button($v["purchase_url"], convertCurrency($price_au["current_price"],"AUD","USD"), "USD") ?>
</negotiate_item>
<negotiate_item value="AU">
        <?= buy_button($v["purchase_url"], $price_aup["current_price"], "AUD+") ?>
</negotiate_item>
</negotiate_list>
<?php if ("bulk_offers" == $k && $temp["bulk_qty"] > 1) { ?>
<div style="text-align:center;">
        <br />
<negotiate_list field="country">
<negotiate_item default="default">
        <?= average_price_text(
      "$" .
          convertCurrency(
              $price_au["current_price"] / $temp["bulk_qty"],
              "AUD","USD"
          ) .
          " USD"
  ) ?>
</negotiate_item>
<negotiate_item value="AU">
        <?= average_price_text(
      "$" .
          convertCurrency(
              $price_aup["current_price"] / $temp["bulk_qty"],
              "AUD"
          ) .
          " AUD"
  ) ?>
</negotiate_item>
</negotiate_list>
</div>
<?php }} else {echo mail_button($v["purchase_url"]);}

$price_html = ob_get_clean();
if ("right" != $v["price_position"]) {
    echo "<br /><br /><br />" . $price_html;
}
?>
    </td>
    <td valign="top">
        <h3 style="margin:0.5em 0;font-size:15px"><a href="<?= htmlspecialchars(
      $v["view_url"]
  ) ?>" style="text-decoration:none;"><?= htmlspecialchars(
    $v["name"]
) ?></a></h3>
        <p style="font-size:12px"><?= formatHTMLDescription($v["text"]) ?></p>
<?php
if (count($shots)) { ?>
        <ul style="list-style:none;margin:0;padding:0">
<?php foreach ($shots as $shot) {
    if (0 != $shot["sub_id"]) { ?>
            <li style="float:left;padding:12px;margin:0"><a href="<?= htmlspecialchars(
       relative_url(
           str_replace(
               "/screenshots/",
               "/screenshots2/",
               $shot["screenshot_url"]
           ),
           $track_url
       )
   ) ?>" target="_blank"><img src="<?= htmlspecialchars(
    relative_url(str_replace("-sml.", "-0x40.", $shot["thumb_url"]), $base_url)
) ?>" alt="<?= htmlspecialchars(
    $shot["caption"]
) ?>" style="border:1px solid #ccc" /></a></li>
<?php }
} ?>
        </ul>
        <br style="clear:both" />
<?php }
if ("right" == $v["price_position"]) {
    echo $price_html;
}
?>
    </td>
</tr></table>
<?php $first = false;
                }
            }
        }

        break;

    case "full":
        $first = true;
        if (is_array($settings["items"])) {
            foreach ($settings["items"] as $v) {
                if ("" != $v["str"]) {
                    if ("bulk_offers" == $k) {
                        $temp = fetchBulk($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["title"];
                        }

                        if ("" == $v["text"]) {
                            $v["text"] = trim($temp["short_desc"]);
                        }

                        $price_au = fetchBulkPricing($v["str"], "AUD");
                        $price_aup = fetchBulkPricing($v["str"], "AUD+");
                        $price_us = fetchBulkPricing($v['str'], 'USD');
                        /*
$price_us = fetchBulkPricing($v['str'], 'USD');
 */
                        $v["view_url"] = relative_url(
                            $temp["bulk_url"],
                            $track_url
                        );
                        $v["image_url"] = relative_url(
                            $temp["image_url"],
                            $base_url
                        );
                        $v["purchase_url"] = relative_url(
                            "/bulk_offers/addtocart_" . $temp["id"] . ".html",
                            $track_url
                        );
                    } else {
                        $temp = fetchProduct($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["product_name"];
                        }

                        if ("" == $v["text"]) {
                            $v["text"] = trim($temp["short_desc"]);
                        }

                        $price_au = fetchProductPricing($v["str"], "AUD");
                        $price_aup = fetchProductPricing($v["str"], "AUD+");
                        /*
$price_us = fetchProductPricing($v['str'], 'USD');
 */
                        $v["view_url"] = relative_url(
                            $temp["product_url"],
                            $track_url
                        );
                        $v["image_url"] = relative_url(
                            $temp["image_url"],
                            $base_url
                        );
                        $v["purchase_url"] = relative_url(
                            "/checkout/addtocart_" .
                                $temp["id"] .
                                "_" .
                                $temp["sub_id"] .
                                ".html",
                            $track_url
                        );
                    } ?>
<table style="border-collapse:collapse<?= !$first
    ? ";border-top:1px solid #ccc"
    : "" ?>" width="100%" cellpadding="8"><tr>
    <td><a href="<?= htmlspecialchars(
     $v["view_url"]
 ) ?>"><img src="<?= htmlspecialchars(
    $v["image_url"]
) ?>" alt="<?= htmlspecialchars($v["name"]) ?>" title="<?= htmlspecialchars(
    $v["name"]
) ?>" border="0" /></a></td>
    <td valign="top" width="100%">
        <h3 style="margin:0.5em 0;font-size:15px"><a href="<?= htmlspecialchars(
      $v["view_url"]
  ) ?>" style="text-decoration:none;"><?= htmlspecialchars(
    $v["name"]
) ?></a></h3>
        <p style="font-size:12px"><?= formatHTMLDescription($v["text"]) ?></p>
    </td>
    <td style="text-align:center">
        <br />
<?php
if ("y" == $temp["price_online"] && $temp["saving"] >= 1.0) { ?>
        <br />
<negotiate_list field="country">
<negotiate_item default="default">
        <?= dont_pay_text("$" .  convertCurrency($price_au["normal_price"],"AUD","USD") . " USD") ?>
</negotiate_item>
<negotiate_item value="AU">
        <?= dont_pay_text("$" . $price_aup["normal_price"] . " AUD inc. GST") ?>
</negotiate_item>
</negotiate_list>
        <b><i style="color:#c00">Save <?= $temp["saving"] ?>%</i></b><br /><br />
<?php } elseif ("n" == $temp["price_online"]) {
    echo please_ask_text();
} else {
     ?>
        <br />
        <br />
        <br />
        <br />
<?php
}
if ("y" == $temp["price_online"]) { ?>
<negotiate_list field="country">
<negotiate_item default="default">
        <?= buy_button($v["purchase_url"], convertCurrency($price_au["current_price"],"AUD","USD"), "USD") ?>
</negotiate_item>
<negotiate_item value="AU">
        <?= buy_button($v["purchase_url"], $price_aup["current_price"], "AUD+") ?>
</negotiate_item>
</negotiate_list>
<?php if ("bulk_offers" == $k && $temp["bulk_qty"] > 1) { ?>
                <br />
<negotiate_list field="country">
<negotiate_item default="default">
                <?= average_price_text(
        "$" .
            convertCurrency(
                $price_au["current_price"] / $temp["bulk_qty"],
                "AUD","USD"
            ) .
            " USD"
    ) ?>
</negotiate_item>
<negotiate_item value="AU">
                <?= average_price_text(
        "$" .
            convertCurrency(
                $price_aup["current_price"] / $temp["bulk_qty"],
                "AUD"
            ) .
            " AUD"
    ) ?>
</negotiate_item>
</negotiate_list>
<?php }} else {echo mail_button($v["purchase_url"]);}
?>
    </td>
</tr></table>
<?php $first = false;
                }
            }
        }

        break;

    case "brief":
        if (is_array($temp = $settings["items"])) {
            foreach ($temp as $k2 => $v) {
                if ("" == $v["str"]) {
                    unset($temp[$k2]);
                }
            }
        }

        if (is_array($temp)) {
            foreach (array_chunk($temp, 5) as $first1 => $row) {

                $cell_width = 740 / count($row) - 8;
                $name_height = 0;
                foreach ($row as $v) {
                    if ("bulk_offers" == $k) {
                        $temp = fetchBulk($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["title"];
                        }
                    } else {
                        $temp = fetchProduct($v["str"]);
                        if ("" == $v["name"]) {
                            $v["name"] = $temp["product_name"];
                        }
                    }
                    $name_height = max(
                        $name_height,
                        estimate_p_height(12, $v["name"], $cell_width, true)
                    );
                }

                $style1 = "";
                if ($first1) {
                    echo dividing_bar();
                }
                ?>
<table style="border-collapse:collapse" width="100%" cellpadding="0" class="tableListing"><tr>
<?php foreach ($row as $first2 => $v) {

    if ("bulk_offers" == $k) {
        $temp = fetchBulk($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["title"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }

        $price_au = fetchBulkPricing($v["str"], "AUD");
        $price_aup = fetchBulkPricing($v["str"], "AUD+");
        /*
$price_us = fetchBulkPricing($v['str'], 'USD');
 */
        $v["view_url"] = relative_url($temp["bulk_url"], $track_url);
        $v["image_url"] = relative_url($temp["thumb_url"], $base_url);
        $v["purchase_url"] = relative_url(
            "/bulk_offers/addtocart_" . $temp["id"] . ".html",
            $track_url
        );
    } else {
        $temp = fetchProduct($v["str"]);
        if ("" == $v["name"]) {
            $v["name"] = $temp["product_name"];
        }

        if ("" == $v["text"]) {
            $v["text"] = trim($temp["short_desc"]);
        }

        $price_au = fetchProductPricing($v["str"], "AUD");
        $price_aup = fetchProductPricing($v["str"], "AUD+");
        /*
$price_us = fetchProductPricing($v['str'], 'USD');
 */
        $v["view_url"] = relative_url($temp["product_url"], $track_url);
        $v["image_url"] = relative_url($temp["thumb_url"], $base_url);
        $v["purchase_url"] = relative_url(
            "/checkout/addtocart_" .
                $temp["id"] .
                "_" .
                $temp["sub_id"] .
                ".html",
            $track_url
        );
    }
    $style2 = "";
    if ($first2) {
        $style2 = "border-left:1px solid #ccc;";
    }

    $style = rtrim($style1 . $style2, ";");
    ?>
    <td<?= "" != $style
     ? ' style="' . $style . '"'
     : "" ?> width="20%" valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="4">
        <tr><th height="<?= ceil(
      $name_height + 8
  ) ?>"><h3 style="margin:0;font-size:12px"><a href="<?= htmlspecialchars(
    $v["view_url"]
) ?>" style="font-weight:bold;text-decoration:none"><?= htmlspecialchars(
    $v["name"]
) ?></a></h3></th></tr>
        <tr><td valign="top" align="center"><a href="<?= htmlspecialchars(
      $v["view_url"]
  ) ?>"><img title="<?= htmlspecialchars(
    $v["name"]
) ?>" src="<?= htmlspecialchars($v["image_url"]) ?>" alt="<?= htmlspecialchars(
    $v["name"]
) ?>" border="0" style="width:120px;"/></a></td></tr>
        <tr><td valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="3">
            <tr>
                <td style="color:#f00;font-size:18px" colspan="3" align="center">
<?php
if ("y" == $temp["price_online"] && 0.0 != $price_au["current_price"]) { ?>
<negotiate_list field="country">
<negotiate_item default="default">
                    <b><i>$<?= convertCurrency($price_au[
         "current_price"
                    ],"AUD","USD") ?></i></b> 
                    <!-- <span style="font-size:10px;white-space:nowrap;">exc GST</span> -->
</negotiate_item>
<negotiate_item value="AU">
                    <b><i>$<?= $price_aup[
         "current_price"
     ] ?></i></b> <span style="font-size:10px;white-space:nowrap;">inc GST</span>
</negotiate_item>
</negotiate_list>
<?php } else { ?>
                    <b><i>Please Ask</i></b>
<?php }
if ($temp["saving"] >= 1.0) { ?>
                    <br />
                    <i style="color:#f00;font-size:10px;">(Save <?= $temp["saving"] ?>%)</i>
<?php }
?>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="3"><a href="<?= htmlspecialchars(
        $v["purchase_url"]
    ) ?>"><img src="<?= htmlspecialchars(
    relative_url(
        "/images/newsletter_" . $style_id . "/unit_button.png",
        $base_url
    )
) ?>" alt="Buy Now!" border="0" /></a></td>
            </tr>
<?php if (count($temp["bulk_pricing"])) {
    foreach ($temp["bulk_pricing"] as $v) {

        if (0.0 == $v["unit_sale_price_us"]) {
            $v["unit_sale_price_us"] = convertCurrency(
                $v["unit_sale_price"],
                "AUD",
                "USD",
                false,
                $temp["precision"]
            );
        }

        if (0.0 == $v["unit_sale_price"]) {
            $v["unit_sale_price"] = convertCurrency(
                $v["unit_sale_price_us"],
                "USD",
                "AUD",
                false,
                $temp["precision"]
            );
        }
        ?>
            <tr style="border:1px solid #ccc">
                <td align="center"><i><?= intval($v["qty"]) ?> x</i></td>
                <td align="center">
<negotiate_list field="country">
<negotiate_item default="default">
                <i>$<?= convertCurrency(
        $v["unit_sale_price"] / 1.1,
        "AUD",
        false,
        $temp["precision"]
    ) ?></i>
</negotiate_item>
<negotiate_item value="AU">
                <i>$<?= convertCurrency(
        $v["unit_sale_price"],
        "AUD",
        false,
        $temp["precision"]
    ) ?></i>
</negotiate_item>
</negotiate_list>
                </td>
                <td align="center"><a href="<?= htmlspecialchars(
        relative_url(
            "/checkout/addtocart_" .
                $temp["id"] .
                "_" .
                $temp["sub_id"] .
                "_" .
                $v["qty"] .
                ".html",
            $track_url
        )
    ) ?>"><img src="<?= htmlspecialchars(
    relative_url(
        "/images/newsletter_" . $style_id . "/bulk_button.png",
        $base_url
    )
) ?>" alt="Buy" border="0" /></a></td>
            </tr>
<?php
    }
} ?>
        </table></td></tr>
    </table></td>
<?php
} ?>
</tr></table>
<?php
            }
        }

        break;
}
$packets[] = ["size" => "wide", "content" => ob_get_clean()];
break;

        }
    }
}

foreach ($packets as $k => $v) {
    $packets[$k]["content"] =
        '<div style="border:1px solid #ccc;margin:4px;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:4px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px">' .
        $packets[$k]["content"] .
        "</div>";
}

$prev = false;
$next = false;
foreach ($packets as $k => $v) {
    $prev = $next;
    $next = $k;
    if (
        false !== $prev &&
        "thin" == $packets[$prev]["size"] &&
        "thin" == $v["size"]
    ) {
        $packets[$k]["size"] = "wide";
        $packets[$k]["content"] =
            '<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>' .
            "\n\t" .
            '<td width="50%" valign="top">' .
            $packets[$prev]["content"] .
            "</td>" .
            "\n\t" .
            '<td width="50%" valign="top">' .
            $packets[$next]["content"] .
            "</td>" .
            "\n" .
            "</tr></table>";
        unset($packets[$prev]);
        $prev = false;
    }
}

/**
 * Apply superscript to $ and decimals in prices
 */
//foreach($packets as $k => $v) $packets[$k] = fancy_currency($v);

//foreach($packets as $v) echo $v['content'] . "\n\n";
$temp = [];
foreach ($packets as $v) {
    $temp[] = $v["content"];
}

echo implode('<div style="height:0.5em"></div>' . "\n\n", $temp) . "\n\n";
?>

<div style="background:#eee url(<?= htmlspecialchars(
    relative_url("/images/newsletter_" . $style_id . "/htiles.png", $base_url)
) ?>) repeat-x 0px -112px;border:1px solid #ccc;color:#000;height:100px;margin:4px;position:relative">
<?php
$row = $DB->query_first(
    "SELECT `company_invoiceheader` FROM `highwaygroup`.`company_info` WHERE `company_id` = 39"
);
$row = explode("\n", $row["company_invoiceheader"]);
?>
    <table style="border-collapse:collapse;font-size:10px;position:absolute;right:16px;top:20px;width: 49%; float: right;" cellpadding="0"> <?php foreach (
     $row
     as $v
 ) {
     $row = explode(":", $v);
     if (count($row) == 1) { ?>
        <tr><td valign="top" colspan="2" align="right"><?= htmlspecialchars(
      trim($v)
  ) ?></td></tr>
<?php } elseif (count($row) == 2) { ?>
        <tr>
            <th valign="top" align="right"><?= htmlspecialchars(trim($row[0])) ?>:</th>
            <td valign="top" align="right"><?= htmlspecialchars(trim($row[1])) ?></td>
        </tr>
<?php } else {foreach (explode(", ", $v) as $v) {
             $row = explode(":", $v); ?>
        <tr>
            <th valign="top" align="right"><?= htmlspecialchars(trim($row[0])) ?>:</th>
            <td valign="top" align="right"><?= htmlspecialchars(trim($row[1])) ?></td>
        </tr>
<?php
         }}
 } ?>
    </table>
    <table style="border-collapse:collapse;font-size:9px;left:16px;position:absolute;top:20px" cellpadding="0"><tr>
        <td height="80" width="64" valign="top" align="center"><a href="javascript:window.print()" style="color:#000;text-decoration:none"><img src="<?= htmlspecialchars(
      relative_url(
          "/images/newsletter_" . $style_id . "/print_button.png",
          $base_url
      )
  ) ?>" alt="" border="0" /><br />Print</a></td>
        <td height="80" width="64" valign="top" align="center"><a href="<?= htmlspecialchars(
      relative_url(
          "/messages/newsletter_forward.html?id=" .
              $item["id"] .
              "&from_name=[FIRST_NAME]+[LAST_NAME]&from_email=[EMAIL_ADDRESS]",
          $track_url
      )
  ) ?>" target="_blank" style="color:#000;text-decoration:none"><img src="<?= htmlspecialchars(
    relative_url(
        "/images/newsletter_" . $style_id . "/forward_button.png",
        $base_url
    )
) ?>" alt="" border="0" /><br />Email to<br />Friend</a></td>
        <td height="80" width="64" valign="top" align="center"><a href="<?= htmlspecialchars(
      relative_url(
          "/messages/unsubscribe.html?email=[EMAIL_ADDRESS]",
          $track_url
      )
  ) ?>" target="_blank" style="color:#000;text-decoration:none"><img src="<?= htmlspecialchars(
    relative_url(
        "/images/newsletter_" . $style_id . "/unsubscribe_button.png",
        $base_url
    )
) ?>" alt="" border="0" /><br />Unsubscribe</a></td>
    </tr></table>
</div>

</body>
</html>
<?php
function dividing_bar()
{
    ob_start(); ?>
<hr style="background:#158;height:6px;border-style:none;border-top:1px solid #579;border-bottom:1px solid #bdd" />
<?php return ob_get_clean();
}

function please_ask_text($text = "")
{
    ob_start(); ?>
<p style="font-size:9px;color:#777">
    <b style="font-size:16px">PLEASE ASK</b><br />
    <i>Product Pricing is currently unavailable.<br />If you wish to be notified when pricing becomes available, click the button and add your name to our list.</i><br />
</p>
<?php return ob_get_clean();
}

function average_price_text($text = "")
{
    ob_start(); ?>
<p style="font-size:17px;color:#888;margin:0">
    Average Price<br />
    <?= $text ?><br />
</p>
<?php return fancy_currency(ob_get_clean());
}

function dont_pay_text($text = "")
{
    ob_start(); ?>
<p style="font-size:17px;color:#888;margin:0">
    Don't Pay<br />
    <strike><?= $text ?></strike><br />
</p>
<?php return fancy_currency(ob_get_clean());
}

function mail_button(
    $url = "#",
    $text = 'Coming Soon<br /><span style="font-size:22px">NOTIFY ME</span>'
) {
    global $base_url, $style_id;
    ob_start();
    ?>
<a target="_blank" href="<?= htmlspecialchars(
    $url
) ?>" style="text-align:center;width:130px;padding:4px 67px 4px 4px;font-size:18px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(<?= htmlspecialchars(
    relative_url(
        "/images/newsletter_" . $style_id . "/mail_button.png",
        $base_url
    )
) ?>) no-repeat 0px 0px scroll;height:54px"><b><i><?= $text ?></i></b></a>
<?php return ob_get_clean();
}

function small_mail_button(
    $url = "#",
    $text = 'Coming Soon<br /><span style="font-size:16px">NOTIFY ME</span>'
) {
    global $base_url, $style_id;
    ob_start();
    ?>
<a target="_blank" href="<?= htmlspecialchars(
    $url
) ?>" style="text-align:center;width:95px;padding:5px 52px 5px 5px;font-size:13px;color:#fff;text-decoration:none;display:block;margin:0 auto;background:transparent url(<?= htmlspecialchars(
    relative_url(
        "/images/newsletter_" . $style_id . "/small_mail_button.png",
        $base_url
    )
) ?>) no-repeat 0px 0px scroll;height:36px"><b><i><?= $text ?></i></b></a>
<?php return ob_get_clean();
}

function buy_button($url = "#", $price = "", $currency = "")
{
    global $base_url, $style_id;
    $currency_symbol = "";
    if ("" != $currency) {
        $currency_symbol = getCurrencySymbol(substr($currency, 0, 3), "HTML");
    }

    if (strlen($price) > 7 && substr($price, -3) == ".00") {
        $price = substr($price, 0, -3);
    }

    $font_size = min(30, 44 - strlen($price) * 2);
    ob_start();
    ?>
<a target="_blank" href="<?= htmlspecialchars(
    $url
) ?>" style="text-align:center;width:121px;padding:8px 73px 8px 8px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(<?= htmlspecialchars(relative_url("/images/newsletter_" . $style_id . "/buy_button.png", $base_url)) ?>) no-repeat 0px 0px scroll;height:46px;vertical-align:middle"><b><i><big style="font-size:30px"><?= $currency_symbol . $price ?></big><br /><span style="float:right;font-size:12px"><?= substr($currency, 0, 3) . ("AUD" == $currency ? " exc. GST" : "") . ("AUD+" == $currency ? " inc. GST" : "") ?></span></i></b></a>
<?php return fancy_currency(ob_get_clean());
}

function small_buy_button($url = "#", $price = "", $currency = "")
{
    global $base_url, $style_id;
    $currency_symbol = "";
    if ("" != $currency) {
        $currency_symbol = getCurrencySymbol(substr($currency, 0, 3), "HTML");
    }

    if (strlen($price) > 8 && substr($price, -3) == ".00") {
        $price = substr($price, 0, -3);
    }

    $font_size = min(22, 54 - strlen($price) * 4);
    ob_start();
    ?>
<a target="_blank" href="<?= htmlspecialchars(
    $url
) ?>" style="text-align:center;width:92px;padding:6px 54px 6px 6px;color:#fff;text-decoration:none;display:block;margin:0 auto;background:transparent url(<?= htmlspecialchars(relative_url("/images/newsletter_" . $style_id . "/small_buy_button.png", $base_url)) ?>) no-repeat 0px 0px scroll;height:34px"><b><i><big style="font-size:<?= $font_size ?>px"><?= $currency_symbol . $price ?></big><br /><span style="float:right;font-size:10px"><?= substr($currency, 0, 3) . ("AUD" == $currency ? " exc. GST" : "") . ("AUD+" == $currency ? " inc. GST" : "") ?></span></i></b></a>
<?php return fancy_currency(ob_get_clean());
}

function fetchProduct($id)
{
    moduleload("product");
    /**
     * Fields needed:
     * Y bulk_pricing => qty,unit_sale_price_us
     * N final_price
     * Y id
     * Y image_180_url
     * Y precision
     * Y price_online
     * Y product_name
     * N product_offerus
     * Y product_url
     * N saving
     * Y short_desc
     * Y thumb_url
     */
    $temp = productFromDatabase($id);
    $temp["bulk_pricing"] = productBulkPricingFromDatabase($id);

    $temp["precision"] = $temp["dPoints"];

    $other = fetchProductPricing($id, "USD");
    $temp["saving"] = $other["savings"];
    $temp["product_offerus"] = $other["normal_price"];
    $temp["final_price"] = $other["current_price"];

    if ($temp["final_price"] <= 0) {
        $temp["price_online"] = "n";
    }

    if ("n" == $temp["price_online"]) {
        $temp["special"] = "n";
    }

    if ("" == $temp["image_url"]) {
        $temp["image_url"] = "/new_images/noimage.gif";
    }

    if ("" == $temp["thumb_url"]) {
        $temp["thumb_url"] = "/new_images/noimage_small.gif";
    }

    return $temp;
}

function fetchBulk($id)
{
    /**
     * Fields needed:
     * Y title
     * Y thumb_url
     * Y short_desc
     * Y saving
     * N product_offerus
     * N price_online
     * Y precision
     * Y image_180_url
     * Y id
     * N final_price
     * Y bulk_url
     * Y bulk_pricing
     */
    $temp = bulkFromDatabase($id);
    $temp["bulk_pricing"] = [];
    $temp["precision"] = 2;

    $temp["price_online"] = "y" == $temp["status"] ? "y" : "n";
    $other = fetchBulkPricing($id, "AUD");
    $temp["saving"] = $other["savings"];
    $temp["product_offerus"] = $other["normal_price"];
    $temp["final_price"] = $other["current_price"];

    if ($temp["final_price"] <= 0) {
        $temp["price_online"] = "n";
    }

    if ("n" == $temp["price_online"]) {
        $temp["special"] = "n";
    }

    if ("" == $temp["image_url"]) {
        $temp["image_url"] = "/new_images/noimage.gif";
    }

    if ("" == $temp["thumb_url"]) {
        $temp["thumb_url"] = "/new_images/noimage_small.gif";
    }

    return $temp;
}

function fetchProductPricing($product_str, $wanted_currency = "USD")
{
    $product = productFromDatabase($product_str);
    $currency = strtoupper(substr($wanted_currency, 0, 3));

    $result = [];
    $result["primary_price"] = $product["primary_price"];
    $result["primary_currency"] = $product["primary_currency"];
    $result["secondary_price"] = $product["secondary_price"];
    $result["secondary_currency"] = $product["secondary_currency"];

    $result["normal_price"] = convertCurrency(
        $product["primary_price"],
        $product["primary_currency"],
        $currency,
        false,
        $product["precision"]
    );
    $result["normal_currency"] = $currency;
    $result["current_price"] = convertCurrency(
        $product["secondary_price"],
        $product["secondary_currency"],
        $currency,
        false,
        $product["precision"]
    );
    $result["current_currency"] = $currency;

    if ("AUD+" == $wanted_currency) {
        $result["normal_price"] = bcadd(
            $result["normal_price"],
            bcmul($result["normal_price"], "0.1", 2),
            $product["precision"]
        );
        $result["current_price"] = bcadd(
            $result["current_price"],
            bcmul($result["current_price"], "0.1", 2),
            $product["precision"]
        );
        if (1 == $product["section"] || 2 == $product["section"]) {
            $excess = bcmod(bcmul("100", $result["normal_price"], 0), "100");
            if (bccomp($excess, "0", 0) > 0) {
                $result["normal_price"] = bcadd(
                    convertCurrency(
                        bcmul(
                            "0.01",
                            bcsub(
                                bccomp($excess, "50", 0) < 0 ? "0" : "100",
                                $excess,
                                0
                            ),
                            2
                        ),
                        $result["current_currency"]
                    ),
                    $result["normal_price"],
                    $product["precision"]
                );
            }

            $excess = bcmod(bcmul("100", $result["current_price"], 0), "100");
            if (bccomp($excess, "0", 0) > 0) {
                $result["current_price"] = bcadd(
                    convertCurrency(
                        bcmul(
                            "0.01",
                            bcsub(
                                bccomp($excess, "50", 0) < 0 ? "0" : "100",
                                $excess,
                                0
                            ),
                            2
                        ),
                        $result["current_currency"]
                    ),
                    $result["current_price"],
                    $product["precision"]
                );
            }
        }
    }

    $savings = "0";
    if (bccomp($result["normal_price"], "0.0000", 4) > 0) {
        $savings = bcsub(
            $result["normal_price"],
            $result["current_price"],
            $product["precision"]
        );
        $savings = bcmul("100", bcdiv($savings, $result["normal_price"], 2), 0);
    }
    $result["savings"] = $savings;

    return $result;
    /*
$sales_price_au = convertCurrency($product['product_offerau'] / 1.1, 'AUD', '', false, $precision);
$sales_price_us = convertCurrency($product['product_offer'], 'USD', '', $precision != 4, $precision);
$sales_price = (strtoupper($product['selected']) == 'AUD') ? $sales_price_au : $sales_price_us;
$sales_currency = strtoupper($product['selected']);

$previous_price_au = convertCurrency($product['orig_priceau'] / 1.1, 'AUD', '', false, $precision);
$previous_price_us = convertCurrency($product['orig_price'], 'USD', '', $precision != 4, $precision);
$previous_price = ($wanted_currency == 'AUD') ? $previous_price_au : $previous_price_us;
$previous_currency = ($wanted_currency == 'AUD') ? 'AUD' : 'USD';

$special_price = $product['special_price'];
$special_currency = 'USD';

$precision = $product['dPoints'];
$special_flag = 'n';

$primary_currency = ($wanted_currency == 'AUD') ? 'AUD' : ($wanted_currency == 'USD') ? 'USD' : $sales_currency;
$primary_price = ($wanted_currency == 'AUD') ? $sales_price_au : ($wanted_currency == 'USD') ? $sales_price_us : $sales_price;

$normal_currency = $wanted_currency;
$normal_price = convertCurrency($primary_price, $primary_currency, $wanted_currency, true, $precision);
if (convertCurrency($previous_price, $previous_currency, $normal_currency) > $normal_price) $normal_price = convertCurrency($previous_price, $previous_currency, $wanted_currency, true, $precision);

$secondary_currency = ($special_flag == 'y') ? $special_currency : $primary_currency;
$secondary_price = ($special_flag == 'y') ? $special_price : $primary_price;

$current_currency = $wanted_currency;
$current_price = convertCurrency($secondary_price, $secondary_currency, $wanted_currency, true, $precision);
 */
    /**
     * Stupid rounding method
     */
    /*
if ($product['section'] == 1 || $product['section'] == 2)
{
$normal_price = convertCurrency(round($normal_price), $normal_currency);
$current_price = convertCurrency(round($current_price), $current_currency);
}

$savings = 0.0;
if ($current_price != 0.0 && $normal_price != 0.0) $savings = ceil(($normal_price - $current_price) * 100.0 / $normal_price);
//$savings = ceil(100.0 * ($normal_price / $current_price) - 100.0);

$result = array();
$result['primary_price'] = $primary_price;
$result['primary_currency'] = $primary_currency;
$result['normal_price'] = $normal_price;
$result['normal_currency'] = $normal_currency;
$result['secondary_price'] = $secondary_price;
$result['secondary_currency'] = $secondary_currency;
$result['current_price'] = $current_price;
$result['current_currency'] = $current_currency;
$result['savings'] = $savings;
return $result;
 */
}

function fetchBulkPricing($bulk_id, $wanted_currency = "USD")
{
    $bulk = bulkFromDatabase($bulk_id);
    $currency = strtoupper(substr($wanted_currency, 0, 3));

    $primary_price = convertCurrency(
        $bulk["bulk_offer"] / ("AUD" == $bulk["bulk_currency"] ? 1.1 : 1.0),
        $bulk["bulk_currency"]
    );
    $primary_currency = $bulk["bulk_currency"];

    $normal_price = convertCurrency(
        $bulk["bulk_online"],
        $bulk["bulk_currency"]
    );
    $normal_currency = $bulk["bulk_currency"];

    $secondary_price = $primary_price;
    $secondary_currency = $primary_currency;

    $current_price = convertCurrency(
        $secondary_price,
        $secondary_currency,
        $currency
    );
    $current_currency = $currency;

    if ("AUD+" == $wanted_currency) {
        $normal_price = bcadd(
            $normal_price,
            bcmul($normal_price, "0.1", 2),
            $bulk["precision"]
        );
        $current_price = bcadd(
            $current_price,
            bcmul($current_price, "0.1", 2),
            $bulk["precision"]
        );
        if (1 == $bulk["section"] || 2 == $bulk["section"]) {
            $excess = bcmod(bcmul("100", $normal_price, 0), "100");
            if (bccomp($excess, "0", 0) > 0) {
                $normal_price = bcadd(
                    convertCurrency(
                        bcmul(
                            "0.01",
                            bcsub(
                                bccomp($excess, "50", 0) < 0 ? "0" : "100",
                                $excess,
                                0
                            ),
                            2
                        ),
                        $current_currency
                    ),
                    $normal_price,
                    $bulk["precision"]
                );
            }

            $excess = bcmod(bcmul("100", $current_price, 0), "100");
            if (bccomp($excess, "0", 0) > 0) {
                $current_price = bcadd(
                    convertCurrency(
                        bcmul(
                            "0.01",
                            bcsub(
                                bccomp($excess, "50", 0) < 0 ? "0" : "100",
                                $excess,
                                0
                            ),
                            2
                        ),
                        $current_currency
                    ),
                    $current_price,
                    $bulk["precision"]
                );
            }
        }
    }

    $savings = 0.0;
    if (0.0 != $current_price && 0.0 != $normal_price) {
        $savings = ceil(
            (($normal_price - $current_price) * 100.0) / $normal_price
        );
    }

    //$savings = ceil(100.0 * ($normal_price / $current_price) - 100.0);

    $result = [];
    $result["primary_price"] = $primary_price;
    $result["primary_currency"] = $primary_currency;
    $result["normal_price"] = $normal_price;
    $result["normal_currency"] = $normal_currency;
    $result["secondary_price"] = $secondary_price;
    $result["secondary_currency"] = $secondary_currency;
    $result["current_price"] = $current_price;
    $result["current_currency"] = $current_currency;
    $result["savings"] = $savings;
    return $result;
}

function estimate_p_height($font_size, $text, $width = 256, $bold = false)
{
    $font_size = floatval($font_size * 0.7656247);
    $font_file = "/home/common/fonts/Arial.ttf";
    if ($bold) {
        $font_file = "/home/common/fonts/Arial Bold.ttf";
    }

    $font_angle = 0.0;
    $result_text = [];
    foreach (explode("\n", $text) as $paragraph) {
        $words = preg_split('/[\\t ]+/', $paragraph);
        $line = [];
        while (count($words)) {
            $word = array_shift($words);
            $test = $line;
            $test[] = $word;
            $test = trim(implode(" ", $test));
            $test = imagettfbbox($font_size, $font_angle, $font_file, $test);
            if (max($test[2], $test[4]) - min($test[0], $test[6]) > $width) {
                $result_text[] = implode(" ", $line);
                $chars = "";
                while (strlen($word) > 0) {
                    $char = substr($word, 0, 1);
                    $word = substr($word, 1);

                    $test = imagettfbbox(
                        $font_size,
                        $font_angle,
                        $font_file,
                        $chars . $char
                    );
                    if (
                        max($test[2], $test[4]) - min($test[0], $test[6]) >
                        $width
                    ) {
                        $result_text[] = $chars;
                        $chars = $char;
                    } else {
                        $chars .= $char;
                    }
                }
                if (strlen($chars)) {
                    $line = [$chars];
                }
            } else {
                $line[] = $word;
            }
        }
        if (count($line)) {
            $result_text[] = implode(" ", $line);
        }
    }

    $rawr = "";
    for ($i = 32; $i < 255; $i++) {
        $rawr .= chr($i);
    }

    $test = imagettfbbox(
        $font_size,
        $font_angle,
        $font_file,
        $rawr . implode("\n", $result_text) . $rawr
    );
    return 0.94539 * max($test[1], $test[3]) - min($test[5], $test[7]);
}

function fancy_currency($html)
{
    return preg_replace(
        '/\\$([0-9]+)\\.([0-9]+)/',
        '<sup style="vertical-align:text-top">\\$</sup>$1.<sup style="vertical-align:text-top">$2</sup>',
        $html
    );
}
?>
