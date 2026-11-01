<?php

include_once __DIR__ . '/common.inc.php';

global $DB;

/* Printing results in HTML */
if ($query = $DB->query(sprintf(
    "SELECT `id`, `image`, `last_modified` FROM `products`"
))) {
    while ($line = $DB->fetch_assoc($query)) {
        if (isset($line['image']) && ("" != $line['image'])) {
            echo htmlspecialchars(sprintf(
                "UPDATE `products` SET `image` = '%s' WHERE `id` = '%s' LIMIT 1",
                $DB->escape($line['image']),
                $DB->escape($line['id'])
            )) . "<br />\n";
        }
    }
    $DB->free_result($query);
}
