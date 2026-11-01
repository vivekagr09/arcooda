<!-- status.tpl.php -->

<?php
if(is_array($by_status))
{
	$preview_url='index.php?action=preview_list&amp;id='.$id;
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionNewsletterStatus\');">Update Status</a>') ?>

<div id="sectionNewsletterStatus">
	<table class="table_fields">
		<tr>
			<th style="width:50%;">Total Members Listed</th>
			<td><?php echo count($item_members_list) ?></td>
		</tr>
		<tr>
			<th style="width:50%;">Total Successful Emails Sent</th>
			<td><?php echo count($by_status['sent']) ?></td>
		</tr>
		<tr>
			<th style="width:50%;">Total Failed Emails</th>
			<td><?php echo count($by_status['error']) ?></td>
		</tr>
		<tr>
			<th style="width:50%;">Total Emails Remaining</th>
			<td><?php echo count($by_status['listed']) ?></td>
		</tr>
	</table>
	<br /><br />

</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionNewsletterStatus_hideNote">Newsletter Status can be viewed by Clicking "Newsletter Status" above<br /></div>

<?php
}
?>

<!-- /status.tpl.php -->
