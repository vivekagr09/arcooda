<!-- attachments.tpl.php -->

<?php
$supplier_attachments = supplierAttachmentsFromDatabase($supplier_id);
if (is_array($supplier_attachments)) {
    $staff_list = supplierGetOptionList('staff_id');
?>

<script type="text/javascript">
//<![CDATA[
var attachments = 100;
function memberAttachmentAdd() {
    openWindow('addAttachment.php?action=add&supplier_id=<?=$supplier_id?>', 700, 300, true);
}
function addAttachment(data) {
    var att_table = getElement('member_attachment_table');
    if (att_table == null) return;
    var newRow = addTableRow(att_table, 'member_attachment_rowa[' + attachments + ']');
    addTableCell(newRow, '<input type="hidden" name="member_attachments[' + attachments + '][id]" value="' + data['id'] + '" /><a href="' + data['image_url'] + '" target="_blank"><img src="' + data['thumb_url'] + '" border="0" alt="" /></a>');
    addTableCell(newRow, '<a href="' + data['image_url'] + '" target="_blank">' + data['caption'] + '</a>');
    addTableCell(newRow, data['date_str']);
    addTableCell(newRow, <?php jecho($_GLOBALS['staff']['name']) ?>);
    addTableCell(newRow, data['filetype'].toUpperCase());
    addTableCell(newRow, data['size']);
    addTableCell(newRow, '<input type="button" value="Edit" onclick="openWindow(\'addAttachment.php?action=edit&id=' + data['id'] + '&member_id=<?=$supplier_id?>\',700,300,true);" /><input type="button" value="Delete" onclick="memberAttachmentDelete(\'' + attachments + '\')" />');
    if (attachments % 2 == 1) newRow.className = 'alt';
}
function memberAttachmentDelete(row) {
    if (confirm('Are You Sure you want to delete this attachment??')) {
        var theRow = getElement('member_attachment_rowa[' + row + ']');
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    }
}
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMemberAttachments\');">Supplier Attachments</a>', '<a href="#" style="color:#000;" onclick="memberAttachmentAdd();return false;">Add New Attachment</a>')?>

<div id="sectionMemberAttachments">
	<table class="table_grid" id="member_attachment_table">
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
    foreach ($supplier_attachments as $attachment_id) {
        $attachment = supplierAttachmentFromDatabase($attachment_id);
        $count++;
        $affected[] = $attachment_id;
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?> id="member_attachment_rowa[<?=$count?>]">
			<td>
				<input type="hidden" name="member_attachments[<?=$count?>][id]" value="<?=$attachment_id?>" />
				<a href="<?php hecho($attachment['image_url'])?>" target="_blank">
					<img src="../assets/suppliers/thumbnails/<?php hecho($attachment['filename'])?>" border="0" alt="" /></a>
			</td>
			<td style="text-align:left;"><a href="<?php hecho($attachment['image_url'])?>" target="_blank"><?php hecho($attachment['caption'])?></a></td>
			<td><?=$attachment['date_str']?></td>
			<td><?php hecho($staff_list[$attachment['staff_id']])?></td>
			<td><?php hecho(strtoupper($attachment['filetype']))?></td>
			<td><?=formatFileSize($attachment['size'])?></td>
			<td>
				<input type="button" value="Edit" onclick="openWindow('addAttachment.php?action=edit&amp;id=<?=$attachment_id?>&amp;supplier_id=<?=$supplier_id?>',700,300,true);" />
				<input type="button" value="Delete" onclick="memberAttachmentDelete('<?=$count?>')" />
			</td>
		</tr>
<?php
}
?>
	</table>
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionMemberAttachments_hideNote">Member Attachments can be viewed by Clicking "Member Attachments" above<br /></div>

<?php
}
?>

<!-- /attachments.tpl.php -->
