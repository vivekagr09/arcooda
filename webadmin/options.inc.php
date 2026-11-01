<?php
/**
 * @var mixed[] $posted
 */
?><?php

global $_GLOBALS;
global $DB;
global $posted;

if (!isset($posted) && !isset($get_vars)) {
    $posted = array_merge($_GET, $_POST);
    if (is_array($posted)) {
        array_walk_recursive($posted, function (&$v, $k) {
            $v = strtr($v, [
                "\r\n" => "\n",
                "\n\r" => "\n",
                "\r"   => "\n",
            ]);
        });
    }

    $get_vars = $posted;
    if (count($get_vars) > 0) {
        foreach ($get_vars as $key => $value) {
            if (is_array($value) || trim($value) == '') {
                unset($get_vars[$key]);
            }
        }
    }
}

if (!array_key_exists('schema', $_GLOBALS)) {
    $_GLOBALS['schema'] = array(
        'itemtype' => 'WebPage',
    );
}

/**
 * @fixme The site_options should be replaced with calls to options module
 */
if (!array_key_exists('site_options', $_GLOBALS)) {
    $site_options = [];

    if ($temp = $DB->query(sprintf(
        "SELECT `variable`, `value` FROM `options`"
    ))) {
        while ($row = $DB->fetch_assoc($temp)) {
            $site_options[strtoupper($row['variable'])] = $row['value'];
            $site_options[strtolower($row['variable'])] = $row['value'];
        }
        $DB->free_result($temp);
    }
    unset($temp);

    $sql_query = $DB->query("SELECT `variable`, `value` FROM `new_site_options`");
    while ($row = $DB->fetch_assoc($sql_query)) {
        $site_options[strtoupper($row['variable'])] = $row['value'];
    }

    $DB->free_result($sql_query);

    $_GLOBALS['site_options'] = $site_options;
    unset($site_options);
}
if (!isset($_GLOBALS['criteria_options'])) {
    $criteria_options           = [];
    $criteria_options['global'] = [
        'active'    => 'y',
        'active_id' => $_GLOBALS['site_settings']['site_id'],
        'site_id'   => $_GLOBALS['site_settings']['site_id'],

        'active_' . $_GLOBALS['site_settings']['site_id']  => 'y',
        '_active_' . $_GLOBALS['site_settings']['site_id'] => 'y',
        'site_' . $_GLOBALS['site_settings']['site_id']    => 'y',
        '_site_' . $_GLOBALS['site_settings']['site_id']   => 'y',
    ];
    //about
    //association
    $criteria_options['banner'] = array_merge($criteria_options['global'], []);
    $criteria_options['brand']  = array_merge($criteria_options['global'], []);
    //bulk
    $criteria_options['category']    = array_merge($criteria_options['global'], []);
    $criteria_options['distributor'] = array_merge($criteria_options['global'], []);
    //game
    //glossary
    $criteria_options['invoice'] = array_merge($criteria_options['global'], []);
    //license
    //manufacturer
    $criteria_options['member'] = array_merge($criteria_options['global'], []);
    //movie
    $criteria_options['news']       = array_merge($criteria_options['global'], []);
    $criteria_options['newsletter'] = array_merge($criteria_options['global'], []);
    $criteria_options['page']       = array_merge($criteria_options['global'], []);
    //partner
    $criteria_options['product']  = array_merge($criteria_options['global'], []);
    $criteria_options['products'] = $criteria_options['product'];
    //softjob
    //supplier
    //tradeshow
    //updates
    $_GLOBALS['criteria_options'] = $criteria_options;
    unset($criteria_options);
}
if (array_key_exists('REMOTE_USER', $_SERVER) && moduleLoad('staff')) {
    $_GLOBALS['staff']['staff_id'] = staffFromUser($_SERVER['REMOTE_USER']);
    $_GLOBALS['staff']             = staffFromDatabase($_GLOBALS['staff']['staff_id']);
    $_GLOBALS['staff']['staff_id'] = $_GLOBALS['staff']['id'];
}

// @fixme Remove this once permissions are fixed
if (0 === strpos($_SERVER['REQUEST_URI'], '/webadmin/')) {
    include_once __DIR__ . '/users/auth.php';
}
