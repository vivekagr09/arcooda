<?php
/**
 * @var string[] $all_staff_list
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $_GLOBALS;

moduleLoad('staff');

//Only show history if there is some to show
if (count($staff_history)<=0) return;
?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionJobHistory\');">Job History</a>')?>

<script type="text/javascript">
function viewJobHistory()
{
	var url='.?action=history&return_message=null&staff_id=<?=$staff_id?>';
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'history_popup',features);
}
</script>

<div id="sectionJobHistory">
<?php
$style1="width:25%;white-space:nowrap;";
$style2="width:25%;text-align:center;background-color:#ffffff;white-space:nowrap;";
$style3="width:25%;white-space:nowrap;";
$style4="width:25%;text-align:center;background-color:#ffffff;white-space:nowrap;";
foreach ($staff_history as $history_id)
{
	$item=staffHistoryFromDatabase($history_id);
	$item['message_colour']='white';
?>
<table class="wide">
	<tr>
		<td class="left_title" style="<?=$style1?>">Type</td>
		<td style="width:25%;text-align:center;background-color:<?=$item['message_colour'] ?>;"><b><?=htmlspecialchars($item['message_description'])?></b></td>
		<td class="left_title" style="<?=$style3?>">Date</td>
		<td style="<?=$style4 ?>"><?=date('d/m/Y',$item['message_time'])?></td>
	</tr>
	<tr>
		<td class="left_title" style="<?=$style1?>">Sender</td>
		<td style="<?=$style2 ?>"><?=htmlspecialchars($all_staff_list[$item['message_sender']])?></td>
		<td class="left_title" style="<?=$style3?>"></td>
		<td style="<?=$style4?>"></td>
	</tr>
<?php
	if (trim($item['message_notes'])!='')
	{
?>
	<tr>
		<td class="left_title" valign="top" style="width:25%;">Notes</td>
		<td colspan="3"><?=nl2br(htmlspecialchars($item['message_notes']))?></td>
	</tr>
<?php
	}
?>
</table>
<br /><br />
<?php
}
?>
<table class="wide">
	<tr class="alt"><td style="text-align:center;">
		<input type="button" value="Print History" onclick="javascript:window.print();" />
<?php
if ('default' == $_GLOBALS['layout']) {
?>
		<input type="button" value="View All(<?=$staff['history_count']?>)" onclick="javascript:viewJobHistory();" />
<?php
}
?>
	</td></tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionJobHistory_hideNote">
Job History can be viewed by Clicking "Job History" above
<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
