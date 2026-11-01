<script type="text/javascript">
//<![CDATA[
function deleteWarning(id)
{
	if(confirm('Are you sure you want to delete this Warning ??'))
	{
		$.ajax({
			type: "GET",
			url: "members_delete_warning.php",
			data: "id="+id,
			async: false,
			success: function(msg) {
				if(msg=='success') {
					var theRow=getElement("warning_"+id);
					if(theRow) {
						theRow.parentNode.deleteRow(theRow.sectionRowIndex);
						alert("Deleted Successfully");
					}
				}
				else alert('There was an error with your request, please try again...');
			}
		});
	}
}
//]]>
</script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionWarnings\'); return false;">Member Warnings</a>')?>
<div id="sectionWarnings"><table>
	<tr><td class="left_title" style="width:25%;">Member Warning :</td><td><textarea name="member_warning" rows="3" onkeyup="javascript:textareaAutoSize(this,1,3,25);"></textarea></td></tr>
</table><br />
<?php
if (count($member_warnings) > 0) {
?>
<table class="wide" border="1" cellpadding="4">
	<tr><th colspan="4" style="background-color:#EEEEEE">Member Warnings</td></tr>
	<tr><th>Date</th><th>User</th><th>Comment</th><th></th></tr>
<?php
$count = 0;
    foreach ($member_warnings as $warning) {
?>
	<tr <?=($count++ % 2 == 1) ? 'class="alt"' : ''?> style="text-align:center;" id="warning_<?=$warning['id']?>">
		<td style="padding:4px;" nowrap><?=date(_('d/m/Y'), $warning['comment_time'])?></td>
		<td style="padding:4px;" nowrap><?=$warning['staff']?></td>
		<td style="padding:4px;width:100%;text-align:left;"><?=nl2br(htmlspecialchars($warning['comments']))?></td>
		<td><input type="button" value="Delete" onclick="deleteWarning('<?=$warning['id']?>')" /></td>
	</tr>
<?php
}
?>
</table><br />
<?php
}
?>
</div>