<?php
/**
 * Common setup for template 1
 *
 * Expects:
 *  $item_settings - settings for the template, to override if necessary
 * Returns:
 *  $sections - A list of sections
 *  $ranks - A list of sections by rank
 */

$sections = array(
    "paragraph"          => array(
        "title"    => "Welcome",
        "options"  => "",
        "defaults" => array("enable" => "y", "value" => "Dear [FIRST_NAME],\n\n\n\nBest Regards,\n[STAFF_NAME]"),
    ),
    "banner"             => array(
        "title"    => "Banner",
        "options"  => "header",
        "defaults" => array("enable" => "n", "option" => "header", "qty" => "1"),
    ),
    "text_offers"        => array(
        "title"    => "Special Offer",
        "options"  => "full",
        "defaults" => array("enable" => "n", "option" => "full", "qty" => "6"),
    ),
    "featured_products"  => array(
        "title"    => "Feature Product",
        "options"  => "brief_twin_full",
        "defaults" => array("enable" => "n", "option" => "full", "qty" => "3"),
    ),
    "promotion_products" => array(
        "title"    => "Promotion",
        "options"  => "brief_twin_full",
        "defaults" => array("enable" => "n", "option" => "brief", "qty" => "8"),
    ),
    "news"               => array(
        "title"    => "News",
        "options"  => "brief_twin_full",
        "defaults" => array("enable" => "n", "option" => "twin", "qty" => "2"),
    ),
    "wanted"             => array(
        "title"    => "Wanted Products",
        "options"  => "normal_footer",
        "defaults" => array("enable" => "n", "option" => "normal", "qty" => "16"),
    ),
    "bottom_banner"      => array(
        "title"    => "Bottom Banner",
        "options"  => "footer",
        "defaults" => array("enable" => "n", "option" => "footer", "qty" => "2"),
    ),

    /*
"paragraph" => array(
"title" => "Welcome",
"options" => "",
"defaults" => array("enable" => "y", "value" => "Dear [FIRST_NAME],\n\n\n\nBest Regards,\n[STAFF_NAME]"),
),
"specials" => array(
"title" => "Products On Sale",
"options" => "brief", //"twin_brief_full",
"defaults" => array("enable" => "n", "option" => "brief", "qty" => "4"),
),
"coming_soon" => array(
"title" => "Coming Soon",
"options" => "twin_brief_full",
"defaults" => array("enable" => "n", "option" => "twin", "qty" => "4"),
),
"top_sellers" => array(
"title" => "Top Sellers",
"options" => "twin_brief_full",
"defaults" => array("enable" => "n", "option" => "twin", "qty" => "4"),
),
"latest_offers" => array(
"title" => "Latest Products",
"options" => "brief", //"twin_brief_full_detailed",
"defaults" => array("enable" => "n", "option" => "brief", "qty" => "4"),
),
"latest_sections" => array(
"title" => "Latest Sections",
"options" => "twin_brief_full_detailed",
"defaults" => array("enable" => "n", "option" => "detailed", "qty" => "4"),
),
"arcade_machines" => array(
"title" => "Arcade Machines - Recent Additions",
"options" => "twin_brief_full",
"defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
),
"arcade_boards" => array(
"title" => "Arcade Game Boards - Recent Additions",
"options" => "twin_brief_full",
"defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
),
"arcade_parts" => array(
"title" => "Arcade Parts - Recent Additions",
"options" => "twin_brief_full",
"defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
),
"arcade_merchandise" => array(
"title" => "Merchandise - Recent Additions",
"options" => "twin_brief_full",
"defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
),
"bulk_offers" => array(
"title" => "Bulk Deals",
"options" => "twin_brief_full_detailed",
"defaults" => array("enable" => "n", "option" => "full", "qty" => "1"),
),
"packages" => array(
"title" => "Bulk Deals",
"options" => "twin_brief_full",
"defaults" => array("enable" => "n", "option" => "full", "qty" => "1"),
),
"disclaimer" => array(
"title" => "Disclaimer",
"options" => "",
"defaults" => array("enable" => "n", "value" => ""),
),
 */
);
