<?php

include_once __DIR__ . '/common.inc.php';

global $DB;

if ($query = $DB->query(sprintf(
    "SELECT * FROM `mailinglist_foo` WHERE `id` > 7459"
))) {
    echo "<table>\n";
    while ($line = $DB->fetch_assoc($query)) {
        echo "\t<tr>\n";
        foreach ($line as $col_value) {
            echo "\t\t<td>" . htmlspecialchars($col_value) . "</td>\n";
        }
        echo "\t</tr>\n";
    }
    echo "</table>\n";
    $DB->free_result($query);
}
