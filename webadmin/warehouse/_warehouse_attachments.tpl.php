<?php
/**
 * @var int $invoice_id
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('asset'); //assetFromDatabase, assetToCompatible

function attachment_row($acount = '[ATTACHMENT_COUNT]', $attachment = array())
{
	global $invoice_id;

    $attach_viewurl  = "/webadmin/assets/?action=view&asset_type=warehouse&owner_id=" . $id . "&asset_id=";
    $attach_thumburl = "/webadmin/assets/?action=thumb&asset_type=warehouse&owner_id=" . $id . "&asset_id=";
    ob_start();
?>
<tr id="attachment_rowa[<?=$acount?>]">
	<td><a href="<?=$attach_viewurl . $acount?>" title="<?=$attachment['file_orig']?>" target="_blank"><img border="0" src="<?=$attach_thumburl . $acount?>" title="<?=$attachment['file_orig']?>" /></a></td>
	<td width="50%"><a href="<?=$attach_viewurl . $acount?>" target="_blank"><?=$attachment['asset_caption']?></a></td>
	<td width="15%"><?=(is_numeric($attachment['asset_time'])) ? date('d/m/Y', $attachment['asset_time']) : $attachment['asset_time']?></td>
	<td><?=$attachment['file_type']?></td>
	<td><input type="button" onclick="javascript:deleteAttachment(<?=$acount?>);" value="Remove" /></td>
</tr>
<?php
return ob_get_clean();
}
?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAttachments\');">Attachments</a>')?>

<div id="sectionAttachments">
<script type="text/javascript">
//<![CDATA[
function addAttachment()
{
	var url='/webadmin/assets/?action=add&asset_type=warehouse&return_message=attach_attach&owner_id=<?=$id?>';
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'attach_popup',features);
}

function deleteAttachment(asset_id)
{
	var url='/webadmin/assets/?action=delete&asset_type=warehouse&return_message=attach_delete&owner_id=<?=$id?>&asset_id='+escape(asset_id);
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
    'owner_id'      => $invoice_id,
    'file_orig'     => '[FILE_ORIG]',
    'asset_caption' => '[ASSET_CAPTION]',
    'asset_time'    => '[ASSET_TIME]',
    'file_type'     => '[FILE_TYPE]',
);
$replace2 = array(
    '[ATTACHMENT_COUNT]' => "'+acount+'",
    '[FILE_ORIG]'        => "'+data['file_orig']+'",
    '[ASSET_CAPTION]'    => "'+data['asset_caption']+'",
    '[ASSET_TIME]'       => "'+data['asset_nicedate']+'",
    '[FILE_TYPE]'        => "'+data['file_type']+'",
);
$count = 0;
foreach (explode("\n", attachment_row('[ATTACHMENT_COUNT]', $replace1)) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSString(substr($row, 4, -5))) . ");\n";
    } elseif (substr($row, 0, 31) == '<td style="text-align:center;">') {
        echo "addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSString(substr($row, 31, -5))) . ").style.textAlign='center';\n";
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

<table class="table_fields" id="attachment_table">
	<tr>
		<th style="text-align: left;">Image</th>
		<th  style="text-align: left;">Description</th>
		<th  style="text-align: left;width: 15%;">Attach Date</th>
		<th  style="text-align: left;">File Type</th>
		<th  style="text-align: left;">Remove</th>
	</tr>
<?php
$acount = 1;
foreach ($partner_assets as $asset_id) {
    $asset      = assetFromDatabase('warehouse', $asset_id);
    $attachment = assetToCompatible('warehouse', $asset);
    echo attachment_row($asset_id, $attachment);
}
?>
	<tr>
		<td class="left_title" colspan="6" style="text-align:center;"><input type="button" value="Add Attachment" onclick="javascript:addAttachment();" /></td>
	</tr>
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionAttachments_hideNote">
Attachments can be viewed by Clicking "Attachments" above
<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
