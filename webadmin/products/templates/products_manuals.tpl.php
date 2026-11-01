<!--
Created and modified by Mingze Xi@ Feb 2, 2015

Finished: 100%

Unfinished job: FTP Upload is not working, (ftp server is down);
based on products_manuals.tpl@AGS -->

<!-- Section : Manuals -->
<?php
$status = array('y'=>'Yes', 'n'=>'No');
?>
<div align="center">
	<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMan\');">Manuals</a>');?>
</div>
<div id="sectionMan">
<table class="wide" align="center" style="width:95%;padding-left: 3;border: 1px solid #CCCCCC;border-collapse: collapse;margin-left: auto;margin-right: auto;" name="manuals" id="manuals">
	<tr>
		<th style="background-color:#FFCFAF;font-weight:bold;font-size:12" colspan="7">
			Product Manuals
		</th>
	</tr>
	<tr bgcolor="#E3EBF7">
		<td style="width:10%;" align="center" nowrap="nowrap" class="bottomLeft"><strong>Type Of Manual</strong></td>
		<td style="width:auto;" align="center" nowrap="nowrap" class="bottomLeft"><strong>Notes/Description</strong></td>
		<td style="width:7%;" align="center" class="bottomLeft"><strong>File Type</strong></td>
		<td style="width:5%;" align="center" class="bottomLeft"><strong>Size</strong></td>
		<td style="width:10%;" align="center" class="bottomLeft"><strong>Uploaded By</strong></td>
		<td style="width:9%;" align="center" class="bottomLeft"><strong>Date Added</strong></td>
		<td style="width:4%;" align="center" class="bottomLeftRight"><strong>Edit</strong></td>
	</tr>
	<tbody id="attach_list">
<?php
	productAttachmentsFTPToDatabase($product['id']);
	$manuals = productAttachmentsFromDatabase($product['id']);
	$count++;
	foreach($manuals as $man)
	{
		$manual_details = productAttachmentFromDatabase($man, $product['id']);
?>
			<tr id="attach[<?php echo $man
?>]" bgcolor="<?php echo ($count % 2 == 1) ? "#EEEEEE" : "#FFFFFF" ?>">
				<td class="bottomLeft" nowrap="nowrap" align="center">
					<a target="_blank" href="/manuals/C<?php echo $man ?>_<?php echo $product['id'] ?>.<?php echo $manual_details['filetype'] ?>">
						<!-- <a target="_blank" href="viewimage.php?product_id=<?php echo $product['id'] ?>&amp;attachment_id=<?php echo $man ?>"> -->
						<!-- <img src="viewimage.php?product_id=<?php echo $product['id'] ?>&amp;attachment_id=<?php echo $man ?>&amp;thumb=true"> -->
						<?php echo typeOfManual($manual_details['manual_type']) ?>
					</a>
				</td>
				<td class="bottomLeft" nowrap="nowrap" align="left">
					<a target="_blank" href="/manuals/C<?php echo $man
?>_<?php echo $product['id'] ?>.<?php echo $manual_details['filetype'] ?>">
						<!-- <a target="_blank" href="viewimage.php?product_id=<?php echo $product['id'] ?>&amp;attachment_id=<?php echo $man ?>"> -->
						<?php echo $manual_details['description'] ?>
					</a>
				</td>
				<td class="bottomLeft" nowrap="nowrap" align="center"><?php echo $manual_details['filetype'] ?></td>
<?php
		$size = ($manual_details['size'] != 0) ? human_file_size($manual_details['size']) : '&nbsp;'; ?>
				<td class="bottomLeft" nowrap="nowrap" align="center"><?php echo $size
?></td>
				<td class="bottomLeft" nowrap="nowrap" align="center"><?php echo ($manual_details['user'] != '') ? $manual_details['user'] : '&nbsp;' ?></td>
				<td class="bottomLeft" nowrap="nowrap" align="center"><?php echo date("d/m/y", strtotime($manual_details['attach_date'])) ?></td>
				<td class="bottomLeftRight"><input type="button" value="Edit" onclick="javascript:editAttachment(<?php echo $man ?>)" /></td>
				<!--<td class="bottomLeftRight"><input type="button" value="Remove" onClick="javascript:removeAttachWindow(<?php echo $man ?>)" /></td>-->
			</tr>
<?php
		$count++;
	}
?>
		</tbody>
		<tfoot>
			<tr id="attach_foot" name="attach_foot" bgcolor="<?php echo ($count % 2 == 1) ? "#EEEEEE" : "#FFFFFF" ?>">
				<td class="bottomLeftRight" align="center" colspan="7">
					<input type="button" value="Add Manual" onclick="javascript:addAttachWindow()" />
					<input type="button" value="FTP Upload" onclick="window.open('<?php echo productAttachmentGetFTPURI($product['id']) ?>')" />
				</td>
			</tr>
		</tfoot>
	</table>
</div><br />
<!-- Section : Manuals: End -->