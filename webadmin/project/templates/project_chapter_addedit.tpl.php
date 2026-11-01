<?php
moduleLoad('asset'); //assetFromDatabase, assetToCompatible

if (isset($_GET['updated'])) {
	echo "<p style='color: #ff0000'><i>Project Chapter Updated.</i></p>";
}

$staff_list = projectGetOptionList('staff_id');

function attachment_row($acount='[ATTACHMENT_COUNT]',$attachment=array())
{
	global $chapter_id;
	$attach_viewurl="/webadmin/assets/index.php?action=view&asset_type=project_pages&owner_id=".$chapter_id."&asset_id=";
	$attach_thumburl="/webadmin/assets/index.php?action=thumb&asset_type=project_pages&owner_id=".$chapter_id."&asset_id=";
	ob_start();
?>
<tr id="attachment_rowa[<?php echo $acount ?>]">
	<td><a href="javascript:moveDown(<?php echo $acount ?>);"><img src="/webadmin/images/down_arrow.gif" border="0" /></a><a href="javascript:moveUp(<?php echo $acount ?>);"><img src="/webadmin/images/up_arrow.gif" border="0" /></a></td>
	<td><a href="<?php echo $attach_viewurl.$acount ?>" title="<?php echo $attachment['file_orig'] ?>" target="_blank"><img border="0" src="<?php echo $attach_thumburl.$acount ?>" title="<?php echo $attachment['file_orig'] ?>" /></a></td>
	<td width="100%"><a href="<?php echo $attach_viewurl.$acount ?>" target="_blank"><?php echo htmlspecialchars($attachment['page_content']) ?></a></td>
	<td><?php echo (is_numeric($attachment['asset_time']))?date('d/m/Y',$attachment['asset_time']):$attachment['asset_time'] ?></td>
	<td>-</td>
	<td><?php echo htmlspecialchars($attachment['image_type']) ?></td>
	<td>-</td>
	<td><input type="hidden" name="page_id[<?php echo $acount ?>]" value="<?php echo htmlspecialchars($attachment['page_id']) ?>" /><input type="hidden" name="page_rank[<?php echo $acount ?>]" value="<?php echo htmlspecialchars($attachment['page_rank']) ?>" /><input type="button" onclick="javascript:deleteAttachment(<?php echo $acount ?>);" value="Delete" /></td>
</tr>
<?php
	return ob_get_clean();
}
?>


<script type="text/javascript">
var acount=<?php echo max(100,count($page_list)) ?>;

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

<input type="hidden" name="chapter[chapter_id]" value="<?php echo $chapter_id ?>" />
<input type="hidden" name="page[page_id]" value="<?php echo $page_id ?>" />
<input type="hidden" name="page[page_rank]" value="0" />
<input type="hidden" name="chapter[project_id]" value="<?php echo $chapter['project_id'] ?>" />

<table>
	<tr>
		<td class="left_title" style="width:25%;">Project</td>
		<td><?php echo htmlspecialchars($project_list[$chapter['project_id']]) ?></td>
	</tr>
	<tr>
		<td class="left_title">Title</td>
		<td><input type="text" class="intext" name="chapter[chapter_title]" value="<?php echo htmlspecialchars($chapter['chapter_title']) ?>" /></td>
	</tr>
	<tr>
<?php
$rows=explode("\n",$page['page_content']);
$row_count=0;
foreach($rows as $row)
	$row_count+=floor(strlen($row)/60.0)+1;
?>
		<td class="left_title">Current Notes on Project<br><br><span style="font-size: 10px;"><i>(Please list final notes or current situation/development notes)</i></span></td>
		<!--td><textarea name="page[page_content]" rows="<?php echo max(20,$row_count) ?>" onkeyup="javascript:textareaAutoSize(this,1,20);"><?php echo htmlspecialchars($page['page_content']) ?></textarea></td-->
		<td>
			<div style="border: 1px solid #000; width: 98%; margin: 30px 0; border-radius: 5px; -webkit-border-radius: 5px; -moz-border-radius: 5px; overflow: hidden;">
				<select id="note_type" style="width: 100%; height: 30px;">
					<option disabled="disabled" selected="selected">-- Select Type --</option>
					<option value="Development Notes">Development Notes</option>
					<option value="Final Notes">Final Notes</option>
					<option value="Current Situation">Current Situation</option>
				</select>
				<textarea name="page[page_content]" rows="<?php echo max(20,$row_count) ?>" id="text-editor"><?php echo htmlspecialchars($page['page_content']) ?></textarea>
			</div>
		</td>
	</tr>
	<tr>
		<td class="left_title"></td>
		<!--td style="text-align:justify;">
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
		</td-->
	</tr>
</table>

<br />
<br />

<div class="flagHeader"><span><a href="javascript:toggleSection('projectnotes');">Project Notes</a></span></div>
<br>

<div id="projectnotes">
	<table class="wide">
		<tbody>
			<tr>
				<td class="left_title" style="width:25%;">Type</td>
				<td>
					<select name="projectnotes[type]">
						<option value=""></option>
						<option value="working_notes">Working Notes</option>
						<option value="emails_suppliers">Emails to/from Suppliers</option>
					</select>
				</td>
			</tr>

			<tr>
				<td class="left_title">Notes</td>
				<td><textarea name="projectnotes[notes]" rows="8" onkeyup="javascript:textareaAutoSize(this,1,8);"></textarea></td>
			</tr>
		</tbody>
	</table>
<br><br>
</div>

<div class="flagHeader"><span><a href="javascript:toggleSection('noteshistory');">Notes History</a></span></div>
<br>

<div id="noteshistory">
<?php
	if (projectNoteExists($chapter_id)) {
		$noteHistory = projectNoteFromDatabase($chapter_id);
		foreach($noteHistory as $history) {
			$type = ($history['type'] == "working_notes") ? "Working Notes" : "Emails to/from Suppliers";
			$color = ($history['type'] == "working_notes") ? "FF9999" : "F573DE";
?>
	<table class="wide">
		<tbody>
			<tr>
				<td class="left_title" style="width:10%;white-space:nowrap;">Type</td>
				<td style="width:39%;text-align:center;background-color:#<?= $color ?>"><b><?php echo $type ?></b></td>
				<td class="left_title" style="width:15%;white-space:nowrap;">Date</td>
				<td style="width:36%;text-align:center;background-color:#ffffff;white-space:nowrap;"><?php echo date("d/m/Y", strtotime($history['added_date'])); ?></td>
			</tr>
			<tr>
				<td class="left_title" style="width:10%;white-space:nowrap;">Staff</td>
				<td style="width:39%;text-align:center;background-color:#ffffff;white-space:nowrap;"><?php echo $staff_list[$history['staff']]; ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td class="left_title" valign="top" style="width:10%;">Notes</td>
				<td colspan="3">
					<div style="border: 1px solid #c0c0c0;">
						<p>
							<?php echo $history['notes']; ?>
						</p>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<br>
	<?php 	} ?>
	<?php } ?>
<br><br>
</div>

<?php
if($chapter_id>0)
{
?>
<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAttachments\');">Attachments</a>', '<a href="javascript:addAttachment();">Add Attachment</a>') ?>

<div id="sectionAttachments">
<script type="text/javascript">
function addAttachment()
{
	var url='/webadmin/assets/index.php?action=add&asset_type=project_pages&return_message=attach_attach&owner_id=<?php echo $chapter_id ?>';
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'attach_popup',features);
}

function deleteAttachment(asset_id)
{
	var url='/webadmin/assets/index.php?action=delete&asset_type=project_pages&return_message=attach_delete&owner_id=<?php echo $chapter_id ?>&asset_id='+escape(asset_id);
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
			echo "addTableCell(newRow".$count.",".str_replace(array_keys($replace2),array_values($replace2),json_encode(substr($row,4,-5))).");\n";
		elseif(substr($row,0,31)=='<td style="text-align:center;">')
			echo "addTableCell(newRow".$count.",".str_replace(array_keys($replace2),array_values($replace2),json_encode(substr($row,31,-5))).").style.textAlign = 'center';\n";
		elseif(substr($row,0,17)=='<td width="100%">')
			echo "addTableCell(newRow".$count.",".str_replace(array_keys($replace2),array_values($replace2),json_encode(substr($row,17,-5))).").style.width = '100%';\n";
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
			$asset=assetFromDatabase('project_pages',$asset_id);
			$attachment=assetToCompatible('project_pages',$asset);
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

<link href="jquery-te-1.4.0.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jquery-te-1.4.0.min.js"></script>
<script type="text/javascript">
	$('#text-editor').jqte();

	$('body').on("change", "#note_type", function(){
		var type = $(this).val();
		var after = "";
		if ($('#note-type-header-after').length == 0) {
			after = "<p id='note-type-header-after'><br><br><br></p>";
		}

		var html = "<h1 style='text-decoration:underline;' class='note-type-header'>" + type + "</h1>" + after + $("#text-editor").val();
		$('#text-editor').jqteVal(html);

		$('.note-type-header:nth-child(2)').remove();
	});
</script>