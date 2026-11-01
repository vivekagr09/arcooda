<!-- attachments.tpl.php -->

<?php
$contractor_attachments = contractorAttachmentsFromDatabase($contractor_id);
if (is_array($contractor_attachments)) {
    $staff_list = contractorGetOptionList('staff_id');
?>
<script type="text/javascript">
//<![CDATA[
var attachments = 100;
function contractorAttachmentAdd() {
    openWindow('addAttachment.php?action=add&contractor_id=<?=$contractor_id?>', 700, 300, true);
}
function addAttachment(data) {
	console.log(data);
    var att_table = getElement('contractor_attachment_table');
    if (att_table == null) return;
    var newRow = addTableRow(att_table, 'contractor_attachment_rowa[' + attachments + ']');
    addTableCell(newRow, '<input type="hidden" name="contractor_attachments[' + attachments + '][id]" value="' + data['id'] + '" /><a href="' + data['image_url'] + '" target="_blank"><img src="' + data['thumb_url'] + '" border="0" alt="" /></a>');
    addTableCell(newRow, '<a href="' + data['image_url'] + '" target="_blank">' + data['caption'] + '</a>');
    addTableCell(newRow, data['date_str']);
    addTableCell(newRow, <?php jecho($_GLOBALS['staff']['name']) ?>);
    addTableCell(newRow, data['filetype'].toUpperCase());
    addTableCell(newRow, data['size']);
    addTableCell(newRow, '<input type="button" value="Edit" onclick="openWindow(\'addAttachment.php?action=edit&id=' + data['id'] + '&contractor_id=<?=$contractor_id?>\',700,300,true);" /><input type="button" value="Delete" onclick="contractorAttachmentDelete(\'' + attachments + '\')" />');
    if (attachments % 2 == 1) newRow.className = 'alt';
}
function contractorAttachmentDelete(row) {
    if (confirm('Are You Sure you want to delete this attachment??')) {
        var theRow = getElement('contractor_attachment_rowa[' + row + ']');
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    }
}
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectioncontractorAttachments\');">Contractor Attachments</a>', '<a href="#" style="color:#000;" onclick="contractorAttachmentAdd();return false;">Add New Attachment</a>')?>

<div id="sectioncontractorAttachments">
	<table class="table_grid" id="contractor_attachment_table">
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
$count    = 0;
    $affected = array();
    foreach ($contractor_attachments as $attachment_id => $attachment) {
        $count++;
        $affected[] = $attachment_id;
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?> id="contractor_attachment_rowa[<?=$count?>]">
			<td>
				<input type="hidden" name="contractor_attachments[<?=$count?>][id]" value="<?=$attachment_id?>" />
				<a href="<?php hecho($attachment['image_url'])?>" target="_blank"><img src="<?php hecho($attachment['thumb_url'])?>" border="0" alt="" style="width:40px;"/></a>
			</td>
			<td style="text-align:left;"><a href="<?php hecho($attachment['image_url'])?>" target="_blank"><?php hecho($attachment['caption'])?></a></td>
			<td><?=$attachment['date_str']?></td>
			<td><?php hecho($staff_list[$attachment['staff_id']])?></td>
			<td><?php hecho(strtoupper($attachment['filetype']))?></td>
			<td><?=formatFileSize($attachment['size'])?></td>
			<td>
				<input type="button" value="Edit" onclick="openWindow('addAttachment.php?action=edit&amp;id=<?=$attachment_id?>&amp;contractor_id=<?=$contractor_id?>',700,300,true);" />
				<input type="button" value="Delete" onclick="contractorAttachmentDelete('<?=$count?>')" />
			</td>
		</tr>
<?php
}
?>
	</table>
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectioncontractorAttachments_hideNote">contractor Attachments can be viewed by Clicking "contractor Attachments" above<br /></div>

<?php
}
?>

<!-- /attachments.tpl.php -->
