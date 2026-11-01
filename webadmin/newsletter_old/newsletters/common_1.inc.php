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
    "specials"           => array(
        "title"    => "Products On Sale",
        "options"  => "brief", //"summary_brief_full",
        "defaults" => array("enable" => "n", "option" => "brief", "qty" => "5"),
    ),
    'wanted'             => array(
        'title'    => 'Wanted',
        'options'  => '',
        'defaults' => array('enable' => 'n', 'option' => '', 'qty' => '5'),
    ),
    "coming_soon"        => array(
        "title"    => "Coming Soon",
        "options"  => "summary_brief_full",
        "defaults" => array("enable" => "n", "option" => "summary", "qty" => "5"),
    ),
    "top_sellers"        => array(
        "title"    => "Top Sellers",
        "options"  => "summary_brief_full",
        "defaults" => array("enable" => "n", "option" => "summary", "qty" => "5"),
    ),
    "latest_offers"      => array(
        "title"    => "Latest Products",
        "options"  => "brief", //"summary_brief_full_detailed",
        "defaults" => array("enable" => "n", "option" => "brief", "qty" => "5"),
    ),
    "news"               => array(
        "title"    => "News",
        "options"  => "brief_full",
        "defaults" => array("enable" => "n", "option" => "brief", "qty" => "5"),
    ),
    "latest_sections"    => array(
        "title"    => "Latest Sections",
        "options"  => "summary_brief_full_detailed",
        "defaults" => array("enable" => "n", "option" => "detailed", "qty" => "5"),
    ),
    "featured_products"  => array(
        "title"    => "Featured Products",
        "options"  => "brief", //"summary_brief_full",
        "defaults" => array("enable" => "n", "option" => "brief", "qty" => "5"),
    ),
    "arcade_machines"    => array(
        "title"    => "Latest Full Machines",
        "options"  => "summary_brief_full",
        "defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
    ),
    "arcade_boards"      => array(
        "title"    => "Latest PCBs & Kits",
        "options"  => "summary_brief_full",
        "defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
    ),
    "arcade_merchandise" => array(
        "title"    => "Merchandise - Recent Additions",
        "options"  => "summary_brief_full",
        "defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
    ),
    "arcade_parts"       => array(
        "title"    => "Latest Spare Parts",
        "options"  => "summary_brief_full",
        "defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
    ),

    /*
"large_banner" => array(
"title" => "Large Banner",
"options" => "wide_thin",
"defaults" => array("enable" => "y", "option" => "wide", "qty" => "1"),
),
"paragraph" => array(
"title" => "Welcome",
"options" => "",
"defaults" => array("enable" => "y", "value" => "Dear [FIRST_NAME],\n\n\n\nBest Regards,\n[STAFF_NAME]"),
),
"bulk_offers" => array(
"title" => "Bulk Deals",
"options" => "summary_brief_full_detailed",
"defaults" => array("enable" => "n", "option" => "full", "qty" => "1"),
),
"packages" => array(
"title" => "Bulk Deals",
"options" => "summary_brief_full",
"defaults" => array("enable" => "n", "option" => "full", "qty" => "1"),
),
"promotion" => array(
"title" => "Special Promotion",
"options" => "summary_brief_full_detailed",
"defaults" => array("enable" => "n", "option" => "brief", "qty" => "1"),
),
"disclaimer" => array(
"title" => "Disclaimer",
"options" => "",
"defaults" => array("enable" => "n", "value" => ""),
),
"bottom_banner" => array(
"title" => "Bottom Banner",
"options" => "wide_thin",
"defaults" => array("enable" => "n", "option" => "wide", "qty" => "1"),
),
 */
);
