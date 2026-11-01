<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

//header('Content-Type: text/plain');

$limit = 20;
if ($posted["limit"] > 0) {
    $limit = intval($posted["limit"]);
}

$timestamp = formatSQLDateTime(mktime(-672, 0, 0));

$visitor_list = array($_GLOBALS["visitor"]["id"]);
if ("" != $posted["visitor_id"]) {
    $visitor_list = preg_split("/[^0-9]+/", $posted["visitor_id"]);
}

if ("txt" == $posted["style"]) {
    //header("Content-Type: text/comma-separated-values");
    header("Content-Type: application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename="visitor_' . implode("_", $visitor_list) . '_' . date("YmdHis") . '.csv"');

    echo "Request URI, Referer, Timestamp, Data\r\n";
    $query = $DB->query("SELECT `request_uri` AS `to_uri`, `referer` AS `from_uri`, `timestamp`, `post` FROM `highwaygroup`.`visitor_trace` WHERE `visitor_id` IN(" . implode(", ", $visitor_list) . ") ORDER BY `timestamp` ASC");
    while ($row = $DB->fetch_assoc($query)) {
        $fields = array();
        foreach ($row as $k => $v) {
            $fields[] = (preg_match('/["\0-\x1f]/', $v)) ? ('"' . str_replace('"', '"""', $v) . '"') : $v;
        }

        echo implode(",", $fields) . "\r\n";
    }
    $DB->free_result($query);
    die();
}

ob_start();
?>
digraph referals {
<?php
//    rankdir=LR;
$url_list = array();
$check    = array();
$query    = $DB->query("SELECT COUNT(*) AS `hit_count`, `request_uri` AS `to_uri`, `referer` AS `from_uri`, `method` FROM `highwaygroup`.`visitor_trace` WHERE `visitor_id` IN(" . implode(", ", $visitor_list) . ") AND `timestamp` >= " . $timestamp . " GROUP BY `request_uri`, `referer`, `method` ORDER BY `timestamp` DESC");
while ($row = $DB->fetch_assoc($query)) {
    $from   = $row['from_uri'];
    $to     = $row['to_uri'];
    $method = $row["method"];
    //$from=reset(explode('?',$from));
    //$to=reset(explode('?',$to));
    //while (substr($from,-1)=='/') $from=substr($from,0,-1);
    //while (substr($to,-1)=='/') $to=substr($to,0,-1);
    $k1 = array_search($from, $url_list);
    if (false === $k1) {
        $url_list[$k1 = count($url_list)] = $from;
    }

    $k2 = array_search($to, $url_list);
    if (false === $k2) {
        $url_list[$k2 = count($url_list)] = $to;
    }

    $ext = end(explode('.', $to));
    $row = "\t" . '"' . $from . '" -> "' . $to . '" [label="' . $row['hit_count'] . '"' . (("redirect" == $method) ? ',style=dotted' : '') . "];\n";
    $key = md5($row);

    //Avoid blanks
    if ('' != $from && '' != $to) {
        //Avoid duplicates
        //if (!array_key_exists($key, $check)) {
        //Avoid phplive
        if (!in_array($to, array('/phplive/js/status_image.php'))) {
            //Avoid webadmin
            if (substr($from, 0, 10) != '/webadmin/') {
                //Limit to webadmin
                //if (substr($from,0,10)=='/webadmin/') {
                //Avoid scripts
                if (!in_array($ext, array('css', 'js'))) {
                    //Avoid images
                    if (!in_array($ext, array('gif', 'png', 'jpg'))) {
                        //Limit to deepening links
                        //if (substr_count($from, '/') <= substr_count($to, '/'))  {
                        $path_list[$k1][$k2] = $row;
                        //$rows[]=$row;
                        echo $row;
                    }
                }
            }
        }
    }

    $check[$key] = true;
}
//foreach ($url_list as $url) if ($url != "") echo "\t" . '"' . $url . '"' . ";\n";
?>
}
<?php

switch ($_GET['style']) {
    case 'dot':
        header('Content-Type: text/plain; charset=utf-8');
        passthru('echo ' . escapeshellarg(ob_get_clean()));
        break;

    case 'svg':
        header('Content-Type: image/svg+xml');
        passthru('echo ' . escapeshellarg(ob_get_clean()) . ' | dot -Tsvg');
        break;

    case 'png':
    default:
        header('Content-Type: image/png');
        passthru('echo ' . escapeshellarg(ob_get_clean()) . ' | dot -Tpng');
        break;
}
