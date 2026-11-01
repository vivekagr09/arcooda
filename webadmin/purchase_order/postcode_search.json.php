<?php
include_once __DIR__ . "/common.inc.php";

$action = $posted['action'];
$q      = $posted['q'];

$split = array_unique(preg_split("/[^a-z0-9]+/", strtolower($q)));
sort($split);
$whereOR = array();
foreach (array("`Pcode`", "`Locality`", "`State`") as $k) {
    foreach ($split as $v) {
        if (trim($v) != "") {
            $whereOR[] = $k . " LIKE " . formatSQLString('%' . $v . '%');
        }
    }
}

$results = array();
if (count($whereOR)) {
    $scores = array();
    $query  = $DB->query("SELECT * FROM `post_codes` WHERE " . implode(" OR ", $whereOR));
    while ($row = $DB->fetch_assoc($query)) {
        $score1 = 0;
        $score2 = 0;
        foreach (array("Pcode", "Locality", "State") as $k) {
            foreach ($split as $v) {
                if (trim($v) != "" && stripos($row[$k], trim($v)) !== false) {
                    $score1++;
                    $score2 += stripos($row[$k], trim($v));
                }
            }
        }

        $scores[$score1][$score2][] = $row;
    }
    $DB->free_result($query);

    ksort($scores);
    foreach (array_reverse($scores) as $v1) {
        ksort($v1);
        foreach ($v1 as $v2) {
            ksort($v2);
            foreach ($v2 as $row) {
                $results[] = $row;
            }

        }
    }
}

if (array_key_exists("limit", $posted) && count($results)) {
    $results = reset(array_chunk($results, $posted["limit"]));
}

header("Content-Type:application/json; charset=utf-8");
jecho($results);
