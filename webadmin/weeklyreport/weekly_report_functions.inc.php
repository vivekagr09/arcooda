<?php
include_once __DIR__ . '/common.inc.php';

function choose_userdb()
{
    global $DB, $from, $to;
    $visible = $DB->query_first(sprintf(
        "SELECT `visible_from` FROM `highwaygroup`.`weekly_report_websites` WHERE `from` = %d AND `to` = %d LIMIT 1",
        $from,
        $to
    ), MYSQLI_ASSOC);
    $dbname = $DB->query_first(sprintf(
        "SELECT `DB` FROM `highwaygroup`.`websites` WHERE `id` = %d LIMIT 1",
        $visible['visible_from']
    ), MYSQLI_ASSOC);
    return $dbname['DB'];
}
