<?php
/***********************
 * Supplier Search Magic
 * Filename: supplier_search.php
 ***********************
 * Purpose:
 *    All actions related to searching for suppliers
 * Configuration Variables used:
 *    $config['db_prefix']
 *    $config['template_path']
 * Functions defined:
 ***********************/

include_once __DIR__ . '/common.inc.php';

moduleLoad('template');

require_once "spo_config.php";

require_once "functions/country.inc.php"; // Template Magic File

switch ($_REQUEST['action']) {
    case "results":
        doSearch();
        break;
    default:
        showSearchForm();
        break;
}

function showSearchForm()
{
    global $config;
    $page = new Template($config['template_path']);
    $page->set("countries", makeCountryArray());
    echo $page->fetch("ssearch_form.tpl.php");
}

function doSearch()
{
    // Grab globals
    global $DB, $config;

    // Get data from request
    $name    = $_REQUEST['name'];
    $type    = $_REQUEST['type'];
    $country = $_REQUEST['country'];

    $country_list = getCountryList(false);

    // Look through the DB
    $search_sql = "SELECT supplier_id, supplier_name, supplier_name_ch, country_code FROM
				{$config['db_prefix']}suppliers
			WHERE
				supplier_name LIKE '%{$name}%' OR
				supplier_name_ch LIKE '%{$name}%'";
    if ($country) {
        $search_sql .= " AND country_code='$country'";
    }

    $search_query = $DB->query($search_sql);
    while ($row = $DB->fetch_array($search_query)) {
        if ($type) {
            // Check that this supplier supplies this type
            $DB->query("SELECT * FROM {$config['db_prefix']}suppliers_supplytypes
							WHERE supplier_id='{$row['supplier_id']}' AND supply_type='$type'");
            if (!$DB->num_rows()) {
                continue;
            }
        }

        // Figure out where they are
        $row['country_name'] = $country_list[$row['country_code']] ?? $row['country_code'];

        // Figure out what they do
        $supps     = "";
        $supps_sql = "SELECT * FROM {$config['db_prefix']}suppliers_supplytypes WHERE supplier_id='{$row['supplier_id']}'";
        $DB->query($supps_sql);
        while ($srow = $DB->fetch_array()) {
            switch ($srow['supply_type']) {
                case 1:
                    $supps .= "Machines, ";
                    break;
                case 2:
                    $supps .= "PCB's, ";
                    break;
                case 3:
                    $supps .= "Parts, ";
                    break;
                case 4:
                    $supps .= "Merchandise, ";
                    break;
                case 5:
                    $supps .= "Services, ";
                    break;
            }
        }
        $row['supps'] = substr($supps, 0, -2);

        // Stick into the result array
        $search_res[] = $row;
    }

    // Show results form
    $page = new Template($config['template_path']);
    $page->set("results", $search_res);
    echo $page->fetch("ssearch_result.tpl.php");
}
