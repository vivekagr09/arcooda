<?php
/**
 * @var string[] $all_staff_list
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('staff');

if (count($staff_admin_notes)>0)
{
?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAdminHistory\');">Admin History</a>')?>

<div id="sectionAdminHistory">
<table class="wide spacetable">
	<tr><th>Date</th><th>User</th><th>Admin Note</th></tr>
<?php
	foreach ($staff_admin_notes as $message_id)
	{
		$item=staffHistoryFromDatabase($message_id);
?>
	<tr<?=($alt=!$alt)?' class="alt"':''?>>
		<td style="white-space:nowrap;text-align:center;"><?=date('d/m/Y',$item['message_time'])?></td>
		<td style="white-space:nowrap;text-align:center;"><?=htmlspecialchars($all_staff_list[$item['message_sender']])?></td>
		<td style="width:100%;"><?=nl2br(htmlspecialchars($item['message_notes']))?></td>
	</tr>
<?php
	}
?>
</table><br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionAdminHistory_hideNote">
Admin History can be viewed by Clicking "Admin History" above<br />
</div>
<?php
}

?>
<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
