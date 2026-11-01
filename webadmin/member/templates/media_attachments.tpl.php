<!-- members_attachments.tpl.php -->

<?php
moduleLoad('asset'); //assetFromDatabase, assetToCompatible

/**
 * @param string  $acount
 * @param mixed[] $attachment
 *
 * @return string
 */
function attachment_row($acount = '[ATTACHMENT_COUNT]', $attachment = array())
{
    global $member_id, $all_staff_list;
    $url             = "/webadmin/assets/?asset_type=member&owner_id=" . $member_id . "&asset_id=" . $acount . "&action=";
    $attach_viewurl  = $url . 'view';
    $attach_thumburl = $url . 'thumb';
    ob_start();
?>
<tr id="attachment_rowa[<?=$acount?>]" style="text-align:center;">
	<!---<td style="text-align:center;"><input type="checkbox" name="attachment_flag[<?=$acount?>]" /></td>-->
	<td><a href="<?=$attach_viewurl?>" title="<?=$attachment['file_orig']?>" target="_blank"><img border="0" src="<?=$attach_thumburl?>" title="<?=$attachment['file_orig']?>" /></a></td>
	<td><a href="<?=$attach_viewurl?>" target="_blank"><?=$attachment['caption']?></a></td>
	<td><?=(is_numeric($attachment['asset_time'])) ? date(_('d/m/Y'), $attachment['asset_time']) : $attachment['asset_time']?></td>
	<td><?=$all_staff_list[$attachment['staff']]?></td>
	<td><?=$attachment['filetype']?></td>
	<td><?=('' != $attachment['size_formatted']) ? $attachment['size_formatted'] : formatFileSize($attachment['size'])?></td>
	<td><input type="button" onclick="editAttachment(<?=$acount?>)" value="Edit" /><input type="button" onclick="javascript:deleteAttachment(<?=$acount?>);" value="Remove" /></td>
</tr>
<?php
    return ob_get_clean();
}
?>
<!-- attachments.tpl.php -->

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAttachments\');">Customer Attachments</a>')?>

<div id="sectionAttachments">
<script type="text/javascript">
//<![CDATA[
function addAttachment()
{
	var url='/webadmin/assets/?action=add&asset_type=member&return_message=attach_attach&owner_id=<?=$member_id?>';
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'attach_popup',features);
}

function deleteAttachment(asset_id)
{
	var url='/webadmin/assets/?action=delete&asset_type=member&return_message=attach_delete&owner_id=<?=$member_id?>&asset_id='+escape(asset_id);
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'attach_popup',features);
}

function editAttachment(asset_id)
{
	var url='/webadmin/assets/?action=edit&asset_type=member&return_message=attach_edit&owner_id=<?=$member_id?>&asset_id='+escape(asset_id);
	openWindow(url,600,400,true);
}

function setAssetData(data)
{
	switch(data['return_message'])
	{
	case 'attach_delete':
		var theRow=getElement("attachment_rowa["+data['asset_id']+"]");
		if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
		break;
	case 'attach_edit':
		var theRow=getElement("attachment_rowa["+data['asset_id']+"]");
		if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
		// after deleting the row, add it with the new data...
	case 'attach_attach':
		var acount=String(data['asset_id']);
		var attach_table=getElement("attachment_table");
		if(attach_table==null) return;

		var newRow1=addTableRow(attach_table,'attachment_rowa['+acount+']',-2);
		newRow1.style.textAlign = 'center';
<?php
$replace1 = array(
    'asset_id'       => '[ASSET_ID]',
    'owner_id'       => $member_id,
    'file_orig'      => '[FILE_ORIG]',
    'caption'        => '[ASSET_CAPTION]',
    'asset_time'     => '[ASSET_TIME]',
    'filetype'       => '[FILE_TYPE]',
    'size_formatted' => '[SIZE_FORMATTED]',
);
$replace2 = array(
    '[ASSET_ID]'         => "'+data['asset_id']+'",
    '[ATTACHMENT_COUNT]' => "'+acount+'",
    '[FILE_ORIG]'        => "'+data['file_orig']+'",
    '[ASSET_CAPTION]'    => "'+data['caption']+'",
    '[ASSET_TIME]'       => "'+data['asset_nicedate']+'",
    '[FILE_TYPE]'        => "'+data['filetype']+'",
    '[SIZE_FORMATTED]'   => "'+data['size_formatted']+'",
);
$count = 0;
foreach (explode("\n", attachment_row('[ATTACHMENT_COUNT]', $replace1)) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSString(substr($row, 4, -5))) . ");\n";
    } elseif (substr($row, 0, 31) == '<td style="text-align:center;">') {
        echo "addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSString(substr($row, 31, -5))) . ").style.textAlign = 'center';\n";
    } elseif (substr($row, 0, 17) == '<td width="100%">') {
        echo "addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSString(substr($row, 17, -5))) . ").style.width='100%';\n";
    }
}
?>
		break;
	}
}
//]]>
</script>

<table class="wide table_grid" id="attachment_table" cellpadding="4">
	<tr>
		<th style="width: 5%;">Image</th>
		<th style="width: auto;">Description</th>
		<th style="width: 5%;">Attach Date</th>
		<th style="width: 5%;">Attached By</th>
		<th style="width: 5%;">File Type</th>
		<th style="width: 10%;">File Size</th>
		<th style="width: 5%;">Edit/Delete</th></tr>
	</tr>
<?php
$acount = 1;
foreach ($member_assets as $asset_id) {
    $asset      = assetFromDatabase('member', $asset_id);
    $attachment = assetToCompatible('member', $asset);
    echo attachment_row($asset_id, $attachment);
}
?>
	<tr>
		<td class="left_title" colspan="7" style="text-align:center;"><input type="button" value="Add Attachment" onclick="javascript:addAttachment();" /></td>
	</tr>
</table>
<br /><br />
</div>

<!-- /members_attachments.tpl.php -->
