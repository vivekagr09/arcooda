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
$_GLOBALS["load_ext_options"] = false;
$_GLOBALS["load_ext_modules"] = false;

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

// Load module

moduleOverride("survey", "/home/arcooda/public_html/webadmin/survey/classes/Survey.php");
moduleLoad("survey");

moduleOverride("product", $_SERVER['DOCUMENT_ROOT'] . "/webadmin/products/functions/query_products.inc.php");
moduleLoad("product");

moduleOverride("member", "/home/arcooda/public_html/webadmin/new_members/functions/query_member.inc.php");
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

// Staffs
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

// Get all websites from database
$ig_websites = array( // Websites to be ignored
    3, // Coinopexpress
    5, // Channel Beat
    10, // Josifovski Admin
    12, // Freight Calculator
    15, // Fortunewave
    21, // Great Delta
    23, // Radikal Darts Australia
    27, // SVOS
    28, // Destiny Bridal
    29, // Images By Angelo
    32, // FotoInc
    33, // Highway Media
);

$ig_websites_str = implode(",", $ig_websites); // Stringify ig_websites

/*$websitesQuery = $DB->query("SELECT * FROM `highwaygroup`.`websites` WHERE `id` NOT IN ({$ig_websites_str})");
$websites = array();
while($row = $DB->fetch_assoc($websitesQuery)) $websites[] = $row;*/

// Manual added websites
$websites = array(
    "www.arcooda.com/",
);

// Session ID
session_start();

function generateSuccessToken()
{
    $_SESSION['success'] = md5(uniqid());
    return $_SESSION['success'];
}

function notifySuccess()
{
    global $get_vars;

    if (isset($get_vars['success']) === true && empty($get_vars['success']) === false &&
        isset($_SESSION['success']) === true && empty($_SESSION['success']) === false && $get_vars['success'] == $_SESSION['success']) {

        unset($get_vars['success']);
        unset($_SESSION['success']);

        return true;
    }

    return false;
}

function pagination($offset, $limit, $total)
{

    global $get_vars;

    $link = $get_vars;

    $c = $offset + 1; // Current showing start offset
    $e = (($offset + $limit) > $total) ? $total : ($offset + $limit); // Current end results

    $html = "<br>";

    $html .= "<div style='text-align: center;'><b>Showing : {$c} - {$e} of {$total}</b>";

    for ($i = 0; $i < $total; $i += $limit) {
        $ic = $i + 1; // Start offset
        $ie = (($i + $limit) > $total) ? $total : ($i + $limit);

        if ($offset == $i) {
            $html .= " | <span>{$ic} - {$ie}</span>";
        } else {
            $link["offset"] = $i;

            $html .= " | <a href='?" . http_build_query($link) . "'>{$ic} - {$ie}</a>";
        }
    }

    $html .= "</div><br>";

    echo $html;
}
