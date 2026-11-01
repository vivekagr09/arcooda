<?php
/**
 * Common setup for template 9
 *
 * Expects:
 *  $item_settings - settings for the template, to override if necessary
 * Returns:
 *  $sections - A list of sections
 *  $ranks - A list of sections by rank
 */

$sections = array(
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
	"latest_offers" => array(
		"title" => "New Arrivals",
		"options" => "summary_brief_full_detailed",
		"defaults" => array("enable" => "n", "option" => "detailed", "qty" => "5"),
	),
	"arcade_machines" => array(
		"title" => "Arcade Machines - Recent Additions",
		"options" => "summary_brief_full",
		"defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
	),
	"arcade_boards" => array(
		"title" => "Arcade Game Boards - Recent Additions",
		"options" => "summary_brief_full",
		"defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
	),
	"featured_products" => array(
		"title" => "Featured Products",
		"options" => "summary_brief_full",
		"defaults" => array("enable" => "n", "option" => "summary", "qty" => "2"),
	),
	"arcade_parts" => array(
		"title" => "Arcade Parts - Recent Additions",
		"options" => "summary_brief_full",
		"defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
	),
	"arcade_merchandise" => array(
		"title" => "Merchandise - Recent Additions",
		"options" => "summary_brief_full",
		"defaults" => array("enable" => "n", "option" => "brief", "qty" => "10"),
	),
	"bulk_offers" => array(
		"title" => "Bulk Deals",
		"options" => "summary_brief_full_detailed",
		"defaults" => array("enable" => "n", "option" => "full", "qty" => "1"),
	),
	/*
	"packages" => array(
		"title" => "Bulk Deals",
		"options" => "summary_brief_full",
		"defaults" => array("enable" => "n", "option" => "full", "qty" => "1"),
	),
	*/
	"news" => array(
		"title" => "News",
		"options" => "brief_full",
		"defaults" => array("enable" => "n", "option" => "brief", "qty" => "2"),
	),
	"promotion" => array(
		"title" => "Special Promotion",
		"options" => "summary_brief_full_detailed",
		"defaults" => array("enable" => "n", "option" => "brief", "qty" => "1"),
	),
	"specials" => array(
		"title" => "Specials",
		"options" => "summary_brief_full",
		"defaults" => array("enable" => "n", "option" => "summary", "qty" => "3"),
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
);
?>