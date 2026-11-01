<pre>It starts...

<?php
include_once __DIR__ . '/common.inc.php';

/****************************************************
 * Stupid script to set basic options for customers *
 ****************************************************/

$stime = time();

$findmem_qry = $DB->query("SELECT `member_id` FROM `highwaygroup`.`new_members`");
while ($row = $DB->fetch_assoc($findmem_qry)) {
    $checkopt_sql = $DB->query_first(sprintf(
        "SELECT * FROM `highwaygroup`.`new_members_survey` WHERE `member_id` = %d",
        $row['member_id']
    ), MYSQLI_ASSOC);
    if (!$checkopt_sql) {
        $DB->query(sprintf(
            "INSERT INTO `highwaygroup`.`new_members_survey` (`member_id`, `option`) VALUES (%d, 5), (%d, 11)",
            $row['member_id'],
            $row['member_id']
        ));
        echo $row['member_id'] . "\n";
    }
}

echo "execution time: " . (time() - $stime) . " seconds";
?>
</pre>
