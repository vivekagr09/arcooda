<?php
//Restrict to current website if not coinopexpress.com
if ('www.coinopexpress.com' != $_SERVER['HTTP_HOST']) {
    $query = $DB->query("SELECT id,url FROM `highwaygroup`.websites");
    while ($row = $DB->fetch_assoc($query)) {
        $match = parse_url($row['url']);
        $match = explode('.', $match['host']);
        while (in_array(reset($match), array('www', 'local'))) {
            array_shift($match);
        }

        $match = implode('.', $match);
        if (strpos(strtolower($_SERVER['HTTP_HOST']), strtolower($match)) !== false) {
            $_GET['site_id'] = $row['id'];
        }

    }
}

$posted = array_merge($_GET, $_POST);

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || trim($value) == '') {
            unset($get_vars[$key]);
        }
    }
}
