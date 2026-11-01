<!-- attachments.tpl.php -->

<?php
$weeklyspecial_attachments=$section_handler->attachmentsFromDatabase($id);
if(is_array($weeklyspecial_attachments))
{
	$staff_list=$section_handler->getOptionList('staff_id');
?>

<script type="text/javascript">
//<![CDATA[
var attachments=100;
function weeklyspecialAttachmentAdd()
{
	openWindow('addAttachment.php?action=add&id=<?= $id ?>', 700,300,true);
}
function addAttachment(data)
{
	var att_table = getElement('weeklyspecial_attachment_table');
	if(att_table == null) return;
	var newRow = addTableRow(att_table,'weeklyspecial_attachment_rowa['+attachments+']');
	addTableCell(newRow,'<input type="hidden" name="weeklyspecial_attachments['+attachments+'][id]" value="'+data['id']+'" /><a href="/webadmin'+data['image_url']+'" target="_blank"><img src="/webadmin'+data['thumb_url']+'" border="0" alt="" /></a>');
	addTableCell(newRow,'<a href="/webadmin'+data['image_url']+'" target="_blank">'+data['caption']+'</a>');
	addTableCell(newRow,data['date_str']);
	addTableCell(newRow, <?=json_encode($_GLOBALS['staff']['name'])?>);
	addTableCell(newRow,data['filetype'].toUpperCase());
	addTableCell(newRow,data['size']);
	addTableCell(newRow,'<input type="button" value="Edit" onclick="openWindow(\'addAttachment.php?action=edit&id='+data['id']+'&id=<?= $id ?>\',700,300,true);" /><input type="button" value="Delete" onclick="weeklyspecialAttachmentDelete(\''+attachments+'\')" />');
	if(attachments%2==1) newRow.className='alt';
}
function weeklyspecialAttachmentDelete(row)
{
	if(confirm('Are You Sure you want to delete this attachment??'))
	{
		var theRow = getElement('weeklyspecial_attachment_rowa['+row+']');
		if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
}
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialAttachments\');">WeeklySpecial Attachments</a>', '<a href="#" style="color:#000;" onclick="weeklyspecialAttachmentAdd();return false;">Add New Attachment</a>') ?>

<div id="sectionWeeklySpecialAttachments">
	<table class="wide" id="weeklyspecial_attachment_table">
		<tr>
			<th style="width:5%;">Image</th>
			<th style="width:auto;">Description</th>
			<th style="width:5%;">Attach Date</th>
			<th style="width:5%;">Attached By</th>
			<th style="width:5%;">File Type</th>
			<th style="width:10%;">File Size</th>
			<th style="width:5%;">Edit/Delete</th>
		</tr>
<?php
	$count=0;
	$affected=array();
	foreach($weeklyspecial_attachments as $attachment_id)
	{
		$attachment=$section_handler->attachmentFromDatabase($attachment_id);
		$count++;
		$affected[]=$attachment_id;
?>
		<tr<?= ($alt=!$alt)?' class="alt"':'' ?> id="weeklyspecial_attachment_rowa[<?= $count ?>]">
			<td>
				<input type="hidden" name="weeklyspecial_attachments[<?= $count ?>][id]" value="<?= $attachment_id ?>" />
				<a href="<?= htmlspecialchars('/webadmin'.$attachment['image_url']) ?>" target="_blank"><img src="<?= htmlspecialchars('/webadmin'.$attachment['thumb_url']) ?>" border="0" alt="" /></a>
			</td>
			<td style="text-align:left;"><a href="<?= htmlspecialchars('/webadmin'.$attachment['image_url']) ?>" target="_blank"><?= htmlspecialchars($attachment['caption']) ?></a></td>
			<td><?= $attachment['date_str'] ?></td>
			<td><?= htmlspecialchars($staff_list[$attachment['staff_id']]) ?></td>
			<td><?= htmlspecialchars(strtoupper($attachment['filetype'])) ?></td>
			<td><?= formatFileSize($attachment['size']) ?></td>
			<td>
				<input type="button" value="Edit" onclick="openWindow('addAttachment.php?action=edit&amp;id=<?= $attachment_id ?>&amp;id=<?= $id ?>',700,300,true);" />
				<input type="button" value="Delete" onclick="weeklyspecialAttachmentDelete('<?= $count ?>')" />
			</td>
		</tr>
<?php
	}
?>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionWeeklySpecialAttachments_hideNote">WeeklySpecial Attachments can be viewed by Clicking "WeeklySpecial Attachments" above<br /></div>

<?php
}
?>

<!-- /attachments.tpl.php -->
