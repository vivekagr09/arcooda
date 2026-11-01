<?php
moduleLoad('asset'); //assetFromDatabase, assetToCompatible

function attachment_row($acount='[ATTACHMENT_COUNT]',$attachment=array())
{
	global $chapter_id;
	$attach_viewurl="/webadmin/assets/index.php?action=view&asset_type=workmanual_pages&owner_id=".$chapter_id."&asset_id=";
	$attach_thumburl="/webadmin/assets/index.php?action=thumb&asset_type=workmanual_pages&owner_id=".$chapter_id."&asset_id=";
	ob_start();
?>
<tr id="attachment_rowa[<?= $acount ?>]">
	<td><a href="javascript:moveDown(<?= $acount ?>);"><img src="/webadmin/images/down_arrow.gif" border="0" /></a><a href="javascript:moveUp(<?= $acount ?>);"><img src="/webadmin/images/up_arrow.gif" border="0" /></a></td>
	<td><a href="<?= $attach_viewurl.$acount ?>" title="<?= $attachment['file_orig'] ?>" target="_blank"><img border="0" src="<?= $attach_thumburl.$acount ?>" title="<?= $attachment['file_orig'] ?>" /></a></td>
	<td width="100%"><a href="<?= $attach_viewurl.$acount ?>" target="_blank"><?= htmlspecialchars($attachment['page_content']) ?></a></td>
	<td><?= (is_numeric($attachment['asset_time']))?date('d/m/Y',$attachment['asset_time']):$attachment['asset_time'] ?></td>
	<td>-</td>
	<td><?= htmlspecialchars($attachment['image_type']) ?></td>
	<td>-</td>
	<td><input type="hidden" name="page_id[<?= $acount ?>]" value="<?= htmlspecialchars($attachment['page_id']) ?>" /><input type="hidden" name="page_rank[<?= $acount ?>]" value="<?= htmlspecialchars($attachment['page_rank']) ?>" /><input type="button" onclick="javascript:deleteAttachment(<?= $acount ?>);" value="Delete" /></td>
</tr>
<?php
	return ob_get_clean();
}

?>
<script type="text/javascript">
var acount=<?= max(100,count($page_list)) ?>;

function updateRankOrder()
{
	var table=getElement("attachment_table");
	if(table==null) return;

	var counter=1;
	for(row_id in table.rows)
	{
		var theElem=table.rows[row_id];
		if(theElem.id!=null && theElem.id.substring(0,15)=='attachment_rowa')
			setValue('page_rank'+theElem.id.substring(15),counter++);
	}
}

function moveUp(row_id)
{
	var table=getElement("attachment_table");
	if(table==null) return;

	var theElem=null;
	var prevElem=null;
	for(tr_id in table.rows)
	{
		if(table.rows[tr_id].id!=null && table.rows[tr_id].id.substring(0,15)=='attachment_rowa')
		{
			prevElem=theElem;
			theElem=table.rows[tr_id];
			if(theElem.id=='attachment_rowa['+row_id+']')
				break;
		}
//		if(theElem!=null && theElem.id!=null && theElem.id.substring(0,15)=='attachment_rowa')
//			prevElem=theElem;
//		theElem=table.rows[tr_id];
//		if(theElem.id!=null && theElem.id=='attachment_rowa['+row_id+']')
//			break;
	}

//	if(theElem!=null && prevElem!=null)
	if(prevElem!=null && theElem!=null && theElem.id=='attachment_rowa['+row_id+']')
		theElem.parentNode.insertBefore(theElem,prevElem);

	updateRankOrder();
}

function moveDown(row_id)
{
	var table=getElement("attachment_table");
	if(table==null) return;

	var theElem=null;
	var prevElem=null;
	for(tr_id in table.rows)
	{
		if(table.rows[tr_id].id!=null && table.rows[tr_id].id.substring(0,15)=='attachment_rowa')
		{
			prevElem=theElem;
			theElem=table.rows[tr_id];
			if(prevElem!=null && prevElem.id=='attachment_rowa['+row_id+']')
				break;
		}
	}

	if(prevElem!=null && prevElem.id=='attachment_rowa['+row_id+']')
		theElem.parentNode.insertBefore(theElem,prevElem);

	updateRankOrder();
}
</script>

<input type="hidden" name="chapter[chapter_id]" value="<?= $chapter_id ?>" />
<input type="hidden" name="page[page_id]" value="<?= $page_id ?>" />
<input type="hidden" name="page[page_rank]" value="0" />
<input type="hidden" name="chapter[manual_id]" value="<?= $chapter['manual_id'] ?>" />

<table>
	<tr>
		<td class="left_title" style="width:25%;">Manual</td>
		<td><?= htmlspecialchars($manual_list[$chapter['manual_id']]) ?></td>
	</tr>
	<tr>
		<td class="left_title">Title</td>
		<td><input type="text" class="intext" name="chapter[chapter_title]" value="<?= htmlspecialchars($chapter['chapter_title']) ?>" /></td>
	</tr>
	<tr>
<?php
$rows=explode("\n",$page['page_content']);
$row_count=0;
foreach($rows as $row)
	$row_count+=floor(strlen($row)/60.0)+1;
?>
		<td class="left_title">Text</td>
		<td><textarea name="page[page_content]" rows="<?= max(20,$row_count) ?>" onkeyup="javascript:textareaAutoSize(this,1,20);"><?= htmlspecialchars($page['page_content']) ?></textarea></td>
	</tr>
	<tr>
		<td class="left_title"></td>
		<td style="text-align:justify;">
			<input style="width:96px;" type="button" value="Insert Link" onclick="javascript:generic_insert('page[page_content]','a',' href=&quot;http://www.WEBSITE.com/&quot; target=&quot;_blank&quot;');" />
			<input style="width:96px;" type="button" value="Heading" onclick="javascript:generic_insert('page[page_content]','h2');" />
			<input style="width:96px;" type="button" value="Sub-Heading" onclick="javascript:generic_insert('page[page_content]','h3');" />

			<br />

			<input style="width:96px;font-weight:bold;" type="button" value="Bold" onclick="javascript:generic_insert('page[page_content]','b');" />
			<input style="width:96px;font-style:italic;" type="button" value="Italic" onclick="javascript:generic_insert('page[page_content]','i');" />
			<input style="width:96px;text-decoration:underline;" type="button" value="Underline" onclick="javascript:generic_insert('page[page_content]','u');" />
			<input style="width:96px;text-decoration:line-through;" type="button" value="Strikethru" onclick="javascript:generic_insert('page[page_content]','s');" />

			<br />

			<input style="width:96px;color:red;" type="button" value="Red" onclick="javascript:generic_insert('page[page_content]','span',' style=&quot;color:red;&quot;');" />
			<input style="width:96px;color:green;" type="button" value="Green" onclick="javascript:generic_insert('page[page_content]','span',' style=&quot;color:green;&quot;');" />
			<input style="width:96px;color:blue;" type="button" value="Blue" onclick="javascript:generic_insert('page[page_content]','span',' style=&quot;color:blue;&quot;');" />
			<input style="width:96px;color:yellow;" type="button" value="Yellow" onclick="javascript:generic_insert('page[page_content]','span',' style=&quot;color:yellow;&quot;');" />

			<br />

			<input style="width:96px;background-color:red;" type="button" value="Red" onclick="javascript:generic_insert('page[page_content]','span',' style=&quot;background-color:red;&quot;');" />
			<input style="width:96px;background-color:green;" type="button" value="Green" onclick="javascript:generic_insert('page[page_content]','span',' style=&quot;background-color:green;&quot;');" />
			<input style="width:96px;background-color:blue;" type="button" value="Blue" onclick="javascript:generic_insert('page[page_content]','span',' style=&quot;background-color:blue;&quot;');" />
			<input style="width:96px;background-color:yellow;" type="button" value="Yellow" onclick="javascript:generic_insert('page[page_content]','span',' style=&quot;background-color:yellow;&quot;');" />

			<br />
		</td>
	</tr>
</table>

<br />
<br />

<?php
if($chapter_id>0)
{
?>
<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAttachments\');">Attachments</a>', '<a href="javascript:addAttachment();">Add Attachment</a>') ?>

<div id="sectionAttachments">
<script type="text/javascript">
function addAttachment()
{
	var url='/webadmin/assets/index.php?action=add&asset_type=workmanual_pages&return_message=attach_attach&owner_id=<?= $chapter_id ?>';
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'attach_popup',features);
}

function deleteAttachment(asset_id)
{
	var url='/webadmin/assets/index.php?action=delete&asset_type=workmanual_pages&return_message=attach_delete&owner_id=<?= $chapter_id ?>&asset_id='+escape(asset_id);
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

		var newRow1=addTableRow(attach_table,'attachment_rowa['+acount+']',-1);
<?php
	$replace1=array(
		'owner_id'=>$chapter_id,
		'file_orig'=>'[FILE_ORIG]',
		'page_content'=>'[PAGE_CONTENT]',
		'asset_time'=>'[ASSET_TIME]',
		'image_type'=>'[IMAGE_TYPE]',
		'page_id'=>'[PAGE_ID]',
		'page_rank'=>'[PAGE_RANK]'
	);
	$replace2=array(
		'[ATTACHMENT_COUNT]' => '" + data.page_id + "',
		'[FILE_ORIG]'        => '" + data.file_orig + "',
		'[PAGE_CONTENT]'     => '" + data.page_content + "',
		'[ASSET_TIME]'       => '" + data.asset_nicedate + "',
		'[IMAGE_TYPE]'       => '" + data.image_type + "',
		'[PAGE_ID]'          => '" + data.page_id + "',
		'[PAGE_RANK]'        => '" + data.page_rank + "',
	);
	$count=0;
	foreach(explode("\n",attachment_row('[ATTACHMENT_COUNT]',$replace1)) as $row)
	{
		$row=trim($row);
		if(substr($row,0,3)=='<tr')
			$count++;
		elseif(substr($row,0,4)=='<td>')
			echo "addTableCell(newRow" . $count . ", " . str_replace(array_keys($replace2), array_values($replace2), json_encode(substr($row, 4, -5))) . ");\n";
		elseif(substr($row,0,31)=='<td style="text-align:center;">')
			echo "addTableCell(newRow" . $count . ", " . str_replace(array_keys($replace2), array_values($replace2), json_encode(substr($row, 31, -5))) . ").style.textAlign = 'center';\n";
		elseif(substr($row,0,17)=='<td width="100%">')
			echo "addTableCell(newRow" . $count . ", " . str_replace(array_keys($replace2), array_values($replace2), json_encode(substr($row, 17, -5))) . ").style.width = '100%';\n";
	}
?>
		break;
	}
}
</script>

<table class="wide" id="attachment_table">
	<tr>
		<th>Rank</th>
		<th>Image</th>
		<th>Description</th>
		<th>Attach Date</th>
		<th>Attached By</th>
		<th>File Type</th>
		<th>Size</th>
		<th>Delete</th>
	</tr>
<?php
	$acount=0;
	foreach($page_list as $asset_id)
	{
		$acount++;
		if($acount>1)
		{
			$asset=assetFromDatabase('workmanual_pages',$asset_id);
			$attachment=assetToCompatible('workmanual_pages',$asset);
			echo attachment_row($asset_id,$attachment);
		}
	}
?>
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionAttachments_hideNote">
Attachments can be viewed by Clicking "Attachments" above
<br />
</div>
<?php
}
?>

<input type="submit" value="Update Changes" />
