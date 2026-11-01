<?php
/**
 *    Module: Product Builder
 *
 *    common.inc.php
 *
 *    Module loaded:
 *    - Product Builder
 *    - Product
 *
 *    @author Kelvin (khtat@highwaygroup.net)
 *    @since  29/12/2017
 */

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

// Load module
moduleOverride("product_builder", $_SERVER['DOCUMENT_ROOT'] . "/webadmin/product_builder_new/functions/ProductBuilder.inc.php");
moduleLoad("product_builder");

moduleOVerride("product", $_SERVER['DOCUMENT_ROOT'] . "/webadmin/products/functions/query_products.inc.php");
moduleLoad("product");

moduleLoad("member");
moduleLoad("invoice");

if (!function_exists("panel_title")) {
    function panel_title($text)
    {
        static $img = null;
        if (is_null($img)) {
            $img = file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/headers");
        }

        if ($img) {
            echo '<img src="' . hecho("/images/headers/" . urlencode($text) . ".png", 1) . '" alt="' . hecho($text, 1) . ' :" />';
        } else {
            echo '<span class="panel-title-text">' . hecho($text, 1) . '</span>';
        }

    }
}

if (!function_exists("generateAlternateText")) {
    function generateAlternateText($content)
    {
        $basename = "http://" . $_SERVER['HTTP_HOST'] . "/";

        //Get base address
        $matches = array();
        if (preg_match('/<base[^>]* href="([^"]+)"[^>]* \\/>/siU', $content, $matches)) {
            $basename = $matches[1];
        }

        //Remove html head
        $content = preg_replace('/<head[^>]*>.*<\\/head>/siU', '', $content);

        //Add extra newlines
        $content = preg_replace('/<br \\/>/siU', "\n", $content);
        $content = preg_replace('/<hr \\/>/siU', "\n----------------------------------------\n", $content);

        //Replace images
        $content = preg_replace('/<img[^>]* alt="([^"]+)"[^>]*\\/>/siU', '\\1', $content);

        //Replace html links
        $content = preg_replace('/<a[^>]* href="([^"]+:[^"]+)"[^>]*>(.*)<\\/a>/siU', '\\2 -> \\1', $content);
        $content = preg_replace('/<a[^>]* href="([^"]+)"[^>]*>(.*)<\\/a>/siU', '\\2 -> ' . rtrim($basename, '/') . '\\1', $content);

        //Remove html tags
        $content = strip_tags($content);

        //Cleanup html entities
        $content = html_entity_decode($content, ENT_COMPAT, 'UTF-8');

        //Cleanup whitespace
        $content = strtr($content, array("\r\n" => "\n"));
        $content = strtr($content, array("\r" => "\n"));
        $content = strtr($content, array("\t" => " "));
        $content = preg_replace('/  +/', ' ', $content);

        //Remove Duplicate Lines
        $explode = explode("\n", $content);
        foreach ($explode as $k => $v) {
            $explode[$k] = trim($v);
        }

        $found = array();
        $prev  = '';
        foreach ($explode as $k => $v) {
            if (('' == $v && '' == $prev) || (strpos($v, 'http://') !== false && in_array($v, $found))) {
                unset($explode[$k]);
                $v = '';
            } else {
                $found[] = $v;
            }

            $prev = $v;
        }
        $content = implode("\n", $explode);

        return wordwrap($content);
    }
}

$site_list = array(1 => 'hg', 2 => 'he', 9 => 'cnv', 16 => 'car', 20 => 'asp');
if ($_GLOBALS['site_settings']['site_id'] > 0 && isset($site_list[$_GLOBALS['site_settings']['site_id']])) {
    $_GET['site_id'] = $_GLOBALS['site_settings']['site_id'];
}

$all_staff_list = array();
$query          = $DB->query("SELECT `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name`, `active` FROM `highwaygroup`.`users` ORDER BY `first_name` ASC");
while ($row = $DB->fetch_assoc($query)) {
    $all_staff_list[$row['id']] = $row['name'];
}

$DB->free_result($query);

$staff_id = $_GLOBALS['staff']['staff_id'];
$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

$country_list = getCountryList(false);

session_start();
