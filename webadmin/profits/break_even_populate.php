<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/templates/profits_header.tpl.php';

$start_month                = 3;
$start_year                 = 2004;
list($end_month, $end_year) = explode('-', date("n-Y"));

$month = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$list  = array();
$plist = array();
while ($start_year <= $end_year) {
    if ($start_year == $end_year && $start_month > $end_month) {
        break;
    }

    $key = $month[$start_month] . ' ' . $start_year;

    $start            = mktime(0, 0, 0, $start_month, 1, $start_year);
    $end              = mktime(23, 59, 59, $start_month, cal_days_in_month(CAL_GREGORIAN, $start_month, $start_year), $start_year);
    $month_start_time = mktime(0, 0, 0, idate('m', $start), idate('d', $start) + 1 - idate('w', $start), idate('Y', $start));
    $week_end_time    = mktime(0, 0, 0, idate('m', $month_start_time), idate('d', $month_start_time) + 7 - idate('w', $month_start_time), idate('Y', $month_start_time));

    $add  = $month_start_time . '-' . $week_end_time;
    $padd = date(_('jS F, Y'), $month_start_time) . ' - ' . date(_('jS F, Y'), $week_end_time);
    if (!in_array($add, $list)) {
        $list[]  = $add;
        $plist[] = $padd;
    }
    while ($week_end_time < $end) {
        $week_start_time = mktime(0, 0, 0, idate('m', $week_end_time), idate('d', $week_end_time) + 1 - idate('w', $week_end_time), idate('Y', $week_end_time));
        $week_end_time   = mktime(0, 0, 0, idate('m', $week_start_time), idate('d', $week_start_time) + 7 - idate('w', $week_start_time), idate('Y', $week_start_time));
        $add             = $week_start_time . '-' . $week_end_time;
        $padd            = date(_('jS F, Y'), $week_start_time) . ' - ' . date(_('jS F, Y'), $week_end_time);
        if (!in_array($add, $list)) {
            $list[]  = $add;
            $plist[] = $padd;
        }
        //$list[] = date(_('jS F, Y'),$week_start_time).' - '.date(_('jS F, Y'),$week_end_time);
    }
    $start_month++;
    if ($start_month > 12) {
        $start_month = 1;
        $start_year++;
    }
}

//now need to add all the previous weeks into the database...
//firstly get the break-even value...
$qry        = $DB->query_first("SELECT value FROM `highwaygroup`.`values` WHERE name=" . $DB->escape("DEFAULT_WEEKLY_COST"));
$break_even = ('' != $qry['value']) ? $qry['value'] : 7500;
foreach ($list as $key => $times) {
    list($start, $end) = explode('-', $times);
    $row               = $DB->query_first("SELECT `id` FROM `break_even` WHERE `start` = $start AND `end` = $end", MYSQLI_ASSOC);
    if (!$row) {
        echo $start . ', ' . $end . '<br />';
        $DB->query("INSERT INTO `break_even` (`start`, `end`, `weekly_cost`) VALUES (" . $start . ", " . $end . ", " . $DB->escape($break_even) . ")");
    }
}

include_once __DIR__ . '/templates/profits_footer.tpl.php';
