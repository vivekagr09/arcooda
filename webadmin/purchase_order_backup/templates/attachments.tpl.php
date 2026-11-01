<!-- attachments.tpl -->

<?php
moduleLoad('asset'); //assetFromDatabase, assetToCompatible

function attachment_row($acount = '[ATTACHMENT_COUNT]', $attachment = array())
{
	global $order_id;

    $attach_viewurl  = "/webadmin/assets/index.php?action=view&amp;asset_type=purchaseorder&amp;owner_id=" . $order_id . "&amp;asset_id=";
    $attach_thumburl = "/webadmin/assets/index.php?action=thumb&amp;asset_type=purchaseorder&amp;owner_id=" . $order_id . "&amp;asset_id=";
    ob_start();
?>
<tr id="attachment_rowa[<?= $acount ?>]">
	<td style="text-align:center;"><input type="checkbox" name="attachment_flag[<?= $acount ?>]" onclick="if (getElement('flag_attachments_customer')) getElement('flag_attachments_customer').checked = true;" /></td>
	<td><a href="<?= $attach_viewurl . $acount ?>" title="<?= $attachment['file_orig'] ?>" target="_blank"><img alt="" border="0" src="<?= $attach_thumburl . $acount ?>" title="<?= $attachment['file_orig'] ?>" /></a></td>
	<td width="100%"><a href="<?= $attach_viewurl . $acount ?>" target="_blank"><?= $attachment['asset_caption'] ?></a></td>
	<td><?= (is_numeric($attachment['asset_time'])) ? date('d/m/Y', $attachment['asset_time']) : $attachment['asset_time'] ?></td>
	<td><?= $attachment['file_type'] ?></td>
	<td><input type="button" onclick="deleteAttachment(<?= $acount ?>);" value="Remove" /></td>
</tr>
<?php
return ob_get_clean();
}
?>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAttachments\');">Attachments</a>') ?>

<div id="sectionAttachments">
<script type="text/javascript">
//<![CDATA[
function addAttachment()
{
	var url='/webadmin/assets/index.php?action=add&asset_type=purchaseorder&return_message=attach_attach&owner_id=<?= $order_id ?>';
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'attach_popup',features);
}

function deleteAttachment(asset_id)
{
	var url='/webadmin/assets/index.php?action=delete&asset_type=purchaseorder&return_message=attach_delete&owner_id=<?= $order_id ?>&asset_id='+escape(asset_id);
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'attach_popup',features);
}

function setAssetData(data)
{
	switch(data['return_message'])
	{
	case 'attach_delete':
		var theRow=getElement("attachment_rowa["+data['asset_id']+"]");
		if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
		break;
	case 'attach_attach':
		var acount=String(data['asset_id']);
		var attach_table=getElement("attachment_table");
		if(attach_table==null) return;

		var newRow1=addTableRow(attach_table,'attachment_rowa['+acount+']',-2);
<?php
$replace1 = array(
    'owner_id'      => $order_id,
    'file_orig'     => '[FILE_ORIG]',
    'asset_caption' => '[ASSET_CAPTION]',
    'asset_time'    => '[ASSET_TIME]',
    'file_type'     => '[FILE_TYPE]',
);
$replace2 = array(
    '[ATTACHMENT_COUNT]' => '" + acount + "',
    '[FILE_ORIG]'        => '" + data["file_orig"] + "',
    '[ASSET_CAPTION]'    => '" + data["asset_caption"] + "',
    '[ASSET_TIME]'       => '" + data["asset_nicedate"] + "',
    '[FILE_TYPE]'        => '" + data["file_type"] + "',
);
$count = 0;
foreach (explode("\n", attachment_row('[ATTACHMENT_COUNT]', $replace1)) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace(array_keys($replace2), array_values($replace2), jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 31) == '<td style="text-align:center;">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace(array_keys($replace2), array_values($replace2), jecho(substr($row, 31, -5), 1)) . ").style.textAlign = 'center';\n";
    } elseif (substr($row, 0, 17) == '<td width="100%">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace(array_keys($replace2), array_values($replace2), jecho(substr($row, 17, -5), 1)) . ").style.width = '100%';\n";
    }

}
?>
		break;
	}
}
//]]>
</script>

<table class="wide" id="attachment_table">
	<tr>
		<th>Attach To Customer Email</th>
		<th>Image</th>
		<th>Description</th>
		<th>Attach Date</th>
		<th>File Type</th>
		<th>Remove</th>
	</tr>
<?php
$acount = 1;
foreach ($invoice_assets as $asset_id) {
    $asset      = assetFromDatabase('purchaseorder', $asset_id);
    $attachment = assetToCompatible('purchaseorder', $asset);
    echo attachment_row($asset_id, $attachment);
}
?>
	<tr>
		<td class="left_title" colspan="6" style="text-align:center;"><input type="button" value="Add Attachment" onclick="addAttachment();" /></td>
	</tr>
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionAttachments_hideNote">
Attachments can be viewed by Clicking "Attachments" above
<br />
</div>

<!-- /attachments.tpl -->
