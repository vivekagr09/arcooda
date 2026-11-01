<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

include_once __DIR__ . '/templates/profits_header.tpl.php';

$cur_mon = idate('m');
$cur_yr  = idate('Y');

$year  = intval($posted['year']);
$month = intval($posted['month']);
if (0 != $year) {
    $cur_yr = $year;
}

if (0 != $month) {
    $cur_mon = $month;
}

$start_time = mktime(0, 0, 0, $cur_mon, 1, $cur_yr);
$end_time   = mktime(23, 59, 59, $cur_mon, cal_days_in_month(CAL_GREGORIAN, $cur_mon, $cur_yr), $cur_yr);

$week_start_time = mktime(0, 0, 0, idate('m', $start_time), idate('d', $start_time) + 1 - idate('w', $start_time), idate('Y', $start_time));
$week_end_time   = mktime(23, 59, 59, idate('m', $week_start_time), idate('d', $week_start_time) + 7 - idate('w', $week_start_time), idate('Y', $week_start_time));

$list   = array();
$list[] = array('start_time' => $week_start_time, 'end_time' => $week_end_time);
while ($week_end_time < $end_time) {
    $week_start_time = mktime(0, 0, 0, idate('m', $week_end_time), idate('d', $week_end_time) + 1 - idate('w', $week_end_time), idate('Y', $week_end_time));
    $week_end_time   = mktime(23, 59, 59, idate('m', $week_start_time), idate('d', $week_start_time) + 7 - idate('w', $week_start_time), idate('Y', $week_start_time));

    $list[] = array('start_time' => $week_start_time, 'end_time' => $week_end_time);
}
profitAddMonth($list, $_GLOBALS['site_options']['DEFAULT_WEEKLY_COST']);

/*
$week_start_time = mktime(0, 0, 0, idate('m'), idate('d') + 1 - idate('w'), idate('Y'));
$week_end_time = mktime(0, 0, 0, idate('m', $week_start_time), idate('d', $week_start_time) + 7 - idate('w', $week_start_time), idate('Y', $week_start_time));
profitAddWeek($week_start_time, $week_end_time, $_GLOBALS['site_options']['DEFAULT_WEEKLY_COST']);
 */

if (0 == $month || 0 == $year) {
    list($month, $year) = explode('-', date("n-Y"));
}

$list      = profitGetMonth($month, $year);
$txt_month = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

?>

<?=formatHTMLTitle('<a href="#" onclick="$(\'#break_by_month\').toggle();return false">By Month</a>')?>

<div id="break_by_month"><table class="table_grid" style="width:auto;margin:auto;">
	<tr>
		<th></th>
<?php
for ($j = 1; $j <= 12; $j++) {
?>
		<th><?=date("F", mktime(0, 0, 0, $j, 1))?></th>
<?php
}
?>
	</tr>
<?php
for ($i = 2009; $i <= idate("Y"); $i++) {
?>
	<tr>
		<th><?=$i?></th>
<?php
for ($j = 1; $j <= 12; $j++) {
?>
		<td><a href="?year=<?=$i?>&amp;month=<?=$j?>"><?=date("F Y", mktime(0, 0, 0, $j, 1, $i))?></a></td>
<?php
}
?>
	</tr>
<?php
}
?>
</table></div>

<?=formatHTMLTitle('Break-Even Setup (' . $txt_month[$month] . ' ' . $year . ')')?>

<form action="." method="post">
<input type="hidden" name="action" value="break_even_update" />
<input type="hidden" name="month" value="<?=$month?>" />
<input type="hidden" name="year" value="<?=$year?>" />
<table class="table_grid spacetable" cellpadding="2" style="width:60%">
	<tr>
		<td class="left_title">Default Weekly Costs :</td>
		<td><b><input type="text" name="default_cost" value="<?=$_GLOBALS['site_options']['DEFAULT_WEEKLY_COST']?>" /> AUD</b></td>
	</tr>
</table>
</div>
<br /><br />
<table class="table_grid spacetable" align="center" cellpadding="2">
	<tr>
		<th>Date Range</th>
		<th>Weekly Costs</th>
	</tr>
<?php
$count = 0;
foreach ($list as $id => $array) {
?>
<tr<?=($count % 2 == 1) ? ' class="alt"' : ''?> style="text-align:center;">
	<td><?=date("d/m/Y", $array['start'])?> - <?=date("d/m/Y", $array['end'])?></td>
	<td><input type="text" name="cost[<?=$id?>]" value="<?=$array['weekly_cost']?>" /></td>
</tr>
<?php
$count++;
}
?>
</table>
<div class="text-center"><input type="submit" value="Update Costs" /></div>
</form>
<?php
include_once __DIR__ . '/templates/profits_footer.tpl.php';
