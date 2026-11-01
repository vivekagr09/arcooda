<?php
/**
 * @var int      $staff_id
 * @var string[] $allowed_sections
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<div id="sectionYearlySummary">
<?php

$grouped_by='yearly';
$timesheet_list=array_reverse(timesheetSearch(array('staff_id'=>$staff_id),mktime(0,0,0,7,1,2004),time()));
$package_list=array();

foreach ($timesheet_list as $timesheet_id)
{
	$timesheet=timesheetFromDatabase($timesheet_id);
	$group_id = idate('Y', $timesheet['start_time']) - ((idate('m', $timesheet['start_time']) > 6) ? 0 : 1);
	foreach (array('net_time','Package','GrossPay','WithHeld','NetPay','BonusPay','SuperPay','Commission','Deductions','Sacrifices') as $key)
		$package_list[$group_id][$key]+=$timesheet[$key];
}
if (count($package_list)>0)
{
	foreach ($package_list as $group_id=>$group)
	{
		$package_list[$group_id]['time_from']=mktime(0,0,0,7,1,$group_id);
		$package_list[$group_id]['time_to']=mktime(0,0,0,7,1,$group_id+1);
	}
	$force_hours=true;
	if (in_array('USERS',$allowed_sections))
		$group_url='/webadmin/timesheets/?staff_id='.$staff_id.'&action=list&year=';
	include $_SERVER['DOCUMENT_ROOT'].'/webadmin/timesheets/templates/timesheet_packagelist.tpl.php';
?>
<br /><br />

<table style="width:auto;">
<?php
}

if (in_array('USERS',$allowed_sections))
{
?>
	<tr>
		<td>
<input type="button" value="Add Timesheet" onclick="javascript:window.open('/webadmin/timesheets/?action=add&staff_id=<?=$staff_id?>');" />
		</td>
		<td>
<input type="button" value="Pay Work Bonus" onclick="javascript:window.open('/webadmin/timesheets/?action=st_payment&staff_id=<?=$staff_id?>&type=bonus');" />
		</td>
		<td>
<input type="button" value="Pay Commission" onclick="javascript:window.open('/webadmin/timesheets/?action=st_payment&staff_id=<?=$staff_id?>&type=commission');" /><br />
	</td>
</tr>
<?php
}
?>

	<tr>
		<td colspan="3">
<input type="button" value="View Timesheets" onclick="javascript:window.open('/webadmin/timesheets/?action=list&staff_id=<?=$staff_id?>');" />
		</td>
	</tr>
</table>

</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionYearlySummary_hideNote">
Yearly Summary can be viewed by Clicking "Yearly Summary" above
</div>

<br /><br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
