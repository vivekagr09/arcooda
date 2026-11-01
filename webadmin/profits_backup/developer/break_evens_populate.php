<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

header("Content-Type:text/plain; charset=utf-8");

$ranges = array();

for ($i = 0; $i < 520; $i++) {
    $ranges[mktime(0, 0, 0, 3, $i * 7 + 1, 2004)] = mktime(23, 59, 59, 3, $i * 7 + 7, 2004);
}

foreach ($ranges as $k => $v) {
    if ($k > time()) {
        unset($ranges[$k]);
    }
}

ksort($ranges);

$breakevens = array();
$query      = $DB->query("SELECT * FROM `break_even`");
while ($row = $DB->fetch_assoc($query)) {
    $breakevens[$row["start"]] = $row["weekly_cost"];
}

$DB->free_result($query);

$state = -1.0;
foreach ($ranges as $k => $v) {
    if (array_key_exists($k, $breakevens)) {
        $state = $breakevens[$k];
    } elseif ($state >= 0.0) {
        $insert[] = sprintf("(%d, %d, %0.2f)", $k, $v, $state);
    }
}
if (count($insert)) {
    echo "INSERT INTO `break_even` (`start`, `end`, `weekly_cost`) VALUES\n" . implode(",\n", $insert) . ";";
} else {
    echo "#NO CHANGES";
}
