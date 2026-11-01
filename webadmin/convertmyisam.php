<?php

$section_code = 'developer';
include_once __DIR__ . '/common.inc.php';

global $DB;

if ($q1 = $DB->query(sprintf(
    "SHOW DATABASES"
))) {
    while ($row1 = $DB->fetch_assoc($q1)) {
        if ($q2 = $DB->query(sprintf(
            "SHOW TABLE STATUS FROM `%s`",
            $row1['Database']
        ))) {
            while ($row2 = $DB->fetch_assoc($q2)) {
                if ('MyISAM' != $row2['Type']) {
                    if ('ISAM' !== $row1['Type']) {
                        echo '#';
                    }
                    echo htmlspecialchars(sprintf(
                        "ALTER TABLE `%s`.`%s` TYPE = MYISAM;",
                        $row1['Database'],
                        $row2['Name']
                    )) . "<br />\n";
                }
            }
            $DB->free_result($q2);
        }
    }
    $DB->free_result($q1);
}
