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

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionNewsletterStatus\');">Newsletter Status</a>') ?>

<div id="sectionNewsletterStatus">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:50%;">Total Members Listed</td>
			<td><a target="_blank" href="<?php echo $preview_url ?>"><?php echo count($item_members_list) ?></a></td>
		</tr>
		<tr>
			<td class="left_title">Total Successful Emails Sent</td>
			<td><a target="_blank" href="<?php echo $preview_url ?>&amp;email_status=sent"><?php echo count($by_status['sent']) ?></a></td>
		</tr>
		<tr>
			<td class="left_title">Total Failed Emails</td>
			<td><a target="_blank" href="<?php echo $preview_url ?>&amp;email_status=error"><?php echo count($by_status['error']) ?></a></td>
		</tr>
		<tr>
			<td class="left_title">Total Emails Remaining</td>
			<td><a target="_blank" href="<?php echo $preview_url ?>&amp;email_status=listed"><?php echo count($by_status['listed']) ?></a></td>
		</tr>
	</table>
	<br /><br />

<?php
	if ($action == 'edit')
	{
?>
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;">Status</td>
			<td style="width:75%;"><select name="<?php echo $section_name ?>[active]">
				<option value="y"<?php echo ($item['active'] == 'y')?' selected="selected"':'' ?>>Active Newsletter</option>
				<option value="n"<?php echo ($item['active'] == 'n')?' selected="selected"':'' ?>>Cancelled Newsletter</option>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Display Online</td>
			<td><select name="<?php echo $section_name ?>[display_online]">
				<option value="y"<?php echo ($item['display_online'] == 'y')?' selected="selected"':'' ?>>Yes</option>
				<option value="n"<?php echo ($item['display_online'] == 'n')?' selected="selected"':'' ?>>No</option>
			</select></td>
		</tr>
	</table>
	<br /><br />
<?php
	}
?>

</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionNewsletterStatus_hideNote">Newsletter Status can be viewed by Clicking "Newsletter Status" above<br /></div>

<?php
}
?>

<!-- /status.tpl.php -->
